FROM nginx:1.17-alpine

COPY ./docker/production/nginx/conf.d /etc/nginx/conf.d

WORKDIR /var/www/app

CMD ["nginx", "-g", "daemon off;"]
