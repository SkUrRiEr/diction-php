<?php

class Style {
	static $abbreviations;

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

	function kincaid() {
		return 11.8 * ($this->syllables / $this->words) + 0.39 * ($this->words / $this->sentences) - 15.59;
	}

	function ari() {
		return 4.71 * ($this->characters / $this->words) + 0.5 * ($this->words / $this->sentences) - 21.43;
	}

	function coleman_liau() {
		return 5.879851 * ($this->characters / $this->words) - 29.587280 * ($this->sentences / $this->words) - 15.800804;
	}

	function flesch() {
		return 206.835 - 84.6 * ($this->syllables / $this->words) - 1.015 * ($this->words / $this->sentences);
	}

	function fog() {
		return 0.4 * ($this->words / $this->sentences + 100.0 * $this->bigwords / $this->words);
	}

	function wstf() {
		return 0.1935 * ($this->bigwords / $this->words) + 0.1672 * ($this->words / $this->sentences) - 0.1297 * ($this->longwords / $this->words)  - 0.0327 * ($this->shortwords / $this->words) - 0.875;
	}

	function wheeler_smith() {
		$idx = ($this->words / $this->sentences) * 10.0 * ($this->bigwords / $this->words);

		$grade = 99;

		if ($idx <= 16)
		       	$grade = 0;
		else if ($idx <= 20)
		       	$grade = 5;
		else if ($idx <= 24)
		       	$grade = 6;
		else if ($idx <= 29)
		       	$grade = 7;
		else if ($idx <= 34)
		       	$grade = 8;
		else if ($idx <= 38)
		       	$grade = 9;
		else if ($idx <= 42)
		       	$grade = 10;

		return array("_value" => $idx, "grade" => $grade);
	}

	function lix() {
		$idx = ($this->words / $this->sentences) + 100.0 * ($this->longwords / $this->words);

		$grade = 99;

		if ($idx < 34)
		       	$grade = 0;
		else if ($idx < 38)
		       	$grade = 5;
		else if ($idx < 41)
		       	$grade = 6;
		else if ($idx < 44)
		       	$grade = 7;
		else if ($idx < 48)
		       	$grade = 8;
		else if ($idx < 51)
		       	$grade = 9;
		else if ($idx < 54)
		       	$grade = 10;
		else if ($idx < 57)
		       	$grade = 11;
	
		return array("_value" => $idx, "grade" => $grade);
	}

	function smog() {
		return sqrt(30.0 * ($this->bigwords / $this->sentences)) + 3.0;
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

	function process($string, $length, $line) {
		echo "Process: \"".$string."\" (Length: ".$length.") @Line: ".$line."\n";
	}
}

return;

?>

	function wordcmp($r, $s) {
		for( $i = 0; $i < min(strlen($r), strlen($s)); $i++ )
			if( ($res = $r[$i] - strtolower($s[$])) != 0 )
				return $res;

		return strlen($s) >= $i && ctype_alpha($s[$i]);
	}

	/**
	 * Test if the word is an article.  This function uses docLanguage to
	 * determine the used language.
	 */
	function article($word, $l) {
		$list = array("the", "a", "an");

		foreach($list as $item)
			if( $this->wordcmp($item, $word) == 0 )
				return true;
		
		return false;
	}

	/**
	 * Test if the word is a pronoun.  This function uses docLanguage to
	 * determine the used language.
	 */
	function pronoun($word, $l) {
		$list = array("i", "me", "we", "us", "you", "he", "him", "she", "her", "it", "they", "them", "thou", "thee", "ye", "myself", "yourself", "himself", "herself", "itself", "ourselves", "yourselves", "themselves", "oneself", "my", "mine", "his", "hers", "yours", "ours", "theirs", "its", "our", "that", "their", "these", "this", "those", "your");

		foreach($list as $item)
			if( $this->wordcmp($item, $word) == 0 )
				return true;
		
		return false;
	}

	/**
	 * Test if the word is an interrogative pronoun.  This function uses
	 * docLanguage to determine the used language.
	 */
	function interrogativePronoun($word, $l) {
		$list = array("why", "who", "what", "whom", "when", "where", "how");

		foreach($list as $item)
			if( $this->wordcmp($item, $word) == 0 )
				return true;
		
		return false;
	}

