// OdHCL - JS principal
// Maneja navegación, formularios, odontograma, pacientes y agenda

// --- Navegación Sidebar ---
// Contenedor global para notificaciones (toasts)
(function() {
    const c = document.createElement('div');
    c.id = 'toast-container';
    c.style.position = 'fixed';
    c.style.top = '12px';
    c.style.right = '12px';
    c.style.zIndex = 9999;
    document.body && document.body.appendChild(c);
})();

function showToast(msg, type = 'info', timeout = 3500) {
    const cont = document.getElementById('toast-container');
    if (!cont) return alert(msg); // fallback
    const t = document.createElement('div');
    t.className = 'toast ' + type;
    t.textContent = msg;
    t.style.background = type === 'error' ? '#e74c3c' : (type === 'success' ? '#27ae60' : '#333');
    t.style.color = '#fff';
    t.style.padding = '8px 12px';
    t.style.marginTop = '8px';
    t.style.borderRadius = '6px';
    t.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
    t.style.opacity = '0';
    t.style.transition = 'opacity 220ms ease, transform 220ms ease';
    cont.appendChild(t);
    requestAnimationFrame(() => { t.style.opacity = '1';
        t.style.transform = 'translateY(0)'; });
    setTimeout(() => { t.style.opacity = '0';
        t.style.transform = 'translateY(-6px)';
        setTimeout(() => t.remove(), 240); }, timeout);
}

// Modal de confirmación visual que devuelve Promise<boolean>
function showConfirm(message, title = 'Confirmar') {
    return new Promise(resolve => {
        // crear overlay si no existe
        let ov = document.getElementById('confirm-overlay');
        if (!ov) {
            ov = document.createElement('div');
            ov.id = 'confirm-overlay';
            ov.style.position = 'fixed';
            ov.style.left = 0;
            ov.style.top = 0;
            ov.style.right = 0;
            ov.style.bottom = 0;
            ov.style.background = 'rgba(0,0,0,0.45)';
            ov.style.display = 'flex';
            ov.style.alignItems = 'center';
            ov.style.justifyContent = 'center';
            ov.style.zIndex = 10000;
            const card = document.createElement('div');
            card.id = 'confirm-card';
            card.style.background = '#fff';
            card.style.padding = '18px';
            card.style.borderRadius = '8px';
            card.style.width = '360px';
            card.style.maxWidth = '92%';
            card.style.boxShadow = '0 6px 24px rgba(0,0,0,0.25)';
            card.innerHTML = `<h3 id="confirm-title"></h3><p id="confirm-message" style="margin-top:8px;margin-bottom:16px"></p><div style="display:flex;gap:8px;justify-content:flex-end"><button id="confirm-cancel">Cancelar</button><button id="confirm-ok">Aceptar</button></div>`;
            ov.appendChild(card);
            document.body.appendChild(ov);
        }
        document.getElementById('confirm-title').textContent = title;
        document.getElementById('confirm-message').textContent = message;
        ov.style.display = 'flex';
        const ok = document.getElementById('confirm-ok');
        const cancel = document.getElementById('confirm-cancel');

        function cleanup(val) { ov.style.display = 'none';
            ok.removeEventListener('click', onOk);
            cancel.removeEventListener('click', onCancel);
            resolve(val); }

        function onOk() { cleanup(true); }

        function onCancel() { cleanup(false); }
        ok.addEventListener('click', onOk);
        cancel.addEventListener('click', onCancel);
    });
}
document.querySelectorAll('.sidebar nav a').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.sidebar nav a').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.section').forEach(sec => sec.style.display = 'none');
        const target = this.getAttribute('href').replace('#', '');
        const secEl = document.getElementById(target);
        secEl.style.display = '';
        // Inicializadores por sección
        const inits = {
            'agenda': () => { popularSelectPacientes();
                filtrarSelectPacientes(''); },
            'facturacion': () => { renderFacturacion();
                removePlusTextNodes(document.getElementById('facturacion')); },
            'reportes': () => { renderReportes(); },
            'configuracion': () => { renderConfiguracion(); }
        };
        if (inits[target]) inits[target]();
    });
});

// --- Dashboard Stats (simulación) ---
function actualizarDashboard() {
    const pacientes = obtenerPacientes();
    const citas = obtenerCitas();
    const boletas = obtenerBoletas();
    // resumen rapido
    const elTotalPac = document.getElementById('total-pacientes'); if (elTotalPac) elTotalPac.textContent = pacientes.length;
    const elCitasDia = document.getElementById('citas-dia'); if (elCitasDia) elCitasDia.textContent = obtenerCitasHoy().length;
    const elTotalHist = document.getElementById('total-historias'); if (elTotalHist) elTotalHist.textContent = pacientes.filter(p => p.historia).length;
    // Estadísticas adicionales (si existen elementos del dashboard)
    const statTotalCitas = document.getElementById('stat-total-citas'); if (statTotalCitas) statTotalCitas.textContent = citas.length;
    const statProx7 = document.getElementById('stat-prox-7'); if (statProx7) {
        const hoy = new Date();
        const prox = citas.filter(c => { const d = new Date(c.fecha + 'T00:00:00'); const diff = (d - new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate())) / (1000 * 60 * 60 * 24); return diff >= 0 && diff <= 7; });
        statProx7.textContent = prox.length;
    }
    const statBoletas = document.getElementById('stat-boletas'); if (statBoletas) statBoletas.textContent = boletas.length;

    // actualizar notificaciones, KPIs, listas y charts
    actualizarNotificaciones();
    renderKpis();
    renderRecentPatients();
    renderRecentBoletas();
    renderUpcomingCitas();
    renderCharts();
    // small UI animations
    try { animateDashboardCards(); } catch(e){}
}

// rellenar KPIs adicionales en el dashboard
function renderKpis() {
    const k = calcularKpis();
    const elSin = document.getElementById('stat-sin-citas');
    if (elSin) elSin.textContent = k.sinCitas;
    const elEdad = document.getElementById('stat-edad-prom');
    if (elEdad) elEdad.textContent = k.edadProm;
    const elIngresos = document.getElementById('stat-ingresos');
    if (elIngresos) elIngresos.textContent = '$' + Number(k.ingresos).toFixed(2);
    const elPorc = document.getElementById('stat-porc-hist');
    if (elPorc) elPorc.textContent = k.porcConHistoria + '%';

    // actualizar pequeños elementos que mostraban valores individuales
    const elTotalPac = document.getElementById('total-pacientes'); if (elTotalPac) elTotalPac.textContent = obtenerPacientes().length;
    const elCitasDia = document.getElementById('citas-dia'); if (elCitasDia) elCitasDia.textContent = obtenerCitasHoy().length;
    const elTotalHist = document.getElementById('total-historias'); if (elTotalHist) elTotalHist.textContent = obtenerPacientes().filter(p => p.historia).length;
}

// Render lists for dashboard
function renderRecentPatients() {
    const ul = document.getElementById('recent-patients');
    if (!ul) return;
    const pacientes = obtenerPacientes().slice().reverse().slice(0, 12);
    ul.innerHTML = pacientes.length === 0 ? '<li>No hay pacientes.</li>' : pacientes.map(p => `<li style="padding:6px 0;border-bottom:1px solid #f0f0f0">${p.nombre} ${p.apellido} ${p.dni? '<small style="color:#666;margin-left:6px">' + p.dni + '</small>':''}</li>`).join('');
    try { animateListItems(ul); } catch(e) {}
}

function renderRecentBoletas() {
    const ul = document.getElementById('recent-boletas');
    if (!ul) return;
    const boletas = obtenerBoletas().slice().sort((a,b) => (b.fecha||'').localeCompare(a.fecha||'')).slice(0,12);
    ul.innerHTML = boletas.length === 0 ? '<li>No hay boletas.</li>' : boletas.map(b => `<li style="padding:6px 0;border-bottom:1px solid #f0f0f0">${new Date(b.fecha).toLocaleDateString()} — ${b.paciente} <strong style="float:right">$${(b.total||0).toFixed(2)}</strong></li>`).join('');
    try { animateListItems(ul); } catch(e) {}
}

