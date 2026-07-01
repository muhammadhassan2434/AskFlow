<?php

namespace App\Exceptions;

use Exception;

class WorkspaceInUseException extends Exception
{
    public static function cannotDelete(): self
    {
        return new self(
            'This workspace is used by one or more bots and cannot be deleted.'
        );
    }

    public static function cannotDeactivate(): self
    {
        return new self(
            'This workspace is used by one or more bots and cannot be set to inactive.'
        );
    }
}
