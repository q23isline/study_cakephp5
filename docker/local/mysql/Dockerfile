FROM mysql:9.0

# タイムゾーンを設定（日本）
ENV TZ=Asia/Tokyo

COPY docker/local/mysql/etc-mysql.cnf /etc/mysql/conf.d/etc-mysql.cnf

EXPOSE 3306
