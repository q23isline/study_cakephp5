# バックエンドのビルドステージ
FROM php:8.4.3-fpm AS php-build
ENV TZ=Asia/Tokyo
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    libonig-dev \
    libicu-dev \
    libxml2-dev \
    gpg \
    unixodbc-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure zip && docker-php-ext-install zip \
    && docker-php-ext-install mbstring intl simplexml \
    && docker-php-ext-configure gd --with-jpeg --with-freetype \
    && docker-php-ext-install gd \
    && curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl https://packages.microsoft.com/config/debian/12/prod.list | tee /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv \
    && apt-get clean && rm -rf /var/lib/apt/lists/*
WORKDIR /src
COPY backend/composer.* /src/
RUN php composer.phar install --no-dev

# フロントエンドのビルドステージ
FROM node:22.12 AS node-build
ENV TZ=Asia/Tokyo
WORKDIR /src
COPY frontend/package*.json /src/
RUN npm install
COPY frontend/ /src
ARG VITE_API_BASE_URL=/
ENV VITE_API_BASE_URL=$VITE_API_BASE_URL
RUN npm run build-only

# ランタイムステージ
FROM amazonlinux:2023
ENV TZ=Asia/Tokyo
# この指定がないと backend/config/bootstrap.php で開発用のパッケージを読もうとしてエラーになる
ENV APP_NAME=Study_CakePHP5
# backend/config/.env に指定していた内容をここで指定する
ENV DEBUG=false
ENV APP_ENCODING=UTF-8
ENV APP_DEFAULT_LOCALE=ja_JP
ENV APP_DEFAULT_TIMEZONE=Asia/Tokyo
ENV SECURITY_SALT=25249bef0309e62b292e4d8bfd924fa59e30e869f2b7742bfc8dce7dd1013f73

WORKDIR /var/www/localhost/publish
RUN dnf install -y nginx php8.4 php8.4-zip php8.4-intl php8.4-gd php-pear php8.4-devel && \
    curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /etc/pki/rpm-gpg/microsoft-prod.gpg && \
    printf "[packages-microsoft-com-prod]\nname=packages-microsoft-com-prod\nbaseurl=https://packages.microsoft.com/rhel/9/prod/\nenabled=1\ngpgcheck=1\ngpgkey=file:///etc/pki/rpm-gpg/microsoft-prod.gpg" | tee /etc/yum.repos.d/microsoft-prod.repo && \
    ACCEPT_EULA=Y dnf install -y msodbcsql18 mssql-tools18 unixODBC-devel && \
    dnf clean all && \
    pecl channel-update pecl.php.net && \
    pecl install sqlsrv pdo_sqlsrv && \
    echo "extension=sqlsrv.so" >> /etc/php.ini && \
    echo "extension=pdo_sqlsrv.so" | tee /etc/php.d/30-pdo_sqlsrv.ini
COPY docker/prod/amazon_linux/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./docker/prod/amazon_linux/php/php.ini /etc/php.d/php.ini
COPY docker/prod/amazon_linux/nginx/ssl /etc/nginx/ssl
COPY backend/ /var/www/localhost/publish
COPY --from=php-build /src/vendor /var/www/localhost/publish/vendor
COPY --from=node-build /src/dist /var/www/localhost/publish/webroot
COPY docker/prod/amazon_linux/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh && \
    mv config/app_local.example.php config/app_local.php && \
    chown -R apache:apache ./ && \
    mkdir -p /run/php-fpm && \
    chown -R apache:apache /run/php-fpm

EXPOSE 80 443
ENTRYPOINT ["/entrypoint.sh"]
