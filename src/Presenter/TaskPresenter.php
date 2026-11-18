<?php

namespace TaskMaster\Presenter;

use Exception;
use TaskMaster\Model\Task;
use TaskMaster\View\TaskViewInterface;

class TaskPresenter
{
    private Task $model;
    private TaskViewInterface $view;

    // INJEÇÃO DE DEPENDÊNCIA: o Presenter exige o Model e a INTERFACE da View
    public function __construct(Task $model, TaskViewInterface $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function index(): void
    {
        try {
            $tasks = $this->model->getAll();

            // Lógica de formatação da apresentação (só existe no MVP)
            foreach ($tasks as &$task) {
                $task['title'] = mb_strtoupper($task['title']);
            }
            unset($task);

            $this->view->displayTasks($tasks);
        } catch (Exception $e) {
            $this->view->showError($e->getMessage());
        }
    }

    public function create($title, $description, $dueDate): void
    {
        try {
            $this->model->save($title, $description, $dueDate);
            $this->redirect();
        } catch (Exception $e) {
            $this->view->showError($e->getMessage());
        }
    }

    public function complete($id): void
    {
        $this->model->complete($id);
        $this->redirect();
    }

    public function delete($id): void
    {
        $this->model->delete($id);
        $this->redirect();
    }

    // Isolado em um método para poder ser sobrescrito nos testes
    protected function redirect(): void
    {
        header("Location: index.php");
        exit;
    }
}