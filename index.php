<?php 
  require('function.php');

    login_check();
  //DBの接続
  require('dbconnect.php');
 // ログインチェック



 



  // //ログインチェック
  // if(isset($_SESSION['id'])){
  //   //ログインしている
  // }else{
  //   //ログインしていない
  //   //ログイン画面へ飛ばす

  //   header("Location: login.php");
  //   exit();
  // }



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

// modifiedはtimestamp型なので勝手に日時が入る
      $sql = "INSERT INTO `tweets`(`tweet`,`member_id`,`reply_tweet_id`,`created`,`modified`) VALUES(?,?,-1,now(),now())";

       //SQL文実行
       // sha1　暗号化を行う関数

      // 変数を定義しない場合
      // $data = array($_POST['tweet'],$SESSION['member_id'],-1);
      $data = array($tweet,$member_id);
      $stmt = $dbh->prepare($sql);

      // var_dump($data); 
      $stmt->execute($data);


      // 自分の画面に移動する（データの再送防止）
      header("Location: index.php");

     }
   }

  

//----- ページング処理

$page = "";


//パラメータが存在していたらページ番号代入
if(isset($_GET["page"])){

  $page = $_GET["page"];
}else{
//存在しないときはページ１とする
  $page = 1;


}

//１以下のページ番号を入力されたら数字を１にする
//カンマ区切りで羅列された数字の中で最大の数字
$page = max($page,1);


//1ページ分の表示件数
  $page_row = 5;

//データの件数から最大ページ数を計算する
//AS `cnt`を使うとfetchしたデータから取り出したCOUNT(*)データが
$sql = "SELECT COUNT(*)  AS `cnt` FROM `tweets` WHERE `delete_flag`=0";
$page_stmt = $dbh->prepare($sql);
$page_stmt->execute();

$record_count = $page_stmt->fetch(PDO::FETCH_ASSOC);

// var_dump($record_count);
// exit;
// 小数点の繰り上げ

$all_page_number = ceil($record_count['cnt'] / $page_row);


//パラメータのページ番号が最大ページを超えていれば、強制的に最後のページとする。
//min カンマ区切りの数字の羅列の中から最小の数字を取得する。

$page = min($page,$all_page_number);



//$startは表示するデータの表示開始場所
  $start = ($page-1) * $page_row;




