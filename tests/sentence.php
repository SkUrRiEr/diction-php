<?php

require_once("../Sentence.php");
require_once("../LangEN.php");

class Test extends Sentence {
	function processSentence($string, $line) {
		echo "Process: \"".$string."\" (Length: ".strlen($string).") @Line: ".$line."\n";
	}
}

$s = new Test(new LangEN());

$s->processString(file_get_contents("input.txt"));

?>
