<?php

/**
 * table2html()
 *
 * This function outputs a string which shows a table of the field
 * contents of records from a database. It takes an indexed array of
 * records, each of which is an associative array of field names =>
 * contents. Records are in whatever order your SQL statement fetched
 * them and are displayed in the fetched order.
 *
 * @param array $records indexed/assoc array of records
 *
 * @return string All the HTML for a table display.
 *
 */

function table2html($records)
{
	$max_records = count($records);

	$str = '<style>' . PHP_EOL;
	$str .= '.sansserif {font-family: verdana, helvetica, arial, sans-serif;}' . PHP_EOL;
	$str .= '.row0 {background-color: #FFFFFF;}' . PHP_EOL;
	$str .= '.row1 {background-color: #CCCCCC;}' . PHP_EOL;
	$str .= '</style>' . PHP_EOL;

	$str .= '<div class="sansserif">' . PHP_EOL;
	$str .= '<table rules="all" border="1">' . PHP_EOL;
    $row = 0;
	for ($i = 0; $i < $max_records; $i++) {

		// headers: field names
		if ($i == 0) {
			$keys = array_keys($records[$i]);
			$max_fields = count($keys);
			$str .= '<tr class="row' . ($row++ & 1) . '">';
			for ($j = 0; $j < $max_fields; $j++) {
				$str .= '<th>';
				$str .= $keys[$j];
				$str .= '</th>';
			}
			$str .= '</tr>' . PHP_EOL;
		}

		// records
		$values = array_values($records[$i]);
		$str .= '<tr class="row' . ($row++ & 1) . '">';
		for ($j = 0; $j < $max_fields; $j++) {
			$str .= '<td>';
			$str .= $values[$j];
			$str .= '</td>';
		}
		$str .= '</tr>' . PHP_EOL;
	}
	$str .= '</table>' . PHP_EOL;
	$str .= '</div>' . PHP_EOL;

	return $str;
}

