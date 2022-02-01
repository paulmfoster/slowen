<?php

class pdf_report
{
	function __construct()
	{
		global $cfg;

		define('FPDF_FONTPATH', 'fpdf/font/');
		require_once('fpdf/fpdf.php');
		$this->pdf = new FPDF('P','pt','Letter');
		$this->font_size = 12;
		$this->pdf->SetFont('Courier','',$this->font_size);
		$this->dir = $cfg['printdir'];
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

	function print_line($text)
	{
		$this->pdf->Text($this->x, $this->y, $text);
		$this->skip_line();
	}

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

	function setx($x)
	{
		$this->x = $x;
		$this->pdf->SetX($x);
	}

	function getx()
	{
		return $this->pdf->GetX();
	}

	function sety($y)
	{
		$this->y = $y;
		$this->pdf->SetY($y);
	}

	function gety()
	{
		return $this->pdf->GetY();
	}

	function setxy($x, $y)
	{
		$this->setx($x);
		$this->sety($y);
	}

	function add_page()
	{
		$this->pdf->AddPage();
	}

	function output($filename)
	{
		$this->pdf->Output('F', $filename);
	}
}

