<?php

require_once("../Sentence.php");
require_once("../LangEN.php");

class Test extends Sentence {
	function processSentence($string, $line) {
		echo "\"".$string."\" (Length: ".strlen($string).") @Line: ".$line."\n";
	}
}

$s = new Test(new LangEN());

$filename = "input.txt";

if( $_SERVER["argc"] > 1 )
	$filename = $_SERVER["argv"][1];

$s->processString(file_get_contents($filename));

?>
