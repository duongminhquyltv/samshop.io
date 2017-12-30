<?php 
Class MY_Function {
	/**
	 * Cắt một chuổi và trả về một chuổi mới với số từ nhất định
	 * @param: $chars: số từ, $str: chuổi.
	 * cut_string_number_word($str, 20, false);
	 */
	function cut_string_number_word($str, $chars, $to_space, $replacement="...")
	{
		if($chars > strlen($str)) return $str;

	    $str = substr($str, 0, $chars);
	    $space_pos = strrpos($str, " ");
	    if($to_space && $space_pos >= 0) 
	       $str = substr($str, 0, strrpos($str, " "));

	    return($str . $replacement);
	}
}