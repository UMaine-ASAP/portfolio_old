#!/bin/sh

SCRIPTPATH=$(dirname $0)
DB=mj_dev
USER=asap

echo "Database: $DB"
echo "Username: $USER"
cat $SCRIPTPATH/mainejournal_init.sql | mysql $DB -u $USER -p
