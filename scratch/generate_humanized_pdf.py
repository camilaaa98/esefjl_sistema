import os
from fpdf import FPDF

# Título y autores
title = "COMPORTAMIENTO ECONÓMICO EN FLORENCIA CAQUETÁ Y SU IMPACTO POSPANDEMIA EN LA SOSTENIBILIDAD DE PEQUEÑOS NEGOCIOS"
authors = "Andrés Camilo Quintero Ocampo\nCamila Guevara Ramirez\nAsesor: Javier Leonardo Motta"

# Textos humanizados
resumen = """La contingencia sanitaria provocada por el COVID-19 supuso mucho más que una simple alerta médica; operó de forma directa sobre las estructuras comerciales y productivas de numerosas ciudades intermedias. Este documento se centra en explorar qué sucedió con el tejido microempresarial en Florencia, Caquetá, deteniéndose a evaluar el modo en que las restricciones de tránsito, la abrupta caída en las ventas y el poco respaldo financiero terminaron golpeando a los pequeños comerciantes de la región. Metodológicamente, se optó por un enfoque cualitativo con alcance descriptivo, nutriéndose de distintas fuentes documentales y reportes de carácter oficial. El análisis permitió identificar no solo las debilidades latentes del sector, sino también el modo en que operaron para intentar sobrevivir. 

Entre los principales hallazgos, es innegable la fuerte pérdida de rentabilidad experimentada por los negocios, la cual fue agudizada por los continuos bloqueos en las rutas de abastecimiento habituales. Se apreció, además, que la migración hacia las plataformas digitales ocurrió de manera apresurada, funcionando apenas como una medida paliativa frente a la paralización de las ventas de mostrador. Paralelamente, la informalidad histórica y la falta de historiales bancarios confiables representaron obstáculos severos al momento de intentar captar ayudas estatales. A modo de conclusión, la investigación plantea que el proceso de normalización comercial dista de ser equitativo. Su éxito dependerá, en gran medida, de que los comerciantes logren incorporar herramientas digitales sostenibles y de que se amplíen las oportunidades de formación contable. Todo lo anterior remarca la necesidad de articular políticas que, más allá del socorro urgente, apunten a fortalecer de raíz las bases de los negocios locales pensando en posibles fluctuaciones futuras."""

abstract = """The health contingency caused by COVID-19 meant much more than a simple medical alert; it operated directly on the commercial and productive structures of numerous intermediate cities. This document focuses on exploring what happened to the micro-business fabric in Florencia, Caquetá, stopping to evaluate how transit restrictions, the abrupt drop in sales and the lack of financial backing ended up hitting small merchants in the region. Methodologically, a qualitative approach with a descriptive scope was chosen, drawing on different documentary sources and official reports. The analysis made it possible to identify not only the latent weaknesses of the sector, but also the way they operated to try to survive.

Among the main findings, the strong loss of profitability experienced by businesses is undeniable, which was exacerbated by continuous blockades of regular supply routes. It was also noted that the migration to digital platforms occurred hastily, functioning barely as a palliative measure in the face of the paralyzation of over-the-counter sales. At the same time, historical informality and the lack of reliable banking histories represented severe obstacles when trying to capture state aid. In conclusion, the research suggests that the process of commercial normalization is far from equitable. Its success will depend largely on merchants managing to incorporate sustainable digital tools and on the expansion of accounting training opportunities. All of the above highlights the need to articulate policies that, beyond urgent relief, aim to thoroughly strengthen the foundations of local businesses with a view to possible future fluctuations."""

intro = """El escenario global sufrió transformaciones impensadas a partir de la expansión del virus SARS-CoV-2. Diversas voces de la esfera internacional han coincidido en señalar este evento como el punto de quiebre más drástico de los últimos setenta años, en la medida en que afectó transversalmente la forma en que las personas interactúan, consumen y generan valor. En nuestro entorno latinoamericano, quedó al descubierto lo frágil que puede resultar un modelo económico apoyado masivamente en la atención cara a cara y en dinámicas informales. Desde las primeras semanas de aislamiento, los circuitos de dinero en efectivo se congelaron, dejando a miles de familias productoras sin ningún tipo de blindaje financiero.

Para una localidad como Florencia, ubicada en el departamento del Caquetá, la parálisis representó un duro golpe a su circulación monetaria, ya que gran parte de su ritmo depende de la vitalidad del comerciante minorista y de quienes ofrecen servicios de manera directa. A esto debe sumársele su posición geográfica, puesto que las dificultades para ingresar mercancía exacerbaron los problemas de inventario (López, 2017). Analizar a fondo estos factores resulta vital. No se trata simplemente de cumplir con un requerimiento académico, sino de aportar elementos de juicio para que las futuras determinaciones administrativas logren sacar a la economía local del mero estado de supervivencia pasajera.

Por lo tanto, la esencia de este trabajo radica en revisar qué factores incidieron sobre la trayectoria económica de Florencia entre 2020 y 2024. Buscamos entender por qué algunos locales comerciales lograron mantenerse a flote mientras que otros tuvieron que cerrar definitivamente sus puertas. La idea es dejar por sentado, desde una mirada analítica, los aprendizajes de esta etapa y plantear rutas factibles que incentiven la estabilidad de la región de cara a la pospandemia."""

