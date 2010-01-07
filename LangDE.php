<?php

/* LangDE.php
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

require_once("Lang.php");

class LangDE extends Lang {
	var $article_list = array("der", "die", "das", "des", "dem", "den", "ein", "eine", "einer", "eines", "einem", "einen");

	var $pronoun_list = array("ich", "du", "er", "sie", "es", "wir", "ihr", "mich", "dich", "ihn", "uns", "euch", "mir", "dir", "ihm",  "ihnen", "mein", "dein", "sein", "unser", "euer", "meiner", "deiner", "seiner", "unserer", "eurer", "ihrer", "meine", "deine", "seine", "unsere", "eure", "ihre", "meines", "deines", "seines", "unseres", "eures", "ihres", "meinem", "deinem", "seinem", "unserem", "eurem", "ihrem", "meinen", "deinen", "seinen", "unseren", "euren", "ihren");

	var $interrogativePronoun_list = array("wer", "was", "wem", "wen", "wessen", "wo", "wie", "warum", "weshalb", "wann", "wieso", "weswegen");

	var $conjunction_list = array("und", "oder", "aber", "sondern", "doch", "nur", "blo�", "denn", "weder", "noch", "sowie");

	var $subConjunction_list = array("als", "als dass", "als da�", "als ob", "anstatt dass", "anstatt da�", "ausser dass", "ausser da�", "ausser wenn", "bevor", "bis", "da", "damit", "dass", "da�", "ehe", "falls", "indem", "je", "nachdem", "ob", "obgleich", "obschon", "obwohl", "ohne dass", "ohne da�", "seit", "so da�", "sodass", "sobald", "sofern", "solange", "so oft", "statt dass", "statt da�", "w�hrend", "weil", "wenn", "wenn auch", "wenngleich", "wie", "wie wenn", "wiewohl", "wobei", "wohingegen", "zumal" "als zu", "anstatt zu", "ausser zu", "ohne zu", "statt zu", "um zu");

	var $preposition_list = array("aus", "au�er", "bei", "mit", "nach", "seit", "von", "zu", "bis", "durch", "f�r", "gegen", "ohne", "um", "an", "auf", "hinter", "in", "neben", "�ber", "unter", "vor", "zwischen", "anstatt", "statt", "trotz", "w�hrend", "wegen");

	var $auxVerb_list = array("haben", "habe", "hast", "hat", "habt", "gehabt", "h�tte", "h�ttest", "h�tten", "h�ttet", "werden", "werde", "wirst", "wird", "werdet", "geworden", "w�rde", "w�rdest", "w�rden", "w�rdet", "k�nnen", "kann", "kannst", "k�nnt", "konnte", "konntest", "konnten", "konntet", "gekonnt", "k�nnte", "k�nntest", "k�nnten", "k�nntet", "m�ssen", "muss", "mu�", "musst", "m�sst", "musste", "musstest", "mussten", "gemusst", "m�sste", "m�sstest", "m�ssten", "m�sstet", "sollen", "soll", "sollst", "sollt", "sollte", "solltest", "solltet", "sollten", "gesollt");

	var $tobeVerb_list = array("sein", "bin", "bist", "ist", "sind", "seid", "war", "warst", "wart", "waren", "gewesen", "w�re", "w�rst", "w�r", "w�ren", "w�rt", "w�ret");

	var $nominalization_list = array("ung", "heit", "keit", "nis", "tum");

	function __construct() {
		// $this->abbreviations = array();
	}

	function syllables($s, $l) {
		$count = 0;
		$ol = $l > 1;

		if( $this->vowel($s[0]) ) {
			for( $i = 0; $l > 0; $i++, $l-- )
				if( $l > 1 && $this->vowel($s[$i]) && !$this->vowel($s[$i + 1]) ) {
					$count++;
					$i++;
					$l--;
				} else if( $l == 1 && $ol && !$this->vowel($s[$i - 1]) && $s[$i] == "e" )
					$count++;
		} else {
			for( $i = 0; $l > 0; $i++, $l-- )
				if( $l > 1 && !$this->vowel($s[$i]) && $this->vowel($s[$i + 1]) ) {
					$count++;
					$i++;
					$l--;
				}
		}

		if( $count == 0 )
			return 1;

		return $count;
	}
}

?>
