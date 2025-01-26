# CakePHP Application Skeleton

## バックエンド開発ガイドライン

### コーディング標準チェック単体実行

```bash
# フォーマッターチェック実行
docker compose exec backend ./vendor/bin/phpcs --colors -p src/
# フォーマッター自動整形実行
docker compose exec backend ./vendor/bin/phpcbf src/
# コード静的解析実行
docker compose exec backend ./vendor/bin/phpstan analyse
```

### Postman による API の動作確認

- ヘッダータブに以下を設定

| KEY    | VALUE            |
| ------ | ---------------- |
| Cookie | csrfToken=xxxxxx |

※ デベロッパーツール > ネットワークタブ > リクエスト行 > ヘッダータブ > リクエストヘッダーアコーディオン > Cookie から取得する

- 【GET以外の場合】追加でヘッダータブに以下を設定

| KEY          | VALUE  |
| ------------ | ------ |
| X-CSRF-Token | yyyyyy |

※ 画面の HTML を表示し、input タグの name が _csrfToken の value （ ↑ とは異なる値）

### パッケージをインストールする

インストール済のパッケージは `backend/composer.json` 参照

パッケージ一覧

<https://packagist.org/>

```bash
docker compose exec backend php composer.phar require ｛パッケージ名｝
# PHPStan を開発用にインストールする例
# docker compose exec backend php composer.phar require --dev phpstan/phpstan

# インストールしたパッケージに実行権限を含めた全権限を与える
sudo chmod -R 777 backend/vendor
```

### マイグレーションファイルを生成する

```bash
docker compose exec backend bin/cake bake migration Create｛テーブル名｝ ｛カラム名｝:｛型名｝
# products テーブルを追加する例
# docker compose exec backend bin/cake bake migration CreateProducts name:string description:text created modified

# マイグレーションファイルを DB のテーブルに反映させる
docker compose exec backend bin/cake migrations migrate
```

<https://book.cakephp.org/migrations/3/ja/index.html#id5>

### 既存のデータベースからマイグレーションファイルを作成する

```bash
docker compose exec backend bin/cake bake migration_snapshot Initial
```

<https://book.cakephp.org/migrations/3/ja/index.html#id15>

### コードを自動生成する

#### MVC を自動生成する

```bash
docker compose exec backend bin/cake bake all ｛MVCを生成したいリソース名｝
# user テーブルを元に MVC を生成する例
# docker compose exec backend bin/cake bake all users
```

Model のみ、Controller のみ、 View のみを自動生成する例

<https://book.cakephp.org/5/ja/tutorials-and-examples/cms/tags-and-users.html>

---

![Build Status](https://github.com/cakephp/app/actions/workflows/ci.yml/badge.svg?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/cakephp/app.svg?style=flat-square)](https://packagist.org/packages/cakephp/app)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%207-brightgreen.svg?style=flat-square)](https://github.com/phpstan/phpstan)

A skeleton for creating applications with [CakePHP](https://cakephp.org) 5.x.

The framework source code can be found here: [cakephp/cakephp](https://github.com/cakephp/cakephp).

## Installation

1. Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar create-project --prefer-dist cakephp/app [app_name]`.

If Composer is installed globally, run

```bash
composer create-project --prefer-dist cakephp/app
```

In case you want to use a custom app dir name (e.g. `/myapp/`):

```bash
composer create-project --prefer-dist cakephp/app myapp
```

You can now either use your machine's webserver to view the default home page, or start
up the built-in webserver with:

```bash
bin/cake server -p 8765
```

Then visit `http://localhost:8765` to see the welcome page.

## Update

Since this skeleton is a starting point for your application and various files
would have been modified as per your needs, there isn't a way to provide
automated upgrades, so you have to do any updates manually.

## Configuration

Read and edit the environment specific `config/app_local.php` and set up the
`'Datasources'` and any other configuration relevant for your application.
Other environment agnostic settings can be changed in `config/app.php`.

## Layout

The app skeleton uses [Milligram](https://milligram.io/) (v1.3) minimalist CSS
framework by default. You can, however, replace it with any other library or
custom styles.
