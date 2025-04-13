# https://hub.docker.com
FROM amazonlinux:2023

ENV TZ=Asia/Tokyo

RUN dnf install -y php8.4

# Composer システム要件
# https://getcomposer.org/doc/00-intro.md#system-requirements
RUN dnf install -y php8.4-zip

# CakePHP システム要件
# https://book.cakephp.org/5/ja/installation.html

# https://www.php.net/manual/ja/intl.requirements.php
RUN dnf install -y php8.4-intl

# pecl に必要なライブラリ
RUN dnf install -y php-pear php8.4-devel

# https://www.php.net/manual/ja/ref.pdo-sqlsrv.php
# https://learn.microsoft.com/ja-jp/sql/connect/php/installation-tutorial-linux-mac?view=sql-server-ver16#installing-on-ubuntu-with-php-fpm
# https://learn.microsoft.com/ja-jp/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-ver16&tabs=debian18-install%2Calpine17-install%2Cdebian8-install%2Credhat7-13-install%2Crhel7-offline#18
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /etc/pki/rpm-gpg/microsoft-prod.gpg
RUN printf "[packages-microsoft-com-prod]\nname=packages-microsoft-com-prod\nbaseurl=https://packages.microsoft.com/rhel/9/prod/\nenabled=1\ngpgcheck=1\ngpgkey=file:///etc/pki/rpm-gpg/microsoft-prod.gpg" | tee /etc/yum.repos.d/microsoft-prod.repo
RUN ACCEPT_EULA=Y dnf install -y msodbcsql18 mssql-tools18 unixODBC-devel
RUN pecl channel-update pecl.php.net
RUN pecl install sqlsrv pdo_sqlsrv
RUN echo "extension=sqlsrv.so" >> /etc/php.ini
RUN echo "extension=pdo_sqlsrv.so" | tee /etc/php.d/30-pdo_sqlsrv.ini

# PHPSpreadSheet を動かせるようにする
# https://phpspreadsheet.readthedocs.io/en/latest/
# https://www.php.net/manual/ja/image.installation.php
RUN dnf install -y php8.4-gd

# デバッグ実行できるようにする
# https://xdebug.org/docs/install#pecl
RUN pecl install xdebug
RUN echo "zend_extension=xdebug.so" >> /etc/php.ini

RUN dnf clean all

WORKDIR /src

# amazonlinux:2023 のデフォルト php.ini に PHP8.4 で非推奨のものを消す（警告が出ることを防ぐ）
RUN sed -i '/^session\.sid_length/d' /etc/php.ini
RUN sed -i '/^session\.sid_bits_per_character/d' /etc/php.ini

RUN mkdir -p /run/php-fpm

STOPSIGNAL SIGQUIT
EXPOSE 9000
CMD ["php-fpm", "-F"]
