# Filament User Profile

<div class="filament-hidden">

![Filament User Profile](art/banner.png)

</div>

This package draws inspiration from [Filament Breezy](https://github.com/jeffgreco13/filament-breezy). It does not implement two-factor authentication itself: its Two-Factor Authentication section shows the multi-factor authentication configured on your panel.

Rather than offering multiple plugin options, this package provides a streamlined approach to extending and replacing components.

## Upgrading to 4.0

4.0 swaps the user agent parser again, this time to [`webard/agent`](https://github.com/webard/agent) — a maintained
fork of `al-saloul/agent`, which had not been released since January 2025 and emitted a PHP 8.4 deprecation on every
request that consumers could not suppress.

```sh
composer remove al-saloul/agent
composer require happenv-com/filament-user-profile:^4.0
```

The namespace is unchanged, so if you extended `BrowserSessions` your `use Alsaloul\Agent\Agent;` import keeps
working. `webard/agent` declares `"replace": {"al-saloul/agent": "*"}`, so remove the old package first — the two
cannot be installed side by side.

Nothing else changed; browser, platform and device detection behave exactly as in 3.x.

## Upgrading to 3.0

3.0 raises the minimum PHP version to **8.3**. The package previously declared `^8.2`, but
its own test suite could never run there — `pestphp/pest` 4.x requires `^8.3` — so 8.2 was
untested. The CI matrix now covers 8.3 and 8.4 only.

The browser sessions component no longer depends on the abandoned `jenssegers/agent`
(and its outdated `mobiledetect/mobiledetectlib` 2.x). It now uses the maintained fork
[`al-saloul/agent`](https://github.com/al-saloul/agent).

If your application requires `jenssegers/agent` only because of this package, remove it:

```sh
composer remove jenssegers/agent
composer require happenv-com/filament-user-profile:^3.0
```

If you extended `BrowserSessions` and referenced the agent class, update the import:

```diff
-use Jenssegers\Agent\Agent;
+use Alsaloul\Agent\Agent;
```

The public API (`browser()`, `platform()`, `isDesktop()`, `isMobile()`, `isTablet()`)
is unchanged, so no other changes are required.

## Installation

To install the package, execute the following command:

```sh
composer require happenv-com/filament-user-profile
```

## Register Plugin

To register the plugin, use the following code snippet:

```php
use Happenv\FilamentUserProfile\UserProfilePlugin;

$panel->plugins([
    UserProfilePlugin::make()
]);
```

## Options

### Register User Menu Item

Control whether the plugin should automatically register the user menu item:

```php
UserProfilePlugin::make()
    ->registerUserMenu(false);
```

### Custom Profile Page

Replace the entire `ProfilePage` with your custom component:

```php
UserProfilePlugin::make()
    ->profilePage(MyProfileComponent::class);
```

### Custom Profile Components

Manage the registered components and their order:

```php
use Happenv\FilamentUserProfile\Livewire\PersonalInfo;
use Happenv\FilamentUserProfile\Livewire\UpdatePassword;

UserProfilePlugin::make()
    ->profileComponents([
        'personal_info' => PersonalInfo::class,
        'update_password' => UpdatePassword::class,
    ]);
```

### Replace Profile Component

Replace a specific profile component:

```php
use My\Component\PersonalInfo;

UserProfilePlugin::make()
    ->replaceProfileComponent('personal_info', PersonalInfo::class);
```

### Register New Profile Component

Register a new profile component:

```php
use My\Component\SomeComponent;

UserProfilePlugin::make()
    ->registerProfileComponent('some_component', SomeComponent::class);
```

### Remove Profile Component

Remove a specific profile component:

```php
use My\Component\SomeComponent;

UserProfilePlugin::make()
    ->removeProfileComponent('personal_info');
```

## Laravel Sanctum

Laravel Sanctum is automatically detected, and a component to manage Sanctum tokens is displayed in the profile. Control the available abilities with the following code:

```php
UserProfilePlugin::make()
    ->sanctumAbilities(['read', 'write']);
```

By default, all tokens are registered with all abilities (`[*]`).

## Two-Factor Authentication

This package is compatible with [stephenjude/filament-two-factor-authentication](https://github.com/stephenjude/filament-two-factor-authentication). Simply register the component as shown below:

```php
UserProfilePlugin::make()
    ->registerProfileComponent('2fa', \Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication::class)
```
