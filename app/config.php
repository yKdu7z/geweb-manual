<?php
declare(strict_types=1);

const RAIZ_PROJETO = __DIR__ . '/..';
const PASTA_DADOS = RAIZ_PROJETO . '/storage/data';
const ARQUIVO_DADOS = PASTA_DADOS . '/manuais.json';
const PASTA_UPLOADS = RAIZ_PROJETO . '/storage/uploads';
const PASTA_SESSOES = RAIZ_PROJETO . '/storage/sessions';
const TAMANHO_MAXIMO_UPLOAD = 25 * 1024 * 1024;

if (session_status() === PHP_SESSION_NONE) {
    if (!is_dir(PASTA_SESSOES)) {
        mkdir(PASTA_SESSOES, 0775, true);
    }
    session_save_path(PASTA_SESSOES);
    session_start();
}

function escapar(?string $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function url_base(): string
{
    $pastaScript = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '')));

    if (str_ends_with($pastaScript, '/app')) {
        $pastaScript = substr($pastaScript, 0, -4);
    }

    return rtrim($pastaScript, '/');
}

function url(string $caminho): string
{
    return url_base() . '/' . ltrim($caminho, '/');
}

function url_app(string $caminho): string
{
    return url('app/' . ltrim($caminho, '/'));
}

function usuario_atual(): string
{
    return (string) ($_SESSION['usuario_manual'] ?? '');
}

function esta_logado(): bool
{
    return usuario_atual() !== '';
}

function usuario_e_admin_geweb(): bool
{
    return stripos(usuario_atual(), 'geweb') === 0;
}

function garantir_armazenamento(): void
{
    if (!is_dir(PASTA_DADOS)) {
        mkdir(PASTA_DADOS, 0775, true);
    }

    if (!is_dir(PASTA_UPLOADS)) {
        mkdir(PASTA_UPLOADS, 0775, true);
    }

    if (!file_exists(ARQUIVO_DADOS)) {
        salvar_manuais(manuais_padrao());
    }
}

function manuais_padrao(): array
{
    return [
        ['tipo' => 'geral', 'empresa' => '', 'titulo' => 'Cadastro de agentes', 'categoria' => 'Cadastros', 'descricao' => 'Manual para cadastrar e manter agentes no ERP Geweb.', 'atualizadoEm' => '03/06/2026', 'pdf' => '', 'videoYoutube' => ''],
        ['tipo' => 'geral', 'empresa' => '', 'titulo' => 'Contas a receber', 'categoria' => 'Financeiros', 'descricao' => 'Fluxo base para conferir titulos, baixas e recebimentos.', 'atualizadoEm' => '03/06/2026', 'pdf' => '', 'videoYoutube' => ''],
        ['tipo' => 'geral', 'empresa' => '', 'titulo' => 'Integracao de pedidos', 'categoria' => 'Integracoes', 'descricao' => 'Orientacoes para acompanhar importacoes e integracoes externas.', 'atualizadoEm' => '03/06/2026', 'pdf' => '', 'videoYoutube' => ''],
        ['tipo' => 'empresa', 'empresa' => 'JASPE', 'titulo' => 'Separacao por lote', 'categoria' => 'Operacional', 'descricao' => 'Rotina especifica da JASPE para separacao e conferencia de lotes.', 'atualizadoEm' => '03/06/2026', 'pdf' => '', 'videoYoutube' => ''],
        ['tipo' => 'empresa', 'empresa' => 'SOGAMAX', 'titulo' => 'Pedido integrado', 'categoria' => 'Integracoes', 'descricao' => 'Como revisar pedidos recebidos pela integracao da SOGAMAX.', 'atualizadoEm' => '03/06/2026', 'pdf' => '', 'videoYoutube' => ''],
        ['tipo' => 'empresa', 'empresa' => 'DROGAMAIS', 'titulo' => 'Tabela de preco por filial', 'categoria' => 'Financeiros', 'descricao' => 'Manual da DROGAMAIS para trabalhar com tabelas por filial.', 'atualizadoEm' => '03/06/2026', 'pdf' => '', 'videoYoutube' => ''],
        ['tipo' => 'empresa', 'empresa' => 'MEDICAMENTAL', 'titulo' => 'Conferencia de produtos controlados', 'categoria' => 'Operacional', 'descricao' => 'Processo especifico para conferencia de produtos controlados.', 'atualizadoEm' => '03/06/2026', 'pdf' => '', 'videoYoutube' => ''],
        ['tipo' => 'empresa', 'empresa' => 'CHOLMED', 'titulo' => 'Cadastro especial de clientes', 'categoria' => 'Cadastros', 'descricao' => 'Campos e regras usados pela CHOLMED no cadastro de clientes.', 'atualizadoEm' => '03/06/2026', 'pdf' => '', 'videoYoutube' => ''],
    ];
}

