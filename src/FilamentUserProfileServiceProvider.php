<?php

namespace Happenv\FilamentUserProfile;

use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentUserProfileServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('happenv-filament-user-profile')
            ->hasViews()
            ->hasTranslations();
    }

    public function packageBooted(): void
    {
        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            'happenv/filament-user-profile',
        );
    }

    protected function getAssets(): array
    {
        if (! UserProfilePlugin::passkeysAreAvailable()) {
            return [];
        }

        return [
            Js::make('filament-passkeys-scripts', __DIR__.'/../resources/dist/filament-passkeys.js'),
        ];
    }
}
