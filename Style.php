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

require_once("Sentence.php");

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

	var $lengths;

	function __construct($lang = null) {
		parent::__construct($lang);

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
		return $this->listcmp($word, $this->lang->article_list);
	}

	/**
	 * Test if the word is a pronoun.  This function uses docLanguage to
	 * determine the used language.
	 */
	function pronoun($word) {
		return $this->listcmp($word, $this->lang->pronoun_list);
	}

	/**
	 * Test if the word is an interrogative pronoun.  This function uses
	 * docLanguage to determine the used language.
	 */
	function interrogativePronoun($word) {
		return $this->listcmp($word, $this->lang->interrogativePronoun_list);
	}

	/**
	 * Test if the word is an conjunction.  This function uses
	 * docLanguage to determine the used language.
	 */
	function conjunction($word) {
		return $this->listcmp($word, $this->lang->conjunction_list);
	}

	/**
	 * Test if the word is an sub conjunction.  This function uses
	 * docLanguage to determine the used language.
	 */
	function subConjunction($word) {
		return $this->listcmp($word, $this->lang->subConjunction_list);
	}

	/**
	 * Test if the word is an preposition.  This function uses
	 * docLanguage to determine the used language.
	 */
	function preposition($word) {
		return $this->listcmp($word, $this->lang->preposition_list);
	}

	/**
	 * Test if the word is an auxiliary verb.  This function uses
	 * docLanguage to determine the used language.
	 */
	function auxVerb($word) {
		return $this->listcmp($word, $this->lang->auxVerb_list);
	}

	/**
	 * Test if the word is an 'to be' verb.  This function uses
	 * docLanguage to determine the used language.
	 */
	function tobeVerb($word) {
		return $this->listcmp($word, $this->lang->tobeVerb_list);
	}

	/**
	 * Test if the word is a nominalization.  This function uses
	 * docLanguage to determine the used language.
	 */
	function nominalization($word, $l) {
		if( $l < 7 )
		       	return 0;

		foreach($this->lang->nominalization_list as $item)
			if( $item == substr($word, strlen($word) - strlen($item)) )
				return true;

		return false;
	}

	function syllables($s, $l) {
		return $this->lang->syllables($s, $l);
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
	function processSentence($str, $line) {
		$firstWord = true;
		$inword = false;
		$innumber = false;
		$wordLength = -1;
		$sentWords = 0;
		$passive = false;

		if($str == "") {
			$this->paragraphs++;
			return;
		}

		assert($str != null);
		assert(strlen($str)>= 2);
		
		$phraseEnd = 0;

		for( $i = 0; $i < strlen($str); $i++ ) {
			$s = $str[$i];
			
			if( $inword ) {
				if( !ctype_alpha($s) && $s != '-' && !$this->endingInPossessiveS(substr($str, 0, $i + 2)) ) {
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

		if( $str[strlen($str) - 1] == '?' )
			$this->questions++;

		if( !isset($this->lengths[$sentWords]) )
			$this->lengths[$sentWords] = 1;
		else
			$this->lengths[$sentWords]++;
		
		if( $passive )
		       	$this->passiveSent++;
	}
}

?>
