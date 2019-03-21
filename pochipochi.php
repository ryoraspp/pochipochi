<?php
// WordPress用の関数をここでも使いたいから読み込む
require_once( dirname( __FILE__ ) . '/wp-load.php' );
$getasin = $_GET["asin"];
$getkw = $_GET["kw"];
// エンドポイントのホスト、パスをそれぞれ変数に格納
$host = 'ecs.amazonaws.jp';
$path = '/onca/xml';
 
// リクエストURLのパラメータを配列で指定
$params = array(
    // XXXXXXXXの箇所にアクセスキーIDを指定。
    'AWSAccessKeyId' => 'XXXXXXXX',
    // XXXXXXXXの箇所にアソシエイトタグを指定
    'AssociateTag' => 'XXXXXXXX-22',
    // Product Advertising API のサービスを指定
    'Service' => 'AWSECommerceService',
    // 実行するProduct Advertising APIの種類を指定
    'Operation' => 'ItemLookup',
    // XXXXXXXXの箇所に商品IDを指定。複数の場合はをカンマで区切って指定（最大10）
    "ItemId" => $getasin,
    // 返されるデータのグループを指定。複数の場合はをカンマで区切って指定
    'ResponseGroup' => 'Small,Images',
    // タイムスタンプを指定
    'Timestamp' => gmdate('Y-m-d\TH:i:s\Z')
);
 
//楽天アフィリエイトID
  $rakuten_affiliate_id = 'XXXXXXXX';
  //Yahoo!バリューコマースSID
  $sid = 'XXXXXXXX';
  //Yahoo!バリューコマースPID
  $pid = 'XXXXXXXX';
// 上記配列のキーを英数字でソート
ksort($params);
 
// パラメータの配列を結合
$parameter = '';
foreach ($params as $key => $value) {
    $parameter .= $key . '=' . rawurlencode($value) . '&';
}
// パラメータに不要な最後の文字列&を削除
$parameter = rtrim($parameter, '&');
 
// 署名を作成。XXXXXXXXの箇所には「シークレットキー」を指定
$secret_key = 'XXXXXXXX';
$signature = "GET\n" . $host . "\n" . $path . "\n" . $parameter;
$signature = hash_hmac('sha256', $signature, $secret_key, true);
$signature = rawurlencode(base64_encode($signature));
 
// リクエストURLを作成
$request_url = 'http://' . $host . $path . '?' . $parameter . '&Signature=' . $signature;

//request_urlをdump
var_dump($request_url);

// XML形式の商品データをオブジェクトに変換
$xml = simplexml_load_file($request_url);

// 使用する商品データを取得して変数に格納
$item = $xml->Items->Item;
$page_url = $item->DetailPageURL;
$attributes = $item->ItemAttributes;
$image_url = $item->MediumImage->URL;
$title = $attributes->Title;
 
// 各種アフィリエイトのキーワード検索用URLをそれぞれの変数に入れる
$amazon_url = 'https://www.amazon.co.jp/gp/search?keywords='.$getkw.'&tag='.$params['AssociateTag'];
$rakuten_url = 'https://hb.afl.rakuten.co.jp/hgc/'.$rakuten_affiliate_id.'/?pc=https%3A%2F%2Fsearch.rakuten.co.jp%2Fsearch%2Fmall%2F'.$getkw.'%2F-%2Ff.1-p.1-s.1-sf.0-st.A-v.2%3Fx%3D0%26scid%3Daf_ich_link_urltxt%26m%3Dhttp%3A%2F%2Fm.rakuten.co.jp%2F';
$yahoo_url = 'https://ck.jp.ap.valuecommerce.com/servlet/referral?sid='.$sid.'&pid='.$pid.'&vc_url=http%3A%2F%2Fsearch.shopping.yahoo.co.jp%2Fsearch%3Fp%3D'.$getkw;
?>
<html>
<head>
    <title>ポチポチ</title>
</head>
<body>
<h1>ポチポチ</h1>
じゅん.さおとめらいふがカエレバと同じようにリンクを作るためだけに作ったページ
<h2>使い方</h2>
まずは、このブックマークレットをブラウザに保存する。<br>
<!-- これがブックマークレットの本体。URL部分はphpファイルを置いたパスに変更する必要がある。 -->
<a href="javascript:(function(){var nakami;$nakami=location.pathname.replace(/.*\/(ASIN|dp|product|aw\/d)\/([0-9A-Z]+)\/.*/,'$2');window.open('https://ryo.nagoya/pochipochi.php?asin='+$nakami+'&kw='+window.getSelection().toString())})();">ポチポチブックマークレット</a><br>
<a href="javascript:(function(){var nakami;$nakami=location.pathname.replace(/.*\/(ASIN|dp|product|aw\/d)\/([0-9A-Z]+)\/.*/,'$2');window.open('https://ryo.nagoya/pochiyome.php?asin='+$nakami+'&kw='+window.getSelection().toString())})();">ポチポチkindleブックマークレット</a><br>
あとは、Amazonの商品ページで、楽天とYahoo!のキーワードにするキーワードを選択した状態でブックマークレットを実行するだけ。<br>
 
<div class="cstmreba">
<div class="kaerebalink-box">
<div class="kaerebalink-image"><a href="<?php echo esc_url($page_url); ?>" target="_blank" ><img src="<?php echo esc_url($image_url); ?>" style="border: none;" /></a></div>
<div class="kaerebalink-info">
<div class="kaerebalink-name"><a href="<?php echo esc_url($page_url); ?>" target="_blank" ><?php echo esc_html($title); ?></a></p>
<div class="kaerebalink-powered-date">posted with <a href="https://ryo.nagoya/pochipochi.php" rel="nofollow" target="_blank">ポチポチ</a></div>
</div>
<div class="kaerebalink-link1">
<div class="shoplinkamazon"><a href="<?php echo esc_html($amazon_url); ?>" target="_blank" >Amazon</a></div>
<div class="shoplinkrakuten"><a href="<?php echo esc_html($rakuten_url); ?>" target="_blank" >楽天市場</a></div>
<div class="shoplinkyahoo"><a href="<?php echo esc_html($yahoo_url); ?>" target="_blank" >Yahoo!ショッピング</a></div>
</div>
</div>
<div class="booklink-footer"></div>
</div>
</div>
<textarea onclick="this.select()">
<div class="cstmreba">
<div class="kaerebalink-box">
<div class="kaerebalink-image"><a href="<?php echo esc_url($page_url); ?>" target="_blank" ><img src="<?php echo esc_url($image_url); ?>" style="border: none;" /></a></div>
<div class="kaerebalink-info">
<div class="kaerebalink-name"><a href="<?php echo esc_url($page_url); ?>" target="_blank" ><?php echo esc_html($title); ?></a></p>
<div class="kaerebalink-powered-date">posted with <a href="https://jun3010.me/pochipochi-php-15585.html" rel="nofollow" target="_blank">ポチポチ</a></div>
</div>
<div class="kaerebalink-link1">
<div class="shoplinkamazon"><a href="<?php echo esc_html($amazon_url); ?>" target="_blank" >Amazon</a></div>
<div class="shoplinkrakuten"><a href="<?php echo esc_html($rakuten_url); ?>" target="_blank" >楽天市場</a></div>
<div class="shoplinkyahoo"><a href="<?php echo esc_html($yahoo_url); ?>" target="_blank" >Yahoo!ショッピング</a></div>
</div>
</div>
<div class="booklink-footer"></div>
</div>
</div></textarea>
</body>
</html>