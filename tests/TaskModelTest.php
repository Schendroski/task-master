<?php

namespace Tests;

PHPUnit\Framework\TestCase;
use TaskMaster\Model\TaskModel;
use PDO;

class TaskModelTest extends TestCase
{
    private PDO $pdo;
    private TaskModel $taskModel;

    protected function setUp(): void
    {
        // Cria um banco SQLite em memória para garantir testes isolados e rápidos
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("CREATE TABLE tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            due_date TEXT,
            responsible TEXT,
            done INTEGER DEFAULT 0
        )");

        $this->taskModel = new TaskModel($this->pdo);
    }

    public function test_can_create_and_fetch_tasks(): void
    {
        $this->taskModel->create('Estudar TDD', 'Criar testes unitários', '2026-11-15', 'Livia');

        $tasks = $this->taskModel->getAll();

        $this->assertCount(1, $tasks);
        $this->assertEquals('Estudar TDD', $tasks[0]['title']);
        $this->assertEquals('Livia', $tasks[0]['responsible']);
    }

    public function test_can_mark_task_as_completed(): void
    {
        $this->taskModel->create('Tarefa Pendente', '', '2026-11-20', 'Equipe');
        $tasks = $this->taskModel->getAll();
        $taskId = $tasks[0]['id'];

        $this->taskModel->complete($taskId);

        $updatedTasks = $this->taskModel->getAll();
        $this->assertEquals(1, $updatedTasks[0]['done']);
    }
}