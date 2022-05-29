<?php

class validate
{
	static function boolean($content)
	{
		return filter_var($content, FILTER_VALIDATE_BOOLEAN);
	}

	static function email($content)
	{
		return filter_var($content, FILTER_VALIDATE_EMAIL);
	}

	static function float($content)
	{
		return filter_var($content, FILTER_VALIDATE_FLOAT);
	}

	static function int($content)
	{
		return filter_var($content, FILTER_VALIDATE_INT);
	}

	static function number($content)
	{
		if ($this->float($content) || $this->int($content)) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	static function ip($content)
	{
		return filter_var($content, FILTER_VALIDATE_IP);
	}

	static function mac($content)
	{
		return filter_var($content, FILTER_VALIDATE_MAC);
	}

	static function url($content)
	{
		return filter_var($content, FILTER_VALIDATE_URL);
	}

	static function ccard($content)
	{
        $vregex = '/^(4\d{12})|(((4|3)\d{3})|(5[1-5]\d{2})|(6011))(\d{12})|(3[4,7]\d{13})|(30[0-5]\d{1}|(36|38)\d(2))(\d{10})|((2131|1800)|(2014|2149))(\d{11})$/';

        // squeeze out any non-numeric characters (spaces, dashes, etc.)
        $ccno = preg_replace('%[^0-9]%', '', $content);
        if (preg_match($vregex, $ccno) == 0) {
            $ret = false;
        }
        else {
            $sum = 0;
            $ccno = trim($ccno);
            $len = strlen($ccno);
            if ($len > 16)
                $ret = false;
            else {
                $straight = true;
                $sum = 0;
                /* For the last and every other number, accumulate it.
                 * For numbers in between, double them, accumulate the sum
                 * of their digits.
                 */
                for ($i = $len - 1; $i >= 0; $i--) {
                    $prelim = (int) substr($ccno, $i, 1);
                    if ($straight)
                        $straight = false;
                    else {
                        $prelim = 2 * $prelim;
                        if ($prelim > 9)
                            $prelim -= 9;
                        $straight = true;
                    }
                    $sum = $sum + $prelim;
                }
                $ret = ($sum % 10 == 0) ? true : false;
            }
        }
        return $ret;
	}

	function version()
	{
		return 1.1;
	}

}
