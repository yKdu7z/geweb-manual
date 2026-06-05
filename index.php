<?php
require __DIR__ . '/config.php';

$manuals = load_manuals();
$loggedUser = current_user();
$canManage = is_geweb_admin();
$message = flash();
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manual Geweb</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body id="top">
    <svg class="svg-sprite" aria-hidden="true">
        <symbol id="icon-phone" viewBox="0 0 24 24"><path d="M6.6 10.8c1.7 3.3 3.4 5 6.6 6.6l2.2-2.2c.3-.3.8-.4 1.2-.2 1.3.4 2.6.6 4 .6.7 0 1.2.5 1.2 1.2v3.5c0 .7-.5 1.2-1.2 1.2C10.3 21.5 2.5 13.7 2.5 3.4c0-.7.5-1.2 1.2-1.2h3.5c.7 0 1.2.5 1.2 1.2 0 1.4.2 2.8.6 4 .1.4 0 .8-.3 1.2l-2.1 2.2z"/></symbol>
        <symbol id="icon-mail" viewBox="0 0 24 24"><path d="M3.8 5h16.4c.9 0 1.6.7 1.6 1.6v10.8c0 .9-.7 1.6-1.6 1.6H3.8c-.9 0-1.6-.7-1.6-1.6V6.6C2.2 5.7 2.9 5 3.8 5zm.4 2.4v.1l7.8 5.1 7.8-5.1v-.1H4.2zm15.6 2.4-7 4.6c-.5.3-1.1.3-1.6 0l-7-4.6v6.8c0 .2.2.4.4.4h14.8c.2 0 .4-.2.4-.4V9.8z"/></symbol>
        <symbol id="icon-location" viewBox="0 0 24 24"><path d="M12 22s7-6.2 7-13A7 7 0 0 0 5 9c0 6.8 7 13 7 13zm0-9.2A2.8 2.8 0 1 1 12 7a2.8 2.8 0 0 1 0 5.8z"/></symbol>
        <symbol id="icon-clock" viewBox="0 0 24 24"><path d="M12 2.2A9.8 9.8 0 1 0 12 21.8 9.8 9.8 0 0 0 12 2.2zm1 10.1 3.8 2.2-1 1.7-4.8-2.8V6.2h2v6.1z"/></symbol>
        <symbol id="icon-book" viewBox="0 0 24 24"><path d="M5 3.5h11.2A2.8 2.8 0 0 1 19 6.3v15.2H6.4A3.4 3.4 0 0 1 3 18.1V5.5c0-1.1.9-2 2-2zm.8 14.2c-.5 0-.9.4-.9.9s.4.9.9.9H17v-1.8H5.8zM7 6.5v8h10V6.3c0-.4-.3-.8-.8-.8H7.5c-.3 0-.5.2-.5.5z"/></symbol>
        <symbol id="icon-search" viewBox="0 0 24 24"><path d="m20.5 19-4.4-4.4a7.4 7.4 0 1 0-1.6 1.6l4.4 4.4L20.5 19zM5.2 10.3a5.1 5.1 0 1 1 10.2 0 5.1 5.1 0 0 1-10.2 0z"/></symbol>
        <symbol id="icon-file" viewBox="0 0 24 24"><path d="M5 2.8h9.1L20 8.7v12.5H5V2.8zm8.4 1.9v5.1h5.1l-5.1-5.1zM8 13h8v-1.8H8V13zm0 3.3h8v-1.8H8v1.8zm0 3.2h5.8v-1.8H8v1.8z"/></symbol>
        <symbol id="icon-box" viewBox="0 0 24 24"><path d="M12 2.2 21 7v10l-9 4.8L3 17V7l9-4.8zm0 2.3L6.1 7.6 12 10.7l5.9-3.1L12 4.5zm-7 5.1v5.8l6 3.2v-5.8L5 9.6zm8 9 6-3.2V9.6l-6 3.2v5.8z"/></symbol>
        <symbol id="icon-users" viewBox="0 0 24 24"><path d="M8.5 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0 1.8c3.2 0 6.3 1.5 6.3 3.7v1.7H2.2v-1.7c0-2.2 3.1-3.7 6.3-3.7zm8-1.4a3.2 3.2 0 1 1 0-6.4 3.2 3.2 0 0 1 0 6.4zm.4 1.6c2.8.2 5 1.5 5 3.4v1.8h-4.5v-1.7c0-1.1-.6-2.1-1.6-2.9.3-.2.6-.4.9-.6z"/></symbol>
        <symbol id="icon-arrow" viewBox="0 0 24 24"><path d="M13.3 5.3 20 12l-6.7 6.7-1.5-1.5 4.1-4.1H4v-2.2h11.9l-4.1-4.1 1.5-1.5z"/></symbol>
        <symbol id="icon-facebook" viewBox="0 0 24 24"><path d="M14.2 8.1h2.2V4.6c-.4-.1-1.7-.2-3.2-.2-3.2 0-5.4 1.9-5.4 5.5v3.1H4.5v3.9h3.3v7h4v-7h3.3l.5-3.9h-3.8V10.3c0-1.1.3-2.2 2.4-2.2z"/></symbol>
        <symbol id="icon-x" viewBox="0 0 24 24"><path d="M14.2 10.4 21.5 2h-2.6l-6 6.9L8.1 2H2.5l7.7 11.1L2.4 22h2.6l6.4-7.4 5.1 7.4h5.6l-7.9-11.6zm-2.3 2.7-1.2-1.7L5.8 4h1.9l4 5.8 1.2 1.7 5.2 7.6h-1.9l-4.3-6z"/></symbol>
        <symbol id="icon-skype" viewBox="0 0 24 24"><path d="M18.2 14.3c.1-.5.2-1 .2-1.5a7.1 7.1 0 0 0-8.6-7c-.9-.6-1.9-.9-3-.9a5.4 5.4 0 0 0-5.4 5.4c0 1.1.3 2.1.9 3a7.1 7.1 0 0 0 8.6 8.6c.8.5 1.8.8 2.8.8a5.4 5.4 0 0 0 5.4-5.4c0-1-.3-2-.9-2.8zm-6.1 4.1c-2.4 0-4.4-1-4.4-2.3 0-.6.5-1.1 1.2-1.1 1 0 1.1 1.4 3 1.4 1 0 1.7-.4 1.7-1 0-.8-.8-1-2.2-1.3-2-.4-3.7-1-3.7-3.1 0-2 1.9-3.2 4.1-3.2 2.5 0 4.1 1.1 4.1 2.2 0 .7-.5 1.2-1.2 1.2-.9 0-1.1-1.2-2.8-1.2-.9 0-1.4.4-1.4.9 0 .7.8.8 2.2 1.1 2 .4 3.8 1.1 3.8 3.2 0 2-1.9 3.2-4.4 3.2z"/></symbol>
        <symbol id="icon-linkedin" viewBox="0 0 24 24"><path d="M5.1 8.4h3.7V22H5.1V8.4zM6.9 2a2.1 2.1 0 1 1 0 4.2A2.1 2.1 0 0 1 6.9 2zm5.5 6.4h3.5v1.9h.1c.5-1 1.8-2.2 3.6-2.2 3.9 0 4.6 2.5 4.6 5.8V22h-3.7v-7.2c0-1.7 0-3.9-2.4-3.9s-2.8 1.9-2.8 3.8V22h-3.7V8.4z"/></symbol>
    </svg>

    <header class="site-header">
        <div class="utility-bar">
            <div class="container utility-inner">
                <span>Bem-vindo a Geweb Inform&aacute;tica</span>
                <div class="quick-links">
                    <a href="tel:+551636028840"><svg><use href="#icon-phone"></use></svg>+55 (16) 3602-8840</a>
                    <a href="mailto:comercial@geweb.com.br"><svg><use href="#icon-mail"></use></svg>comercial@geweb.com.br</a>
                    <a href="https://www.skype.com" target="_blank" rel="noopener"><svg><use href="#icon-skype"></use></svg>Skype: comercialgeweb</a>
                </div>
            </div>
        </div>

        <div class="hero-shell">
            <nav class="container nav-bar" aria-label="Navega&ccedil;&atilde;o do manual">
                <a class="brand" href="#top" aria-label="Manual Geweb">
                    <img src="Geweb-sistemas.png" alt="Geweb Sistemas">
                </a>
                <div class="nav-actions">
                    <a href="#manuais">Manuais</a>
                    <a href="#contato">Contato</a>
                    <?php if (is_logged_in()): ?>
                        <span class="user-chip"><?= e($loggedUser) ?></span>
                        <a href="logout.php">Sair</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                    <?php endif; ?>
                    <div class="social-line" aria-label="Redes sociais">
                        <a class="social-icon" href="https://www.facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><svg><use href="#icon-facebook"></use></svg></a>
                        <a class="social-icon" href="https://www.x.com" target="_blank" rel="noopener" aria-label="X"><svg><use href="#icon-x"></use></svg></a>
                        <a class="social-icon" href="https://www.skype.com" target="_blank" rel="noopener" aria-label="Skype"><svg><use href="#icon-skype"></use></svg></a>
                        <a class="social-icon" href="https://www.linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><svg><use href="#icon-linkedin"></use></svg></a>
                    </div>
                </div>
            </nav>

            <section class="container hero">
                <div class="hero-copy reveal">
                    <p class="eyebrow">Central de conhecimento</p>
                    <h1>Manuais do Geweb em um s&oacute; lugar.</h1>
                    <p>Consulte rotinas do sistema, encontre o assunto pelo nome e baixe o PDF correto quando ele estiver dispon&iacute;vel.</p>
                    <label class="hero-search">
                        <svg><use href="#icon-search"></use></svg>
                        <input id="manualSearch" type="search" placeholder="Buscar por produto, vendedor, tributa&ccedil;&atilde;o...">
                    </label>
                </div>

                <div class="hero-panel reveal">
                    <div class="panel-card">
                        <svg><use href="#icon-book"></use></svg>
                        <span id="heroManualCount">10</span>
                        <strong>manuais mapeados</strong>
                    </div>
                    <div class="contact-card">
                        <div>
                            <svg><use href="#icon-location"></use></svg>
                            <p>R. Jos&eacute; Bianchi, 555 - Sl. 2412<br>Nova Ribeir&atilde;nea - Ribeir&atilde;o Preto - SP</p>
                        </div>
                        <div>
                            <svg><use href="#icon-clock"></use></svg>
                            <p>Seg - Sex 08.00 - 18.00<br>Sab | Dom Fechado</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </header>

    <main>
        <?php if ($message): ?>
            <div class="flash-page container <?= e($message['type']) ?>"><?= e($message['message']) ?></div>
        <?php endif; ?>

        <section class="notice container reveal" aria-label="Aviso importante">
            <svg><use href="#icon-file"></use></svg>
            <p><strong>Aten&ccedil;&atilde;o:</strong> alguns manuais podem ser antigos e, com atualiza&ccedil;&otilde;es do sistema, novos campos podem surgir. Se tiver d&uacute;vida, chame o suporte no WhatsApp <a href="https://wa.me/163602-8840" target="_blank" rel="noopener">(16) 3602-8840</a>.</p>
        </section>

        <section id="manuais" class="manuals-area container">
            <div class="section-head reveal">
                <div>
                    <p class="eyebrow">Biblioteca</p>
                    <h2>Escolha o manual</h2>
                </div>
                <span id="manualCount" class="count"></span>
            </div>

            <div class="filter-row reveal" aria-label="Categorias">
                <button class="category-filter is-active" type="button" data-filter="todos">Todos</button>
                <button class="category-filter" type="button" data-filter="Cadastros">Cadastros</button>
                <button class="category-filter" type="button" data-filter="Agentes">Agentes</button>
                <button class="category-filter" type="button" data-filter="Produtos">Produtos</button>
            </div>

            <div id="manualGrid" class="manual-grid"></div>
            <div id="emptyState" class="empty-state" hidden>
                <h3>Nenhum manual encontrado</h3>
                <p>Tente buscar por outro termo ou selecione outra categoria.</p>
            </div>
        </section>
    </main>

    <?php if ($canManage): ?>
        <button class="manual-admin-button" type="button" aria-label="Cadastrar manual" data-open-manual-modal>
            <svg><use href="#icon-book"></use></svg>
        </button>

        <dialog class="manual-modal" id="manualModal">
            <form class="manual-modal-card" action="save_manual.php" method="post" enctype="multipart/form-data">
                <div class="modal-head">
                    <div>
                        <p class="eyebrow">Cadastro interno</p>
                        <h2>Novo manual</h2>
                    </div>
                    <button type="button" class="modal-close" data-close-manual-modal aria-label="Fechar">x</button>
                </div>

                <div class="form-grid">
                    <label>
                        Titulo
                        <input name="title" placeholder="Produtos" required>
                    </label>
                    <label>
                        Categoria
                        <input name="category" placeholder="Cadastros" required>
                    </label>
                    <label>
                        Grupo
                        <input name="group" placeholder="Produtos" required>
                    </label>
                    <label>
                        Atualizado em
                        <input name="updatedAt" value="<?= e(date('d/m/Y')) ?>" required>
                    </label>
                </div>

                <label>
                    Descricao
                    <textarea name="description" rows="4" placeholder="Resumo curto do manual." required></textarea>
                </label>

                <label>
                    PDF do manual
                    <input type="file" name="pdf" accept="application/pdf">
                </label>

                <button class="support-button" type="submit">
                    Salvar manual
                    <svg><use href="#icon-arrow"></use></svg>
                </button>
            </form>
        </dialog>
    <?php endif; ?>

    <footer id="contato" class="site-footer">
        <div class="container footer-cta reveal">
            <div>
                <p class="eyebrow">Suporte Geweb</p>
                <h2>Precisa confirmar uma rotina do ERP?</h2>
                <p>Use a central de manuais primeiro. Se o PDF estiver antigo ou faltar algum campo, fale com o suporte.</p>
            </div>
            <a class="support-button" href="https://wa.me/1636028840" target="_blank" rel="noopener">
                Chamar suporte
                <svg><use href="#icon-arrow"></use></svg>
            </a>
        </div>

        <div class="container footer-grid">
            <section class="footer-about reveal">
                <img src="Geweb-sistemas.png" alt="Geweb Sistemas">
                <p>Desde 2002, a GEWEB INFORM&Aacute;TICA atua com solu&ccedil;&otilde;es para agilizar processos e reduzir custos operacionais.</p>
            </section>
            <section class="footer-contact reveal">
                <h2>Contato</h2>
                <p><svg><use href="#icon-location"></use></svg>R. Jos&eacute; Bianchi, 555 - Sl. 2412, Ribeir&atilde;o Preto - SP</p>
                <p><svg><use href="#icon-phone"></use></svg>+55 (16) 3602.8840</p>
                <p><svg><use href="#icon-mail"></use></svg>comercial@geweb.com.br</p>
                <p><svg><use href="#icon-clock"></use></svg>Seg - Sex 8.00 - 18.00</p>
            </section>
            <section class="footer-social reveal">
                <h2>Redes sociais</h2>
                <div class="social-line">
                    <a class="social-icon" href="https://www.facebook.com" target="_blank" rel="noopener" aria-label="Facebook"><svg><use href="#icon-facebook"></use></svg></a>
                    <a class="social-icon" href="https://www.x.com" target="_blank" rel="noopener" aria-label="X"><svg><use href="#icon-x"></use></svg></a>
                    <a class="social-icon" href="https://www.skype.com" target="_blank" rel="noopener" aria-label="Skype"><svg><use href="#icon-skype"></use></svg></a>
                    <a class="social-icon" href="https://www.linkedin.com" target="_blank" rel="noopener" aria-label="LinkedIn"><svg><use href="#icon-linkedin"></use></svg></a>
                </div>
            </section>
        </div>

        <div class="footer-bottom">
            <div class="container footer-bottom-inner">
                <strong>&copy; 2022 Geweb Inform&aacute;tica</strong>
                <a class="to-top" href="#top" aria-label="Voltar ao topo"><svg><use href="#icon-arrow"></use></svg></a>
            </div>
        </div>
    </footer>

    <script>
        window.MANUALS = <?= json_encode($manuals, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="assets/app.js"></script>
</body>
</html>
