const manuaisPadrao = [];
const manuais = Array.isArray(window.MANUAIS) ? window.MANUAIS : manuaisPadrao;

const gradeManuais = document.querySelector('#gradeManuais');
const estadoVazio = document.querySelector('#estadoVazio');
const totalManuais = document.querySelector('#totalManuais');
const totalEmpresas = document.querySelector('#totalEmpresas');
const campoBusca = document.querySelector('#buscaManual');
const categoriaBusca = document.querySelector('#categoriaBusca');
const filtrosCategorias = document.querySelector('#filtrosCategorias');
const filtrosAssuntos = document.querySelector('#filtrosAssuntos');
const modalManual = document.querySelector('#modalManual');
const botaoAbrirModal = document.querySelector('[data-abrir-modal-manual]');
const botaoFecharModal = document.querySelector('[data-fechar-modal-manual]');
const tipoManual = document.querySelector('#tipoManual');
const campoEmpresaManual = document.querySelector('#campoEmpresaManual');

let categoriaAtiva = 'todos';
let assuntoAtivo = 'todos';

function normalizar(valor) {
    return String(valor)
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

function escaparHtml(valor) {
    return String(valor)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function textoDoManual(manual) {
    return normalizar(`${manual.titulo} ${manual.categoria} ${manual.assunto} ${manual.empresa} ${manual.descricao}`);
}

function empresasCadastradas() {
    return [...new Set(
        manuais
            .filter((manual) => manual.tipo === 'empresa' && manual.empresa)
            .map((manual) => manual.empresa)
    )];
}

function categoriasCadastradas() {
    return [...new Set(manuais.map((manual) => manual.categoria).filter(Boolean))].sort();
}

function assuntoDoManual(manual) {
    if (manual.assunto) {
        return manual.assunto;
    }

    const texto = normalizar(`${manual.titulo} ${manual.descricao}`);
    const assuntosConhecidos = [
        ['agente', 'Agentes'],
        ['cliente', 'Clientes'],
        ['produto', 'Produtos'],
        ['nfe', 'NFe'],
        ['nota fiscal', 'NFe'],
        ['pedido', 'Pedidos'],
        ['preco', 'Tabela de preco'],
        ['lote', 'Lotes'],
        ['receber', 'Contas a receber'],
        ['integracao', 'Integracoes']
    ];
    const encontrado = assuntosConhecidos.find(([termo]) => texto.includes(termo));
    return encontrado ? encontrado[1] : 'Outros';
}

function assuntosDaCategoria() {
    if (categoriaAtiva === 'todos') {
        return [];
    }

    return [...new Set(
        manuais
            .filter((manual) => manual.categoria === categoriaAtiva)
            .map(assuntoDoManual)
            .filter(Boolean)
    )].sort();
}

function filtrarManuais() {
    const termo = normalizar(campoBusca.value.trim());

    return manuais.filter((manual) => {
        const combinaTermo = !termo || textoDoManual(manual).includes(termo);
        const combinaCategoria = categoriaAtiva === 'todos' || manual.categoria === categoriaAtiva;
        const combinaAssunto = assuntoAtivo === 'todos' || assuntoDoManual(manual) === assuntoAtivo;
        return combinaTermo && combinaCategoria && combinaAssunto;
    });
}

function classeDoManual(manual) {
    return manual.tipo === 'empresa' ? 'empresa' : 'geral';
}

function rotuloDoManual(manual) {
    return manual.tipo === 'empresa' && manual.empresa ? manual.empresa : 'Manual geral';
}

function iconeDoManual(manual) {
    return manual.tipo === 'empresa' ? 'icon-building' : 'icon-file';
}

function linksDoManual(manual) {
    const links = [];

    if (manual.pdf) {
        links.push(`<a class="botao-acao" href="${escaparHtml(manual.pdf)}" target="_blank" rel="noopener" download>PDF</a>`);
    }

    if (manual.videoYoutube) {
        links.push(`<a class="botao-acao video" href="${escaparHtml(manual.videoYoutube)}" target="_blank" rel="noopener">Video</a>`);
    }

    return links.length ? links.join('') : '<span class="pendente">Material pendente</span>';
}

function renderizarCategorias() {
    const categorias = categoriasCadastradas();
    const opcoes = ['todos', ...categorias];

    filtrosCategorias.innerHTML = opcoes.map((categoria) => {
        const texto = categoria === 'todos' ? 'Todos' : categoria;
        const ativo = categoria === categoriaAtiva ? ' ativo' : '';
        return `<button class="filtro-rapido${ativo}" type="button" data-categoria="${escaparHtml(categoria)}">${escaparHtml(texto)}</button>`;
    }).join('');

    categoriaBusca.innerHTML = opcoes.map((categoria) => {
        const texto = categoria === 'todos' ? 'Todas' : categoria;
        const selecionado = categoria === categoriaAtiva ? ' selected' : '';
        return `<option value="${escaparHtml(categoria)}"${selecionado}>${escaparHtml(texto)}</option>`;
    }).join('');

    filtrosCategorias.querySelectorAll('[data-categoria]').forEach((botao) => {
        botao.addEventListener('click', () => {
            categoriaAtiva = botao.dataset.categoria;
            assuntoAtivo = 'todos';
            renderizarTudo();
        });
    });
}

function renderizarAssuntos() {
    if (!filtrosAssuntos) {
        return;
    }

    const assuntos = assuntosDaCategoria();
    filtrosAssuntos.hidden = categoriaAtiva === 'todos' || assuntos.length === 0;

    if (filtrosAssuntos.hidden) {
        filtrosAssuntos.innerHTML = '';
        assuntoAtivo = 'todos';
        return;
    }

    filtrosAssuntos.innerHTML = ['todos', ...assuntos].map((assunto) => {
        const texto = assunto === 'todos' ? 'Todos os assuntos' : assunto;
        const ativo = assunto === assuntoAtivo ? ' ativo' : '';
        return `<button class="filtro-assunto${ativo}" type="button" data-assunto="${escaparHtml(assunto)}">${escaparHtml(texto)}</button>`;
    }).join('');

    filtrosAssuntos.querySelectorAll('[data-assunto]').forEach((botao) => {
        botao.addEventListener('click', () => {
            assuntoAtivo = botao.dataset.assunto;
            renderizarTudo();
        });
    });
}

function renderizarManuais() {
    const filtrados = filtrarManuais();

    gradeManuais.innerHTML = filtrados.map((manual) => `
        <article class="cartao-manual ${classeDoManual(manual)}">
            <div class="topo-cartao">
                <span class="icone-manual"><svg><use href="#${iconeDoManual(manual)}"></use></svg></span>
                <span class="tipo-manual">${escaparHtml(rotuloDoManual(manual))}</span>
            </div>
            <h2>${escaparHtml(manual.titulo)}</h2>
            <p>${escaparHtml(manual.descricao)}</p>
            <div class="metadados-manual">
                <span>${escaparHtml(manual.categoria)}</span>
                <span>${escaparHtml(assuntoDoManual(manual))}</span>
                <span>${escaparHtml(manual.atualizadoEm)}</span>
            </div>
            <div class="acoes-manual">${linksDoManual(manual)}</div>
        </article>
    `).join('');

    totalManuais.textContent = `${filtrados.length} ${filtrados.length === 1 ? 'manual' : 'manuais'}`;
    estadoVazio.hidden = filtrados.length > 0;
}

function renderizarResumo() {
    totalEmpresas.textContent = String(empresasCadastradas().length);
}

function renderizarTudo() {
    renderizarCategorias();
    renderizarAssuntos();
    renderizarResumo();
    renderizarManuais();
}

campoBusca.addEventListener('input', renderizarManuais);

categoriaBusca.addEventListener('change', () => {
    categoriaAtiva = categoriaBusca.value;
    assuntoAtivo = 'todos';
    renderizarTudo();
});

if (botaoAbrirModal && modalManual) {
    botaoAbrirModal.addEventListener('click', () => modalManual.showModal());
}

if (botaoFecharModal && modalManual) {
    botaoFecharModal.addEventListener('click', () => modalManual.close());
}

if (modalManual) {
    modalManual.addEventListener('click', (evento) => {
        if (evento.target === modalManual) {
            modalManual.close();
        }
    });
}

if (tipoManual && campoEmpresaManual) {
    const atualizarCampoEmpresa = () => {
        campoEmpresaManual.classList.toggle('campo-oculto', tipoManual.value !== 'empresa');
    };

    tipoManual.addEventListener('change', atualizarCampoEmpresa);
    atualizarCampoEmpresa();
}

renderizarTudo();
