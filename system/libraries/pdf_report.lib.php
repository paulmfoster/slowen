<?php

class pdf_report
{
	function __construct()
	{
		define('FPDF_FONTPATH', LIBDIR . 'fpdf/font/');
        if (!file_exists(LIBDIR . 'fpdf/fpdf.php')) {
            die("I can't find 'fpdf.php'!");
        }
		include LIBDIR . 'fpdf/fpdf.php';
		$this->pdf = new FPDF('P','pt','Letter');
		$this->font_size = 12;
		$this->pdf->SetFont('Courier','',$this->font_size);
		$this->dir = PRINTDIR;
		$this->x = 0;
		$this->y = 0;
		$this->top_margin = 0;
		$this->bottom_margin = 0;

		$this->lines_per_page = (11 * 72) / $this->font_size;
	}

	/**
	 * Set the margins for the document
	 *
	 * @param float Top margin in lines
	 * @param float Bottom margin in lines
	 * @param float Left margin in characters
	 */

	function set_margins($top = 0, $bottom = 0, $left = 0)
	{
		$this->x = $left * 72 / $this->font_size;

		$this->top_margin = $top * $this->font_size;
		$this->y = $this->top_margin;

		$lines_per_page = 11 * 72 / $this->font_size;
		$bottom_blank = $lines_per_page - $bottom;

		$this->bottom_margin = $bottom_blank * $this->font_size;

	}

    /**
     * Print line to PDF
     *
     * @param string The text to print
     */

	function print_line($text)
	{
		$this->pdf->Text($this->x, $this->y, $text);
		$this->skip_line();
	}

    /**
     * Increment internal variables to add one line.
     *
     * @param integer Number of lines to skip
     */

	function skip_line($num = 1)
	{
		$this->y += $num * $this->font_size;
		
		/*
		if ($this->y >= $this->bottom_margin) {
			$this->add_page();
			$this->y = $this->top_margin;
		}
		 */
	}

    /**
     * Set the internal x-axis value
     *
     * @param integer The x value
     */

	function setx($x)
	{
		$this->x = $x;
		$this->pdf->SetX($x);
	}

    /**
     * Get the internal x-axis value
     *
     * @return integer The internal x-axis value
     */

	function getx()
	{
		return $this->pdf->GetX();
	}

    /**
     * Set the internal y (vertical) value
     *
     * @param integer The value to set
     */

	function sety($y)
	{
		$this->y = $y;
		$this->pdf->SetY($y);
	}

    /**
     * Get the internal y axis value
     *
     * @return integer The y value
     */

	function gety()
	{
		return $this->pdf->GetY();
	}

    /**
     * Set both x and y internal values
     *
     * @param integer x value
     * @param integer y value
     */

	function setxy($x, $y)
	{
		$this->setx($x);
		$this->sety($y);
	}

    /**
     * Add a page in PDF
     */

	function add_page()
	{
		$this->pdf->AddPage();
	}

    /**
     * Generate an output PDF
     *
     * @param string The filename to write to
     */

	function output($filename)
	{
		$this->pdf->Output('F', $filename);
	}
}

