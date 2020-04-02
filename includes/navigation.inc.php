<?php

// Routines to show navigation links on screen as 

function navs($link_array)
{
	$str = '';
	foreach ($link_array as $link) {
		$str .= '<li><a href="' . $link['url'] . '">' . $link['txt'] . '</a></li>' . PHP_EOL;
	}

	return $str;
}

function hiernavs($link_array)
{
	$str = '';
	foreach ($link_array as $title => $links) {
		$str .= '<div class="navhead">' . $title . '</div>' . PHP_EOL;
		$str .= '<ul>' . PHP_EOL;
		foreach ($links as $link) {
			$str .= '<li class="sub"><a href="' . $link['url'] . '">' . $link['txt'] . '</a></li>' . PHP_EOL;
		}
		$str .= '</ul>' . PHP_EOL;
	} // foreach

	return $str;
}

