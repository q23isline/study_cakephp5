# https://hub.docker.com/_/node/tags
FROM node:22.12

# タイムゾーンを日本にする
ENV TZ=Asia/Tokyo
# ローカル開発用なので development に指定してライブラリたちのログ出力などを詳細にする
# https://nodejs.org/en/learn/getting-started/nodejs-the-difference-between-development-and-production
ENV NODE_ENV=development

WORKDIR /src
