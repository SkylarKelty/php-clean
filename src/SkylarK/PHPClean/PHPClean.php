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

	/** A list of assignment tokens */
	private $_assignment_tokens;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->_source = "";
		$this->_result = "";
		$this->_nl_tokens = array(
			T_ECHO, T_FUNCTION, T_CLASS, T_INTERFACE, T_CASE, T_CATCH, T_CONTINUE, T_DO, T_IF,
			T_INCLUDE, T_INCLUDE_ONCE, T_REQUIRE, T_REQUIRE_ONCE, T_SWITCH, T_THROW, T_TRAIT,
			T_PRINT, T_PRIVATE, T_PUBLIC, T_PROTECTED, T_RETURN, T_TRY, T_UNSET, T_VARIABLE, T_YIELD
		);
		$this->_tab_tokens = $this->_nl_tokens;
		$this->_assignment_tokens = array(
			T_AND_EQUAL, T_CONCAT_EQUAL, T_DIV_EQUAL, T_MINUS_EQUAL, T_MOD_EQUAL, T_MUL_EQUAL,
			T_OR_EQUAL, T_PLUS_EQUAL, T_SL_EQUAL, T_SR_EQUAL, T_XOR_EQUAL
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
	protected function addResult($tokenid, $token, $prev, $stack_count, $in_assignment) {

		// New line check.
		if (!$in_assignment && (in_array($tokenid, $this->_nl_tokens) || $token === '}')) {
			if ($prev[1] !== "\n") {
				$this->_result = rtrim($this->_result);
				$this->_result .= "\n";
			}
		}

		// Should we be tabbing in?
		if (!$in_assignment  && (in_array($tokenid, $this->_tab_tokens) || $token === '}')) {
			for ($i = 0; $i < $stack_count; $i++) {
				$this->_result .= "\t";
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

		// Are we in an assignment?
		$in_assignment = false;

		// Our tokens.
		$tokens = token_get_all($this->_source);

		$prev = null;
		foreach ($tokens as $token) {
			// If this is just a character, skip out.
			if (!is_array($token)) {
				$token = trim($token);

				// Is this an assignment?
				if ($token === '=') {
					$in_assignment = true;
				}

				// Is this closing off a stack item?
				if ($token === '{') {
					$stack[] = $token;
					$in_assignment = false;
				}

				// Is this closing off a stack item?
				if ($token === '}') {
					$item = array_pop($stack);
				}

				// Is this closing an assignment (possibly)?
				if ($token === ';') {
					$in_assignment = false;
				}

				$this->addResult(null, $token, $prev, count($stack), $in_assignment);
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

			// Assignment check.
			if (in_array($tokenid, $this->_assignment_tokens)) {
				$in_assignment = true;
			}

			// Work out what to do.
			switch ($tokenid) {
			}

			// Cleanup
			$this->addResult($tokenid, $out, $prev, count($stack), $in_assignment);
			$prev = $token;
		}

		if (!is_array($prev) || $prev[1] !== "\n") {
			$this->_result .= "\n";
		}
	}
}