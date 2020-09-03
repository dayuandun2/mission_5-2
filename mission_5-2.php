<!DOCTYPE html>
<html lang="ja">
 <head>
 <meta charset="utf-8">
 <title>mission_5-1</title>
</head>
<?php

// DB接続設定
//$dsnの式の中にスペースを入れない！
 $dsn='mysql:dbname=データベース名;host=localhost';
 $user='ユーザー名';
 $password='パスワード';
 $pdo = new PDO($dsn, $user, $password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

	//データベース内にテーブルを作成
 $sql = "CREATE TABLE IF NOT EXISTS tbtest"
 ." ("
 . "id INT AUTO_INCREMENT PRIMARY KEY,"
 . "name char(32),"
 . "comment TEXT,"
 . "newpass char(32),"
 . "time TEXT"
 .");";
 $stmt = $pdo->query($sql);
	//編集対象番号
 if(isset($_POST['editnumber'])){
	$enum = $_POST['editnumber'];
 }
 	//編集時パスワード
 if(isset($_POST['pass3'])){
	 $editpass = $_POST['pass3'] ;
 }
 	//新規パスワード
 if(isset($_POST['pass1'])){
	$newpass = $_POST['pass1'] ;
 }
 
 //入力されているデータレコードの内容をHTMLに表示
 if(!empty($enum)&&empty($ednum)&&!empty($editpass)){
	  //$rowの添字（[ ]内）は、「テーブル作成」で作成したカラムの名称に併せる
	  //$rowの中にはテーブルのカラム名が入る
	  $sql = 'SELECT * FROM tbtest';
	  $stmt = $pdo->query($sql);
	  $results = $stmt->fetchAll();
		//新規パスワードと合わせたい if文の条件を$editpass==$row['newpass']で出来た！！
	  foreach ($results as $row){
			if($editpass==$row['newpass']&&$enum==$row['id']){
		  $ednum = $row['id'];
      $nameget = $row['name'];
		  $comget = $row['comment'];
		 }
   }
	}
	?>
<body>
 <form action="" method="post">
  <input type ="name" name = "names" value="<?php if(isset($nameget)){echo $nameget;} ?>"placeholder ="名前"><br>
  <input type ="text" name="comment" value="<?php if (isset($comget)){echo $comget ;}?>"placeholder="コメント">
  <input type="hidden" name="edit" value="<?php if (isset($ednum)){echo $ednum;} ?>" placeholder="編集用"><br>
  <input type="password" name="pass1" value="" placeholder="パスワード">
  <input type="submit" name="submit"><br><br>
  <input type="text" name="deletenumber" value="" placeholder="削除対象番号"><br>
  <input type="password" name="pass2" value="" placeholder="パスワード">
  <input type="submit" value="削除"><br><br>
  <input type="text" name="editnumber" value="" placeholder="編集対象番号"><br>
  <input type="password" name="pass3" value="" placeholder="パスワード">
  <input type="submit" value="編集">
 </form>

 <?php
 //名前
  if(isset($_POST['names'])){
		$nam = $_POST['names'];
	}
	//コメント
	if(isset($_POST['comment'])){
		$com = $_POST['comment'];
	} 
  //削除対象番号
	if(isset($_POST['deletenumber'])){
		$dnum = $_POST['deletenumber'];
	} 
	//編集対象番号
	if(isset($_POST['editnumber'])){
		$enum = $_POST['editnumber'];
	}
	//新規パスワード
  if(isset($_POST['pass1'])){
	  $newpass = $_POST['pass1'] ;
	}
	//削除時パスワード
	if(isset($_POST['pass2'])){
	  $dpass = $_POST['pass2'];
	}
	//編集時パスワード
	if(isset($_POST['pass3'])){
		$editpass = $_POST['pass3'] ;
	}
	//id番号出力(編集時使用)
	if(isset($_POST['edit'])){
		$ednum = $_POST['edit'] ;
	}

	$postdate =date('Y-m-d H:i:s') ;//date関数の中身を('Y-m-d H:i:s')の様にすることで解決

	//入力されているデータレコードの内容を編集
	//bindParamの引数（:nameなど）は「テーブル作成」でどんな名前のカラムを設定したかで変える必要がある。
	if(!empty($nam)&& !empty($com)&& !empty($ednum)&&!empty($newpass)){
		 //編集したい番号 	$ednum;
		 $sql = 'SELECT * FROM tbtest';
		 $stmt = $pdo->query($sql);
		 $results = $stmt->fetchAll();
		 foreach ($results as $row){
			if($ednum==$row['id']){
	     $sql = 'UPDATE tbtest SET name=:name,comment=:comment,time=:time,newpass=:newpass WHERE id=:id';
	     $stmt = $pdo->prepare($sql);
	     $stmt->bindParam(':name', $nam, PDO::PARAM_STR);
	     $stmt->bindParam(':comment', $com, PDO::PARAM_STR);
		   $stmt->bindParam(':time', $postdate, PDO::PARAM_STR);
		   $stmt->bindParam(':id', $ednum, PDO::PARAM_INT);
		   $stmt->bindParam(':newpass',$newpass, PDO::PARAM_STR);
			 $stmt->execute();
			}
		 }
	}

	//新規送信(データ入力)
	elseif (!empty($nam)&& !empty($com)&&empty($ednum)&&!empty($newpass)){
	 $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, time ,newpass) VALUES (:name, :comment,:time,:newpass)");
	 $sql -> bindParam(':name', $nam, PDO::PARAM_STR);
	 $sql -> bindParam(':comment', $com, PDO::PARAM_STR);
	 $sql -> bindParam(':time',$postdate, PDO::PARAM_STR);
	 $sql -> bindParam(':newpass',$newpass, PDO::PARAM_STR);
	 $sql -> execute();
	}

		//入力したデータレコードを削除
	elseif(!empty($dnum)&&!empty($dpass)){	
		$sql = 'SELECT * FROM tbtest';
	  $stmt = $pdo->query($sql);
	  $results = $stmt->fetchAll();
		foreach ($results as $row){
		 if($dpass==$row['newpass']){//削除時パスワード照合 ループさせることで解決！！
	    //削除したい番号 	$dnum;
		  $sql = 'delete from tbtest where id=:id';
	    $stmt = $pdo->prepare($sql);
		  $stmt->bindParam(':id', $dnum, PDO::PARAM_INT);
			$stmt->execute();
		 }
	  }
	}
	//データを取得し、表示
	
	//$rowの添字（[ ]内）は、「テーブル作成」で作成したカラムの名称に併せる
	//$rowの中にはテーブルのカラム名が入る
	$sql = 'SELECT * FROM tbtest';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['time'].'<br>';
  	echo "<hr>";
	}
	
?>

</body>
</html>
