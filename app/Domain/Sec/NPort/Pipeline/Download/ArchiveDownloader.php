<?php

namespace App\Domain\Sec\NPort\Pipeline\Download;

use App\Domain\Sec\Imports\Enums\DatasetType;
use App\Domain\Sec\Imports\Models\DatasetRelease;
use App\Domain\Sec\NPort\Data\NPortQuarter;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

final readonly class ArchiveDownloader
{
    public function __construct(private ReleaseLocator $releaseLocator) {}

    public function retrieve(NPortQuarter $quarter): DatasetRelease
    {
        $userAgent = $this->configuredUserAgent();
        $archiveDisk = $this->configuredArchiveDisk();
        $sourceUrl = $this->releaseLocator->url($quarter);
        $temporaryPath = $this->createTemporaryPath();

        try {
            $this->download($sourceUrl, $userAgent, $temporaryPath);

            $sha256 = $this->checksum($temporaryPath);
            $byteSize = $this->byteSize($temporaryPath);
            $archivePath = $this->archivePath($quarter, $sha256);

            $this->storeArchive($archiveDisk, $archivePath, $temporaryPath);

            return $this->recordRelease(
                $quarter,
                $sourceUrl,
                $archiveDisk,
                $archivePath,
                $byteSize,
                $sha256,
            );
        } finally {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }
        }
    }

    private function configuredUserAgent(): string
    {
        $userAgent = trim((string) config('sec.user_agent'));

        if ($userAgent === '') {
            throw new RuntimeException(
                'SEC_USER_AGENT must include an application or company name and contact email.',
            );
        }

        return $userAgent;
    }

    private function configuredArchiveDisk(): string
    {
        $archiveDisk = trim((string) config('sec.archive_disk'));

        if ($archiveDisk === '') {
            throw new RuntimeException('SEC_ARCHIVE_DISK must name a configured filesystem disk.');
        }

        return $archiveDisk;
    }

    private function createTemporaryPath(): string
    {
        $temporaryPath = tempnam(sys_get_temp_dir(), 'sec-nport-');

        if ($temporaryPath === false) {
            throw new RuntimeException('Unable to create a temporary SEC archive file.');
        }

        return $temporaryPath;
    }

    private function download(string $sourceUrl, string $userAgent, string $temporaryPath): void
    {
        Http::withHeaders(['User-Agent' => $userAgent])
            ->timeout((int) config('sec.timeout_seconds'))
            ->retry(
                (int) config('sec.retry_attempts'),
                (int) config('sec.retry_delay_milliseconds'),
                $this->shouldRetry(...),
            )
            ->sink($temporaryPath)
            ->get($sourceUrl)
            ->throw();
    }

    private function shouldRetry(Throwable $exception): bool
    {
        return $exception instanceof ConnectionException
            || ($exception instanceof RequestException && $exception->response->serverError());
    }

    private function checksum(string $temporaryPath): string
    {
        $sha256 = hash_file('sha256', $temporaryPath);

        if ($sha256 === false) {
            throw new RuntimeException('Unable to checksum the downloaded SEC archive.');
        }

        return $sha256;
    }

    private function byteSize(string $temporaryPath): int
    {
        $byteSize = filesize($temporaryPath);

        if ($byteSize === false) {
            throw new RuntimeException('Unable to measure the downloaded SEC archive.');
        }

        return $byteSize;
    }

    private function archivePath(NPortQuarter $quarter, string $sha256): string
    {
        return "sec/nport/{$quarter->year}/q{$quarter->quarter}/{$sha256}.zip";
    }

    private function storeArchive(string $archiveDisk, string $archivePath, string $temporaryPath): void
    {
        $disk = Storage::disk($archiveDisk);

        if ($disk->exists($archivePath)) {
            return;
        }

        $stream = fopen($temporaryPath, 'rb');

        if ($stream === false) {
            throw new RuntimeException('Unable to open the downloaded SEC archive.');
        }

        try {
            if (! $disk->put($archivePath, $stream)) {
                throw new RuntimeException("Unable to store SEC archive on disk [{$archiveDisk}].");
            }
        } finally {
            fclose($stream);
        }
    }

    private function recordRelease(
        NPortQuarter $quarter,
        string $sourceUrl,
        string $archiveDisk,
        string $archivePath,
        int $byteSize,
        string $sha256,
    ): DatasetRelease {
        return DB::transaction(fn (): DatasetRelease => $this->findOrCreateRelease(
            $quarter,
            $sourceUrl,
            $archiveDisk,
            $archivePath,
            $byteSize,
            $sha256,
        ), attempts: 3);
    }

    private function findOrCreateRelease(
        NPortQuarter $quarter,
        string $sourceUrl,
        string $archiveDisk,
        string $archivePath,
        int $byteSize,
        string $sha256,
    ): DatasetRelease {
        $releases = DatasetRelease::query()
            ->where('dataset', DatasetType::NPort)
            ->where('year', $quarter->year)
            ->where('quarter', $quarter->quarter)
            ->lockForUpdate()
            ->get();

        $existingRelease = $releases->firstWhere('sha256', $sha256);

        if ($existingRelease !== null) {
            return $existingRelease;
        }

        return DatasetRelease::query()->create([
            'dataset' => DatasetType::NPort,
            'year' => $quarter->year,
            'quarter' => $quarter->quarter,
            'version' => ($releases->max('version') ?? 0) + 1,
            'source_url' => $sourceUrl,
            'archive_disk' => $archiveDisk,
            'archive_path' => $archivePath,
            'byte_size' => $byteSize,
            'sha256' => $sha256,
            'retrieved_at' => now(),
        ]);
    }
}
