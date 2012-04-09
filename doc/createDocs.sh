#!/bin/sh

# Assumes phpdoc has been installed into the host's PATH

SCRIPTPATH=$(dirname $0)

phpdoc run -d $SCRIPTPATH/../controllers $SCRIPTPATH/../models -t $SCRIPTPATH/doc/
ln -s $SCRIPTPATH/doc/index.html $SCRIPTPATH/mj_doc.html
