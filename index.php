<?php
// Autoload do Composer (PSR-4: namespace TaskMaster\ => pasta src/)
require_once __DIR__ . '/vendor/autoload.php';

use TaskMaster\Model\Task;
use TaskMaster\Presenter\TaskPresenter;
use TaskMaster\View\TaskHtmlView;

$pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. Instanciamos o Model
$model = new Task($pdo);

// 2. Instanciamos a View (que implementa TaskViewInterface)
$view = new TaskHtmlView();

// 3. Instanciamos o Presenter, injetando as dependências
$presenter = new TaskPresenter($model, $view);

// Roteamento
$action = $_GET['action'] ?? 'index';

if ($action === 'create') {
    $presenter->create($_POST['title'] ?? '', $_POST['description'] ?? '', $_POST['due_date'] ?? '');
} elseif ($action === 'complete') {
    $presenter->complete($_GET['id'] ?? 0);
} elseif ($action === 'delete') {
    $presenter->delete($_GET['id'] ?? 0);
} else {
    $presenter->index();
}