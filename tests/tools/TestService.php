<?php

namespace ObsidianTestTools;

class TestService {
	protected $test = 'foobar';

    public function getTest() {
        return $this->test;
    }

    public function setTest( $value ) {
    	$this->test = $value;
    }
}
