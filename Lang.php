<?php

/* Lang.php
 *
 * Copyright 2009 Julian Calaby <julian.calaby@gmail.com>
 *
 * This file is based upon sentence.c and style.c from GNU Diction.
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

abstract class Lang {
	var $article_list;
	var $pronoun_list;
	var $interrogativePronoun_list;
	var $conjunction_list;
	var $subConjunction_list;
	var $preposition_list;
	var $auxVerb_list;
	var $tobeVerb_list;
	var $nominalization_list;

	var $abbreviations = array("ch", "Ch", "ckts", "dB", "Dept", "dept", "Depts", "depts", "Dr", "Drs", "Eq", "eq", "etc", "et al", "Fig", "fig", "Figs", "figs", "ft", "0 in", "1 in", "2 in", "3 in", "4 in", "5 in", "6 in", "7 in", "8 in", "9 in", "Inc", "Jr", "jr", "mi", "Mr", "Mrs", "Ms", "No", "no", "Nos", "nos", "Ph", "Ref", "ref", "Refs", "refs", "St", "vs", "yr");

	function vowel($c) {
		return ($c=='a' || $c=='ä' || $c=='e' || $c=='i' || $c=='o' || $c=='ö' || $c=='u' || $c=='ü' ||	$c=='ë' || $c=='é' || $c=='è' || $c=='à' || $c=='i' || $c=='ï' || $c=='y');
	}

	abstract function syllables($s, $l);
}

?>
