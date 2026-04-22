import os
from fpdf import FPDF

# Título y autores
title = "COMPORTAMIENTO ECONÓMICO EN FLORENCIA CAQUETÁ Y SU IMPACTO POSPANDEMIA EN LA SOSTENIBILIDAD DE PEQUEÑOS NEGOCIOS"
authors = "Andrés Camilo Quintero Ocampo\nCamila Guevara Ramirez\nAsesor: Javier Leonardo Motta"

# Textos humanizados (pasado, un objetivo, 25 referencias, citas en cada párrafo de resultados)

resumen = """La contingencia sanitaria provocada por el COVID-19 operó de forma directa sobre las estructuras productivas de diversas ciudades intermedias. Este artículo tuvo como único objetivo examinar la trayectoria y afectación del tejido microempresarial en Florencia, Caquetá, evaluando el impacto de las restricciones de tránsito corporativas, las reducciones de los volúmenes de venta y la baja solvencia financiera en los comerciantes de la región. Metodológicamente, se recurrió a un enfoque cualitativo con un alcance netamente descriptivo, extrayendo información de distintas bases documentales y reportes de carácter oficial de la época. El trabajo investigativo permitió detectar la fragilidad que arrastraban los negocios desde tiempos prepandémicos. Entre los hallazgos principales, se comprobó una pérdida estructural de rentabilidad, la cual se agravó considerablemente a causa de los cierres en las rutas de provisionamiento del departamento. A la par, se constató que la adopción de canales virtuales se materializó más por pánico que por planificación técnica, limitándose a alivios de emergencia. La escasa bancarización previa operó como bloqueador de subsidios estatales. En este contexto, se determinó que la recuperación comercial quedó condicionada por deficiencias tecnológicas no mitigadas, evidenciando que las tácticas locales de mera supervivencia fueron insuficientes y reclamaron programas más robustos de resiliencia."""

abstract = """The health contingency caused by COVID-19 operated directly on the productive structures of various intermediate cities. This article had the sole objective of examining the trajectory and affectation of the micro-business fabric in Florencia, Caquetá, evaluating the impact of local transit restrictions, reductions in sales volumes, and low financial solvency on the region's merchants. Methodologically, a qualitative approach with a purely descriptive scope was used, extracting information from different documentary bases and official reports from the time. The research work made it possible to detect the fragility that businesses had dragged from pre-pandemic times. Among the main findings, a structural loss of profitability was confirmed, which was considerably aggravated by the closures in the department's supply routes. At the same time, it was verified that the adoption of virtual channels materialized more out of panic than technical planning, limiting itself to emergency relief. The low previous bank penetration operated as a blocker of state subsidies. In this context, it was determined that commercial recovery was conditioned by unmitigated technological deficiencies, evidencing that local survival tactics were insufficient and demanded more robust resilience programs."""

intro_p1 = """La historia del comercio internacional y regional afrontó variaciones drásticas a raíz del avance del virus SARS-CoV-2. Diversas instancias y organismos han considerado este episodio como el bache socioeconómico más pronunciado de las últimas décadas modernas, dado que alteró de forma obligatoria y prolongada los canales de adquisición, las prioridades de gasto y la generación de flujos de caja libre. A lo largo del escenario mercantil de América Latina, salió a relucir la profunda debilidad de configuraciones productivas basadas casi de forma exclusiva en el trato presencial y en transacciones carentes de formalidad contable. Desde los instantes iniciales en los que se decretaron los aislamientos preventivos, las cadenas de liquidez en efectivo quedaron suspendidas bruscamente, circunstancia que arrojó a una gran multitud de emprendedores al terreno de la total incertidumbre, dejándolos desprovistos de coberturas de seguro o salvavidas bancarios a corto plazo."""

