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

		if( $length == 1 )
			return true;

		if( !ctype_alpha($s[$length - 1]) )
			return false;

		if( $this->endingInPossessiveS($s) )
			return false;

		if( !ctype_alpha($s[$length - 2]) )
			return true;

		foreach($this->lang->abbreviations as $abbrev) {
			$aLength = strlen($abbrev);

			if( $aLength < $length ) {
				if( !ctype_alpha($s[$length - $aLength - 1]) && substr($s, $length - $aLength, $aLength) == $abbrev )
					return true;
			} else if( $s == $abbrev )
				return true;
		}

		return false;
	}

	function processString($in) {
		$sent = "";
		$inSentence = false;
		$inWhiteSpace = false;
		$inParagraph = false;
		$line = 1;
		$beginLine = 1;

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

					if(ctype_alpha($oc))
						$inSentence = true;

					if( preg_match("/(^|\s)\.\.\.$/", $sent) && ($c == -1 || ctype_space($c)) ) {
						/* omission ellipsis */
					} else if( preg_match("/^(.*[^ ])\.\.\.$/", $sent, $regs) && ($c == -1 || ctype_space($c)) ) {
						/* beginning ellipsis */
						$sent = $regs[1];

						if( $inSentence )
							$this->processSentence($sent, $beginLine);

						$sent = "...";
						$inParagraph = false;
						$beginLine = $line;
						$inSentence = false;
					} else if( preg_match("/\.\.\..$/", $sent) && ($c == -1 || ctype_space($c)) ) {
						/* ending ellipsis */
						if( $inSentence )
							$this->processSentence(rtrim($sent), $beginLine);

						$sent = "";
						$inSentence = false;
					} else if( ($oc == "." || $oc == ":" || $oc == "!" || $oc == "?") && ($c == -1 || ctype_space($c) || $c == "\"") && !($oc == "." && $this->endingInAbbrev($sent)) ) {
						/* end of sentence */
						if( $inSentence )
							$this->processSentence(rtrim($sent), $beginLine);

						$sent = "";
						$inSentence = false;
					}

					$inWhiteSpace = false;
				}
			} else if( ctype_upper($oc) ) {
				$inParagraph = false;
				$sent .= $oc;
				$inWhiteSpace = false;
				$beginLine = $line;
				$inSentence = true;
			} else if( !$inParagraph && $oc == "\n" && $c == "\n" ) {
				$this->processSentence("", $line);

				$inParagraph = true;
			}

			$voc = $oc;
			$oc = $c;
		}

		if( !$inParagraph )
			$this->processSentence("", $line);
	}
}

?>