//--------



  //----表示用のデータ取得-----
  try{
    // ログインしている人の情報を取得
     $sql = "SELECT * FROM `members` WHERE `member_id`=".$_SESSION["id"];

     $stmt = $dbh->prepare($sql);
     $stmt->execute();

     $login_member = $stmt->fetch(PDO::FETCH_ASSOC);

     // 一覧用の情報を取得
     //テーブル結合
     //ORDER BY 最新順位並び替え
     // 論理削除に対応、delete_flag = 0のものだけ取得
    $sql = "SELECT `tweets`.*,`members`.`nick_name`,`members`.`picture_path` FROM `tweets` INNER JOIN `members` ON `tweets`.`member_id`=`members`.`member_id` WHERE `delete_flag`=0 ORDER BY `tweets`.`modified` DESC LIMIT ".$start.",".$page_row;

     $stmt = $dbh->prepare($sql);
     $stmt->execute();

     // 一覧表示用の配列を用意
     $tweet_list = array();
     // 複数行のデータを取得するためループ
     while (1) {
      $one_tweet = $stmt->fetch(PDO::FETCH_ASSOC);

      if($one_tweet == false){
       break;
     }else{
      // like数を求めるSQL文
      $like_sql = "SELECT COUNT(*)as `like_count` FROM `likes` WHERE `tweet_id`=".$one_tweet["tweet_id"];

      // SQL文実行
      $like_stmt = $dbh->prepare($like_sql);
      $like_stmt->execute();

      $like_number = $like_stmt->fetch(PDO::FETCH_ASSOC);

      // $one_tweetの中身
      // $one_tweet["tweet"]つぶやき
      // $one_tweet["member_id"]つぶやいた人のid
      // $one_tweet["nick_name"]つぶやいた人のニックネーム
      // $one_tweet["picture_path"]つぶやいた人のプロフィール画像
      // $one_tweet["modified"]つぶやいた日時

      //1行分のデータに新しいキーを用意して、like数を代入
      $one_tweet["like_count"] = $like_number["like_count"];

      // ログインしている人がLikeしているかどうかの情報を取得
      $login_like_sql = "SELECT COUNT(*) as `like_count` FROM `likes` WHERE `tweet_id`=".$one_tweet['tweet_id']." AND `member_id` =".$_SESSION["id"];

      // SQL文実行
      $login_like_stmt = $dbh->prepare($login_like_sql);
      $login_like_stmt->execute();


      // フェッチして取得
      $login_like_number = $login_like_stmt->fetch(PDO::FETCH_ASSOC);

      $one_tweet["login_like_flag"] = $login_like_number["like_count"];


      // データが取得できている
      $tweet_list[] = $one_tweet;
     }
    }


    //followingの数
    $following_sql ="SELECT COUNT(*) as `cnt` FROM `follows` WHERE `member_id` =".$_SESSION["id"];
    $following_stmt = $dbh->prepare($following_sql);
    $following_stmt->execute();
    $following = $following_stmt->fetch(PDO::FETCH_ASSOC);

    //Followerの数
    $follower_sql ="SELECT COUNT(*) as `cnt` FROM `follows` WHERE `follower_id` =".$_SESSION["id"];
    $follower_stmt = $dbh->prepare($follower_sql);
    $follower_stmt->execute();
    $follower = $follower_stmt->fetch(PDO::FETCH_ASSOC);




   }catch(Exception $e){

  }

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
      <div class="col-md-4 content-margin-top">
 

        <legend>ようこそ<?php echo $login_member["nick_name"]; ?>さん！</legend>
        <form method="post" action="" class="form-horizontal" role="form">
            <!-- つぶやき -->
            <div class="form-group">
              <label class="col-sm-4 control-label">つぶやき</label>
              <div class="col-sm-8">
                <!--  <input type="hidden" name="tweet_id" value=""> -->
                <textarea name="tweet" cols="50" rows="5" class="form-control" placeholder="例：Hello World!"></textarea>
                <?php if (isset($error) && ($error["tweet"] == "blank")) {?>
                <p class="error">なにかつぶやいてください</p>
                <?php } ?>

              </div>
            </div>
          <ul class="paging">
            <input type="submit" class="btn btn-info" value=つぶやく>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <?php if($page == 1){ ?>
                <li>前</li>
                <?php }else{ ?>

                <li><a href="index.php?page=<?php echo $page - 1; ?>" class="btn btn-default">前</a></li>
                <?php } ?>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php if($page == $all_page_number){ ?>
                 <li>次</li>
                <?php }else{ ?>
                <li><a href="index.php?page=<?php echo $page + 1; ?>" class="btn btn-default">次</a></li>
                <li>page <?php echo $page; ?>/<?php echo $all_page_number; ?></li>
                <?php } ?>
          </ul>
        </form>
      </div>

      <div class="col-md-8 content-margin-top">
        <div class="msg_header">
          <a href="follow.php">Followers<span class="badge badge-pill badge-default"><?php echo $follower["cnt"]; ?></span></a><a href="#">Following<span class="badge badge-pill badge-default"><?php echo $following["cnt"]; ?></span></a>

        </div>


      <?php foreach ($tweet_list as $one_tweet) {
        ?>

     <!--    くりかえすタグが書かれる場所 -->
        <div class="msg">
          <a href="profile.php?member_id=<?php echo $one_tweet["member_id"];?>">
          <img src="picture_path/<?php echo $one_tweet["picture_path"];?>" width="48" height="48">
          </a>
          <p>
            <?php echo $one_tweet["tweet"]; ?><span class="name"> ( <?php echo $one_tweet["nick_name"]; ?>) </span>
            [<a href="reply.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>">Re</a>]

            <?php if ($one_tweet["login_like_flag"] == 0){?>


             <a href="like.php?like_tweet_id=<?php echo $one_tweet["tweet_id"];?>&page=<?php echo $page; ?>"><i class="fa fa-thumbs-o-up" aria-hidden="true">Like</i><a> 
              <?php }else{?>
              <a href="like.php?unlike_tweet_id=<?php echo $one_tweet["tweet_id"];?>&page=<?php echo $page; ?>"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i>UnLike</a>
              <?php } ?>


              <?php if($one_tweet["like_count"] > 0){echo $one_tweet["like_count"];} ?></i>
          </p>

          
          <p class="day">
            <a href="view.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>">
               <?php 
                $modify_date = $one_tweet["modified"];
                //strtotime 文字型のデータを日時型に変換できる
                // 年月日を入れることもできる
                $modify_date = date("Y-m-d H:i",strtotime($modify_date));

               echo $modify_date;

               ?>
            </a>
            <?php if($_SESSION["id"] == $one_tweet["member_id"]){ ?>
            [<a href="edit.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>" style="color: #00994C;">編集</a>]
            [<a onclick="return confirm('削除します、よろしいですか？');" href="delete.php?tweet_id=<?php echo $one_tweet["tweet_id"]; ?>" style="color: #F33;">削除</a>]
            <?php }?>
            <?php if($one_tweet["reply_tweet_id"] > 0){ ?>
            [<a href="view.php?tweet_id=<?php echo $one_tweet["reply_tweet_id"]; ?>" style="color: #a9a9a9;">返信元のメッセージを表示</a>]
            <?php } ?>

          </p>
        </div>

      <?php
      } 
      ?>

      </div>

    </div>
  </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="assets/js/jquery-3.1.1.js"></script>
    <script src="assets/js/jquery-migrate-1.4.1.js"></script>
    <script src="assets/js/bootstrap.js"></script>
  </body>
</html>
