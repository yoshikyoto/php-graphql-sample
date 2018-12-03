# PHP GraphQL Server Sample

## これは何？

PHPでGraphQLのサーバーを立てるときのサンプルです。

## GraphQLとは

Facebookが提唱した、REST APIに変わる新しいAPIの形です

http://graphql.org/

GitHubやInstagramでも採用されています。

### Graph Query Language

GraphQLはGraph Query Languageの略です。
Query Languageという名前の通り、どちらかと言えばSQLのようなものに近く、
従来のREST APIとはかなり異なるものです。

### 従来のREST API

REST APIは、**あるエンドポイントに対してリクエストを送ると決まったレスポンスが返ってくる**というものでした。
しかし、これには問題点がありました。

* 欲しいリソースが多数ある場合、その数だけAPIを叩かなければならない。
* あるエンドポイントから得られる情報のうち、一部の情報だけが欲しくても、全部取得してくるしか無い。不要にネットワークの帯域を使ってしまったり、不要なクエリがDBに流れたりするケースがある。

### これを解決するためのGrahpQL

GraphQLのエンドポイントは1つです。そのエンドポイントにたいしてクエリを投げることで、レスポンスとしてクエリの実行結果が返ってきます。

GraphQLはフロントAPI向けの枠組みであると僕は思っています。

例えば、twitterを例にします。タイムラインを取得したい場合のクエリとして以下のようなものが考えられます。

```
timeline {
  text
  createdAt
  user {
    icon
    screenName
  }
}
```

タイムラインを表示するのであればこういう感じです。レスポンスはこうなるイメージです。

```
{
  "timeline":[
    {
      "test":"ツイートだよ",
      "createdAt":"2018-04-13 12:34:56",
      "user":{
        "icon":"http://twitter.com/icon.jpg",
        "screenName":"yoshikyoto"
      }
    },
    ...
  ]
}
```


本文、投稿日時、アイコン、ユーザー名があればタイムラインが表示可能です。

いっぽうでユーザーページを表示するならこうです。

```
user {
  screenName
  icon
  profile
  location
  tweet(limit:20) {
    text
    createdAt
  }
}
```

ユーザーのscreenNameなどの基本的な情報とツイートを20件取得してくるというクエリです。レスポンスはこういう感じです。

```
{
  "user": {
    "screenName":"yoshikyoto",
    "icon":"http://twitter.com/icon.jpg",
    "profile":"yoshikyotoです。エンジニアです。",
    "location":"京都",
    "tweet": [
      {
        "text":"はじめてのツイート",
        "createdAt":"..."
      },
      ...
    ]
  }
}
```

このようにクエリに対応した要素だけレスポンスに含まれるようになります。
一方でサーバーサイドは、投げられたクエリに対して結果を返すだけになりますのでシンプルになります。

### GraphQLのメリット

* APIを1回叩けば必要な情報を全て手に入れることができる（Reactと相性が良い）。
* 不要なプロパティを取らないようにすることができるのでサーバーサイドの負荷が下がる。

### GraphQLの欠点

* 普及率の問題。普及率がまだ高くは無いのでベストプラクティスのような知見が少ない。エコシステムも発展途上。
* ユーザーに露出しているフロントAPIにもかかわらず、クエリが柔軟なのでどれだけ重いクエリでも投げられる（可能性がある）。
* エンドポイントが1つだけ、HTTPメソッドがPOSTだけなので、従来のようにApacheアクセスログなどを使ったモニタリングなどはできない。
* n+1問題など、考えるべき問題がいくつかある。


## Dependency

* PHP 5.6 or 7
* https://github.com/webonyx/graphql-php
  * Document: http://webonyx.github.io/graphql-php/data-fetching

## 動作方法

* `composer install`
* Apacheなどを使ってindex.phpにアクセスできるようにします


## curlでリクエストしてみる

