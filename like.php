<?php 

  session_start();

  //DBの接続
  require('dbconnect.php');


// likeボタンが押されたとき
  if(isset($_GET["like_tweet_id"])){
    // like関数を引っ張ってきている
    like($_GET["like_tweet_id"],$_SESSION["id"],$_GET["page"]);


    // // like情報をlikeテーブルに登録
    // $sql = "INSERT INTO `likes` (`tweet_id`, `member_id`) VALUES (".$_GET["like_tweet_id"].", ".$_SESSION["id"].");";

    // // SQL実行
    //  $stmt = $dbh->prepare($sql);
    //  $stmt->execute();

    //       // 一覧ページへもどる
    //  header("Location: index.php");

  }


// umlikeボタンが押されたとき
  if(isset($_GET["unlike_tweet_id"])){
    unlike($_GET["unlike_tweet_id"],$_SESSION["id"]);

    // 登録されているlike情報をumlikeテーブルから削除
    // $sql = 'DELETE FROM `likes` WHERE tweet_id='.$_GET["unlike_tweet_id"].' AND member_id='.$_SESSION["id"];

    // // SQL実行
    //  $stmt = $dbh->prepare($sql);
    //  $stmt->execute();



    //  // 一覧ページへもどる
    //  header("Location: index.php");

  }


  //like関数
  //引数 like_tweet_id,login_member_id
  function like($like_tweet_id,$login_member_id,$page){
    // 関数の中に書く
     require('dbconnect.php');
         // like情報をlikeテーブルに登録
    $sql = "INSERT INTO `likes` (`tweet_id`, `member_id`) VALUES (".$like_tweet_id.", ".$login_member_id.");";

    // SQL実行
     $stmt = $dbh->prepare($sql);
     $stmt->execute();

          // 一覧ページへもどる
     header("Location: index.php?page=".$page);


  }

 //引数 like_tweet_id,login_member_id
  function unlike($unlike_tweet_id,$login_member_id,$page){

    require('dbconnect.php');
        // 登録されているlike情報をumlikeテーブルから削除
    $sql = 'DELETE FROM `likes` WHERE tweet_id='.$unlike_tweet_id.' AND member_id='.$login_member_id;

    // SQL実行
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     header("Location: index.php?page=".$page);

  }

 ?>