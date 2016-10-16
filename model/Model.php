<?php

class Model
{
    private $messagesArray;//массив сообщений
    private $messagesAmount;//общее количество сообщений
    private $messagesPerPage;//количество сообщений на одной странице
    private $pagesAmount;//количество страниц
    private $pageMessages;//массив сообщений для запрошенной страницы
    private $pageNumber;//номер запрошенной страницы
    private $action;//действие read или write
    private $formData;//данные для формы
    private $error;//сообщение обшибке при некорректных данных в форме
    private $actionParams;

    public function __construct($action,$actionParams) {
        //устанавливаем для всех свойств значения по умолчанию
        //чтобы объект был полностью инициализированv
        $this->messagesArray = array();
        $this->messagesAmount = 0;
        $this->action = $action;
        $this->messagesPerPage = 1;
        $this->pagesAmount = 1;
        $this->pageMessages = array();
        $this->pageNumber = 1;
        $this->formData['name'] = '';
        $this->formData['message'] = '';
        $this->error = '';
        $this->actionParams = $actionParams;
    }
    public function setMessagesPerPage($messagesPerPage) {//установка количества сообщений на странице
        $this->messagesPerPage = $messagesPerPage;
    }
    public function run($pageNumber = 1) {//выполняет все действия, в аргументе передается номер запрошенной страницы
            $this->pageNumber = $pageNumber;//записываем полученный в параметре номер страницы в свойство
            $this->{$this->action}();//вызываем метод по имени, хранящемся в $this->action,т.е. read или write

    }
    public function getPageMessages() {//функция для получения сообщений страницы для View
        return $this->pageMessages;
    }
    public function getPagesAmount() {//функция для получения количества страниц тоже передачи View
        return $this->pagesAmount;
    }
    public function getFormData() {//получение данных для формы, если они были введены с ошибкой, для повторного вывода
        return $this->formData;
    }
    public function getError() {//получить сообщение при ошибке во введенных в форму данных
        return $this->error;
    }
    private function read() {
        $this->messagesArray = DataBase::getDB()->readRecords();
        $this->messagesArray = array_reverse($this->messagesArray);//реверсиуем массив от новых сообщений к старым
        $this->messagesAmount = count($this->messagesArray);//определяем количество сообщений
        $this->pagesAmount = ceil($this->messagesAmount/$this->messagesPerPage);//определяем количество страниц
        if ($this->pageNumber<1 || $this->pageNumber>$this->pagesAmount) {//если номер страницы вне диапазона существующих страниц
            //throw new Exception('Запрошенная страница не существует');//генерируем исключение, оно перехватится в контроллере
        }
        $messageOffset = ($this->pageNumber-1) * $this->messagesPerPage;//номер первого сообщения на странице
        $this->pageMessages = array_slice($this->messagesArray, $messageOffset, $this->messagesPerPage);//извлекаем сообщения для страницы
    }

    private function login(){
        $role = DataBase::getDB()->getRole($_POST['login'],$_POST['password']);
        if($role != ''){
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['role'] = $role;
        }
        $this->error = DataBase::getDB()->getError();
        $this->read();  
    }

    private function logout(){
        unset($_SESSION['login']);
        unset($_SESSION['role']);
        $this->read();  
    }

    private function createRecord(){
        DataBase::getDB()->createRecord($this->actionParams[login],$this->actionParams[textRecord]);
        header('Location: http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']);
        exit();                
    }

    private function editRecord(){
        DataBase::getDB()->updateRecord($this->actionParams[idRecord],$this->actionParams[textRecord]);
        $this->read();          
    }

    private function deleteRecord(){
        DataBase::getDB()->deleteRecord($this->actionParams[idRecord]);
        $this->read();          
    }

    private function createComment(){
        DataBase::getDB()->createComment($this->actionParams[login],$this->actionParams[text],$this->actionParams[idRecord]);
        $this->read();          
    }

    private function deleteComment(){
        DataBase::getDB()->deleteComment($this->actionParams[idComment]);
        $this->read();          
    }
}