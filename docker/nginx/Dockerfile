FROM nginx:latest

ARG APP_HOST

COPY ./nginx/site.conf /etc/nginx/conf.d/site.conf
RUN sed -i -e "s/APP_HOST/$APP_HOST/g" /etc/nginx/conf.d/site.conf

WORKDIR /web/backend

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
