## 制作アプリのタイトル
プロ野球チーム成績ダッシュボード

## 制作アプリの説明（40文字程度）
プロ野球パ・リーグの試合結果を入力し、結果一覧とチーム別の成績をダッシュボード風に表示するアプリ

## 工夫した点・こだわった点
- 今回はChart.jsというライブラリを使って、データをグラフ化することを試みた。
勝率の円グラフなどグラフ化する意味はあまりないが、
いろんな種類のグラフを試してみたかったため、使用したもの。
- 散布図でチームラベルの表示は標準機能ではできなかったので、
オプションのプラグインを入れてカスタマイズした。
- PHPに関してはまだまだこれから何度もコードを書いて慣れていく必要がある。
var_dumpでの表示もjsのコンソールと比べると見にくいので慣れが必要。
配列の中身がどうなっているのかがわかりにくかった。

## 次回トライしたこと（または機能）
配列の処理の仕方、while、foreachなど、とにかくPHPに慣れていきたい。

## 備考（感想、シェアしたいこと等なんでも）
- サンプルデータが作りやすかったので、今回はプロ野球のデータ集計で作ってみました。
- PHPになってから、CSSが効かないことがたびたび発生し時間を取りました。
原因はキャッシュが残ることで、キャッシュクリアすれば解決しましたが、無駄な時間をとりました。
- Chart.jsは綺麗なグラフを作ってくれるので非常にいいですね！
今回初めて使ってみましたが、非常に使いやすいかったです。
- PHPやサーバ関係、デプロイなどまだまだ理解不足なので、積み重ねて慣れていきたいです。