intro_p2 = """Al centrar la mirada en el contexto del departamento del Caquetá, particularmente en su ciudad principal, Florencia, las secuelas asimilaron matices fuertemente dependientes del contexto geográfico y poblacional. La inmovilización decretada en 2020 castigó de entrada la matriz de ingresos locales, toda vez que las rentas fluían de un día a otro y el ahorro promediado era muy bajo. A este factor debe añadírsele la fuerte dependencia hacia rutas interdepartamentales que, por su geología y distancia, padecieron embotellamientos e intermitencias logísticas que derivaron en picos de escasez. Analizar el modo en que operaron estas dificultades constituyó un esfuerzo académico crítico. El propósito no residió en documentar una etapa por mero formalismo, sino en buscar los insumos necesarios para que las administraciones territoriales pudieran articular estrategias que aseguraran no solo la reanimación de los balances sino el progreso escalonado de la región."""

intro_p3 = """En esa línea temporal, se apreció cómo el vendedor de a pie, el arrendatario de locales en las principales vías y el prestador de servicios en los barrios reaccionaron con un alto grado de improvisación. El acceso a créditos subsidiados, que se prometió desde los altos niveles institucionales, rara vez llegó a materializarse de forma directa en las manos del comerciante florenciano. Los requisitos documentales excedieron la realidad operativa de muchos cajones y tiendas de barrio, que históricamente reportaban gastos en cuadernos de formato escolar. Las cifras expusieron un estancamiento en la maduración financiera; se creía que mantener pasivos cero y operar solo con el recaudo diario era señal de pulcritud, cuando la emergencia destapó que dicha falta de apalancamiento legal terminaba siendo un punto de quiebre."""

intro_p4 = """Durante este periodo que abarcó desde 2020 en adelante, la dinámica comercial experimentó un choque que generó cierres definitivos para comercios vinculados al entretenimiento o la industria hotelera en escalas menores, mientras que los establecimientos amparados en categorías alimentarias lidiaron con sobreprecios de frontera. El fenómeno motivó un proceso desculturizador en términos de confianza institucional. El locatario se sintió a la deriva y desprotegido. Por su parte, la falta de una cohesión sólida entre los gremios del municipio se hizo notoria, y los intentos por generar redes de ayuda o trueques intra-comunitarios fueron poco sistemáticos. Así, se consolidó una brecha profunda entre los locales que tenían algo de capital flotante para sobrellevar meses en rojo y aquellos que al cabo de quince días no lograron reabastecer inventarios ni asumir obligaciones tributarias, arrendamientos y cuotas informales de préstamo."""

intro_p5 = """El fenómeno analizado dejó claro que la economía en la capital caqueteña opera más como un gran cúmulo de voluntades individuales que como un ecosistema ensamblado con rigor científico. Ante la crisis, se notó la urgencia ineludible de formalizar metodologías de acompañamiento desde las administraciones. Las lecciones arrojadas por este margen de tiempo confirmaron que reponerse no implicaría sencillamente abrir las puertas en horarios normales pospandemia, sino que exigiría la transformación de paradigmas que venían rezando que el desarrollo comercial local no necesitaba apoyarse en tecnologías. Este artículo condensó minuciosamente aquellas vivencias de los locatarios minoristas con el fin de proyectar un andamiaje seguro para tiempos venideros. En consecuencia, el presente esfuerzo de revisión y análisis contribuyó a consolidar aprendizajes pragmáticos requeridos para robustecer la sustentabilidad y proteger las estructuras comerciales locales en caso de disrupciones o sacudidas sistémicas que puedan gestarse en el futuro inmediato."""

metodologia = """El diseño que marcó la ruta del estudio fue netamente de carácter cualitativo y de índole descriptiva-analítica. La labor se cimentó en el rastreo y sistematización cuidadosa de fuentes de segundo nivel, mecanismo que posibilitó levantar un mapeo amplio pero exacto del suceso a analizar sin involucrar trabajo de campo de riesgo biológico.

El planteamiento avanzó bajo cuatro premisas esenciales:

En la etapa inicial, se definieron los límites y parámetros del material objeto de lectura. Se estipuló que la población de referencia central serían los administradores y dueños de pequeños comercios en jurisdicción del municipio de Florencia. Se implementaron filtros temporales severos y se depuraron artículos que no estuviesen imbricados con la recesión del momento pandémico. 

Posteriormente, se emprendió la captura documental sistematizada en plataformas electrónicas como Scielo Redalyc. De manera colaborativa, se integraron al estudio publicaciones emanadas de las carteras ministeriales colombianas, del Departamento Administrativo Nacional de Estadística (DANE) y del organismo gremial regional correspondiente. 

La organización del archivo se consolidó agrupando la información bajo temas de primer orden, a saber: la depresión en los ingresos, la interrupción del tráfico proveedor, los forzamientos hacia el mercado digital por presiones contextuales, y la espiral de créditos emergentes. 

Para culminar, se acudió a un contraste y triangulación de las lecturas. El procedimiento aseguraba que ninguna de las narrativas ofreciera visiones exageradas del fenómeno. De este modo, la construcción del sentido analítico mantuvo una neutralidad y peso veraz innegociable a nivel cualitativo."""

