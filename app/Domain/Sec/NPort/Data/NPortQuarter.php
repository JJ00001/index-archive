<?php

namespace App\Domain\Sec\NPort\Data;

use InvalidArgumentException;

final readonly class NPortQuarter
{
    private function __construct(
        public int $year,
        public int $quarter,
    ) {}

    public static function from(int $year, int $quarter): self
    {
        if ($quarter < 1 || $quarter > 4 || $year < 2019 || ($year === 2019 && $quarter < 4)) {
            throw new InvalidArgumentException('N-PORT datasets begin at 2019 Q4.');
        }

        return new self($year, $quarter);
    }

    public function slug(): string
    {
        return $this->year.'q'.$this->quarter;
    }
}
