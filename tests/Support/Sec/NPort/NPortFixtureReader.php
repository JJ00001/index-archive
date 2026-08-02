<?php

namespace Tests\Support\Sec\NPort;

use App\Domain\Sec\NPort\Data\NPortFile;
use RuntimeException;

final class NPortFixtureReader
{
    /**
     * @var list<string>
     */
    private array $headers;

    /**
     * @var list<list<string>>
     */
    private array $rows;

    private function __construct(public readonly string $path)
    {
        [$this->headers, $this->rows] = self::read($path);
    }

    public static function valid(NPortFile $file): self
    {
        return new self(self::fixturesDirectory().'/valid/'.$file->filename());
    }

    public static function malformed(NPortFile $file): self
    {
        return new self(self::fixturesDirectory().'/malformed/'.$file->filename());
    }

    public static function fixturesDirectory(): string
    {
        return dirname(__DIR__, 3).'/Fixtures/Sec/NPort';
    }

    /**
     * @return list<string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * @return list<list<string>>
     */
    public function rows(): array
    {
        return $this->rows;
    }

    /**
     * @return list<int>
     */
    public function rowWidths(): array
    {
        return array_map(count(...), $this->rows);
    }

    /**
     * @return list<list<string>>
     */
    public function wrongWidthRows(): array
    {
        $expectedWidth = count($this->headers);

        return array_values(array_filter(
            $this->rows,
            fn (array $row): bool => count($row) !== $expectedWidth,
        ));
    }

    /**
     * @return list<array<string, string>>
     */
    public function records(): array
    {
        return array_map($this->recordFrom(...), $this->rows);
    }

    /**
     * @return list<array<string, string>>
     */
    public function where(string $header, string $value): array
    {
        $this->guardHeaderExists($header);

        return array_values(array_filter(
            $this->records(),
            fn (array $record): bool => $record[$header] === $value,
        ));
    }

    /**
     * @return array<string, string>
     */
    public function firstWhere(string $header, string $value): array
    {
        $record = $this->where($header, $value)[0] ?? null;

        if ($record === null) {
            throw new RuntimeException(
                "Fixture [{$this->path}] has no record where [{$header}] equals [{$value}].",
            );
        }

        return $record;
    }

    /**
     * @return array{list<string>, list<list<string>>}
     */
    private static function read(string $path): array
    {
        $handle = self::open($path);

        try {
            return [self::readHeaders($handle, $path), self::readRows($handle)];
        } finally {
            fclose($handle);
        }
    }

    /**
     * @return resource
     */
    private static function open(string $path)
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new RuntimeException("Unable to open fixture [{$path}].");
        }

        return $handle;
    }

    /**
     * @param  resource  $handle
     * @return list<string>
     */
    private static function readHeaders($handle, string $path): array
    {
        $headers = fgetcsv($handle, null, "\t", '"', '');

        if ($headers === false) {
            throw new RuntimeException("Fixture [{$path}] has no header row.");
        }

        return $headers;
    }

    /**
     * @param  resource  $handle
     * @return list<list<string>>
     */
    private static function readRows($handle): array
    {
        $rows = [];

        while (($row = fgetcsv($handle, null, "\t", '"', '')) !== false) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param  list<string>  $row
     * @return array<string, string>
     */
    private function recordFrom(array $row): array
    {
        $record = array_combine($this->headers, $row);

        if ($record === false) {
            throw new RuntimeException("Fixture [{$this->path}] contains a structurally invalid record.");
        }

        return $record;
    }

    private function guardHeaderExists(string $header): void
    {
        if (! in_array($header, $this->headers, true)) {
            throw new RuntimeException("Fixture [{$this->path}] has no [{$header}] header.");
        }
    }
}
