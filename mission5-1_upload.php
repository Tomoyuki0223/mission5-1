<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
<?php
    /*DB接続開始*/
	$dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    /*DB接続完了*/
    
    /*データベース内にテーブルを作成*/
    $sql = "CREATE TABLE IF NOT EXISTS tb_mission5_1"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"/*自動で登録されていうナンバリング*/
	. "name char(32),"/*名前を入れる。文字列、半角英数で32文字*/
	. "comment TEXT,"/*コメントを入れる。文字列、長めの文章も入る*/
	. "date DateTime,"/*投稿日時を入れる。文字列、長めの文章も入る*/
	. "pass char(8)"/*パスワードを入れる。文字列、半角英数で8文字*/
	.");";
	$stmt = $pdo->query($sql);
	/*データベース内にテーブルを作成終了*/
	
    $editname = NULL;
    $editcomment = NULL;
    $chanum = NULL;
            if(isset($_POST["submit"])) {/*送信ボタンが押されたとき*/
                $chanum = $_POST["chanum"];/*編集対象の番号*/
                $pass1 = $_POST["pass1"];/*入力されたパスワード*/
                $name = $_POST["name"];/*入力した名前*/
                $comment = $_POST["comment"];/*変更したいコメント*/
                
                if(!empty($name) && !empty($comment) && !empty($pass1)){/*空欄があったらダメ*/
                    $date = date("Y/m/d H:i:s");/*日付の設定*/
                    
                    if(!empty($chanum)) {/*編集番号が書かれているとき*/
                        $id = $chanum; //変更する投稿番号
	                    $sql = 'UPDATE tb_mission5_1 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
	                    $stmt = $pdo->prepare($sql);
	                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
	                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
	                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
	                    $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
	                    $stmt -> bindParam(':pass', $pass1, PDO::PARAM_STR);
	                    $stmt->execute();
                        
                    } else {/*編集対象番号が書かれてないとき*/
                        /*データを入力（データレコードの挿入）*/
                        $sql = $pdo -> prepare("INSERT INTO tb_mission5_1 (name, comment,date,pass) VALUES (:name, :comment, :date, :pass)");
	                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	                    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	                    $sql -> bindParam(':pass', $pass1, PDO::PARAM_STR);
	                    $date = date("Y/m/d H:i:s");
	                    $pass1 = $_POST["pass1"];/*入力された投稿欄のパスワード*/
	                    $sql -> execute();
                        /*データ入力完了*/
                    }
                    
                }
                
            } else if(isset($_POST["delete"])) {/*削除ボタンが押された場合*/
                $delnum = $_POST["delnum"];
                $pass2 = $_POST["pass2"];
                
                if(!empty($delnum) && !empty($pass2)) {/*空欄がない場合*/
                    $sql = 'SELECT * FROM tb_mission5_1';
	                $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    
	                foreach ($results as $row){

                        if($delnum == $row['id']) {/*削除対象番号と一致した場合*/

                            if($pass2 == $row['pass']) {/*パスワードが一致した場合*/
                                /*削除番号のデータレコードを削除*/
                                $id = $delnum;
                                $sql = 'delete from tb_mission5_1 where id=:id';/*DELETEのSQLの作成*/
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                                $stmt->execute();
                                /*削除番号のデータの削除完了*/

                            } else {/*パスワードが違う場合*/
                                echo '<font color="red">';
                                echo "パスワードが違います！";
                                echo '<font color="black">';
                            }

                        }

                    }

                }
                
            } else if(isset($_POST["changeb"])) {/*編集ボタンが押された場合*/
                $change = $_POST["change"];
                $pass3 = $_POST["pass3"];

                if(!empty($change) && !empty($pass3)) {/*空欄がない場合*/
                    $sql = 'SELECT * FROM tb_mission5_1';
	                $stmt = $pdo->query($sql);
                    $results = $stmt->fetchAll();
                    
	                foreach ($results as $row){

		                if($row['id'] == $change) {/*編集したい番号だった場合*/

                             if($row['pass'] == $pass3) {/*パスワードが一致した場合*/
                                $chanum = $change;
                                $editname = $row['name'];
                                $editcomment = $row['comment'];

                            } else {/*パスワードが違う場合*/
                                echo '<font color="red">';
                                echo "パスワードが違います！";
                                echo '<font color="black">';
                            }

		                }
		                    
                    }
                    
                } 
                    
            }
?>
    <form action="" method="post">
        <p>
            <label><input type="text" name="name" placeholder="名前" value="<?php echo $editname; ?>"></label><br>
            <label><input type="text" name="comment" placeholder="コメント" value="<?php echo $editcomment; ?>"></label><br>
            <label><input type="hidden" name="chanum" value="<?php echo $chanum; ?>"></label>
            <input type="password" name="pass1" placeholder="パスワード">
            <input type="submit" name="submit"><br>
        </p>
        
        <p>
            <label><input type="text" name="delnum" placeholder="削除対象番号"></label><br>
            <input type="password" name="pass2" placeholder="パスワード">
            <input type="submit" name="delete" value="削除"><br>
        </p>
        
        <p>
            <label><input type="text" name="change" placeholder="編集対象番号"><br>
            <input type="password" name="pass3" placeholder="パスワード">
            <input type="submit" name="changeb" value="編集"><br>
        </p>
    </form>
    <?php
    
    /*入力したデータレコードを抽出し、表示する*/
    $sql = 'SELECT * FROM tb_mission5_1';
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].' ';
		echo $row['name'].' ';
		echo $row['comment'].' ';
		echo $row['date'].'<br>';
        echo "<hr>";
	}
	/*画面に表示完了*/
    ?>
</body>
</html>
