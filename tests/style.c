/* Notes */ /*{{{C}}}*//*{{{*/
/*

This program is GNU software, copyright 1997-2007
Michael Haardt <michael@moria.de>.

This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 3 of the License, or (at your
option) any later version.

This program is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
for more details.

You should have received a copy of the GNU General Public License along
with this program.  If not, write to the Free Software Foundation, Inc.,
59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.

*/
/*}}}*/

/* #includes */ /*{{{*/
#undef  _POSIX_SOURCE
#define _POSIX_SOURCE   1
#undef  _POSIX_C_SOURCE
#define _POSIX_C_SOURCE 2

#include <sys/types.h>
#include <assert.h>
#include <ctype.h>
#include <errno.h>
#include <limits.h>
#include <locale.h>
#ifdef HAVE_GETTEXT
#include <libintl.h>
#define _(String) gettext(String)
#else
#define _(String) String
#endif
#include <regex.h>
#include <math.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

/*}}}*/

/* variables */ /*{{{*/
static const char *docLanguage = "en";
static const char *phraseEnd = (const char*)0;
/*}}}*/

static const char *abbreviations[]= /*{{{*/
{
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
  (const char*)0
};
/*}}}*/

int endingInPossesiveS(const char *s, size_t length) /*{{{*/
{
  return (length>=3 && strncmp(s+length-2,"\'s",2)==0);
}
/*}}}*/
static int endingInAbbrev(const char *s, size_t length, const char *lang) /*{{{*/
{
  const char **abbrev=abbreviations;
  size_t aLength;

  if (!isalpha(s[length-1])) return 0;
  if (endingInPossesiveS(s,length)) return 0;
  while (*abbrev!=(const char*)0)
  {
    if ((aLength=strlen(*abbrev))<length)
    {
      if (!isalpha(s[length-2])) return 1;
      if (!isalpha(s[length-aLength-1]) && strncmp(s+length-aLength,*abbrev,aLength)==0) return 1;
    }
    else
    {
      if (length==1) return 1;
      if (aLength==length && strncmp(s,*abbrev,aLength)==0) return 1;
    }      
    ++abbrev;
  }
  return 0;
}
/*}}}*/

