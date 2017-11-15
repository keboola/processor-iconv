FROM alpine:latest

COPY . /code

WORKDIR /code

CMD /code/run.sh
