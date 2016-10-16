<?php

	$host="localhost";
	$dbname="my_work";
	$user="root";
	$pass="root";

	function renderForm(){
		echo '
			<html>
			<head>
				<meta charset="utf-8">
					<style>
						.registr{
							width: 200px; /* Ширина элемента в пикселах */
							padding: 10px; /* Поля вокруг текста */
							margin: auto; /* Выравниваем по центру */
							background: #aabbcc;
							font-family: Times New Roman;
						}
						#but{
							border-radius: 5px;
							background-color: #aaccdd;
						}
					</style>
			</head>
			<body>
				<div class="registr">
					<form method="POST">
						<lable>Логин:</lable><br><input type="text" pattern ="^[a-zA-Z][a-zA-Z0-9-_\.]{3,20}$" name="login" title="Логин может содержать цифры и латинские буквы, должен начинаться с латинской буквы и содержать 4-20 симовлов" required><br>
						<lable>Пароль:</lable><br><input type="text" pattern="[a-zA-Z0-9_]{4,}" name="pass" title="Пароль должен содержать прописные и строчные буквы, а также цифры" required><br>
						<input id="but" type="submit" value="Зарегистрироваться">
					</form>
				</div>
			</body>
		</html>
		';
	}

   renderForm();
   if (isset($_POST['login'])&&isset($_POST['pass'])){ 
			$dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
			$stmt = $dbh->prepare("INSERT INTO users (login, password, role) VALUES (:login, :password, 'ROLE_USER')");
			$stmt->bindParam(':login', $_POST['login']);
			$stmt->bindParam(':password', $_POST['pass']);
			if($stmt->execute()!= 1){
			   	echo "Пользователь с таким логином уже существует";
			} else {
			   	echo "Регистрация прошла успешно";
			   	echo '<a href="index.php">На главную</a>';
			}
   	}
?>
