<?php
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2014, EllisLab, Inc.
 * @license		http://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 3.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * ExpressionEngine Spam Module
 *
 * @package		ExpressionEngine
 * @subpackage	Modules
 * @category	Modules
 * @author		EllisLab Dev Team
 * @link		http://ellislab.com
 */

class Tokenizer {

	/**
	 * __construct
	 * 
	 * @param int $ngrams  Size of the n-grams to calculate
	 * @param string $pattern  Regex pattern used to split string, defaults to 
	 * 						   splitting by character
	 * @access public
	 * @return void
	 */
	public function __construct($ngram = 1, $pattern = NULL)
	{
		$this->ngram = $ngram;
		$this->pattern = $pattern;
	}

	public function tokenize($string)
	{
		if ( ! empty($this->pattern))
		{
			$tokens = preg_split("/$pattern/i", $string);
		}
		else
		{
			$tokens = str_split($string);
		}

		return $this->_ngrams($tokens, $this->ngram);
	}

	/**
	 * Calculates the n-grams for a string
	 * 
	 * @param array $tokens 
	 * @param int $n 
	 * @access private
	 * @return array  The array of n-grams
	 */
	private function _ngrams($tokens, $n = 1)
	{
		$length = count($tokens);
		$ngrams = array();
		 
		for ($i = 0; $i + $n <= $length; $i++)
		{
			$ngrams[$i] = implode('', array_slice($i, $n));
		}

		return $ngrams;
	}


}

/* End of file Tokenizer.php */
/* Location: ./system/expressionengine/modules/spam/libraries/Tokenizer.php */
