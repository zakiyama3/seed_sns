<?php

$tweet_id = htmlspecialchars($_POST["tweet_id"]);
$tweet = htmlspecialchars($_POST["tweet"]);
$member_id = htmlspecialchars($_POST["member_id"]);
$reply_tweet_id = htmlspecialchars($_POST["reply_tweet_id"]);
$created = htmlspecialchars($_POST["created"]);
$modified = htmlspecialchars($_POST["modified"]);



// $created = htmlspecialchars($_POST["created"]);

  require('dbconnect.php');




  $sql = $sql = "INSERT INTO `tweets`(`tweet_id`,`tweet`,`member_id`,`reply_tweet_id`,`created`,`modified`) VALUES(?,?,?,-1,now(),now())";



// $created = now();

$stmt = $dbh->prepare($sql);
var_dump(); 
$stmt->execute();
$dbh = null;



header('location: index.php');


?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>セブ掲示版</title>
</head>
<body>


      <input type="hidden" name="id" value="<?php echo $_POST["tweet_id"] ?>">

      <input type="hidden" name="nickname" value="<?php echo $_POST["tweet"] ?>">

      <input type="hidden" name="comment" value="<?php echo $_POST["member_id"] ?>">

        <input type="hidden" name="created" value="<?php echo $_POST["reply_tweet_id"] ?>">

      <input type="hidden" name="comment" value="<?php echo $_POST["created"] ?>">

        <input type="hidden" name="created" value="<?php echo $_POST["modified"] ?>">



</body>
</html>