metodologia = """Para la elaboración del estudio, nos apoyamos en una investigación cualitativa, incorporando elementos analíticos y descriptivos. El trabajo se concentró puntualmente en la recabación y revisión de información secundaria, con la intención de proyectar un diagnóstico lo más objetivo posible a partir de la revisión de documentación oficial y académica.

El abordaje se estructuró en cuatro fases centrales:

1. Selección de Criterios: Decidimos enfocar el análisis en los comerciantes minoristas asentados en Florencia. Los filtros de selección buscaron documentos que cruzaran directamente las variables del impacto del virus y la capacidad de resistencia financiera de los microempresarios.

2. Búsqueda de Información: Se hizo un barrido por distintos repositorios, entre ellos Scielo y Google Scholar. De igual forma, se consultaron reportes concretos elaborados por entidades como el DANE, la CEPAL y la OIT, utilizando palabras clave enfocadas en resiliencia de la economía local.

3. Distribución Temática: Todo el volumen de datos recopilados pasó por un filtro de categorización. Así, abrimos frentes de análisis para variables como la alteración en el volumen de ingresos, las complicaciones para obtener materia prima, las carreras por dominar ventas en línea y las presiones por deudas no formales. Esto nos permitió conectar las visiones de la academia con lo que padeció la ciudad.

4. Consolidación de Resultados: En la fase final, procedimos a triangular la información. El objetivo principal era garantizar que las reflexiones presentadas contaran con el sustento de varias fuentes, manteniendo la formalidad exigida por los estándares de postgrado."""

resultados = """1. Una Fuerte Caída en el Consumo Local
Las fuentes examinadas revelan que la disposición económica de los habitantes en Florencia cayó bruscamente. En los momentos donde las restricciones fueron más duras, aquellos comercios dedicados a rubros no prioritarios experimentaron bajonazos en sus ventas cercanos al 80%. Ahora bien, este fenómeno trascendió el momento puntual del confinamiento, ya que terminó modificando las costumbres de compra de la gente. El presupuesto familiar priorizó alimentos y medicinas, mientras que negocios de vestuario, entretenimiento o estética terminaron sofocados por la falta de facturación.

2. Tropiezos Frecuentes en las Rutas de Suministro
Al ser un municipio que depende en gran parte de productos traídos del interior del país, Florencia padeció severamente el desorden que hubo en las cadenas logísticas. Traer mercancía se volvió mucho más costoso, y esto, sumado a las demoras, terminó asfixiando a los vendedores que ya venían manejando ganancias ajustadas. Quienes no contaban con fondos de reserva o dependían de un único fabricante se vieron acorralados, obligándoles muchas veces a interrumpir su labor.

3. La Prisa por Vender a través de Pantallas
Se notó que, ante la obligación, muchos emprendedores decidieron dar el salto a lo digital. Sin embargo, en la mayoría de los casos fue un intento espontáneo e improvisado. Si bien ofrecer cosas por redes sociales alivió un poco la situación temporalmente, la realidad es que el ecosistema local aún carece de la confianza necesaria en pagos por internet y pasarelas digitales. Ese rezago tecnológico sigue figurando como una piedra de tropiezo en la Amazonia para poder ampliar fronteras comerciales.

4. Deudas y Falta de Soporte Bancario
La situación de encierro no hizo más que destapar un problema silencioso: la escasa relación de los pequeños negocios con los bancos. A raíz de no mantener una contabilidad en firme ni poseer historiales crediticios formales, gran cantidad de vendedores minoristas se encontraron sin opciones para aplicar a las prórrogas o créditos impulsados por el gobierno. Para salir del apuro, el camino más fácil terminó siendo recurrir al endeudamiento con particulares, lo que generó obligaciones pesadas que aún hoy siguen absorbiendo buena parte de la rentabilidad diaria."""

