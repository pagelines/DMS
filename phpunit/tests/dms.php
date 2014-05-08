<?php
class DMSTest extends \PHPUnit_Framework_TestCase {	
    public function testThemeName() {
        $this->expectOutputString('PageLines');
        print PL_THEMENAME;
    }
}