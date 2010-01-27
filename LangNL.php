<?php

/* LangNL.php
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

class LangNL extends Lang {
	var $article_list = array("de", "het", "een");

	var $pronoun_list = array("ik", "jij", "je", "u", "gij", "ge", "hij", "zij", "ze", "het", "wij", "we", "jullie", "me", "mijzelf", "mezelf", "je", "jezelf", "uzelf", "zich", "zichzelf", "haarzelf", "onszelf", /* "jezelf", */ "elkaar", "elkaars", "elkander", "elkanders", "mekaar", "mekaars", "mijnen", "deinen", "zijnen", "haren", "onzen", "uwen", "hunnen", "haren", "mijner", "deiner", "zijner", "harer", "onzer", "uwer", "hunner", "harer", "mijnes", "deines", "zijnes", "hares", "onzes", "uwes", "hunnes", "hares");

	var $interrogativePronoun_list = array("welke", "wat", "wat voor", "wat voor een", "welk", "wie", "waar", "wanneer", "hoe");

	var $conjunction_list = array("en", "maar", "of", "want", "dus");

	var $subConjunction_list = array("aangezien", "als", "alsof", "behalve", "daar", "daarom", "dat", "derhalve", "doch", "doordat", "hoewel", "mits", "nadat", "noch", "ofschoon", "omdat", "ondanks", "opdat", "sedert", "sinds", "tenzij", "terwijl", "toen", "totdat", "voordat", "wanneer", "zoals", "zodat", "zodra", "zonder dat", "om te");

	var $preposition_list = array("à", "aan", "ad", "achter", "behalve", "beneden", "betreffende", "bij", "binnen", "blijkens", "boven", "buiten", "circa", "conform", "contra", "cum", "dankzij", "door", "gedurende", "gezien", "hangende", "in", "ingevolge", "inzake", "jegens", "krachtens", "langs", "met", "middels", "mits", "na", "naar", "naast", "nabij", "namens", "niettegenstaande", "nopens", "om", "omstreeks", "omtrent", "ondanks", "onder", "ongeacht", "onverminderd", "op", "over", "overeenkomstig", "per", "plus", "richting", "rond", "rondom", "sedert", "staande", "te", "tegen", "tegenover", "ten", "ter", "tijdens", "tot", "tussen", "uit", "uitgezonderd", "van", "vanaf", "vanuit", "vanwege", "versus", "via", "volgens", "voor", "voorbij", "wegens", "zonder");

	var $auxVerb_list = array("heb", "hebt", "heeft", "hebben", "had", "hadden", "gehad", /* "ben", "bent", "is", "zijn", "was", "waren", "geweest", */ "word", "wordt", "worden", "werd", "werden", "geworden", "kan", "kan", "kunnen", "kon", "konden", "gekund", "wil", "willen", "wilde", "wilden", "gewild", "wou", "wouden", "zal", "zult", "zullen", "zou", "zouden", "mag", "mogen", "mocht", "mochten", "gemogen", "moet", "moeten", "moest", "moesten", "gemoeten", "hoef", "hoeft", "hoeven", "hoefde", "hoefden", "gehoeven", "doe", "doet", "doen", "deed", "deden", "gedaan");

	var $tobeVerb_list = array("ben", "bent", "is", "zijn", "was", "waren", "geweest");

	var $nominalization_list = array("tie", "heid", "ing", "end", "ende");

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
