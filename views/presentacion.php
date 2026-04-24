<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Paths de las imágenes generadas por IA en el directorio de artefactos
$img_base = 'https://antigravity.google/artifacts/5e0c7d69-9292-44b4-96a7-00cacbc51439/';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sustentación ULTRA-PRO â€” SISFARMA PRO | ESE FJL</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/base.css">
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({
            startOnLoad: true,
            theme: 'dark',
            securityLevel: 'loose',
            themeVariables: {
                primaryColor: '#00796b',
                primaryTextColor: '#fff',
                primaryBorderColor: '#00796b',
                lineColor: '#00796b',
                secondaryColor: '#ffffff',
                tertiaryColor: '#f5f5f5'
            }
        });
    </script>
    <style>
        * { box-sizing: border-box; }
        body { overflow: hidden; background: #020c1b; font-family: 'Inter', sans-serif; }

        .pres-wrap {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            position: relative;
            background: #020c1b;
        }

        /* FONDO INSTITUCIONAL COMO ELEMENTO PRINCIPAL SUTIL */
        .pres-wrap::after {
            content: '';
            position: absolute;
            inset: 0;
            background: url('../img/fondo.jpg') no-repeat center center;
            background-size: cover;
            opacity: 0.18; /* Aumentado para que sea visible */
            pointer-events: none;
            z-index: 0;
            filter: grayscale(40%) contrast(110%);
        }

        .slides-track {
            display: flex;
            height: 100vh;
            transition: transform 0.9s cubic-bezier(0.77, 0, 0.175, 1);
            will-change: transform;
            position: relative;
            z-index: 1;
        }

        .slide {
            min-width: 100vw;
            height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 60px 80px;
            position: relative;
        }

        .slide::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 20% 50%, rgba(255,255,255,0.05) 0%, transparent 70%);
            pointer-events: none;
        }

        /* CONTENT LEFT */
        .slide-content { padding-right: 60px; }
        .slide-tag { font-size: 0.75rem; font-weight: 700; color: #4db6ac; letter-spacing: 4px; text-transform: uppercase; margin-bottom: 15px; }
        .slide-title { font-size: 3.8rem; font-weight: 900; line-height: 0.95; color: white; margin-bottom: 20px; }
        .slide-title span { color: #4db6ac; }
        .slide-sub { font-size: 1.15rem; color: var(--text-dim); line-height: 1.6; margin-bottom: 30px; }

        .feat-list { list-style: none; display: flex; flex-direction: column; gap: 12px; }
        .feat-list li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 1rem;
            color: var(--text-main);
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 15px;
        }
        .feat-list li .icon { color: #4db6ac; font-size: 1.2rem; margin-top: 2px; flex-shrink: 0; }

        /* VISUAL RIGHT */
        .slide-visual {
            background: var(--primary-light);
            border-radius: 32px;
            height: 82%;
            border: 1px solid var(--border);
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slide-visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.85;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: auto;
        }

        .visual-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 40%, rgba(2,12,27,0.9) 100%);
        }

        .visual-label {
            position: absolute;
            bottom: 20px;
            left: 20px;
            right: 20px;
            color: white;
            font-size: 0.85rem;
            background: rgba(2,12,27,0.7);
            backdrop-filter: blur(10px);
            padding: 10px 15px;
            border-radius: 10px;
            border: 1px solid var(--border);
        }

        /* Semáforo visual slide */
        .sema-bars { display: flex; flex-direction: column; gap: 15px; padding: 30px; width: 100%; }
        .sema-row { display: flex; align-items: center; gap: 15px; font-size: 0.9rem; color: var(--text-main); }
        .sema-dot { width: 18px; height: 18px; border-radius: 50%; flex-shrink: 0; box-shadow: 0 0 10px currentColor; }
        .sema-bar { height: 10px; border-radius: 5px; flex: 1; }
        .c-red { color: #ff5252; background: #ff5252; }
        .c-yellow { color: #ffc107; background: #ffc107; }
        .c-green { color: var(--secondary); background: var(--secondary); }

        /* Arquitectura visual */
        .arch-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 25px;
            width: 100%;
        }
        .arch-node {
            background: rgba(100,255,218,0.06);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .arch-icon { font-size: 1.5rem; }
        .arch-label { font-size: 0.85rem; }
        .arch-label strong { color: var(--secondary); display: block; }
        .arch-label span { color: var(--text-dim); font-size: 0.75rem; }

        /* Controls */
        .nav-bar {
            position: fixed;
            bottom: 35px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(2,12,27,0.8);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 50px;
            padding: 10px 20px;
            z-index: 100;
        }
        .nav-btn {
            background: transparent;
            border: 1px solid #4db6ac;
            color: #4db6ac;
            padding: 8px 20px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 0.8rem;
            font-weight: 700;
            transition: 0.2s;
        }
        .nav-btn:hover { background: #4db6ac; color: #020c1b; }
        .slide-counter { color: var(--text-dim); font-size: 0.85rem; padding: 0 10px; min-width: 60px; text-align: center; }

        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            background: #00796b;
            transition: width 0.5s;
            z-index: 200;
            box-shadow: 0 0 15px rgba(0, 121, 107, 0.4);
        }

        .exit-btn {
            position: fixed;
            top: 20px;
            left: 25px;
            color: var(--text-dim);
            text-decoration: none;
            font-size: 0.8rem;
            z-index: 100;
            background: rgba(2,12,27,0.8);
            border: 1px solid var(--border);
            padding: 8px 15px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .slide-number {
            position: fixed;
            top: 20px;
            right: 25px;
            color: var(--secondary);
            font-size: 2rem;
            font-weight: 900;
            opacity: 0.15;
            z-index: 100;
            font-family: monospace;
        }

        /* BIG STAT */
        .big-stat { font-size: 5rem; font-weight: 900; color: #4db6ac; line-height: 1; }
        .stat-row { display: flex; gap: 30px; margin-top: 20px; }
        .stat-item { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 20px; flex: 1; text-align: center; }
        .stat-num { font-size: 2rem; font-weight: 800; color: #4db6ac; }
        .stat-lbl { font-size: 0.7rem; color: var(--text-dim); text-transform: uppercase; }

        /* Roadmap */
        .roadmap { display: flex; flex-direction: column; gap: 8px; padding: 15px; width: 100%; }
        .road-item {
            display: flex;
            gap: 15px;
            align-items: center;
            padding: 12px;
            background: rgba(255,255,255,0.03);
            border-radius: 10px;
            border-left: 4px solid;
        }
        .road-item.done { border-color: #4db6ac; }
        .road-item.active-road { border-color: #f57f17; }
        .road-item.pending { border-color: #172a45; opacity: 0.5; }
        .road-badge { font-size: 0.65rem; font-weight: 700; padding: 3px 8px; border-radius: 4px; }
        .badge-done { background: rgba(77,182,172,0.2); color: #4db6ac; }
        .badge-active { background: rgba(245,127,23,0.2); color: var(--accent); }
        .badge-pend { background: rgba(255,255,255,0.05); color: var(--text-dim); }

        .mermaid { background: rgba(10,25,47,0.5); border-radius: 20px; padding: 20px; border: 1px solid var(--border); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
        .bg-slide-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; pointer-events: none; image-rendering: -webkit-optimize-contrast; }
    </style>
</head>
<body>
<div class="progress-bar" id="progressBar" style="width:7.14%"></div>
<a href="inicio.php" class="exit-btn">â† SALIR AL PANEL</a>
<div class="slide-number" id="slideNum">01</div>

<div class="pres-wrap">
    <div class="slides-track" id="track">

        <!-- SLIDE 1: PORTADA -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">ESE Fabio Jaramillo Londoño Â· Proyecto Institucional Â· 2026</div>
                <h1 class="slide-title">SISFARMA<br><span>PRO</span></h1>
                <p class="slide-sub">Gestión integral de inventarios y despacho de medicamentos para la Red IPS de la ESE Fabio Jaramillo Londoño.</p>
                <div class="stat-row" style="margin-top:30px;">
                    <div class="stat-item"><div class="stat-num">5</div><div class="stat-lbl">Sedes Municipales</div></div>
                    <div class="stat-item"><div class="stat-num">100%</div><div class="stat-lbl">Control de Lotes</div></div>
                    <div class="stat-item"><div class="stat-num">SIAU</div><div class="stat-lbl">Integrado</div></div>
                </div>
            </div>
            <div class="slide-visual">
                <img src="../img/logoesefjl.jpg" alt="ESE FJL Logo" style="object-fit: contain; padding: 40px; background: white; border-radius: 20px;">
                <div class="visual-overlay"></div>
                <div class="visual-label">Sede Administrativa â€” Florencia, Caquetá</div>
            </div>
        </div>

        <!-- SLIDE 2: DIAGRAMA DE FLUJO -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">Operación</div>
                <h1 class="slide-title">Operatividad del<br><span>Servicio</span></h1>
                <p class="slide-sub">Flujo de procesos desde la recepción técnica en CEDIS hasta la entrega definitiva al usuario final.</p>
                <ul class="feat-list">
                    <li><span class="icon">ðŸ“‹</span><strong>Ingreso:</strong> Control de facturación y lotes.</li>
                    <li><span class="icon">ðŸš›</span><strong>Traslado:</strong> Despacho interno a municipios.</li>
                    <li><span class="icon">ðŸ‘¤</span><strong>Suministro:</strong> Entrega directa al paciente.</li>
                </ul>
            </div>
            <div class="slide-visual">
                <div class="mermaid">
                graph TD
                    A[CEDIS: Recepción Técnica] --> B{Solicitud Pedido}
                    B -->|Aprobado| C[Traslado a Sede]
                    C --> D[Sede: Ingreso Local]
                    D --> E[Suministro a Paciente]
                    E --> F[Registro de Auditoría]
                    style A fill:#64ffda11,stroke:#64ffda
                    style B fill:#64ffda11,stroke:#64ffda
                    style E fill:#64ffda11,stroke:#64ffda
                </div>
            </div>
        </div>

        <!-- SLIDE 3: DIAGRAMA DE PROCESO -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">Red Hospitalaria</div>
                <h1 class="slide-title">Distribución<br><span>Municipal</span></h1>
                <p class="slide-sub">Integración del nodo central administrativo con las sedes operativas en los municipios del departamento.</p>
                <div class="stat-row">
                    <div class="stat-item"><div class="stat-num">5</div><div class="stat-lbl">Municipios</div></div>
                    <div class="stat-item"><div class="stat-num">Central</div><div class="stat-lbl">CEDIS</div></div>
                </div>
            </div>
            <div class="slide-visual">
                <div class="mermaid">
                graph LR
                    subgraph Centro_Distribucion
                    C[CEDIS Florencia]
                    end
                    subgraph Red_Municipios
                    I1[IPS Solita]
                    I2[IPS Solano]
                    I3[IPS Milan]
                    I4[IPS Getucha]
                    I5[IPS Valparaíso]
                    end
                    C --> I1
                    C --> I2
                    C --> I3
                    C --> I4
                    C --> I5
                    style C fill:#64ffda11,stroke:#64ffda
                </div>
            </div>
        </div>

        <!-- SLIDE 4: CASOS DE USO -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">Roles y Funciones</div>
                <h1 class="slide-title">Casos de<br><span>Uso</span></h1>
                <p class="slide-sub">Definición de responsabilidades y acciones por cada perfil de usuario dentro del ecosistema SISFARMA.</p>
                <ul class="feat-list">
                    <li><strong>Admin:</strong> Auditoría y gestión global.</li>
                    <li><strong>Regente:</strong> Control técnico e inventario.</li>
                    <li><strong>Jefe IPS:</strong> Dispensación y pedidos mensuales.</li>
                </ul>
            </div>
            <div class="slide-visual">
                <div class="mermaid">
                graph LR
                    A((Administrador)) --> U1[Gestionar Usuarios]
                    A --> U2[Configurar Sedes]
                    R((Regente)) --> U3[Aprobar Pedidos]
                    R --> U4[Ingresar Lotes]
                    J((Jefe IPS)) --> U5[Solicitar Insumos]
                    J --> U6[Entregar a Paciente]
                    style A fill:#64ffda11,stroke:#64ffda
                    style R fill:#64ffda11,stroke:#64ffda
                    style J fill:#64ffda11,stroke:#64ffda
                </div>
            </div>
        </div>

        <!-- SLIDE 5: EL PROBLEMA -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">01 â€” Diagnóstico Actual</div>
                <h1 class="slide-title">Limitaciones del<br><span>Sistema Manual</span></h1>
                <p class="slide-sub">El manejo artesanal de inventarios genera riesgos críticos para la prestación del servicio de salud.</p>
                <ul class="feat-list">
                    <li><span class="icon">âš ï¸</span><div><strong>Pérdida por Vencimiento</strong><br><small style="color:var(--text-dim)">Lotes vencidos por falta de alertas proactivas.</small></div></li>
                    <li><span class="icon">ðŸ’°</span><div><strong>Riesgo Sancionatorio</strong><br><small style="color:var(--text-dim)">Incongruencias en reportes para la red de vigilancia.</small></div></li>
                    <li><span class="icon">ðŸ“‹</span><div><strong>Vacíos de Información</strong><br><small style="color:var(--text-dim)">Dificultad para rastrear el destino final de cada insumo.</small></div></li>
                </ul>
            </div>
            <div class="slide-visual">
                <img src="../img/GrupoTrabajoSP.jpg" alt="Equipo de Trabajo">
                <div class="visual-overlay"></div>
                <div class="visual-label">Equipo de Trabajo â€” ESE Fabio Jaramillo Londoño</div>
            </div>
        </div>

        <!-- SLIDE 6: LA SOLUCIÃ“N -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">02 â€” Sisfarma PRO</div>
                <h1 class="slide-title">Solución<br><span>Estandarizada</span></h1>
                <p class="slide-sub">Herramienta digital diseñada para garantizar la trazabilidad total y el cumplimiento normativo institucional.</p>
                <ul class="feat-list">
                    <li><span class="icon">ðŸš¨</span><div><strong>Alertas de Vigencia</strong><br><small style="color:var(--text-dim)">Notificaciones automáticas según semaforización técnica.</small></div></li>
                    <li><span class="icon">ðŸ“²</span><div><strong>Soporte Digital</strong><br><small style="color:var(--text-dim)">Registro inmediato de cada movimiento en bodega.</small></div></li>
                    <li><span class="icon">ðŸ“Š</span><div><strong>Generación de Reportes</strong><br><small style="color:var(--text-dim)">Consolidados listos para auditorías internas y externas.</small></div></li>
                </ul>
            </div>
            <div class="slide-visual">
                <img src="../img/Gerente.jpg" alt="Gerencia">
                <div class="visual-overlay"></div>
                <div class="visual-label">Gerencia Institucional â€” Liderando el Proyecto</div>
            </div>
        </div>

        <!-- SLIDE 7: ARQUITECTURA -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">Diseño de Red</div>
                <h1 class="slide-title">Infraestructura<br><span>CEDIS â€” IPS</span></h1>
                <p class="slide-sub">Modelo de distribución centralizada para optimizar recursos y garantizar el abastecimiento regional.</p>
                <ul class="feat-list">
                    <li><span class="icon">ðŸ›ï¸</span><div><strong>Punto Central (Florencia)</strong><br><small style="color:var(--text-dim)">Coordinación de compras y control de stock nacional.</small></div></li>
                    <li><span class="icon">ðŸ¥</span><div><strong>Sedes Municipales</strong><br><small style="color:var(--text-dim)">Suministro local y reporte de necesidades en tiempo real.</small></div></li>
                    <li><span class="icon">âš¡</span><div><strong>Sincronización Asíncrona</strong><br><small style="color:var(--text-dim)">Funcionamiento garantizado incluso en zonas de baja conectividad.</small></div></li>
                </ul>
            </div>
            <div class="slide-visual">
                <img src="../img/fondo.jpg" alt="Fondo Institucional" class="bg-slide-img" style="opacity: 0.3;">
                <div class="arch-grid" style="position: relative; z-index: 1;">
                    <div class="arch-node" style="border-color: var(--secondary);">
                        <div class="arch-icon">ðŸ›ï¸</div>
                        <div class="arch-label"><strong>CEDIS â€” Florencia</strong><span>Nodo Administrativo Principal</span></div>
                    </div>
                    <div style="text-align:center; color: var(--secondary); font-size: 1.5rem;">â†•</div>
                    <?php
                    $ips = ['Solita', 'Solano', 'Milán', 'Getuchá', 'Valparaíso'];
                    foreach ($ips as $i): ?>
                    <div class="arch-node">
                        <div class="arch-icon">ðŸ¥</div>
                        <div class="arch-label"><strong>IPS <?= $i ?></strong><span>Nodo Municipal</span></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- SLIDE 8: STACK -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">04 â€” Especificación Técnica</div>
                <h1 class="slide-title">Estructura<br><span>Tecnológica</span></h1>
                <p class="slide-sub">Selección de herramientas para garantizar estabilidad, portabilidad y bajo mantenimiento en el entorno hospitalario.</p>
                <ul class="feat-list">
                    <li><span class="icon">âš™ï¸</span><div><strong>Lenguaje: PHP 8.x</strong><br><small style="color:var(--text-dim)">Lógica de servidor robusta y escalable.</small></div></li>
                    <li><span class="icon">ðŸ—ƒï¸</span><div><strong>Motor: SQLite</strong><br><small style="color:var(--text-dim)">Base de datos portable que no requiere servidor externo.</small></div></li>
                    <li><span class="icon">ðŸŽ¨</span><div><strong>Interfaz: CSS3 + JS</strong><br><small style="color:var(--text-dim)">Diseño limpio sin librerías externas pesadas.</small></div></li>
                </ul>
            </div>
            <div class="slide-visual">
                <div style="padding: 30px; width: 100%; text-align: center;">
                    <div style="font-size:5rem; margin-bottom:20px;">ðŸ’»</div>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: left;">
                        <?php foreach(['PHP 8.x' => 'âš™ï¸', 'SQLite' => 'ðŸ—ƒï¸', 'CSS Pro' => 'ðŸŽ¨', 'WAMP' => 'ðŸ–¥ï¸'] as $tech => $icon): ?>
                        <div style="background: rgba(100,255,218,0.06); border: 1px solid var(--border); border-radius: 10px; padding: 12px;">
                            <div><?= $icon ?></div>
                            <div style="color: var(--secondary); font-weight: 700; font-size: 0.9rem;"><?= $tech ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLIDE 9: RIESGOS -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">05 â€” Continuidad</div>
                <h1 class="slide-title">Garantía de<br><span>Operación</span></h1>
                <p class="slide-sub">Protocolos técnicos diseñados para mantener el servicio ante fallas de conectividad externa.</p>
                <ul class="feat-list">
                    <li><span class="icon">ðŸ“¶</span><div><strong>Persistencia Local</strong><br><small style="color:var(--text-dim)">Capacidad de registro de datos sin dependencia de internet.</small></div></li>
                    <li><span class="icon">ðŸ”„</span><div><strong>Sincronización</strong><br><small style="color:var(--text-dim)">Los registros pendientes se actualizan automáticamente al restaurar servicio.</small></div></li>
                    <li><span class="icon">ðŸ“</span><div><strong>Registro de Eventos</strong><br><small style="color:var(--text-dim)">Auditoría completa de movimientos en cualquier condición de red.</small></div></li>
                </ul>
            </div>
            <div class="slide-visual">
                <div style="padding: 30px; text-align: center; width: 100%;">
                    <div style="font-size:5rem; margin-bottom:20px;">âš¡</div>
                    <div style="background: rgba(255,82,82,0.1); border: 1px solid #ff5252; border-radius: 12px; padding: 15px; margin-bottom: 10px; color:#ff5252; font-weight:700;">âš ï¸ API FALLA</div>
                    <div style="color: var(--text-dim); font-size: 1.5rem;">â†“</div>
                    <div style="background: rgba(100,255,218,0.1); border: 1px solid var(--secondary); border-radius: 12px; padding: 15px; color:var(--secondary); font-weight:700;">âœ… MODO LOCAL ACTIVO</div>
                    <div style="color: var(--text-dim); font-size: 0.8rem; margin-top:10px;">El paciente recibe su medicamento. Sin interrupciones.</div>
                </div>
            </div>
        </div>

        <!-- SLIDE 10: ROLES -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">06 â€” Gestión de Accesos</div>
                <h1 class="slide-title">Control de<br><span>Perfiles</span></h1>
                <p class="slide-sub">Accesos restringidos por nivel de responsabilidad y ubicación geográfica para garantizar seguridad.</p>
                <ul class="feat-list">
                    <li><span class="icon">ðŸ”‘</span><div><strong>Coordinación Central</strong><br><small style="color:var(--text-dim)">Supervisión total y configuración de parámetros.</small></div></li>
                    <li><span class="icon">ðŸ‘©â€âš•ï¸</span><div><strong>Servicios Farmacéuticos IPS</strong><br><small style="color:var(--text-dim)">Gestión de stock municipal y dispensación.</small></div></li>
                    <li><span class="icon">ðŸ’Š</span><div><strong>Auditoría Técnica</strong><br><small style="color:var(--text-dim)">Validación de movimientos y existencias reales.</small></div></li>
                </ul>
            </div>
            <div class="slide-visual">
                <div style="padding: 25px; width: 100%;">
                    <?php
                    $users = [
                        ['ðŸ›ï¸', 'ADMINISTRADOR', 'Florencia â€” CEDIS', '#64ffda'],
                        ['ðŸ‘©â€âš•ï¸', 'JEFE ENFERMERÃA', 'Solita IPS', '#8892b0'],
                        ['ðŸ‘©â€âš•ï¸', 'JEFE ENFERMERÃA', 'Solano IPS', '#8892b0'],
                        ['ðŸ‘©â€âš•ï¸', 'JEFE ENFERMERÃA', 'Milán IPS', '#8892b0'],
                    ];
                    foreach ($users as $u): ?>
                    <div style="display:flex; align-items:center; gap:12px; padding:10px; border-bottom: 1px solid var(--border);">
                        <span style="font-size:1.5rem;"><?= $u[0] ?></span>
                        <div>
                            <div style="color:<?= $u[3] ?>; font-size:0.8rem; font-weight:700;"><?= $u[1] ?></div>
                            <div style="color:var(--text-dim); font-size:0.7rem;"><?= $u[2] ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div style="text-align:center; color:var(--text-dim); font-size:0.8rem; margin-top:10px;">+ 2 sedes más...</div>
                </div>
            </div>
        </div>

        <!-- SLIDE 11: ROADMAP -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">07 â€” Cronograma de Trabajo</div>
                <h1 class="slide-title">Etapas de<br><span>Implementación</span></h1>
                <p class="slide-sub">Ruta técnica para la puesta en marcha definitiva del sistema en toda la red hospitalaria.</p>
                <ul class="feat-list">
                    <li><span class="icon">âœ…</span><div><strong>Fase 1:</strong> Análisis de requisitos y entorno local</div></li>
                    <li><span class="icon">âœ…</span><div><strong>Fase 2:</strong> Desarrollo de módulos de inventario</div></li>
                    <li><span class="icon">ðŸ”„</span><div><strong>Fase 3:</strong> Despliegue en red IPS y alertas</div></li>
                    <li><span class="icon">â³</span><div><strong>Fase 4:</strong> Pruebas finales y entrega técnica</div></li>
                </ul>
            </div>
            <div class="slide-visual">
                <div class="roadmap" style="position: relative; z-index: 1;">
                    <div class="road-item done"><span style="font-size:1.2rem;">âœ…</span><div><div style="color:var(--secondary); font-size:0.85rem; font-weight:700;">Semana 1</div><div style="color:var(--text-dim); font-size:0.75rem;">Taller y Stack Tecnológico</div></div><span class="road-badge badge-done">LISTO</span></div>
                    <div class="road-item done"><span style="font-size:1.2rem;">âœ…</span><div><div style="color:var(--secondary); font-size:0.85rem; font-weight:700;">Semana 2</div><div style="color:var(--text-dim); font-size:0.75rem;">Inventario y Distribución Core</div></div><span class="road-badge badge-done">LISTO</span></div>
                    <div class="road-item active-road"><span style="font-size:1.2rem;">ðŸ”„</span><div><div style="color:var(--accent); font-size:0.85rem; font-weight:700;">Semana 3</div><div style="color:var(--text-dim); font-size:0.75rem;">Red IPS + Alertas de Salud</div></div><span class="road-badge badge-active">EN CURSO</span></div>
                    <div class="road-item pending"><span style="font-size:1.2rem;">â³</span><div><div style="font-size:0.85rem; font-weight:700;">Semana 4</div><div style="color:var(--text-dim); font-size:0.75rem;">Auditoría y Despliegue Final</div></div><span class="road-badge badge-pend">PRÃ“XIMO</span></div>
                </div>
            </div>
        </div>

        <!-- SLIDE 11: TABLAS DB -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">09 â€” Estructura de Datos</div>
                <h1 class="slide-title">Base de Datos<br><span>SQLite</span></h1>
                <p class="slide-sub">Modelo persistente y ligero optimizado para la red de salud.</p>
                <ul class="feat-list">
                    <li><strong>productos:</strong> Catálogo maestro de medicamentos.</li>
                    <li><strong>inventario:</strong> Stock por sede y lotes.</li>
                    <li><strong>sedes:</strong> Florencia y las 5 IPS Municipales.</li>
                    <li><strong>usuarios:</strong> Control de acceso por roles.</li>
                </ul>
            </div>
            <div class="slide-visual">
                <div style="padding: 20px; font-size: 0.75rem; color: var(--text-dim); overflow-y: auto; max-height: 80%;">
                    <pre style="background: rgba(0,0,0,0.3); padding: 15px; border-radius: 10px; border: 1px solid var(--border);">
CREATE TABLE productos (
    id PRIMARY KEY,
    nombre, concentracion, forma...
);

CREATE TABLE inventario (
    id PRIMARY KEY,
    producto_id, sede_id, cantidad,
    lote, fecha_vencimiento...
);

CREATE TABLE sedes (
    id PRIMARY KEY,
    nombre, ciudad...
);
                    </pre>
                </div>
            </div>
        </div>

        <!-- SLIDE 12: RELACIONES DB -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">10 â€” Relaciones</div>
                <h1 class="slide-title">Modelo de<br><span>Relaciones</span></h1>
                <p class="slide-sub">Integridad referencial total entre CEDIS, IPS, Inventario y Entregas.</p>
                <div class="stat-item" style="margin-top:20px;">
                    <div class="stat-num">âˆž</div>
                    <div class="stat-lbl">Relaciones Escalables</div>
                </div>
            </div>
            <div class="slide-visual">
                <div class="mermaid">
                erDiagram
                    SEDES ||--o{ INVENTARIO : contiene
                    PRODUCTOS ||--o{ INVENTARIO : registra
                    SEDES ||--o{ USUARIOS : asigna
                    INVENTARIO ||--o{ ENTREGAS : despacha
                    PACIENTES ||--o{ ENTREGAS : recibe
                </div>
            </div>
        </div>

        <!-- SLIDE 14: CONCLUSIÃ“N -->
        <div class="slide">
            <div class="slide-content">
                <div class="slide-tag">Cierre</div>
                <h1 class="slide-title">Compromiso<br><span>Institucional</span></h1>
                <p class="slide-sub">SISFARMA PRO es una solución robusta enfocada en la eficiencia operativa y el bienestar del ciudadano.</p>
                <div class="stat-row" style="margin-top: 30px;">
                    <div class="stat-item"><div class="stat-num">5</div><div class="stat-lbl">Sedes</div></div>
                    <div class="stat-item"><div class="stat-num">7</div><div class="stat-lbl">Perfiles</div></div>
                </div>
            </div>
            <div class="slide-visual">
                <div style="text-align:center; padding: 30px;">
                    <div style="font-size:6rem;">âœ…</div>
                    <h2 style="color:var(--secondary); font-size:1.5rem; margin: 20px 0;">SISTEMA OPERATIVO</h2>
                    <p style="color:var(--text-dim); font-size:0.9rem; line-height:1.6;">Software adaptable, resiliente y diseñado para fortalecer la prestación del servicio farmacéutico en la ESE.</p>
                    <div style="margin-top:20px; border-top: 1px solid var(--border); padding-top: 15px; color: var(--text-dim); font-size:0.75rem;">
                        ESE Fabio Jaramillo Londoño Â· Gestión 2026
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end track -->
</div>

<!-- NAV BAR -->
<div class="nav-bar">
    <button class="nav-btn" id="prevBtn" onclick="navTo(-1)">â—€ ANTERIOR</button>
    <div class="slide-counter" id="counter">1 / 14</div>
    <button class="nav-btn" id="nextBtn" onclick="navTo(1)">SIGUIENTE â–¶</button>
</div>

<script>
let cur = 0;
const total = 14;

function navTo(dir) {
    cur = Math.max(0, Math.min(total - 1, cur + dir));
    update();
}

function goTo(idx) {
    cur = Math.max(0, Math.min(total - 1, idx));
    update();
}

function update() {
    document.getElementById('track').style.transform = `translateX(-${cur * 100}vw)`;
    document.getElementById('progressBar').style.width = ((cur + 1) / total * 100) + '%';
    document.getElementById('counter').innerText = `${cur + 1} / ${total}`;
    document.getElementById('slideNum').innerText = String(cur + 1).padStart(2, '0');
    document.getElementById('prevBtn').disabled = cur === 0;
    document.getElementById('nextBtn').innerText = cur === total - 1 ? 'ðŸ FINALIZAR' : 'SIGUIENTE â–¶';
    if(cur === total - 1 && document.getElementById('nextBtn').dataset.fin === '1') {
        window.location.href = 'inicio.php';
    }
    if(cur === total - 1) { document.getElementById('nextBtn').dataset.fin = '1'; }
    else { document.getElementById('nextBtn').dataset.fin = '0'; }
}

window.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowRight' || e.key === ' ') navTo(1);
    if (e.key === 'ArrowLeft') navTo(-1);
});

update();
</script>
</body>
</html>
