<?php

/* Style.php
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

include_once("Sentence.php");

class Style extends Sentence {
	var $characters;
	var $syllables;
	var $words;
	var $shortwords;
	var $longwords;
	var $bigwords;
	var $sentences;
	var $questions;
	var $passiveSent;
	var $beginArticles;
	var $beginPronouns;
	var $pronouns;
	var $beginInterrogativePronouns;
	var $interrogativePronouns;
	var $beginConjunctions;
	var $conjunctions;
	var $nominalizations;
	var $prepositions;
	var $beginPrepositions;
	var $beginSubConjunctions;
	var $subConjunctions;
	var $auxVerbs;
	var $tobeVerbs;
	var $shortestLine;
	var $shortestLength;
	var $longestLine;
	var $longestLength;
	var $paragraphs;

	public static $article_list = array("the", "a", "an");

	public static $pronoun_list = array("i", "me", "we", "us", "you", "he", "him", "she", "her", "it", "they", "them", "thou", "thee", "ye", "myself", "yourself", "himself", "herself", "itself", "ourselves", "yourselves", "themselves", "oneself", "my", "mine", "his", "hers", "yours", "ours", "theirs", "its", "our", "that", "their", "these", "this", "those", "your");

	public static $interrogativePronoun_list = array("why", "who", "what", "whom", "when", "where", "how");

	public static $conjunction_list = array("and", "but", "or", "yet", "nor");

	public static $subConjunction_list = array("after", "because", "lest", "till", "'til", "although", "before", "now that", "unless", "as", "even if", "provided that", "provided", "until", "as if", "even though", "since", "as long as", "so that", "whenever", "as much as", "if", "than", "as soon as", "inasmuch", "in order that", "though", "while");

	public static $preposition_list = array("aboard", "about", "above", "according to", "across from", "after", "against", "alongside", "alongside of", "along with", "amid", "among", "apart from", "around", "aside from", "at", "away from", "back of", "because of", "before", "behind", "below", "beneath", "beside", "besides", "between", "beyond", "but", "by means of", "concerning", "considering", "despite", "down", "down from", "during", "except", "except for", "excepting for", "from among", "from between", "from under", "in addition to", "in behalf of", "in front of", "in place of", "in regard to", "inside of", "inside", "in spite of", "instead of", "into", "like", "near to", "off", "on account of", "on behalf of", "onto", "on top of", "on", "opposite", "out of", "out", "outside", "outside of", "over to", "over", "owing to", "past", "prior to", "regarding", "round about", "round", "since", "subsequent to", "together", "with", "throughout", "through", "till", "toward", "under", "underneath", "until", "unto", "up", "up to", "upon", "with", "within", "without", "across", "along", "by", "of", "in", "to", "near", "of", "from");

	public static $auxVerb_list = array("will", "shall", "cannot", "may", "need to", "would", "should", "could", "might", "must", "ought", "ought to", "can't", "can");

	public static $tobeVerb_list = array("be", "being", "was", "were", "been", "are", "is");

	public static $nominalization_list = array("tion", "ment", "ence", "ance");

	var $lengths;

	function __construct() {
		$this->lengths = array();

		$this->characters = 0;
		$this->syllables = 0;
		$this->words = 0;
		$this->shortwords = 0;
		$this->longwords = 0;
		$this->bigwords = 0;
		$this->sentences = 0;
		$this->questions = 0;
		$this->passiveSent = 0;
		$this->beginArticles = 0;
		$this->beginPronouns = 0;
		$this->pronouns = 0;
		$this->beginInterrogativePronouns = 0;
		$this->interrogativePronouns = 0;
		$this->beginConjunctions = 0;
		$this->conjunctions = 0;
		$this->nominalizations = 0;
		$this->prepositions = 0;
		$this->beginPrepositions = 0;
		$this->beginSubConjunctions = 0;
		$this->subConjunctions = 0;
		$this->auxVerbs = 0;
		$this->tobeVerbs = 0;
		$this->shortestLine = 0;
		$this->shortestLength = 0;
		$this->longestLine = 0;
		$this->longestLength = 0;
		$this->paragraphs = 0;
	}

	function listcmp($word, $list) {
		foreach($list as $item)
			if( $item == substr($word, 0, strlen($item)) && !ctype_alpha($word[strlen($item)]) )
				return strlen($item);

		return false;
	}

	/**
	 * Test if the word is an article.  This function uses docLanguage to
	 * determine the used language.
	 */
	function article($word) {
		return $this->listcmp($word, Style::$article_list);
	}

	/**
	 * Test if the word is a pronoun.  This function uses docLanguage to
	 * determine the used language.
	 */
	function pronoun($word) {
		return $this->listcmp($word, Style::$pronoun_list);
	}

	/**
	 * Test if the word is an interrogative pronoun.  This function uses
	 * docLanguage to determine the used language.
	 */
	function interrogativePronoun($word) {
		return $this->listcmp($word, Style::$interrogativePronoun_list);
	}

	/**
	 * Test if the word is an conjunction.  This function uses
	 * docLanguage to determine the used language.
	 */
	function conjunction($word) {
		return $this->listcmp($word, Style::$conjunction_list);
	}

	/**
	 * Test if the word is an sub conjunction.  This function uses
	 * docLanguage to determine the used language.
	 */
	function subConjunction($word) {
		return $this->listcmp($word, Style::$subConjunction_list);
	}

	/**
	 * Test if the word is an preposition.  This function uses
	 * docLanguage to determine the used language.
	 */
	function preposition($word) {
		return $this->listcmp($word, Style::$preposition_list);
	}

	/**
	 * Test if the word is an auxiliary verb.  This function uses
	 * docLanguage to determine the used language.
	 */
	function auxVerb($word) {
		return $this->listcmp($word, Style::$auxVerb_list);
	}

	/**
	 * Test if the word is an 'to be' verb.  This function uses
	 * docLanguage to determine the used language.
	 */
	function tobeVerb($word) {
		return $this->listcmp($word, Style::$tobeVerb_list);
	}

	/**
	 * Test if the word is a nominalization.  This function uses
	 * docLanguage to determine the used language.
	 */
	function nominalization($word, $l) {
		if( $l < 7 )
		       	return 0;

		foreach(Style::$nominalization_list as $item)
			if( $item == substr($word, strlen($word) - strlen($item)) )
				return true;

		return false;
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

	function processString($in) {
		parent::processString($in);

		ksort($this->lengths);
	}

	/**
	 * Process one sentence.
	 * @param str sentence
	 * @param length its length
	 */
	function processSentence($str, $length, $line) {
		$firstWord = true;
		$inword = false;
		$innumber = false;
		$wordLength = -1;
		$sentWords = 0;
		$passive = false;

		if($length == 0) {
			$this->paragraphs++;
			return;
		}

		assert($str != null);
		assert($length >= 2);
		
		$phraseEnd = 0;

		for( $i = 0; $i < $length; $i++ ) {
			$s = $str[$i];
			
			if( $inword ) {
				if( !ctype_alpha($s) && $s != '-' && !$this->endingInPossessiveS($str, $i + 2) ) {
					$word = strtolower(substr($str, $i - $wordLength, $wordLength));
					$wordstring = strtolower(substr($str, $i - $wordLength));

					$inword = false;

					$count = $this->syllables($word, $wordLength);
					$this->syllables += $count;

					if( $count >= 3 )
						$this->bigwords++;
					else if( $count == 1 )
						$this->shortwords++;

					if( $wordLength > 6 )
						$this->longwords++;

					if( $phraseEnd == 0 || ($i - $wordLength) > $phraseEnd ) {
						/* part of speech tagging-- order matters! */
						if( $firstWord && $this->article($wordstring) !== false )
						       	$this->beginArticles++;
						else if( $this->pronoun($wordstring) !== false ) {
							$this->pronouns++;

							if( $firstWord )
								$this->beginPronouns++;
						} else if( $this->interrogativePronoun($wordstring) !== false ) {
							$this->interrogativePronouns++;

							if( $firstWord )
								$this->beginInterrogativePronouns++;
						} else if( $this->conjunction($wordstring) !== false ) {
							$this->conjunctions++;

							if( $firstWord )
								$this->beginConjunctions++;
						} else if( ($len = $this->subConjunction($wordstring)) !== false ) {
							$phraseEnd = $i - $wordLength + $len;

							$this->subConjunctions++;

							if( $firstWord )
								$this->beginSubConjunctions++;
						} else if( ($len = $this->preposition($wordstring)) !== false ) {
							$phraseEnd = $i - $wordLength + $len;

							$this->prepositions++;

							if( $firstWord )
								$this->beginPrepositions++;
						} else if( $this->tobeVerb($wordstring) !== false ) {
							$passive = true;
							$this->tobeVerbs++;
						} else if( ($len = $this->auxVerb($wordstring)) !== false ) {
							$phraseEnd = $i - $wordLength + $len;

							$this->auxVerbs++;
						} else if( $this->nominalization($word, $wordLength) )
							$this->nominalizations++;
					}
					
					if( $firstWord )
						$firstWord = false;
				} else {
					$wordLength++;
					$this->characters++;
				}
			} else if( $innumber ) {
				if( ctype_digit($s) || (($s == "." || $s == ",") && ctype_digit($str[$i + 1])) ) {
					$wordLength++;
					$this->characters++;
				} else {
					$innumber = false;
					$this->syllables++;
				}
			} else {
				if( ctype_alpha($s) ) {
					$this->words++;
					$sentWords++;
					$inword = true;
					$wordLength = 1;
					$this->characters++;
				} else if( ctype_digit($s) ) {
					$this->words++;
					$sentWords++;
					$innumber = true;
					$wordLength = 1;
					$this->characters++;
				}
			}
		}

		$this->sentences++;

		if( $this->shortestLine == 0 || $sentWords < $this->shortestLength ) {
			$this->shortestLine = $this->sentences;
			$this->shortestLength = $sentWords;
		}

		if( $this->longestLine == 0 || $sentWords > $this->longestLength ) {
			$this->longestLine = $this->sentences;
			$this->longestLength = $sentWords;
		}

		if( $str[$length - 1] == '?' )
			$this->questions++;

		$this->lengths[$sentWords]++;
		
		if( $passive )
		       	$this->passiveSent++;
	}
}

?>
