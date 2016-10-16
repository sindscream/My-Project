<?php
class View
{
    private $pageMessages;//массив сообщений для вывода
    private $pageNumber;//номер запрошенной страницы
    private $pagesAmount;//количество страниц
    private $paginationIndent;//количество номеров слева и справа от текущей страницы в пагинаторе
    private $formData;//данные для формы, чтобы отобразить повторно при некорректном вводе
    private $error;//сообщение обшибке при некорректных данных в форме
    private $flag;
    private $param;


    public function __construct($pageNumber, $pagesAmount, $paginationIndent) {//в аргументах конструктора передаются настройки пагинатора
        $this->pageMessages = array();//инициализация пустым массивом
        $this->pageNumber = $pageNumber;
        $this->pagesAmount = $pagesAmount;
        $this->paginationIndent = $paginationIndent;
    }

    public function render($pageMessages, $formData, $error, $flag) {//выводит страницу, массив данных передается аргументом
        $this->error = $error;
        $this->flag = explode(':', $flag)[0];
        $this->param = explode(':', $flag)[1];
        $this->renderLoginForm();
        $this->renderQuit();
        $this->pageMessages = $pageMessages;
        $this->formData = $formData;
        $this->renderHeader();//вызываем функции, выводящие блоки страниц
        $this->renderForm();
        $this->renderMessages();
        $this->renderPagination();
        $this->renderFooter();
        //$this->renderCommentForm();
    }

    private function renderHeader() {//private, так как вызывается только из этого класса функцией render
        echo '<!DOCTYPE html><head><meta charset="utf-8"><title>Мой проект' . $this->pageNumber .'</title>
		<link href="styles.css" rel="stylesheet" type="text/css" /></head><body>
		<div id="container">
			<div id="header">
				<div class="headerTop">
					<div class="menu"><ul>
						<li><a href="main.html">О нашем проекте</a></li>
						<li><a href="">Видео</a></li>
						<li><a href="">Фотогалерея</a></li>
						<li><a href="">Контакты</a></li>
					</ul>
					</div>
					<div class="icons">
						<ul class="icons">
							<li><a href=""><img src="images/twitter.png" alt="" /></a></li>
							<li><a href=""><img src="images/facebook.png" alt="" /></a></li> 
						</ul>
					</div>
					<div class="clearfloat"></div>
				</div>
				<div class="headerPics">
					<div class="headerBox1">
					<h2>У нас вы найдете:</h2>
					<ul>
						<li>1.Полезную информацию по питанию</li>
						<li>2.Режими тренировок</li>
						<li>3.Интервью с ведущими спортсменами в области бодибилдинга и фитнеса в РБ</li>
                    
					</ul>
				</div>
				<div class="headerBox3"><img src="images/biceps222.jpg" alt="" /></div>
				<div class="headerBox2">
            	<div class="logo"><a href=""><img src="images/gantelya1.png" alt="" /></a></div>
                	<span id="citata">Жить — это значит постоянно оставаться голодным.
					Смысл жизни состоит не в том, чтобы просто существовать и выживать, а в том, чтобы двигаться вперед, вверх, достигать и завоевывать.</span>
				</div>        	
				</div>
			</div>
				<h1>Фитнес портал</h1>
				';
        if($_SESSION['role']=="ROLE_ADMIN"){
            echo '  <form method="POST" action="index.php">
                        <input type="hidden" name = "create" value="true">
                        <input type="submit" value="Добавить запись">
                    </form>';
            }
    }

