<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Happenv\FilamentUserProfile\Livewire\PersonalInfo;
use Happenv\FilamentUserProfile\UserProfilePlugin;

function personalInfoOnly(bool $avatars): array
{
    $panel = Panel::make()
        ->id('test-'.($avatars ? 'avatars' : 'plain'))
        ->path('test-'.($avatars ? 'avatars' : 'plain'))
        ->plugin(UserProfilePlugin::make()->avatars($avatars));

    Filament::registerPanel(fn (): Panel => $panel);
    Filament::setCurrentPanel($panel);

    $method = new ReflectionMethod(PersonalInfo::class, 'getOnly');
    $method->setAccessible(true);

    return $method->invoke(new PersonalInfo);
}

it('saves only name and email when avatars are disabled', function () {
    expect(personalInfoOnly(avatars: false))->toBe(['name', 'email']);
});

it('includes the avatar column when avatars are enabled', function () {
    expect(personalInfoOnly(avatars: true))->toContain('avatar_url');
});
