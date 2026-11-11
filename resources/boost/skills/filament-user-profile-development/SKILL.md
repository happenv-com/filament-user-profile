---
name: filament-user-profile-development
description: >-
  Build the Filament "My profile" page with happenv-com/filament-user-profile. Use when
  registering MyProfilePage and UserProfilePlugin on a panel, choosing or ordering profile
  sections (profileComponents(), registerProfileComponent(), insertAfterProfileComponent(),
  replaceProfileComponent(), removeProfileComponent()), writing a custom section by extending
  MyProfileComponent ($only, canView(), getTitle()/getDescription(), submit()), customising
  PersonalInfo (avatars(), avatar_url), Sanctum tokens (sanctumAbilities()), browser sessions,
  the two-factor section with Filament MFA providers, the passkeys section, or the
  UserProfileIconAlias icons.
---

# Filament User Profile

A replacement for Filament's profile page made of stacked sections. Each section is a Livewire component; the plugin holds an ordered `key => class` list of them, registers each with Livewire under its key, and `MyProfilePage` (a subclass of Filament's `EditProfile`) renders every one whose static `canView()` allows it. Customising the profile means editing that list and writing sections that extend `MyProfileComponent`.

## When to use this skill

- Adding the profile page to a panel, or deciding which sections it shows.
- Adding a profile section of your own (preferences, company data, a read-only info panel).
- Changing a built-in section (extra fields on personal info, avatars, another avatar column).
- Wiring two-factor / passkeys / Sanctum tokens / browser sessions into the profile.

## Installation and registration

```bash
composer require happenv-com/filament-user-profile
```

Register **both** the page and the plugin. The plugin does not add the page by itself:

```php
use Happenv\FilamentUserProfile\Pages\MyProfilePage;
use Happenv\FilamentUserProfile\UserProfilePlugin;

return $panel
    ->profile(MyProfilePage::class, isSimple: false)   // inside the panel layout, not the auth card
    ->plugin(
        UserProfilePlugin::make()
            ->avatars()
            ->removeProfileComponent('sanctum_tokens')
    );
```

Filament's `->profile()` gives the route and the user-menu link. `UserProfilePlugin::profilePage()` only stores a class that nothing reads, and `registerUserMenu()`, shown in the README, does not exist (calling it is a fatal error). Do not use either.

Requires PHP 8.3+, Filament 4 or 5. Hard dependencies: `spatie/laravel-passkeys` (for the `passkeys` section) and `webard/agent` (user-agent parsing; it replaces `al-saloul/agent`, remove that one first).

## The default sections

In this order (the order of the array is the render order; `$sort` / `getSort()` exist but are not used):

