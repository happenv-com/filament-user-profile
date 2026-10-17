<?php

namespace Happenv\FilamentUserProfile\Pages;

use BackedEnum;
use Filament\Auth\Pages\EditProfile as BasePage;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Happenv\FilamentUserProfile\UserProfilePlugin;
use Happenv\FilamentUserProfile\View\UserProfileIconAlias;
use Illuminate\Contracts\Support\Htmlable;

class MyProfilePage extends BasePage
{
    protected string $view = 'happenv-filament-user-profile::filament.pages.my-profile';

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve(UserProfileIconAlias::NAVIGATION_PROFILE)
            ?? Heroicon::OutlinedDocumentText;
    }

    public static function getPlugin(): UserProfilePlugin
    {
        /** @var UserProfilePlugin $plugin */
        $plugin = filament('happenv-filament-user-profile');

        return $plugin;
    }

    public function getTitle(): string
    {
        return __('happenv-filament-user-profile::default.profile.my_profile');
    }

    public function getHeading(): string
    {
        return __('happenv-filament-user-profile::default.profile.my_profile');
    }

    public static function getLabel(): string
    {
        return static::$title ?? __('filament-panels::auth/pages/edit-profile.label');
    }

    public function getSubheading(): ?string
    {
        return __('happenv-filament-user-profile::default.profile.subheading');
    }

    public static function getNavigationLabel(): string
    {
        return __('happenv-filament-user-profile::default.profile.profile');
    }

    public function getRegisteredMyProfileComponents()
    {
        return static::getPlugin()->getRegisteredMyProfileComponents();
    }
}
