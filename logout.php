<?php 
  session_start();

  //セッションの中身を空の配列で上書きする
  $_SESSION = array();

  //セッション情報を有効期限切れにする。
  //サーバー側のセッション情報を削除
  if(ini_get("session.use_cookies")){
    $params = session_get_cookie_params();
    setcookie(session_name(),'',time() - 42000,$params['path'],$params['domain'],$params['secure'],$params['httponly']);

  }

  //セッション情報の破棄
  session_destroy();

  
  //Cookie情報も削除
  //ブラウザのクッキー情報を削除
  setcookie('email','',time( )- 3000);
  setcookie('password','',time() - 3000);

  //ログイン後の画面に戻る
  header("Location: index.php");
  exit();

  //ログイン後の画面に、ログインチェックの機能を実装する
  //ログイン後の画面に行くことによってしっかりログアウトしていることを確認できる
  


 ?>