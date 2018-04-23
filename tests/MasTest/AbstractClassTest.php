<?php

    abstract class AbstractClass
    {
        public function concreteMethod()
        {
            return $this->abstractMethod();
        }

        public abstract function abstractMethod();
    }

    class AbstractClassTest extends PHPUnit\Framework\TestCase
    {
        public function testConcreteMethod()
        {
            $stub = $this->getMockForAbstractClass('AbstractClass');

            $stub->expects($this->any())
                 ->method('abstractMethod')
                 ->will($this->returnValue(TRUE));

            $this->assertTrue($stub->concreteMethod());
        }
    }

?>