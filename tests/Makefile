EXECUTABLES=sentence style
DIFFS=sentence.diff style.diff
OUTPUT=sentence_php.txt sentence_c.txt style_php.txt style_c.txt

CC=gcc
PHP=php
DIFF=diff

all: $(DIFFS)

backup:
	for i in $(DIFFS); do [ -f $$i ] && cp $$i $$i.old; done

sentence.diff: sentence_c.txt sentence_php.txt
	$(DIFF) sentence_c.txt sentence_php.txt -u > sentence.diff

sentence_c.txt: input.txt sentence
	./sentence > sentence_c.txt

sentence_php.txt: input.txt ../Sentence.php ../DictionBase.php ../LangEN.php ../Lang.php sentence.php
	$(PHP) sentence.php > sentence_php.txt

sentence: sentence.c
	$(CC) -Wall sentence.c -o sentence

style.diff: style_c.txt style_php.txt
	$(DIFF) style_c.txt style_php.txt -u > style.diff

style_c.txt: input.txt style
	./style > style_c.txt

style_php.txt: input.txt ../Style.php ../Sentence.php ../DictionBase.php ../LangEN.php ../Lang.php style.php
	$(PHP) style.php > style_php.txt

style: style.c
	$(CC) -Wall style.c -o style

clean:
	rm -f $(OUTPUT)

cleanbackups:
	for i in $(DIFFS); do rm -f $$i.old; done

distclean: clean cleanbackups
	rm -f $(EXECUTABLES) $(DIFFS)
