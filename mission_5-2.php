<html>
	<head>
		<title>mission_5-1</title> 
		<meta charset="utf-8"> 
	</head>
	<body>
	
	<?php
		//以下4つエラーのための変数
		$Error_1="";
		$Error_2="";
		$Error_3="";
		$flag=0;
		$password1="管理者のパスワード"; //管理者のパスワード
		//①データベースへの接続
		$dsn = 'データベース名';
		$user = 'ユーザー名';
		$password = 'パスワード';
		$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
		//②テーブルの作成
		

		$sql = "CREATE TABLE IF NOT EXISTS mission_5" //テーブルの作成
		." ("
		. "id INT AUTO_INCREMENT PRIMARY KEY," //id:カラム名　INT：データ型　AUTO～:自動的に1ずつ増える設定
		. "name char(32)," //名前の設定
		. "comment TEXT," //コメントの設定
		. "date TIMESTAMP,"
		. "password char(32)"
		.");";
		
		$stmt = $pdo->query($sql); //引数で指定したSQLをデータベースに対して発行
		//③formから入力された投稿データを変数に代入
		if(!empty($_POST["name"])){
			$name1=$_POST["name"];
		}elseif(empty($_POST["name"])){
			$Error1="(名前)";
		}
		if(!empty($_POST["comment"])){
			$comment1=$_POST["comment"];
		}elseif(empty($_POST["comment"])){
			$Error2="(コメント)";
		}
		if(!empty($_POST["pass1"])){
			$pass1=$_POST["pass1"];
		}elseif(empty($_POST["pass1"])){
			$Error3="(パスワード)";
		}
		if(!empty($_POST["edit_number"])){
			$edit_number=$_POST["edit_number"];
		}
		if(isset($Error1)){
			$Error_1=$Error_1.$Error1;
			if(isset($Error2)){
				$Error_1=$Error_1.$Error2;
				if(isset($Error3)){
					$Error_1=$Error_1.$Error3;
				}
			}elseif(isset($Error3)){
				$Error_1=$Error_1.$Error3;
			}
		}elseif(isset($Error2)){
			$Error_1=$Error_1.$Error2;
			if(isset($Error3)){
				$Error_1=$Error_1.$Error3;
		}
		}elseif(isset($Error3)){
			$Error_1=$Error_1.$Error3;
		}
		
		//④投稿をテーブルに挿入
		if(isset($_POST['submit1'])){
			if(isset($name1) && isset($comment1) && isset ($pass1)){
				//※編集するときの処理
				if(isset($edit_number)){
					$id = $edit_number; //変更する投稿番号
					$name = $name1;
					$comment = $comment1; //変更したい名前、変更したいコメントは自分で決めること
					$password = $pass1;
					$sql = 'update mission_5 set name=:name,comment=:comment,password=:password where id=:id'; //update テーブル名 setで編集ができる
					$stmt = $pdo->prepare($sql); //ユーザーからの入力を求めているのでprepare
					$stmt->bindParam(':name', $name, PDO::PARAM_STR);
					$stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
					$stmt->bindParam(':password', $password, PDO::PARAM_STR);
					$stmt->bindParam(':id', $id, PDO::PARAM_INT);
					$stmt->execute();
					$Reception="投稿番号".$edit_number."の内容を編集しました。"."<br>";
				//※編集しないときの処理
				}elseif(empty($edit_number)){
					$sql = $pdo -> prepare("INSERT INTO mission_5 (name, comment , date , password) VALUES (:name, :comment, :date , :password)"); //名前とコメントをテーブルに入れる準備　prepareはquaryと違い、何回もデータを入れたいときに良い
					$sql -> bindParam(':name', $name, PDO::PARAM_STR); //名前のデータをテーブルにいれる
					$sql -> bindParam(':comment', $comment, PDO::PARAM_STR); //コメントのデータをテーブルに入れる設定
					$sql -> bindParam(':date', $date, PDO::PARAM_STR); //コメントのデータをテーブルに入れる設定
					$sql -> bindParam(':password', $password, PDO::PARAM_STR); //コメントのデータをテーブルに入れる設定
					$name = $name1;
					$comment = $comment1; //好きな名前、好きな言葉は自分で決めること
					$date= date('Y/m/d H:i:s');
					$password= $pass1;
					$sql -> execute(); //以上の内容を実行する(テーブルに入れる)
					$Reception="投稿を受け付けました。"."<br>";
				}
			}else{
				$Error_1="投稿のエラーです。"."<br>".$Error_1."が入力されていません"."<br>";
			}
		}
		//⑤formから入力された削除データを変数に代入
		$Error1=""; //エラー変数のリセット
		$Error2="";
		if(!empty($_POST["delete"])){
			$delete=$_POST["delete"];
		}elseif(empty($_POST["delete"])){
			$Error1 ="(削除対象番号)";
		}
		if(!empty($_POST["pass2"])){
			$pass2=$_POST["pass2"];
		}elseif(empty($_POST["pass2"])){
			$Error2 ="(パスワード)";
		}
		if(isset($Error1)){
			$Error_2=$Error_2.$Error1;
				if(isset($Error2)){
					$Error_2=$Error_2.$Error2;
				}
		}elseif(isset($Error2)){
			$Error_2=$Error_2.$Error2;
		}
		//⑥対象となるテーブルデータの削除
		if(isset($_POST['submit2'])){
			if(isset($delete) && isset($pass2)){
				$sql = 'SELECT * FROM mission_5'; //SELECT *(すべて)　テーブルtbtestの全てのデータを表示
				$stmt = $pdo->query($sql);
				$results = $stmt->fetchAll(); //複数のデータを配列resultsに返す
				foreach ($results as $text){
					if($text['id'] == $delete && (($text['password'] == $pass2) || ($pass2 == $password1))){
						$id = $delete;
						$sql = 'delete from mission_5 where id=:id'; //DELETE FROM テーブル名 WHERE 条件;
						$stmt = $pdo->prepare($sql);
						$stmt->bindParam(':id', $id, PDO::PARAM_INT);
						$stmt->execute();
						$Reception="投稿番号".$delete."を削除しました"."<br>";
					}elseif($text['id'] == $delete && (($text['password'] != $pass2) || ($pass2 != $password1))){
						$Error_2="削除のエラーです。"."<br>"."パスワードが違います"."<br>";
					}elseif(!preg_match("/^[0-9]+$/", $delete)){
						$Error_2="削除のエラーです。"."<br>"."削除対象番号が半角数字ではありません"."<br>"."もう一度入力してください"."<br>";
					}elseif($text['id'] != $delete){
						$flag++;
					}
				}
				if(count($results)==$flag){
					$Error_2="削除対象番号が投稿番号にありません"."<br>"."もう一度入力してください"."<br>";
				}
			}else{
				$Error_2="削除のエラーです。"."<br>".$Error_2."が入力されていません"."<br>";
			}
			
		}
		
		//⑦formから入力された編集データを変数に代入
		$Error1=""; //エラー変数のリセット
		$Error2="";
		$flag=0;
		if(!empty($_POST["edit"])){
			$edit=$_POST["edit"];
		}elseif(empty($_POST["edit"])){
			$Error1 ="(編集対象番号)";
		}

		if(!empty($_POST["pass3"])){
			$pass3=$_POST["pass3"];
		}elseif(empty($_POST["pass3"])){
			$Error2 ="(パスワード)";
		}
		if(isset($Error1)){
			$Error_3=$Error_3.$Error1;
				if(isset($Error2)){
					$Error_3=$Error_3.$Error2;
				}
		}elseif(isset($Error2)){
			$Error_3=$Error_3.$Error2;
		}
		//⑧対象となるテーブルデータの編集
		if(isset($_POST['submit3'])){
			if(isset($edit) && isset($pass3)){
				$sql = 'SELECT * FROM mission_5'; //SELECT *(すべて)　テーブルtbtestの全てのデータを表示
				$stmt = $pdo->query($sql);
				$results = $stmt->fetchAll(); //複数のデータを配列resultsに返す
				foreach ($results as $text){
					if($text['id'] == $edit && (($text['password'] == $pass3) || ($pass3 == $password1))){
						$edit_name=$text['name'];
						$edit_comment=$text['comment'];
						$edit_nu=$edit;
						$Reception="投稿番号".$edit."を編集します"."<br>";
					}elseif($text['id'] == $edit && (($text['password'] != $pass3) || ($pass3 != $password1))){
						$Error_3="編集のエラーです。"."<br>"."パスワードが違います"."<br>";
					}elseif(!preg_match("/^[0-9]+$/", $edit)){
						$Error_3="編集のエラーです。"."<br>"."編集対象番号が半角数字ではありません"."<br>"."もう一度入力してください"."<br>";
					}elseif($text['id'] != $edit){
						$flag++;
					}
				}
				if(count($results)==$flag){
					$Error_3="編集対象番号が投稿番号にありません"."<br>"."もう一度入力してください"."<br>";
				}
			}else{
				$Error_3="編集のエラーです。"."<br>".$Error_3."が入力されていません"."<br>";
			}
			
		}
		
		//⑧テーブルの中身全て削除
		if(!empty($_POST["pass4"])){
			$pass4=$_POST["pass4"];
		}elseif(empty($_POST["name"])){
			$Error_4="管理者フォームのエラーです。"."<br>"."パスワードが入力されていません。"."<br>";
		}
		if(isset($_POST['submit4'])){
			if(isset($pass4)){
				if($pass4== $password1){
					$sql = 'delete from mission_5';
					$stmt = $pdo->prepare($sql);
					$stmt->execute();
					$sql = 'ALTER TABLE mission_5 AUTO_INCREMENT = 1';  //ここから↓3文は管理者権限でテーブルがリセットされたとき、
					$stmt = $pdo->prepare($sql);				//idをもう一度1から振り直す処理	
					$stmt->execute();
					$Reception="テーブルを削除しました。"."<br>";
				}else{
					$Error_4="管理者フォームのエラーです。."."<br>"."パスワードが違います。"."<br>";
				}
			}
		}
	?>
	
	<!--タイトル・説明文等-->
	<h1 id="midashi_1" align="center"> ○○の掲示板 </h1>
	<hr>
	<div align="center">
	みなさん、こんにちは！○○です。この掲示板はみなさんの地元のおすすめの観光地を書いていただく掲示板です。
	<br>
	投稿する際は必ず、名前、コメント、パスワードを入力お願いします!
	<br>
	※ただし、投稿した際のパスワードは忘れないでくださいね！
	</div>
	<hr>
	
	<!--フォーム内容-->
	<form action="mission_5-1.php" method="post">
	<p> 【　投稿用フォーム　】 </p>
	<p> お名前：
	<input type ="text" name="name" value="<?php if(!empty($edit_name)){ echo $edit_name; }?>"></p>
	<p> コメント：
	<input type ="text" name="comment" value="<?php if(!empty($edit_comment)){ echo $edit_comment; }?>"></p>
	<p> <input type="hidden" name ="edit_number" value="<?php if(!empty($edit_nu)){ echo $edit_nu; }?>"></p>
	<p> パスワード：
	<input type="password" name="pass1">
	<p> <input type="submit" name="submit1" value="送信"></p>
	<br>
	<p> 【　削除フォーム　】
	<p> 削除番号(半角数字で入力)： 
	<input type="text" name="delete" ></p> 
	<p> パスワード：
	<input type="password" name="pass2"></p>
	<p> <input type="submit" name="submit2" value="削除"></p> 
	<br>
	<p> 【　編集フォーム　】 </p>
	<p> 編集番号(半角数字で入力）：
	<input type="text" name="edit"></p> 
	<p> パスワード：
	<input type="password" name="pass3"></p>
	<p> <input type="submit" name="submit3" value="編集"></p>
	<br>
	<p> 【　管理者フォーム　】 </p>
	<p> パスワード：
	<input type="password" name="pass4"></p>
	<p>すべてのテーブルを削除するのはこちら→
	<input type="submit" name="submit4" value="AllDelete"></p> 

	</form>
		<?php
			//以下表示するプログラム
			echo "<hr>"."【　受付内容　】"."<br>";
			if(!empty($Reception)){
				echo $Reception;
			}
			echo "<hr>"."【　エラー一覧　】"."<br>";
			if(isset($_POST["submit1"])){
				if(!empty($Error_1)){
					echo $Error_1;
				}
			}
			if(isset($_POST["submit2"])){
				if(!empty($Error_2)){
					echo $Error_2;
				}
			}
			if(isset($_POST["submit3"])){
				if(!empty($Error_3)){
					echo $Error_3;
				}
			}
			if(isset($_POST["submit4"])){
				if(!empty($Error_4)){
					echo $Error_4;
				}
			}
			echo "<hr>"."【　投稿一覧　】"."<br>";
			$dsn = 'データベース名';
			$user = 'ユーザー名';
			$password = 'パスワード';
			$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
			$sql = 'SELECT * FROM mission_5'; //SELECT *(すべて)　テーブルtbtestの全てのデータを表示
			$stmt = $pdo->query($sql);
			$results = $stmt->fetchAll(); //複数のデータを配列resultsに返す
			foreach ($results as $row){
				//$rowの中にはテーブルのカラム名が入る
				echo $row['id'].'  ';
				echo $row['name'].'  ';
				echo "「".$row['comment']."」".'  ';
				echo $row['date'].'<br>';
			}
			
		?>
	</body>
</html>
	