<?php

/* Sentence.php
 *
 * Copyright 2009 Julian Calaby <julian.calaby@gmail.com>
 *
 * This file is based upon sentence.c from GNU Diction.
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

require_once("DictionBase.php");

abstract class Sentence extends DictionBase {
	abstract function processSentence($string, $line);

	function endingInPossessiveS($s) {
		return (strlen($s) > 2 && substr($s, -2) == "'s");
	}

	function endingInAbbrev($s) {
		$length = strlen($s);

		$s = substr($s, 0, $length - 1);

		$length--;

		if( $length == 1 )
			return true;

		if( !ctype_alpha($s[$length - 1]) )
			return false;

		if( $this->endingInPossessiveS($s) )
			return false;

		if( $length > 1 && !ctype_alpha($s[$length - 2]) )
			return true;

		foreach($this->lang->abbreviations as $abbrev)
			if( substr($s, -strlen($abbrev)) == $abbrev )
				return true;

		return false;
	}

	function processString($in) {
		$sent = "";
		$inWhiteSpace = false;
		$inParagraph = false;
		$line = 1;
		$beginLine = 1;

		$in = trim($in);

		$voc = '\n';
		$oc = $in[0];

		for( $i = 1; $i <= strlen($in); $i++ ) {
			if( $i == strlen($in) )
				$c = -1;
			else
				$c = $in[$i];

			if($oc == "\n")
				$line++;

			if($sent != "") {
				if(ctype_space($oc)) {
					if(!$inWhiteSpace) {
						$sent .= " ";
						$inWhiteSpace = true;
					}
				} else {
					$sent .= $oc;

					if( preg_match("/(^|\s)\.\.\.$/", $sent) && ctype_space($c) ) {
						/* omission ellipsis */
					} else if(
						$c == -1 /* end of file */
						|| ( preg_match("/[^ ]\.\.\.$/", $sent) && ($c == -1 || ctype_space($c)) ) /* ending ellipsis */
						|| ( ($oc == "." || $oc == ":" || $oc == "!" || $oc == "?") && (ctype_space($c) || $c == "\"") && !($oc == "." && $this->endingInAbbrev($sent)) ) /* end of sentence */
							) {
						$this->processSentence(rtrim($sent), $beginLine);

						$sent = "";
					}

					$inWhiteSpace = false;
				}
			} else if( ctype_alpha($oc) ) {
				$inParagraph = false;
				$sent = $oc;
				$inWhiteSpace = false;
				$beginLine = $line;
			} else if( $voc == "." && $oc == "." && $c == "." ) {
				$inParagraph = false;
				$sent = "..";
				$inWhiteSpace = false;
				$beginLine = $line;
			} else if( !$inParagraph && $oc == "\n" && $c == "\n" ) {
				$this->processSentence("", $line);

				$inParagraph = true;
			}

			$voc = $oc;
			$oc = $c;
		}

		if( !$inParagraph )
			$this->processSentence("", $line + 1);
	}
}

?>
