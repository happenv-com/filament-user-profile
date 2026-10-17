<x-happenv-filament-user-profile::section-table :aside="true">
    <x-slot name="heading">
        {{ __('happenv-filament-user-profile::default.profile.sanctum.title') }}
    </x-slot>

    <x-slot name="description">
        {{ __('happenv-filament-user-profile::default.profile.sanctum.description') }}
    </x-slot>

    <div class="">
        @if ($plainTextToken)
            <div
                class="fi-section-content-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 md:col-span-2">
                <div class="fi-section-content space-y-2 bg-warning-500 p-6 rounded">
                    <p class="text-sm">{{ __('happenv-filament-user-profile::default.profile.sanctum.create.message') }}</p>
                    <input type="text" disabled @class([
                        'w-full py-1 px-3 rounded-lg bg-gray-100 border-gray-200 dark:bg-gray-700 dark:border-gray-500',
                    ]) name="plain_text_token"
                        value="{{ $plainTextToken }}" />
                    <div class="flex items-center justify-between">
                        <div class="inline-block text-xs">
                            <x-happenv-filament-user-profile::clipboard-link :data="$plainTextToken" />
                        </div>
                        <x-filament::button :icon="\Filament\Support\Icons\Heroicon::ClipboardDocumentCheck"
                            :icon-alias="\Happenv\FilamentUserProfile\View\UserProfileIconAlias::SANCTUM_TOKEN_COPIED" size="sm" type="button"
                            wire:click="$set('plainTextToken',null)">{{ __('happenv-filament-user-profile::default.profile.sanctum.copied.label') }}
                        </x-filament::button>
                    </div>

                </div>
            </div>
        @endif
        <div style="display: {{ $plainTextToken ? 'none' : '' }}">
            {{ $this->table }}
        </div>

    </div>



</x-happenv-filament-user-profile::section-table>
