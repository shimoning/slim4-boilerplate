# A Boilerplate of Slim Framework 4

## Extensions
* `.env`
* Twig
* Migration
* Seeder
* PDO repository
* CSRF Prevention
* Email


## Dotenv
`.env.example` をコピーして `.env` ファイルを作成します。
`.env` に書かれている key=value がそのまま `$_ENV['key'] = value` として取得できるようになります。


## Migration & Seeder
[`robmorgan/phinx`](https://github.com/cakephp/phinx) を利用しています。
初期化済みなので、そのまま使えます。

### Directories
それぞれファイルの格納場所は以下です。

* マイグレーション: `database/migrations`
* シーダー: `database/seeds`

### マイグレーションファイル作成
```cli
composer migrate:create
```

### マイグレーション実行
```cli
composer migrate:run
```
### マイグレーションロールバック
```cli
composer migrate:rollback
```

### シーダーファイル作成
```cli
composer seed:create
```

### シーダー実行
```cli
composer seed:run
```

## Twig
ビュー用のテンプレートエンジンに Twig を採用しています。
書き方などは [公式サイト](https://twig.symfony.com/) を参照してください。

### Directories
テンプレートのディレクトリ構成は以下のようにしてあります。
好みで変更してください。

* レイアウト: `templates/layouts`
* 各ページ: `templates/pages`
* 再利用可能なパーツ: `templates/elements`

### Action での使い方
`App\Application\Actions\Action` クラスを継承していれば、 `return $this->render('template-name', $args)` という形で利用できます。
`router` 内でやる場合は各自 `Twig` を DI してください。

## CSRF 対策
フォームを設置するページのテンプレートの form タグの配下で、以下のようにエレメントを呼び出してください。

```
{% include 'elements/csrf/inputs.twig' %}
```
