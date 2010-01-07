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

	var $conjunction_list = array("und", "oder", "aber", "sondern", "doch", "nur", "bloß", "denn", "weder", "noch", "sowie");

	var $subConjunction_list = array("als", "als dass", "als daß", "als ob", "anstatt dass", "anstatt daß", "ausser dass", "ausser daß", "ausser wenn", "bevor", "bis", "da", "damit", "dass", "daß", "ehe", "falls", "indem", "je", "nachdem", "ob", "obgleich", "obschon", "obwohl", "ohne dass", "ohne daß", "seit", "so daß", "sodass", "sobald", "sofern", "solange", "so oft", "statt dass", "statt daß", "während", "weil", "wenn", "wenn auch", "wenngleich", "wie", "wie wenn", "wiewohl", "wobei", "wohingegen", "zumal" "als zu", "anstatt zu", "ausser zu", "ohne zu", "statt zu", "um zu");

	var $preposition_list = array("aus", "außer", "bei", "mit", "nach", "seit", "von", "zu", "bis", "durch", "für", "gegen", "ohne", "um", "an", "auf", "hinter", "in", "neben", "über", "unter", "vor", "zwischen", "anstatt", "statt", "trotz", "während", "wegen");

	var $auxVerb_list = array("haben", "habe", "hast", "hat", "habt", "gehabt", "hätte", "hättest", "hätten", "hättet", "werden", "werde", "wirst", "wird", "werdet", "geworden", "würde", "würdest", "würden", "würdet", "können", "kann", "kannst", "könnt", "konnte", "konntest", "konnten", "konntet", "gekonnt", "könnte", "könntest", "könnten", "könntet", "müssen", "muss", "muß", "musst", "müsst", "musste", "musstest", "mussten", "gemusst", "müsste", "müsstest", "müssten", "müsstet", "sollen", "soll", "sollst", "sollt", "sollte", "solltest", "solltet", "sollten", "gesollt");

	var $tobeVerb_list = array("sein", "bin", "bist", "ist", "sind", "seid", "war", "warst", "wart", "waren", "gewesen", "wäre", "wärst", "wär", "wären", "wärt", "wäret");

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
