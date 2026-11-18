<?php

namespace TaskMaster\View;

interface TaskViewInterface {
    // A View DEVE saber exibir uma lista de tarefas
    public function displayTasks(array $tasks): void;
    
    // A View DEVE saber exibir uma mensagem de erro
    public function showError(string $message): void;
}
