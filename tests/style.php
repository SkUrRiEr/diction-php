<?php

require_once("../Style.php");
require_once("../LangEN.php");

$cls = new Style(new LangEN());

$filename = "input.txt";

if( $_SERVER["argc"] > 1 )
	$filename = $_SERVER["argv"][1];

$cls->processString(file_get_contents($filename));

echo "characters: ".$cls->characters."\n";
echo "syllables: ".$cls->syllables."\n";
echo "words: ".$cls->words."\n";
echo "shortwords: ".$cls->shortwords."\n";
echo "longwords: ".$cls->longwords."\n";
echo "bigwords: ".$cls->bigwords."\n";
echo "sentences: ".$cls->sentences."\n";
echo "questions: ".$cls->questions."\n";
echo "passiveSent: ".$cls->passiveSent."\n";
echo "beginArticles: ".$cls->beginArticles."\n";
echo "beginPronouns: ".$cls->beginPronouns."\n";
echo "pronouns: ".$cls->pronouns."\n";
echo "beginInterrogativePronouns: ".$cls->beginInterrogativePronouns."\n";
echo "interrogativePronouns: ".$cls->interrogativePronouns."\n";
echo "beginConjunctions: ".$cls->beginConjunctions."\n";
echo "conjunctions: ".$cls->conjunctions."\n";
echo "nominalizations: ".$cls->nominalizations."\n";
echo "prepositions: ".$cls->prepositions."\n";
echo "beginPrepositions: ".$cls->beginPrepositions."\n";
echo "beginSubConjunctions: ".$cls->beginSubConjunctions."\n";
echo "subConjunctions: ".$cls->subConjunctions."\n";
echo "auxVerbs: ".$cls->auxVerbs."\n";
echo "tobeVerbs: ".$cls->tobeVerbs."\n";
echo "shortestLine: ".$cls->shortestLine."\n";
echo "shortestLength: ".$cls->shortestLength."\n";
echo "longestLine: ".$cls->longestLine."\n";
echo "longestLength: ".$cls->longestLength."\n";
echo "paragraphs: ".$cls->paragraphs."\n";

echo "\nSentence Length : Count\n";

foreach($cls->lengths as $i => $item)
	if( $item != 0 )
		echo $i." : ".$item."\n";

?>
