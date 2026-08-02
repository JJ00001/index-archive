<?php

namespace Tests\Support\Sec\NPort;

use App\Domain\Sec\NPort\Data\NPortFile;
use RuntimeException;
use ZipArchive;

final class NPortFixtureArchive
{
    public static function create(string $fixtureDirectory, string $destination): string
    {
        $archive = self::open($destination);

        foreach (NPortFile::cases() as $file) {
            self::addFixture($archive, $file, $fixtureDirectory, $destination);
        }

        self::close($archive, $destination);

        return $destination;
    }

    private static function open(string $destination): ZipArchive
    {
        $archive = new ZipArchive;
        $result = $archive->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        if ($result !== true) {
            throw new RuntimeException(sprintf(
                'Unable to open fixture archive [%s] (ZipArchive error %s).',
                $destination,
                $result,
            ));
        }

        return $archive;
    }

    private static function addFixture(
        ZipArchive $archive,
        NPortFile $file,
        string $fixtureDirectory,
        string $destination,
    ): void {
        $source = $fixtureDirectory.DIRECTORY_SEPARATOR.$file->filename();

        if ($archive->addFile($source, $file->filename())) {
            return;
        }

        $archive->close();

        throw new RuntimeException(sprintf(
            'Unable to add fixture file [%s] to archive [%s].',
            $source,
            $destination,
        ));
    }

    private static function close(ZipArchive $archive, string $destination): void
    {
        if ($archive->close()) {
            return;
        }

        throw new RuntimeException(sprintf(
            'Unable to close fixture archive [%s].',
            $destination,
        ));
    }
}
