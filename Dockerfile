FROM alpine:latest

CMD cd /data/in/tables/ && find . -type f -exec sh -c 'iconv -f $KBC_PARAMETER_SOURCE_ENCODING -t UTF-8 "{}" > /data/out/tables/"{}"' \;
