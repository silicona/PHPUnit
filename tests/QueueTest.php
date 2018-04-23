<?php

// Ejemplo de setUp() y tearDown()

class QueueTest extends \PHPUnit\Framework\TestCase {

    public function setUp(){
        $this->queue = Array();
    }

    public function tearDown(){
        unset($this->queue);
    }

    public function testEmptyQuery() {
        $this->assertTrue(empty($this->queue));
    }

    public function testPush() {
        array_push($this->queue, 'element1');
        $this->assertEquals('element1', $this->queue[count($this->queue)-1]);
        $this->assertFalse(empty($this->queue));
    }
}

?>