void sentence(const char *cmd, FILE *in, const char *file, void (*process)(const char *, size_t, const char *, int), const char *lang) /*{{{*/
{
  /* variables */ /*{{{*/
  int voc,oc,c;
  char *sent=malloc(128);
  size_t length=0,capacity=128;
  int inSentence=0;
  int inWhiteSpace=0;
  int inParagraph=0;
  int line=1,beginLine=1;
  int err;
  regex_t hashLine;
  char filebuf[_POSIX_PATH_MAX+1];
  /*}}}*/

  /* compile #line number "file" regular expression */ /*{{{*/
  if ((err=regcomp(&hashLine,"^[ \t]*line[ \t]*\\([0-9][0-9]*\\)[ \t]*\"\\([^\"]*\\)\"",0)))
  {
    char buf[256];
    size_t len=regerror(err,&hashLine,buf,sizeof(buf)-1);
    buf[len]='\0';
    fprintf(stderr,_("%s: internal error, compiling a regular expression failed (%s).\n"),cmd,buf);
    exit(2);
  }
  /*}}}*/
  voc='\n';
  c=getc(in);
  while ((oc=c)!=EOF)
  {
    c=getc(in);
    if (oc=='\n') ++line;
    if (voc=='\n' && oc=='#') /* process cpp style #line, continue */ /*{{{*/
    {
      char buf[_POSIX_PATH_MAX+20];
      regmatch_t found[3];

      buf[0]=c; buf[1]='\0';
      (void)fgets(buf+1,sizeof(buf)-1,in);
      if (regexec(&hashLine,buf,3,found,0)==0) /* #line */ /*{{{*/
      {
        size_t len;

        line=strtol(buf+found[1].rm_so,(char**)0,10)-1;
        len=found[2].rm_eo-found[2].rm_so;
        if (len>_POSIX_PATH_MAX) len=_POSIX_PATH_MAX;
        strncpy(filebuf,buf+found[2].rm_so,len);
        filebuf[len]='\0';
        file=filebuf;
      }
      /*}}}*/
      c='\n';
      continue;
    }
    /*}}}*/
    if (length)
    {
      if (length>=(capacity-1) && (sent=realloc(sent,capacity*=2))==(char*)0)
      {
        fprintf(stderr,_("%s: increasing sentence buffer failed: %s\n"),cmd,strerror(errno));
        exit(2);
      }
      if (isspace(oc))
      {
        if (!inWhiteSpace)
        {
          sent[length++]=' ';
          inWhiteSpace=1;
        }
      }
      else
      {
        sent[length++]=oc;
        if (isalpha(oc)) inSentence=1;
        if
        (
          (length==3 && strncmp(sent+length-3,"...",3)==0 && (c==EOF || isspace(c)))
          || (length>=4 && strncmp(sent+length-4," ...",4)==0 && (c==EOF || isspace(c)))
        )
        {
          /* omission ellipsis */
          inWhiteSpace=0;
        }
        else if (length>=4 && !isspace(sent[length-4]) && strncmp(sent+length-3,"...",3)==0 && (c==EOF || isspace(c)))
        {
          /* beginning ellipsis */
          char foo;

          foo=sent[length-3];
          sent[length-3]='\0';
          if (inSentence) process(sent,length-3,file,beginLine);
          sent[length-3]=foo;
          memmove(sent,sent+length-3,3);
          length=3;
          inParagraph=0;
          inWhiteSpace=0;
          beginLine=line;
          inSentence=0;
        }
        else if (length>=4 && strncmp(sent+length-4,"...",3)==0 && (c==EOF || isspace(c)))
        {
          /* ending ellipsis */
          if (inWhiteSpace) --length;
          sent[length]='\0';
          if (inSentence) process(sent,length,file,beginLine);
          length=0;
          inWhiteSpace=0;
          inSentence=0;
        }
        else if ((oc=='.' || oc==':' || oc=='!' || oc=='?') && (c==EOF || isspace(c) || c=='"') && (!isdigit(voc) || oc!='.' || !isdigit(c)) && (oc!='.' || !endingInAbbrev(sent,length,lang)))
        {
          /* end of sentence */
          if (inWhiteSpace) --length;
          sent[length]='\0';
          if (inSentence) process(sent,length,file,beginLine);
          length=0;
          inWhiteSpace=0;
          inSentence=0;
        }
        else
        {
          /* just a regular character */
          inWhiteSpace=0;
        }
      }
    }
    else if (isupper(oc))
    {
      inParagraph=0;
      sent[length++]=oc;
      inWhiteSpace=0;
      beginLine=line;
      inSentence=1;
    }
    else if (!inParagraph && oc=='\n' && c=='\n')
    {
      process("",0,file,line);
      inParagraph=1;
    }
    voc=oc;
  }
  if (!inParagraph) process("",0,file,line);
  regfree(&hashLine);
}
/*}}}*/
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
/* word class checks */ /*{{{*/
static int wordcmp(const char *r, const char *s) /*{{{*/
{
  int res;

  while (*r)
  {
    if ((res=*r-tolower(*s))!=0) return res;
    ++r; ++s;
  }
  return isalpha(*s);
}
/*}}}*/

/**
 * Test if the word is an article.  This function uses docLanguage to
 * determine the used language.
 */
