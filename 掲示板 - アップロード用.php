<?php
//DB接続設定
	$dsn = "データベース名";
	$user = "ユーザー名";
	$password = "パスワード";
//エラー発生発見コード
	$pdo = new PDO($dsn, $user, $password, 
	    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
//テーブル作成    
	$sql = "CREATE TABLE IF NOT EXISTS tbtest"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);
	
 //パスワード判定
    $FLG=0;//判定フラグを初期化
    if(!empty($_POST["del_pass"]) || !empty($_POST["edit_pass"])){
        if(!empty($_POST["del_pass"])){  
            $pass = $_POST["del_pass"];
        }elseif(!empty($_POST["edit_pass"])){
            $pass = $_POST["edit_pass"];
        }
        $sql = "SELECT * FROM tbtest WHERE pass=:pass";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":pass", $pass, PDO::PARAM_STR);
        $stmt->execute();
        $results = $stmt->fetchAll();
        foreach($results as $row){
            if($row["pass"]==$pass){
                $FLG = 1;
            }
        }
    }
	
//データ削除
    if(!empty($_POST["del"]) && $FLG==1){
        $id = $_POST["del"];
	    $sql = "DELETE FROM tbtest WHERE id=:id";
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
	    $stmt->execute();
    }
    
//データ編集情報取得
    if(!empty($_POST["edit"]) && $FLG==1){
        $id = $_POST["edit"];
        $sql = "SELECT * FROM tbtest WHERE id=:id";
	    $stmt = $pdo->prepare($sql);
	    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
	    $stmt->execute();
	    $results = $stmt->fetchAll();
	    foreach($results as $row){
            $editid = $row["id"];
            $editname = $row["name"];
            $editcom = $row["comment"];
            $editpass = $row["pass"];
	    }
    }    
	
//投稿
    if(!empty($_POST["com"])){
        if(empty($_POST["editid"])){//編集番号が指定されていない（新規投稿）
            $sql = $pdo->prepare("INSERT INTO 
                tbtest (name, comment, date, pass) 
                VALUES (:name, :comment, :date, :pass)");
	        $sql->bindParam(":name", $name, PDO::PARAM_STR);
	        $sql->bindParam(":comment", $comment, PDO::PARAM_STR);
	        $sql->bindParam(":date", $date, PDO::PARAM_STR);
	        $sql->bindParam(":pass", $pass, PDO::PARAM_STR);
	        if(!empty($_POST["name"])){
	            $name = $_POST["name"];
	        }else{//名前欄が空白の場合、名無しを設定
	            $name = "名無し";
	        }
	        $comment = $_POST["com"];
	        $date = date("Y/m/d H:i:s");
	        $pass = $_POST["pass"];
	        $sql->execute();
        }else{//編集番号がしていされている（編集投稿）
            $id = $_POST["editid"]; 
	        $name = $_POST["name"];
	        $comment = $_POST["com"];
	        $date = date("Y/m/d H:i:s");
	        $pass = $_POST["pass"];
	        $sql = "UPDATE tbtest SET 
	                name=:name,comment=:comment,date=:date,pass=:pass 
	                WHERE id=:id";
	        $stmt = $pdo->prepare($sql);
	        $stmt->bindParam(":name", $name, PDO::PARAM_STR);
	        $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
	        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
	        $stmt->bindParam(":date", $date, PDO::PARAM_STR);
	        $stmt->bindParam(":pass", $pass, PDO::PARAM_STR);
	        $stmt->execute();
        }
    }
//投稿内容表示
    $sql = "SELECT * FROM tbtest";
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		echo $row["id"].".<";
		echo $row["name"].">";
		echo $row["comment"]." ";
		echo $row["date"]."<br>";
	    echo "<hr>";
	}

?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>mission5-1</title>
    </head>
<body>

<!--投稿フォーム-->
    <form action="" method="post">
        <input type="hidden" name="editid"
            value="<?php 
            if(!empty($editid)){echo $editid;} ?>">
        <input type="text" name="name" 
            value="<?php 
                        if(!empty($editname)){
                            echo $editname;
                        }else{デフォルトで名無しを設定
                            echo "名無し";
                        } 
                    ?>"
            placeholder="名前を入力">
        <input type="text" name="com" 
            value="<?php 
            if(!empty($editcom)){echo $editcom;} ?>" 
            placeholder="コメントを入力">
        <input type="text" name="pass"
            value="<?php 
            if(!empty($editpass)){echo $editpass;} ?>" 
            placeholder="パスワードを入力(任意)">
        <input type="submit" value="投稿">
    </form>

<!--削除フォーム-->
    <form action="" method="post">
        <input type="number" name="del"
            placeholder="削除番号を入力">
        <input type="text" name="del_pass"
            placeholder="パスワードを入力(必須)">
        <input type="submit" value="削除">
        <?php
            if(!empty($_POST["del_pass"]) && $FLG==0){
                echo "パスワードが違います!!";
            }
        ?>
    </form>

<!--編集フォーム-->
    <form action="" method="post">
        <input type="number" name="edit"
            placeholder="編集番号を入力">
        <input type="text" name="edit_pass"
            placeholder="パスワードを入力(必須)">
        <input type="submit" value="編集">
        <?php
            if(!empty($_POST["edit_pass"]) && $FLG==0){
                echo "パスワードが違います!!";
            }
        ?>
    </form>
</body>
</html>