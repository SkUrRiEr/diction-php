<?php

require_once("../Sentence.php");
require_once("../LangEN.php");
require_once("test_environment.php");

class Test extends Sentence {
	function processSentence($string, $line) {
		echo "\"".$string."\" (Length: ".strlen($string).") @Line: ".$line."\n";
	}
}

class Generator extends Sentence {
	function processSentence($string, $line) {
		echo $line.":".strlen($string).":".$string."\n";
	}
}

$filename = "input.txt";
$s = new Test(new LangEN());

if( $_SERVER["argc"] > 1 )
	for( $i = 1; $i < $_SERVER["argc"]; $i++ )
		if( $_SERVER["argv"][$i] == "--generate" )
			$s = new Generator(new LangEN());
		else
			$filename = $_SERVER["argv"][$i];

$s->processString(file_get_contents($filename));

?>
