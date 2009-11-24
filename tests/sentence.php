<?php

include_once("../Sentence.php");
include_once("../LangEN.php");

class Test extends Sentence {
	function processSentence($string, $length, $line) {
		echo "Process: \"".$string."\" (Length: ".$length.") @Line: ".$line."\n";
	}
}

$s = new Test(new LangEN());

$s->processString(file_get_contents("input.txt"));

?>
