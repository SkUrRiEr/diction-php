EXECUTABLES=sentence style
DIFFS=sentence.diff style.diff
OUTPUT=sentence_php.txt sentence_c.txt style_php.txt style_c.txt

CC=gcc
PHP=php
DIFF=diff

all: sentence.diff style.diff

sentence.diff: sentence_c.txt sentence_php.txt
	$(DIFF) sentence_c.txt sentence_php.txt -u > sentence.diff

sentence_c.txt: sentence input.txt
	./sentence > sentence_c.txt

sentence_php.txt: ../Sentence.php sentence.php input.txt
	$(PHP) sentence.php > sentence_php.txt

sentence: sentence.c
	$(CC) -Wall sentence.c -o sentence

style.diff: style_c.txt style_php.txt
	$(DIFF) style_c.txt style_php.txt -u > style.diff

style_c.txt: style input.txt
	./style > style_c.txt

style_php.txt: ../Style.php style.php input.txt
	$(PHP) style.php > style_php.txt

style: style.c
	$(CC) -Wall style.c -o style

clean:
	rm -f $(OUTPUT)

distclean: clean
	rm -f $(EXECUTABLES) $(DIFFS)
