const fallbackManuals = [
    {
        title: 'Agentes',
        category: 'Cadastros',
        group: 'Agentes',
        description: 'Manual para cadastrar e manter agentes no ERP Geweb.',
        updatedAt: '03/06/2026',
        pdf: ''
    },
    {
        title: 'Tipos de agente',
        category: 'Cadastros',
        group: 'Agentes',
        description: 'Orientacoes sobre os tipos de agente usados no sistema.',
        updatedAt: '03/06/2026',
        pdf: ''
    },
    {
        title: 'Vendedores',
        category: 'Cadastros',
        group: 'Agentes',
        description: 'Passo a passo para cadastrar vendedores.',
        updatedAt: '03/06/2026',
        pdf: ''
    },
    {
        title: 'Transportadoras',
        category: 'Cadastros',
        group: 'Agentes',
        description: 'Manual para cadastro e manutencao de transportadoras.',
        updatedAt: '03/06/2026',
        pdf: ''
    },
    {
        title: 'Cidades',
        category: 'Cadastros',
        group: 'Agentes',
        description: 'Como cadastrar cidades e revisar dados relacionados.',
        updatedAt: '03/06/2026',
        pdf: ''
    },
    {
        title: 'Produtos',
        category: 'Cadastros',
        group: 'Produtos',
        description: 'Manual para cadastrar produtos, campos principais e dados comerciais.',
        updatedAt: '03/06/2026',
        pdf: ''
    },
    {
        title: 'Familias',
        category: 'Cadastros',
        group: 'Produtos',
        description: 'Como cadastrar familias para organizar os produtos.',
        updatedAt: '03/06/2026',
        pdf: ''
    },
    {
        title: 'Grupos',
        category: 'Cadastros',
        group: 'Produtos',
        description: 'Como organizar produtos em grupos no ERP.',
        updatedAt: '03/06/2026',
        pdf: ''
    },
    {
        title: 'Regra de tributacao',
        category: 'Cadastros',
        group: 'Produtos',
        description: 'Manual para regras fiscais e tributarias ligadas aos produtos.',
        updatedAt: '03/06/2026',
        pdf: ''
    },
    {
        title: 'Tabela de preco',
        category: 'Cadastros',
        group: 'Produtos',
        description: 'Como configurar e atualizar tabelas de preco.',
        updatedAt: '03/06/2026',
        pdf: ''
    }
];

const manuals = Array.isArray(window.MANUALS) ? window.MANUALS : fallbackManuals;
const grid = document.querySelector('#manualGrid');
const emptyState = document.querySelector('#emptyState');
const manualCount = document.querySelector('#manualCount');
const heroManualCount = document.querySelector('#heroManualCount');
const searchInput = document.querySelector('#manualSearch');
const searchBox = document.querySelector('.hero-search');
const filters = [...document.querySelectorAll('.category-filter')];
const header = document.querySelector('.site-header');
const modal = document.querySelector('#manualModal');
const openModalButton = document.querySelector('[data-open-manual-modal]');
const closeModalButton = document.querySelector('[data-close-manual-modal]');

let activeFilter = 'todos';

function normalize(value) {
    return value
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
}

function iconFor(manual) {
    if (manual.group === 'Agentes') {
        return 'icon-users';
    }

    if (manual.group === 'Produtos') {
        return 'icon-box';
    }

    return 'icon-file';
}

function renderManuals() {
    const term = normalize(searchInput.value.trim());
    const filtered = manuals.filter((manual) => {
        const haystack = normalize(`${manual.title} ${manual.category} ${manual.group} ${manual.description}`);
        const matchesSearch = !term || haystack.includes(term);
        const matchesFilter = activeFilter === 'todos'
            || manual.category === activeFilter
            || manual.group === activeFilter;

        return matchesSearch && matchesFilter;
    });

    grid.innerHTML = filtered.map((manual, index) => `
        <article class="manual-card" style="transition-delay: ${Math.min(index * 45, 260)}ms">
            <div>
                <span class="manual-icon"><svg><use href="#${iconFor(manual)}"></use></svg></span>
                <span class="manual-path">${manual.category} / ${manual.group}</span>
                <h3>${manual.title}</h3>
                <p>${manual.description}</p>
            </div>
            <div class="card-footer">
                <span class="badge">Atualizado em ${manual.updatedAt}</span>
                ${manual.pdf
                    ? `<a class="button" href="${manual.pdf}" target="_blank" rel="noopener" download>Baixar PDF</a>`
                    : '<span class="badge">PDF pendente</span>'}
            </div>
        </article>
    `).join('');

    manualCount.textContent = `${filtered.length} manual${filtered.length === 1 ? '' : 's'}`;
    emptyState.hidden = filtered.length > 0;

    requestAnimationFrame(() => {
        document.querySelectorAll('.manual-card').forEach((card) => card.classList.add('is-visible'));
    });
}

function updateHeaderState() {
    header.classList.toggle('is-compact', window.scrollY > 40);
}

function setupRevealAnimations() {
    const items = [...document.querySelectorAll('.reveal')];

    if (!('IntersectionObserver' in window)) {
        items.forEach((item) => item.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    items.forEach((item) => observer.observe(item));
}

searchInput.addEventListener('input', renderManuals);
searchInput.addEventListener('focus', () => searchBox.classList.add('is-focused'));
searchInput.addEventListener('blur', () => searchBox.classList.remove('is-focused'));

filters.forEach((button) => {
    button.addEventListener('click', () => {
        activeFilter = button.dataset.filter;
        filters.forEach((item) => item.classList.toggle('is-active', item === button));
        renderManuals();
    });
});

window.addEventListener('scroll', updateHeaderState, { passive: true });

if (openModalButton && modal) {
    openModalButton.addEventListener('click', () => modal.showModal());
}

if (closeModalButton && modal) {
    closeModalButton.addEventListener('click', () => modal.close());
}

if (modal) {
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.close();
        }
    });
}

heroManualCount.textContent = String(manuals.length);
setupRevealAnimations();
updateHeaderState();
renderManuals();