function renderUpcomingCitas() {
    const ul = document.getElementById('upcoming-citas');
    if (!ul) return;
    const hoy = new Date().setHours(0,0,0,0);
    const proximas = obtenerCitas().slice().filter(c => {
        const d = new Date(c.fecha + 'T00:00:00').getTime();
        const diff = Math.round((d - hoy) / (1000*60*60*24));
        return diff >= 0 && diff <= 7;
    }).sort((a,b) => (a.fecha + ' ' + a.hora).localeCompare(b.fecha + ' ' + b.hora)).slice(0,12);
    ul.innerHTML = proximas.length === 0 ? '<li>No hay citas próximas.</li>' : proximas.map(c => `<li style="padding:6px 0;border-bottom:1px solid #f0f0f0">${c.fecha} ${c.hora} — ${c.paciente}</li>`).join('');
    try { animateListItems(ul); } catch(e) {}
}

// Donut neumorfismo - calcular y actualizar SVGs

    // Render charts de barras para dashboard
    function renderCharts() {
        try {
            // Edad: buckets
            const edades = obtenerPacientes().map(p => Number(p.edad) || 0).filter(n => n > 0);
            const buckets = [0,10,20,30,40,50,60,70];
            const labelsAge = buckets.map((b,i) => i < buckets.length-1 ? `${b}-${buckets[i+1]-1}` : `${b}+`);
            const counts = labelsAge.map((lab, idx) => {
                if (idx < labelsAge.length -1) {
                    const min = buckets[idx]; const max = buckets[idx+1]-1;
                    return edades.filter(e => e >= min && e <= max).length;
                } else {
                    const min = buckets[buckets.length-1]; return edades.filter(e => e >= min).length;
                }
            });
        drawBarChartSVG('chart-age', labelsAge, counts, { color: 'var(--color-primary)' });

            // Citas próximas 7 días
            const hoy = new Date(); hoy.setHours(0,0,0,0);
            const labelsCitas = [];
            const countsCitas = [];
            for (let i=0;i<7;i++){
                const d = new Date(hoy); d.setDate(hoy.getDate()+i);
                const key = d.toISOString().slice(0,10);
                labelsCitas.push(d.toLocaleDateString());
                const c = obtenerCitas().filter(ci => ci.fecha === key).length;
                countsCitas.push(c);
            }
        drawBarChartSVG('chart-citas', labelsCitas, countsCitas, { color: 'var(--color-secondary)' });

            // Ingresos últimos 6 meses
            const boletas = obtenerBoletas().slice();
            const labelsIng = [];
            const valsIng = [];
            const now = new Date();
            for (let m=5; m>=0; m--) {
                const d = new Date(now.getFullYear(), now.getMonth()-m, 1);
                const yyyy = d.getFullYear(); const mm = String(d.getMonth()+1).padStart(2,'0');
                const key = `${yyyy}-${mm}`;
                labelsIng.push(d.toLocaleString(undefined, { month: 'short', year: 'numeric' }));
                const total = boletas.filter(b => (b.fecha||'').startsWith(key)).reduce((s,b)=>s + (Number(b.total)||0), 0);
                valsIng.push(total);
            }
        drawBarChartSVG('chart-ingresos', labelsIng, valsIng, { color: 'var(--color-accent)', format: v => '$' + v.toFixed(2) });
        // apply animations to charts shortly after drawing
        setTimeout(() => {
            try {
                const svgAge = document.getElementById('chart-age')?.querySelector('svg'); if (svgAge) animateChartBars(svgAge);
                const svgCitas = document.getElementById('chart-citas')?.querySelector('svg'); if (svgCitas) animateChartBars(svgCitas);
                const svgIng = document.getElementById('chart-ingresos')?.querySelector('svg'); if (svgIng) animateChartBars(svgIng);
            } catch (e) { /* ignore */ }
        }, 60);
        } catch (e) { console.error('renderCharts error', e); }
    }

    function drawBarChartSVG(containerId, labels, values, options = {}) {
        const cont = document.getElementById(containerId);
        if (!cont) return;
        cont.innerHTML = '';
        // create wrapper
        const wrapper = document.createElement('div'); wrapper.className = 'svg-chart';
        cont.appendChild(wrapper);

        // dimensions
        const cw = cont.clientWidth || 480;
        const ch = 180;
        const margin = { top: 12, right: 12, bottom: 48, left: 40 };
        const w = Math.max(200, cw) - margin.left - margin.right;
        const h = ch - margin.top - margin.bottom;

        const svgNS = 'http://www.w3.org/2000/svg';
        const svg = document.createElementNS(svgNS, 'svg');
        svg.setAttribute('width', (w + margin.left + margin.right));
        svg.setAttribute('height', (h + margin.top + margin.bottom));
        svg.setAttribute('viewBox', `0 0 ${w + margin.left + margin.right} ${h + margin.top + margin.bottom}`);
        wrapper.appendChild(svg);

        // scales
        const maxV = Math.max(1, ...values);
        const barW = Math.max(12, Math.floor(w / (labels.length * 1.6)));
        const gap = Math.max(8, Math.floor((w - labels.length * barW) / Math.max(1, labels.length + 1)));

        // grid lines and y axis
        const ticks = 4;
        for (let t = 0; t <= ticks; t++) {
            const y = margin.top + Math.round(h - (h * (t / ticks)));
            const val = Math.round((maxV * (t / ticks)));
            const line = document.createElementNS(svgNS, 'line');
            line.setAttribute('x1', margin.left);
            line.setAttribute('x2', margin.left + w);
            line.setAttribute('y1', y);
            line.setAttribute('y2', y);
            line.setAttribute('stroke', '#e6eef7');
            line.setAttribute('stroke-width', '1');
            svg.appendChild(line);

            const txt = document.createElementNS(svgNS, 'text');
            txt.setAttribute('x', margin.left - 8);
            txt.setAttribute('y', y + 4);
            txt.setAttribute('text-anchor', 'end');
            txt.setAttribute('font-size', '11');
            txt.setAttribute('fill', '#6b7280');
            txt.textContent = val;
            svg.appendChild(txt);
        }

        // bars
        let x = margin.left + gap;
        labels.forEach((lab, i) => {
            const v = values[i] || 0;
            const barH = maxV === 0 ? 0 : Math.round((v / maxV) * h);
            const bx = x;
            const by = margin.top + (h - barH);

            const rect = document.createElementNS(svgNS, 'rect');
            rect.setAttribute('x', bx);
            rect.setAttribute('y', by);
            rect.setAttribute('width', barW);
            rect.setAttribute('height', barH);
            rect.setAttribute('rx', 4);
            rect.setAttribute('fill', options.color || 'var(--color-primary)');
            rect.style.transition = 'height 400ms ease, y 400ms ease';
            svg.appendChild(rect);

            // value label (above bar)
            const vtxt = document.createElementNS(svgNS, 'text');
            vtxt.setAttribute('x', bx + barW / 2);
            vtxt.setAttribute('y', by - 6);
            vtxt.setAttribute('text-anchor', 'middle');
            vtxt.setAttribute('font-size', '11');
            vtxt.setAttribute('fill', '#111827');
            vtxt.textContent = options.format ? options.format(v) : String(v);
            svg.appendChild(vtxt);

            // x label
            const lbl = document.createElementNS(svgNS, 'text');
            lbl.setAttribute('x', bx + barW / 2);
            lbl.setAttribute('y', margin.top + h + 18);
            lbl.setAttribute('text-anchor', 'middle');
            lbl.setAttribute('font-size', '11');
            lbl.setAttribute('fill', '#374151');
            lbl.textContent = lab;
            svg.appendChild(lbl);

            // tooltip interactions
            rect.addEventListener('mouseenter', (ev) => showChartTooltip(ev, options.format ? options.format(v) : String(v)));
            rect.addEventListener('mouseleave', hideChartTooltip);
            rect.addEventListener('mousemove', (ev) => moveChartTooltip(ev));

            x += barW + gap;
        });

        // create a simple y axis label (optional)
        const axisLabel = document.createElementNS(svgNS, 'text');
        axisLabel.setAttribute('x', 10);
        axisLabel.setAttribute('y', margin.top - 2);
        axisLabel.setAttribute('font-size', '12');
        axisLabel.setAttribute('fill', '#374151');
        axisLabel.textContent = '';
        svg.appendChild(axisLabel);

        // tooltip element (singleton)
        if (!window._chartTooltipEl) {
            const t = document.createElement('div');
            t.id = 'chart-tooltip';
            t.className = 'chart-tooltip';
            t.style.position = 'fixed';
            t.style.display = 'none';
            t.style.zIndex = 99999;
            document.body.appendChild(t);
            window._chartTooltipEl = t;
        }

        // store redraw handle
    if (!window._chartResizeHandler) {
        window._chartResizeHandler = () => { setTimeout(renderCharts, 120); };
        window.addEventListener('resize', window._chartResizeHandler);
    }

}

