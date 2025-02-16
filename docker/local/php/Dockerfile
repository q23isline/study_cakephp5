# https://hub.docker.com
FROM amazonlinux:2023

ENV TZ=Asia/Tokyo

RUN dnf update -y

# PHP 8.4.3 インストール
# amazonlinux:2023 ではまだ PHP8.4 がパッケージに追加されていないのでソースからビルドする
ENV PHP_BUILD_TOOLS="gcc libxml2-devel sqlite-devel"
RUN dnf install -y tar gzip $PHP_BUILD_TOOLS
RUN curl -OL https://www.php.net/distributions/php-8.4.3.tar.gz
RUN tar -xzvf php-8.4.3.tar.gz
RUN rm php-8.4.3.tar.gz
WORKDIR php-8.4.3
RUN ./configure --enable-fpm
RUN make
RUN make install

# Composer システム要件
# https://getcomposer.org/doc/00-intro.md#system-requirements
RUN dnf install -y autoconf libzip-devel zip unzip
WORKDIR /php-8.4.3/ext/zip
RUN phpize
RUN ./configure
RUN make
RUN make install
RUN echo "extension=zip.so" >> /usr/local/lib/php.ini

# CakePHP システム要件
# https://book.cakephp.org/5/ja/installation.html

# https://www.php.net/manual/ja/mbstring.installation.php
RUN dnf install -y oniguruma oniguruma-devel
WORKDIR /php-8.4.3/ext/mbstring
RUN phpize
RUN ./configure
RUN make
RUN make install
RUN echo "extension=mbstring.so" >> /usr/local/lib/php.ini

# https://www.php.net/manual/ja/intl.requirements.php
RUN dnf install -y libicu libicu-devel g++
WORKDIR /php-8.4.3/ext/intl
RUN phpize
RUN ./configure
RUN make
RUN make install
RUN echo "extension=intl.so" >> /usr/local/lib/php.ini

RUN dnf install -y libcurl-devel
WORKDIR /php-8.4.3/ext/curl
RUN phpize
RUN ./configure
RUN make
RUN make install
RUN echo "extension=curl.so" >> /usr/local/lib/php.ini

RUN dnf install -y openssl-devel
WORKDIR /php-8.4.3/ext/openssl
RUN cp config0.m4 config.m4
RUN phpize
RUN ./configure
RUN make
RUN make install
RUN echo "extension=openssl.so" >> /usr/local/lib/php.ini

WORKDIR /php-8.4.3/ext/zlib
RUN cp config0.m4 config.m4
RUN phpize
RUN ./configure
RUN make
RUN make install
RUN echo "extension=zlib.so" >> /usr/local/lib/php.ini

# https://www.php.net/manual/ja/ref.pdo-sqlsrv.php
# https://learn.microsoft.com/ja-jp/sql/connect/php/installation-tutorial-linux-mac?view=sql-server-ver16#installing-on-ubuntu-with-php-fpm
# https://learn.microsoft.com/ja-jp/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-ver16&tabs=debian18-install%2Calpine17-install%2Cdebian8-install%2Credhat7-13-install%2Crhel7-offline#18
# https://github.com/microsoft/msphpsql/tree/v5.12.0/buildscripts
RUN dnf install -y gpg git
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /etc/pki/rpm-gpg/microsoft-prod.gpg
RUN printf "[packages-microsoft-com-prod]\nname=packages-microsoft-com-prod\nbaseurl=https://packages.microsoft.com/rhel/9/prod/\nenabled=1\ngpgcheck=1\ngpgkey=file:///etc/pki/rpm-gpg/microsoft-prod.gpg" | tee /etc/yum.repos.d/microsoft-prod.repo
RUN ACCEPT_EULA=Y dnf install -y msodbcsql18 mssql-tools18 unixODBC-devel
WORKDIR /tmp
# pecl でパッケージをインストールしようとするとパッケージがないと言われるのでソースからビルドする
RUN git clone -b v5.12.0 --depth 1 https://github.com/Microsoft/msphpsql.git
RUN cp -r msphpsql/source/shared msphpsql/source/sqlsrv/shared
RUN cp -r msphpsql/source/shared msphpsql/source/pdo_sqlsrv/shared
RUN cp -r msphpsql/source/sqlsrv /php-8.4.3/ext/sqlsrv
RUN cp -r msphpsql/source/pdo_sqlsrv /php-8.4.3/ext/pdo_sqlsrv
WORKDIR /php-8.4.3
RUN bash buildconf --force
WORKDIR /php-8.4.3/ext/sqlsrv
RUN phpize
RUN ./configure --enable-sqlsrv=shared
RUN make
RUN make install
RUN echo "extension=sqlsrv.so" >> /usr/local/lib/php.ini
WORKDIR /tmp/msphpsql/source/pdo_sqlsrv
RUN phpize
RUN ./configure --enable-pdo --with-pdo-sqlsrv=shared
RUN make
RUN make install
RUN echo "extension=pdo_sqlsrv.so" >> /usr/local/lib/php.ini

