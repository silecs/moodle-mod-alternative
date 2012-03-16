.PHONY : all

all: structure.html

%.html: %.md Makefile
	pandoc -c ~/.emacs.d/data/markdown.css -s -f markdown -t html --standalone -o $@ $<

%.odt: %.md Makefile
	pandoc --standalone -f markdown -t odt -o $@ $<

%.pdf: %.md %.odt
	markdown2pdf -f markdown -o $@ $<
