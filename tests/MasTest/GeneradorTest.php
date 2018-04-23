<?php

	namespace Test;

	//use Src\GeneratorClass;
	use App\MasTest\Generador;

	/**
	* Testing for GeneratorClass
	*/
	class GeneradorTest extends \PHPUnit\Framework\TestCase {

	    /**
	     * Test for check a Generator
	     *
	     * @return void
	    */ 
	    public function testGetLines() {

	        $generador = new Generador();
	        $this->assertInternalType("object", $generador);
	        $this->assertInstanceOf("App\MasTest\Generador", $generador);

	        $dictionary = Array();

	        $counts = $generador -> getCountOfWords("./tests/MasTest/generador_prueba.txt");
	        $this->assertInstanceOf("Iterator", $counts);
	        // $counts = $generator->getCountOfWords("./tests/fixture/file.txt");
	        //$primero = $counts -> current();
	        //$this->assertInstanceOf("Integer", $primero);
	        //$this->assertEquals(array(), $counts);

	        $this->assertEquals( 4, $counts -> current() );

	        $this->assertEquals("word3", $counts -> key() );

	        //$this->assertInstanceOf("Generador", $counts);
	    }
	}

?>