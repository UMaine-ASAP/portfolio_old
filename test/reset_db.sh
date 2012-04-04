#!/bin/sh

SCRIPTPATH=$(dirname $0)

echo "Username: asap"
cat $SCRIPTPATH/db_init.sql $SCRIPTPATH/test_data.sql | mysql mj_dev -u asap -p
