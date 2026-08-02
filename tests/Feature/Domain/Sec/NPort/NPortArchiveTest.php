<?php

use App\Domain\Sec\Imports\Models\DatasetRelease;
use App\Domain\Sec\NPort\Data\NPortQuarter;
use App\Domain\Sec\NPort\Pipeline\Download\ArchiveDownloader;
use App\Domain\Sec\NPort\Pipeline\Download\ReleaseLocator;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config([
        'sec.archive_disk' => 'sec',
        'sec.user_agent' => 'Index Archive test@example.com',
        'sec.retry_delay_milliseconds' => 0,
    ]);

    Storage::fake('sec');
    Http::preventStrayRequests();
});

it('locates the official SEC archive for a quarter', function () {
    expect(app(ReleaseLocator::class)->url(NPortQuarter::from(2026, 1)))
        ->toBe('https://www.sec.gov/files/dera/data/form-n-port-data-sets/2026q1_nport.zip');
});

it('requires an SEC user agent before sending a request', function () {
    config(['sec.user_agent' => '  ']);
    Http::fake();

    expect(fn () => app(ArchiveDownloader::class)->retrieve(NPortQuarter::from(2026, 1)))
        ->toThrow(RuntimeException::class, 'SEC_USER_AGENT');

    Http::assertNothingSent();
});

it('stores an archive by checksum and reuses its release record', function () {
    $contents = 'zip-bytes';
    $sha256 = hash('sha256', $contents);
    $expectedUrl = 'https://www.sec.gov/files/dera/data/form-n-port-data-sets/2026q1_nport.zip';
    $expectedPath = "sec/nport/2026/q1/{$sha256}.zip";
    Http::fake(fn () => Http::response($contents));

    $first = app(ArchiveDownloader::class)->retrieve(NPortQuarter::from(2026, 1));
    $second = app(ArchiveDownloader::class)->retrieve(NPortQuarter::from(2026, 1));

    expect($first->is($second))->toBeTrue()
        ->and($first->version)->toBe(1)
        ->and($first->source_url)->toBe($expectedUrl)
        ->and($first->archive_disk)->toBe('sec')
        ->and($first->archive_path)->toBe($expectedPath)
        ->and($first->byte_size)->toBe(strlen($contents))
        ->and($first->sha256)->toBe($sha256)
        ->and(DatasetRelease::query()->count())->toBe(1)
        ->and(Storage::disk('sec')->get($expectedPath))->toBe($contents);

    Http::assertSent(fn (Request $request): bool => $request->url() === $expectedUrl
        && $request->hasHeader('User-Agent', 'Index Archive test@example.com'));
    Http::assertSentCount(2);
});

it('retains prior archive versions when SEC publishes different bytes', function () {
    Http::fakeSequence()
        ->push('first-zip')
        ->push('corrected-zip');

    $first = app(ArchiveDownloader::class)->retrieve(NPortQuarter::from(2026, 1));
    $corrected = app(ArchiveDownloader::class)->retrieve(NPortQuarter::from(2026, 1));

    expect($first->version)->toBe(1)
        ->and($corrected->version)->toBe(2)
        ->and($corrected->sha256)->not->toBe($first->sha256)
        ->and(DatasetRelease::query()->count())->toBe(2);

    Storage::disk('sec')->assertExists([$first->archive_path, $corrected->archive_path]);
});

it('retries transient connection and server failures', function () {
    Http::fakeSequence()
        ->pushFailedConnection('Connection failed.')
        ->push('after-connection-retry')
        ->push('server-failed', 503)
        ->push('after-server-retry');

    $first = app(ArchiveDownloader::class)->retrieve(NPortQuarter::from(2026, 1));
    $second = app(ArchiveDownloader::class)->retrieve(NPortQuarter::from(2026, 2));

    expect(Storage::disk('sec')->get($first->archive_path))->toBe('after-connection-retry')
        ->and(Storage::disk('sec')->get($second->archive_path))->toBe('after-server-retry');

    Http::assertSentCount(4);
});

it('does not retry a client error', function () {
    Http::fake(['*' => Http::response('not found', 404)]);

    expect(fn () => app(ArchiveDownloader::class)->retrieve(NPortQuarter::from(2026, 1)))
        ->toThrow(RequestException::class);

    Http::assertSentCount(1);
    expect(DatasetRelease::query()->count())->toBe(0);
});

it('fails without recording a release when archive storage fails', function () {
    $temporaryFilesBefore = glob(sys_get_temp_dir().'/sec-nport-*') ?: [];
    $disk = Mockery::mock(FilesystemAdapter::class);
    $disk->shouldReceive('exists')->once()->andReturnFalse();
    $disk->shouldReceive('put')
        ->once()
        ->with(Mockery::type('string'), Mockery::on(is_resource(...)))
        ->andReturnFalse();

    Storage::shouldReceive('disk')->once()->with('sec')->andReturn($disk);
    Http::fake(['*' => Http::response('zip-bytes')]);

    expect(fn () => app(ArchiveDownloader::class)->retrieve(NPortQuarter::from(2026, 1)))
        ->toThrow(RuntimeException::class, 'Unable to store SEC archive');

    expect(DatasetRelease::query()->count())->toBe(0)
        ->and(glob(sys_get_temp_dir().'/sec-nport-*') ?: [])->toBe($temporaryFilesBefore);
});
