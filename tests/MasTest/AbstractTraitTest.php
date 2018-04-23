<?php


	trait AbstractTrait	{

	    public function concreteMethod()
	    {
	        return $this->abstractMethod();
	    }

	    public abstract function abstractMethod();
	}


	class TraitClassTest extends PHPUnit\Framework\TestCase	{
		
	    public function testConcreteMethod()
	    {
	        $mock = $this->getMockForTrait('AbstractTrait');

	        $mock->expects($this->any())
	             ->method('abstractMethod')
	             ->will($this->returnValue(TRUE));

	        $this->assertTrue($mock->concreteMethod());
	    }
	}


?>