	/**
	 * Test if the word is an conjunction.  This function uses
	 * docLanguage to determine the used language.
	 */
	function conjunction($word, $l) {
		$list = array("and", "but", "or", "yet", "nor");

		foreach($list as $item)
			if( $this->wordcmp($item, $word) == 0 )
				return true;
		
		return false;
	}

/**
 * Test if the word is a nominalization.  This function uses
 * docLanguage to determine the used language.
 */
static int nominalization(const char *word, size_t l) /*{{{*/
{
  static const char *en[]= /* nominalization suffixes */ /*{{{*/
  {
     /* a bit limited, but it is exactly what the original style(1) did */
     "tion", "ment", "ence", "ance", (const char*)0
  };
  /*}}}*/

  const char **list;

  /* exclude words too short to have such long suffixes */
  if (l < 7) return 0;

  list=en;

  while (*list) if (wordcmp(*list,word+l-strlen(*list))==0) return 1; else ++list;
  return 0;
}
/*}}}*/

/**
 * Test if the word is an sub conjunction.  This function uses
 * docLanguage to determine the used language.
 */
 
static int subConjunction(const char *word, size_t l) /*{{{*/
{
  static const char *en[]= /* subordinating conjunctions */ /*{{{*/
  {
    "after", "because", "lest", "till", "'til", "although", "before", 
    "now that", "unless", "as", "even if", "provided that", "provided", 
    "until", "as if", "even though", "since", "as long as", "so that",
    "whenever", "as much as", "if", "than", "as soon as", "inasmuch",
    "in order that", "though", "while", (const char*)0
  };
  /*}}}*/

  const char **list;

  list=en;

  while (*list)
  {
    if (wordcmp(*list,word)==0) 
    {
      phraseEnd = word+strlen(*list);
      return 1; 
    }
    else ++list;
  }
  return 0;
}
/*}}}*/

/**
 * Test if the word is an preposition.  This function uses
 * docLanguage to determine the used language.
 */
 
static int preposition(const char *word, size_t l) /*{{{*/
{
  static const char *en[]= /* prepositions */ /*{{{*/
  {
    "aboard", "about", "above", "according to", "across from",
    "after", "against", "alongside", "alongside of", "along with",
    "amid", "among", "apart from", "around", "aside from", "at", "away from",
    "back of", "because of", "before", "behind", "below", "beneath", "beside",
    "besides", "between", "beyond", "but", "by means of",
    "concerning", "considering", "despite", "down", "down from", "during",
    "except", "except for", "excepting for", "from among",
    "from between", "from under", "in addition to", "in behalf of",
    "in front of", "in place of", "in regard to", "inside of", "inside",
    "in spite of", "instead of", "into", "like", "near to", "off",
    "on account of", "on behalf of", "onto", "on top of", "on", "opposite",
    "out of", "out", "outside", "outside of", "over to", "over", "owing to",
    "past", "prior to", "regarding", "round about", "round",
    "since", "subsequent to", "together", "with", "throughout", "through",
    "till", "toward", "under", "underneath", "until", "unto", "up",
    "up to", "upon", "with", "within", "without", "across", "along",
    "by", "of", "in", "to", "near", "of", "from",  (const char*)0
  };
  /*}}}*/

  const char **list;

  list=en;

  while (*list)
  {
    if (wordcmp(*list,word)==0)
    {
      phraseEnd = word+strlen(*list);
      return 1; 
    }
    else ++list;
  }
  return 0;
}
/*}}}*/

/**
 * Test if the word is an auxiliary verb.  This function uses
 * docLanguage to determine the used language.
 */
 
static int auxVerb(const char *word, size_t l) /*{{{*/
{
  static const char *en[]= /* auxiliary verbs */ /*{{{*/
  {
    "will", "shall", "cannot", "may", "need to", "would", "should",
    "could", "might", "must", "ought", "ought to", "can't", "can",
    (const char*)0
  };
  /*}}}*/

  const char **list;

  list=en;

  while (*list)
  {
    if (wordcmp(*list,word)==0) 
    {
      phraseEnd = word+strlen(*list);
      return 1; 
    }
    else ++list;
  }
  return 0;
}
/*}}}*/

	/**
	 * Test if the word is an 'to be' verb.  This function uses
	 * docLanguage to determine the used language.
	 */
	function tobeVerb($word, $l) {
		$list = array("be", "being", "was", "were", "been", "are", "is");

		foreach($list as $item)
			if( $this->wordcmp($item, $word) == 0 )
				return true;
		
		return false;
	}

	function vowel($c) {
		return ($c=='a' || $c=='ä' || $c=='e' || $c=='i' || $c=='o' || $c=='ö' || $c=='u' || $c=='ü' ||	$c=='ë' || $c=='é' || $c=='è' || $c=='à' || $c=='i' || $c=='ï' || $c=='y');
	}