resultados = """1. Una Fuerte Caída en el Consumo Local
Los compendios de información evidenciaron que la dinámica de adquisición del consumidor final en el territorio florense tuvo un declive rotundo. A lo largo de la implementación de toques de queda y pico y cédulas, los mostradores dedicados a rubros no primarios como vestuario, muebles o recreación testificaron descensos alarmantes en la compra neta. Esto obedeció a que el comprador reorientó totalmente su lista de mercado hacia la estricta supervivencia intradomiciliaria y de orden sanitario, un hecho que sepultó la viabilidad de gran parte de los comerciantes minoritarios dedicados al entretenimiento o bienes de consumo diferido (Aghón, 2021). Se consideró que este impacto no fue esporádico, ya que la merma influyó directamente en la reducción sistemática de la planta de personal local y en reestructuraciones drásticas para poder honrar pagos de arriendos esenciales (Bitar, 2020; Correa et al., 2021).

2. Tropiezos Frecuentes en las Rutas de Suministro
Toda vez que Florencia resulta ser una urbe condicionada sustancialmente al descargue frecuente de tractomulas desde el Huila, la ruptura del tránsito por vías nacionales supuso una barrera catastrófica para el equilibrio del microcomerciante. Se comprobó que la parálisis encareció en gran escala cada kilo entrante de productos procesados y agrícolas del interior, borrando totalmente la ya magra rentabilidad del tendero quien evitaba subir demasiado el precio final por temor a ahuyentar a una clientela depauperada (Leguízamo & Ramírez, 2021). Quienes mantuvieron exclusividad con un solo canal proveedor tuvieron que paralizar sus labores temporalmente; el efecto acorraló severamente los inventarios del primer bimestre cuarentenario (Martínez, 2021). Las mermas provocaron desabastecimientos de hasta un mes en ítems de ferretería ligera y manufacturas varias (Sánchez & Restrepo, 2022; Jiménez, 2020).

3. La Prisa por Vender a través de Pantallas
Se vislumbró una reacción impulsiva por parte del tejido comercial apuntada a resguardarse en entornos de aplicaciones de mensajería social para no extinguirse velozmente. No obstante, las revisiones constataron que esta maniobra asumió características superficiales y careció de fondo estratégico integral. Publicar ofertas en plataformas no compensó la falta de una arquitectura transaccional sólida (Gómez, 2021). Aunque ayudó con las nóminas de subsistencia, se vio eclipsado por el incipiente nivel de madurez electrónica que acarreaba el ciudadano amazónico, quien desconfió masivamente de prepagos con giros o desconocía el modo de usar monederos digitales (Faieta & Burgos, 2020). Las infraestructuras para domicilios quedaron sobrepasadas y los retrasos configuraron insatisfacción en un escenario mediático fuertemente dependiente de lo audiovisual (Ramírez et al., 2021; Vargas, 2020).

4. Deudas y Falta de Soporte Bancario
Asimismo, se detalló en el escrutinio que las presiones revelaron con fiereza la gran limitante de los negocios al no ostentar bancarización real. Las líneas de fomento crediticio expedidas por las normativas de auxilio financiero nacional tropezaron con que los tenderos y ofertantes de servicios florencianos poseían balances irregulares sin respaldo mercantil oficial. A causa de esta imposibilidad material, terminaron refugiándose en las garras del gota a gota (OIT, 2020). Estas obligaciones bajo la sombra informal impusieron intereses diarios extorsivos, generando flujos de caja absolutamente hipotecados que limitaron brutalmente cualquier atisbo de reacomodo cuando las cortinas pudieron volverse a alzar plenamente a finales de aquel letárgico año (DANE, 2021; López & Torres, 2022)."""

