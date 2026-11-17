<?php

/**
 * @param string $binDir
 * @return list<string>
 */
function autoloaderCandidates(string $binDir): array
{
    return [
        $binDir . '/../vendor/autoload.php',
        $binDir . '/../../../autoload.php',
    ];
}

/**
 * @param list<string> $candidates
 * @return string|null the first candidate that exists as a file, or null
 */
function locateAutoloader(array $candidates): ?string
{
    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            return $candidate;
        }
    }

    return null;
}
