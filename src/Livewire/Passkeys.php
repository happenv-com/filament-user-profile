<?php

declare(strict_types=1);

namespace Happenv\FilamentUserProfile\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\View\View;
use Spatie\LaravelPasskeys\Livewire\PasskeysComponent;

final class Passkeys extends PasskeysComponent implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label(__('happenv-filament-user-profile::default.profile.passkeys.delete'))
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn (array $arguments) => $this->deletePasskey($arguments['passkey']));
    }

    public function deletePasskey(int|string $passkeyId): void
    {
        parent::deletePasskey($passkeyId);

        Notification::make()
            ->title(__('happenv-filament-user-profile::default.profile.passkeys.deleted_notification_title'))
            ->success()
            ->send();
    }

    public function storePasskey(string $passkey): void
    {
        parent::storePasskey($passkey);

        Notification::make()
            ->title(__('happenv-filament-user-profile::default.profile.passkeys.created_notification_title'))
            ->success()
            ->send();
    }

    public function render(): View
    {
        /** @var View $view */
        $view = view('happenv-filament-user-profile::livewire.passkeys', data: [
            'passkeys' => $this->currentUser()->passkeys()->get(),
        ]);

        return $view;
    }
}
