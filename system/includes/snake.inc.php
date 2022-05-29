<?php

/**
 * Yields "snaked" columns.
 *
 * This routine yields table rows.
 *
 * @param integer $numcols How many columns do you want?
 * @param array $arr Indexed array of user values.
 *
 * @return string The values in a snaked column table.
 */

function snake($numcols, $arr)
{
	$maxcols = $numcols;
	$numlines = count($arr);
	$remainder = $numlines % $maxcols;
	if ($remainder != 0) {
		$maxrows = ($numlines - $remainder) / $maxcols;
		$maxrows++;
	}
	else
		$maxrows = $numlines / $maxcols;

    $ncells = $maxcols * $maxrows;

	$master = array();
	for ($i = 0; $i < $ncells; $i++) {
		$row = $i % $maxrows;
		$col = floor($i / $maxrows);

        if (isset($arr[$i])) {
    		$master[$row][$col] = $arr[$i];
        }
        else {
    		$master[$row][$col] = '';
        }
	}

	$str = '';
	for ($x = 0; $x < $maxrows; $x++) {

		$z = $x & 1;
		$str .= "<tr class=\"row$z\">\n";

		for ($y = 0; $y < $maxcols; $y++) {
			$str .= "<td>" . $master[$x][$y] . "</td>\n";
		}

		$str .= "</tr>\n";
	}

	return $str;
}

/**
 * Yields "snaked" columns.
 *
 * This routine is designed for printouts.
 *
 * @param integer $numcols How many columns do you want?
 * @param array $arr Indexed array of user values.
 *
 * @return string The values in a snaked column table.
 */

function snakev2($numcols, $arr)
{
	// calculate rows and columns
	$maxcols = $numcols;
	$numitems = count($arr);
	$remainder = $numitems % $maxcols;
	if ($remainder != 0) {
		$maxrows = ($numitems - $remainder) / $maxcols;
		$maxrows++;
	}
	else
		$maxrows = $numitems / $maxcols;

	// calculate the width of each "cell"
	$total_width = 75;
	$item_width = floor(($total_width - 5)/ $maxcols) + 1;

	$master = array();
	for ($i = 0; $i < $numitems; $i++) {

		$row = $i % $maxrows;
		$col = floor($i / $maxrows);

		$master[$row][$col] = $arr[$i];
	}

	$str = '';
	for ($x = 0; $x < $maxrows; $x++) {

		// row start, if any
		$str .= '     ';

		for ($y = 0; $y < $maxcols; $y++) {
			if (isset($master[$x][$y])) {
				$item_text = substr($master[$x][$y], 0, $item_width); 
				$str .= sprintf("%-{$item_width}s ", $item_text);
			}
		}

		// row end, if any
		$str .= "\n";
	}

	return $str;
}
