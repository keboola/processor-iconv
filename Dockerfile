FROM alpine:latest

CMD cd /data/in/tables/ && find . -type f -exec sh -c 'iconv -f CP1250 -t UTF-8 "{}" > /data/out/tables/"{}"' \;
