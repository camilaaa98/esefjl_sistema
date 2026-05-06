<?php
/**
 * Sustentación Institucional - ESE Fabio Jaramillo Londoño
 * Temática: Caquetá - Puerta de Oro de la Amazonía
 */
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sustentación Institucional — FARMACIA ESEFJL</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/mermaid/dist/mermaid.min.js"></script>
    <script>
        mermaid.initialize({
            startOnLoad: true,
            theme: 'dark',
            securityLevel: 'loose',
            themeVariables: {
                primaryColor: '#22c55e',
                primaryTextColor: '#fff',
                primaryBorderColor: '#d4af37',
                lineColor: '#22c55e',
                secondaryColor: '#ffffff',
                tertiaryColor: '#0a1a0f'
            }
        });
    </script>
    <style>
        * { margin:0; padding:0; box-sizing: border-box; }
        body { overflow: hidden; background: var(--amazon-dark); font-family: 'Inter', sans-serif; color: #fff; }

        .pres-wrap {
            width: 100vw;
            height: 100vh;
            overflow: hidden;
            position: relative;
            background: var(--bg-amazon);
        }

        /* Capas de fondo amazónico */
        .amazon-overlay {
            position: absolute;
            inset: 0;
            z-index: 0;
            opacity: 0.15;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 80 80'%3E%3Cpath d='M40 10 C20 25, 15 50, 40 70 C65 50, 60 25, 40 10Z' fill='%2322c55e' /%3E%3C/svg%3E");
            background-size: 150px;
            pointer-events: none;
        }

        .mist-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40%;
            background: linear-gradient(to top, rgba(10,26,15,0.8), transparent);
            z-index: 1;
            pointer-events: none;
        }

        .slides-track {
            display: flex;
            height: 100vh;
            transition: transform 1s cubic-bezier(0.85, 0, 0.15, 1);
            will-change: transform;
            position: relative;
            z-index: 2;
        }

        .slide {
            min-width: 100vw;
            height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 4rem 6rem;
            position: relative;
        }

        /* Contenido de Slide */
        .slide-content { padding-right: 4rem; z-index: 5; }
        .slide-tag { 
            font-family: 'Outfit', sans-serif;
            font-size: 0.75rem; 
            font-weight: 800; 
            color: var(--amazon-leaf); 
            letter-spacing: 0.3em; 
            text-transform: uppercase; 
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .slide-tag::before { content: ''; width: 40px; height: 2px; background: var(--amazon-leaf); }
        
        .slide-title { 
            font-family: 'Outfit', sans-serif;
            font-size: 4rem; 
            font-weight: 900; 
            line-height: 1; 
            color: white; 
            margin-bottom: 1.5rem;
            letter-spacing: -0.02em;
        }
        .slide-title span { color: var(--amazon-leaf); }
        .slide-sub { font-size: 1.25rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin-bottom: 2.5rem; }

        /* Visual de Slide */
        .slide-visual {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(20px);
            border-radius: 2.5rem;
            height: 85%;
            border: 1px solid rgba(34, 197, 94, 0.2);
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 40px 100px rgba(0,0,0,0.4);
        }

        .slide-visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.9;
            transition: transform 0.5s;
        }
        .slide-visual:hover img { transform: scale(1.05); }

        .visual-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 50%, rgba(10,26,15,0.9) 100%);
        }

        .visual-label {
            position: absolute;
            bottom: 2rem;
            left: 2rem;
            right: 2rem;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            background: rgba(34, 197, 94, 0.15);
            backdrop-filter: blur(10px);
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            border: 1px solid rgba(34, 197, 94, 0.3);
            text-align: center;
        }

        /* Mermaid styling */
        .mermaid { 
            background: rgba(0,0,0,0.2); 
            border-radius: 1.5rem; 
            padding: 2rem; 
            width: 100%; 
            height: 100%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
        }

        /* Stats */
        .stat-row { display: flex; gap: 1.5rem; margin-top: 2rem; }
        .stat-item { 
            background: rgba(34, 197, 94, 0.08); 
            border: 1px solid rgba(34, 197, 94, 0.2); 
            border-radius: 1.25rem; 
            padding: 1.5rem; 
            flex: 1; 
            text-align: center; 
            transition: transform 0.3s;
        }
        .stat-item:hover { transform: translateY(-5px); border-color: var(--accent-gold); }
        .stat-num { font-family: 'Outfit', sans-serif; font-size: 2.5rem; font-weight: 800; color: var(--amazon-leaf); }
        .stat-lbl { font-size: 0.75rem; color: rgba(255,255,255,0.5); text-transform: uppercase; letter-spacing: 0.1em; margin-top: 5px; }

        /* Lista de características */
        .feat-list { list-style: none; display: flex; flex-direction: column; gap: 1rem; }
        .feat-list li {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            transition: all 0.3s;
        }
        .feat-list li:hover { background: rgba(34, 197, 94, 0.05); border-color: rgba(34, 197, 94, 0.2); }
        .feat-list li .icon { color: var(--amazon-leaf); font-size: 1.25rem; }
        .feat-list li strong { display: block; color: #fff; font-size: 1rem; margin-bottom: 2px; }
        .feat-list li small { color: rgba(255,255,255,0.5); font-size: 0.8rem; }

        /* Controls */
        .nav-bar {
            position: fixed;
            bottom: 3rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(10, 30, 18, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 3rem;
            padding: 0.75rem 1.5rem;
            z-index: 100;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
        }
        .nav-btn {
            background: transparent;
            border: 1px solid var(--amazon-leaf);
            color: var(--amazon-leaf);
            padding: 0.6rem 1.5rem;
            border-radius: 2rem;
            cursor: pointer;
            font-family: 'Outfit', sans-serif;
            font-size: 0.8rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: all 0.3s;
        }
        .nav-btn:hover:not(:disabled) { background: var(--amazon-leaf); color: #000; }
        .nav-btn:disabled { opacity: 0.3; cursor: not-allowed; border-color: rgba(255,255,255,0.2); color: #fff; }
        
        .slide-counter { 
            color: #fff; 
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem; 
            font-weight: 700;
            padding: 0 1rem; 
            min-width: 80px; 
            text-align: center; 
        }

        .progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--amazon-leaf), var(--accent-gold));
            transition: width 0.6s cubic-bezier(0.22, 1, 0.36, 1);
            z-index: 200;
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.5);
        }

        .exit-btn {
            position: fixed;
            top: 2rem;
            left: 2rem;
            color: #fff;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-size: 0.75rem;
            font-weight: 800;
            z-index: 100;
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            padding: 0.75rem 1.25rem;
            border-radius: 2rem;
            backdrop-filter: blur(10px);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: all 0.3s;
        }
        .exit-btn:hover { background: rgba(34, 197, 94, 0.2); transform: translateX(-5px); }

        .slide-number {
            position: fixed;
            top: 1rem;
            right: 2rem;
            color: var(--amazon-leaf);
            font-family: 'Outfit', sans-serif;
            font-size: 6rem;
            font-weight: 900;
            opacity: 0.08;
            z-index: 1;
            pointer-events: none;
        }

        /* Fireflies */
        .firefly {
            position: absolute;
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--amazon-firefly);
            box-shadow: 0 0 10px 2px rgba(168, 216, 103, 0.4);
            animation: float 10s infinite ease-in-out;
            z-index: 1;
            opacity: 0;
        }
        @keyframes float {
            0%, 100% { transform: translate(0,0) scale(0.5); opacity:0; }
            50% { transform: translate(100px, -100px) scale(1.2); opacity:0.8; }
        }

        /* Roadmap */
        .roadmap { display: flex; flex-direction: column; gap: 1rem; width: 100%; padding: 2rem; }
        .road-item {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 1.25rem;
            background: rgba(255,255,255,0.03);
            border-radius: 1.25rem;
            border-left: 5px solid rgba(255,255,255,0.1);
            transition: all 0.3s;
        }
        .road-item.done { border-left-color: var(--amazon-leaf); background: rgba(34, 197, 94, 0.05); }
        .road-item.active-road { border-left-color: var(--accent-gold); background: rgba(212, 175, 55, 0.05); }
        .road-badge { font-size: 0.65rem; font-weight: 800; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; }
        .badge-done { background: var(--amazon-leaf); color: #000; }
        .badge-active { background: var(--accent-gold); color: #000; }

    </style>
</head>
<body>

    <div class="progress-bar" id="progressBar"></div>
    <a href="inicio" class="exit-btn">← Salir al Panel</a>
    <div class="slide-number" id="slideNum">01</div>

    <div class="pres-wrap">
        <div class="amazon-overlay"></div>
        <div class="mist-overlay"></div>
        
        <!-- Luciérnagas -->
        <?php for($i=0; $i<12; $i++): ?>
            <div class="firefly" style="left:<?=rand(0,100)?>%; top:<?=rand(0,100)?>%; animation-delay:<?=rand(0,10)?>s; animation-duration:<?=rand(8,15)?>s;"></div>
        <?php endfor; ?>

        <div class="slides-track" id="track">

            <!-- SLIDE 1: PORTADA -->
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-tag">ESE Fabio Jaramillo Londoño · 2026</div>
                    <h1 class="slide-title">FARMACIA<br><span>ESEFJL</span></h1>
                    <p class="slide-sub">Sistema de gestión inteligente para la red de salud del Caquetá. Control total en la Puerta de Oro de la Amazonía.</p>
                    <div class="stat-row">
                        <div class="stat-item"><div class="stat-num">6</div><div class="stat-lbl">Sedes</div></div>
                        <div class="stat-item"><div class="stat-num">100%</div><div class="stat-lbl">Trazabilidad</div></div>
                        <div class="stat-item"><div class="stat-num">SIAU</div><div class="stat-lbl">Conectado</div></div>
                    </div>
                </div>
                <div class="slide-visual">
                    <img src="<?= BASE_URL ?>/img/logoesefjl.jpg" alt="ESE FJL Logo" style="object-fit: contain; padding: 5rem;">
                    <div class="visual-overlay"></div>
                    <div class="visual-label">Sede Administrativa — Florencia, Caquetá</div>
                </div>
            </div>

            <!-- SLIDE 2: EL PROBLEMA -->
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-tag">Diagnóstico Crítico</div>
                    <h1 class="slide-title">Desafíos del<br><span>Pasado</span></h1>
                    <p class="slide-sub">La gestión manual ponía en riesgo la salud pública y la estabilidad institucional.</p>
                    <ul class="feat-list">
                        <li>
                            <span class="icon">⚠️</span>
                            <div><strong>Riesgo de Vencimientos</strong><small>Pérdidas económicas por falta de alertas proactivas en lotes.</small></div>
                        </li>
                        <li>
                            <span class="icon">📉</span>
                            <div><strong>Desabastecimiento</strong><small>Incapacidad de prever necesidades en las sedes municipales remotas.</small></div>
                        </li>
                        <li>
                            <span class="icon">📁</span>
                            <div><strong>Información Fragmentada</strong><small>Dificultad para consolidar reportes de auditoría para la Red de Vigilancia.</small></div>
                        </li>
                    </ul>
                </div>
                <div class="slide-visual">
                    <img src="<?= BASE_URL ?>/img/GrupoTrabajoSP.jpg" alt="Equipo de Trabajo">
                    <div class="visual-overlay"></div>
                    <div class="visual-label">Identificando fallas en el proceso manual</div>
                </div>
            </div>

            <!-- SLIDE 3: LA SOLUCIÓN -->
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-tag">Propuesta de Valor</div>
                    <h1 class="slide-title">Transformación<br><span>Digital</span></h1>
                    <p class="slide-sub">Un sistema robusto diseñado para la geografía y necesidades del Caquetá.</p>
                    <ul class="feat-list">
                        <li>
                            <span class="icon">⚡</span>
                            <div><strong>Semaforización Proactiva</strong><small>Alertas visuales inmediatas para medicamentos próximos a vencer.</small></div>
                        </li>
                        <li>
                            <span class="icon">🌐</span>
                            <div><strong>Red Sincronizada</strong><small>Conexión en tiempo real entre el CEDIS Central y las 5 IPS Municipales.</small></div>
                        </li>
                        <li>
                            <span class="icon">📊</span>
                            <div><strong>Auditoría Inteligente</strong><small>Reportes ejecutivos automáticos para la toma de decisiones gerenciales.</small></div>
                        </li>
                    </ul>
                </div>
                <div class="slide-visual">
                    <img src="<?= BASE_URL ?>/img/Gerente.jpg" alt="Gerencia">
                    <div class="visual-overlay"></div>
                    <div class="visual-label">Gerencia Institucional impulsando la innovación</div>
                </div>
            </div>

            <!-- SLIDE 4: ARQUITECTURA -->
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-tag">Estructura de Red</div>
                    <h1 class="slide-title">Ecosistema<br><span>Conectado</span></h1>
                    <p class="slide-sub">Modelo Hub-and-Spoke: Florencia como nodo central nutriendo la red municipal.</p>
                    <div class="stat-row">
                        <div class="stat-item"><div class="stat-num">5</div><div class="stat-lbl">Municipios</div></div>
                        <div class="stat-item"><div class="stat-num">1</div><div class="stat-lbl">Centro Central</div></div>
                    </div>
                </div>
                <div class="slide-visual">
                    <div class="mermaid">
                    graph TD
                        C[CEDIS Florencia] --> S1[IPS Solita]
                        C --> S2[IPS Solano]
                        C --> S3[IPS Milán]
                        C --> S4[IPS Getuchá]
                        C --> S5[IPS Valparaíso]
                        style C fill:#22c55e22,stroke:#22c55e,stroke-width:4px
                    </div>
                </div>
            </div>

            <!-- SLIDE 5: FLUJO OPERATIVO -->
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-tag">Procesos Seguros</div>
                    <h1 class="slide-title">Ciclo de Vida del<br><span>Insumo</span></h1>
                    <p class="slide-sub">Garantizamos que cada tableta llegue al paciente con seguridad y registro.</p>
                    <ul class="feat-list">
                        <li><strong>1. Recepción:</strong> Validación técnica y carga de lotes en CEDIS.</li>
                        <li><strong>2. Distribución:</strong> Traslado seguro a las IPS según pedido.</li>
                        <li><strong>3. Dispensación:</strong> Entrega final con registro de paciente y SMS.</li>
                    </ul>
                </div>
                <div class="slide-visual">
                    <div class="mermaid">
                    graph LR
                        A[Ingreso] --> B[Almacén]
                        B --> C{Pedido IPS}
                        C -->|Aprobado| D[Traslado]
                        D --> E[Entrega Paciente]
                        style E fill:#d4af3722,stroke:#d4af37
                    </div>
                </div>
            </div>

            <!-- SLIDE 6: TECNOLOGÍA -->
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-tag">Stack Tecnológico</div>
                    <h1 class="slide-title">Ingeniería de<br><span>Confianza</span></h1>
                    <p class="slide-sub">Desarrollado con herramientas que garantizan portabilidad y bajo costo de mantenimiento.</p>
                    <div class="stat-row">
                        <div class="stat-item"><div class="stat-num">PHP</div><div class="stat-lbl">Lógica 8.2</div></div>
                        <div class="stat-item"><div class="stat-num">SQL</div><div class="stat-lbl">Motor de Datos</div></div>
                    </div>
                </div>
                <div class="slide-visual">
                    <div style="padding: 3rem; text-align: center;">
                        <div style="font-size: 5rem; margin-bottom: 2rem;">🌿</div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; text-align: left;">
                            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); padding: 1.5rem; border-radius: 1rem;">
                                <div style="color: var(--amazon-leaf); font-weight: 800;">PHP 8.2</div>
                                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.6);">Servidor Seguro</div>
                            </div>
                            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); padding: 1.5rem; border-radius: 1rem;">
                                <div style="color: var(--amazon-leaf); font-weight: 800;">Postgres/SQLite</div>
                                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.6);">Persistencia</div>
                            </div>
                            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); padding: 1.5rem; border-radius: 1rem;">
                                <div style="color: var(--amazon-leaf); font-weight: 800;">CSS3/JS</div>
                                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.6);">UX Premium</div>
                            </div>
                            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); padding: 1.5rem; border-radius: 1rem;">
                                <div style="color: var(--amazon-leaf); font-weight: 800;">Docker</div>
                                <div style="font-size: 0.8rem; color: rgba(255,255,255,0.6);">Despliegue</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 7: ROADMAP -->
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-tag">Progreso del Proyecto</div>
                    <h1 class="slide-title">Ruta de<br><span>Éxito</span></h1>
                    <p class="slide-sub">Cronograma de implementación y hitos alcanzados.</p>
                </div>
                <div class="slide-visual">
                    <div class="roadmap">
                        <div class="road-item done">
                            <span class="road-badge badge-done">Completado</span>
                            <div><strong>Semana 1: Análisis</strong><small>Levantamiento de requerimientos y diseño de base de datos.</small></div>
                        </div>
                        <div class="road-item done">
                            <span class="road-badge badge-done">Completado</span>
                            <div><strong>Semana 2: Desarrollo Core</strong><small>Módulos de inventario central y gestión de sedes.</small></div>
                        </div>
                        <div class="road-item active-road">
                            <span class="road-badge badge-active">En Curso</span>
                            <div><strong>Semana 3: Despliegue IPS</strong><small>Integración de solicitudes municipales y reportes.</small></div>
                        </div>
                        <div class="road-item">
                            <span class="road-badge" style="background:rgba(255,255,255,0.1)">Próximo</span>
                            <div><strong>Semana 4: Auditoría</strong><small>Pruebas finales y capacitación institucional.</small></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SLIDE 8: CONCLUSIÓN -->
            <div class="slide">
                <div class="slide-content">
                    <div class="slide-tag">Compromiso Final</div>
                    <h1 class="slide-title">Futuro de la<br><span>Salud</span></h1>
                    <p class="slide-sub">FARMACIA ESEFJL no es solo un software, es una garantía de bienestar para todo el Caquetá.</p>
                    <div style="background: var(--amazon-mist); padding: 2rem; border-radius: 1.5rem; border: 1px solid var(--amazon-leaf);">
                        <p style="font-size: 1.1rem; color: var(--amazon-leaf); font-weight: 700; text-align: center;">"Gestión transparente, pacientes seguros."</p>
                    </div>
                </div>
                <div class="slide-visual">
                    <div style="text-align: center; padding: 4rem;">
                        <div style="font-size: 7rem; margin-bottom: 2rem;">✅</div>
                        <h2 style="font-family: 'Outfit', sans-serif; font-size: 2rem; font-weight: 900;">SISTEMA LISTO</h2>
                        <p style="color: rgba(255,255,255,0.6); margin-top: 1rem;">E.S.E Fabio Jaramillo Londoño — Florencia, Caquetá</p>
                    </div>
                </div>
            </div>

        </div><!-- end track -->
    </div>

    <!-- NAV BAR -->
    <div class="nav-bar">
        <button class="nav-btn" id="prevBtn" onclick="navTo(-1)">Anterior</button>
        <div class="slide-counter" id="counter">1 / 8</div>
        <button class="nav-btn" id="nextBtn" onclick="navTo(1)">Siguiente</button>
    </div>

    <script>
    let cur = 0;
    const total = 8;

    function navTo(dir) {
        cur = Math.max(0, Math.min(total - 1, cur + dir));
        update();
    }

    function update() {
        document.getElementById('track').style.transform = `translateX(-${cur * 100}vw)`;
        document.getElementById('progressBar').style.width = ((cur + 1) / total * 100) + '%';
        document.getElementById('counter').innerText = `${cur + 1} / ${total}`;
        document.getElementById('slideNum').innerText = String(cur + 1).padStart(2, '0');
        document.getElementById('prevBtn').disabled = cur === 0;
        
        const nextBtn = document.getElementById('nextBtn');
        if (cur === total - 1) {
            nextBtn.innerText = 'Finalizar';
            nextBtn.onclick = () => window.location.href = 'inicio';
        } else {
            nextBtn.innerText = 'Siguiente';
            nextBtn.onclick = () => navTo(1);
        }
    }

    window.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowRight' || e.key === ' ') navTo(1);
        if (e.key === 'ArrowLeft') navTo(-1);
    });

    update();
    </script>
</body>
</html>
