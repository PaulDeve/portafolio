<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OdHCL - Sistema de Historias Clínicas Dentales</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script defer src="assets/app.js"></script>
</head>

<body>
    <div class="container">
        <!-- Sidebar de navegación -->
        <aside class="sidebar">
            <div class="logo">OdHCL</div>
            <nav>
                <ul>
                    <li><a href="#dashboard" class="active">Dashboard</a></li>
                    <li><a href="#pacientes">Pacientes</a></li>
                    <li><a href="#historias">Historias Clínicas</a></li>
                    <li><a href="#agenda">Agenda</a></li>
                    <li><a href="#reportes">Reportes</a></li>
                    <li><a href="#facturacion">Facturación</a></li>
                    <li><a href="#configuracion">Configuración</a></li>
                </ul>
            </nav>
        </aside>
        <!-- Sección principal -->
        <main class="main-content">
            <!-- Header -->
            <header class="main-header">
                <div class="user-info">
                    <span id="user-name">Administrador</span>
                    <div class="notif-wrapper">
                        <button id="notif-button" class="notif-button" aria-haspopup="true" aria-expanded="false" title="Notificaciones">🔔 <span id="notif-count" class="notif-count">0</span></button>
                        <div id="notif-dropdown" class="notif-dropdown" style="display:none" aria-label="Notificaciones"></div>
                    </div>
                </div>
            </header>
            <!-- Dashboard -->
            <section id="dashboard" class="dashboard section">
                <h2>Dashboard</h2>

                <!-- KPI quick stats -->
                <div class="dashboard-grid">
                    <div class="dash-card stat-card">
                        <div class="dc-top">
                            <div class="dc-title">Total Pacientes</div>
                            <div class="dc-value" id="total-pacientes">0</div>
                        </div>
                        <small>Pacientes registrados en el sistema</small>
                    </div>

                    <div class="dash-card stat-card">
                        <div class="dc-top">
                            <div class="dc-title">Citas (Hoy)</div>
                            <div class="dc-value" id="citas-dia">0</div>
                        </div>
                        <small>Citas programadas para hoy</small>
                    </div>

                    <div class="dash-card stat-card">
                        <div class="dc-top">
                            <div class="dc-title">Pacientes con Historia</div>
                            <div class="dc-value" id="total-historias">0</div>
                        </div>
                        <small>Porcentaje en los donuts</small>
                    </div>

                    <div class="dash-card stat-card">
                        <div class="dc-top">
                            <div class="dc-title">Ingresos (Total)</div>
                            <div class="dc-value" id="stat-ingresos">$0.00</div>
                        </div>
                        <small>Sumatoria de boletas</small>
                    </div>

                    <div class="dash-card stat-card">
                        <div class="dc-top">
                            <div class="dc-title">Edad Promedio</div>
                            <div class="dc-value" id="stat-edad-prom">0</div>
                        </div>
                        <small>Edad promedio de pacientes</small>
                    </div>

                    <div class="dash-card stat-card">
                        <div class="dc-top">
                            <div class="dc-title">Pacientes sin Citas</div>
                            <div class="dc-value" id="stat-sin-citas">0</div>
                        </div>
                        <small>Pacientes sin citas programadas</small>
                    </div>
                </div>

                <!-- Gráficos de barras: edad, citas próximas y ingresos -->
                <div class="charts-grid" style="margin-top:12px;display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;">
                    <div class="dash-card">
                        <div class="dc-title">Distribución por Edad</div>
                        <small class="small-note">Gráfico de barras por rango etario</small>
                        <div id="chart-age" class="bar-chart" style="margin-top:12px;"></div>
                    </div>
                    <div class="dash-card">
                        <div class="dc-title">Citas Próximas (7 días)</div>
                        <small class="small-note">Número de citas por día</small>
                        <div id="chart-citas" class="bar-chart" style="margin-top:12px;"></div>
                    </div>
                    <div class="dash-card">
                        <div class="dc-title">Ingresos (últimos 6 meses)</div>
                        <small class="small-note">Total facturado por mes</small>
                        <div id="chart-ingresos" class="bar-chart" style="margin-top:12px;"></div>
                    </div>
                </div>

                <!-- Listas y tablas rápidas -->
                <div class="dashboard-grid" style="margin-top:18px;">
                    <div class="dash-card">
                        <div class="dc-title">Pacientes Recientes</div>
                        <small class="small-note">Últimos pacientes agregados</small>
                        <ul id="recent-patients" style="margin-top:10px;list-style:none;padding:0;max-height:220px;overflow:auto"></ul>
                    </div>
                    <div class="dash-card">
                        <div class="dc-title">Boletas Recientes</div>
                        <small class="small-note">Últimas boletas generadas</small>
                        <ul id="recent-boletas" style="margin-top:10px;list-style:none;padding:0;max-height:220px;overflow:auto"></ul>
                    </div>
                    <div class="dash-card">
                        <div class="dc-title">Próximas Citas</div>
                        <small class="small-note">Agenda (7 días)</small>
                        <ul id="upcoming-citas" style="margin-top:10px;list-style:none;padding:0;max-height:220px;overflow:auto"></ul>
                    </div>
                </div>
            </section>
            <!-- Pacientes -->
            <section id="pacientes" class="section" style="display:none;">
                <h2>Pacientes</h2>
                <input type="text" id="busqueda-paciente" placeholder="Buscar paciente...">
                <ul id="lista-pacientes"></ul>
            </section>
            <!-- Historias Clínicas -->
            <section id="historias" class="section" style="display:none;">
                <h2>Historia Clínica Dental</h2>
                <form id="form-historia">
                    <fieldset>
                        <legend>Datos Personales</legend>
                        <label>Nombre: <input type="text" id="nombre" required></label>
                        <label>Apellido: <input type="text" id="apellido" required></label>
                        <label>Fecha de Nacimiento: <input type="date" id="fecha-nacimiento" required></label>
                        <label>Edad: <input type="number" id="edad" readonly></label>
                        <label>Sexo:
                            <select id="sexo" required>
                                <option value="">Seleccione</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </label>
                        <label>Teléfono: <input type="tel" id="telefono"></label>
                        <label>DNI: <input type="text" id="dni" placeholder="Documento identificación"></label>
                        <label>Email: <input type="email" id="email"></label>
                    </fieldset>
                    <fieldset>
                        <legend>Antecedentes Médicos</legend>
                        <textarea id="antecedentes" rows="3" placeholder="Describa antecedentes médicos..."></textarea>
                    </fieldset>
                    <fieldset>
                        <legend>Examen Extraoral</legend>
                        <textarea id="extraoral" rows="2" placeholder="Observaciones extraorales..."></textarea>
                    </fieldset>
                    <fieldset>
                        <legend>Examen Intraoral</legend>
                        <textarea id="intraoral" rows="2" placeholder="Observaciones intraorales..."></textarea>
                    </fieldset>
                    <fieldset>
                        <legend>Odontograma Interactivo</legend>
                        <div id="odontograma-toolbar">
                            <label>Condición:
                                <select id="odontograma-condicion">
                                    <option value="">Ninguno</option>
                                    <option value="Caries">Caries</option>
                                    <option value="Obturacion">Obturación</option>
                                    <option value="Corona">Corona</option>
                                    <option value="Extraccion">Extracción</option>
                                    <option value="Fractura">Fractura</option>
                                    <option value="Implante">Implante</option>
                                </select>
                            </label>
                            <div class="odontograma-legend">
                                <span class="legend-item"><span class="dot caries"></span>Caries</span>
                                <span class="legend-item"><span class="dot obt"></span>Obturación</span>
                                <span class="legend-item"><span class="dot corona"></span>Corona</span>
                            </div>
                        </div>
                        <div id="odontograma"></div>
                    </fieldset>
                    <fieldset>
                        <legend>Plan de Tratamiento</legend>
                        <textarea id="tratamiento" rows="2" placeholder="Plan de tratamiento propuesto..."></textarea>
                    </fieldset>
                    <fieldset>
                        <legend>Evolución y Notas</legend>
                        <textarea id="evolucion" rows="2" placeholder="Notas de evolución..."></textarea>
                    </fieldset>
                    <button type="submit">Guardar Historia Clínica</button>
                </form>
            </section>
            <!-- Agenda -->
            <section id="agenda" class="section" style="display:none;">
                <h2>Agenda de Citas</h2>
                <div class="agenda-grid">
                    <div id="calendario"></div>
                    <div class="agenda-form">
                        <h3>Agregar / Editar Cita</h3>
                        <form id="form-cita">
                            <label>Buscar paciente (nombre o DNI): <input id="buscar-cita" placeholder="Nombre o DNI"></label>
                            <label>Paciente:
                                    <select id="cita-paciente"></select>
                                </label>
                            <label>Fecha: <input type="date" id="cita-fecha" required></label>
                            <label>Hora: <input type="time" id="cita-hora" required></label>
                            <label>Notas: <input type="text" id="cita-notas"></label>
                            <div style="display:flex;gap:8px;margin-top:8px;">
                                <button type="submit">Guardar Cita</button>
                                <button type="button" id="btn-cancelar-cita">Limpiar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
            <!-- Facturación -->
            <section id="facturacion" class="section" style="display:none;">
                <h2>Facturación</h2>
                <div class="fact-grid">
                    <div class="invoice">
                        <h3>Crear Boleta</h3>
                        <label>Buscar paciente (nombre o DNI): <input id="fact-buscar" placeholder="Escriba nombre o DNI" style="width:100%;padding:8px;margin:6px 0;border-radius:6px;border:1px solid var(--color-border)"></label>
                        <div style="display:flex;gap:12px;align-items:flex-start">
                            <div style="flex:1">
                                <label>Paciente: <select id="fact-paciente"><option value="">-- Seleccione --</option></select></label>
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
                                <input type="number" class="li-precio" value="30">
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
                </div>
            </section>
            <!-- Reportes -->
            <section id="reportes" class="section" style="display:none;">
                <h2>Reportes</h2>
                <p>Próximamente...</p>
            </section>
            <!-- Configuración -->
            <section id="configuracion" class="section" style="display:none;">
                <h2>Configuración</h2>
                <p>Opciones de usuario y sistema.</p>
            </section>
        </main>
    </div>
</body>

</html>
