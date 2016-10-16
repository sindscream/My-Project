<?php

class Controller
{
    private $model;//объект модель
    private $view;//объект вид
    private $pageNumber;//номер запрошенной страницы
    private $action;//чтение или запись
    private $flag;
    private $actionParams;

    public function __construct() {
        $this->pageNumber = isset($_GET['number'])?intval($_GET['number']):1;
        $this->actionParams = array();
        if($_POST['login']){
            $this->login();
        } else if($_POST['logout']){
            $this->logout();
        } else if($_POST['delete']){
            $this->deleteRecord($_POST['delete']);//
        } else if($_POST['create']){
            $this->showCreateRecordForm();
        } else if($_POST['edit']){
            $this->showEditRecordForm();
        } else if($_POST['edit_id']){
            $this->editRecord($_POST['edit_id'],$_POST['edit_text']);//
        } else if($_POST['comment']){
            $this->showCreateCommentForm();
        } else if($_POST['commented_record_id']){
            $this->createComment($_SESSION['login'],$_POST['comment_text'],$_POST['commented_record_id']);//
        } else if($_POST['comment_delete']){//
            $this->deleteComment($_POST['comment_delete']);
        } else if (!empty($_POST)) {
            $this->createRecord($_SESSION['login'],$_POST['message']);
        } else {
            $this->action = 'read';
        }
    }
    public function run() {
        try {
            $this->model = new Model($this->action,$this->actionParams);//создаем новую модель
            $this->model->setMessagesPerPage(MESSAGES_PER_PAGE);//передаем ей количество сообщений на странице
            $this->model->run($this->pageNumber);//запускаем, она записывает или извлекает нужные данные

            $this->view = new View($this->pageNumber, $this->model->getPagesAmount(), PAGINATION_INDENT);//создаем новый объект View
            $this->view->render($this->model->getPageMessages(), $this->model->getFormData(), $this->model->getError(),$this->flag);//выводим страницу
        }
        catch(Exception $e) {
            echo 'ОШИБКА<br/>' . $e->getMessage();//если возникла ошибка, выводим сообщение
        }
    }

    private function login(){
        $this->action = 'login';
    }

    private function logout(){
        $this->action = 'logout';
    }

    private function showCreateRecordForm(){
        $this->action = 'read';
        $this->flag = 'create';        
    }

    private function showEditRecordForm(){
        $this->action = 'read';
        $this->flag = 'edit:'.$_POST['edit'];
    }

    private function showCreateCommentForm(){
        $this->action = 'read';
        $this->flag = 'comment:'.$_POST['comment'];
    }

    private function createRecord($login,$text){
        $this->action = 'createRecord';
        $this->actionParams[login] = $login;
        $this->actionParams[textRecord] = $text;
    }

    private function editRecord($id,$text){
        $this->action = 'editRecord';
        $this->actionParams[idRecord] = $id;
        $this->actionParams[textRecord] = $text;
    }

    private function deleteRecord($id){
        $this->action = 'deleteRecord';
        $this->actionParams[idRecord] = $id;
    }

    private function createComment($login,$text,$record_id){
        $this->action = 'createComment';
        $this->actionParams[login] = $login;
        $this->actionParams[text] = $text;
        $this->actionParams[idRecord] = $record_id;
    }

    private function deleteComment($id){
        $this->action = 'deleteComment';
        $this->actionParams[idComment] = $id;
    }

}