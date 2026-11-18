<?php

namespace Tests;

use Exception;
use PHPUnit\Framework\TestCase;
use TaskMaster\Model\Task;
use TaskMaster\Presenter\TaskPresenter;
use TaskMaster\View\TaskViewInterface;

class TaskPresenterTest extends TestCase
{
    public function testIndexFormataTitulosParaMaiusculo(): void
    {
        $modelMock = $this->createMock(Task::class);
        $modelMock->method('getAll')->willReturn([
            ['id' => 1, 'title' => 'estudar php', 'description' => '', 'due_date' => '', 'done' => 0]
        ]);

        $viewMock = $this->createMock(TaskViewInterface::class);

        $viewMock->expects($this->once())
            ->method('displayTasks')
            ->with($this->callback(function (array $tasks): bool {
                return $tasks[0]['title'] === 'ESTUDAR PHP';
            }));

        $presenter = new TaskPresenter($modelMock, $viewMock);
        $presenter->index();
    }

    public function testMaiusculoRespeitaAcentos(): void
    {
        $modelMock = $this->createMock(Task::class);
        $modelMock->method('getAll')->willReturn([
            ['id' => 1, 'title' => 'revisão de ação', 'description' => '', 'due_date' => '', 'done' => 0]
        ]);

        $viewMock = $this->createMock(TaskViewInterface::class);
        $viewMock->expects($this->once())
            ->method('displayTasks')
            ->with($this->callback(function (array $tasks): bool {
                return $tasks[0]['title'] === 'REVISÃO DE AÇÃO';
            }));

        (new TaskPresenter($modelMock, $viewMock))->index();
    }

    public function testCreateComSucessoSalvaERedireciona(): void
    {
        $modelMock = $this->createMock(Task::class);
        $modelMock->expects($this->once())
            ->method('save')
            ->with('Estudar', 'Capítulo 3', '2026-12-31');

        $viewMock = $this->createMock(TaskViewInterface::class);
        $viewMock->expects($this->never())->method('showError');

        // Subclasse de teste: não chama header()/exit de verdade
        $presenter = new class($modelMock, $viewMock) extends TaskPresenter {
            public bool $redirecionou = false;

            protected function redirect(): void
            {
                $this->redirecionou = true;
            }
        };

        $presenter->create('Estudar', 'Capítulo 3', '2026-12-31');

        $this->assertTrue($presenter->redirecionou);
    }

    public function testCreateComErroMandaViewExibirErro(): void
    {
        $modelMock = $this->createMock(Task::class);
        $modelMock->method('save')
            ->willThrowException(new Exception('Título e Data são obrigatórios.'));

        $viewMock = $this->createMock(TaskViewInterface::class);
        $viewMock->expects($this->once())
            ->method('showError')
            ->with('Título e Data são obrigatórios.');

        (new TaskPresenter($modelMock, $viewMock))->create('', '', '');
    }

    public function testCompleteChamaModelERedireciona(): void
    {
        $modelMock = $this->createMock(Task::class);
        $modelMock->expects($this->once())->method('complete')->with(5);

        $viewMock = $this->createMock(TaskViewInterface::class);

        $presenter = new class($modelMock, $viewMock) extends TaskPresenter {
            public bool $redirecionou = false;

            protected function redirect(): void
            {
                $this->redirecionou = true;
            }
        };

        $presenter->complete(5);

        $this->assertTrue($presenter->redirecionou);
    }
}