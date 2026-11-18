<?php
// api.php - O PHP agora é apenas um Provedor de Dados (API)
require_once __DIR__ . '/vendor/autoload.php';

use TaskMaster\Model\Task;

// O navegador deve entender que estamos enviando JSON
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? 'list';

try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/tasks.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $model = new Task($pdo);

    switch ($action) {
        case 'list':
            echo json_encode($model->getAll());
            break;

        case 'create':
            // Recebe dados JSON do Front-end
            $data = json_decode(file_get_contents('php://input'), true) ?? [];
            $model->save($data['title'] ?? '', $data['description'] ?? '', $data['due_date'] ?? '');
            echo json_encode(['status' => 'success']);
            break;

        case 'complete':
            $model->complete($_GET['id'] ?? 0);
            echo json_encode(['status' => 'success']);
            break;

        case 'delete':
            $model->delete($_GET['id'] ?? 0);
            echo json_encode(['status' => 'success']);
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Ação desconhecida.']);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}