* 例えば、http://localhost:8080/graphql/ でindex.phpにアクセスできるとします。
* `application/json` 形式でPOSTします。
* jsonの`query`キーの値がGraphQLのクエリになります。
* HTTPメソッドは常にPOSTになります

[PHPビルトインサーバー](http://php.net/manual/ja/features.commandline.webserver.php)で動かしてみます。

```
cd public
php -S localhost:8080
```

id:1のユーザーのidとnameを取得するクエリは以下の通りです。

```
curl -X POST -H "Content-Type: application/json" "http://localhost:8080/graphql/" -d '{"query": "query { user(id: 1){id name} }"}'
{"data":{"user":{"id":1,"name":"Sakamoto"}}}
```

id:2のユーザーのidとnameを取得する場合は以下です。

```
curl -X POST -H "Content-Type: application/json" "http://localhost:8080/graphql/" -d '{"query": "query { user(id: 1){id name} }"}'
{"data":{"user":{"id":1,"name":"Sakamoto"}}}
```

id:2のユーザーのnameのみを取得する場合は以下です。

```
$ curl -X POST -H "Content-Type: application/json" "http://localhost:8080/graphql/" -d '{"query": "query { user(id: 1){name} }"}'
{"data":{"user":{"name":"Sakamoto"}}}
```

レスポンスがnameだけになりました。このように、必要なフィールドだけ取得できるのが特徴です。
今回の例の場合は余り恩恵がないですが、もう少しクエリが複雑になってくると、無駄なDB負荷を減らしたりできます。

## 実装

* `public/index.php` がエントリーポイントです
* `src` 以下がロジックになります

### index.php

ルーティングとコントローラー的な役割を担うクラスです。

GraphQLでは参照系はquery、更新系はmutationというメソッド（？）になります。

### UtakataQL\Type\ 以下

`GraphQL\Type\Definition\ObjectType` に`ObjectType::string()`や`ObjectType::int()`などの基本的な型がありますが、Userなどのドメインに依存した型を似たような形で`DomainType::user()`のように呼ぶためのクラスが`DomainType`クラスです。

`User`クラスの定義本体は`UtakataQL\Type\User\User`にあります。

### UtakataQL\Repository\UserRepository

`Repository`ディレクトリ以下は、（DDDでいう）ドメインロジックのようなものを考えてもらえれば良いです。
`UserRepository`は配列に突っ込んだダミーデータを返しますが、
本当はここでDBにアクセスしたり他のAPIにアクセスしたりします。

`UtakataQL\Type\User\User`からこの`UserRepository`が呼ばれておりますが、`UserRepository`が実際のデータの取得などを行い、`Type\User\User`クラスはGraphQLのクエリとデータの対応付のようなことを行っています。

## n+1問題

以下のようなクエリを投げるとします。

```
curl -X POST -H "Content-Type: application/json" "http://localhost:9080/graphql/" -d '{"query": "query { user(id: 1){name address followers{id name followers{id name followers{id name}}}} }"}'
```

結果はこうなります。

```
{"data":{"user":{"name":"Sakamoto","address":"\u4eac\u90fd\u5e9c","followers":[{"id":2,"name":"Sato","followers":[{"id":3,"name":"Tanaka","followers":[{"id":1,"name":"Sakamoto"}]}]}]}}}
```

ここで、`UserRepository`の`getUser`にログを仕込んであるので、ログを見てみます。

```
2018-05-01 18:58:55	UtakataQL\Repository\UserRepository::getUser
2018-05-01 18:58:55	UtakataQL\Repository\UserRepository::getUser
2018-05-01 18:58:55	UtakataQL\Repository\UserRepository::getUser
2018-05-01 18:58:55	UtakataQL\Repository\UserRepository::getUser
2018-05-01 18:58:55	UtakataQL\Repository\UserRepository::getUser
```

リポジトリの`getUser`メソッドが5回呼ばれていることがわかります。