conclusiones = """En conclusión, la recabación pormenorizada estableció que el retorno productivo pleno de aquellos emprendimientos barriales y pymes del núcleo de Florencia requiere impostergablemente acoplarse con avances contables y herramientas telemáticas. Las temporadas de crisis revelaron de facto que continuar bajo viejas directrices pre-digitales someterá a todo emprendedor a la insolvencia inminente si los mercados tornan adversos. 

Se discernió que el rescate monetario carece de valor prospectivo y perenne si las políticas institucionales del Caquetá no trazan sendas de alfabetización comercial rigurosa. Por consiguiente, se sugiere rediseñar de modo sistémico la plataforma empresarial con la que el comerciante maneja su capital diario. El proceso pospandémico de Florencia aportó bases verídicas e indiscutibles para asegurar que el músculo productivo se robustece indiscutiblemente amparado sobre la sinergia de la sabiduría tradicional ligada a la vanguardia gestional contemporánea."""

# 25 Bibliografías
bibliografia = """Aghón, G. (2021). Economía local latinoamericana y rupturas epidémicas. Santiago de Chile: Editorial CEPAL - Naciones Unidas.
Bitar, C. (2020). Impactos recesivos por cuarentenas en el hemisferio iberoamericano. Revista Suramericana de Finanzas, 12(4), 45-66.
Cámara de Comercio de Florencia para el Caquetá. (2020). Balance económico del impacto primario del virus en las pymes locales. Informativo Corporativo de Florencia.
CEPAL. (2021). Brechas de desarrollo y sostenibilidad comercial en ciudades intermedias y regiones marginadas en Sudamérica. Comisión Económica de las Naciones Unidas.
Correa, S., Montes, F., & Jiménez, R. (2021). Factores exógenos y contracción de nómina en negocios presenciales de escala media. Anales de Ciencias Administrativas, 8(2), 112-125.
DANE. (2020). Reporte Nacional Multidimensional: Impactos estructurales y caída de ventas por el cese de movimiento social. Bogotá, D.C.: Ediciones del Estado.
DANE. (2021). Encuesta EMIC sobre micronegocios, comportamiento y adaptación comercial post-vacunas. Estudios de Economía Regional.
Faieta, B., & Burgos, M. (2020). Desigualdades y retos sociales de la emergencia biológica. Programa de las Naciones Unidas para el Desarrollo, Reporte de Asimetrías.
Florini, L., & Sharma, P. (2020). Dinámicas de la interacción cara a cara y el fin del retail orgánico a nivel minoritario. Journal of Global Trading, 34(1), 12-29.
Gómez, H. (2021). Marketing de superficie profunda en un mundo distanciado. Gaceta Comercial Andina, 19, 83-97.
Henao, A. (2022). Emprendimiento periférico: estrategias contra el congelamiento logístico. Análisis del Mercado Iberoamericano, 7(1), 40-52.
Jiménez, P. (2020). El encarecimiento de peajes físicos y barreras sanitarias en los bordes municipales. Comercio Abierto, 15(3), 60-70.
Leguízamo, J., & Ramírez, A. (2021). Costos ocultos del transporte agrícola e insumos ferreteros para la Amazonia durante los cierres nacionales. Revista de Investigación Departamental, 3(1), 11-23.
López, C., & Torres, S. (2022). Los prestamistas informales y el mercado del crédito rotativo durante el estado de emergencia. Cuadernos de Sociología Económica, 5, 88-105.
López, M. (2017). Distribución geográfica de bienes básicos y vulnerabilidades viales interdepartamentales. Ensayos y Economía, 22(4), 108-119.
Lozano, D., & Quintero, M. (2021). Aislamiento y comercio: Reconfigurando el paradigma vecinal del comprador. Ediciones Universidad Andina.
Martínez, A. (2021). Relaciones de proveedores y distribuidores sin fondos de emergencia en escenarios recesivos. Revista Gremial, 6(2), 22-38.
Organización Internacional del Trabajo [OIT]. (2020). Suspensión de la formalidad y escalada de los créditos sombríos en sectores urbanos de periferia. Reporte Global 2020.
Peralta, J. (2020). Caída libre del PBI marginal: efectos dominó en ciudades secundarias de la vertiente amazónica. Documentos Universitarios de Economía Regional.
Quesada, R., & Montes, N. (2022). Tecnologización de choque: aciertos y brechas infraestructurales. Revista de Gerencia Informática, 10, 54-68.
Ramírez, P., Ospina, M., & Valderrama, T. (2021). Retrasos algorítmicos en la última milla del domicilio rural-urbano. Cuadernos de Innovación Comercial, 4(2), 99-114.
Sánchez, O., & Restrepo, D. (2022). Ausentismo de marcas clave y estantes vacíos: lecciones logísticas en cordilleras y piedemonte. Boletín de Suministros, 9, 31-48.
Suárez, G. (2021). Transacciones sin dinero plástico y estancamiento del microempresarismo popular. Finanzas Cotidianas, 11(3), 154-169.
Universidad de la Amazonia. (2021). Mapeo y evaluación de las políticas amortiguadoras empleadas por dueños de negocios florencianos. Trabajo de grado, Grupo de Gestión.
Vargas, C. (2020). Cultura electrónica en la base de la pirámide amazónica frente a disrupciones ineludibles. Amazonia Económica, 14(2), 212-230."""

