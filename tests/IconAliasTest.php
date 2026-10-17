<?php

use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Filament\Support\View\SupportIconAlias;
use Happenv\FilamentUserProfile\Pages\MyProfilePage;
use Happenv\FilamentUserProfile\View\UserProfileIconAlias;
use Illuminate\Support\Facades\Blade;

/**
 * The path data of an icon, which is what tells two icons apart in rendered markup.
 */
function iconPath(Heroicon $icon): string
{
    preg_match('/ d="([^"]+)"/', \Filament\Support\generate_icon_html($icon)->toHtml(), $matches);

    return $matches[1];
}

it('uses the Heroicons by default', function () {
    expect(MyProfilePage::getNavigationIcon())->toBe(Heroicon::OutlinedDocumentText)
        ->and(Blade::render('<x-happenv-filament-user-profile::clipboard-link data="x" />'))
        ->toContain(iconPath(Heroicon::ClipboardDocument));
});

it('lets the host replace the navigation icon through its alias', function () {
    FilamentIcon::register([UserProfileIconAlias::NAVIGATION_PROFILE => Heroicon::Star]);

    expect(MyProfilePage::getNavigationIcon())->toBe(Heroicon::Star);
});

it('lets the host replace the clipboard icon through its alias', function () {
    FilamentIcon::register([UserProfileIconAlias::CLIPBOARD_COPY => Heroicon::Star]);

    expect(Blade::render('<x-happenv-filament-user-profile::clipboard-link data="x" />'))
        ->toContain(iconPath(Heroicon::Star))
        ->not->toContain(iconPath(Heroicon::ClipboardDocument));
});

it('lets the host replace the section chevron, with Filament\'s own alias as fallback', function () {
    $render = fn (): string => Blade::render(
        '<x-happenv-filament-user-profile::section-table heading="H" :collapsible="true">x</x-happenv-filament-user-profile::section-table>'
    );

    FilamentIcon::register([SupportIconAlias::SECTION_COLLAPSE_BUTTON => Heroicon::Heart]);
    expect($render())->toContain(iconPath(Heroicon::Heart));

    FilamentIcon::register([UserProfileIconAlias::SECTION_COLLAPSE => Heroicon::Star]);
    expect($render())->toContain(iconPath(Heroicon::Star))->not->toContain(iconPath(Heroicon::Heart));
});
