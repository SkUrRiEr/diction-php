<?php

/* LangEN.php
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

include_once("Lang.php");

class LangEN extends Lang {
	var $article_list = array("the", "a", "an");

	var $pronoun_list = array("i", "me", "we", "us", "you", "he", "him", "she", "her", "it", "they", "them", "thou", "thee", "ye", "myself", "yourself", "himself", "herself", "itself", "ourselves", "yourselves", "themselves", "oneself", "my", "mine", "his", "hers", "yours", "ours", "theirs", "its", "our", "that", "their", "these", "this", "those", "your");

	var $interrogativePronoun_list = array("why", "who", "what", "whom", "when", "where", "how");

	var $conjunction_list = array("and", "but", "or", "yet", "nor");

	var $subConjunction_list = array("after", "because", "lest", "till", "'til", "although", "before", "now that", "unless", "as", "even if", "provided that", "provided", "until", "as if", "even though", "since", "as long as", "so that", "whenever", "as much as", "if", "than", "as soon as", "inasmuch", "in order that", "though", "while");

	var $preposition_list = array("aboard", "about", "above", "according to", "across from", "after", "against", "alongside", "alongside of", "along with", "amid", "among", "apart from", "around", "aside from", "at", "away from", "back of", "because of", "before", "behind", "below", "beneath", "beside", "besides", "between", "beyond", "but", "by means of", "concerning", "considering", "despite", "down", "down from", "during", "except", "except for", "excepting for", "from among", "from between", "from under", "in addition to", "in behalf of", "in front of", "in place of", "in regard to", "inside of", "inside", "in spite of", "instead of", "into", "like", "near to", "off", "on account of", "on behalf of", "onto", "on top of", "on", "opposite", "out of", "out", "outside", "outside of", "over to", "over", "owing to", "past", "prior to", "regarding", "round about", "round", "since", "subsequent to", "together", "with", "throughout", "through", "till", "toward", "under", "underneath", "until", "unto", "up", "up to", "upon", "with", "within", "without", "across", "along", "by", "of", "in", "to", "near", "of", "from");

	var $auxVerb_list = array("will", "shall", "cannot", "may", "need to", "would", "should", "could", "might", "must", "ought", "ought to", "can't", "can");

	var $tobeVerb_list = array("be", "being", "was", "were", "been", "are", "is");

	var $nominalization_list = array("tion", "ment", "ence", "ance");

	function __construct() {
		$this->abbreviations = array("ch", "Ch", "ckts", "dB", "Dept", "dept", "Depts", "depts", "Dr", "Drs", "Eq", "eq", "etc", "et al", "Fig", "fig", "Figs", "figs", "ft", "0 in", "1 in", "2 in", "3 in", "4 in", "5 in", "6 in", "7 in", "8 in", "9 in", "Inc", "Jr", "jr", "mi", "Mr", "Mrs", "Ms", "No", "no", "Nos", "nos", "Ph", "Ref", "ref", "Refs", "refs", "St", "vs", "yr");
	}

	function vowel($c) {
		return ($c=='a' || $c=='ä' || $c=='e' || $c=='i' || $c=='o' || $c=='ö' || $c=='u' || $c=='ü' ||	$c=='ë' || $c=='é' || $c=='è' || $c=='à' || $c=='i' || $c=='ï' || $c=='y');
	}

	function syllables($s, $l) {
		$count = 0;

		if( $l >= 2 && preg_match("/ed$/", $s) )
			$l -= 2;

		for( $i = 0; $l > 0; $i++, $l-- )
			if ($l >= 2 && $this->vowel($s[$i]) && !$this->vowel($s[$i + 1])) {
				$count++;
				$i++;
				$l--;
			}

		if( $count == 0 )
			return 1;

		return $count;
	}
}

?>
