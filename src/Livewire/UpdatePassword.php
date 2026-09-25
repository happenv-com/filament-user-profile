<?php

namespace Happenv\FilamentUserProfile\Livewire;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UpdatePassword extends MyProfileComponent
{
    protected string $view = 'happenv-filament-user-profile::livewire.edit-component';

    public ?array $data = [];

    public $user;

    public static $sort = 20;

    public function getTitle(): string
    {
        return __('happenv-filament-user-profile::default.profile.password.heading');
    }

    public function getDescription(): string
    {
        return __('happenv-filament-user-profile::default.profile.password.subheading');
    }

    public function mount()
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('current_password')
                    ->label(__('happenv-filament-user-profile::default.password_confirm.current_password'))
                    ->required()
                    ->revealable()
                    ->password()
                    ->autocomplete('current-password')
                    ->currentPassword(guard: Filament::getAuthGuard()),
                Forms\Components\TextInput::make('new_password')
                    ->label(__('happenv-filament-user-profile::default.fields.new_password'))
                    ->password()
                    ->revealable()
                    ->autocomplete(false)
                    ->rule(Password::defaults())
                    ->showAllValidationMessages()
                    ->required(),
                Forms\Components\TextInput::make('new_password_confirmation')
                    ->label(__('happenv-filament-user-profile::default.fields.new_password_confirmation'))
                    ->password()
                    ->revealable()
                    ->autocomplete(false)
                    ->same('new_password')
                    ->required(),
            ])
            ->inlineLabel()
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = collect($this->getForm('form')->getState())
            ->only('new_password')
            ->all();

        $this->user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        session()->forget('password_hash_'.Filament::getCurrentPanel()->getAuthGuard());

        Filament::auth()->login($this->user);

        $this->reset(['data']);

        Notification::make()
            ->success()
            ->title(__('happenv-filament-user-profile::default.profile.password.notify'))
            ->send();
    }
}
