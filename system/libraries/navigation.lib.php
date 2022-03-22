<?php

class navigation
{
	function __construct()
	{
		$this->links = [];
		$this->menu_type = '';
	}

	function init($menu_type = '', $links, $top = NULL)
	{
		if (!is_string($menu_type) || strpos('HhLlAa', $menu_type) === FALSE) {
			die('Specified a non-existent menu type. Aborting.');
		}

		$this->menu_type = $menu_type;
		$this->links = $links;

		if (!is_null($top)) {
			$this->add($top);
		}
	}

	function add($top)
	{
		$this->links = array_merge($top, $this->links);
	}

	function show()
	{
		switch ($this->menu_type) {
		case 'H':
		case 'h':
			$str = $this->hierarchical();
			break;
		case 'A':
		case 'a':
			$str = $this->accordion();
			break;
		case 'L':
		case 'l':
			$str = $this->linear();
			break;
		default:
			$str = '';
		}

		return $str;
	}

	/**
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
	
	function hierarchical()
	{
		$str = '';
		foreach ($this->links as $title => $links) {
			$str .= '<label>' . $title . '</label>' . PHP_EOL;
			$str .= '<div class="menu-items">' . PHP_EOL;
			$str .= '<ul>' . PHP_EOL;
			foreach ($links as $link => $url) {
				$str .= '<li><a href="' . $url . '">' . $link . '</a></li>' . PHP_EOL;
			}
			$str .= '</ul>' . PHP_EOL;
			$str .= '</div> <!-- menu-items -->' . PHP_EOL;
		}

		return $str;
	}

	function linear()
	{
		$str = '<div class="menu-items">' . PHP_EOL;
		$str .= '<ul>' . PHP_EOL;
		foreach ($this->links as $title => $link) {
			$str .= '<li><a href="' . $link . '">' . $title . '</a></li>' . PHP_EOL;
		}
		$str .= '</ul>' . PHP_EOL;
		$str .= '</div> <!-- menu-items -->' . PHP_EOL;
		return $str;
	}

	function accordion()
	{
		$str = '<div class="accordion">' . PHP_EOL;

		$i = 1;

		foreach ($this->links as $title => $links) {
			$str .= '<input type="checkbox" id="menu-heading-' . $i . '"/>' . PHP_EOL;
			$str .= '<label for="menu-heading-' . $i . '">' . $title . '</label>' . PHP_EOL;
			$str .= '<div class="menu-items">' . PHP_EOL;
			$str .= '<ul>' . PHP_EOL;
			foreach ($links as $link => $url) {	
				$str .= '<li><a href="' . $url . '">' . $link . '</a></li>' . PHP_EOL;
			}
			$str .= '</ul>' . PHP_EOL;
			$str .= '</div> <!-- menu-items -->' . PHP_EOL;
			$i++;
		}

		$str .= '</div> <!-- accordion -->' . PHP_EOL;

		return $str;
	}
}


