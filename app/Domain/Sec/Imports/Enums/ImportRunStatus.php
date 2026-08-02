<?php

namespace App\Domain\Sec\Imports\Enums;

enum ImportRunStatus: string
{
    case Running = 'running';
    case Completed = 'completed';
    case CompletedWithWarnings = 'completed_with_warnings';
    case Failed = 'failed';
}
