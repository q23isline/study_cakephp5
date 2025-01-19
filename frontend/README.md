# study-cakephp5

## フロントエンド開発ガイドライン

### コーディング標準チェック単体実行

```bash
# フォーマッターチェック実行
docker compose exec frontend npm run format-check
# フォーマッター自動整形実行
docker compose exec frontend npm run format
# コード静的解析実行
docker compose exec frontend npm run type-check
# コード静的解析実行
docker compose exec frontend npm run lint
```

### ビルドする

```bash
docker compose exec frontend npm run build
# ビルド結果のファイルたちに Linux の読み取り・書き込み権限を与える
sudo chmod -R ogu+rw frontend/dist
```

### ライブラリをインストールする

インストール済のライブラリは `frontend/package.json` 参照

ライブラリ一覧

<https://www.npmjs.com/>

```bash
docker compose exec frontend npm install ｛ライブラリ名｝
# axios をインストールする例
# docker compose exec frontend npm install axios

# インストールしたライブラリに実行権限を含めた全権限を与える
sudo chmod -R 777 frontend/node_modules
```

---

This template should help get you started developing with Vue 3 in Vite.

## Recommended IDE Setup

[VSCode](https://code.visualstudio.com/) + [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) (and disable Vetur).

## Type Support for `.vue` Imports in TS

TypeScript cannot handle type information for `.vue` imports by default, so we replace the `tsc` CLI with `vue-tsc` for type checking. In editors, we need [Volar](https://marketplace.visualstudio.com/items?itemName=Vue.volar) to make the TypeScript language service aware of `.vue` types.

## Customize configuration

See [Vite Configuration Reference](https://vite.dev/config/).

## Project Setup

```sh
npm install
```

### Compile and Hot-Reload for Development

```sh
npm run dev
```

### Type-Check, Compile and Minify for Production

```sh
npm run build
```

### Lint with [ESLint](https://eslint.org/)

```sh
npm run lint
```
