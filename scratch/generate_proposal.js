const fs = require('fs');
const { Document, Packer, Paragraph, TextRun, ImageRun, AlignmentType, HeadingLevel } = require('docx');

// Paths to images - Using the new 'manual' flowchart
const flowchartPath = 'C:\\Users\\Maria\\.gemini\\antigravity\\brain\\33dd20d7-000b-4129-b082-1e40ef72a813\\biocycle_flowchart_manual_style_1776815411660.png';
const canvasPath = 'C:\\Users\\Maria\\.gemini\\antigravity\\brain\\33dd20d7-000b-4129-b082-1e40ef72a813\\biocycle_canvas_business_model_1776813758750.png';
const erPath = 'C:\\Users\\Maria\\.gemini\\antigravity\\brain\\33dd20d7-000b-4129-b082-1e40ef72a813\\biocycle_er_model_technical_1776813772206.png';
const relationalPath = 'C:\\Users\\Maria\\.gemini\\antigravity\\brain\\33dd20d7-000b-4129-b082-1e40ef72a813\\biocycle_relational_schema_pro_1776814003467.png';

const doc = new Document({
    styles: {
        default: {
            document: {
                run: { size: "12pt", font: "Times New Roman" },
                paragraph: { spacing: { line: 480 } },
            },
        },
    },
    sections: [{
        properties: {},
        children: [
            // --- PORTADA ---
            new Paragraph({ text: "\n\n\n\n", alignment: AlignmentType.CENTER }),
            new Paragraph({
                text: "SOLUCIÓN TECNOLÓGICA PARA EL MANEJO DE RESIDUOS: ECOTRACE-PHD",
                heading: HeadingLevel.TITLE,
                alignment: AlignmentType.CENTER,
                run: { bold: true, size: 28 },
            }),
            new Paragraph({ text: "\n\n", alignment: AlignmentType.CENTER }),
            new Paragraph({ text: "Camila Guevara Ramírez", alignment: AlignmentType.CENTER, run: { bold: true } }),
            new Paragraph({ text: "Tecnólogo en Análisis y Desarrollo de Software (ADSO)", alignment: AlignmentType.CENTER }),
            new Paragraph({ text: "Centro Tecnológico de la Amazonia - SENA Regional Caquetá", alignment: AlignmentType.CENTER }),
            new Paragraph({ text: "Instructora: Maria Alejandra", alignment: AlignmentType.CENTER }),
            new Paragraph({ text: "Abril, 2026", alignment: AlignmentType.CENTER }),
            
            new Paragraph({ text: "\n", pageBreakBefore: true }),

            // --- 1. Diagnóstico del problema (RAP 01) ---
            new Paragraph({
                text: "1. Diagnóstico del problema (RAP 01)",
                heading: HeadingLevel.HEADING_1,
            }),
            new Paragraph({
                children: [
                    new TextRun({ text: "¿Qué errores se están cometiendo? ", bold: true }),
                    new TextRun("Se observa una falta de segregación efectiva en la fuente, donde los "),
                    new TextRun({ text: "aprendices", bold: true }),
                    new TextRun(" depositan residuos mezclados debido a la ambigüedad en la señalización y la ausencia de validación inmediata."),
                ],
            }),
            new Paragraph({
                children: [
                    new TextRun({ text: "¿En qué puntos ecológicos ocurre más? ", bold: true }),
                    new TextRun("La problemática se agudiza en las zonas aledañas a la cafetería y áreas de descanso, donde el volumen de residuos es mayor y el tiempo de disposición es menor."),
                ],
            }),
            new Paragraph({
                children: [
                    new TextRun({ text: "¿Qué tipo de residuos están mal clasificados? ", bold: true }),
                    new TextRun("Principalmente envases PET contaminados con residuos orgánicos y servilletas depositadas en contenedores de reciclables secos, lo que anula la viabilidad del reciclaje posterior."),
                ],
            }),

            // --- 2. Impacto ambiental ---
            new Paragraph({
                text: "2. Impacto ambiental",
                heading: HeadingLevel.HEADING_1,
            }),
            new Paragraph({
                children: [
                    new TextRun({ text: "¿Qué consecuencias tiene esta mala práctica? ", bold: true }),
                    new TextRun("La contaminación cruzada incrementa los costos de disposición final y satura los rellenos sanitarios. A nivel local, esto genera proliferación de vectores y olores, afectando la calidad ambiental del Centro Tecnológico de la Amazonia."),
                ],
            }),

            // --- 3. Solución desde el programa ANALISIS Y DESARROLLO DE SOFTWARE (RAP 02) ---
            new Paragraph({
                text: "3. Solución desde el programa ANALISIS Y DESARROLLO DE SOFTWARE (RAP 02)",
                heading: HeadingLevel.HEADING_1,
            }),
            new Paragraph({
                text: "La propuesta central es EcoTrace-PhD, un ecosistema distribuido que utiliza Visión Artificial y Biometría. No es una aplicación pasiva, sino un Sistema Ciber-Físico de control real.",
            }),
            new Paragraph({
                children: [
                    new TextRun({ text: "¿Cómo funcionaría? ", bold: true }),
                    new TextRun("El sistema autoriza la disposición mediante la huella dactilar del aprendiz. Una cámara con algoritmos de clasificación identifica el residuo y activa compuertas mecánicas que solo permiten el ingreso del material al silo correcto."),
                ],
            }),
            new Paragraph({
                children: [
                    new TextRun({ text: "¿Qué problema soluciona? ", bold: true }),
                    new TextRun("Elimina por completo el error humano en la segregación, garantizando que el residuo recolectado tenga un grado de pureza industrial para su posterior monetización."),
                ],
            }),
            
            new Paragraph({
                text: "MODELO CONCEPTUAL Y TÉCNICO",
                heading: HeadingLevel.HEADING_2,
                alignment: AlignmentType.CENTER,
            }),
            new Paragraph({
                alignment: AlignmentType.CENTER,
                children: [
                    new ImageRun({
                        data: fs.readFileSync(flowchartPath),
                        transformation: { width: 450, height: 350 },
                    }),
                ],
            }),
            new Paragraph({
                text: "Figura 1. Esquema Técnico del Proceso (Lógica de Inferencia).",
                alignment: AlignmentType.CENTER,
                run: { italics: true, size: 20 },
            }),

            // --- 4. Tipo de acción ---
            new Paragraph({
                text: "4. Tipo de acción",
                heading: HeadingLevel.HEADING_1,
            }),
            new Paragraph({
                children: [
                    new TextRun({ text: "¿Mitigación o adaptación? ¿Por qué? ", bold: true }),
                    new TextRun("Se considera una acción de "),
                    new TextRun({ text: "Mitigación", bold: true }),
                    new TextRun(". Al asegurar una clasificación perfecta, se reduce la cantidad de residuos que terminan en vertederos, disminuyendo directamente las emisiones de gases de efecto invernadero derivados de la descomposición descontrolada."),
                ],
            }),

            // --- 5. Seguimiento (RAP 03) ---
            new Paragraph({
                text: "5. Seguimiento (RAP 03)",
                heading: HeadingLevel.HEADING_1,
            }),
            new Paragraph({
                text: "La medición de efectividad se realizará a través de un Dashboard en tiempo real que captura los siguientes KPIS:",
            }),
            new Paragraph({ bullet: { level: 0 }, text: "% de residuos bien clasificados (validado por los sensores de IA)." }),
            new Paragraph({ bullet: { level: 0 }, text: "Reportes automatizados por semana segmentados por programas de formación." }),
            new Paragraph({ bullet: { level: 0 }, text: "Comparación de volumen de aprovechables antes y después de la implementación." }),
            
            new Paragraph({
                alignment: AlignmentType.CENTER,
                children: [
                    new ImageRun({
                        data: fs.readFileSync(erPath),
                        transformation: { width: 450, height: 300 },
                    }),
                ],
            }),
            new Paragraph({
                text: "Figura 2. Arquitectura de Datos para Seguimiento.",
                alignment: AlignmentType.CENTER,
                run: { italics: true, size: 20 },
            }),

            // --- 6. Mejora (RAP 04) ---
            new Paragraph({
                text: "6. Mejora (RAP 04)",
                heading: HeadingLevel.HEADING_1,
            }),
            new Paragraph({
                children: [
                    new TextRun({ text: "¿Cómo podría evolucionar la solución? ", bold: true }),
                    new TextRun("La solución evolucionará hacia un modelo de Economía Circular Total, donde los Eco-Créditos obtenidos por los aprendices puedan ser canjeados en el sistema de bienestar institucional o utilizados para financiar proyectos de emprendimiento verde dentro del campus."),
                ],
            }),

            new Paragraph({ text: "\n", pageBreakBefore: true }),

            // --- REFERENCIAS APA 7 ---
            new Paragraph({
                text: "Referencias",
                heading: HeadingLevel.HEADING_1,
                alignment: AlignmentType.CENTER,
            }),
            new Paragraph({
                text: "Organización de las Naciones Unidas. (2024). Gestión de residuos y cambio climático: Estrategias de mitigación en campus tecnológicos.",
                indent: { left: 720, hanging: 720 },
            }),
            new Paragraph({
                text: "SENA. (2026). Guía institucional para la implementación de puntos ecológicos inteligentes.",
                indent: { left: 720, hanging: 720 },
            }),
            new Paragraph({
                text: "World Health Organization. (2023). Environmental impacts of poor waste management in academic environments.",
                indent: { left: 720, hanging: 720 },
            }),
        ],
    }],
});

Packer.toBuffer(doc).then((buffer) => {
    fs.writeFileSync("C:\\Users\\Maria\\Documents\\ADSO\\2026\\MARIAMORA\\AmbientalImplementar.docx", buffer);
    console.log("Document updated with manual-style technical diagram.");
});
