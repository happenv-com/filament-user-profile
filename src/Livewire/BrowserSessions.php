<?php

namespace Happenv\FilamentUserProfile\Livewire;

use Alsaloul\Agent\Agent;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions as ComponentsActions;
use Filament\Schemas\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BrowserSessions extends MyProfileComponent implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'happenv-filament-user-profile::livewire.browser-sessions';

    protected string $listView = 'happenv-filament-user-profile::components.browser-sessions-list';

    public ?array $data;

    public static $sort = 50;

    public function mount(Request $request) {}

    public static function canView(): bool
    {
        return config('session.driver') === 'database';
    }

    public function form(Schema $form): Schema
    {

        return $form
            ->schema([
                Forms\Components\ViewField::make('browserSessions')
                    ->label(__('happenv-filament-user-profile::default.profile.browser_sessions.label'))
                    ->hiddenLabel()
                    ->view($this->listView)
                    ->viewData(['data' => self::getSessions()]),

                ComponentsActions::make([
                    Action::make('deleteBrowserSessions')
                        ->label(__('happenv-filament-user-profile::default.profile.browser_sessions.logout_other_sessions'))
                        ->requiresConfirmation()
                        ->modalHeading(__('happenv-filament-user-profile::default.profile.browser_sessions.logout_heading'))
                        ->modalDescription(__('happenv-filament-user-profile::default.profile.browser_sessions.logout_description'))
                        ->modalSubmitActionLabel(__('happenv-filament-user-profile::default.profile.browser_sessions.logout_action'))
                        ->form([
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->revealable()
                                ->label(__('happenv-filament-user-profile::default.fields.password'))
                                ->required(),
                        ])
                        ->action(function (array $data) {
                            self::logoutOtherBrowserSessions($data['password']);
                        })
                        ->modalWidth('2xl'),
                ]),
            ]);
    }

    public static function getSessions(): array
    {
        if (config('session.driver') !== 'database') {
            return [];
        }

        $sessions = DB::connection(config('session.connection'))->table(config('session.table'))
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->latest('last_activity')
            ->get();

        return $sessions->map(function (object $session): object {
            $agent = self::createAgent($session);

            return (object) [
                'device' => [
                    'browser' => $agent->browser(),
                    'desktop' => $agent->isDesktop(),
                    'mobile' => $agent->isMobile(),
                    'tablet' => $agent->isTablet(),
                    'platform' => $agent->platform(),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === request()->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        })->toArray();
    }

    protected static function createAgent(mixed $session)
    {
        return tap(new Agent, fn ($agent) => $agent->setUserAgent($session->user_agent));
    }

    public static function logoutOtherBrowserSessions(string $password): void
    {
        if (! Hash::check($password, Auth::user()->getAuthPassword())) {
            Notification::make()
                ->danger()
                ->title(__('happenv-filament-user-profile::default.profile.browser_sessions.incorrect_password'))
                ->send();

            return;
        }

        // @phpstan-ignore-next-line
        Auth::guard()->logoutOtherDevices($password);

        request()->session()->put([
            'password_hash_'.Auth::getDefaultDriver() => Auth::user()->getAuthPassword(),
        ]);

        self::deleteOtherSessionRecords();

        Notification::make()
            ->success()
            ->title(__('happenv-filament-user-profile::default.profile.browser_sessions.logout_success'))
            ->send();
    }

    protected static function deleteOtherSessionRecords()
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))->table(config('session.table'))
            ->where('user_id', Auth::user()->getAuthIdentifier())
            ->where('id', '!=', request()->session()->getId())
            ->delete();
    }
}
