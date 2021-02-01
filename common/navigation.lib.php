<?php

class navigation
{
	function __construct()
	{
		$this->links = [];
	}

	function init($bottom, $top = NULL)
	{
		if (file_exists($bottom)) {
			include $bottom;
		}
		else {
			die('No basic navigation specified. Aborting.');
		}

		$this->links = $links;

		if (!is_null($top)) {
			$this->add($top);
		}
	}

	function add($top)
	{
		$this->links = array_merge($top, $this->links);
	}

	/**
	 * nav()
	 *
	 * Creates a HTML string with is a hierarchical list of links with
	 * headings.
	 *
	 * Links are in the form of:
	 *
	 * 'Fruits' => [
	 * 		'Grapes' => [
	 * 			'Red Grapes' => 'redgrapes.com',
	 * 			'White Grapes' => 'whitegrapes.com'
	 * 		],
	 * 		'Peaches' => 'peaches.com'
	 *	];
	 *
	 * @return string The HTML of the array of links
	 */
	
	function show()
	{
		$str = '';
		foreach ($this->links as $title => $links) {
			$str .= '<div class="navhead">' . $title . '</div>' . PHP_EOL;
			$str .= '<ul>' . PHP_EOL;
			foreach ($links as $link => $url) {
				$str .= '<li class="sub"><a href="' . $url . '">' . $link . '</a></li>' . PHP_EOL;
			}
			$str .= '</ul>' . PHP_EOL;
		}

		return $str;
	}


}


