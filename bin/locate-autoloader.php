<?php

/**
 * Resolve the autoloader for the layout this package runs in.
 *
 * The installed layout (vendor/felipeverse/blougly/bin) is detected by the
 * Composer marker `vendor/composer/installed.json` in the consumer's vendor
 * dir, and the consumer autoloader is resolved. A repo checkout's own
 * vendor/ marker wins when both markers are present. With neither marker we
 * assume a repo checkout and resolve only the repo autoloader, so a stray
 * autoloader outside the package is never picked up by accident. The markers
 * are positional, not authenticated: anything that can write both
 * `composer/installed.json` and `autoload.php` two levels above this package
 * can steer resolution. The repo probe assumes the default vendor-dir;
 * custom vendor-dir checkouts fall through to the "composer install" error.
 */
function locateAutoloader(string $binDir): ?string
{
    if (is_file($binDir . '/../vendor/composer/installed.json')) {
        $candidate = $binDir . '/../vendor/autoload.php';
    } elseif (is_file($binDir . '/../../../composer/installed.json')) {
        $candidate = $binDir . '/../../../autoload.php';
    } else {
        $candidate = $binDir . '/../vendor/autoload.php';
    }

    return is_file($candidate) ? $candidate : null;
}
