// JS para la versión estática: carga projects.json y renderiza tarjetas
async function loadProjects() {
    try {
        const res = await fetch('projects.json', {cache: 'no-store'});
        if (!res.ok) throw new Error('No se pudo cargar projects.json');
        const projects = await res.json();
        renderGrid(projects);
    } catch (err) {
        document.getElementById('grid').textContent = 'Error cargando proyectos: ' + err.message;
    }
}

function renderGrid(projects) {
    const grid = document.getElementById('grid');
    grid.innerHTML = '';
    const isGitHubPages = window.location.hostname.includes('github.io');
    projects.forEach(p => {
        const article = document.createElement('article');
        article.className = 'card';
        const imgSrc = `assets/img/${p.name}.svg`;
        article.innerHTML = `
            <div class="card-thumb"><img src="${imgSrc}" alt="${escapeHtml(p.name)}" onerror="this.onerror=null;this.src='assets/img/placeholder.svg'"></div>
            <div class="card-body">
                <h3>${escapeHtml(p.name)}</h3>
                <p class="desc">${escapeHtml(p.desc || 'Sistema en PHP + MySQL. Haz clic en "Ver demo" para abrir el demo.')}</p>
                <div class="badges">
                  ${(p.tech||[]).map(t=>`<span class="badge">${escapeHtml(t)}</span>`).join('')}
                </div>
                <div class="card-actions">
                    ${isGitHubPages ? 
                        `<button class="btn" onclick="alert('Los demos requieren PHP y MySQL. Por favor, descarga el código desde GitHub y ejecútalo localmente con XAMPP o Docker.')">Ver demo</button>` :
                        `<a class="btn" target="_blank" href="${escapeHtml(p.path)}">Ver demo</a>`
                    }
                    <button class="btn ghost js-details" data-name="${escapeHtml(p.name)}" data-path="${escapeHtml(p.path)}" data-desc="${escapeHtml(p.desc||'')}">Detalles</button>
                </div>
            </div>`;
        grid.appendChild(article);
    });
    attachDetailHandlers();
}

function attachDetailHandlers() {
    document.querySelectorAll('.js-details').forEach(btn => {
        btn.addEventListener('click', () => {
            const name = btn.dataset.name;
            const path = btn.dataset.path;
            const desc = btn.dataset.desc || 'Sin descripción.';
            const modal = document.getElementById('modal');
            document.getElementById('modal-title').textContent = name;
            document.getElementById('modal-body').innerHTML = '<p>' + escapeHtml(desc) + '</p><p><strong>Ruta demo:</strong> ' + escapeHtml(path) + '</p>';
            const open = document.getElementById('modal-open');
            open.href = path;
            modal.setAttribute('aria-hidden','false');
        });
    });
}

document.getElementById('modal-close').addEventListener('click', function(){
    document.getElementById('modal').setAttribute('aria-hidden','true');
});

function escapeHtml(s){
    return String(s).replace(/[&<>"']/g, function(m){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[m]});
}

// iniciar
loadProjects();
