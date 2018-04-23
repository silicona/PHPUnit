<?php

    use App\XString\XString;
    //use Clases;
    // use PHPUnit\Framework\TestCase;

    
    class XStringTest extends PHPUnit\Framework\TestCase{

        // Factorizacion con Setup y teardown
        protected $xstring;

        public function setup(){

            $this -> xstring = new XString('hello world');
        }

        public function teardown(){

            unset( $this -> xstring );
        }

        /**
         * Test for startWith method. 
         *
         * @return void
        */
        public function testStartWith()
        {
            // $xstring = new XString('hello world');
            // $this->assertTrue($xstring->startWith('hello'));
            // $this->assertFalse($xstring->startWith('world'));

            $this->assertTrue($this -> xstring -> startWith('hello'));
            $this->assertFalse($this -> xstring -> startWith('world'));

        }

        /** 
         * Test for endWith method.
         *
         * @return void
        */
        public function testEndWith()
        {
            //$xstring = new XString('hello world');
            // $this->assertTrue($xstring->endWith('world'));
            // $this->assertFalse($xstring->endWith('hello'));

            $this->assertTrue( $this -> xstring->endWith('world'));
            $this->assertFalse( $this -> xstring->endWith('hello'));
        }
    }
    
?>