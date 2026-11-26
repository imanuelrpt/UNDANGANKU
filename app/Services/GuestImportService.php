<?php

namespace App\Services;

use App\Enums\GuestGroup;
use App\Imports\GuestsImport;
use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class GuestImportService
{
    /**
     * @return array{imported: int, failed: array<int, array{row: int, errors: array<int, string>}>}
     */
    public function import(Invitation $invitation, UploadedFile $file): array
    {
        $import = new GuestsImport;
        Excel::import($import, $file);

        $rows = $import->rows();
        $header = array_shift($rows);
        $columns = $this->mapHeaders($header ?? []);

        $existing = $invitation->guests()->get(['name', 'phone'])
            ->map(fn (Guest $guest): string => $this->duplicateKey($guest->name, $guest->phone))
            ->flip()
            ->all();

        $imported = 0;
        $failed = [];

        DB::transaction(function () use ($invitation, $rows, $columns, &$existing, &$imported, &$failed): void {
            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2;
                $values = $row instanceof Collection ? $row->all() : (array) $row;
                $data = $this->mapRow($values, $columns);

                $validator = Validator::make($data, [
                    'name' => ['required', 'string', 'max:100'],
                    'group' => ['nullable', 'string'],
                    'phone' => ['nullable', 'string', 'max:30'],
                    'pax' => ['nullable', 'integer', 'min:1', 'max:50'],
                ]);

                if ($validator->fails()) {
                    $failed[] = ['row' => $rowNumber, 'errors' => $validator->errors()->all()];

                    continue;
                }

                $cleaned = $validator->validated();
                $key = $this->duplicateKey($cleaned['name'], $cleaned['phone'] ?? null);

                if (isset($existing[$key])) {
                    $failed[] = ['row' => $rowNumber, 'errors' => ['Nama dan nomor WhatsApp sudah terdaftar.']];

                    continue;
                }

                Guest::create([
                    'invitation_id' => $invitation->id,
                    'name' => $cleaned['name'],
                    'group' => $this->resolveGroup($cleaned['group'] ?? null)->value,
                    'phone' => $cleaned['phone'] ?? null,
                    'pax_quota' => $cleaned['pax'] ?? 1,
                    'token' => Str::random(config('undangan.guest_token_length', 24)),
                ]);

                $existing[$key] = true;
                $imported++;
            }
        });

        return ['imported' => $imported, 'failed' => $failed];
    }

    /**
     * @param  array<int, mixed>  $header
     * @return array<string, int>
     */
    private function mapHeaders(array $header): array
    {
        $aliases = [
            'name' => ['nama', 'name', 'nama tamu'],
            'group' => ['grup', 'group', 'kategori'],
            'phone' => ['nowa', 'no wa', 'phone', 'whatsapp', 'wa', 'telepon', 'no hp'],
            'pax' => ['pax', 'jumlah', 'kuota', 'pax quota'],
        ];

        $columns = [];

        foreach ($header as $index => $value) {
            $normalized = Str::lower(Str::squish((string) $value));

            foreach ($aliases as $field => $names) {
                if (in_array($normalized, $names, true)) {
                    $columns[$field] = $index;
                    break;
                }
            }
        }

        return $columns;
    }

    /**
     * @param  array<int, mixed>  $values
     * @param  array<string, int>  $columns
     * @return array<string, mixed>
     */
    private function mapRow(array $values, array $columns): array
    {
        $data = [];

        foreach ($columns as $field => $index) {
            $data[$field] = is_object($values[$index] ?? null) ? (string) $values[$index] : ($values[$index] ?? null);
        }

        return $data;
    }

    private function resolveGroup(?string $value): GuestGroup
    {
        $normalized = Str::lower(Str::squish((string) $value));

        foreach (GuestGroup::cases() as $group) {
            if ($normalized === $group->value || $normalized === Str::lower($group->label())) {
                return $group;
            }
        }

        return GuestGroup::Teman;
    }

    private function duplicateKey(string $name, ?string $phone): string
    {
        return Str::lower(Str::squish($name)).'|'.Str::lower(Str::squish((string) $phone));
    }
}
