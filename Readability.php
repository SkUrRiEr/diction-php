<?php

/* Readability.php
 *
 * Copyright 2009 Julian Calaby <julian.calaby@gmail.com>
 *
 * This file is based upon style.c from GNU Diction.
 * 
 * GNU Diction is GNU software, copyright 1997-2007
 * Michael Haardt <michael@moria.de>.
 *
 * This program is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation; either version 3 of the License, or (at your
 * option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
 * for more details.
 * 
 * You should have received a copy of the GNU General Public License along
 * with this program.  If not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

require_once("Style.php");

class Readability extends Style {
	function kincaid() {
		return 11.8 * ($this->syllables / $this->words) + 0.39 * ($this->words / $this->sentences) - 15.59;
	}

	function ari() {
		return 4.71 * ($this->characters / $this->words) + 0.5 * ($this->words / $this->sentences) - 21.43;
	}

	function coleman_liau() {
		return 5.879851 * ($this->characters / $this->words) - 29.587280 * ($this->sentences / $this->words) - 15.800804;
	}

	function flesch() {
		return 206.835 - 84.6 * ($this->syllables / $this->words) - 1.015 * ($this->words / $this->sentences);
	}

	function fog() {
		return 0.4 * ($this->words / $this->sentences + 100.0 * $this->bigwords / $this->words);
	}

	function wstf() {
		return 0.1935 * ($this->bigwords / $this->words) + 0.1672 * ($this->words / $this->sentences) - 0.1297 * ($this->longwords / $this->words)  - 0.0327 * ($this->shortwords / $this->words) - 0.875;
	}

	function wheeler_smith() {
		$idx = ($this->words / $this->sentences) * 10.0 * ($this->bigwords / $this->words);

		$grade = 99;

		if ($idx <= 16)
		       	$grade = 0;
		else if ($idx <= 20)
		       	$grade = 5;
		else if ($idx <= 24)
		       	$grade = 6;
		else if ($idx <= 29)
		       	$grade = 7;
		else if ($idx <= 34)
		       	$grade = 8;
		else if ($idx <= 38)
		       	$grade = 9;
		else if ($idx <= 42)
		       	$grade = 10;

		return array("_value" => $idx, "grade" => $grade);
	}

	function lix() {
		$idx = ($this->words / $this->sentences) + 100.0 * ($this->longwords / $this->words);

		$grade = 99;

		if ($idx < 34)
		       	$grade = 0;
		else if ($idx < 38)
		       	$grade = 5;
		else if ($idx < 41)
		       	$grade = 6;
		else if ($idx < 44)
		       	$grade = 7;
		else if ($idx < 48)
		       	$grade = 8;
		else if ($idx < 51)
		       	$grade = 9;
		else if ($idx < 54)
		       	$grade = 10;
		else if ($idx < 57)
		       	$grade = 11;

		return array("_value" => $idx, "grade" => $grade);
	}

	function smog() {
		return sqrt(30.0 * ($this->bigwords / $this->sentences)) + 3.0;
	}
}

?>