	function syllables($s, $l) {
		$count = 0;

		if( $l >= 2 && substr($s, $l - 2, 2) == "ed" )
			$l -= 2;

		for( $i = 0; $l > 0; $i++, $l-- )
			if ($l >= 2 && $this->vowel($s[$i]) && !$this->vowel($s[$i + 1])) {
				$count++;
				$s++;
				$l--;
			}

		if( $count == 0 )
			return 1;

		return $count;
	}


static struct Hit lengths;

/* hit counting functions */ /*{{{*/
struct Hit /*{{{*/
{
  int *data;
  int capacity;
  int size;
};
/*}}}*/
static void newHit(struct Hit *hit) /*{{{*/
{
  if ((hit->data=malloc((hit->capacity=3)*sizeof(int)))==(int*)0)
  {
    fprintf(stderr,_("style: out of memory\n"));
    exit(1);
  }
  memset(hit->data,0,hit->capacity*sizeof(int));
  hit->size=0;
}
/*}}}*/
static void noteHit(struct Hit *hit, int n) /*{{{*/
{
  assert(n>0);
  if (n>hit->capacity)
  {
    if ((hit->data=realloc(hit->data,n*2*sizeof(int)))==(int*)0)
    {
      fprintf(stderr,_("style: out of memory\n"));
      exit(1);
    }
    memset(hit->data+hit->capacity,0,(n*2-hit->capacity)*sizeof(int));
    hit->capacity=n*2;
  }
  ++hit->data[n-1];
  if (n>hit->size) hit->size=n;
}
/*}}}*/
/*}}}*/
/**
 * Process one sentence.
 * @param str sentence
 * @param length its length
 */
static void process(const char *str, size_t length, int line) /*{{{*/
{
  int firstWord=1;
  int inword=0;
  int innumber=0;
  int wordLength=-1;
  int sentWords=0;
  int sentLetters=0;
  int count;
  int passive=0;
  int nom=0;
  const char *s=str,*end=s+length;

  if (length==0) { ++paragraphs; return; }
  assert(str!=(const char*)0);
  assert(length>=2);
  phraseEnd = (const char*)0;
  while (s<end)
  {
    if (inword)
    {
      if (!ctype_alpha(*s) && *s!='-' && !endingInPossesiveS(str,s-str+2))
      {
        inword=0;
        count=syllables(s-wordLength,wordLength);
        syllables+=count;
        if (count>=3) ++bigwords;
        else if (count==1) ++shortwords;
        if (wordLength>6) ++longwords;
        if (s-wordLength > phraseEnd)
        {
          /* part of speech tagging-- order matters! */
          if (article(s-wordLength,wordLength) && firstWord) ++beginArticles;
          else if (pronoun(s-wordLength,wordLength))
          {
            ++pronouns;
            if (firstWord) ++beginPronouns;
          }
          else if (interrogativePronoun(s-wordLength,wordLength))
          { 
            ++interrogativePronouns;
            if (firstWord) ++beginInterrogativePronouns;
          }
          else if (conjunction(s-wordLength,wordLength)) 
          { 
            ++conjunctions;
            if (firstWord) ++beginConjunctions;
          }
          else if (subConjunction(s-wordLength,wordLength)) 
          { 
            ++subConjunctions;
            if (firstWord) ++beginSubConjunctions;
          }
          else if (preposition(s-wordLength,wordLength)) 
          { 
            ++prepositions;
            if (firstWord) ++beginPrepositions;
          }
          else if (tobeVerb(s-wordLength,wordLength))
          { 
            ++passive;
            ++tobeVerbs;
          }
          else if (auxVerb(s-wordLength,wordLength)) ++auxVerbs;
          else if (nominalization(s-wordLength,wordLength))
          { 
            ++nom;
            ++nominalizations;
          }
        }
        if (firstWord) firstWord = 0;
      }
      else
      {
        ++wordLength;
        ++characters;
        ++sentLetters;
      }
    }
    else if (innumber)
    {
      if (ctype_digit(*s) || ((*s=='.' || *s==',') && ctype_digit(*(s+1))))
      {
        ++wordLength;
        ++characters;
        ++sentLetters;
      }
      else
      {
        innumber=0;
        ++syllables;
      }
    }
    else
    {
      if (ctype_alpha(*s))
      {
        ++words;
        ++sentWords;
        inword=1;
        wordLength=1;
        ++characters;
        ++sentLetters;
      }
      else if (ctype_digit(*s))
      {
        ++words;
        ++sentWords;
        innumber=1;
        wordLength=1;
        ++characters;
        ++sentLetters;
      }
    }
    ++s;
  }
  ++sentences;
  if (shortestLine==0 || sentWords<shortestLength)
  {
    shortestLine=sentences;
    shortestLength=sentWords;
  }
  if (longestLine==0 || sentWords>longestLength)
  {
    longestLine=sentences;
    longestLength=sentWords;
  }
  if (str[length-1]=='?') ++questions;
  noteHit(&lengths,sentWords);
  if (passive) ++passiveSent;
}
/*}}}*/

int main(int argc, char *argv[]) /*{{{*/
{
  newHit(&lengths);

  sentence(stdin,"(stdin)");

  if (sentences==0)
  {
    printf(_("No sentences found.\n"));
  }
  else
  {
    double fl;
    int wsg;
    int lixg;
    int i,shortLength,shortSent,longLength,longSent;

    printf(_("readability grades:\n"));
    printf("        %s: %.1f\n","Kincaid",kincaid(syllables,words,sentences));
    printf("        %s: %.1f\n","ARI",ari(characters,words,sentences));
    printf("        %s: %.1f\n","Coleman-Liau",coleman_liau(characters,words,sentences));
    fl=flesch(syllables,words,sentences);
    printf("        %s: %.1f%s\n","Flesch Index",fl,fl>=60 && fl<=70 ? _("/100 (plain English)") : _("/100"));
    printf("        %s: %.1f\n","Fog Index",fog(words,bigwords,sentences));
    printf("        %s: %.1f\n","1. WSTF Index",wstf(words,shortwords,longwords,bigwords,sentences));
    printf("        %s: %.1f = ","Wheeler-Smith Index",wheeler_smith(&wsg,words,bigwords,sentences));
    if (wsg==0) printf(_("below school year 5\n"));
    else if (wsg==99) printf(_("higher than school year 10\n"));
    else printf(_("school year %d\n"),wsg);
    printf("        %s: %.1f = ",_("Lix"),lix(&lixg,words,longwords,sentences));
    if (lixg==0) printf(_("below school year 5\n"));
    else if (lixg==99) printf(_("higher than school year 11\n"));
    else printf(_("school year %d\n"),lixg);
    printf("        %s: %.1f\n",_("SMOG-Grading"),smog(bigwords,sentences));

    printf(_("sentence info:\n"));
    printf(_("        %d characters\n"),characters);
    printf(_("        %d words, average length %.2f characters = %.2f syllables\n"),words,((double)characters)/words,((double)syllables)/words);
    printf(_("        %d sentences, average length %.1f words\n"),sentences,((double)words)/sentences);
    shortLength=((double)words)/sentences-4.5;
    if (shortLength<1) shortLength=1;
    for (i=0,shortSent=0; i<=shortLength; ++i) shortSent+=lengths.data[i];
    printf(_("        %d%% (%d) short sentences (at most %d words)\n"),100*shortSent/sentences,shortSent,shortLength);
    longLength=((double)words)/sentences+10.5;
    for (i=longLength,longSent=0; i<=lengths.size; ++i) longSent+=lengths.data[i];
    printf(_("        %d%% (%d) long sentences (at least %d words)\n"),100*longSent/sentences,longSent,longLength);
    printf(_("        %d paragraphs, average length %.1f sentences\n"),paragraphs,((double)sentences)/paragraphs);
    printf(_("        %d%% (%d) questions\n"),100*questions/sentences,questions);
    printf(_("        %d%% (%d) passive sentences\n"),100*passiveSent/sentences,passiveSent);
    printf(_("        longest sent %d wds at sent %d; shortest sent %d wds at sent %d\n"),longestLength,longestLine,shortestLength,shortestLine);

/*
Missing output:

sentence types:
        simple 100% (1) complex   0% (0)
        compound   0% (0) compound-complex   0% (0)
word usage:
        verb types as % of total verbs
        tobe 100% (1) aux   0% (0) inf   0% (0)
        passives as % of non-inf verbs   0% (0)
        types as % of total
        prep 0.0% (0) conj 0.0% (0) adv 0.0% (0)
        noun 25.0% (1) adj 25.0% (1) pron 25.0% (1)
        nominalizations   0 % (0)
*/
        printf(_("word usage:\n"));
        printf(_("        verb types:\n"));
        printf(_("        to be (%d) auxiliary (%d) \n"), tobeVerbs, auxVerbs);
        printf(_("        types as %% of total:\n"));
        printf(_("        conjunctions %1.f% (%d) pronouns %1.f% (%d) prepositions %1.f% (%d)\n"), 
                (100.0*(conjunctions+subConjunctions))/words, 
                conjunctions+subConjunctions, 
                (100.0*pronouns)/words, pronouns, (100.0*prepositions)/words,
                prepositions);
        printf(_("        nominalizations %1.f% (%d)\n"),
                (100.0*nominalizations)/words, nominalizations);

        printf(_("sentence beginnings:\n"));
        printf(_("        pronoun (%d) interrogative pronoun (%d) article (%d)\n"),beginPronouns,beginInterrogativePronouns,beginArticles);
        printf(_("        subordinating conjunction (%d) conjunction (%d) preposition (%d)\n"), beginSubConjunctions,beginConjunctions,beginPrepositions);

/*
        subject opener: noun (0) pron (1) pos (0) adj (0) art (0) tot 100%
        prep   0% (0) adv   0% (0)
        verb   0% (0)  sub_conj   0% (0) conj   0% (0)
        expletives   0% (0)
*/
  }
  exit(0);
}
/*}}}*/