// Chart tooltip helpers (global)
function showChartTooltip(ev, text) {
    const t = window._chartTooltipEl;
    if (!t) return;
    t.textContent = text;
    t.style.display = 'block';
    moveChartTooltip(ev);
}

function moveChartTooltip(ev) {
    const t = window._chartTooltipEl;
    if (!t) return;
    const pad = 10;
    let x = ev.clientX + pad;
    let y = ev.clientY + pad;
    const rect = t.getBoundingClientRect();
    if (x + rect.width + 8 > window.innerWidth) x = ev.clientX - rect.width - pad;
    if (y + rect.height + 8 > window.innerHeight) y = ev.clientY - rect.height - pad;
    t.style.left = x + 'px';
    t.style.top = y + 'px';
}

function hideChartTooltip() {
    const t = window._chartTooltipEl;
    if (!t) return;
    t.style.display = 'none';
}

// Animation helpers
function animateDashboardCards() {
    const cards = Array.from(document.querySelectorAll('.dashboard-grid .dash-card'));
    cards.forEach((c, i) => {
        setTimeout(() => c.classList.add('visible', 'anim-fade-up'), i * 90);
        setTimeout(() => c.classList.remove('anim-fade-up'), 700 + i * 40);
    });
}

function animateChartBars(svg) {
    if (!svg) return;
    const rects = Array.from(svg.querySelectorAll('rect'));
    rects.forEach((r, i) => {
        void r.getBoundingClientRect();
        setTimeout(() => r.classList.add('animate-grow'), i * 80);
    });
}

function animateListItems(ul) {
    if (!ul) return;
    const items = Array.from(ul.querySelectorAll('li'));
    items.forEach((li, idx) => {
        setTimeout(() => li.classList.add('visible'), idx * 40);
    });
}


// KPIs adicionales helper
function calcularKpis() {
    const pacientes = obtenerPacientes();
    const citas = obtenerCitas();
    const boletas = obtenerBoletas();
    const pacientesConCitas = new Set(citas.map(c => String(c.pacienteId)));
    const sinCitas = pacientes.filter(p => !pacientesConCitas.has(String(p.id))).length;
    const edades = pacientes.map(p => Number(p.edad) || 0).filter(e => e > 0);
    const edadProm = edades.length ? Math.round(edades.reduce((s, a) => s + a, 0) / edades.length) : 0;
    const ingresos = boletas.reduce((s, b) => s + (Number(b.total) || 0), 0);
    const porcConHistoria = pacientes.length ? Math.round((pacientes.filter(p => p.historia).length / pacientes.length) * 100) : 0;
    return { sinCitas, edadProm, ingresos, porcConHistoria };
}

function obtenerNotificaciones() {
    const citas = obtenerCitas();
    const boletas = obtenerBoletas();
    const hoy = new Date();
    // Notificaciones: citas dentro de hoy y proximos 3 dias, boletas en las ultimas 2 dias
    const nots = [];
    citas.forEach(c => {
        const d = new Date(c.fecha + 'T' + (c.hora || '00:00'));
        const diff = Math.ceil((d - hoy) / (1000 * 60 * 60 * 24));
        if (diff >= 0 && diff <= 3) nots.push({ type: 'cita', title: `Cita: ${c.paciente}`, text: `${c.fecha} ${c.hora}`, date: c.fecha });
    });
    boletas.slice().reverse().slice(0, 6).forEach(b => {
        const fecha = new Date(b.fecha);
        const diff = Math.ceil((hoy - fecha) / (1000 * 60 * 60 * 24));
        if (diff <= 7) nots.push({ type: 'boleta', title: `Boleta: ${b.paciente}`, text: `Total $${b.total.toFixed(2)}`, date: b.fecha });
    });
    // ordenar por fecha (reciente primero)
    nots.sort((a, b) => (b.date || '').localeCompare(a.date || ''));
    return nots;
}

function actualizarNotificaciones() {
    const nots = obtenerNotificaciones();
    const count = nots.length;
    const badge = document.getElementById('notif-count');
    if (badge) badge.textContent = count;
    const dropdown = document.getElementById('notif-dropdown');
    if (dropdown) {
        dropdown.innerHTML = '';
        if (nots.length === 0) dropdown.innerHTML = '<div class="notif-empty">No hay notificaciones.</div>';
        else nots.forEach(n => {
            const div = document.createElement('div');
            div.className = 'notif-item';
            div.innerHTML = `<div><div class="ni-title">${n.title}</div><small>${n.text}</small></div>`;
            dropdown.appendChild(div);
        });
    }
}

// Toggle notificaciones dropdown
document.addEventListener('click', e => {
    const btn = document.getElementById('notif-button');
    const dropdown = document.getElementById('notif-dropdown');
    if (!btn || !dropdown) return;
    if (e.target.closest && e.target.closest('#notif-button')) {
        const open = dropdown.style.display === 'block';
        dropdown.style.display = open ? 'none' : 'block';
        btn.setAttribute('aria-expanded', String(!open));
        return;
    }
    // click fuera: cerrar
    if (!e.target.closest || !e.target.closest('.notif-wrapper')) {
        dropdown.style.display = 'none';
        btn.setAttribute('aria-expanded', 'false');
    }
});

// --- Datos: ahora persistimos via API PHP y mantenemos caché en memoria ---
async function fetchJSON(url, opts) {
    try {
        const res = await fetch(url, Object.assign({credentials: 'same-origin'}, opts || {}));
        // Intentar parsear JSON, manejar errores de status
        const text = await res.text();
        try {
            return JSON.parse(text);
        } catch (e) {
            console.error('fetchJSON: invalid JSON from', url, 'status', res.status, 'body:', text);
            return null;
        }
    } catch (e) {
        console.error('fetchJSON network error', url, e);
        return null;
    }
}

// caches en memoria (síncronas para la UI)
window._pacientes = [];
window._citas = [];
window._boletas = [];
window._precios = null;
window._config = null;

function obtenerPacientes() { return window._pacientes || []; }
function obtenerCitas() { return window._citas || []; }
function obtenerBoletas() { return window._boletas || []; }
function obtenerPrecios() { return window._precios || { consulta: 30, limpieza: 20, obturacion: 50, corona: 200 }; }
// obtenerConfig se define más abajo junto a defaultConfig

