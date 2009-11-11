<?php

include_once("../Sentence.php");

class Test extends Sentence {
	function process($string, $length, $line) {
		echo "Process: \"".$string."\" (Length: ".$length.") @Line: ".$line."\n";
	}
}

$s = new Test();

$s->sentence(file_get_contents("input.txt"));

?>
