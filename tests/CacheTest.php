<?php

declare(strict_types=1);

namespace Tests\Omexon\Filesystem;

use Omexon\Filesystem\Cache;
use Omexon\Filesystem\Directory;
use Omexon\Filesystem\File;
use Exception;
use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    /** @var string */
    private $tempDirectory;

    /**
     * Test key.
     */
    public function testKey(): void
    {
        $key1 = Cache::key('test');
        $key2 = Cache::key('test', ['test1', 'test2']);
        $this->assertGreaterThan(20, strlen($key1));
        $this->assertGreaterThan(20, strlen($key2));
        $this->assertNotEquals($key1, $key2);
    }

    /**
     * Test path.
     *
     * @throws Exception
     */
    public function testPath(): void
    {
        // Check that path is not set.
        $this->assertNull(Cache::path());

        // Check path set.
        Cache::path($this->tempDirectory);
        $this->assertEquals($this->tempDirectory, Cache::path());

        // Check if path is writable.
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Path is not writable.');
        Cache::path($this->tempDirectory . '/test');

        $path = Cache::path();
        Cache::path($path . '1/', true);
        $this->assertEquals($path . '1', Cache::path());
    }

    /**
     * Test lifetime not set.
     */
    public function testLifetimeNotSet(): void
    {
        $this->assertEquals(0, Cache::lifetime());
    }

    /**
     * Test lifetime set with seconds.
     */
    public function testLifetimeSetWithSeconds(): void
    {
        Cache::lifetime(4);
        $this->assertEquals(4, Cache::lifetime());
    }

    /**
     * Test lifetime set with minutes.
     */
    public function testLifetimeSetWithMinutes(): void
    {
        Cache::lifetime('4m');
        $this->assertEquals(240, Cache::lifetime());
    }

    /**
     * Test lifetime set with hours.
     */
    public function testLifetimeSetWithHours(): void
    {
        Cache::lifetime('4h');
        $this->assertEquals(14400, Cache::lifetime());
    }

    /**
     * Test lifetime not set (storage 'test').
     */
    public function testLifetimeNotSetStorageTest(): void
    {
        $this->assertEquals(0, Cache::lifetime(null, 'test'));
    }

    /**
     * Test lifetime set with seconds (storage 'test').
     */
    public function testLifetimeSetWithSecondsStorageTest(): void
    {
        Cache::lifetime(4, 'test');
        $this->assertEquals(4, Cache::lifetime(null, 'test'));
    }

    /**
     * Test lifetime set with minutes (storage 'test').
     */
    public function testLifetimeSetWithMinutesStorageTest(): void
    {
        Cache::lifetime('4m', 'test');
        $this->assertEquals(240, Cache::lifetime(null, 'test'));
    }

    /**
     * Test lifetime set with hours (storage 'test').
     */
    public function testLifetimeSetWithHoursStorageTest(): void
    {
        Cache::lifetime('4h', 'test');
        $this->assertEquals(14400, Cache::lifetime(null, 'test'));
    }

    /**
     * Test expiration.
     *
     * @throws Exception
     */
    public function testExpiration(): void
    {
        Cache::path($this->tempDirectory);
        Cache::lifetime(4);

        // Validate that expiration is 0 when cache file does not exist.
        $this->assertEquals(0, Cache::expiration('test'));

        Cache::put('test', 'test');

        // Validate that expiration is != 0 when cache file does exist.
        $expiration = Cache::expiration('test');
        $this->assertLessThan(time() + 100, $expiration);
        $this->assertGreaterThan(time() - 100, $expiration);

        $cacheFilename = Cache::path() . '/global/' . Cache::key('test');
        if (file_exists($cacheFilename)) {
            File::delete($cacheFilename);
        }
        $expiration = Cache::expiration('test');
        $this->assertNull($expiration);
    }

    /**
     * Test get.
     *
     * @throws Exception
     */
    public function testGet(): void
    {
        Cache::path($this->tempDirectory);
        Cache::lifetime(4);

        // Validate that cache is null when cache file does not exist.
        $this->assertNull(Cache::get('test'));

        Cache::put('test', 'test');

        // Validate that cache has valid content.
        $this->assertEquals('test', Cache::get('test'));

        // Modify cache file with older datetime for testing.
        $filename = $this->tempDirectory . '/global/' . Cache::key('test');
        $cacheContent = file_get_contents($filename);
        $cacheContent = explode('||', $cacheContent);
        $cacheContent[0] = time() - 100;
        file_put_contents($filename, implode('||', $cacheContent));

        // Validate that cache file does not exist when to old.
        $this->assertNull(Cache::get('test'));
    }

    /**
     * Test put.
     *
     * @throws Exception
     */
    public function testPut(): void
    {
        Cache::path($this->tempDirectory);
        Cache::lifetime(4);

        $storage = 'global';
        $key = Cache::key('test');
        $cacheFilename = $this->tempDirectory . '/' . $storage . '/' . $key;

        // Check that cache file does not exist.
        $this->assertFileNotExists($cacheFilename);

        // Check that cache file do exist.
        Cache::put('test', 'test');
        $this->assertFileExists($cacheFilename);

        // Check cache content.
        $cacheContent = @file_get_contents($cacheFilename);
        $cacheContent = explode('||', $cacheContent);
        $this->assertLessThan(time() + 100, $cacheContent[0]);
        $this->assertGreaterThan(time() - 100, $cacheContent[0]);
        $this->assertEquals('test', unserialize($cacheContent[1]));
    }

    /**
     * Test put path not set.
     *
     * @throws Exception
     */
    public function testPutPathNotSet(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Path not set.');
        Cache::put('test', 'test');
    }

    /**
     * Test put lifetime not set.
     *
     * @throws Exception
     */
    public function testPutLifetimeNotSet(): void
    {
        Cache::path($this->tempDirectory);
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Lifetime not set.');
        Cache::put('test', 'test');
    }

    /**
     * Test has.
     *
     * @throws Exception
     */
    public function testHas(): void
    {
        // Validate that key does not exist.
        $this->assertFalse(Cache::has('test'));

        // Put test data.
        Cache::path($this->tempDirectory);
        Cache::lifetime(4);
        Cache::put('test', 'test');

        // Validate that key does exist.
        $this->assertTrue(Cache::has('test'));
    }

    /**
     * Test forget.
     *
     * @throws Exception
     */
    public function testForget(): void
    {
        // Put test data.
        Cache::path($this->tempDirectory);
        Cache::lifetime(4);
        Cache::put('test', 'test');

        // Validate that cache file exist before forget().
        $this->assertTrue(Cache::has('test'));

        Cache::forget('test');

        // Validate that cache file do not exist after forget().
        $this->assertFalse(Cache::has('test'));
    }

    /**
     * Test flush.
     *
     * @throws Exception
     */
    public function testFlush(): void
    {
        // Put test data.
        Cache::path($this->tempDirectory);
        Cache::lifetime(4);
        Cache::put('test1', 'test');
        Cache::put('test2', 'test');

        // Validate that files exist before flushing cache.
        $this->assertTrue(Cache::has('test1'));
        $this->assertTrue(Cache::has('test2'));

        Cache::flush();

        // Validate that files does not exist after flushing cache.
        $this->assertFalse(Cache::has('test1'));
        $this->assertFalse(Cache::has('test2'));
    }

    /**
     * Setup.
     *
     * @throws \Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDirectory = sys_get_temp_dir();
        $this->tempDirectory .= '/' . str_replace('.', '', microtime(true));
        Directory::make($this->tempDirectory);
        Cache::path(null, true);
        Cache::lifetime(0);
    }

    /**
     * Tear down.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Directory::delete($this->tempDirectory);
    }
}
