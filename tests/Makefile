EXECUTABLES=style
DIFFS=sentence.diff style.diff
OUTPUT=sentence_php.txt style_php.txt style_c.txt style_input.list

CC=gcc
PHP=php
DIFF=diff

all: $(DIFFS)

backup:
	for i in $(DIFFS); do [ -f $$i ] && cp $$i $$i.old; done

sentence.diff: sentence_output.txt sentence_php.txt
	$(DIFF) sentence_output.txt sentence_php.txt -u > sentence.diff

sentence_php.txt: sentence_input.txt ../Sentence.php ../DictionBase.php ../LangEN.php ../Lang.php sentence.php test_environment.php
	$(PHP) sentence.php sentence_input.txt > sentence_php.txt

style.diff: style_c.txt style_php.txt
	$(DIFF) style_c.txt style_php.txt -u > style.diff

style_c.txt: style_input.list style
	./style style_input.list > style_c.txt

style_php.txt: style_input.list ../Style.php ../Sentence.php ../DictionBase.php ../LangEN.php ../Lang.php style.php test_environment.php
	$(PHP) style.php style_input.list > style_php.txt

style: style.c
	$(CC) -Wall style.c -o style

style_input.list: style_input.txt ../Sentence.php ../DictionBase.php ../LangEN.php ../Lang.php sentence.php test_environment.php
	$(PHP) sentence.php --generate style_input.txt > style_input.list

clean:
	rm -f $(OUTPUT)

cleanbackups:
	for i in $(DIFFS); do rm -f $$i.old; done

distclean: clean cleanbackups
	rm -f $(EXECUTABLES) $(DIFFS)
