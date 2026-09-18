<?php

namespace Blougly\Domain\ValueObjects;

enum SourceKind: string
{
    case Contents = 'contents';
    case Data = 'data';
    case Site = 'site';
    case Views = 'views';
    case Assets = 'assets';
}