    private function renderMessages() {//вывод сообщений
        foreach ($this->pageMessages as $row) {
           
            echo '<p>Имя: ' . $row['login'] . ', Дата: ' .$row['datetime']. "" . '<br/>';
            echo '<div id="mainContent"><br/>' . $row['text'] . '</p></div>';//основное содержимое статьи
            if($_SESSION['role']=="ROLE_ADMIN"){
                    echo '  <form method="POST" action="index.php?number='.$this->pageNumber.'">
                                <input type="hidden" name = "delete" value="'.$row[record_id].'">
                                <input type="submit" value="Удалить">
                            </form>';
                     echo '  <form method="POST" action="index.php?number='.$this->pageNumber.'">
                                <input type="hidden" name = "edit" value="'.$row[record_id].'">
                                <input type="submit" value="Редактировать">
                            </form>';
            }
            if($_SESSION['role']=="ROLE_ADMIN" && $this->flag=="edit" && $this->param==$row[record_id]){
                     echo '  <form method="POST" action="index.php?number='.$this->pageNumber.'">
                                 <input type="hidden" name = "edit_id" value="'.$row[record_id].'">
                                 <textarea name="edit_text" cols="50" rows="10">'.$row[text].'</textarea><br>
                                 <input type="submit" value="Изменить">
                            </form>';
            }
            if($_SESSION['login'] && $this->flag=="comment" && $this->param==$row[record_id]){
                     echo '  <form method="POST" action="index.php?number='.$this->pageNumber.'">
                                 <input type="hidden" name = "commented_record_id" value="'.$row[record_id].'">
                                 <textarea name="comment_text" cols="50" rows="10"></textarea><br>
                                 <input type="submit" value="Отправить комментирий">
                            </form>';
            }
            if($_SESSION['login']){
                    echo '  <form method="POST" action="index.php?number='.$this->pageNumber.'">
                                <input type="hidden" name = "comment" value="'.$row[record_id].'">
                                <input type="submit" value="Комментировать">
                            </form>';
                        }
            $comments = $row[comments];
            foreach ($comments as $comment) {
                $this->renderComment($comment);
            }
            echo '<hr/>';
        }
    }

    private function renderComment($comment){
        echo '<p>Пользователь: '. $comment['login'];
        echo '<p>Комментарий: ' . $comment['text'];
        echo '<p>'. $comment['datetime_comment'];
        if($_SESSION['role']=="ROLE_ADMIN" || $_SESSION['login']==$comment['login'])
        echo '  <form method="POST" action="index.php?number='.$this->pageNumber.'">
                    <input type="hidden" name = "comment_delete" value="'.$comment[comment_id].'">
                    <input type="submit" value="Удалить">
                </form>';
    }

    private function renderPagination() {//вывод пагинации, здесь просто функция, хотя лучше создать отдельный класс
        if ($this->pagesAmount<2) return;
        $start = $this->pageNumber - $this->paginationIndent;
        if ($start<1) $start = 1;
        $finish = $this->pageNumber + $this->paginationIndent;
        if ($finish>$this->pagesAmount) $finish = $this->pagesAmount;
        for ($i = $start; $i<=$finish; $i++) {
            if ($i==$this->pageNumber)
                echo $i.'&nbsp;';
            else
                echo '<a href="' . basename($_SERVER['SCRIPT_FILENAME']) . '?number=' . $i . '">' . $i . '</a>&nbsp;';
        }
    }

    private function renderFooter() {
        echo '<div id="footer"><p>Подвал</p></div></div></body></html>';// конец контейнера
    }

    private function renderForm() {
        if($_SESSION['role']=="ROLE_ADMIN"  && $this->flag == "create"){
            echo
            '<div><form method="post" action="index.php">
                Ваше сообщение<br/><textarea name="message" cols="50" rows="10">' . $this->formData['message'] .'</textarea><br/>
                <input type="submit" value="Отправить">
                </form>
            </div>';
        }
    }

    private function renderLoginForm() {//форма регистрации
            echo $this->error;
            if(!$_SESSION['login'])
            echo
            '<div id="vxod">
            <form method="POST" action="index.php">
                <label>Логин:</lable><br><input type="text" pattern ="^[a-zA-Z][a-zA-Z0-9-_\.]{3,20}$" title="Логин может содержать цифры и латинские буквы, должен начинаться с латинской буквы и содержать 4-20 симовлов" name="login" required><br>
                <lable>Пароль:</label><br><input type="password" pattern="[a-zA-Z0-9_]{4,}" title="Пароль должен содержать прописные и строчные буквы, а также цифры" name="password" required><br>
                <input type="submit" value="Войти">
            </form>
            </div>
            <a href="register.php">Регистрация</a>';
        }

    private function renderQuit() {
        if($_SESSION['login']){
            echo
            '
            <p>Здравствуйте, '.$_SESSION['login'].'
            <form method="POST" action="index.php">
                <input type="hidden" name = "logout" value="true">
                <input type="submit" value="Выход">
            </form>';
        }
    }
    private function renderCommentForm(){
        if($_SESSION['login']){
            echo
            '<div>
            <p>Здравствуйте, '.$_SESSION['login'].'
            <form method="POST" action="index.php">
                <textarea name="comment" cols="50" rows="10"></textarea><br>
                <input type="submit" value="Комментировать">
            </form>
            </div>';
        }
    }
}
