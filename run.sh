#!/bin/sh
set -e

if [ -z "$KBC_PARAMETER_SOURCE_ENCODING" ]; then
    echo "Parameter 'source_encoding' not defined."
    exit 1
fi

cd $KBC_DATADIR/in/tables/
# create folders
find . ! -iname "*.manifest" ! -name "." -type d | xargs -n1 -I {} sh -c "mkdir $KBC_DATADIR/out/tables/\"{}\""
# process files
find . ! -iname "*.manifest" ! -name "." -type f | xargs -n1 -I {} sh -c "iconv -f $KBC_PARAMETER_SOURCE_ENCODING -t UTF-8 \"{}\" > $KBC_DATADIR/out/tables/\"{}\""
# move manifests
mv $KBC_DATADIR/in/tables/*.manifest $KBC_DATADIR/out/tables/
