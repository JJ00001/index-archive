<?php

namespace App\Domain\Sec\NPort\Pipeline\Download;

use App\Domain\Sec\NPort\Data\NPortQuarter;

final class ReleaseLocator
{
    public function url(NPortQuarter $quarter): string
    {
        return rtrim((string) config('sec.nport_base_url'), '/')
            .'/'.$quarter->slug().'_nport.zip';
    }
}
