<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Master</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6; color: #333; display: flex; justify-content: center; padding: 50px 15px; }
        .container { background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 100%; max-width: 560px; }
        h1 { font-size: 1.5rem; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .error { color: #dc2626; background: #fee2e2; padding: 10px; border-radius: 4px; font-size: 0.9rem; }
        form { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; margin-bottom: 20px; }
        label { font-size: 0.85rem; font-weight: 600; display: flex; flex-direction: column; gap: 4px; }
        .required { color: #dc2626; }
        .optional { color: #9ca3af; font-weight: 400; }
        .row { display: flex; gap: 10px; }
        .row label { flex: 1; }
        input[type="text"], input[type="date"], textarea { padding: 10px; border: 1px solid #ccc; border-radius: 4px; font-family: inherit; font-size: 0.95rem; }
        textarea { resize: vertical; min-height: 70px; }
        button { background: #2563eb; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #1d4ed8; }
        ul { list-style: none; padding: 0; }
        li { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; padding: 12px; border-bottom: 1px solid #eee; }
        li.done .title { text-decoration: line-through; color: #9ca3af; }
        .title { font-weight: 600; }
        .description { font-size: 0.9rem; color: #555; margin: 4px 0; white-space: pre-line; }
        .meta { font-size: 0.8rem; color: #6b7280; display: flex; flex-wrap: wrap; gap: 12px; }
        .overdue { color: #dc2626; font-weight: 600; }
        .actions { white-space: nowrap; }
        .actions a { text-decoration: none; margin-left: 10px; cursor: pointer; font-size: 0.85rem; color: #2563eb; }
        .actions a:last-child { color: #dc2626; }
    </style>
</head>
<body>

<div class="container">
    <h1>Task Master</h1>

    <?php if (!empty($error)): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <label>
            <span>Título <span class="required">*</span></span>
            <input type="text" name="title" placeholder="O que precisa ser feito?" autocomplete="off"
                   value="<?= htmlspecialchars($old['title'] ?? '') ?>">
        </label>

        <label>
            <span>Descrição <span class="optional">(opcional)</span></span>
            <textarea name="description" placeholder="Detalhes da tarefa..."><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </label>

        <div class="row">
            <label>
                <span>Data de vencimento <span class="required">*</span></span>
                <input type="date" name="due_date" value="<?= htmlspecialchars($old['due_date'] ?? '') ?>">
            </label>
            <label>
                <span>Responsável <span class="required">*</span></span>
                <input type="text" name="responsible" placeholder="Nome da pessoa" autocomplete="off"
                       value="<?= htmlspecialchars($old['responsible'] ?? '') ?>">
            </label>
        </div>

        <button type="submit">Adicionar</button>
    </form>

    <ul>
        <?php foreach ($tasks as $task): ?>
            <?php
                $dueFormatted = '';
                $isOverdue = false;
                if (!empty($task['due_date'])) {
                    $dueObj = DateTime::createFromFormat('Y-m-d', $task['due_date']);
                    $dueFormatted = $dueObj ? $dueObj->format('d/m/Y') : $task['due_date'];
                    $isOverdue = !$task['done'] && $task['due_date'] < ($today ?? date('Y-m-d'));
                }
            ?>
            <li class="<?= $task['done'] ? 'done' : '' ?>">
                <div>
                    <div class="title"><?= htmlspecialchars($task['title']) ?></div>

                    <?php if (!empty($task['description'])): ?>
                        <div class="description"><?= htmlspecialchars($task['description']) ?></div>
                    <?php endif; ?>

                    <div class="meta">
                        <span>Responsável: <?= htmlspecialchars($task['responsible'] ?: 'Sem responsável') ?></span>
                        <span class="<?= $isOverdue ? 'overdue' : '' ?>">
                            Vencimento: <?= $dueFormatted ? htmlspecialchars($dueFormatted) : 'Sem data' ?>
                            <?= $isOverdue ? '(atrasada)' : '' ?>
                        </span>
                    </div>
                </div>

                <div class="actions">
                    <?php if (!$task['done']): ?>
                        <a href="?action=complete&id=<?= $task['id'] ?>" title="Concluir">Concluir</a>
                    <?php endif; ?>
                    <a href="?action=delete&id=<?= $task['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?');" title="Excluir">Excluir</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>