static int article(const char *word, size_t l) /*{{{*/
{
  static const char *de[]= /* German articles */ /*{{{*/
  {
    "der", "die", "das", "des", "dem", "den", "ein", "eine", "einer",
    "eines", "einem", "einen", (const char*)0
  };
  /*}}}*/
  static const char *en[]= /* English articles */ /*{{{*/
  {
    "the", "a", "an", (const char*)0
  };
  /*}}}*/
  static const char *nl[]= /* Dutch articles (lidwoord) */ /*{{{*/
  {
    "de", "het", "een", (const char*)0
  };
  /*}}}*/
  const char **list;

  if (strncmp(docLanguage,"de",2)==0) list=de;
  else if (strncmp(docLanguage,"en",2)==0) list=en;
  else if (strncmp(docLanguage,"nl",2)==0) list=nl;
  else assert(0);

  while (*list) if (wordcmp(*list,word)==0) return 1; else ++list;
  return 0;
}
/*}}}*/

/**
 * Test if the word is a pronoun.  This function uses docLanguage to
 * determine the used language.
 */
static int pronoun(const char *word, size_t l) /*{{{*/
{
  static const char *de[]= /* Pronomen */ /*{{{*/
  {
    /* Nominativ */ "ich", "du", "er", "sie", "es", "wir", "ihr", /* sie */
    /* Akkusativ */ "mich", "dich", "ihn", "uns", "euch", /* sie */
    /* Dativ     */ "mir", "dir", "ihm", /* uns euch ihr */ "ihnen",
    /* Genitiv   */ "mein", "dein", "sein", "unser", "euer", /* ihr */
    /* Genitiv   */ "meiner", "deiner", "seiner", "unserer", "eurer", "ihrer",
    /* Genitiv   */ "meine", "deine", "seine", "unsere", "eure", "ihre",
    /* Genitiv   */ "meines", "deines", "seines", "unseres", "eures", "ihres",
    /* Genitiv   */ "meinem", "deinem", "seinem", "unserem", "eurem", "ihrem",
    /* Genitiv   */ "meinen", "deinen", "seinen", "unseren", "euren", "ihren",
    (const char*)0
  };
  /*}}}*/
  static const char *en[]= /* pronouns */ /*{{{*/
  {
    "i", "me", "we", "us", "you", "he", "him", "she", "her", "it", "they",
    "them", "thou", "thee", "ye", "myself", "yourself", "himself",
    "herself", "itself", "ourselves", "yourselves", "themselves",
    "oneself", "my", "mine", "his", "hers", "yours", "ours", "theirs", "its",
    "our", "that", "their", "these", "this", "those", "your", (const char*)0
  };
  /*}}}*/
  static const char *nl[]= /* Dutch pronouns (voornaamwoord) */ /*{{{*/
  {
    "ik", 		 	"jij", "je", "u", "gij", "ge", 	"hij", "zij", "ze", "het",			/* persoonlijk voornaamwoord */
	"wij", "we", 	"jullie", 						/* "zij", "ze", */
    "me", "mijzelf", "mezelf", "je", "jezelf", "uzelf", 					/* wederkeren voornaamwoord */
	"zich", "zichzelf", "haarzelf", "onszelf", /* "jezelf", */
	"elkaar", "elkaars", "elkander", "elkanders", "mekaar", "mekaars",		/* wedekerig voornamwoord */
    "mijnen",  "deinen", "zijnen",   "haren",    "onzen",   "uwen", "hunnen", "haren",	/* pers. vnw: obsolete naamvallen */
    "mijner",  "deiner", "zijner",   "harer",    "onzer",   "uwer", "hunner", "harer",
    "mijnes",  "deines", "zijnes",   "hares",    "onzes",   "uwes", "hunnes", "hares", (const char*)0
  };
  /*}}}*/

  const char **list;

  if (strncmp(docLanguage,"de",2)==0) list=de;
  else if (strncmp(docLanguage,"en",2)==0) list=en;
  else if (strncmp(docLanguage,"nl",2)==0) list=nl;
  else assert(0);

  while (*list) if (wordcmp(*list,word)==0) return 1; else ++list;
  return 0;
}
/*}}}*/

/**
 * Test if the word is an interrogative pronoun.  This function uses
 * docLanguage to determine the used language.
 */
 
