<?php

/**
 * Navigation class.
 */

class navigation
{
	function __construct()
	{
		$this->links = [];
		$this->menu_type = '';
	}

    /**
     * Initialize menu.
     *
     * @param character Menu type (H, h, A, a, L, l, T, t)
     * @param array Associative array of links
     * @param array Any existing menu items
     */

	function init($menu_type, $links, $top = NULL)
	{
		if (strpos('HhLlAaTt', $menu_type) === FALSE) {
			die('Specified a non-existent menu type. Aborting.');
		}

		$this->menu_type = $menu_type;
		$this->links = $links;

		if (!is_null($top)) {
			$this->add($top);
		}
	}

    /**
     * Add menu items.
     *
     * @param Menu items to insert
     */

	function add($top)
	{
		$this->links = array_merge($top, $this->links);
	}

    /**
     * Show the menu
     *
     * @return string HTML menu
     */

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
        case 'T':
        case 't':
            $str = $this->top();
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
     *	They display like:
     *
     * ```
     *	Fruits
     *	  Grapes
     *	    Red Grapes
     *	    White Grapes
     *	  Peaches
     * ```
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

    /**
     * Linear menu
     *
     * Links are as with hierarchical(), except not nested.
     * Items appear in a vertical line.
     *
	 * @return string The HTML of the array of links
     */

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

    /**
     * Accordion menu
     *
     * Links are as with hierarchical.
     * Sub items pop out when parents are clicked on.
     *
	 * @return string The HTML of the array of links
     */

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

    function top()
    {
        $str = '<ul>' . PHP_EOL;

        foreach ($this->links as $key => $value) {
            if (is_array($value)) {
                // handle as title/array of links
                $str .= "<li><a href=\"\">$key</a>";
                $str .= "<ul class=\"dropdown\">" . PHP_EOL;
                foreach ($value as $link => $url) {
                    $str .= "<li><a href=\"$url\">$link</a></li>" . PHP_EOL;
                }
                $str .= "</ul>" . PHP_EOL;
                $str .= "</li>" . PHP_EOL;
            }
            else {
                // handle as single link
                $str .= "<li><a href=\"$value\">$key</a></li>" . PHP_EOL;
            }
        }

        $str .= '</ul>' . PHP_EOL;

        return $str;
    }
}


