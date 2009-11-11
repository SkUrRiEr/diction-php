EXECUTABLES=sentence
DIFFS=sentence.diff
OUTPUT=sentence_php.txt sentence_c.txt

CC=$(which gcc)
PHP=$(which php)
DIFF=$(which diff)

all: sentence.diff

sentence.diff: sentence_c.txt sentence_php.txt
	@$(DIFF) sentence_c.txt sentence_php.txt -u > sentence.diff

sentence_c.txt: sentence input.txt
	./sentence > sentence_c.txt

sentence_php.txt: sentence.php input.txt
	$(PHP) sentence.php > sentence_php.txt

sentence: sentence.c
	$(CC) -Wall sentence.c -o sentence

clean:
	rm -f $(OUTPUT)

distclean: clean
	rm -f $(EXECUTABLES) $(DIFFS)
