<?php

namespace Blougly\Ports\Outbound;

interface FileSystem
{
    public function read(string $path): string;
    public function write(string $path, string $content): void;
    public function delete(string $path): void;
    public function allFiles(string $directory): array;
    public function exists(string $path): bool;
    public function copy(string $from, string $to): void;
}
