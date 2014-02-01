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

	/** A list of tokens that tab in a stack */
	private $_tab_tokens;

	/** A list of tokens that require addition to the stack */
	private $_stack_tokens;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_source = "";
		$this->_result = "";
		$this->_nl_tokens = array(
			T_ECHO, T_FUNCTION, T_CLASS
		);
		$this->_tab_tokens = array(
			T_ECHO, T_FUNCTION, T_CLASS
		);
		$this->_stack_tokens = array(
			
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
	 * Process a result.
	 */
	protected function addResult($tokenid, $token, $stack_count) {
		// Should we be tabbing in?
		if (in_array($tokenid, $this->_tab_tokens) || $token === '}') {
			for ($i = 0; $i < $stack_count; $i++) {
				$token = "\t" . $token;
			}
		}

		$this->_result .= $token;
	}

	/**
	 * Protected clean method.
	 */
	protected function clean() {
		$this->_result = "";

		// Current stack.
		$stack = array();

		// Our tokens.
		$tokens = token_get_all($this->_source);

		$prev = null;
		foreach ($tokens as $token) {
			// If this is just a character, skip out.
			if (!is_array($token)) {

				// Is this closing off a stack item?
				if ($token === '{') {
					$stack[] = $token;
				}

				// Is this closing off a stack item?
				if ($token === '}') {
					$item = array_pop($stack);
				}

				$this->addResult(null, $token, count($stack));
				$prev = $token;
				continue;
			}

			$tokenid = $token[0];

			// The next thing to add to result.
			$out = $token[1];
			// Trim if we are not whitespace.
			if ($tokenid !== T_WHITESPACE) {
				$out = trim($out);
			}

			// New line check.
			if (in_array($tokenid, $this->_nl_tokens)) {
				if ($prev[0] !== T_WHITESPACE) {
					$this->_result .= "\n";
				}
			}

			// Stack check.
			if (in_array($tokenid, $this->_stack_tokens)) {
				$stack[] = $token;
			}


			// Work out what to do.
			switch ($tokenid) {
			}

			// Cleanup
			$this->addResult($tokenid, $out, count($stack));
			$prev = $token;
		}

		if (!is_array($prev) || $prev[1] !== "\n") {
			$this->_result .= "\n";
		}
	}
}