static int interrogativePronoun(const char *word, size_t l) /*{{{*/
{
  static const char *de[]= /* Interrogativpronomen */ /*{{{*/
  {
    "wer", "was", "wem", "wen", "wessen", "wo", "wie", "warum", "weshalb",
    "wann", "wieso", "weswegen", (const char*)0
  };
  /*}}}*/
  static const char *en[]= /* interrogative pronouns */ /*{{{*/
  {
    "why", "who", "what", "whom", "when", "where", "how", (const char*)0
  };
  /*}}}*/
  static const char *nl[]= /* Dutch interrogative pronouns (vragend voornaamwoord) */ /*{{{*/
  {
    "welke", "wat", "wat voor", "wat voor een", "welk", 
    "wie", "waar", "wanneer", "hoe", (const char*)0
  };
  /*}}}*/  
  
  const char **list;
 
  if (strncmp(docLanguage,"de",2)==0) list=de;
  else if (strncmp(docLanguage,"en",2)==0) list=en;
  else if (strncmp(docLanguage,"nl",2)==0) list=nl;
  else assert(0);

  while (*list) if (wordcmp(*list,word)==0) return 1; else ++list;
  return 0;
}
/*}}}*/

/**
 * Test if the word is an conjunction.  This function uses
 * docLanguage to determine the used language.
 */
 
static int conjunction(const char *word, size_t l) /*{{{*/
{
  static const char *de[]= /* Konjunktionen */ /*{{{*/
  {
    "und", "oder", "aber", "sondern", "doch", "nur", "bloﬂ", "denn",
    "weder", "noch", "sowie", (const char*)0
  };
  /*}}}*/
  static const char *en[]= /* conjunctions */ /*{{{*/
  {
    "and", "but", "or", "yet", "nor", (const char*)0
  };
  /*}}}*/
  static const char *nl[]= /* Dutch conjunctions (nevenschikkend voegwoord) */ /*{{{*/
  {
    "en", "maar", "of", "want", "dus", (const char*)0
  };
  /*}}}*/

  const char **list;

  if (strncmp(docLanguage,"de",2)==0) list=de;
  else if (strncmp(docLanguage,"en",2)==0) list=en;
  else if (strncmp(docLanguage,"nl",2)==0) list=nl;
  else assert(0);

  while (*list) if (wordcmp(*list,word)==0) return 1; else ++list;
  return 0;
}
/*}}}*/

/**
 * Test if the word is a nominalization.  This function uses
 * docLanguage to determine the used language.
 */
 
