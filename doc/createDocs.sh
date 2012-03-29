#!/bin/sh

# Assumes phpdoc has been installed into the host's PATH

phpdoc run -d ../controllers ../models -t doc/
ln -s doc/index.html mj_doc.html
