<?php

declare(strict_types=1);

namespace Happenv\FilamentUserProfile\View;

/**
 * Icon aliases this package resolves, so a host can re-icon the profile page globally. Register them
 * exactly as Filament's own:
 *
 * ```php
 * use Filament\Support\Facades\FilamentIcon;
 * use Happenv\FilamentUserProfile\View\UserProfileIconAlias;
 *
 * FilamentIcon::register([
 *     UserProfileIconAlias::NAVIGATION_PROFILE => Phosphor::UserCircle,
 * ]);
 * ```
 *
 * The string values are part of the public contract — a host registers against them — so they may
 * not be renamed without a breaking release. They share the package's translation namespace, which
 * is what keeps them from colliding with another package's aliases.
 */
class UserProfileIconAlias
{
    /** The profile page in the panel navigation. Default: Heroicon::OutlinedDocumentText. */
    const NAVIGATION_PROFILE = 'happenv-filament-user-profile::navigation.profile';

    /**
     * The chevron collapsing a profile section.
     *
     * Resolved with Filament's own section collapse alias as the SECOND candidate, so a host that
     * re-icons Filament wholesale gets a matching chevron without knowing this package exists.
     * Default: Heroicon::ChevronDown.
     */
    const SECTION_COLLAPSE = 'happenv-filament-user-profile::section.collapse';

    /** A browser session on a desktop device. Default: Heroicon::OutlinedComputerDesktop. */
    const BROWSER_SESSIONS_DESKTOP = 'happenv-filament-user-profile::browser-sessions.desktop';

    /** A browser session on a mobile device. Default: Heroicon::OutlinedDevicePhoneMobile. */
    const BROWSER_SESSIONS_MOBILE = 'happenv-filament-user-profile::browser-sessions.mobile';

    /** The "copy to clipboard" link next to a freshly created token. Default: Heroicon::ClipboardDocument. */
    const CLIPBOARD_COPY = 'happenv-filament-user-profile::clipboard.copy';

    /** The "token copied" confirmation button. Default: Heroicon::ClipboardDocumentCheck. */
    const SANCTUM_TOKEN_COPIED = 'happenv-filament-user-profile::sanctum.token-copied';
}