function carregar_manuais(): array
{
    garantir_armazenamento();
    $manuais = json_decode((string) file_get_contents(ARQUIVO_DADOS), true);
    $manuais = is_array($manuais) ? $manuais : manuais_padrao();
    return array_map('normalizar_manual', $manuais);
}

function salvar_manuais(array $manuais): void
{
    if (!is_dir(PASTA_DADOS)) {
        mkdir(PASTA_DADOS, 0775, true);
    }

    file_put_contents(ARQUIVO_DADOS, json_encode(array_values($manuais), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

function salvar_pdf(array $arquivo): string
{
    if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }

    if (($arquivo['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('Falha ao enviar o PDF.');
    }

    if (($arquivo['size'] ?? 0) > TAMANHO_MAXIMO_UPLOAD) {
        throw new RuntimeException('O PDF deve ter no maximo 25 MB.');
    }

    $extensao = strtolower(pathinfo((string) $arquivo['name'], PATHINFO_EXTENSION));
    if ($extensao !== 'pdf') {
        throw new RuntimeException('Envie somente arquivo PDF.');
    }

    garantir_armazenamento();
    $nome = 'manual-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.pdf';
    $destino = PASTA_UPLOADS . '/' . $nome;

    if (!move_uploaded_file((string) $arquivo['tmp_name'], $destino)) {
        throw new RuntimeException('Nao foi possivel salvar o PDF.');
    }

    return url('storage/uploads/' . $nome);
}

function normalizar_manual(array $manual): array
{
    $empresa = trim((string) ($manual['empresa'] ?? ''));
    $tipo = (string) ($manual['tipo'] ?? ($empresa !== '' ? 'empresa' : 'geral'));

    return [
        'tipo' => $tipo === 'empresa' ? 'empresa' : 'geral',
        'empresa' => strtoupper($empresa),
        'titulo' => (string) ($manual['titulo'] ?? ''),
        'categoria' => (string) ($manual['categoria'] ?? ($manual['grupo'] ?? 'Geral')),
        'assunto' => (string) ($manual['assunto'] ?? ''),
        'descricao' => (string) ($manual['descricao'] ?? ''),
        'atualizadoEm' => (string) ($manual['atualizadoEm'] ?? date('d/m/Y')),
        'pdf' => (string) ($manual['pdf'] ?? ''),
        'videoYoutube' => (string) ($manual['videoYoutube'] ?? ''),
    ];
}

function mensagem_temporaria(?string $mensagem = null, string $tipo = 'sucesso'): ?array
{
    if ($mensagem !== null) {
        $_SESSION['mensagem_temporaria'] = ['mensagem' => $mensagem, 'tipo' => $tipo];
        return null;
    }

    if (empty($_SESSION['mensagem_temporaria'])) {
        return null;
    }

    $mensagem = $_SESSION['mensagem_temporaria'];
    unset($_SESSION['mensagem_temporaria']);
    return $mensagem;
}


