<?php

	// vfsStream - Esta herramienta es un simulador del sistema de ficheros, el cual ejecuta en memoria todo lo relativo al disco.

	use org\bovigo\vfs\vfsStream;

	class FileSystemCacheWithVfsStreamTest extends \PHPUnit_Framework_TestCase{

	    private $root;
	    public function setUp() {
	        $this->root = vfsStream::setup();
	    }
	    /**
	     * @test
	     */
	    public function createsDirectoryIfNotExists() {
	        $cache = new FileSystemCache($this->root->url() . '/cache');
	        $cache->store('example', ['bar' => 303]);
	        $this->assertTrue($this->root->hasChild('cache'));
	    }
	    /**
	     * @test
	     */
	    public function storesDataInFile() {
	        $cache = new FileSystemCache($this->root->url() . '/cache');
	        $cache->store('example', ['bar' => 303]);
	        $this->assertTrue($this->root->hasChild('cache/example'));
	        $this->assertEquals(
	                ['bar' => 303],
	                unserialize($this->root->getChild('cache/example')->getContent())
	        );
	    }
	}


	class FileSystemCacheWithoutVfsStreamTest extends \PHPUnit_Framework_TestCase{

    /**
     * ensure that the directory and file are not present from previous run
     * Necesaria la funcion clean() en setUp y tearDown
     */
    private function clean() {
        if (file_exists(__DIR__ . '/cache/example')) {
            unlink(__DIR__ . '/cache/example');
        }
        if (file_exists(__DIR__ . '/cache')) {
            rmdir(__DIR__ . '/cache');
        }
    }

    public function setUp() {
        $this->clean();
    }

    public function tearDown() {
        $this->clean();
    }

    /**
     * @test
     */
    public function createsDirectoryIfNotExists() {
        $cache = new FileSystemCache(__DIR__ . '/cache');
        $cache->store('example', ['bar' => 303]);
        $this->assertFileExists(__DIR__ . '/cache');
    }

    /**
     * @test
     */
    public function storesDataInFile() {
        $cache = new FileSystemCache(__DIR__ . '/cache');
        $cache->store('example', ['bar' => 303]);
        $this->assertEquals(
                ['bar' => 303],
                unserialize(file_get_contents(__DIR__ . '/cache/example'))
        );
    }
	}


?>