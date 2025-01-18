# https://hub.docker.com/_/php
FROM php:8.4.3-fpm

ENV TZ=Asia/Tokyo

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

# デバッグ実行できるようにする
# https://xdebug.org/docs/install#pecl
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apt clean
RUN rm -rf /var/lib/apt/lists/*

WORKDIR /src
