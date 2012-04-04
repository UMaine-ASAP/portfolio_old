#!/bin/sh

SCRIPTPATH=$(dirname $0)
DB=mj_dev
USER=asap

echo "Database: $DB"
echo "Username: $USER"
cat $SCRIPTPATH/db_init.sql $SCRIPTPATH/test_data.sql | mysql $DB -u $USER -p
