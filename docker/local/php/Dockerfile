# https://hub.docker.com/_/php
FROM php:8.4.3-fpm

ENV TZ=Asia/Tokyo

WORKDIR /var/www/html

RUN apt update

# Composer システム要件
# https://getcomposer.org/doc/00-intro.md#system-requirements
RUN apt install -y libzip-dev zip unzip
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip

# CakePHP システム要件
# https://book.cakephp.org/5/ja/installation.html

# https://www.php.net/manual/ja/mbstring.installation.php
RUN apt install -y libonig-dev
RUN docker-php-ext-install mbstring

# https://www.php.net/manual/ja/intl.requirements.php
RUN apt install -y libicu-dev
RUN docker-php-ext-install intl

# https://www.php.net/manual/ja/simplexml.requirements.php
RUN apt install -y libxml2-dev
RUN docker-php-ext-install simplexml

# https://www.php.net/manual/ja/ref.pdo-sqlsrv.php
# https://learn.microsoft.com/ja-jp/sql/connect/php/installation-tutorial-linux-mac?view=sql-server-ver16#installing-on-ubuntu-with-php-fpm
# https://learn.microsoft.com/ja-jp/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-ver16&tabs=debian18-install%2Calpine17-install%2Cdebian8-install%2Credhat7-13-install%2Crhel7-offline#18
RUN apt install -y gpg
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg
RUN curl https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list
RUN apt update
RUN ACCEPT_EULA=Y apt install -y msodbcsql18
RUN echo 'export PATH="$PATH:/opt/mssql-tools18/bin"' >> ~/.bashrc
RUN bash -c "source ~/.bashrc"
RUN apt install -y unixodbc-dev
RUN pecl install sqlsrv
RUN pecl install pdo_sqlsrv
RUN docker-php-ext-enable pdo_sqlsrv

# LibXL インストール
# https://www.libxl.com/php.html
RUN curl -OL https://www.libxl.com/download/libxl-lin-4.5.1.tar.gz
RUN tar zxvf libxl-lin-4.5.1.tar.gz
RUN apt install -y git
# 公式だと make コマンドでエラーになる。
# RUN git clone https://github.com/iliaal/php_excel.git
# 公式からフォークされたリポジトリを利用すると make は通るが make test で 40% のテストが失敗する。
RUN git clone -b php7_with_pulls https://github.com/Jan-E/php_excel.git
WORKDIR /var/www/html/php_excel
RUN phpize
RUN ./configure --with-libxl-incdir=../libxl-4.5.1/include_c --with-libxl-libdir=../libxl-4.5.1/lib64 --with-libxml-dir=/usr/include/libxml2
RUN make
RUN make test
RUN make install
WORKDIR /var/www/html
RUN rm libxl-lin-4.5.1.tar.gz

# デバッグ実行できるようにする
# https://xdebug.org/docs/install#pecl
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apt clean
RUN rm -rf /var/lib/apt/lists/*

WORKDIR /src
