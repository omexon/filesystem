<?php

declare(strict_types=1);

namespace Tests\Omexon\Filesystem;

use Omexon\Filesystem\Directory;
use Omexon\Filesystem\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    /** @var string */
    private $tempDirectory;

    /**
     * Test get.
     */
    public function testGet(): void
    {
        $test = 'test';
        $filename = File::getTempFilename($this->tempDirectory);
        File::put($filename, $test);
        $this->assertEquals($test, File::get($filename));
    }

    /**
     * Test get default value.
     */
    public function testGetDefaultValue(): void
    {
        $filename = File::getTempFilename($this->tempDirectory);
        if (File::exist($filename)) {
            File::delete($filename);
        }
        $check = md5((string)mt_rand(1, 100000));
        $checkValue = File::get($filename, $check);
        $this->assertEquals($check, $checkValue);
    }

    /**
     * Test put.
     */
    public function testPut(): void
    {
        $this->testGet();
    }

    /**
     * Append.
     */
    public function testAppend(): void
    {
        $test = 'test';

        // Test when file does not exists.
        $filename = File::getTempFilename($this->tempDirectory);
        File::append($filename, 'X' . $test);
        $this->assertEquals('X' . $test, File::get($filename));

        // Test when file exists.
        $filename = File::getTempFilename($this->tempDirectory);
        File::put($filename, $test);
        File::append($filename, 'X' . $test);
        $this->assertEquals($test . 'X' . $test, File::get($filename));
    }

    /**
     * Test prepend.
     */
    public function testPrepend(): void
    {
        $test = 'test';

        // Test when file does not exists.
        $filename = File::getTempFilename($this->tempDirectory);
        File::prepend($filename, $test . 'X');
        $this->assertEquals($test . 'X', File::get($filename));

        // Test when file exists.
        $filename = File::getTempFilename($this->tempDirectory);
        File::put($filename, $test);
        File::prepend($filename, $test . 'X');
        $this->assertEquals($test . 'X' . $test, File::get($filename));
    }

    /**
     * Test prepend file not exist.
     */
    public function testPrependFileNotExist(): void
    {
        $filename = File::getTempFilename($this->tempDirectory);
        if (File::exist($filename)) {
            File::delete($filename);
        }
        $check = md5((string)mt_rand(1, 100000));
        File::prepend($filename, $check);
        $this->assertEquals($check, File::get($filename));
    }

    /**
     * Test get lines.
     */
    public function testGetLines(): void
    {
        $lines = ['test1', 'test2'];

        // Test load with "\n".
        $filename = File::getTempFilename($this->tempDirectory);
        File::put($filename, implode("\n", $lines));
        $this->assertEquals($lines, File::getLines($filename));

        // Test load with "\r\n".
        $filename = File::getTempFilename($this->tempDirectory);
        File::put($filename, implode("\r\n", $lines));
        $this->assertEquals($lines, File::getLines($filename));

        // Test default.
        File::delete($filename);
        $this->assertEquals(['davs'], File::getLines($filename, ['davs']));
    }

    /**
     * Test put line.
     */
    public function testPutLine(): void
    {
        $line = 'test1';
        $filename = File::getTempFilename($this->tempDirectory);
        File::putLine($filename, $line);
        $this->assertEquals([$line], File::getLines($filename));
    }

    /**
     * Test put lines.
     */
    public function testPutLines(): void
    {
        $lines = ['test1', 'test2'];
        $filename = File::getTempFilename($this->tempDirectory);
        File::putLines($filename, $lines);
        $this->assertEquals($lines, File::getLines($filename));
    }

    /**
     * Test append line.
     */
    public function testAppendLine(): void
    {
        $line1 = 'test1';
        $filename = File::getTempFilename($this->tempDirectory);
        File::appendLine($filename, $line1);
        $this->assertEquals([$line1], File::getLines($filename));

        $line2 = 'test2';
        File::appendLine($filename, $line2);
        $this->assertEquals([$line1, $line2], File::getLines($filename));
    }

    /**
     * Test append lines.
     */
    public function testAppendLines(): void
    {
        $lines1 = ['test1', 'test2'];
        $lines2 = ['test3', 'test4'];

        // Test when file does not exists.
        $filename = File::getTempFilename($this->tempDirectory);
        File::appendLines($filename, $lines2);
        $this->assertEquals($lines2, File::getLines($filename));

        // Test when file exists.
        $filename = File::getTempFilename($this->tempDirectory);
        File::putLines($filename, $lines1);
        File::appendLines($filename, $lines2);
        $this->assertEquals(array_merge($lines1, $lines2), File::getLines($filename));

        // Test when file does not exists.
        $filename = File::getTempFilename($this->tempDirectory);
        if (File::exist($filename)) {
            File::delete($filename);
        }
        File::appendLines($filename, $lines2);
        $this->assertEquals($lines2, File::getLines($filename));
    }

    /**
     * Test prepend line.
     */
    public function testPrependLine(): void
    {
        $line1 = 'test1';
        $filename = File::getTempFilename($this->tempDirectory);
        File::prependLine($filename, $line1);
        $this->assertEquals([$line1], File::getLines($filename));

        $line2 = 'test2';
        File::prependLine($filename, $line2);
        $this->assertEquals([$line2, $line1], File::getLines($filename));
    }

    /**
     * Test prepend lines.
     */
    public function testPrependLines(): void
    {
        $lines1 = ['test1', 'test2'];
        $lines2 = ['test3', 'test4'];

        // Test when file does not exists.
        $filename = File::getTempFilename($this->tempDirectory);
        File::prependLines($filename, $lines2);
        $this->assertEquals($lines2, File::getLines($filename));

        // Test when file exists.
        $filename = File::getTempFilename($this->tempDirectory);
        File::putLines($filename, $lines1);
        File::prependLines($filename, $lines2);
        $this->assertEquals(array_merge($lines2, $lines1), File::getLines($filename));

        // Test when file does not exists in default directory.
        $filename = File::getTempFilename($this->tempDirectory);
        if (File::exist($filename)) {
            File::delete($filename);
        }
        File::prependLines($filename, $lines2);
        $this->assertEquals($lines2, File::getLines($filename));
    }

    /**
     * Test get json.
     */
    public function testGetJson(): void
    {
        $lines = ['firstname' => 'test1', 'lastname' => 'test2'];
        $filename = File::getTempFilename($this->tempDirectory, '', 'json');
        File::putJson($filename, $lines);
        $this->assertEquals($lines, File::getJson($filename));
        File::delete($filename);
        $this->assertEquals([], File::getJson($filename));
    }

    /**
     * Test get json invalid json.
     */
    public function testGetJsonInvalidJson(): void
    {
        $filename = File::getTempFilename($this->tempDirectory, '', 'json');
        File::put($filename, '.invalid.json');
        $this->assertEquals([], File::getJson($filename));
    }

    /**
     * Test put json.
     */
    public function testPutJson(): void
    {
        $this->testGetJson();
    }

    /**
     * Test exist.
     */
    public function testExist(): void
    {
        $filename = File::getTempFilename($this->tempDirectory);
        touch($filename);
        $this->assertTrue(File::exist($filename));
        File::delete($filename);
        $this->assertFalse(File::exist($filename));
    }

    /**
     * Test touch.
     */
    public function testTouch(): void
    {
        $filename = $this->tempDirectory . '/test';
        $this->assertFalse(File::exist($filename));
        File::touch($filename);
        $this->assertTrue(File::exist($filename));
    }

    /**
     * Test copy.
     */
    public function testCopy(): void
    {
        $filename = File::getTempFilename($this->tempDirectory, '', 'test');
        $path = $this->tempDirectory . '/' . md5((string)microtime(true));

        $this->assertTrue(File::exist($filename));

        // Copy file to not-existent path.
        $this->assertFalse(File::copy($filename, $path));
        $this->assertFalse(File::exist($path . '/' . basename($filename)));

        // Copy file.
        Directory::make($path);
        $this->assertTrue(File::copy($filename, $path));
        $this->assertTrue(File::exist($path . '/' . basename($filename)));
    }

    /**
     * Test move.
     */
    public function testMove(): void
    {
        $filename = File::getTempFilename($this->tempDirectory, '', 'test');
        $path = $this->tempDirectory . '/' . md5((string)microtime(true));

        $this->assertTrue(File::exist($filename));

        // Copy file to not-existent path.
        $this->assertFalse(File::move($filename, $path));
        $this->assertFalse(File::exist($path . '/' . basename($filename)));

        // Copy file.
        Directory::make($path);
        $this->assertTrue(File::move($filename, $path));
        $this->assertFalse(File::exist($filename));
        $this->assertTrue(File::exist($path . '/' . basename($filename)));
    }

    /**
     * Test delete.
     */
    public function testDelete(): void
    {
        $filename = File::getTempFilename($this->tempDirectory);
        touch($filename);
        $this->assertTrue(file_exists($filename));
        File::delete($filename);
        $this->assertFalse(file_exists($filename));
    }

    /**
     * Test name.
     */
    public function testName(): void
    {
        $path = '/tmp/this-is-a-test.txt';
        $this->assertEquals('this-is-a-test', File::name($path));
    }

    /**
     * Test basename.
     */
    public function testBasename(): void
    {
        $path = '/tmp/this-is-a-test.txt';
        $this->assertEquals('this-is-a-test.txt', File::basename($path));
    }

    /**
     * Test dirname.
     */
    public function testDirname(): void
    {
        $path = '/tmp/this-is-a-test.txt';
        $this->assertEquals('/tmp', File::dirname($path));
    }

    /**
     * Test extension.
     */
    public function testExtension(): void
    {
        $path = '/tmp/this-is-a-test.txt';
        $this->assertEquals('txt', File::extension($path));
    }

    /**
     * Test type.
     */
    public function testType(): void
    {
        $path = $this->tempDirectory . '/this-is-a-test.txt';

        // Check non-existent file.
        $this->assertEquals('', File::type($path));

        // Check file.
        touch($path);
        $this->assertEquals('file', File::type($path));
    }

    /**
     * Test mimetype.
     */
    public function testMimeType(): void
    {
        $path = $this->tempDirectory . '/this-is-a-test.txt';

        // Check non-existent file.
        $this->assertEquals('', File::mimetype($path));

        // Check file.
        touch($path);
        $this->assertEquals('inode/x-empty', File::mimetype($path));
    }

    /**
     * Test size.
     */
    public function testSize(): void
    {
        $filename1 = File::getTempFilename($this->tempDirectory);
        $filename2 = File::getTempFilename($this->tempDirectory);

        // Create files.
        touch($filename1);
        File::put($filename2, 'test');

        // Check file-sizes.
        $this->assertEquals(0, File::size($filename1));
        $this->assertEquals(4, File::size($filename2));
    }

    /**
     * Test last modified.
     */
    public function testLastModified(): void
    {
        $filename = File::getTempFilename($this->tempDirectory);
        $modifiedDatetime1 = File::lastModified($filename);
        touch($filename, mktime(0, 0, 0, 4, 1, 2000));
        $modifiedDatetime2 = File::lastModified($filename);
        $this->assertNotEquals($modifiedDatetime1, $modifiedDatetime2);
        $this->assertEquals('2000-04-01 00:00:00', date('Y-m-d H:i:s', $modifiedDatetime2));
    }

    /**
     * Test is file.
     */
    public function testIsFile(): void
    {
        $path = $this->tempDirectory . '/this-is-a-test.txt';

        // Check non-existent file.
        $this->assertEquals('', File::type($path));

        // Check file.
        touch($path);
        $this->assertEquals('file', File::type($path));

        $isFile = File::isFile($path);
        $this->assertTrue($isFile);
    }

    /**
     * Test get temp filename.
     */
    public function testGetTempFilename(): void
    {
        $filename1 = File::getTempFilename($this->tempDirectory);
        $filename2 = File::getTempFilename($this->tempDirectory);
        $this->assertNotEquals($filename1, $filename2);
        $this->assertTrue(File::exist($filename1));
        $this->assertTrue(File::exist($filename2));
    }

    /**
     * Test get temp filename prefix.
     */
    public function testGetTempFilenamePrefix(): void
    {
        $check = '-CHECK-';
        $filename = File::getTempFilename($this->tempDirectory, $check);
        $this->assertStringContainsString($check, $filename);
        $this->assertFalse(substr($filename, -strlen($check)) === $check);
    }

    /**
     * Test get temp filename suffix.
     */
    public function testGetTempFilenameSuffix(): void
    {
        $check = '-CHECK-';
        $filename = File::getTempFilename($this->tempDirectory, '', $check);
        $this->assertStringContainsString($check, $filename);
        $this->assertTrue(substr($filename, -strlen($check)) === $check);
    }

    /**
     * Test get temp filename default temp directory.
     */
    public function testGetTempFilenameDefaultTempDirectory(): void
    {
        $filename = File::getTempFilename();
        $this->assertTrue($filename !== '');
        if (File::exist($filename)) {
            File::delete($filename);
        }
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->tempDirectory = sys_get_temp_dir();
        $this->tempDirectory .= '/' . str_replace('.', '', microtime(true));
        Directory::make($this->tempDirectory);
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
