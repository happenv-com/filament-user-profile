<?php

namespace Happenv\FilamentUserProfile\Livewire;

use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class PersonalInfo extends MyProfileComponent
{
    protected string $view = 'happenv-filament-user-profile::livewire.edit-component';

    public ?array $data = [];

    public $user;

    public $userClass;

    protected array $only = ['name', 'email'];

    public function getTitle(): string
    {
        return __('happenv-filament-user-profile::default.profile.personal_info.heading');
    }

    public function getDescription(): string
    {
        return __('happenv-filament-user-profile::default.profile.personal_info.subheading');
    }

    public function mount(): void
    {
        $this->user = Filament::getCurrentPanel()->auth()->user();

        $this->userClass = get_class($this->user);

        /** @var Model $userModel */
        $userModel = $this->user;
        //
        $this->getForm('form')->fill($userModel->only($this->only));
    }

    public function getAvatarUploadComponent()
    {
        $fileUpload = FileUpload::make('avatar_url')
            ->label(__('happenv-filament-user-profile::default.fields.avatar'))
            ->avatar()
            ->imagePreviewHeight('200px')
            ->imageAspectRatio('1:1')
            ->disk('public')
            ->directory('avatars');

        return $fileUpload;
    }

    public function getAvatarGroupSchema(): array
    {
        return [
            $this->getAvatarUploadComponent(),
        ];
    }

    public function getPersonalDataFormSchema(): array
    {
        return [
            $this->getNameComponent(),
            $this->getEmailComponent(),
        ];
    }

    protected function getProfileFormSchema(): array
    {
        if (! $this->getPlugin()->hasAvatars()) {
            return $this->getPersonalDataFormSchema();
        }

        $groupFields = Group::make([
            ...$this->getAvatarGroupSchema(),

            Group::make(
                $this->getPersonalDataFormSchema(),
            )->columnSpan(2),

        ])->columnSpanFull()->columns(3);

        return [
            $groupFields,
        ];
    }

    protected function getNameComponent(): Forms\Components\TextInput
    {
        return Forms\Components\TextInput::make('name')
            ->required()
            ->label(__('happenv-filament-user-profile::default.fields.name'));
    }

    protected function getEmailComponent(): Forms\Components\TextInput
    {
        /** @var Model $userModel */
        $userModel = $this->user;

        return Forms\Components\TextInput::make('email')
            ->required()
            ->email()
            ->unique($this->userClass, ignorable: $userModel)
            ->label(__('happenv-filament-user-profile::default.fields.email'));
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema($this->getProfileFormSchema())
            ->statePath('data');
    }
}
