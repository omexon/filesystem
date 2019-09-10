<?php

declare(strict_types=1);

namespace Tests\Omexon\Filesystem;

use Omexon\Filesystem\Path;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    /** @var string */
    private $rootDirectory;

    /** @var string */
    private $currentVendor;

    /** @var string */
    private $currentPackage;

    /** @var string */
    private $vendorBaseDirectory;

    /**
     * Test get root.
     */
    public function testRoot(): void
    {
        $this->assertEquals($this->rootDirectory, Path::root());
        $this->assertEquals($this->rootDirectory . '/test1/test2', Path::root(['test1', 'test2']));
        $this->assertEquals($this->rootDirectory . '/test1/test2', Path::root('test1/test2'));
    }

    /**
     * Test package current.
     */
    public function testPackageCurrent(): void
    {
        $expected = $this->rootDirectory . '/' . $this->vendorBaseDirectory;
        $expected .= '/' . $this->currentVendor . '/' . $this->currentPackage;
        $this->assertEquals(
            $expected,
            Path::packageCurrent()
        );
    }

    /**
     * Test package.
     */
    public function testPackage(): void
    {
        $this->assertEquals(
            $this->rootDirectory . '/' . $this->vendorBaseDirectory . '/test1/test2',
            Path::package('test1', 'test2')
        );
    }

    /**
     * Test package segments as array.
     */
    public function testPackageSegmentsAsArray(): void
    {
        $this->assertEquals(
            $this->rootDirectory . '/' . $this->vendorBaseDirectory . '/test1/test2/a/b/c/d',
            Path::package('test1', 'test2', ['a', 'b', 'c', 'd'])
        );
    }

    /**
     * Test package segments as dot notation.
     */
    public function testPackageSegmentsAsString(): void
    {
        $this->assertEquals(
            $this->rootDirectory . '/' . $this->vendorBaseDirectory . '/test1/test2/a/b/c/d',
            Path::package('test1', 'test2', 'a/b/c/d')
        );
    }

    /**
     * Test package segments as single.
     */
    public function testPackageSegmentsAsSingle(): void
    {
        $this->assertEquals(
            $this->rootDirectory . '/' . $this->vendorBaseDirectory . '/test1/test2/a',
            Path::package('test1', 'test2', 'a')
        );
    }

    /**
     * Test vendor name.
     */
    public function testVendorName(): void
    {
        $this->assertEquals($this->currentVendor, Path::vendorName());
    }

    /**
     * Test package name.
     */
    public function testPackageName(): void
    {
        $this->assertEquals($this->currentPackage, Path::packageName());
    }

    /**
     * Setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Prepare root directory.
        $this->rootDirectory = __DIR__;
        for ($c1 = 0; $c1 < 3; $c1++) {
            $this->rootDirectory = dirname($this->rootDirectory);
        }
        $this->vendorBaseDirectory = basename($this->rootDirectory);
        $this->rootDirectory = dirname($this->rootDirectory);

        // Get package details.
        $packagePath = dirname(__DIR__);
        $this->currentPackage = basename($packagePath);
        $this->currentVendor = basename(dirname($packagePath));
    }
}