| Key | Class (`Happenv\FilamentUserProfile\Livewire\`) | Shown when | Needs |
|---|---|---|---|
| `personal_info` | `PersonalInfo` | always | `name`, `email` fillable on the user; with `avatars()`, an `avatar_url` column too. |
| `update_password` | `UpdatePassword` | always | `Password::defaults()` rules; re-logs the user in after the change. |
| `passkeys` | `Passkeys` (spatie `PasskeysComponent`) | always | spatie/laravel-passkeys fully set up: its migration, user implements `Spatie\LaravelPasskeys\Models\Concerns\HasPasskeys` and uses `InteractsWithPasskeys`. Otherwise remove it. |
| `two_factor_auth` | `TwoFactorAuth` | always | Renders the management schema of every provider in `$panel->multiFactorAuthentication([...])`; empty section without MFA. |
| `browser_sessions` | `BrowserSessions` | `session.driver` is `database` | `AuthenticateSession` in the panel middleware for "log out other sessions" to take effect. |
| `sanctum_tokens` | `SanctumTokens` | `laravel/sanctum` is installed | User model `use HasApiTokens`. |

Gotchas:

- `sanctum_tokens` switches itself on as soon as Sanctum is in `composer.json`, for every user of the panel. If personal tokens are not a product decision, `removeProfileComponent('sanctum_tokens')`.
- `passkeys` is spatie's implementation (spatie's table and relying party). With Filament's MFA passkeys (`happenv-com/filament-passkeys`, built on `laravel/passkeys`) remove `passkeys`: the passkey then lives in the `two_factor_auth` section, and the spatie section would register keys the login never reads. Both packages use a `passkeys` table, so their migrations cannot both run.
- `BrowserSessions` uses the application's default guard (`Auth::guard()`), not the panel's. On a panel with a different guard, check it before relying on it.
- Removing a section removes it from the Livewire registry too (registration happens in `register()`, from the final list).

## Managing the list

```php
UserProfilePlugin::make()
    ->profileComponents([                                   // replace the whole list
        'personal_info' => PersonalInfo::class,
        'update_password' => UpdatePassword::class,
    ])
    ->registerProfileComponent('preferences', Preferences::class)              // append (or overwrite the key)
    ->insertAfterProfileComponent('personal_info', 'company', Company::class)   // insert at a position
    ->replaceProfileComponent('personal_info', App\Profile\PersonalInfo::class) // same key, your class
    ->removeProfileComponent('passkeys')
    ->sanctumAbilities(['read', 'write'])     // or ['orders:read' => 'Read orders']
    ->avatars();
```

- Keys become **Livewire component names**: keep them unique across the app and Livewire-safe (`snake_case`).
- Chain every call on the instance you pass to `->plugin()`: the panel runs the plugin's `register()`, which registers the Livewire names, at that moment.
- `insertAfterProfileComponent()` does nothing when `$afterKey` is not in the list (for example after you removed it).
- `sanctumAbilities()`: a list becomes `lowercase => label` options; an associative array is used as `ability => label`. Without abilities (default) the checkbox list is hidden and tokens get `['*']`.

## Writing a section

Extend `MyProfileComponent`. Its default view (`happenv-filament-user-profile::livewire.edit-component`) renders an aside section with `getTitle()` / `getDescription()`, your form and a Save button calling `submit()`.

```php
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Happenv\FilamentUserProfile\Livewire\MyProfileComponent;

final class Company extends MyProfileComponent
{
    /** Fields written back to the user. Keep it protected: public Livewire state is client-writable. */
    protected array $only = ['company_name', 'vat_id'];

    public function mount(): void
    {
        $this->user = Filament::auth()->user();
        $this->userClass = $this->user::class;
        $this->getForm('form')->fill($this->user->only($this->only));
    }

    public static function canView(): bool
    {
        return Filament::auth()->user()?->isCompanyAccount() ?? false;
    }

    public function getTitle(): string
    {
        return __('profile.company.title');
    }

    public function getDescription(): string
    {
        return __('profile.company.description');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_name')->required(),
                TextInput::make('vat_id'),
            ])
            ->statePath('data');      // `data` is the property submit() reads through the form
    }
}
```

What the base class gives you and expects:

- `submit()` validates the form named `form`, keeps only the keys in `$only` (via `getOnly()`), calls `$this->user->update(...)`, sends a success notification and redirects to `getRedirectUrl()` when it returns a URL. Fields outside `$only` are validated but never saved.
- The columns in `$only` must be fillable on the user model, otherwise `update()` drops them silently.
- Override `sendNotification()` for your own message (the default reuses the personal-info "saved" text), `getRedirectUrl()` to navigate after saving, or `submit()` entirely for anything that is not a plain update of the user (e.g. a related model).
- `canView()` is static and evaluated on every render. Compute from the current user; never cache per class (Octane workers serve many users).
- Read-only section: set `protected string $view` to your own Blade view (wrap it in `<x-filament::section :aside="true">` to match), leave `$only = []` and do not render a submit button.
- Sections with actions or tables implement the matching Filament contracts themselves (`HasTable` + `InteractsWithTable`, as `SanctumTokens` does). `HasActions` / `HasForms` are already on the base class.

### Changing a built-in section

Subclass it and swap it in under the same key:

```php
use Happenv\FilamentUserProfile\Livewire\PersonalInfo as BasePersonalInfo;

