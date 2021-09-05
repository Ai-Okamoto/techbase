<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1.php</title>
</head>

<body>
    <h1>
        掲示板
    </h1>
    自由に書き込んでください！<br><br>
    
    <?php 
    //GitHub提出用
    
    
    // DB接続設定
    $dsn = 'データベース名';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
    /*
    if ($pdo == null){
        print('接続に失敗しました。<br>');
    }else{
        print('接続に成功しました。<br>');
    }
    */
    
    //もしまだこのテーブルが存在しないならテーブルを作成
    $sql = "CREATE TABLE IF NOT EXISTS tbmission5_1new" 
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "date datetime,"
    . "password char(32),"
    . "editmemo enum('comment', 'deletecomment')"
    .");";
    $stmt = $pdo->query($sql);
    
    $datenow = date ( "Y/m/d H:i:s" );
    
    if(isset($_POST["submit"])){//送信が押された場合
        //名前、コメント、パスワードの定義
        $name_new = filter_input(INPUT_POST,"name"); //$_POST["name"];
        $comment_new = filter_input(INPUT_POST,"comment");//$_POST["comment"];
        $pass_new = filter_input(INPUT_POST,"pass");//$_POST["pass"];
        
        //編集番号が表示されている場合の処理
        if(!empty($_POST["editmode"])){//編集番号がある場合始
            $editmode = filter_input(INPUT_POST,"editmode");//編集番号を定義
            //作業メモ
            //編集コメントの定義
            $id=$editmode; //filter_input(INPUT_POST,"deletenumber");
            $name = $name_new;
            $comment = $comment_new;
            $date = $datenow;
            $password = $pass_new;
            $editmemo = "comment";
            
            //テーブルを指定しコメントを編集
            $sql = 'UPDATE tbmission5_1new SET name=:name,comment=:comment,date=:date,password=:password,editmemo=:editmemo WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
            $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
            $stmt -> bindParam(':editmemo', $editmemo, PDO::PARAM_STR);
            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo "コメントを編集しました<br>";
            
        }//編集番号がある場合終
        else{ //編集番号が無い場合
            if (!empty($name_new) && !empty($comment_new) && !empty($pass_new)){//名前、コメント、パスワード全部が書かれている場合
                //テーブルを指定しINSERT文でデータを登録
                $sql = $pdo -> prepare("INSERT INTO 
                tbmission5_1new (name, comment, date, password, editmemo) 
                VALUES (:name, :comment, :date, :password, :editmemo)");
                $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $sql -> bindParam(':date', $date, PDO::PARAM_STR);
                $sql -> bindParam(':password', $password, PDO::PARAM_STR);
                $sql -> bindParam(':editmemo', $editmemo, PDO::PARAM_STR);
                
                $name = $name_new;
                $comment = $comment_new; 
                $date =  $datenow;
                $password = $pass_new;
                $editmemo =  'comment';
                
                $sql -> execute(); //bindparamを実行
                echo "新規コメントを受け取りました<br>";
            }//名前、コメント、パスワード全部が書かれている場合終
            else{//入力されていないものがある場合
                echo "正しく入力されていません<br>";
            }
            
        }//編集番号が無い場合終
    }//新規コメント送信された場合終
    
    //if2,削除対象番号に書き込みがある場合
    if(isset($_POST["desubmit"])){ //削除が入力されている
        if(!empty($_POST["deletenumber"]) && !empty($_POST["depass"]) ){//削除番号とパスワードが入力されている
        //入力されているデータレコードの内容を編集
        //変更する投稿番号を入力されたものに
        $id=filter_input(INPUT_POST,"deletenumber");
        $name = "不明";
        $comment = "コメントは削除されました。";
        $date = $datenow;
        $password = "deletepass";
        $editmemo = "deletecomment";
        
        $depass=filter_input(INPUT_POST,"depass");
        
        //パスワードが等しいか
        //テーブルを指定し指定のidのパスワードを抽出
        $sql = 'SELECT * FROM tbmission5_1new WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
        $stmt->execute();                             
        $results = $stmt->fetchAll(); 
        foreach ($results as $row){
            //echo "check1<br>";//デバッグ用
            //送信されたパスワードと等しければ
            if($row['password']==$depass){ 
                //echo "check2<br>";//デバッグ用
                //テーブルを指定し置き換えを実行
                $sql = 'UPDATE tbmission5_1new SET name=:name,comment=:comment,date=:date,password=:password,editmemo=:editmemo WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
                $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
                $stmt -> bindParam(':password', $password, PDO::PARAM_STR);
                $stmt -> bindParam(':editmemo', $editmemo, PDO::PARAM_STR);
                $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                
                echo "コメントを削除しました<br>";
            }//パスワードが等しい場合終
            else{
                echo "パスワードが違います<br>";
            }
        }//繰り返し終
        }//削除番号とパスワードが入力されている場合終
        else{
            echo "正しく入力されていません<br>";
        }
    }//削除が入力されている場合終
    
    //if3,編集
    if(isset($_POST["edit"])){//編集が押された場合
        $id =filter_input(INPUT_POST,"editnumber");
        $editnumber=filter_input(INPUT_POST,"editnumber");
        $editpass = filter_input(INPUT_POST,"editpass");
        if(!empty($editnumber) && !empty($editpass)){//編集番号とパスワードが送信された場合
            //パスワードが等しいか
            //テーブルを指定し指定のidのパスワードを抽出
            $sql = 'SELECT * FROM tbmission5_1new WHERE id=:id ';
            $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
            $stmt->execute();                             // ←SQLを実行する。
            $results = $stmt->fetchAll(); 
            foreach ($results as $row){//4-6の補足参考、どうしてforeach？　閉じはどこまでか
                //echo "check1<br>";//デバッグ用
                //送信されたパスワードと等しければ
                if($row['password']==$editpass){
                    echo "編集可能です<br>";
                    $editname=$row['name'];
                    $editcmt=$row['comment'];
                    $editps=$row['password'];
                }//パスワードが等しい場合終
                else{
                    echo "パスワードが違います<br>";
                }
            }//繰り返し終
        }//編集番号とパスワードが送信された場合終
        else{//編集番号とパスワードが入っていない場合
            echo "正しく入力されていません<br>";
        }
    }//編集が押された場合終
    
    //テーブルを指定しデータを抽出・表示
    //(delete)とあるものはコメントアウトすれば削除履歴をブラウザに残す
    echo "<hr>";
    $sql = 'SELECT * FROM tbmission5_1new';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            //if($row['editmemo']!="deletecomment"){ //(delete)
                echo $row['id'].',';
                echo $row['name'].',';
                echo $row['comment'].'<br>';
                echo $row['date'].'<br>';
                //echo $row['editmemo'].'<br>'; //確認用
                echo "<hr>";
            //} //(delete)
        }
    ?>

<!-- 新規コメントフォーム -->
<form action="" method="post">
        <input type="text" name="name" placeholder="名前" value="<?php if(isset($editname)) {echo $editname;}?>" ><br>
        <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($editcmt)) {echo $editcmt;}?>"><br>
        <input type="password" name="pass" placeholder="パスワード" value="<?php if(isset($editps)) {echo $editps;}?>"><br>
        <input type="hidden" name="editmode" value="<?php if(isset($editnumber)) {echo $editnumber;}?>">
        <input type="submit" name="submit"><br>
        </form><br>
<!-- コメント削除フォーム -->
    <form action="" method="post">
        <input type="number" name="deletenumber" placeholder=削除対象番号><br>
        <input type="password" name="depass" placeholder="パスワード"><br>
        <input type="submit" name="desubmit" value="削除"><br>
        </form><br>
<!-- コメント編集フォーム -->
    <form action="" method="post">
        <input type="number" name="editnumber" placeholder=編集対象番号><br>
        <input type="password" name="editpass" placeholder="パスワード"><br>
        <input type="submit" name="edit" value="編集">
    </form><br>

</body>
</html>