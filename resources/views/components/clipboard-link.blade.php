@props([
    'data'
])
<a x-data="{}"
    x-on:click.prevent="window.navigator.clipboard.writeText(@js($data));$tooltip('{{ __('happenv-filament-user-profile::default.clipboard.tooltip') }}');"
    href="#" class="flex items-center">
    {{ \Filament\Support\generate_icon_html(
        \Filament\Support\Icons\Heroicon::ClipboardDocument,
        alias: \Happenv\FilamentUserProfile\View\UserProfileIconAlias::CLIPBOARD_COPY,
        attributes: (new \Filament\Support\View\ComponentAttributeBag)->class(['w-4 mr-2']),
    ) }}
    <span class="">{{ __('happenv-filament-user-profile::default.clipboard.link') }}</span>
</a>
