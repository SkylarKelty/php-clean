<?php

class PHPCleanTest extends PHPUnit_Framework_TestCase
{
	private $_cleaner;

	public function setUp() {
		$this->_cleaner = new \SkylarK\PHPClean\PHPClean();
	}

	public static function tearDownAfterClass() {
	}

	// -----------------------------------------------------------------------------------------
	// Tests
	// -----------------------------------------------------------------------------------------

	public function test_Basic() {
		$php = "<?php echo 'hello';\n";
		$expected = "<?php\necho 'hello';\n";

		$this->_cleaner->cleanSource($php);
		$result = $this->_cleaner->getResult();

		$this->assertEquals($expected, $result);
	}

	public function test_LastNewLine() {
		$php = "<?php\necho 'hello';";
		$expected = "<?php\necho 'hello';\n";

		$this->_cleaner->cleanSource($php);
		$result = $this->_cleaner->getResult();

		$this->assertEquals($expected, $result);
	}

	public function test_tabs() {
		$php = "<?php\nfunction test() {\necho 'hello';\n}\n";
		$expected = "<?php\nfunction test() {\n\techo 'hello';\n}\n";

		$this->_cleaner->cleanSource($php);
		$result = $this->_cleaner->getResult();

		$this->assertEquals($expected, $result);
	}

	public function test_multi_tabs() {
		$php = "<?php\nclass test {\nfunction test() {\necho 'hello';\n}\n}\n";
		$expected = "<?php\nclass test {\n\tfunction test() {\n\t\techo 'hello';\n\t}\n}\n";

		$this->_cleaner->cleanSource($php);
		$result = $this->_cleaner->getResult();

		$this->assertEquals($expected, $result);
	}

	public function test_assignment() {
		$php = "<?php\nclass test {\nfunction test() {\n\$a = 'test';\n}\n}\n";
		$expected = "<?php\nclass test {\n\tfunction test() {\n\t\t\$a = 'test';\n\t}\n}\n";

		$this->_cleaner->cleanSource($php);
		$result = $this->_cleaner->getResult();

		$this->assertEquals($expected, $result);
	}

	public function test_inline_function() {
		$php = "<?php\nclass test {\nfunction test() {\n\$a = function() { return 'hey'; };\n}\n}\n";
		$expected = "<?php\nclass test {\n\tfunction test() {\n\t\t\$a = function() {\n\t\t\treturn 'hey';\n\t\t};\n\t}\n}\n";

		$this->_cleaner->cleanSource($php);
		$result = $this->_cleaner->getResult();

		$this->assertEquals($expected, $result);
	}
}