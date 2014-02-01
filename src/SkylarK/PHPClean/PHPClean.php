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

	/** A list of tokens that require a new line before/after */
	private $_nl_tokens;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_source = "";
		$this->_result = "";
		$this->_nl_tokens = array(
			T_ECHO, 
		);
	}

	/**
	 * The main method, call this to cleanup a string.
	 */
	public function cleanSource($source) {
		$this->_source = $source;
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
		$this->_result = "";

		$tokens = token_get_all($this->_source);

		$prev = null;
		foreach ($tokens as $token) {
			// If this is just a character, skip out.
			if (!is_array($token)) {
				$this->_result .= $token;
				$prev = $token;
				continue;
			}

			$tokenid = $token[0];

			// The next thing to add to result.
			$out = $token[1];

			// New line check.
			if (in_array($tokenid, $this->_nl_tokens)) {
				if ($prev[0] !== T_WHITESPACE) {
					$this->_result .= "\n";
				}
			}

			// Work out what to do.
			switch ($tokenid) {
				case T_WHITESPACE:
					break;
				default:
					$out = trim($out);
					break;
			}


			// Cleanup
			$this->_result .= $out;

			$prev = $token;
		}

		if (!is_array($prev) || $prev[1] !== "\n") {
			$this->_result .= "\n";
		}
	}
}