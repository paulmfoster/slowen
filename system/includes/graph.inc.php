<?php

/**
 * Graph a stat using PHP GD library
 *
 * @param string $statname Name of stat to show on graph
 * @param string $filename Basename of graph file
 * @param string $filetype Type of file (jpeg, jpg, png, gif)
 * @param array $values Stat values to graph
 *
 * @return string The filename of the graph
 */

function gd_graph($statname, $filename, $filetype, $values)
{
	$img_width = 925;
	$img_height = 1020;
	$mgn_lft = 75;
	$mgn_top = 75;
	$mgn_rgt = 75;
	$mgn_bot = 75;

	$img = imagecreate($img_width, $img_height);

	// Do internal calculations

	// Step 0: figure graph/image span

	$x_span = $img_width - $mgn_lft - $mgn_rgt;
	$y_span = $img_height - $mgn_top - $mgn_bot;

	// Step 1: Figure the actual high, low and span of the stats

	$stat_min = PHP_INT_MAX;
	$stat_max = PHP_INT_MIN;

	// can't use min() here as NR/NULL values screw it up
	$num_stats = count($values);
	for ($i = 0; $i < $num_stats; $i++) {
		// set maximum and minimums for graph
		if (!is_null($values[$i]['value'])) {
			if ($values[$i]['value'] > $stat_max) {
				$stat_max = $values[$i]['value'];
			}
			if ($stat_min > $values[$i]['value']) {
				$stat_min = $values[$i]['value'];
			}
		}
	}

	// get full span of values (max - min)
	$stat_span = $stat_max - $stat_min;

	$f = floor($stat_span / 10);
	$c = ceil($stat_span / 10);
	if ($f != $c) {
		$y_scale_span = $c * 10;
	}
	else {
		$y_scale_span = $stat_span;
	}

	// Step 2: Figure out the order of magnitude we need for the graph
	// That is, find a number for $power such that it is of the same
	// magnitude as the overall span of the stat.

	$lolimit = $stat_min;
	$hilimit = $stat_min;

	$y_tic_number = 10;

	// values shown for vertical ticks
	$y_tic_values = [];
	for ($i = 0; $i <= $y_tic_number; $i++) {
		$y_tic_values[] = $lolimit + ($i * ($y_scale_span / 10));
	}

	// Step 8: Figure y tic y coordinates

	$y_tic_y_coords = [];
	for ($i = 0; $i <= $y_tic_number; $i++) {
		$y_tic_y_coords[] = ($mgn_top + $y_span) - ($i * ($y_span / $y_tic_number));
	}

	$yratio = $y_span / $y_scale_span;

	// Step 9: Figure stat y coordinates

	$stat_y_coords = [];
	for ($i = 0; $i < $num_stats; $i++) {

		// stat points = $this->_values[$i][1] - $lolimit
		// reversed y value = stat points * yratio
		if (is_numeric($values[$i]['value'])) {
			$stat_points = $values[$i]['value'] - $lolimit;
			$stat_y_coords[] = ($mgn_top + $y_span) - ($stat_points * $yratio);
		}
		else {
			$stat_y_coords[] = NULL;
		}
	}

	// Step 10: Figure stat x coordinates

	$hdiv = ($x_span) / ($num_stats - 1);

	$stat_x_coords = [];
	for ($i = 0; $i < $num_stats; $i++) {
		if (!is_null($values[$i]['value'])) {
			$stat_x_coords[$i] = $i * $hdiv + $mgn_lft;
		}
	}

	// set colors

	// black
	$border_color = imagecolorallocate($img, 0, 0, 0);
	$upstat_color = $border_color;
	$date_color = $border_color;
	$stat_color = $border_color;
	// white
	$background_color = imagecolorallocate($img, 255, 255, 255);
	// red
	$downstat_color = imagecolorallocate($img, 255, 0, 0);
	imagefilledrectangle($img, 0, 0, $img_width, $img_height, $background_color);

	// draw border

	// top border
	imageline($img, $mgn_lft, $mgn_top, $img_width - $mgn_rgt, $mgn_top, $border_color);
	// right border
	imageline($img, $img_width - $mgn_rgt, $mgn_top, $img_width - $mgn_rgt, $img_height - $mgn_bot, $border_color);
	// bottom border
	imageline($img, $mgn_lft, $img_height - $mgn_bot, $img_width - $mgn_rgt, $img_height - $mgn_bot, $border_color);	
	// left border
	imageline($img, $mgn_lft, $mgn_top, $mgn_lft, $img_height - $mgn_bot, $border_color);

	// draw stat name

	$x = $img_width / 2;
	$y = $mgn_top / 2;
	imagestring($img, 5, (int) $x, (int) $y, $statname, $stat_color);

	// draw bottom labels

	$div = $x_span / ($num_stats - 1);

	for ($i = 0; $i < $num_stats; $i++) {
		imagestringup($img, 0, (int) ($mgn_lft + ($i * $div)), (int) ($img_height - 15), $values[$i]['dstamp'], $date_color);
	}

	// draw side labels

	// draw y tics
	for ($i = 0; $i <= $y_tic_number; $i++) {
		imagestring($img, 2, 15, $y_tic_y_coords[$i], $y_tic_values[$i], $border_color);
	}

	// draw stats

	$prior_x = $stat_x_coords[0];
	$prior_y = $stat_y_coords[0];

	for ($i = 0; $i < $num_stats; $i++) {

		if (!is_null($stat_y_coords[$i]) && !is_null($prior_y)) {
			if ($prior_y >= $stat_y_coords[$i]) {
				$color = $upstat_color;
			}
			else {
				$color = $downstat_color;
			}
			// the stat line itself
			$results = imageline($img, (int) $prior_x, (int) $prior_y, (int) $stat_x_coords[$i], (int) $stat_y_coords[$i], $color);
		}

		if (is_numeric($values[$i]['value'])) {
			// the value labels
			imagestring($img, 0, (int) ($stat_x_coords[$i] + 5), (int) $stat_y_coords[$i], (string) $values[$i]['value'], $stat_color);
		}

		$prior_x = $stat_x_coords[$i];
		$prior_y = $stat_y_coords[$i];
	}	

	// output image to the browser or a file
	$fname = $filename . '.' . $filetype;
	switch ($filetype) {
	case 'jpeg':
	case 'jpg':
		imagejpeg($img, $fname);
		break;
	case 'png':
		imagepng($img, $fname);
		break;
	case 'gif':
		imagegif($img, $fname);
		break;
	}

	return $fname;

}

