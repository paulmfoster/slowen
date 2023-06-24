<?php

class pdf_report
{
    public $pdf, $font_size, $dir, $top_margin, $bottom_margin, $lines_per_page;
    public $lines, $columns, $left_margin, $pageno;

	function __construct()
	{
		define('FPDF_FONTPATH', LIBDIR . 'fpdf/font/');
        if (!file_exists(LIBDIR . 'fpdf/fpdf.php')) {
            die("I can't find 'fpdf.php'!");
        }
		include LIBDIR . 'fpdf/fpdf.php';
		$this->pdf = new FPDF('P','pt','Letter');
		$this->font_size = 12; // points = 1/72 inch
		$this->pdf->SetFont('Courier', '', $this->font_size);
		$this->dir = PRINTDIR;
        $this->lines = 0;
        $this->columns = 0;
        $this->top_margin = 6;
        $this->bottom_margin = 3;
        $this->left_margin = 5;
        $this->pageno = 0;

        // 72 points to the inch * 11 inches
		$this->lines_per_page = (11 * 72) / $this->font_size;
	}

	/**
	 * Set the margins for the document
     *
     * top = 1" = 72 pts = 6 lines
     * bottom = 1/2" = 36 pts = 3 lines
     * left = 1/2" = 36 pts = 5 chars
	 *
	 * @param float Top margin in lines
	 * @param float Bottom margin in lines
	 * @param float Left margin in columns
	 */

	function set_margins($top = 0, $bottom = 0, $left = 0)
	{
        $this->top_margin = $top;
        $this->bottom_margin = $bottom;
        $this->left_margin = $left;
	}

    /**
     * Print line to PDF
     *
     * @param string The text to print
     */

	function print_line($text, $new_page = FALSE)
	{
        $this->pdf->Text(($this->columns / 10) * 72, $this->lines * $this->font_size, $text);
        $this->skip_line();
        if ($this->end_of_page() && $new_page) {
		    $this->add_page();
        }
	}

    function get_lines()
    {
        return $this->lines;
    }

    function get_pageno()
    {
        return $this->pageno;
    }

    /**
     * Increment internal variables to add one line.
     *
     * @param integer Number of lines to skip
     */

	function skip_line($num = 1)
    {
        $this->set_line($this->lines + 1);
	}

    /**
     * Set line to print on.
     *
     * @param int line to print on
     */

    function set_line($num = 0)
    {
        $this->lines = $num;
        $this->sety($this->lines * $this->font_size);
    }

    function center($text, $new_page = FALSE)
    {
        $cols = $this->columns;
        $this->set_column((85 - strlen($text)) / 2);
        $this->print_line($text, $new_page);
        $this->set_column($cols);
    }

    /**
     * Set column to print at.
     *
     * @param int column in which to print
     */

    function set_column($num = 0)
    {
        $this->columns = $num;
        $this->setx($num);
    }

    /**
     * Set the internal x-axis value
     *
     * @param integer The x value
     */

	function setx($x)
	{
        $this->pdf->SetX(($this->columns / 10) * 72);
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
        $this->pdf->SetY($this->lines * $this->font_size);
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
        $this->set_line($this->top_margin);
        $this->set_column($this->left_margin);
        $this->pageno++;
	}

    function end_of_page()
    {
        if ($this->lines >= ($this->lines_per_page - $this->bottom_margin))
            return true;
        else
            return false;
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