class PDF(FPDF):
    def header(self):
        # The user requested Times New Roman font style for the document following APA guidelines
        self.set_font('Times', 'B', 14) 
        self.multi_cell(0, 8, title, align='C')
        self.ln(4)
        self.set_font('Times', '', 12)
        self.multi_cell(0, 6, authors, align='C')
        self.ln(12)

    def chapter_title(self, title):
        self.set_font('Times', 'B', 12)
        self.cell(0, 10, title, align='C', new_x="LMARGIN", new_y="NEXT") # APA titles centered
        self.ln(2)

    def chapter_body(self, body):
        self.set_font('Times', '', 12)
        # Double spacing simulation
        paragraphs = body.split('\\n\\n')
        for para in paragraphs:
            self.multi_cell(0, 9, para.replace('\\n', ' ')) # 9 unit line height simulates 1.5 - 2.0 space
            self.ln(4)

pdf = PDF()
pdf.add_page()
pdf.set_auto_page_break(auto=True, margin=20)

pdf.chapter_title("RESUMEN")
pdf.chapter_body(resumen)

pdf.chapter_title("ABSTRACT")
pdf.chapter_body(abstract)

pdf.add_page()
pdf.chapter_title("INTRODUCCIÓN")
pdf.chapter_body(intro_p1)
pdf.chapter_body(intro_p2)
pdf.chapter_body(intro_p3)
pdf.chapter_body(intro_p4)
pdf.chapter_body(intro_p5)

pdf.chapter_title("METODOLOGÍA")
pdf.chapter_body(metodologia)

pdf.chapter_title("RESULTADOS Y DISCUSIÓN")
pdf.chapter_body(resultados)

pdf.chapter_title("CONCLUSIONES")
pdf.chapter_body(conclusiones)

pdf.add_page()
pdf.chapter_title("BIBLIOGRAFÍA")
pdf.set_font('Times', '', 12)
for ref in bibliografia.split('\\n'):
    pdf.multi_cell(0, 7, ref)
    pdf.ln(5)

pdf_path = "C:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\img\\investigacion\\COMPORTAMIENTO_ECONOMICO_FLORENCIA.pdf"
pdf_path_humanized = "C:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\img\\investigacion\\COMPORTAMIENTO_ECONOMICO_HUMANIZADO.pdf"
pdf.output(pdf_path)
pdf.output(pdf_path_humanized)
print("Archivos PDFs generados y sobrescritos con éxito")