final class PersonalInfo extends BasePersonalInfo
{
    protected array $only = ['name', 'email', 'phone'];
    protected string $avatarColumn = 'avatar_path';

    public function getPersonalDataFormSchema(): array
    {
        return [
            ...parent::getPersonalDataFormSchema(),
            TextInput::make('phone')->tel(),
        ];
    }
}

UserProfilePlugin::make()->avatars()->replaceProfileComponent('personal_info', PersonalInfo::class);
```

`PersonalInfo` hooks: `getNameComponent()`, `getEmailComponent()` (unique on the user's table, ignoring the current user), `getPersonalDataFormSchema()`, `getAvatarUploadComponent()`, `getAvatarGroupSchema()`.

### Avatars

`->avatars()` adds an avatar `FileUpload` bound to `avatar_url`, stored on the `public` disk in `avatars/`, and saves it with the rest. The host still has to:

1. add a nullable `avatar_url` column and make it fillable;
2. run `php artisan storage:link`;
3. show it in Filament: implement `Filament\Models\Contracts\HasAvatar` with `getFilamentAvatarUrl()` returning `Storage::disk('public')->url($this->avatar_url)` when set.

Another column or disk: subclass `PersonalInfo` (`$avatarColumn`, `getAvatarUploadComponent()`) as above.

## Two-factor section

`two_factor_auth` shows what the panel configures; it implements no 2FA itself:

```php
$panel->multiFactorAuthentication([
    \Filament\Auth\MultiFactor\App\AppAuthentication::make(),
    \Happenv\FilamentPasskeys\PasskeyAuthentication::make(),   // if happenv-com/filament-passkeys is installed
]);
```

Enabled providers are listed first. Without `multiFactorAuthentication()` the section has a heading and nothing else; remove `two_factor_auth` then. A third-party 2FA Livewire component can be added with `registerProfileComponent()`.

## Icons and translations

- Navigation icon alias: `Happenv\FilamentUserProfile\View\UserProfileIconAlias::NAVIGATION_PROFILE`, registered with `FilamentIcon::register([...])`. The page's static `$navigationIcon` still wins.
- Strings live in `happenv-filament-user-profile::default.*` (shipped in `en` and `pl`); override them with `lang/vendor/happenv-filament-user-profile/{locale}/default.php`.

## Testing

No helpers ship. Sections are Livewire components: set the panel, act as the user, test them directly.

```php
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(fn () => Filament::setCurrentPanel('admin'));

it('saves the company fields', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Company::class)
        ->fillForm(['company_name' => 'Acme'])
        ->call('submit')
        ->assertHasNoFormErrors();

    expect($user->fresh()->company_name)->toBe('Acme');
});

it('offers no personal tokens', function () {
    expect(\Happenv\FilamentUserProfile\UserProfilePlugin::get()->getRegisteredMyProfileComponents()->keys())
        ->not->toContain('sanctum_tokens');
});
```

`UserProfilePlugin::get()` needs a current panel. `getRegisteredMyProfileComponents()` is what the page renders (list after `canView()`); `getProfileComponents()` is the configured list.

## Do not

- Do not register the plugin without `->profile(MyProfilePage::class, isSimple: false)`; nothing renders.
- Do not call `registerUserMenu()` or rely on `profilePage()`.
- Do not make `$only` public, or list a column in `$only` that the user must not change.
- Do not leave `passkeys` on without spatie/laravel-passkeys set up, or next to `happenv-com/filament-passkeys`.
- Do not leave `sanctum_tokens` on by accident just because Sanctum is installed.
- Do not order sections with `$sort`; order the list.
