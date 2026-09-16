<?php

namespace Happenv\FilamentUserProfile\Livewire;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Facades\FilamentView;
use Happenv\FilamentUserProfile\UserProfilePlugin;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

abstract class MyProfileComponent extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms;

    protected string $view = 'happenv-filament-user-profile::livewire.edit-component';

    public ?array $data = [];

    /** @var array<int, string> */
    protected array $only = [];

    public $user;

    public $userClass;

    public static $sort = 0;

    public function getName()
    {
        return str(static::class)->afterLast('\\')->snake();
    }

    public static function getPlugin(): UserProfilePlugin
    {
        /** @var UserProfilePlugin $plugin */
        $plugin = filament('happenv-filament-user-profile');

        return $plugin;
    }

    public function render()
    {
        return view($this->view);
    }

    public static function canView(): bool
    {
        return true;
    }

    public static function getSort(): int
    {
        return static::$sort;
    }

    public static function setSort(int $sort): void
    {
        static::$sort = $sort;
    }

    public function submit(): void
    {
        /** @var Model $userModel */
        $userModel = $this->user;

        $data = collect($this->getForm('form')->getState())->only($this->only)->all();

        $userModel->update($data);

        $this->sendNotification();

        if ($redirectUrl = $this->getRedirectUrl()) {
            $this->redirect($redirectUrl, navigate: FilamentView::hasSpaMode($redirectUrl));
        }
    }

    public function getRedirectUrl(): ?string
    {
        return null;
    }

    protected function sendNotification(): void
    {
        Notification::make()
            ->success()
            ->title(__('happenv-filament-user-profile::default.profile.personal_info.notify'))
            ->send();
    }
}
