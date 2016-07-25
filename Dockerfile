FROM alpine:latest

CMD cd /data/in/tables/ \
	&& find . -iname "*.csv" | xargs -n1 -I {} sh -c "iconv -f $KBC_PARAMETER_SOURCE_ENCODING -t UTF-8 \"{}\" > /data/out/tables/\"{}\""
