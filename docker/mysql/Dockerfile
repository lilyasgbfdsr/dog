ARG MYSQL_VERSION
FROM mysql:5.7

#####################################
# Set Timezone
#####################################
RUN ln -snf /usr/share/zoneinfo/Asia/Shanghai /etc/localtime && echo 'Asia/Shanghai' > /etc/timezone

RUN chown -R mysql:root /var/lib/mysql

COPY conf/my.cnf /etc/mysql/conf.d/my.cnf

RUN chmod 0444 /etc/mysql/conf.d/my.cnf

CMD ["mysqld"]

EXPOSE 3306
