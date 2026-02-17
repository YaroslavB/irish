FROM nginx:alpine

WORKDIR /etc/nginx

# Copy nginx configuration files
COPY nginx.conf /etc/nginx/
COPY default.conf /etc/nginx/conf.d/

# Copy SSL certificates and configuration
COPY certs/nginx-selfsigned.crt /etc/ssl/certs/
COPY certs/nginx-selfsigned.key /etc/ssl/private/
COPY certs/dhparam.pem /etc/nginx/
COPY certs/self-signed.conf certs/ssl-params.conf /etc/nginx/snippets/

# Install required packages
RUN apk add --no-cache bash openssl

# Expose HTTP and HTTPS ports
EXPOSE 80 443

