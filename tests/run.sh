#!/bin/sh
set -e

rm -rf /tmp/tests
mkdir /tmp/tests
cp -r /code/tests/data /tmp/tests

echo "Running tests"
export KBC_DATADIR=/tmp/tests/data
export KBC_PARAMETER_SOURCE_ENCODING=windows-1250

/code/run.sh

diff -w $KBC_DATADIR/out/tables/radio.csv /code/tests/sample/radio.csv
diff -w $KBC_DATADIR/out/tables/radio.csv.manifest /code/tests/sample/radio.csv.manifest
diff -w $KBC_DATADIR/out/tables/text.csv /code/tests/sample/text.csv
diff -w $KBC_DATADIR/out/tables/text.csv.manifest /code/tests/sample/text.csv.manifest
diff -w $KBC_DATADIR/out/tables/sliced/text.csv /code/tests/sample/sliced/text.csv
diff -w $KBC_DATADIR/out/tables/sliced/radio.csv /code/tests/sample/sliced/radio.csv
diff -w $KBC_DATADIR/out/tables/sliced.manifest /code/tests/sample/sliced.manifest

echo "Tests finished"
