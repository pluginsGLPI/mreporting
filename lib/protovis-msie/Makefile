SRC_DIR = ./src

UGLIFY ?= `which uglifyjs`

SOURCE_FILES = \
	${SRC_DIR}/shim-head.js \
	${SRC_DIR}/Vml.js \
	${SRC_DIR}/VmlScene.js \
	${SRC_DIR}/VmlPanel.js \
	${SRC_DIR}/VmlEvents.js \
	${SRC_DIR}/VmlImage.js \
	${SRC_DIR}/VmlLabel.js \
	${SRC_DIR}/VmlWedge.js \
	${SRC_DIR}/shim-tail.js

all: msie min

msie: protovis-msie.js
min: protovis-msie.min.js

protovis-msie.js: $(SOURCE_FILES) Makefile
	@@echo "Building" $@
	@@cat $(SOURCE_FILES) > $@

%.min.js: %.js Makefile
	@@if test ! -z ${UGLIFY}; then \
		echo "Building" $@; \
		${UGLIFY} --ascii < $< > $@; \
	else \
		echo "You must have ${UGLIFY} installed in order to minify the library."; \
	fi

clean:
	rm -f protovis-msie.js protovis-msie.min.js

.PHONY: all msie min clean
