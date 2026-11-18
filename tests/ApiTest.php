<?php

namespace Tests;

use PDO;
use PHPUnit\Framework\TestCase;
use TaskMaster\Model\Task;

class ApiTest extends TestCase
{
    // Teste de integração: precisa do servidor rodando (php -S localhost:8000)
    public function testApiRetornaListaEmJson(): void
    {
        $contexto = stream_context_create(['http' => ['timeout' => 2]]);
        $content = @file_get_contents('http://localhost:8000/api.php?action=list', false, $contexto);

        if ($content === false) {
            $this->markTestSkipped('Servidor fora do ar. Rode "php -S localhost:8000" em outro terminal.');
        }

        $data = json_decode($content, true);
        $this->assertIsArray($data);
    }

    // Teste isolado: não depende de servidor nem do tasks.sqlite real
    public function testModelGeraJsonValido(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE TABLE tasks (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            description TEXT,
            due_date TEXT,
            done INTEGER DEFAULT 0
        )");

        $model = new Task($pdo);
        $model->save('Estudar MVVM', 'Capítulo 4', '2026-12-31');

        $json = json_encode($model->getAll());
        $data = json_decode($json, true);

        $this->assertJson($json);
        $this->assertCount(1, $data);
        $this->assertSame('Estudar MVVM', $data[0]['title']);
    }
}