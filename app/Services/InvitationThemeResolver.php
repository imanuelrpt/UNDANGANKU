<?php

namespace App\Services;

use App\Models\Invitation;
use Illuminate\Support\Facades\Cache;

class InvitationThemeResolver
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function activeThemes(): array
    {
        return Cache::remember('themes.active', now()->addMinutes(10), function (): array {
            $themes = config('themes.list', []);
            $overrides = Cache::get('themes.overrides', []);

            foreach ($overrides as $key => $active) {
                if (isset($themes[$key])) {
                    $themes[$key]['active'] = (bool) $active;
                }
            }

            return array_filter(
                $themes,
                fn (array $theme): bool => (bool) ($theme['active'] ?? false),
            );
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function resolve(Invitation $invitation): array
    {
        $themes = $this->activeThemes();
        $key = $invitation->theme;

        if (! array_key_exists($key, $themes)) {
            $key = config('themes.default', 'elegant-gold');
        }

        return array_merge(['key' => $key], $themes[$key]);
    }

    public function view(Invitation $invitation): string
    {
        return $this->resolve($invitation)['view'];
    }

    public function css(Invitation $invitation): string
    {
        return $this->resolve($invitation)['css'];
    }

    /**
     * @return array<string, string>
     */
    public function options(): array
    {
        return collect($this->activeThemes())
            ->mapWithKeys(fn (array $theme, string $key): array => [$key => $theme['name']])
            ->all();
    }
}
