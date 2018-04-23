<?php

	// Necesita PHP Reflexion

	namespace Test;

	use App\MasTest\Constructor;
	//use Src\ClassPrivateConstruct;

	/**
	* Testing for ClassPrivateConstruct Class
	*
	*/
	class ConstructorTest extends \PHPUnit\Framework\TestCase{

	    /**
	     * Test for private constructor.a
	     * 
	     * @return void
	    */
	    public function testConstruct(){

	        //$reflectionClass = new \ReflectionClass(ClassPrivateConstruct::class);
	        $reflectionClass = new \ReflectionClass(Constructor::class);
	        $meth = $reflectionClass->getMethod('__construct');
	        $this->assertTrue($meth->isPrivate());

	        $class = Constructor::create("var1", "var2");
	        $propertyVarOne = $reflectionClass->getProperty('varOne');
	        $propertyVarTwo = $reflectionClass->getProperty('varTwo');

	        $propertyVarOne->setAccessible(true);
	        $propertyVarTwo->setAccessible(true);

	        $this->assertEquals("var1", $propertyVarOne->getValue($class));
	        $this->assertEquals("var2", $propertyVarTwo->getValue($class));

	    }
	}

?>