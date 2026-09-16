<?php

namespace Happenv\FilamentUserProfile\Pages;

use Filament\Auth\Pages\EditProfile as BasePage;
use Happenv\FilamentUserProfile\UserProfilePlugin;

class MyProfilePage extends BasePage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'happenv-filament-user-profile::filament.pages.my-profile';

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
