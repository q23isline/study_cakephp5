# study_cakephp5

[![LICENSE](https://img.shields.io/badge/license-MIT-green.svg)](./LICENSE)
![releases](https://img.shields.io/github/release/q23isline/study_cakephp5.svg?logo=github)
[![GitHub Actions Backend](https://github.com/q23isline/study_cakephp5/actions/workflows/backend_ci.yml/badge.svg)](https://github.com/q23isline/study_cakephp5/actions/workflows/backend_ci.yml)
[![GitHub Actions Frontend](https://github.com/q23isline/study_cakephp5/actions/workflows/frontend_ci.yml/badge.svg)](https://github.com/q23isline/study_cakephp5/actions/workflows/frontend_ci.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)
[![Open in Visual Studio Code](https://img.shields.io/static/v1?logo=visualstudiocode&label=&message=Open%20in%20Visual%20Studio%20Code&labelColor=555555&color=007acc&logoColor=007acc)](https://github.dev/q23isline/study_cakephp5)

[![PHP](https://img.shields.io/static/v1?logo=php&label=PHP&message=v8.4.3&labelColor=555555&color=777BB4&logoColor=777BB4)](https://www.php.net)
[![CakePHP](https://img.shields.io/static/v1?logo=cakephp&label=CakePHP&message=v5.1.5&labelColor=555555&color=D33C43&logoColor=D33C43)](https://cakephp.org)
[![SQL Server](https://img.shields.io/static/v1?label=SQL%20Server&message=v2022&labelColor=555555&color=FFFFFF&logoColor=FFFFFF)](https://learn.microsoft.com/ja-jp/sql/sql-server/)
[![Node.js](https://img.shields.io/static/v1?logo=node.js&label=Node.js&message=v22.12.0&labelColor=555555&color=339933&logoColor=339933)](https://nodejs.org)
[![npm](https://img.shields.io/static/v1?logo=npm&label=npm&message=v10.9.0&labelColor=555555&color=CB3837&logoColor=CB3837)](https://www.npmjs.com/)
[![Vue.js](https://img.shields.io/static/v1?logo=vue.js&label=Vue.js&message=v3.13.0&labelColor=555555&color=4FC08D&logoColor=4FC08D)](https://ja.vuejs.org/)

CakePHP 5 勉強用リポジトリ

- [バックエンド開発ガイドライン](./backend/README.md)
- [フロントエンド開発ガイドライン](./frontend/README.md)

## 前提

- インストール
  - [Windows Subsystem for Linux](https://learn.microsoft.com/ja-jp/windows/wsl/)
  - [Git](https://git-scm.com/)
  - [Docker Desktop](https://www.docker.com/ja-jp/products/docker-desktop/)
  - [Visual Studio Code](https://code.visualstudio.com/)
  - [SQL Server Management Studio](https://learn.microsoft.com/ja-jp/sql/ssms/)

## はじめにやること

1. Windows Subsystem for Linux 上でプログラムダウンロード

    ```bash
    git clone https://github.com/q23isline/study_cakephp5.git
    ```

2. リポジトリのカレントディレクトリへ移動

    ```bash
    cd study_cakephp5
    ```

3. 開発準備

    ```bash
    rm -f .dockerignore
    cp .vscode/extensions.json.default .vscode/extensions.json
    cp .vscode/launch.json.default .vscode/launch.json
    cp .vscode/settings.json.default .vscode/settings.json
    cp backend/config/.env.example backend/config/.env
    cp backend/config/app_local.example.php backend/config/app_local.php
    ```

4. アプリ立ち上げ

    ```bash
    docker compose build
    sudo chmod -R ugo+rw ./
    docker compose up -d
    docker compose exec backend php composer.phar install
    docker compose exec frontend npm install
    sudo chmod -R 777 backend/vendor frontend/node_modules
    docker compose exec backend bin/cake migrations migrate
    docker compose exec backend bin/cake migrations seed
    ```

## 日常的にやること

### システム起動

```bash
# DB、バックエンドコンテナ起動
docker compose up -d
# バックエンド起動
docker compose exec backend bin/cake server -H 0.0.0.0
# フロントエンド起動
docker compose exec frontend npm run dev -- --host
```

### システム終了

```bash
# フロントエンド起動ターミナルで Ctrl + c
# バックエンド起動ターミナルで Ctrl + c

docker compose down
```

## 動作確認

### URL

#### バックエンド

<http://localhost:8765/users/login>

#### フロントエンド

<http://localhost:5173/>

#### ログイン情報

| Email               | Password |
| ------------------- | -------- |
| <admin@example.com> | admin00  |

## Permission Deniedエラーが出た時の解決方法

```bash
# プロジェクト全体のファイルすべてに読み込み、書き込み権限を与える
sudo chmod -R ugo+rw ./
# インストールしたライブラリに実行権限を含めた全権限を与える
sudo chmod -R 777 backend/vendor frontend/node_modules
```

## データベースへの接続

| 項目名                   | 設定値          |
| ------------------------ | --------------- |
| サーバー名               | 127.0.0.1       |
| 認証                     | SQL Server 認証 |
| ユーザー名               | sa              |
| パスワード               | Passw0rd        |
| サーバー証明書を信頼する | ON              |

## ログ出力場所

| サービス                | ログ出力場所  |
| ----------------------- | ------------- |
| CakePHP (開発)          | backend/logs  |
| SQL Server (開発・本番) | logs/db       |
| Node.js (開発)          | logs/frontend |
| CakePHP (本番)          | logs/backend  |
| NGINX・PHP-FPM (本番)   | logs/web      |

## 本番想定でのアプリ立ち上げ

1. 本番想定のアプリ起動準備

    ```bash
    cp docker/prod/amazon_linux/nginx/ssl/server.crt.default docker/prod/amazon_linux/nginx/ssl/server.crt
    cp docker/prod/amazon_linux/nginx/ssl/server.csr.default docker/prod/amazon_linux/nginx/ssl/server.csr
    cp docker/prod/amazon_linux/nginx/ssl/server.key.default docker/prod/amazon_linux/nginx/ssl/server.key
    cp .dockerignore.prod-default .dockerignore
    ```

2. アプリ立ち上げ

    ```bash
    cd study_cakephp5
    docker compose -f docker-compose-prod.yml build
    docker compose -f docker-compose-prod.yml up -d
    docker compose exec app-prod bin/cake migrations migrate
    docker compose exec app-prod bin/cake migrations seed
    sudo chmod -R ugo+rw logs
    ```

### 本番想定での URL

<http://localhost>

## 本番想定でのアプリ終了

```bash
docker compose -f docker-compose-prod.yml down
```
