<?php
require __DIR__ . '/config.php';

$manuais = carregar_manuais();
$usuarioLogado = usuario_atual();
$podeGerenciar = usuario_e_admin_geweb();
$mensagem = mensagem_temporaria();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manual Geweb</title>
    <link rel="stylesheet" href="<?= escapar(url('assets/css/styles.css')) ?>">
</head>
<body>
    <svg class="simbolos-svg" aria-hidden="true">
        <symbol id="icon-file" viewBox="0 0 24 24"><path d="M5 2.8h9.1L20 8.7v12.5H5V2.8zm8.4 1.9v5.1h5.1l-5.1-5.1zM8 13h8v-1.8H8V13zm0 3.3h8v-1.8H8v1.8zm0 3.2h5.8v-1.8H8v1.8z"/></symbol>
        <symbol id="icon-building" viewBox="0 0 24 24"><path d="M4 21V3h10v4h6v14h-6v-4h-4v4H4Zm3-3h2v-2H7v2Zm0-5h2v-2H7v2Zm0-5h2V6H7v2Zm5 5h2v-2h-2v2Zm0-5h2V6h-2v2Zm4 10h2v-2h-2v2Zm0-5h2v-2h-2v2Z"/></symbol>
        <symbol id="icon-play" viewBox="0 0 24 24"><path d="M8 5v14l11-7L8 5Z"/></symbol>
    </svg>

    <div class="aplicativo-manuais">
        <header class="topo-sistema">
            <div class="marca-sistema">
                <img src="<?= escapar(url('assets/img/Geweb-sistemas.png')) ?>" alt="Geweb Sistemas">
                <div>
                    <strong>Central de manuais</strong>
                    <span>ERP Geweb | Biblioteca de treinamentos</span>
                </div>
            </div>
            <div class="lado-direito-topo">
                <div class="links-topo" aria-label="Atalhos">
                    <a href="#tituloManuais">Biblioteca</a>
                    <a href="#buscaManual">Pesquisar</a>
                    <a href="#rodapeSistema">Contato</a>
                </div>
                <nav class="acoes-topo" aria-label="Acoes do usuario">
                    <?php if (esta_logado()): ?>
                        <span class="etiqueta-usuario"><?= escapar($usuarioLogado) ?></span>
                        <a href="<?= escapar(url_app('logout.php')) ?>">Sair</a>
                    <?php else: ?>
                        <a href="<?= escapar(url_app('login.php')) ?>">Login</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>

        <?php if ($mensagem): ?>
            <div class="mensagem-pagina <?= escapar($mensagem['tipo']) ?>"><?= escapar($mensagem['mensagem']) ?></div>
        <?php endif; ?>

        <main class="area-trabalho">
            <section class="painel-manuais" aria-labelledby="tituloManuais">
                <div class="cabecalho-lista">
                    <div>
                        <p class="rotulo">Biblioteca</p>
                        <h1 id="tituloManuais">Manuais disponiveis</h1>
                    </div>
                    <div class="acoes-biblioteca">
                        <span id="totalManuais" class="contador"></span>
                        <?php if ($podeGerenciar): ?>
                            <button class="botao-criar-manual" type="button" data-abrir-modal-manual aria-label="Criar manual" title="Criar manual">
                                <img src="<?= escapar(url('assets/img/inclui.gif')) ?>" alt="">
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="filtrosCategorias" class="filtros-categorias" aria-label="Categorias"></div>
                <div id="filtrosAssuntos" class="filtros-assuntos" aria-label="Assuntos" hidden></div>
                <div id="gradeManuais" class="grade-manuais"></div>
                <div id="estadoVazio" class="estado-vazio" hidden>
                    <h2>Nenhum manual encontrado</h2>
                    <p>Pesquise por outro termo, empresa ou categoria.</p>
                </div>
            </section>

            <aside class="barra-pesquisa" aria-label="Pesquisa de manuais">
                <div class="cartao-busca">
                    <div class="cabecalho-busca">
                        <img src="<?= escapar(url('assets/img/localiza.gif')) ?>" alt="">
                        <div>
                            <strong>Pesquisar</strong>
                            <span>Manual, empresa ou rotina</span>
                        </div>
                    </div>
                    <label>
                        Buscar
                        <input id="buscaManual" type="search" >
                    </label>
                    <label>
                        Categoria
                        <select id="categoriaBusca">
                            <option value="todos">Todas</option>
                        </select>
                    </label>
                    <div class="resumo-busca">
                        <strong id="totalEmpresas">0</strong>
                        <span>empresas com manuais cadastrados</span>
                    </div>
                </div>

            </aside>
        </main>

        <?php if ($podeGerenciar): ?>
            <dialog class="modal-manual" id="modalManual">
                <form class="janela-cadastro" action="<?= escapar(url_app('save_manual.php')) ?>" method="post" enctype="multipart/form-data">
                    <div class="barra-janela">
                        <strong>Cadastro de manual</strong>
                        <button type="button" class="fechar-janela" data-fechar-modal-manual aria-label="Fechar">x</button>
                    </div>

                    <div class="corpo-janela">
                        <div class="grade-formulario">
                            <label>
                                Tipo
                                <select name="tipo" id="tipoManual">
                                    <option value="geral">Manual geral</option>
                                    <option value="empresa">Manual de empresa</option>
                                </select>
                            </label>
                            <label id="campoEmpresaManual">
                                Empresa
                                <input name="empresa" placeholder="SOGAMAX">
                            </label>
                            <label>
                                Titulo
                                <input name="titulo" placeholder="Cadastro de clientes" required>
                            </label>
                            <label>
                                Categoria
                                <input name="categoria" placeholder="Cadastros, Financeiros, Integracoes..." required>
                            </label>
                            <label>
                                Assunto
                                <input name="assunto" placeholder="Agentes, clientes, NFe...">
                            </label>
                            <label>
                                Atualizado em
                                <input name="atualizadoEm" value="<?= escapar(date('d/m/Y')) ?>" required>
                            </label>
                            <label>
                                URL do YouTube
                                <input type="url" name="videoYoutube" placeholder="https://www.youtube.com/watch?v=...">
                            </label>
                        </div>

                        <label>
                            Descricao
                            <textarea name="descricao" rows="5" placeholder="Resumo curto do manual." required></textarea>
                        </label>

                        <label>
                            PDF do manual
                            <input type="file" name="pdf" accept="application/pdf">
                        </label>

                        <div class="rodape-janela">
                            <button class="botao-gravar" type="submit">Gravar</button>
                        </div>
                    </div>
                </form>
            </dialog>
        <?php endif; ?>

        <footer id="rodapeSistema" class="rodape-sistema">
            <div class="rodape-marca">
                <img src="<?= escapar(url('assets/img/Geweb-sistemas.png')) ?>" alt="Geweb Sistemas">
                <div>
                    <strong>Geweb Informatica</strong>
                    <span>Organizacao local para validar a central de manuais do ERP.</span>
                </div>
            </div>
            <div class="rodape-contatos">
                <a href="tel:+551636028840">(16) 3602-8840</a>
                <a href="mailto:comercial@geweb.com.br">comercial@geweb.com.br</a>
                <a href="https://wa.me/1636028840" target="_blank" rel="noopener">WhatsApp</a>
            </div>
        </footer>
    </div>

    <script>
        window.MANUAIS = <?= json_encode($manuais, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="<?= escapar(url('assets/js/app.js')) ?>"></script>
</body>
</html>
