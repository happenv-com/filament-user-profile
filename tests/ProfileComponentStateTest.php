<?php

use Happenv\FilamentUserProfile\Livewire\BrowserSessions;
use Happenv\FilamentUserProfile\Livewire\MyProfileComponent;
use Happenv\FilamentUserProfile\Livewire\PersonalInfo;
use Happenv\FilamentUserProfile\Livewire\SanctumTokens;
use Happenv\FilamentUserProfile\Livewire\TwoFactorAuth;
use Happenv\FilamentUserProfile\Livewire\UpdatePassword;

/**
 * Public properties on a Livewire component are part of the snapshot sent to the
 * browser, and the browser can write them back. $only decides which form fields
 * reach $userModel->update(), so it must never be public.
 */
it('does not expose $only as client-writable Livewire state', function (string $component) {
    $property = new ReflectionProperty($component, 'only');

    expect($property->isPublic())->toBeFalse();
})->with([
    MyProfileComponent::class,
    PersonalInfo::class,
    UpdatePassword::class,
    BrowserSessions::class,
    SanctumTokens::class,
    TwoFactorAuth::class,
]);
