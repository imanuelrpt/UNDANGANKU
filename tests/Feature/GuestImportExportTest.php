<?php

namespace Tests\Feature;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class GuestImportExportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_csv_import_creates_unique_guests_and_reports_duplicates(): void
    {
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create(['slug' => 'rina-dan-dimas']);
        $file = UploadedFile::fake()->createWithContent('tamu.csv', implode("\n", [
            'nama,grup,nowa,pax',
            'Andi Santoso,teman,081211111111,2',
            'Andi Santoso,teman,081211111111,2',
            'Budi Setiawan,vip,081222222222,1',
        ]));

        $response = $this->actingAs($owner)
            ->post(route('guests.import', $invitation), ['file' => $file]);

        $response->assertRedirect()
            ->assertSessionHas('success', '2 tamu berhasil diimpor.');
        $this->assertSame(2, $invitation->guests()->count());
        $this->assertDatabaseHas('guests', [
            'invitation_id' => $invitation->id,
            'name' => 'Budi Setiawan',
            'group' => 'vip',
            'pax_quota' => 1,
        ]);
    }

    public function test_import_rejects_unsupported_file_type_without_creating_guests(): void
    {
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create();
        $file = UploadedFile::fake()->create('tamu.txt', 1, 'text/plain');

        $response = $this->actingAs($owner)
            ->post(route('guests.import', $invitation), ['file' => $file]);

        $response->assertSessionHasErrors('file');
        $this->assertSame(0, $invitation->guests()->count());
    }

    public function test_owner_can_export_guests_as_spreadsheet(): void
    {
        $owner = User::factory()->create();
        $invitation = Invitation::factory()->for($owner)->create(['slug' => 'rina-dan-dimas']);

        $response = $this->actingAs($owner)->get(route('guests.export', $invitation));

        $response->assertOk()->assertDownload('tamu-rina-dan-dimas.xlsx');
    }
}