static int nominalization(const char *word, size_t l) /*{{{*/
{
  static const char *de[]= /* Nominalisierungsendungen */ /*{{{*/
  {
    "ung", "heit", "keit", "nis", "tum", (const char*)0
  };
  /*}}}*/
  static const char *en[]= /* nominalization suffixes */ /*{{{*/
  {
     /* a bit limited, but it is exactly what the original style(1) did */
     "tion", "ment", "ence", "ance", (const char*)0
  };
  /*}}}*/
  static const char *nl[]= /* Dutch nominalization suffixes (verzelfstandigde werkwoorden */ /*{{{*/
  {
     /* vereenvoudigd */
     "tie", "heid", "ing", "end", "ende", (const char*)0
  };
  /*}}}*/

  const char **list;

  /* exclude words too short to have such long suffixes */
  if (l < 7) return 0;

  if (strncmp(docLanguage,"de",2)==0) list=de;
  else if (strncmp(docLanguage,"en",2)==0) list=en;
  else if (strncmp(docLanguage,"nl",2)==0) list=nl;
  else assert(0);

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
  static const char *de[]= /* unterordnende Konjunktionen */ /*{{{*/
  {
    /* bei Nebens‰tzen */
    "als", "als dass", "als daﬂ", "als ob", "anstatt dass", "anstatt daﬂ",
    "ausser dass", "ausser daﬂ", "ausser wenn", "bevor", "bis", "da", "damit",
    "dass", "daﬂ", "ehe", "falls", "indem", "je", "nachdem", "ob", "obgleich",
    "obschon", "obwohl", "ohne dass", "ohne daﬂ", "seit", "so daﬂ", "sodass",
    "sobald", "sofern", "solange", "so oft", "statt dass", "statt daﬂ",
    "w‰hrend", "weil", "wenn", "wenn auch", "wenngleich", "wie", "wie wenn",
    "wiewohl", "wobei", "wohingegen", "zumal"
    /* bei Infinitivgruppen */
    "als zu", "anstatt zu", "ausser zu", "ohne zu", "statt zu", "um zu",
    (const char*)0
  };
  /*}}}*/
  static const char *en[]= /* subordinating conjunctions */ /*{{{*/
  {
    "after", "because", "lest", "till", "'til", "although", "before", 
    "now that", "unless", "as", "even if", "provided that", "provided", 
    "until", "as if", "even though", "since", "as long as", "so that",
    "whenever", "as much as", "if", "than", "as soon as", "inasmuch",
    "in order that", "though", "while", (const char*)0
  };
  /*}}}*/
  static const char *nl[]= /* Dutch subordinating conjunctions (onderschikeknde voegwoorden */ /*{{{*/
  {
	/* onderschikkende voegwoorden */
    "aangezien", "als", "alsof", "behalve", "daar", "daarom", "dat", 
	"derhalve", "doch", "doordat", "hoewel", "mits", "nadat", 
    "noch", "ofschoon", "omdat", "ondanks", "opdat", "sedert", "sinds",
	"tenzij", "terwijl", "toen", "totdat", "voordat", "wanneer",
	"zoals", "zodat", "zodra", "zonder dat", 
	
	/* infitief constructies */
	"om te", (const char*)0
  };
  /*}}}*/

  const char **list;

  if (strncmp(docLanguage,"de",2)==0) list=de;
  else if (strncmp(docLanguage,"en",2)==0) list=en;
  else if (strncmp(docLanguage,"nl",2)==0) list=nl;
  else assert(0);

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
  static const char *de[]= /* Pr‰positionen */ /*{{{*/
  {
    "aus", "auﬂer", "bei", "mit", "nach", "seit", "von", "zu",
    "bis", "durch", "f¸r", "gegen", "ohne", "um", "an", "auf",
    "hinter", "in", "neben", "¸ber", "unter", "vor", "zwischen",
    "anstatt", "statt", "trotz", "w‰hrend", "wegen", (const char*)0
  };
  /*}}}*/
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
  static const char *nl[]= /* Dutch prepositions (voorzetsels) */ /*{{{*/
  {
    "‡", "aan", "ad", "achter", "behalve", "beneden", "betreffende", "bij", "binnen", "blijkens", "boven", "buiten", 
	"circa", "conform", "contra", "cum", "dankzij", "door", "gedurende", "gezien", "hangende", "in", "ingevolge", 
	"inzake", "jegens", "krachtens", "langs", "met", "middels", "mits", "na", "naar", "naast", "nabij", "namens", 
	"niettegenstaande", "nopens", "om", "omstreeks", "omtrent", "ondanks", "onder", "ongeacht", "onverminderd", 
	"op", "over", "overeenkomstig", "per", "plus", "richting", "rond", "rondom", "sedert", "staande", "te", "tegen", 
	"tegenover", "ten", "ter", "tijdens", "tot", "tussen", "uit", "uitgezonderd", "van", "vanaf", "vanuit", "vanwege", 
	"versus", "via", "volgens", "voor", "voorbij", "wegens", "zonder",  (const char*)0
  };
  /*}}}*/

  const char **list;

  if (strncmp(docLanguage,"de",2)==0) list=de;
  else if (strncmp(docLanguage,"en",2)==0) list=en;
  else if (strncmp(docLanguage,"nl",2)==0) list=nl;
  else assert(0);

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
  static const char *de[]= /* Hilfsverben */ /*{{{*/
  {
    "haben", "habe", "hast", "hat", "habt", "gehabt", "h‰tte", "h‰ttest",
    "h‰tten", "h‰ttet",
    "werden", "werde", "wirst", "wird", "werdet", "geworden", "w¸rde",
    "w¸rdest", "w¸rden", "w¸rdet",
    "kˆnnen", "kann", "kannst", "kˆnnt", "konnte", "konntest", "konnten",
    "konntet", "gekonnt", "kˆnnte", "kˆnntest", "kˆnnten", "kˆnntet",
    "m¸ssen", "muss", "muﬂ", "musst", "m¸sst", "musste", "musstest", "mussten",
    "gemusst", "m¸sste", "m¸sstest", "m¸ssten", "m¸sstet",
    "sollen", "soll", "sollst", "sollt", "sollte", "solltest", "solltet",
    "sollten", "gesollt",
    (const char*)0
  };
  /*}}}*/
  static const char *en[]= /* auxiliary verbs */ /*{{{*/
  {
    "will", "shall", "cannot", "may", "need to", "would", "should",
    "could", "might", "must", "ought", "ought to", "can't", "can",
    (const char*)0
  };
  /*}}}*/
  static const char *nl[]= /* Dutch auxiliary verbs (hulpwerkwoorden) */ /*{{{*/
  {
	/* in combinatie met voltooid deelwoord */
	/* hebben */	"heb",  "hebt", "heeft", "hebben", "had",    "hadden",  "gehad", 
	/* zijn */		/* "ben",  "bent", "is",    "zijn",   "was",    "waren",   "geweest", */
	/* worden */	"word", "wordt",         "worden", "werd",   "werden",  "geworden",

	/* in combinatie met infinitief */
	/* kunnen */	"kan", "kan",            "kunnen", "kon",    "konden",  "gekund",
	/*  willen */	"wil",                   "willen", "wilde",  "wilden",  "gewild", "wou", "wouden", 
	/* zullen */		"zal", "zult",           "zullen", "zou",    "zouden", 
	/* mogen */	"mag",                   "mogen",  "mocht",  "mochten", "gemogen",
	/*moeten */	"moet",                  "moeten", "moest",  "moesten", "gemoeten",
	/* hoeven  */	"hoef", "hoeft",         "hoeven", "hoefde", "hoefden", "gehoeven", 
	/* doen */		"doe", "doet",           "doen",   "deed",   "deden",   "gedaan",
	(const char*)0
  };
  /*}}}*/

  const char **list;

  if (strncmp(docLanguage,"de",2)==0) list=de;
  else if (strncmp(docLanguage,"en",2)==0) list=en;
  else if (strncmp(docLanguage,"nl",2)==0) list=nl;
  else assert(0);

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
 
static int tobeVerb(const char *word, size_t l) /*{{{*/
{
  static const char *de[]= /* Hilfsverb sein */ /*{{{*/
  {
    "sein", "bin", "bist", "ist", "sind", "seid", "war", "warst", "wart",
    "waren", "gewesen", "w‰re", "w‰rst", "w‰r", "w‰ren", "w‰rt", "w‰ret",
    (const char*)0
  };
  /*}}}*/
  static const char *en[]= /* auxiliary verb to be */ /*{{{*/
  {
     "be", "being", "was", "were", "been", "are", "is", (const char*)0
  };
  /*}}}*/
  static const char *nl[]= /* Dutch auxiliary verb to be (zijn) */ /*{{{*/
  {
  "ben", "bent", "is", "zijn", "was", "waren", "geweest",
  (const char*)0
  };
  /*}}}*/

  const char **list;

  if (strncmp(docLanguage,"de",2)==0) list=de;
  else if (strncmp(docLanguage,"en",2)==0) list=en;
  else if (strncmp(docLanguage,"nl",2)==0) list=nl;
  else assert(0);

  while (*list) if (wordcmp(*list,word)==0) return 1; else ++list;
  return 0;
}
/*}}}*/
/*}}}*/
/* syllable counting */ /*{{{*/
/**
 * Check if the character is pronounced as a vowel.
 */
static int vowel(char c) /*{{{*/
{
  if (c=='y' && strncmp(docLanguage,"en",2)) return 1;

    return (c=='a' || c=='‰' || c=='e' || c=='i' || c=='o' || c=='ˆ' || c=='u' || c=='¸' ||		/* JDL */
							 c=='Î' || c=='È' || c=='Ë' || c=='‡' || c=='i' || c=='Ô' || c=='y');
}
/*}}}*/

/**
 * Count syllables for english words by counting vowel-consonant pairs.
 * @param s the word
 * @param l the word's length
 */
static int syll_en(const char *s, size_t l) /*{{{*/
{
  int count=0;

  if (l>=2 && *(s+l-2)=='e' && *(s+l-1)=='d') l-=2;
  while (l)
  {
    if (l>=2 && vowel(*s) && !vowel(*(s+1))) { ++count; s+=2; l-=2; }
    else { ++s; --l; }
  }
  return (count==0 ? 1 : count);
}
/*}}}*/

/**
 * Count syllables for German words by counting vowel-consonant or
 * consonant-vowel pairs, depending on the first character being a vowel or
 * not.  If it is, a trailing e will be handled with a special rule.  This
 * algorithm fails on "vor-ueber".
 * @param s the word
 * @param l the word's length
 */
static int syll_de(const char *s, size_t l) /*{{{*/
{  
  int count=0;
  size_t ol=l;
 
  if (vowel(*s))  
  while (l) 
  {
    if (l>=2 && vowel(*s) && !vowel(*(s+1))) { ++count; s+=2; l-=2; }
    else if (l==1 && ol>1 && !vowel(*(s-1)) && *s=='e') { ++count; s+=1; l-=1; }
    else { ++s; --l; }
  }
  else
  while (l)
  {
    if (l>=2 && !vowel(*s) && vowel(*(s+1))) { ++count; s+=2; l-=2; }
    else { ++s; --l; }
  }
  return (count==0 ? 1 : count);
}
/*}}}*/

/**
 * Count syllables for Dutch words by counting vowel-consonant or
 * consonant-vowel pairs, depending on the first character being a vowel or
 * not.  If it is, a trailing e will be handled with a special rule.  This
 * algorithm fails on "vor-ueber".
 * @param s the word
 * @param l the word's length
 */
static int syll_nl(const char *s, size_t l) /*{{{*/
{  
  int count=0;
  size_t ol=l;
 
  if (vowel(*s))  
  while (l) 
  {
    if (l>=2 && vowel(*s) && !vowel(*(s+1))) { ++count; s+=2; l-=2; }
    else if (l==1 && ol>1 && !vowel(*(s-1)) && *s=='e') { ++count; s+=1; l-=1; }
    else { ++s; --l; }
  }
  else
  while (l)
  {
    if (l>=2 && !vowel(*s) && vowel(*(s+1))) { ++count; s+=2; l-=2; }
    else { ++s; --l; }
  }
  return (count==0 ? 1 : count);
}
/*}}}*/

/**
 * Count syllables.  First, charset is set to the used character set.
 * Depending on the language, the right counting function is called.
 * @param s the word
 * @param l the word's length
 */
static int syll(const char *s, size_t l) /*{{{*/
{
  assert(s!=(const char*)0);
  assert(l>=1);
  
  if (strncmp(docLanguage,"de",2)==0) return syll_de(s,l);
  else if (strncmp(docLanguage,"en",2)==0) return syll_en(s,l);
  else if (strncmp(docLanguage,"nl",2)==0) return syll_nl(s,l);
  else return syll_en(s,l);
}
/*}}}*/
/*}}}*/

/* global style() variables */ /*{{{*/
static int characters;
static int syllables;
static int words;
static int shortwords;
static int longwords;
static int bigwords;
static int sentences;
static int questions;
static int passiveSent;
static int beginArticles;
static int beginPronouns;
static int pronouns;
static int beginInterrogativePronouns;
static int interrogativePronouns;
static int beginConjunctions;
static int conjunctions;
static int nominalizations;
static int prepositions;
static int beginPrepositions;
static int beginSubConjunctions;
static int subConjunctions;
static int auxVerbs;
static int tobeVerbs;
static int shortestLine,shortestLength;
static int longestLine,longestLength;
static int paragraphs;
static struct Hit lengths;
/*}}}*/

/**
 * Process one sentence.
 * @param str sentence
 * @param length its length
 */
static void process(const char *str, size_t length, const char *file, int line) /*{{{*/
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
      if (!isalpha(*s) && *s!='-' && !endingInPossesiveS(str,s-str+2))
      {
        inword=0;
        count=syll(s-wordLength,wordLength);
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
      if (isdigit(*s) || ((*s=='.' || *s==',') && isdigit(*(s+1))))
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
      if (isalpha(*s))
      {
        ++words;
        ++sentWords;
        inword=1;
        wordLength=1;
        ++characters;
        ++sentLetters;
      }
      else if (isdigit(*s))
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
	char *filename = "input.txt";
	FILE *fp;
	regex_t re;
	int err;
  newHit(&lengths);

	if( argc > 1 )
		filename = argv[1];

  if ((err=regcomp(&re,"\\.list$",0)))
  {
    char buf[256];
    size_t len=regerror(err,&re,buf,sizeof(buf)-1);
    buf[len]='\0';
    fprintf(stderr,_("internal error, compiling a regular expression failed (%s).\n"),buf);
    exit(2);
  }
	if ((fp=fopen(filename,"r"))==(FILE*)0) 
      fprintf(stderr,_("style: Opening `%s' failed (%s).\n"),filename,strerror(errno));
    else if (regexec(&re,filename,0,NULL,0)==0) {
	    int line;
	    size_t len;
	    char buf[1024];
	    int x;
	    while( !feof(fp) ) {
		    if( (x = fscanf(fp, "%d:%zd:%[^\n]", &line, &len, buf)) < 2 )
			    len = 0;

		    if( len == 0 )
			    buf[0] = '\0';

		    process(buf, len, filename, line);
	    }
      } else {
      sentence("style",fp,filename,process,"en");
      fclose(fp);
    }

printf("characters: %d\n", characters);
printf("syllables: %d\n", syllables);
printf("words: %d\n", words);
printf("shortwords: %d\n", shortwords);
printf("longwords: %d\n", longwords);
printf("bigwords: %d\n", bigwords);
printf("sentences: %d\n", sentences);
printf("questions: %d\n", questions);
printf("passiveSent: %d\n", passiveSent);
printf("beginArticles: %d\n", beginArticles);
printf("beginPronouns: %d\n", beginPronouns);
printf("pronouns: %d\n", pronouns);
printf("beginInterrogativePronouns: %d\n", beginInterrogativePronouns);
printf("interrogativePronouns: %d\n", interrogativePronouns);
printf("beginConjunctions: %d\n", beginConjunctions);
printf("conjunctions: %d\n", conjunctions);
printf("nominalizations: %d\n", nominalizations);
printf("prepositions: %d\n", prepositions);
printf("beginPrepositions: %d\n", beginPrepositions);
printf("beginSubConjunctions: %d\n", beginSubConjunctions);
printf("subConjunctions: %d\n", subConjunctions);
printf("auxVerbs: %d\n", auxVerbs);
printf("tobeVerbs: %d\n", tobeVerbs);
printf("shortestLine: %d\n", shortestLine);
printf("shortestLength: %d\n", shortestLength);
printf("longestLine: %d\n", longestLine);
printf("longestLength: %d\n", longestLength);
printf("paragraphs: %d\n", paragraphs);

printf("\nSentence Length : Count\n");

int i;

for (i=0; i<=lengths.size; i++)
	if( lengths.data[i] != 0 )
		printf("%d : %d\n", i + 1, lengths.data[i]);

    return 0;
}
/*}}}*/