# PHPSpreadSheet を動かせるようにする
# https://phpspreadsheet.readthedocs.io/en/latest/
# https://www.php.net/manual/ja/image.installation.php
RUN dnf install -y libpng-devel libjpeg-devel freetype-devel
WORKDIR /php-8.4.3/ext/gd
RUN phpize
RUN ./configure --with-jpeg --with-freetype
RUN make
RUN make install
RUN echo "extension=gd.so" >> /usr/local/lib/php.ini

# デバッグ実行できるようにする
# https://xdebug.org/docs/install#pecl
# pecl でパッケージをインストールしようとするとパッケージがないと言われるのでソースからビルドする
WORKDIR /tmp
RUN git clone -b 3.4.1 --depth 1 https://github.com/xdebug/xdebug.git
WORKDIR xdebug
RUN phpize
RUN ./configure
RUN make
RUN make install
RUN echo "zend_extension=$(find /usr/local/lib/php/extensions -name 'xdebug.so')" >> /usr/local/lib/php.ini
# docker/local/php/php.ini のほうが読み込まれず、ブレークポイントで止まらないのでここで書き込む
RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/lib/php.ini
RUN echo "xdebug.mode=debug" >> /usr/local/lib/php.ini
RUN echo "xdebug.start_with_request=yes" >> /usr/local/lib/php.ini
RUN echo "xdebug.log_level=0" >> /usr/local/lib/php.ini

# php-fpm を起動する
# https://hub.docker.com/layers/library/php/8.4.3-fpm/images/sha256-887ba0cc61c905da165ae88ebfbe3d1268c15e9399c09e860ecd0f94d59c6593
WORKDIR /usr/local/etc
RUN if [ -d php-fpm.d ];\
    then sed 's!=NONE/!=!g' php-fpm.conf.default | tee php-fpm.conf > /dev/null;\
    cp php-fpm.d/www.conf.default php-fpm.d/www.conf;\
    else mkdir php-fpm.d;\
    cp php-fpm.conf.default php-fpm.d/www.conf;\
    { echo '[global]';\
    echo 'include=etc/php-fpm.d/*.conf';\
    } | tee php-fpm.conf;\
    fi;
RUN { echo '[global]';\
    echo 'error_log = /proc/self/fd/2';\
    echo;\
    echo ';https://github.com/docker-library/php/pull/725#issuecomment-443540114';\
    echo 'log_limit = 8192';\
    echo;\
    echo '[www]';\
    echo ';php-fpm closes STDOUT on startup, so sending logs to /proc/self/fd/1 does not work.';\
    echo ';https://bugs.php.net/bug.php?id=73886';\
    echo 'access.log = /proc/self/fd/2';\
    echo;\
    echo 'clear_env = no';\
    echo;\
    echo ';Ensure worker stdout and stderr are sent to the main error log.';\
    echo 'catch_workers_output = yes';\
    echo 'decorate_workers_output = no';\
    } | tee php-fpm.d/docker.conf;
RUN { echo '[global]';\
    echo 'daemonize = no';\
    echo;\
    echo '[www]';\
    echo 'listen = 9000';\
    } | tee php-fpm.d/zz-docker.conf;

WORKDIR /src

STOPSIGNAL SIGQUIT
EXPOSE 9000
CMD ["php-fpm"]
