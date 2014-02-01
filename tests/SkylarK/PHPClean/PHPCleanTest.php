<?php

class PHPCleanTest extends PHPUnit_Framework_TestCase
{
	public function setUp() {
	}

	public static function tearDownAfterClass() {
	}

	// -----------------------------------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------------------------------

	public function test_Tokeniser() {
		$cleaner = new \SkylarK\PHPClean\PHPClean();
		$cleaner->cleanSource('<?php echo \'hello\'; ?>');
	}
}