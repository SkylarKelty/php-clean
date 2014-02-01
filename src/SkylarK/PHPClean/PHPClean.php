<?php
/**
 * A PHP cleaner.
 *
 * @author Skylar Kelty <skylarkelty@gmail.com>
 */

namespace SkylarK\PHPClean;

/**
 * The core of the PHP Cleaner.
 */
class PHPClean
{
	/** The source we are working on */
	private $_source;

	/** The result of the operation */
	private $_result;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_source = "";
		$this->_result = "";
	}

	/**
	 * The main method, call this to cleanup a string.
	 */
	public function cleanSource($source) {
		$this->_source = $source;
		$this->_result = "";
		$this->clean();
	}

	/**
	 * The main method, call this to cleanup a file.
	 */
	public function cleanFile($filename) {
		$source = file_get_contents($filename);
		$this->cleanSource($source);
	}
	
	/**
	 * Get the result.
	 */
	public function getResult() {
		return $this->_result;
	}
	
	/**
	 * Save the result to a file.
	 */
	public function saveResult($filename) {
		$result = $this->getResult();
		return file_put_contents($filename, $result);
	}

	/**
	 * Protected clean method.
	 */
	protected function clean() {
		$tokens = token_get_all($this->_source);
		print_r($tokens);
	}
}