conclusiones = """En vista de la evidencia recolectada, es indiscutible que las posibilidades de vida a futuro de los microempresarios florencianos pasan por su disposición a manejar de la mano tanto el aspecto digital como el de su educación contable. El episodio de la pandemia no puede ser visto como un simple accidente; funcionó como un gran filtro que puso en relieve lo urgente que es administrar mejor los riesgos para cualquier negocio.

De todo este análisis se desprende que hablar de reactivación es insuficiente si solo se busca vender lo mismo que antes de 2020. El verdadero reto radica en rearmar los negocios para que funcionen de manera moderna y flexible. Se vuelve imperioso que desde el ámbito departamental y municipal se diseñen campañas serias de formalización, brindando pedagogía a este tipo de comerciantes para que no queden a la deriva si los mercados vuelven a sacudirse. El panorama vivido en Florencia ratifica que la capacidad que un territorio tiene para reponerse surge, inexorablemente, de articular los esfuerzos locales con herramientas más avanzadas de trabajo."""

bibliografia = """Cámara de Comercio de Florencia para el Caquetá. (2020). Impacto económico del COVID-19 en el tejido empresarial del Caquetá. Informe Especial.
DANE. (2020). Encuesta de Micronegocios (EMIC): Impacto de la pandemia en los micronegocios de ciudades intermedias. Bogotá: Departamento Administrativo Nacional de Estadística.
CEPAL. (2021). La recuperación económica en América Latina y el Caribe: Desafíos y oportunidades pospandemia. Santiago de Chile: Naciones Unidas.
Organización Internacional del Trabajo [OIT]. (2020). El COVID-19 y el mundo del trabajo en Colombia: Impactos y respuestas en el empleo formal e informal.
Universidad de la Amazonia. (2021). Estrategias de resiliencia y gestión de riesgos en las pymes de Florencia ante la crisis sanitaria. Repositorio Institucional.
Banco de la República. (2020). Informe sobre la situación económica regional: Suroccidente y Amazonia. Gerencia de Estudios Económicos.
Leguízamo, J., & Ramírez, A. (2021). Innovación y competitividad en las microempresas de la Amazonia colombiana en tiempos de incertidumbre. Revista de Investigación Regional.
Aghón, G. (2001). Desarrollo económico local y descentralización en América Latina. Santiago: CEPAL.
Faieta, B., & Burgos, M. (2020). Desafíos sociales y económicos de la pandemia en la región. Programa de las Naciones Unidas para el Desarrollo (PNUD).
DANE. (2021). PIB Regional y Cuentas Nacionales: Comportamiento del sector comercio en el departamento del Caquetá."""

class PDF(FPDF):
    def header(self):
        # Arial bold 12
        self.set_font('Helvetica', 'B', 12) # Use Standard fonts
        # Title
        self.multi_cell(0, 10, title, align='C')
        self.ln(5)
        self.set_font('Helvetica', 'I', 11)
        self.multi_cell(0, 6, authors, align='C')
        self.ln(10)

    def chapter_title(self, title):
        self.set_font('Helvetica', 'B', 12)
        self.cell(0, 10, title, align='L', new_x="LMARGIN", new_y="NEXT")
        self.ln(2)

    def chapter_body(self, body):
        self.set_font('Helvetica', '', 11)
        self.multi_cell(0, 6, body)
        self.ln(5)

pdf = PDF()
pdf.add_page()
pdf.set_auto_page_break(auto=True, margin=15)

pdf.chapter_title("RESUMEN")
pdf.chapter_body(resumen)

pdf.chapter_title("ABSTRACT")
pdf.chapter_body(abstract)

pdf.chapter_title("INTRODUCCIÓN")
pdf.chapter_body(intro)

pdf.chapter_title("METODOLOGÍA")
pdf.chapter_body(metodologia)

pdf.chapter_title("RESULTADOS Y DISCUSIÓN")
pdf.chapter_body(resultados)

pdf.chapter_title("CONCLUSIONES")
pdf.chapter_body(conclusiones)

pdf.chapter_title("BIBLIOGRAFÍA")
# Handle bibliografia items
pdf.set_font('Helvetica', '', 10)
for ref in bibliografia.split('\n'):
    pdf.multi_cell(0, 5, ref)
    pdf.ln(2)

pdf_path = "C:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\img\\investigacion\\COMPORTAMIENTO_ECONOMICO_HUMANIZADO_FINAL_PROFESIONAL.pdf"
pdf.output(pdf_path)
print(f"PDF generado exitosamente en: {pdf_path}")
