<?php

use Happenv\FilamentUserProfile\Livewire\BrowserSessions;

function agentFor(string $userAgent): object
{
    $method = new ReflectionMethod(BrowserSessions::class, 'createAgent');
    $method->setAccessible(true);

    return $method->invoke(null, (object) ['user_agent' => $userAgent]);
}

it('parses a desktop user agent', function () {
    $agent = agentFor('Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36');

    expect($agent->browser())->toBe('Chrome')
        ->and($agent->platform())->toBe('OS X')
        ->and($agent->isDesktop())->toBeTrue()
        ->and($agent->isMobile())->toBeFalse()
        ->and($agent->isTablet())->toBeFalse();
});

it('parses a mobile user agent', function () {
    $agent = agentFor('Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1');

    expect($agent->browser())->toBe('Safari')
        ->and($agent->platform())->toBe('iOS')
        ->and($agent->isMobile())->toBeTrue()
        ->and($agent->isDesktop())->toBeFalse();
});

it('parses a tablet user agent', function () {
    $agent = agentFor('Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1');

    expect($agent->isTablet())->toBeTrue()
        ->and($agent->isDesktop())->toBeFalse();
});

it('handles an empty user agent', function () {
    $agent = agentFor('');

    expect($agent->browser())->toBeFalse()
        ->and($agent->platform())->toBeFalse();
});
