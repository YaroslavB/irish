FROM nginx:alpine

COPY nginx.conf /etc/nginx/
COPY default.conf /etc/nginx/conf.d/

#SSL
COPY certs/nginx-selfsigned.crt /etc/ssl/certs/nginx-selfsigned.crt
COPY certs/nginx-selfsigned.key /etc/ssl/private/nginx-selfsigned.key
COPY certs/dhparam.pem  /etc/nginx/dhparam.pem
COPY certs/self-signed.conf /etc/nginx/snippets/self-signed.conf
COPY certs/ssl-params.conf /etc/nginx/snippets/ssl-params.conf




RUN apk update && apk upgrade && apk add --no-cache bash openssl

RUN set -x ; \
    addgroup -g 82 -S www-data ; \
    adduser -u 82 -D -S -G www-data www-data && exit 0 ; exit 1

EXPOSE 80:443