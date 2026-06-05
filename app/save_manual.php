<?php
require __DIR__ . '/config.php';

if (!usuario_e_admin_geweb()) {
    mensagem_temporaria('Seu usuario nao tem permissao para cadastrar manuais.', 'erro');
    header('Location: ' . url_app('index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url_app('index.php'));
    exit;
}

try {
    $tipo = trim((string) ($_POST['tipo'] ?? 'geral'));
    $empresa = strtoupper(trim((string) ($_POST['empresa'] ?? '')));
    $titulo = trim((string) ($_POST['titulo'] ?? ''));
    $categoria = trim((string) ($_POST['categoria'] ?? ''));
    $assunto = trim((string) ($_POST['assunto'] ?? ''));
    $descricao = trim((string) ($_POST['descricao'] ?? ''));
    $atualizadoEm = trim((string) ($_POST['atualizadoEm'] ?? date('d/m/Y')));
    $videoYoutube = trim((string) ($_POST['videoYoutube'] ?? ''));

    if ($tipo !== 'empresa') {
        $tipo = 'geral';
        $empresa = '';
    }

    if ($tipo === 'empresa' && $empresa === '') {
        throw new RuntimeException('Informe a empresa do manual.');
    }

    if ($titulo === '' || $categoria === '' || $descricao === '') {
        throw new RuntimeException('Preencha todos os campos obrigatorios.');
    }

    if ($videoYoutube !== '' && !filter_var($videoYoutube, FILTER_VALIDATE_URL)) {
        throw new RuntimeException('Informe uma URL valida para o video.');
    }

    $hostVideo = strtolower((string) parse_url($videoYoutube, PHP_URL_HOST));
    if ($videoYoutube !== '' && !in_array($hostVideo, ['youtube.com', 'www.youtube.com', 'youtu.be', 'www.youtu.be'], true)) {
        throw new RuntimeException('Informe uma URL do YouTube.');
    }

    $manuais = carregar_manuais();
    $manuais[] = [
        'tipo' => $tipo,
        'empresa' => $empresa,
        'titulo' => $titulo,
        'categoria' => $categoria,
        'assunto' => $assunto,
        'descricao' => $descricao,
        'atualizadoEm' => $atualizadoEm,
        'pdf' => salvar_pdf($_FILES['pdf'] ?? []),
        'videoYoutube' => $videoYoutube,
    ];

    salvar_manuais($manuais);
    mensagem_temporaria('Manual cadastrado com sucesso.');
} catch (Throwable $excecao) {
    mensagem_temporaria($excecao->getMessage(), 'erro');
}

header('Location: ' . url_app('index.php#manuais'));
exit;