function guardarPacientes(pacientes) {
    // actualizar cache inmediatamente para mantener UI reactiva
    window._pacientes = pacientes;
    // persistir en background (fire-and-forget)
    (async () => {
        try {
            for (const p of pacientes) {
                await fetchJSON('api/pacientes.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(p) });
            }
            await cargarDatosPacientes();
        } catch (e) { console.error('guardarPacientes background error', e); }
    })();
}

async function guardarCitas(citas) {
    for (const c of citas) {
    try { await fetchJSON('api/citas.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(c) }); } catch(e){ console.error(e); }
    }
    await cargarDatosCitas();
}

async function guardarPrecios(p) {
    await fetchJSON('api/precios.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(p) });
    window._precios = p;
}

async function guardarConfig(cfg) {
    await fetchJSON('api/config.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(cfg) });
    window._config = cfg;
}

async function cargarDatosPacientes() { const d = await fetchJSON('api/pacientes.php'); window._pacientes = Array.isArray(d) ? d : []; }
async function cargarDatosCitas() { const d = await fetchJSON('api/citas.php'); window._citas = Array.isArray(d) ? d : []; }
async function cargarDatosBoletas() { const d = await fetchJSON('api/boletas.php'); window._boletas = Array.isArray(d) ? d : []; }
async function cargarDatosPrecios() { const d = await fetchJSON('api/precios.php'); window._precios = d && typeof d === 'object' ? d : { consulta: 30, limpieza: 20, obturacion: 50, corona: 200 }; }
async function cargarDatosConfig() { const d = await fetchJSON('api/config.php'); window._config = d && typeof d === 'object' ? d : defaultConfig; }

async function cargarDatosIniciales() {
    try {
        await Promise.all([cargarDatosPacientes(), cargarDatosCitas(), cargarDatosBoletas(), cargarDatosPrecios(), cargarDatosConfig()]);
    } catch (e) { console.warn('Error cargando datos iniciales', e); }
    // render inicial usando los datos cargados
    try { renderizarListaPacientes(); } catch(e){}
    try { actualizarDashboard(); } catch(e){}
    try { renderizarCalendario(); } catch(e){}
    try { popularSelectPacientes(); } catch(e){}
    try { renderReportes(); } catch(e){}
    try { renderConfiguracion(); } catch(e){}
    try { renderFacturacion(); } catch(e){}
    // ensure facturacion patient list is refreshed once data loaded
    try { if (window.updateFacturacionPatients) window.updateFacturacionPatients(''); } catch(e){}
}

// Unifica pacientes duplicados: prioriza DNI cuando exista, si no usa nombre+apellido+fechaNacimiento
function dedupePacientes() {
    const list = obtenerPacientes();
    const mapByDni = new Map();
    const mapByKey = new Map();
    const result = [];
    list.forEach(p => {
        const dni = (p.dni || '').toString().trim();
        const key = ((p.nombre || '') + '|' + (p.apellido || '') + '|' + (p.fechaNacimiento || '')).toLowerCase();
        if (dni) {
            if (mapByDni.has(dni)) {
                // merge fields: prefer existing non-empty, else take from current
                const existing = mapByDni.get(dni);
                Object.keys(p).forEach(k => { if ((!existing[k] || existing[k] === '') && p[k]) existing[k] = p[k]; });
            } else {
                mapByDni.set(dni, Object.assign({}, p));
            }
        } else if (mapByKey.has(key)) {
            const existing = mapByKey.get(key);
            Object.keys(p).forEach(k => { if ((!existing[k] || existing[k] === '') && p[k]) existing[k] = p[k]; });
        } else {
            mapByKey.set(key, Object.assign({}, p));
        }
    });
    // combine maps preserving insertion
    mapByDni.forEach(v => result.push(v));
    mapByKey.forEach(v => result.push(v));
    // ensure unique ids: if missing id, assign
    result.forEach(r => { if (!r.id) r.id = Date.now() + Math.floor(Math.random() * 1000); });
    guardarPacientes(result);
    return result;
}

function renderizarListaPacientes(filtro = '') {
    const lista = document.getElementById('lista-pacientes');
    const q = filtro.trim().toLowerCase();
    const pacientes = obtenerPacientes().filter(p => {
        if (!q) return true;
        return (p.nombre + ' ' + p.apellido).toLowerCase().includes(q) || (p.dni || '').toLowerCase().includes(q);
    });
    lista.innerHTML = pacientes.map(p => `
        <li data-id="${p.id}">
            <div class="lp-info">${p.nombre} ${p.apellido} ${p.dni? '<span class="lp-dni">- ' + p.dni + '</span>' : ''} <span class="lp-age">(${p.edad} años)</span></div>
            <div class="lp-actions">
                <button class="icon-btn btn-edit" data-id="${p.id}" title="Editar paciente">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/></svg>
                </button>
                <button class="icon-btn btn-delete" data-id="${p.id}" title="Eliminar paciente">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M6 19a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z"/></svg>
                </button>
            </div>
        </li>`).join('');
}
// Ensure patients are deduped on startup
dedupePacientes();
// render KPIs iniciales
renderKpis();
document.getElementById('busqueda-paciente').addEventListener('input', e => {
    renderizarListaPacientes(e.target.value);
});
document.getElementById('lista-pacientes').addEventListener('click', e => {
    // detectar botones por classList en botones o SVGs internos
    const btnDel = e.target.closest('.btn-delete');
    const btnEdit = e.target.closest('.btn-edit');
    if (btnDel) {
        const id = btnDel.dataset.id;
        const paciente = obtenerPacientes().find(p => p.id == id);
    const citasRelacionadas = obtenerCitas().filter(c => String(c.pacienteId) == String(id));
    const boletas = obtenerBoletas().filter(b => String(b.pacienteId) == String(id));
        const hasTreatment = paciente && ((paciente.tratamiento && paciente.tratamiento.trim() !== '') || (paciente.evolucion && paciente.evolucion.trim() !== '') || !!paciente.historia);

        // Si no tiene citas, boletas ni historial de tratamiento, eliminar directamente con confirmación simple
        if (citasRelacionadas.length === 0 && boletas.length === 0 && !hasTreatment) {
            showConfirm('¿Confirmar eliminación del paciente? Esta acción irreversible.', 'Eliminar paciente').then(async (confirmed) => {
                if (!confirmed) return;
                // eliminar solo paciente
                // eliminar paciente via API
                await fetchJSON(`/api/pacientes.php?id=${encodeURIComponent(id)}`, { method: 'DELETE' });
                await cargarDatosPacientes();
                renderizarListaPacientes();
                actualizarDashboard();
                popularSelectPacientes();
                showToast('Paciente eliminado.', 'success');
            });
            return;
        }

        // Si tiene datos relacionados, informar y ofrecer forzar eliminación
        let mensaje = 'No se puede eliminar el paciente de forma segura porque tiene:';
        if (citasRelacionadas.length) mensaje += `\n- ${citasRelacionadas.length} cita(s) asociada(s)`;
        if (boletas.length) mensaje += `\n- ${boletas.length} boleta(s)/factura(s) asociada(s)`;
        if (hasTreatment) mensaje += `\n- historial o plan de tratamiento registrado`;
        mensaje += '\n\nSi deseas eliminar igualmente al paciente y todos los registros relacionados, pulsa Aceptar para forzar la eliminación. De lo contrario, pulsa Cancelar.';

    showConfirm(mensaje, 'Eliminar paciente y registros').then(async (confirmed) => {
            if (!confirmed) return;
            // Forzar eliminación: paciente + citas + boletas
            // Forzar eliminación: paciente + citas + boletas
            await fetchJSON(`/api/pacientes.php?id=${encodeURIComponent(id)}`, { method: 'DELETE' });
            // eliminar citas relacionados
            const citas = obtenerCitas().filter(c => String(c.pacienteId) == String(id));
            for (const c of citas) await fetchJSON(`/api/citas.php?id=${encodeURIComponent(c.id)}`, { method: 'DELETE' });
            // eliminar boletas relacionadas
            const bs = obtenerBoletas().filter(b => String(b.pacienteId) == String(id));
            for (const b of bs) await fetchJSON(`/api/boletas.php?id=${encodeURIComponent(b.id)}`, { method: 'DELETE' });
            await Promise.all([cargarDatosPacientes(), cargarDatosCitas(), cargarDatosBoletas()]);
            renderizarListaPacientes();
            actualizarDashboard();
            renderizarCalendario();
            popularSelectPacientes();
            showToast('Paciente y registros relacionados eliminados.', 'success');
        });
        return;
    }
    if (btnEdit) {
        const id = btnEdit.dataset.id;
        const paciente = obtenerPacientes().find(p => p.id == id);
        if (paciente) cargarHistoriaPaciente(paciente);
        document.querySelector('.sidebar nav a[href="#historias"]').click();
        return;
    }
    // Si se hizo click fuera de botones, tratar como selección del li
    const li = e.target.closest('li[data-id]');
    if (li) {
        const id = li.getAttribute('data-id');
        const paciente = obtenerPacientes().find(p => p.id == id);
        if (paciente) cargarHistoriaPaciente(paciente);
        document.querySelector('.sidebar nav a[href="#historias"]').click();
    }
});

// --- Formulario Historia Clínica ---
function calcularEdad(fechaNacimiento) {
    const hoy = new Date();
    const nacimiento = new Date(fechaNacimiento);
    let edad = hoy.getFullYear() - nacimiento.getFullYear();
    const m = hoy.getMonth() - nacimiento.getMonth();
    if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
        edad--;
    }
    return edad;
}
document.getElementById('fecha-nacimiento').addEventListener('change', e => {
    document.getElementById('edad').value = calcularEdad(e.target.value);
});

function limpiarFormularioHistoria() {
    document.getElementById('form-historia').reset();
    document.getElementById('edad').value = '';
    odontograma.marcarDientes([]);
}

function cargarHistoriaPaciente(paciente) {
    document.getElementById('nombre').value = paciente.nombre;
    document.getElementById('apellido').value = paciente.apellido;
    document.getElementById('fecha-nacimiento').value = paciente.fechaNacimiento;
    document.getElementById('edad').value = paciente.edad;
    document.getElementById('sexo').value = paciente.sexo;
    document.getElementById('telefono').value = paciente.telefono;
    document.getElementById('dni').value = paciente.dni || '';
    document.getElementById('email').value = paciente.email;
    document.getElementById('antecedentes').value = paciente.antecedentes || '';
    document.getElementById('extraoral').value = paciente.extraoral || '';
    document.getElementById('intraoral').value = paciente.intraoral || '';
    document.getElementById('tratamiento').value = paciente.tratamiento || '';
    document.getElementById('evolucion').value = paciente.evolucion || '';
    odontograma.marcarDientes(paciente.odontograma || []);
}
document.getElementById('form-historia').addEventListener('submit', function(e) {
    e.preventDefault();
    // Validación básica
    const nombre = document.getElementById('nombre').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const fechaNacimiento = document.getElementById('fecha-nacimiento').value;
    const edad = document.getElementById('edad').value;
    if (!nombre || !apellido || !fechaNacimiento || !edad) {
        showToast('Complete los datos personales obligatorios.', 'error');
        return;
    }
    // Guardar paciente
    // crear/actualizar paciente via API
    (async () => {
        const pacientePayload = {
            nombre, apellido, fechaNacimiento, edad,
            sexo: document.getElementById('sexo').value,
            telefono: document.getElementById('telefono').value,
            dni: document.getElementById('dni').value.trim(),
            email: document.getElementById('email').value,
            antecedentes: document.getElementById('antecedentes').value,
            extraoral: document.getElementById('extraoral').value,
            intraoral: document.getElementById('intraoral').value,
            tratamiento: document.getElementById('tratamiento').value,
            evolucion: document.getElementById('evolucion').value,
            odontograma: odontograma.getDientesMarcados(),
            historia: true
        };
        try {
            await fetchJSON('api/pacientes.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(pacientePayload) });
            await cargarDatosPacientes();
            showToast('Historia clínica guardada correctamente.', 'success');
            limpiarFormularioHistoria();
            renderizarListaPacientes();
            actualizarDashboard();
        } catch (e) { console.error(e); showToast('Error al guardar paciente', 'error'); }
    })();
    // eliminar duplicados y obtener lista limpia
    dedupePacientes();
    showToast('Historia clínica guardada correctamente.', 'success');
    limpiarFormularioHistoria();
    renderizarListaPacientes();
    actualizarDashboard();
});

// --- Odontograma Interactivo ---
const odontograma = {
    dientes: Array.from({ length: 32 }, (_, i) => i + 1),
    // almacenará un mapa {diente: condicion}
    marcados: {},
    init() {
        const cont = document.getElementById('odontograma');
        cont.innerHTML = '';
        this.dientes.forEach(num => {
            const d = document.createElement('div');
            d.className = 'odontograma-diente';
            d.textContent = num;
            d.dataset.num = num;
            // Click behavior:
            // - If a condition is selected in the dropdown, apply/toggle that condition on click
            // - If the dropdown is 'Ninguno' (or empty), clicking will *inspect* the tooth and populate the selector
            d.addEventListener('click', (e) => {
                const sel = document.getElementById('odontograma-condicion');
                const selVal = sel ? (sel.value || '').toString().trim() : '';
                const current = this.marcados[num];
                if (!selVal || selVal.toLowerCase() === 'ninguno') {
                    // populate selector with the tooth's condition (if any) for inspection
                    if (sel) sel.value = current || '';
                    // small visual cue for selection
                    d.classList.add('selected-temporary');
                    setTimeout(() => d.classList.remove('selected-temporary'), 600);
                } else {
                    // apply/toggle the selected condition
                    this.toggleDiente(num);
                }
            });
            cont.appendChild(d);
        });
        // cambio de condición
        document.getElementById('odontograma-condicion').addEventListener('change', () => this.render());
    },
    toggleDiente(num) {
        const condicion = document.getElementById('odontograma-condicion').value;
        if (!condicion) {
            // si no hay condicion seleccionada, alternar marcado simple
            if (this.marcados[num]) delete this.marcados[num];
            else this.marcados[num] = 'marcado';
        } else {
            // asignar condicion específica
            if (this.marcados[num] === condicion) delete this.marcados[num];
            else this.marcados[num] = condicion;
        }
        this.render();
    },
    render() {
        document.querySelectorAll('.odontograma-diente').forEach(d => {
            const num = d.dataset.num;
            const val = this.marcados[num];
            d.classList.toggle('marcado', !!val);
            // aplicar condicion como atributo para estilos
            if (val && val !== 'marcado') d.setAttribute('data-condicion', val);
            else d.removeAttribute('data-condicion');
            // mostrar condición como tooltip/title para facilitar inspección
            d.title = val ? (val === 'marcado' ? 'Marcado' : String(val)) : '';
        });
    },
    // acepta tanto formato antiguo (array de números) como nuevo (objeto)
    marcarDientes(arrOrObj) {
        if (!arrOrObj) { this.marcados = {};
            this.render(); return; }
        if (Array.isArray(arrOrObj)) {
            // compatibilidad: array de numeros -> marcado simple
            this.marcados = {};
            arrOrObj.forEach(n => this.marcados[n] = 'marcado');
        } else {
            this.marcados = Object.assign({}, arrOrObj);
        }
        this.render();
    },
    getDientesMarcados() {
        return this.marcados;
    }
};
odontograma.init();

// --- Agenda (Calendario básico) ---
function obtenerCitasHoy() {
    const citas = obtenerCitas();
    const hoy = new Date().toISOString().slice(0, 10);
    return citas.filter(c => c.fecha === hoy);
}

function renderizarCalendario() {
    const cont = document.getElementById('calendario');
    const allCitas = obtenerCitas().slice().sort((a, b) => (a.fecha + ' ' + a.hora).localeCompare(b.fecha + ' ' + b.hora));
    const hoy = new Date().toISOString().slice(0, 10);
    const citasHoy = allCitas.filter(c => c.fecha === hoy);
    // próximas 14 días
    const proximos = allCitas.filter(c => {
        const d = new Date(c.fecha);
        const diff = (d - new Date(hoy + 'T00:00:00')) / (1000 * 60 * 60 * 24);
        return diff > 0 && diff <= 14;
    });
    cont.innerHTML = `<h3>Citas</h3><div class="small-note">Total citas: ${allCitas.length} — Hoy: ${citasHoy.length} — Próximas 14 días: ${proximos.length}</div>`;

    // Citas de hoy
    cont.innerHTML += '<h4>Citas de Hoy</h4>';
    if (citasHoy.length === 0) cont.innerHTML += '<p>No hay citas para hoy.</p>';
    else cont.innerHTML += '<ul>' + citasHoy.map(c => `<li data-id="${c.id}">${c.hora} - ${c.paciente} <button class="btn-eliminar-cita" data-id="${c.id}">Eliminar</button></li>`).join('') + '</ul>';

    // Próximas citas
    cont.innerHTML += '<h4>Próximas (14 días)</h4>';
    if (proximos.length === 0) cont.innerHTML += '<p>No hay citas próximas en los próximos 14 días.</p>';
    else cont.innerHTML += '<ul>' + proximos.map(c => `<li data-id="${c.id}">${c.fecha} ${c.hora} - ${c.paciente} <button class="btn-eliminar-cita" data-id="${c.id}">Eliminar</button></li>`).join('') + '</ul>';

    // Todas las citas (breve)
    cont.innerHTML += '<h4>Todas las Citas</h4>';
    if (allCitas.length === 0) cont.innerHTML += '<p>No hay citas registradas.</p>';
    else cont.innerHTML += '<ul>' + allCitas.map(c => `<li data-id="${c.id}">${c.fecha} ${c.hora} - ${c.paciente} <button class="btn-eliminar-cita" data-id="${c.id}">Eliminar</button></li>`).join('') + '</ul>';
}

// --- Manejo de citas: persistencia, formulario y borrado ---
async function crearOActualizarCita(cita) {
    return fetchJSON('api/citas.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(cita) });
}

// guardarCitas en lote: reemplazado por crearOActualizarCita/guardarCitas
function guardarCitas() { /* legacy placeholder */ }

// Rellenar select de pacientes en el formulario de cita
function popularSelectPacientes() {
    const select = document.getElementById('cita-paciente');
    if (!select) return;
    const pacientes = obtenerPacientes();
    select.innerHTML = '<option value="">-- Seleccione --</option>' + pacientes.map(p => `<option value="${p.id}">${p.nombre} ${p.apellido} ${p.dni? '- ' + p.dni : ''}</option>`).join('');
}

// Filtrar select de pacientes por nombre o DNI
function filtrarSelectPacientes(filtro) {
    const select = document.getElementById('cita-paciente');
    if (!select) return;
    const pacientes = obtenerPacientes().filter(p => {
        const q = filtro.trim().toLowerCase();
        if (!q) return true;
        return (p.nombre + ' ' + p.apellido).toLowerCase().includes(q) || (p.dni || '').toLowerCase().includes(q);
    });
    select.innerHTML = '<option value="">-- Seleccione --</option>' + pacientes.map(p => `<option value="${p.id}">${p.nombre} ${p.apellido} ${p.dni? '- ' + p.dni : ''}</option>`).join('');
}

// Formulario de cita
const formCita = document.getElementById('form-cita');
let editingCitaId = null;
formCita.addEventListener('submit', e => {
    e.preventDefault();
    const pacienteId = document.getElementById('cita-paciente').value;
    const fecha = document.getElementById('cita-fecha').value;
    const hora = document.getElementById('cita-hora').value;
    const notas = document.getElementById('cita-notas').value;
    if (!pacienteId || !fecha || !hora) { showToast('Complete paciente, fecha y hora', 'error'); return; }
    const pacientes = obtenerPacientes();
    const paciente = pacientes.find(p => p.id == pacienteId);
    if (!paciente) { showToast('Paciente no encontrado. Verifique que haya seleccionado un paciente válido.', 'error'); return; }
    let citas = obtenerCitas();
    if (editingCitaId) {
        const cita = citas.find(c => c.id == editingCitaId);
        if (cita) {
            cita.paciente = `${paciente.nombre} ${paciente.apellido}`;
            cita.pacienteId = pacienteId;
            cita.fecha = fecha;
            cita.hora = hora;
            cita.notas = notas;
        }
        editingCitaId = null;
    } else {
        citas.push({ id: Date.now(), paciente: `${paciente.nombre} ${paciente.apellido}`, pacienteId, fecha, hora, notas });
    }
    guardarCitas(citas);
    // Confirmación al usuario
    showToast('Cita guardada correctamente.', 'success');
    formCita.reset();
    // Re-renderizar calendario y selects
    renderizarCalendario();
    actualizarDashboard();
    renderKpis();
    popularSelectPacientes();
});
document.getElementById('btn-cancelar-cita').addEventListener('click', () => { formCita.reset();
    editingCitaId = null; });

// Delegación de eventos para eliminar citas
document.getElementById('calendario').addEventListener('click', e => {
    // Si se clickeó el botón eliminar
    if (e.target.classList.contains('btn-eliminar-cita')) {
        const id = Number(e.target.dataset.id);
        let citas = obtenerCitas();
        citas = citas.filter(c => c.id !== id);
        guardarCitas(citas);
        renderizarCalendario();
        actualizarDashboard();
        return;
    }
    // Si se clickeó sobre una línea de cita (editar)
    const li = e.target.closest('li[data-id]');
    if (li) {
        const id = Number(li.dataset.id);
        const cita = obtenerCitas().find(c => c.id === id);
        if (!cita) return;
        // Cargar en el formulario para edición
        editingCitaId = cita.id;
        document.getElementById('cita-paciente').value = cita.pacienteId || '';
        document.getElementById('cita-fecha').value = cita.fecha || '';
        document.getElementById('cita-hora').value = cita.hora || '';
        document.getElementById('cita-notas').value = cita.notas || '';
        // asegurar que el select de pacientes tenga el paciente seleccionado
        popularSelectPacientes();
        // cambiar vista al formulario (opcional enfocar)
        const formulario = document.getElementById('form-cita');
        formulario.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Conectar búsqueda en el formulario de cita (agenda)
    const buscarCitaInput = document.getElementById('buscar-cita');
if (buscarCitaInput) {
    buscarCitaInput.addEventListener('input', e => {
        filtrarSelectPacientes(e.target.value);
    });
}

// --- Reportes básicos ---
function generarReporteResumen() {
    const pacientes = obtenerPacientes();
    const citas = obtenerCitas();
    return {
        totalPacientes: pacientes.length,
        totalCitas: citas.length,
        citasHoy: obtenerCitasHoy().length,
        conHistoria: pacientes.filter(p => p.historia).length
    };
}

function renderReportes() {
    const cont = document.getElementById('reportes');
    const r = generarReporteResumen();
    cont.innerHTML = `
        <h2>Reportes</h2>
        <div class="report-grid">
            <div class="report-card">Total Pacientes: <strong>${r.totalPacientes}</strong></div>
            <div class="report-card">Total Citas: <strong>${r.totalCitas}</strong></div>
            <div class="report-card">Citas Hoy: <strong>${r.citasHoy}</strong></div>
            <div class="report-card">Pacientes con Historia: <strong>${r.conHistoria}</strong></div>
        </div>
        <div style="margin-top:12px;">
            <button id="export-csv">Exportar Pacientes (CSV)</button>
        </div>`;
    document.getElementById('export-csv').addEventListener('click', () => {
        exportPacientesCSV();
    });
    // Limpia nodos de texto residuales que contengan solo '+' (protección contra inserciones accidentales)
    removePlusTextNodes(cont);
}

function exportPacientesCSV() {
    const pacientes = obtenerPacientes();
    if (pacientes.length === 0) { showToast('No hay pacientes para exportar.', 'info'); return; }
    const headers = ['id', 'nombre', 'apellido', 'fechaNacimiento', 'edad', 'telefono', 'email'];
    const rows = pacientes.map(p => headers.map(h => JSON.stringify(p[h] || '')).join(','));
    const csv = [headers.join(','), ...rows].join('\n');
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'pacientes.csv';
    a.click();
    URL.revokeObjectURL(url);
}

// --- Configuración ---
const defaultConfig = { nombreClinica: 'Clínica Dental', mostrarNotificaciones: true };

// obtenerConfig: devuelve la configuración en memoria si está cargada, si no retorna defaultConfig
function obtenerConfig() { return window._config || defaultConfig; }

// guardarConfig ya está definida arriba como async (usa la API). Nos aseguramos que exista.
// async function guardarConfig(cfg) { await fetchJSON('api/config.php', { method: 'POST', headers: {'Content-Type':'application/json'}, body: JSON.stringify(cfg) }); window._config = cfg; }

function renderConfiguracion() {
    const cont = document.getElementById('configuracion');
    const cfg = obtenerConfig();
    cont.innerHTML = `
        <h2>Configuración</h2>
        <div class="config-grid">
            <div class="report-card">
                <label>Nombre de la Clínica: <input id="cfg-nombre" value="${cfg.nombreClinica}"></label>
                <label><input type="checkbox" id="cfg-notif" ${cfg.mostrarNotificaciones? 'checked':''}> Mostrar notificaciones</label>
                <div style="margin-top:8px;"><button id="cfg-guardar">Guardar Configuración</button></div>
            </div>
            <div class="report-card">
                <h4>Acciones</h4>
                <button id="btn-borrar-datos">Borrar Datos (Pacientes y Citas)</button>
            </div>
        </div>`;
    document.getElementById('cfg-guardar').addEventListener('click', async () => {
        const nuevo = { nombreClinica: document.getElementById('cfg-nombre').value, mostrarNotificaciones: document.getElementById('cfg-notif').checked };
        try { await guardarConfig(nuevo); showToast('Configuración guardada.', 'success'); } catch(e){ showToast('Error guardando configuración', 'error'); }
    });
    document.getElementById('btn-borrar-datos').addEventListener('click', async () => {
        showConfirm('¿Eliminar todos los pacientes y citas? Esta acción no se puede deshacer.', 'Borrar datos').then(async (ok) => {
            if (!ok) return;
            // eliminar todos los pacientes (y cascada manualmente)
            const pacs = obtenerPacientes().slice();
            for (const p of pacs) {
                await fetchJSON(`/api/pacientes.php?id=${encodeURIComponent(p.id)}`, { method: 'DELETE' });
            }
            // eliminar todas las citas
            const citas = obtenerCitas().slice();
            for (const c of citas) {
                await fetchJSON(`/api/citas.php?id=${encodeURIComponent(c.id)}`, { method: 'DELETE' });
            }
            await Promise.all([cargarDatosPacientes(), cargarDatosCitas(), cargarDatosBoletas()]);
            renderizarListaPacientes();
            renderizarCalendario();
            actualizarDashboard();
            popularSelectPacientes();
            showToast('Datos borrados.', 'success');
        });
    });
    // Limpia '+' residuales
    removePlusTextNodes(cont);
}

// --- Inicialización ---
// Inicialización
renderizarListaPacientes();
actualizarDashboard();
renderizarCalendario();
popularSelectPacientes();
renderReportes();
renderConfiguracion();
filtrarSelectPacientes('');

// Re-renderizar cuando cambien datos (por ejemplo tras guardar pacientes)
window.addEventListener('storage', () => {
    renderizarListaPacientes();
    actualizarDashboard();
    renderizarCalendario();
    popularSelectPacientes();
    renderReportes();
    renderConfiguracion();
});

// Autenticación: removida del UI. La lógica de login se ha eliminado para permitir acceso directo.

// --- Facturación: precios y boletas ---
// obtenerPrecios() y guardarPrecios() usan la API y ya están definidas arriba (cargarDatosPrecios / guardarPrecios)

// Añadir sección Facturación en DOM y lógica
function renderFacturacion() {
    const main = document.getElementById('facturacion');
    if (!main) return;
    const precios = obtenerPrecios();
    // Plantilla HTML limpia para facturación
    main.innerHTML = `
        <h2>Facturación</h2>
        <div class="fact-grid">
            <div class="invoice">
                <h3>Crear Boleta</h3>
                <label>Buscar paciente (nombre o DNI): <input id="fact-buscar" placeholder="Escriba nombre o DNI" style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid var(--color-border)"></label>
                <div style="display:flex;gap:12px;align-items:flex-start">
                    <div style="flex:1">
                        <label>Paciente: <select id="fact-paciente">${obtenerPacientes().map(p=>`<option value="${p.id}">${p.nombre} ${p.apellido}${p.dni? ' - ' + p.dni : ''}</option>`).join('')}</select></label>
                    </div>
                    <div style="width:260px;max-height:220px;overflow:auto;border:1px solid var(--color-border);border-radius:8px;padding:8px;background:#fff;">
                        <strong style="display:block;margin-bottom:8px">Pacientes</strong>
                        <ul id="fact-patient-list" style="list-style:none;padding:0;margin:0"></ul>
                    </div>
                </div>
                <div id="line-items">
                    <div class="line-item">
                        <select class="li-tipo">
                            <option value="consulta">Consulta</option>
                            <option value="limpieza">Limpieza</option>
                            <option value="obturacion">Obturación</option>
                            <option value="corona">Corona</option>
                        </select>
                        <input type="number" class="li-cant" value="1" min="1">
                        <input type="number" class="li-precio" value="${precios.consulta}">
                        <button class="li-remove">X</button>
                    </div>
                </div>
                <button id="add-line">Agregar servicio</button>
                <div style="margin-top:8px;">
                    <button id="generar-boleta">Generar Boleta</button>
                </div>
            </div>
            <div class="invoice-preview" id="invoice-preview">
                <h3>Previsualización</h3>
                <div id="preview-content">Seleccione servicios para ver el total.</div>
            </div>
        </div>`;

    // eventos
    document.getElementById('add-line').addEventListener('click', ()=>{
        const cont = document.getElementById('line-items');
        const div = document.createElement('div'); div.className='line-item';
        div.innerHTML = `
            <select class="li-tipo">
                <option value="consulta">Consulta</option>
                <option value="limpieza">Limpieza</option>
                <option value="obturacion">Obturación</option>
                <option value="corona">Corona</option>
            </select>
            <input type="number" class="li-cant" value="1" min="1">
            <input type="number" class="li-precio" value="${precios.consulta}">
            <button class="li-remove">X</button>
        `;
        cont.appendChild(div);
        actualizarPreviewFactura();
    });
    document.getElementById('line-items').addEventListener('click', e=>{
        if(e.target.classList.contains('li-remove')) { e.target.parentElement.remove(); actualizarPreviewFactura(); }
    });
    document.getElementById('line-items').addEventListener('input', actualizarPreviewFactura);
    document.getElementById('generar-boleta').addEventListener('click', generarBoleta);
    actualizarPreviewFactura();

    // Pacientes: render list and wiring for search by nombre/DNI
    function refreshFacturacionPatients(filter) {
        const patients = obtenerPacientes().slice();
        const sel = document.getElementById('fact-paciente');
        const ul = document.getElementById('fact-patient-list');
        if (!sel || !ul) return;
        const q = (filter || '').toString().trim().toLowerCase();
        // repopulate select with filtered results
        sel.innerHTML = '<option value="">-- Seleccione --</option>' + patients.filter(p => {
            if (!q) return true;
            return (p.nombre + ' ' + p.apellido).toLowerCase().includes(q) || (p.dni || '').toLowerCase().includes(q);
        }).map(p => `<option value="${p.id}">${p.nombre} ${p.apellido}${p.dni? ' - ' + p.dni : ''}</option>`).join('');

        // populate simple clickable list
        const listItems = patients.filter(p => {
            if (!q) return true;
            return (p.nombre + ' ' + p.apellido).toLowerCase().includes(q) || (p.dni || '').toLowerCase().includes(q);
        }).slice(0, 200);
        ul.innerHTML = listItems.length === 0 ? '<li style="padding:6px 0;color:#666">No hay pacientes</li>' : listItems.map(p => `<li data-id="${p.id}" style="padding:6px 6px;border-bottom:1px solid #f3f3f3;cursor:pointer"><strong>${p.nombre} ${p.apellido}</strong><div style="font-size:0.85rem;color:#666">${p.dni? 'DNI: '+p.dni : ''}</div></li>`).join('');

        // attach click handlers
        Array.from(ul.querySelectorAll('li[data-id]')).forEach(li => {
            li.addEventListener('click', () => {
                const id = li.getAttribute('data-id');
                sel.value = id;
                sel.dispatchEvent(new Event('change'));
            });
        });
    }

    const busc = document.getElementById('fact-buscar');
    if (busc) {
        busc.addEventListener('input', (e) => { refreshFacturacionPatients(e.target.value); });
    }
    // initial render
    // expose globally so other parts can request a refresh
    try { window.updateFacturacionPatients = refreshFacturacionPatients; } catch(e) {}
    refreshFacturacionPatients('');
}

function actualizarPreviewFactura(){
    const rows = Array.from(document.querySelectorAll('#line-items .line-item'));
    const items = rows.map(r=>({tipo: r.querySelector('.li-tipo').value, cant: Number(r.querySelector('.li-cant').value), precio: Number(r.querySelector('.li-precio').value)}));
    const total = items.reduce((s,i)=>s + i.cant * i.precio, 0);
    const cont = document.getElementById('preview-content');
    if(!cont) return;
    cont.innerHTML = items.length===0? '<p>No hay items.</p>' : `<ul>${items.map(i=>`<li>${i.cant} x ${i.tipo} - $${(i.precio*i.cant).toFixed(2)}</li>`).join('')}</ul><h4>Total: $${total.toFixed(2)}</h4>`;
}

async function generarBoleta(){
    const pacienteId = (document.getElementById('fact-paciente') && document.getElementById('fact-paciente').value) || '';
    if (!pacienteId) { showToast('Seleccione un paciente', 'error'); return; }
    const paciente = obtenerPacientes().find(p => String(p.id) === String(pacienteId));
    if (!paciente) { showToast('Paciente seleccionado no encontrado. Actualice la lista y vuelva a intentar.', 'error'); return; }
    const rows = Array.from(document.querySelectorAll('#line-items .line-item'));
    const items = rows.map(r => ({ tipo: r.querySelector('.li-tipo').value, cant: Number(r.querySelector('.li-cant').value) || 0, precio: Number(r.querySelector('.li-precio').value) || 0 }));
    const total = items.reduce((s, i) => s + i.cant * i.precio, 0);
    const boletaPayload = { pacienteId, paciente: `${paciente.nombre} ${paciente.apellido}`, items, total, fecha: new Date().toISOString() };
    try {
        const res = await fetchJSON('api/boletas.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(boletaPayload) });
        // if API failed or returned invalid response, fallback to local creation
        let boleta = null;
        if (!res || typeof res !== 'object') {
            // fallback: create locally and push into in-memory store
            boleta = Object.assign({ id: Date.now() }, boletaPayload);
            window._boletas = (window._boletas || []).concat([boleta]);
            // try to persist again in background (fire-and-forget)
            (async () => {
                try { await fetchJSON('api/boletas.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(boletaPayload) }); } catch (e) { /* ignore */ }
            })();
            await cargarDatosBoletas();
            showToast('Boleta creada localmente (servidor no respondió).', 'warning');
        } else {
            // if server returned an id, try to fetch the created boleta; otherwise build it
            await cargarDatosBoletas();
            const created = res.id ? await fetchJSON(`/api/boletas.php?id=${encodeURIComponent(res.id)}`) : null;
            boleta = created || Object.assign({ id: res.id || Date.now() }, boletaPayload);
        }

        // Asegurar que items sea un array (algunos endpoints almacenan JSON como string)
        function parseItems(items) {
            if (!items) return [];
            if (Array.isArray(items)) return items;
            if (typeof items === 'string') {
                try { return JSON.parse(items); } catch (e) { return []; }
            }
            return [];
        }
        const itemsArr = parseItems(boleta.items);
        const totalDisplay = (typeof boleta.total === 'number') ? boleta.total : itemsArr.reduce((s,i)=>s + (Number(i.cant)||0)*(Number(i.precio)||0), 0);

        // impresión segura: comprobar popup
        try {
            const popup = window.open('', '_blank');
            const listHtml = `<ul>${itemsArr.map(i=>`<li>${i.cant} x ${i.tipo} - $${(Number(i.precio)*Number(i.cant)).toFixed(2)}</li>`).join('')}</ul>`;
            const html = `<html><head><title>Boleta ${boleta.id}</title></head><body><h2>Boleta</h2><p>Paciente: ${boleta.paciente}</p><p>Fecha: ${new Date(boleta.fecha).toLocaleString()}</p>${listHtml}<h3>Total: $${Number(totalDisplay).toFixed(2)}</h3></body></html>`;
            if (popup) {
                popup.document.write(html);
                popup.print();
                popup.close();
            } else {
                const win = window.open('about:blank', '_blank');
                if (win) { win.document.write(html); win.print(); win.close(); }
            }
        } catch (e) { console.warn('Impresión fallida o bloqueada por popup blocker', e); }

        showToast('Boleta generada.', 'success');
        await Promise.all([cargarDatosBoletas(), cargarDatosPacientes(), cargarDatosCitas()]);
        actualizarDashboard();
        renderKpis();
    } catch (e) { console.error(e); showToast('Error generando boleta', 'error'); }
}

// Mostrar facturacion al inicializar
renderFacturacion();
removePlusTextNodes(document.getElementById('facturacion'));

// Actualizar UI dinámicamente cuando se modifiquen pacientes o precios
function refreshAllViews(){
    renderizarListaPacientes();
    actualizarDashboard();
    renderizarCalendario();
    popularSelectPacientes();
    renderReportes();
    renderConfiguracion();
    renderFacturacion();
    removePlusTextNodes(document.getElementById('facturacion'));
}

// Observador simple en submit de historia para refrescar facturacion
document.getElementById('form-historia').addEventListener('submit', ()=>{
    setTimeout(refreshAllViews,200);
});

// Comentarios explicativos en el código para cada sección importante
// - Navegación: cambia la sección visible y resalta el menú
// - Dashboard: muestra estadísticas rápidas
// - Pacientes: lista y búsqueda, clic para editar historia
// - Historia clínica: formulario con validación y odontograma interactivo
// - Odontograma: permite marcar dientes y guarda el estado
// - Agenda: muestra citas del día
// - Almacenamiento: simulado con localStorage

// Elimina nodos de texto que contienen únicamente signos '+' (puede aparecer por plantillas mal formateadas)
function removePlusTextNodes(root) {
    try {
        const walker = document.createTreeWalker(root || document.body, NodeFilter.SHOW_TEXT, null, false);
        const toRemove = [];
        while(walker.nextNode()) {
            const txt = walker.currentNode.nodeValue.trim();
            if(txt && /^[+]+$/.test(txt)) toRemove.push(walker.currentNode);
        }
        toRemove.forEach(n => n.parentNode && n.parentNode.removeChild(n));
    } catch(e) { /* silencioso */ }
}

// Cargar datos desde la API al iniciar la app
document.addEventListener('DOMContentLoaded', () => {
    cargarDatosIniciales();
});