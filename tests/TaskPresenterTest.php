<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use TaskMaster\Model\Task;
use TaskMaster\View\TaskViewInterface;
use TaskMaster\Presenter\TaskPresenter;

class TaskPresenterTest extends TestCase
{
    public function testIndexFormataTitulosParaMaiusculo(): void
    {
        // 1. Mock do Model Task
        $modelMock = $this->createMock(Task::class);
        $modelMock->method('getAll')->willReturn([
            ['id' => 1, 'title' => 'estudar php', 'description' => '', 'due_date' => '', 'done' => 0]
        ]);

        // 2. Mock da View Interface
        $viewMock = $this->createMock(TaskViewInterface::class);

        // 3. Expectativa: verificar se o Presenter envia o título em MAIÚSCULO
        $viewMock->expects($this->once())
            ->method('displayTasks')
            ->with($this->callback(function (array $tasks): bool {
                return $tasks[0]['title'] === 'ESTUDAR PHP';
            }));

        // 4. Injeção de dependências e execução
        $presenter = new TaskPresenter($modelMock, $viewMock);
        $presenter->index();
    }
}