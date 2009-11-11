<?php

abstract class Sentence {
	static $abbreviations;

	abstract function process($string, $length, $line);

	function __construct() {
		$this->abbreviations = array(
			"ch",
			"Ch",
			"ckts",
			"dB",
			"Dept",
			"dept",
			"Depts",
			"depts",
			"Dr",
			"Drs",
			"Eq",
			"eq",
			"etc",
			"et al",
			"Fig",
			"fig",
			"Figs",
			"figs",
			"ft",
			"0 in",
			"1 in",
			"2 in",
			"3 in",
			"4 in",
			"5 in",
			"6 in",
			"7 in",
			"8 in",
			"9 in",
			"Inc",
			"Jr",
			"jr",
			"mi",
			"Mr",
			"Mrs",
			"Ms",
			"No",
			"no",
			"Nos",
			"nos",
			"Ph",
			"Ref",
			"ref",
			"Refs",
			"refs",
			"St",
			"vs",
			"yr",
		);
	}

	function endingInPossessiveS($s, $length) {
		return ($length >= 3 && substr($s, $length - 2, 2) == "\'s");
	}

	function endingInAbbrev($s, $length) {
		if( ctype_alpha($s[$length - 1]) )
			return 0;

		if( $this->endingInPossessiveS($s, $length) )
			return 0;

		foreach($this->abbreviations as $abbrev) {
			$aLength = strlen($abbrev);

			if( $aLength < $length ) {
				if( !ctype_alpha($s[$length - 2]) )
					return 1;

				if( !ctype_alpha($s[$length - $aLength - 1]) && substr($s, $length - $aLength, $aLength) == $abbrev )
					return 1;
			} else {
				if( $length == 1 )
					return 1;

				if( $aLength == $length && substr($s, 0, $aLength) == $abbrev )
					return 1;
			}
		}

		return 0;
	}

	function sentence($in) {
		$sent = "";
		$length = 0;
		$capacity = 128;
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

			if($length != 0) {
				if(ctype_space($oc)) {
					if(!$inWhiteSpace) {
						$sent .= " ";
						$length++;
						$inWhiteSpace = true;
					}
				} else {
					$sent .= $oc;
					$length++;
					
					if(ctype_alpha($oc))
						$inSentence = true;

					if( preg_match("/\s*\.\.\.$/", $sent) && ($c == -1 || ctype_space($c)) )
						$inWhiteSpace = false;
					else if( preg_match("/^(.*[^ ])\.\.\.$/", $sent, $regs) && ($c == -1 || ctype_space($c)) ) {
						/* beginning ellipsis */
						$sent = $regs[1];

						if( $inSentence )
							$this->process($sent, $length - 3, $beginLine);

						$sent = "...";
						$length = 3;
						$inParagraph = false;
						$inWhiteSpace = false;
						$beginLine = $line;
						$inSentence = false;
					} else if( preg_match("/\.\.\.(.)$/", $sent, $regs) && ($c == -1 || ctype_space($c)) ) {
						/* ending ellipsis */
						if( $inWhiteSpace ) {
							$length--;

							$sent = substr($sent, 0, $length);
						}

						if( $inSentence )
							$this->process($sent, $length, $beginLine);

						$sent = "";
						$length = 0;
						$inWhiteSpace = false;
						$inSentence = false;
					} else if( ($oc == "." || $oc == ":" || $oc == "!" || $oc == "?") && ($c == -1 || ctype_space($c) || $c = "\"") && (!ctype_digit($voc) || $oc != "." || !ctype_digit($c)) && ($oc != "." || !$this->endingInAbbrev($sent, $length)) ) {
						/* end of sentence */
						if( $inWhiteSpace ) {
							$length--;

							$sent = substr($sent, 0, $length);
						}

						if( $inSentence )
							$this->process($sent, $length, $beginLine);
						$sent = "";
						$length = 0;
						$inWhiteSpace = false;
						$inSentence = false;
					} else
						/* just a regular character */
						$inWhiteSpace = false;
				}
			} else if( ctype_upper($oc) ) {
				$inParagraph = false;
				$length++;
				$sent .= $oc;
				$inWhiteSpace = false;
				$beginLine = $line;
				$inSentence = true;
			} else if( !$inParagraph && $oc == "\n" && $c == "\n" ) {
				$this->process("", 0, $line);

				$inParagraph = true;
			}

			$voc = $oc;
			$oc = $c;
		}
		
		if( !$inParagraph )
			$this->process("", 0, $line);
	}
}

?>
