<?php

namespace TaskMaster\Presenter;

use TaskMaster\Model\Task;
use TaskMaster\View\TaskViewInterface;

class TaskPresenter
{
    private Task $model;
    private TaskViewInterface $view;

    public function __construct(Task $model, TaskViewInterface $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function index(): void
    {
        $tasks = $this->model->getAll();

        foreach ($tasks as &$task) {
            $task['title'] = mb_strtoupper($task['title']);
        }

        $this->view->displayTasks($tasks);
    }
}