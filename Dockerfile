FROM ubuntu:latest
MAINTAINER Arthur Juchereau <arthur.juchereau@gmail.com>
RUN apt-get update && apt-get upgrade -y
RUN apt-get install apache2 php5 php5-sqlite php5-curl php5-mcrypt -y
RUN rm /var/www/html/index.html
ADD src /var/www/html
RUN a2enmod rewrite
RUN php5enmod mcrypt
ADD conf/apache2.conf /etc/apache2/apache2.conf
ADD conf/php.ini /etc/php5/apache2/php.ini
ADD conf/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN chown -R www-data:www-data /var/www

EXPOSE 80
EXPOSE 443

CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]
