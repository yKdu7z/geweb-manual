<?php
require __DIR__ . '/config.php';

if (!is_geweb_admin()) {
    flash('Seu usuario nao tem permissao para cadastrar manuais.', 'error');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

try {
    $title = trim((string) ($_POST['title'] ?? ''));
    $category = trim((string) ($_POST['category'] ?? ''));
    $group = trim((string) ($_POST['group'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $updatedAt = trim((string) ($_POST['updatedAt'] ?? date('d/m/Y')));

    if ($title === '' || $category === '' || $group === '' || $description === '') {
        throw new RuntimeException('Preencha todos os campos obrigatorios.');
    }

    $manuals = load_manuals();
    $manuals[] = [
        'title' => $title,
        'category' => $category,
        'group' => $group,
        'description' => $description,
        'updatedAt' => $updatedAt,
        'pdf' => save_pdf($_FILES['pdf'] ?? []),
    ];

    save_manuals($manuals);
    flash('Manual cadastrado com sucesso.');
} catch (Throwable $exception) {
    flash($exception->getMessage(), 'error');
}

header('Location: index.php#manuais');
exit;
