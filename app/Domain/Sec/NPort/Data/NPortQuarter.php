<?php

namespace App\Domain\Sec\NPort\Data;

use InvalidArgumentException;

final readonly class NPortQuarter
{
    public const int FIRST_PUBLIC_YEAR = 2019;

    public const int FIRST_PUBLIC_QUARTER = 4;

    private function __construct(
        public int $year,
        public int $quarter,
    ) {}

    public static function from(int $year, int $quarter): self
    {
        $isBeforeFirstPublicQuarter = $year < self::FIRST_PUBLIC_YEAR
            || ($year === self::FIRST_PUBLIC_YEAR && $quarter < self::FIRST_PUBLIC_QUARTER);

        if ($quarter < 1 || $quarter > 4 || $isBeforeFirstPublicQuarter) {
            throw new InvalidArgumentException(sprintf(
                'N-PORT datasets begin at %d Q%d.',
                self::FIRST_PUBLIC_YEAR,
                self::FIRST_PUBLIC_QUARTER,
            ));
        }

        return new self($year, $quarter);
    }

    public static function firstPublic(): self
    {
        return new self(self::FIRST_PUBLIC_YEAR, self::FIRST_PUBLIC_QUARTER);
    }

    public function slug(): string
    {
        return $this->year.'q'.$this->quarter;
    }
}
