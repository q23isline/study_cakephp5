# study_cakephp5

[![LICENSE](https://img.shields.io/badge/license-MIT-green.svg)](./LICENSE)
![releases](https://img.shields.io/github/release/q23isline/study_cakephp5.svg?logo=github)
[![GitHub Actions Backend](https://github.com/q23isline/study_cakephp5/actions/workflows/ci.yml/badge.svg)](https://github.com/q23isline/study_cakephp5/actions/workflows/ci.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)
[![Open in Visual Studio Code](https://img.shields.io/static/v1?logo=visualstudiocode&label=&message=Open%20in%20Visual%20Studio%20Code&labelColor=555555&color=007acc&logoColor=007acc)](https://github.dev/q23isline/study_cakephp5)

[![PHP](https://img.shields.io/static/v1?logo=php&label=PHP&message=v8.4.3&labelColor=555555&color=777BB4&logoColor=777BB4)](https://www.php.net)
[![CakePHP](https://img.shields.io/static/v1?logo=cakephp&label=CakePHP&message=v5.1.5&labelColor=555555&color=D33C43&logoColor=D33C43)](https://cakephp.org)
[![SQL Server](https://img.shields.io/static/v1?label=SQL%20Server&message=v2022&labelColor=555555&color=FFFFFF&logoColor=FFFFFF)](https://learn.microsoft.com/ja-jp/sql/sql-server/)

CakePHP 5 勉強用リポジトリ

- [バックエンド開発ガイドライン](./backend/README.md)

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
    sudo chmod -R 777 backend/vendor
    docker compose up -d
    ```

## 日常的にやること

### システム起動

```bash
# DB、バックエンドコンテナ起動
docker compose up -d
# バックエンド起動
docker compose exec backend bin/cake server -H 0.0.0.0
```

### システム終了

```bash
# バックエンド起動ターミナルで Ctrl + c

docker compose down
```

## 動作確認

### URL

#### バックエンド

<http://localhost:8765/>

## Permission Deniedエラーが出た時の解決方法

```bash
# プロジェクト全体のファイルすべてに読み込み、書き込み権限を与える
sudo chmod -R ugo+rw ./
# インストールしたライブラリに実行権限を含めた全権限を与える
sudo chmod -R 777 backend/vendor
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

| サービス             | ログ出力場所  |
| -------------------- | ------------- |
| SQL Server           | logs/db       |
