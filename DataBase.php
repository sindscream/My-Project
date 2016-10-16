<?php
	class DataBase{
		private static $db = null;
		private $dbh;
		private $error;

		public static function getDB(){
			if(self::$db == null) 
				self::$db = new DataBase();
			return self::$db;
		}

		private function __construct(){
			$host="localhost";
	        $dbname="my_work";
	        $user="root";
	        $pass="root"; 
	        $this->dbh = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
		}

		public function getRole($login,$password){
			$this->error = null;
			if (isset($login) && isset($password)){
	            $stmt = $this->dbh->prepare("SELECT * FROM users where login = :login AND password = :password");
	            $stmt->bindParam(':login', $_POST['login']);
	            $stmt->bindParam(':password', $_POST['password']);
	            $role='';
	            if ($stmt->execute()) {
	                while ($row = $stmt->fetch()) {
	                    $role = ($row["role"]);
	              }
	            }
	            if($role == ''){
					$this->error = "Неверный логин или пароль";
				} 
	            return $role;
	        }
		}



		public function createRecord($login,$text){
			$this->error = null;
			if (isset($text) && isset($login)){
				$sql = "SELECT * from users where login = '".$login."'";
				foreach ($this->dbh->query($sql) as $row) {
					$user_id = $row['user_id'];
				}
	            $stmt = $this->dbh->prepare('INSERT INTO records(text,user_id,datetime) VALUES(:text,:user_id,NOW())');
	            $stmt->bindParam(':text', $text);
	            $stmt->bindParam(':user_id', $user_id);
				$stmt->execute();
	    	}
		}

		public function updateRecord($id,$text){
			$this->error = null;
			$sql = "UPDATE records SET text = '".$text."' where record_id = ".$id;
			$this->dbh->query($sql);
		}

		public function deleteRecord($id){
			$this->error = null;
			$sql = "DELETE from records where record_id = ".$id;
			$this->dbh->query($sql);
		}

		public function readRecords(){
			$sql = "SELECT * from users inner join records on users.user_id = records.user_id";
			$result = array();
			foreach ($this->dbh->query($sql) as $row) {
				$row[comments]=$this->readComments($row[record_id]);
				$result[] = $row;
			}
	        return $result;
		}

		public function readComments($id){
			$sql = "SELECT * from comments inner join users on comments.user_id = users.user_id where record_id = ".$id;
			$result = array();
			foreach ($this->dbh->query($sql) as $row) {
				$result[] = $row;
			}
	        return $result;
		}

		public function createComment($login,$text,$record_id){
			$this->error = null;
			if (isset($text) && isset($login) && isset($record_id)){
				$sql = "SELECT * from users where login = '".$login."'";
				foreach ($this->dbh->query($sql) as $row) {
					$user_id = $row['user_id'];
				}
	            $stmt = $this->dbh->prepare('INSERT INTO comments(text,user_id,record_id,datetime_comment) VALUES(:text,:user_id,:record_id,NOW())');
	            $stmt->bindParam(':text', $text);
	            $stmt->bindParam(':user_id', $user_id);
	            $stmt->bindParam(':record_id', $record_id);
				$stmt->execute();
	    	}
		}

		public function deleteComment($id){
			$this->error = null;
			$sql = "DELETE from comments where comment_id = ".$id;
			$this->dbh->query($sql);
		}

		public function getError() {
		    return $this->error;
		}
	}
?>