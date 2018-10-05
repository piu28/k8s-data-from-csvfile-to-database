FROM ACCOUNT.dkr.ecr.ap-south-1.amazonaws.com/demo-nginx
ADD . /opt/csvparser/
RUN echo "exit 0" > /usr/sbin/policy-rc.d
CMD /etc/init.d/php7.0-fpm restart && nginx -g 'daemon off;'
