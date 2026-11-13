<?php

namespace Happenv\FilamentUserProfile;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Happenv\FilamentUserProfile\Livewire\BrowserSessions;
use Happenv\FilamentUserProfile\Livewire\Passkeys;
use Happenv\FilamentUserProfile\Livewire\PersonalInfo;
use Happenv\FilamentUserProfile\Livewire\SanctumTokens;
use Happenv\FilamentUserProfile\Livewire\TwoFactorAuth;
use Happenv\FilamentUserProfile\Livewire\UpdatePassword;
use Happenv\FilamentUserProfile\Pages\MyProfilePage;
use Livewire\Livewire;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;

class UserProfilePlugin implements Plugin
{
    use EvaluatesClosures;

    // NEW
    protected string $profilePage = MyProfilePage::class;

    protected string $slug = 'my-profile';

    protected array $profileComponents = [
        'personal_info' => PersonalInfo::class,
        'update_password' => UpdatePassword::class,
        'passkeys' => Passkeys::class,
        'two_factor_auth' => TwoFactorAuth::class,
        'browser_sessions' => BrowserSessions::class,
        'sanctum_tokens' => SanctumTokens::class,
    ];

    protected bool $hasAvatars = false;

    protected array $sanctumAbilities = [];

    public function getId(): string
    {
        return 'happenv-filament-user-profile';
    }

    public function avatars(bool $condition = true): static
    {
        $this->hasAvatars = $condition;

        return $this;
    }

    public function hasAvatars(): bool
    {
        return $this->hasAvatars;
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function register(Panel $panel): void
    {
        collect($this->getProfileComponents())->each(
            fn (string $component, string $key) => Livewire::component($key, $component)
        );
    }

    public function profilePage(string $page): static
    {
        $this->profilePage = $page;

        return $this;
    }

    public function getProfilePage()
    {
        return $this->profilePage;
    }

    public function profileComponents(array $components): static
    {
        $this->profileComponents = $components;

        return $this;
    }

    public function getProfileComponents(): array
    {
        if (static::passkeysAreAvailable()) {
            return $this->profileComponents;
        }

        return array_filter(
            $this->profileComponents,
            fn (string $component): bool => $component !== Passkeys::class,
        );
    }

    /**
     * The `passkeys` section is built on spatie/laravel-passkeys, which is only
     * suggested. Without it the section is neither registered nor rendered.
     *
     * Compared by class name on purpose: loading `Passkeys` without its spatie
     * parent class is a fatal error.
     */
    public static function passkeysAreAvailable(): bool
    {
        return class_exists(PasskeysComponent::class);
    }

    public function replaceProfileComponent(string $key, string $component): static
    {
        $this->profileComponents[$key] = $component;

        return $this;
    }

    public function removeProfileComponent(string $key): static
    {
        unset($this->profileComponents[$key]);

        return $this;
    }

    public function registerProfileComponent(string $key, string $component): static
    {
        $this->profileComponents[$key] = $component;

        return $this;
    }

    public function insertAfterProfileComponent(string $afterKey, string $key, string $component): static
    {
        $newComponents = [];

        foreach ($this->profileComponents as $existingKey => $existingComponent) {
            $newComponents[$existingKey] = $existingComponent;

            if ($existingKey === $afterKey) {
                $newComponents[$key] = $component;
            }
        }

        $this->profileComponents = $newComponents;

        return $this;
    }

    public function sanctumAbilities(array $abilities): static
    {
        $this->sanctumAbilities = $abilities;

        return $this;
    }

    public function getSanctumAbilities(): array
    {
        return collect($this->sanctumAbilities)->mapWithKeys(function ($item, $key) {
            $key = is_string($key) ? $key : strtolower($item);

            return [$key => $item];
        })->toArray();
    }

    public function boot(Panel $panel): void
    {
        // $this->userMenuRegistration();

    }

    public function getRegisteredMyProfileComponents()
    {
        $components = collect($this->getProfileComponents())
            ->filter(
                function (string $component) {
                    if (\method_exists($component, 'canView')) {
                        return $component::canView();
                    }

                    return true;
                }
            );
        // ->sortBy(
        //     function (string $component) {
        //         if (\method_exists($component, 'getSort')) {
        //             return $component::getSort();
        //         }

        //         // put at last place
        //         return 999;
        //     }
        // );

        return $components;
    }
}