/**
 * Graph stat using gnuplot
 *
 * @param string $statname Name of stat for stat graph
 * @param string $filename Basename of stat graph file
 * @param string $filetype (jpeg, jpg, png, pdf)
 * @param array $values Values to graph
 *
 * @return string Graph file name
 */

function gnuplot_graph($statname, $imgdir, $statcode, $filetype, $values)
{
    $csvfile = $imgdir . $statcode . '.csv';
    $gpfile = $imgdir . $statcode . '.gnuplot';

    $fp = fopen($csvfile, 'w');
    foreach ($values as $set) {
        fputcsv($fp, [$set['dstamp'], $set['value']]);
    }
    fclose($fp);

    $max = count($values);

	$fromdate = $values[0]['dstamp'];
	$todate = $values[$max - 1]['dstamp'];

    $basefile = $imgdir . $statcode;
	switch ($filetype) {
	case 'jpeg':
	case 'jpg':
		$fname = $basefile . '.jpg';
		$imgtype = 'jpeg';
		$imgsuffix = 'jpg';
		break;
	case 'png':
		$fname = $basefile . '.png';
		$imgtype = 'png';
		$imgsuffix = 'png';
		break;
	case 'pdf':
		$imgtype = 'postscript portrait';
		$fname = $basefile . '.pdf';
		$imgsuffix = 'ps';
		break;
	};

	$str =<<<EOD
set datafile separator ','
set xdata time
set timefmt "%Y-%m-%d"
set style data lines
set format x "%m/%d/%y"
set xtics rotate '$fromdate', 604800
set xrange ['$fromdate':'$todate']
set grid
set terminal $imgtype
set output '$basefile.$imgsuffix'
plot '$csvfile' using 1:($2) title '$statname'
EOD;

	$fp = fopen($gpfile, "w");
	fputs($fp, $str);
	fclose($fp);

	system("/usr/bin/gnuplot $gpfile");
	if ($filetype == 'pdf') {
		system("/usr/bin/ps2pdf $imgdir{$statcode}.ps $fname");
	}
	return $fname;
}

