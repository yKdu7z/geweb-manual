<?php
declare(strict_types=1);

const DATA_FILE = __DIR__ . '/data/manuals.json';
const UPLOAD_DIR = __DIR__ . '/uploads';
const SESSION_DIR = __DIR__ . '/data/sessions';
const MAX_UPLOAD_BYTES = 25 * 1024 * 1024;

if (session_status() === PHP_SESSION_NONE) {
    if (!is_dir(SESSION_DIR)) {
        mkdir(SESSION_DIR, 0775, true);
    }
    session_save_path(SESSION_DIR);
    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_user(): string
{
    return (string) ($_SESSION['manual_user'] ?? '');
}

function is_logged_in(): bool
{
    return current_user() !== '';
}

function is_geweb_admin(): bool
{
    return stripos(current_user(), 'geweb') === 0;
}

function ensure_storage(): void
{
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0775, true);
    }

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0775, true);
    }

    if (!file_exists(DATA_FILE)) {
        save_manuals(default_manuals());
    }
}

function default_manuals(): array
{
    return [
        ['title' => 'Agentes', 'category' => 'Cadastros', 'group' => 'Agentes', 'description' => 'Manual para cadastrar e manter agentes no ERP Geweb.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
        ['title' => 'Tipos de agente', 'category' => 'Cadastros', 'group' => 'Agentes', 'description' => 'Orientacoes sobre os tipos de agente usados no sistema.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
        ['title' => 'Vendedores', 'category' => 'Cadastros', 'group' => 'Agentes', 'description' => 'Passo a passo para cadastrar vendedores.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
        ['title' => 'Transportadoras', 'category' => 'Cadastros', 'group' => 'Agentes', 'description' => 'Manual para cadastro e manutencao de transportadoras.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
        ['title' => 'Cidades', 'category' => 'Cadastros', 'group' => 'Agentes', 'description' => 'Como cadastrar cidades e revisar dados relacionados.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
        ['title' => 'Produtos', 'category' => 'Cadastros', 'group' => 'Produtos', 'description' => 'Manual para cadastrar produtos, campos principais e dados comerciais.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
        ['title' => 'Familias', 'category' => 'Cadastros', 'group' => 'Produtos', 'description' => 'Como cadastrar familias para organizar os produtos.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
        ['title' => 'Grupos', 'category' => 'Cadastros', 'group' => 'Produtos', 'description' => 'Como organizar produtos em grupos no ERP.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
        ['title' => 'Regra de tributacao', 'category' => 'Cadastros', 'group' => 'Produtos', 'description' => 'Manual para regras fiscais e tributarias ligadas aos produtos.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
        ['title' => 'Tabela de preco', 'category' => 'Cadastros', 'group' => 'Produtos', 'description' => 'Como configurar e atualizar tabelas de preco.', 'updatedAt' => '03/06/2026', 'pdf' => ''],
    ];
}

function load_manuals(): array
{
    ensure_storage();
    $manuals = json_decode((string) file_get_contents(DATA_FILE), true);
    return is_array($manuals) ? $manuals : default_manuals();
}

function save_manuals(array $manuals): void
{
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0775, true);
    }

    file_put_contents(DATA_FILE, json_encode(array_values($manuals), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function save_pdf(array $file): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falha ao enviar o PDF.');
    }

    if (($file['size'] ?? 0) > MAX_UPLOAD_BYTES) {
        throw new RuntimeException('O PDF deve ter no maximo 25 MB.');
    }

    $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        throw new RuntimeException('Envie somente arquivo PDF.');
    }

    ensure_storage();
    $name = 'manual-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.pdf';
    $destination = UPLOAD_DIR . '/' . $name;

    if (!move_uploaded_file((string) $file['tmp_name'], $destination)) {
        throw new RuntimeException('Nao foi possivel salvar o PDF.');
    }

    return 'uploads/' . $name;
}

function flash(?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
        return null;
    }

    if (empty($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
