FROM nginx:1.18-alpine

ENV TZ=UTC

# nginx config file
COPY ./docker/nginx/*.conf /etc/nginx/conf.d/
# SSL証明書と秘密鍵をコピー
COPY ./ssl/certs/ssl_certificate_ichat.crt /etc/ssl/
COPY ./ssl/certs/ssl_certificate_ichat.key /etc/ssl/

WORKDIR /var/www/html
