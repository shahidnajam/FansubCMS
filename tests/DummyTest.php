<?php

class DummyTest extends PHPUnit_Framework_TestCase {
    // just test if tests are configured correctly
    public function testTests() {
        $this->assertEquals(true, true);
        $this->fail('Oops, it seems there is something wrong with your PHPUnit setup!');
    }
}