<?php 



require('function.php');

// ログインチェック
login_check();



  // 宿題:個別ページの表示を完成させましょう

// ヒント:$_GET["tweet_id"]の中に、表示したいつぶやきのtweet_idが格納されている
// ヒント２：送信されてるtweet_idを使用して、SQL文でDBからデータを1件取得
// ヒント３：取得できたデータを、一覧の１行分の表示を参考に表示してみる。

 require('dbconnect.php');

  //----POST送信されていたら、つぶやきをINSERTで保存
  //$_POST["tweet"]=>"" $_POSTが空だと思われない
  //$_POST["tweet"]=>"" $_POST["tweet"]が空だと認識される

  if (isset($_POST) && !empty($_POST)){

     if ($_POST["tweet"] == ""){
      $error["tweet"] = "blank";
    }


  // $_POSTはボタンを押す等のアクションが必要
  $tweet = $_POST['tweet'];

  // 特にアクションがなくても保持して
  $member_id = $_SESSION['id'];

  //認証処理
      if (!isset($error)){

//reply_tweet_idに返信元のtweet_idが入る
// modifiedはtimestamp型なので勝手に日時が入る
      $sql = "INSERT INTO `tweets`(`tweet`,`member_id`,`reply_tweet_id`,`created`,`modified`) VALUES(?,?,?,now(),now())";

       //SQL文実行
       // sha1　暗号化を行う関数

      // 変数を定義しない場合
      // $data = array($_POST['tweet'],$SESSION['member_id'],-1);
      $data = array($tweet,$member_id,$_GET['tweet_id']);
      $stmt = $dbh->prepare($sql);

      var_dump($data); 
      $stmt->execute($data);


      // 一覧へ移動する
      header("Location: index.php");

     }
   }



$sql = "SELECT `tweets`.*,`members`.`nick_name`,`members`.`picture_path` FROM `tweets` INNER JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `tweets`.`tweet_id`=".$_GET["tweet_id"];

$stmt = $dbh->prepare($sql);
$stmt->execute();


$one_tweet = $stmt->fetch(PDO::FETCH_ASSOC);

$reply_msg = "@".$one_tweet["tweet"]."(".$one_tweet['nick_name'].")";



 ?>



<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SeedSNS</title>

    <!-- Bootstrap -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/css/form.css" rel="stylesheet">
    <link href="assets/css/timeline.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">

  </head>
  <body>
  <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
          <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header page-scroll">
              <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="index.php"><span class="strong-title"><i class="fa fa-twitter-square"></i> Seed SNS</span></a>
          </div>
          <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">ログアウト</a></li>
              </ul>
          </div>
          <!-- /.navbar-collapse -->
      </div>
      <!-- /.container-fluid -->
  </nav>

  <div class="container">
    <div class="row">
      <div class="col-md-6 col-md-offset-3 content-margin-top">
        <h4>つぶやきに返信しましょう</h4>
        <div class="msg">
          <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやきに返信</label>
              <div class="col-sm-8">
                <!--  <input type="hidden" name="tweet_id" value=""> -->
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"><?php echo $reply_msg; ?></textarea>
                <?php if (isset($error) && ($error["tweet"] == "blank")) {?>
                <p class="error">なにかつぶやいてください</p>
                <?php } ?>

              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value=返信としてつぶやく>

          </ul>
        </form>
        </div>

        <a href="index.php">&laquo;&nbsp;一覧へ戻る</a>
      </div>
    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
