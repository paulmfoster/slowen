<?php

/**
 * Class to handle vimoutline files.
 *
 * This class is not exhaustive. It handles the different vimoutline syntax
 * cases in a reasonable way.
 *
 * It does not handle tables.
 *
 * Syntax types in vimoutline:
 * : body text (wraps)
 * ; preformatted (no wrap)
 * | table
 * || table header
 * > like :
 * < like ;
 */

class vimoutline
{
	var $str, $lines;

	function __construct($filename)
	{
		$this->lines = file($filename, FILE_IGNORE_NEW_LINES);
		$this->stylesheet();
	}

	function stylesheet()
	{
$this->str = <<<EOS
<style>

.highlighted {
	background-color: #dddddd;
}

.tab0 {
	margin-left: 0em;
}

.tab1 {
	margin-left: 5em;
}

.tab2 {
	margin-left: 10em;
}

.tab3 {
	margin-left: 15em;
}

.tab4 {
	margin-left: 20em;
}

.tab5 {
	margin-left: 25em;
}

.tab6 {
	margin-left: 30em;
}

.tab7 {
	margin-left: 35em;
}

.tabr8 {
	margin-left: 40em;
}

.tab9 {
	margin-left: 45em;
}

</style>

EOS;
	}

	function parse()
	{
		$this->str .= '<div class="tab0">' . PHP_EOL;

		$indent = 0;
		$nothing = 0;
		$colon = 1;
		$semi = 2;
		$lt = 4;
		$gt = 8;
		$table = 16;
		$state = 0;

		foreach ($this->lines as $line) {

			$tabs = substr_count($line, "\t");

			$right = substr($line, $tabs);
			if (strlen($right) != 0) {
				$startchar = $right[0];
			}
			else {
				$startchar = '';
			}

			switch ($startchar) {
			case ':':
				$next_state = $colon;
				$add = substr($right, 1) . PHP_EOL;
				break;
			case ';':
				$next_state = $semi;
				$add = substr($right, 1) . '<br/>' . PHP_EOL;
				break;
			case '<':
				$next_state = $lt;
				$add = substr($right, 1) . '<br/>' . PHP_EOL;
				break;
			case '>':
				$next_state = $gt;
				$add = substr($right, 1) . PHP_EOL;
				break;
			case '|':
				$next_state = $table;
				$add = substr($right, 1) . '<br/>' . PHP_EOL;
				break;
			default:
				$next_state = $nothing;
				$add = $right . '<br/>' . PHP_EOL;
			}

			if ($state != $next_state) {
				if ($state == $colon || $state == $semi || $state == $gt || $state == $lt) {
					$this->str .= '</span>' . PHP_EOL;
				}

				if ($tabs != $indent) {
					$this->str .= '</div>' . PHP_EOL;
					$indent = $tabs;
					$this->str .= '<div class="tab' . $tabs . '">' . PHP_EOL;
				}

				if ($next_state == $colon || $next_state == $semi || $next_state == $gt || $next_state == $lt) {
					$this->str .= '<span class="highlighted">' . PHP_EOL;
				}

				$state = $next_state;
				$this->str .= $add;

			}
			else {
				if ($tabs != $indent) {
					$this->str .= '</div>' . PHP_EOL;
					$indent = $tabs;
					$this->str .= '<div class="tab' . $tabs . '">' . PHP_EOL;
				}
				$this->str .= $add;
			}

		}

		$this->str .= '</div> <!-- whatever tab level -->' . PHP_EOL;
		// $this->str .= '<div class="tab' . $indent . '">' . PHP_EOL;

		return $this->str;
	}

}


