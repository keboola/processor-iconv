FROM alpine:latest

CMD cd /data/in/files/ && find . -name "*.zip" -exec unzip {} -d /data/out/tables/ \;
