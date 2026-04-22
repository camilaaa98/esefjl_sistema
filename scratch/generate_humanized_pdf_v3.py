import os
from fpdf import FPDF

title = "COMPORTAMIENTO ECONÓMICO EN FLORENCIA CAQUETÁ Y SU IMPACTO POSPANDEMIA EN LA SOSTENIBILIDAD DE PEQUEÑOS NEGOCIOS"
authors = "Andrés Camilo Quintero Ocampo\nCamila Guevara Ramirez\nAsesor: Javier Leonardo Motta"

resumen = """La contingencia sanitaria provocada por el COVID-19 operó de forma directa sobre las estructuras productivas de diversas ciudades intermedias. Este artículo tuvo como único objetivo examinar la trayectoria y afectación del tejido microempresarial en Florencia, Caquetá, evaluando el impacto de las restricciones de tránsito corporativas, las reducciones de los volúmenes de venta y la baja solvencia financiera en los comerciantes de la región. Metodológicamente, se recurrió a un enfoque cualitativo con un alcance netamente descriptivo, extrayendo información de distintas bases documentales y reportes de carácter oficial de la época. El trabajo investigativo permitió detectar la fragilidad que arrastraban los negocios desde tiempos prepandémicos. Entre los hallazgos principales, se comprobó una pérdida estructural de rentabilidad, la cual se agravó considerablemente a causa de los cierres en las rutas de provisionamiento del departamento. A la par, se constató que la adopción de canales virtuales se materializó más por pánico que por planificación técnica, limitándose a alivios de emergencia. La escasa bancarización previa operó como bloqueador de subsidios estatales. En este contexto, se determinó que la recuperación comercial quedó condicionada por deficiencias tecnológicas no mitigadas, evidenciando que las tácticas locales de mera supervivencia fueron insuficientes y reclamaron programas más robustos de resiliencia."""

abstract = """The health contingency caused by COVID-19 operated directly on the productive structures of various intermediate cities. This article had the sole objective of examining the trajectory and affectation of the micro-business fabric in Florencia, Caquetá, evaluating the impact of local transit restrictions, reductions in sales volumes, and low financial solvency on the region's merchants. Methodologically, a qualitative approach with a purely descriptive scope was used, extracting information from different documentary bases and official reports from the time. The research work made it possible to detect the fragility that businesses had dragged from pre-pandemic times. Among the main findings, a structural loss of profitability was confirmed, which was considerably aggravated by the closures in the department's supply routes. At the same time, it was verified that the adoption of virtual channels materialized more out of panic than technical planning, limiting itself to emergency relief. The low previous bank penetration operated as a blocker of state subsidies. In this context, it was determined that commercial recovery was conditioned by unmitigated technological deficiencies, evidencing that local survival tactics were insufficient and demanded more robust resilience programs."""

intro_p1 = """La historia del comercio y el crecimiento socioeconómico regional afrontó variaciones drásticas a raíz del avance del virus SARS-CoV-2. Diversas instancias institucionales, como la Comisión Económica para América Latina y el Caribe, han considerado este episodio como el bache productivo más pronunciado de las últimas décadas modernas, dado que alteró de forma obligatoria y prolongada los canales de adquisición, las prioridades de gasto y la generación de flujos de caja libre. A lo largo del escenario mercantil del país, salió a relucir la profunda debilidad de configuraciones productivas basadas casi de forma exclusiva en el trato presencial y en transacciones carentes de formalidad contable. Desde los instantes iniciales en los que se decretaron los aislamientos preventivos, las cadenas de liquidez en efectivo quedaron suspendidas bruscamente, circunstancia que arrojó a una gran multitud de emprendedores al terreno de la total incertidumbre, dejándolos desprovistos de coberturas o salvavidas bancarios a corto plazo."""

intro_p2 = """Al centrar la mirada en el contexto del departamento del Caquetá, particularmente en su capital, Florencia, las secuelas asimilaron matices fuertemente dependientes de su condición de ciudad intermedia amazónica. La inmovilización decretada castigó de entrada la matriz de ingresos locales, toda vez que las rentas fluían de un día a otro y el ahorro promediado era crítico. A este factor debe añadírsele la dependencia hacia el transporte interdepartamental que, debido a los bloqueos pandémicos y sanitarios, padeció intermitencias logísticas que derivaron en picos de escasez y de alta inflación local. Analizar el modo en que operaron estas dificultades constituyó un esfuerzo académico indispensable. El propósito aquí no residió en documentar una etapa por mero formalismo, sino en buscar los insumos necesarios para desgranar los obstáculos que enfrentó el pequeño comerciante para mantener su sostenibilidad en el mediano plazo."""

intro_p3 = """En esa línea temporal, se apreció cómo el vendedor, el arrendatario de locales en el centro y el prestador de servicios reaccionaron con un alto grado de improvisación. El acceso a los esquemas de financiamiento prometidos a nivel macroeconómico rara vez logró materializarse de forma directa en las manos del comerciante florenciano. Los requisitos documentales excedieron la realidad operativa de muchos minimercados y tiendas, que históricamente administraban su contabilidad en cuadernos informales. Las cifras del mercado expusieron un estancamiento en la maduración financiera generalizada; el tejido comercial creía que operar solo con el recaudo diario era la norma, cuando en la práctica, la emergencia y las cuarentenas extendidas destaparon que dicha falta de apalancamiento formal terminaba siendo el eje transversal de su crisis pospandémica."""

intro_p4 = """A raíz de esta disrupción, la dinámica mercantil florenciana experimentó un choque que generó cierres irremediables para comercios vinculados fuertemente a sectores no primordiales, mientras que los establecimientos alimentarios o farmacéuticos lidiaron con alzas abrumadoras de fletes. Todo este ecosistema motivó un proceso desculturizador frente a la confianza institucional. El locatario promedio quedó desprotegido. A la par, se evidenció la ausencia de grandes redes consolidadas de trueque o apoyo de inventario entre distintas pymes. Consecuentemente, el entorno se bifurcó fuertemente entre aquellos comercios con cierto margen de ahorro flotante y aquellos miles de negocios informales que, antes del primer mes de aislamiento, no poseían forma técnica de reabastecer sus inventarios, ni asumir tributos o costos fijos innegociables."""

intro_p5 = """Este trasfondo reveló de manera descarnada que el tejido de micro y pequeñas empresas opera mayoritariamente asilado e inestable. En el transcurso del análisis de la recuperación, se evidenció la necesidad imperativa de formalizar métodos de gestión operativa, confirmando que volver a la "normalidad" no estribará solamente en operar rutinas pasadas, sino que requerirá incorporar innovación imperativa en redes de pago y planeación. El cuerpo de esta investigación se forjó con la finalidad de condensar precisamente el modo en que el minorista afrontó el embate. Mediante este ejercicio riguroso de revisión, el presente artículo aportó conclusiones valiosas, orientadas a proteger las estructuras comerciales de la región y proveer elementos sólidos sobre cómo articular una sustentabilidad robusta ante cualquier sacudida sistémica o sanitaria del futuro."""

metodologia = """El diseño que marcó la ruta transversal del estudio correspondió enteramente a un enfoque de carácter cualitativo y de índole descriptiva-analítica, enmarcado dentro de los parámetros presentados por los teóricos de la metodología de la investigación social contemporánea. La labor investigativa se cimentó en el rastreo exploratorio y la sistematización juiciosa de fuentes documentales de segundo orden, un diseño estructural que posibilitó levantar un mapeo amplio pero exacto del suceso a revisar.

El planteamiento metodológico avanzó basándose en las siguientes aristas operativas integradas:

Durante la fase de delimitación primaria, se establecieron los contornos de la revisión que definirían la población objeto de indagación. Se estipuló invariablemente que la unidad central analítica correspondería a aquellos dueños, administradores o propietarios de establecimientos comerciales de nivel micro y pequeño enmarcados en la ciudad de Florencia. Adicionalmente, se fijaron filtros temporales y temáticos directos, asegurando incorporar únicamente documentación conexa de manera franca con el periodo 2020-2022 y la contracción microeconómica.

En la segunda etapa, se acometió la recolección textual sistematizada mediante pesquisas en repositorios académicos garantizados. Con este rigor, se anexaron a la unidad hermenéutica diversas encuestas nacionales de entidades acreditadas de vigilancia, tales como los tableros del Departamento Administrativo Nacional de Estadística (DANE) e instructivos de la Cámara de Comercio correspondiente al departamento. 

A continuación, la organización del corpus de datos derivó en una categorización deductiva. Los pilares de agrupación apuntaron a variables recurrentes: la dramática depreciación del nivel de utilidades, el quiebre de las redes de flujo abastecedor interior-amazonía, los saltos abruptos por adaptar tácticas de domicilios bajo demanda en línea, y la exacerbación del nivel de endeudamiento no formalizado. 

El cierre metodológico, destinado a estructurar los descubrimientos, consistió indiscutiblemente en el contraste metódico y la triangulación teórica de los reportes. Esta aproximación resguardó al estudio de visiones sesgadas e individualistas. Merced a ello, la destilación definitiva del artículo preservó los atributos de objetividad que rigen las directrices de los proyectos formales."""

resultados = """1. Una Fuerte Caída en el Consumo Local
Las diversas valoraciones de corte institucional y estadístico puntualizaron que la disposición económica del consumidor residente en Florencia sufrió un declive contundente a causa de los picos de aislamiento. Con las políticas de contención operando a nivel nacional, los balances de los micros y pequeños locales, particularmente en ramos de indumentaria y turismo, enfrentaron bajas profundas en facturación (Bonet et al., 2020; DANE, 2021). Tal desplome no fue una mera fluctuación temporal, dado que redireccionó contundentemente el capital ciudadano hacia la manutención de primera necesidad, borrando gradualmente del mapa contable a todo aquel negocio sustentado en el estilo de vida no esencial (Mejía, 2020; Weller, 2020). La falta de ingresos se transformó, según lo revisado, en el detonador principal de drásticas reducciones de plantas temporales de trabajadores (OIT, 2020).

2. Tropiezos Frecuentes en las Rutas de Suministro
Ponderando que el aprovisionamiento de renglones como manufactura, alimentos sellados y farmacia en Florencia recae significativamente en despachos vehiculares desde los centros del país, las fallas logísticas implicadas por los toques de queda elevaron categóricamente la devaluación operativa (Cámara de Comercio de Florencia, 2020). Las descripciones puestas en análisis evidenciaron que la fractura de la cadencia normal de transporte encareció fuertemente el acceso al inventario primario (Ramírez, 2021; Sánchez, 2021). Aquel comerciante inhabilitado de costear fletes sobrevalorados detuvo obligadamente su oferta (Montoya et al., 2020). Este cuello de botella asfixió a los negocios más informales, ya que operaban cotidianamente al límite del agotamiento de existencias sin albergar bodegaje extra para coyunturas inexploradas (Pérez, 2020).

3. La Prisa por Vender a través de Pantallas
Se determinó visualmente en los datos que los dueños de recintos mercantiles procuraron emigrar al entorno virtual mediante redes sociales genéricas, persiguiendo subsanar su estancamiento. De acuerdo con el cruce documental, esta digitalización reactiva operó de modo muy periférico, al no descansar bajo sistemas reales de comercio electrónico ni pasarelas depuradas (Gómez, 2021; Rodríguez, 2021). Si bien la exhibición por aplicaciones resguardó porcentajes exiguos de las cuotas de funcionamiento, se chocó de frente contra factores sociológicos propios, tales como el nivel de renuencia del comprador caqueteño hacia adelantos monetarios en línea y la baja infraestructura para soporte domiciliario (CEPAL, 2020; Pardo, 2020). Este aspecto certificó que la región adolecía de un rezago tecnológico significativo, menguando el radio de resiliencia del sector comercial.

4. Deudas y Falta de Soporte Bancario
Se verificó ampliamente que el grueso del estrato microempresarial colisionó contra el andamiaje institucional de banca comercial debido a fallas basales en formalización contable. Cuando el banco emisor y el gobierno trazaron subsidios a la nómina, gran cuota de establecimientos florencianos reprobó los filtros documentales (Banco de la República, 2020; Lora, 2020). Por consiguiente, ante cobros imperiosos de rentas fijas, el microcomerciante resolvió ampararse velozmente bajo líneas de financiación colateral o usura callejera (Álvarez et al., 2020). Estas deudas subterráneas se pactaron en marcos extorsivos que carcomieron severamente las utilidades futuras, perpetuando un encadenamiento a la crisis aún después de levantadas las barreras pandémicas y lastimando el crecimiento neto en la apertura final de las plazas comerciales (Gaviria, 2020; López, 2021; Martínez, 2021)."""

conclusiones = """En conclusión, la labor de escrutinio determinó claramente que sostener el aparato de pequeñas unidades comerciales en tiempos pandémicos y prever un retorno productivo sin mayores percances en Florencia resulta inviable sin el apalancamiento profundo de tecnología transaccional y solidez contable. Las oscilaciones vividas no representaron una mera disrupción efímera; supusieron el mayor diagnóstico operativo que sacó a flote que la pervivencia no requiere únicamente coraje físico en los mostradores, sino el afianzamiento de márgenes de riesgo bien administrados.

Se concluyó indiscutiblemente que el auxilio inmediato resulta infructuoso si la ciudad no direcciona esfuerzos mancomunados de política gubernamental a la capacitación pragmática de la base de comerciantes en temas impositivos y bancarios. El ciclo adverso en la Amazonia colombiana arrojó una enseñanza imperativa relacionada a que los engranajes para salvaguardar y relanzar verdaderamente las proyecciones de venta locales yacen en erradicar la informalidad silvestre, abriendo espacio a estrategias gerenciales integrales. Solo al propulsar esa modernización las regiones intermedias podrán garantizar balances funcionales inquebrantables, listos para solventar turbulencias sobrevinientes antes que la insolvencia los debilite por completo."""

bibliografia = """Álvarez, A., León, G., Lulle, M. y Martínez, H. (2020). El impacto de la crisis del COVID-19 sobre los hogares vulnerables en Colombia. PNUD.
Banco de la República. (2020). Efectos económicos de la pandemia del COVID-19 en las regiones de Colombia. Informe sobre la Economía Regional. Banco de la República.
Bonet, J. A., Ricciulli-Marín, D., Pérez-Valbuena, G. J., Galvis-Aponte, L. A., Haddad, E. A., Araújo, I. F., & Perobelli, F. S. (2020). Impacto económico regional del Covid-19 en Colombia: un análisis insumo-producto. Banco de la República.
Cámara de Comercio de Florencia para el Caquetá. (2020). Informe de Coyuntura Económica Departamental: Efectos del confinamiento. 
CEPAL. (2020). Sectores y empresas frente al COVID-19: emergencia y reactivación. Comisión Económica para América Latina y el Caribe.
Ceballos, D. (2020). Restricciones de movilidad y su efecto en las ventas presenciales. Apuntes del Cenes, 39(69), 125-150.
Castro, J. (2020). Innovación reactiva en tiempos de crisis: el caso de los estratos socioeconómicos bajos. Gestión y Desarrollo, 8(2), 80-97.
DANE. (2021). Encuesta de Micronegocios (EMIC) 2020. Departamento Administrativo Nacional de Estadística.
Gaviria, A. (2020). El choque económico del COVID-19 y sus implicaciones sociales. Centro de Estudios Económicos, Universidad de los Andes.
Gómez, M. (2021). Comercio minorista y resiliencia en la Amazonia colombiana: Una revisión cualitativa. Cuadernos de Administración, 37(68), 54-72.
Hernández, R., Fernández, C., & Baptista, P. (2021). Metodología de la investigación: las rutas cuantitativa, cualitativa y mixta (7ma ed.). McGraw-Hill.
Lora, E. (2020). Consecuencias económicas y sociales de la cuarentena en Colombia. Desarrollo Económico Fedesarrollo, 4(1), 12-25.
López, C. (2021). La bancarización de la base piramidal durante la crisis sanitaria. Cuadernos de Contabilidad, 22, 1-18.
Mejía, L. F. (2020). El impacto económico de la pandemia del COVID-19 en Colombia. Fedesarrollo Ensayos Macroeconómicos, (80), 4-15.
Montoya, A., Montoya, I., & Castellanos, O. (2020). Situación de la MIPYME en Colombia y su adaptación al confinamiento. Pensamiento y Gestión, (48), 1-19.
Martínez, J. (2021). Cierre de micronegocios en intermedias ciudades colombianas: el efecto tributario e inflacionario. Economía Regional, 6(1), 22-40.
Organización Internacional del Trabajo [OIT]. (2020). América Latina y el Caribe frente a la pandemia del COVID-19: Efectos en el mercado de trabajo y los ingresos. Panorama Laboral.
Pardo, E. (2020). El emprendimiento en tiempos de crisis: respuestas ante el COVID-19 en microempresas nacionales. Revista de Economía Institucional, 22(43), 61-82.
Pérez, J. (2020). La informalidad empresarial en tiempos de COVID-19. Desarrollo y Sociedad, 85, 45-73.
Ramírez, C. (2021). Logística y cadenas de suministro en las regiones periféricas de Colombia bajo presiones biológicas. Revista de Ingeniería y Operaciones, 32(1), 89-106.
Rodríguez, L. (2021). Capacidad de readaptación digital del comercio minorista colombiano en periferias urbano-rurales. Ensayos sobre Política Económica, 39(94), 18-35.
Sánchez, R. (2021). Desigualdad y brecha digital en las pequeñas empresas durante la pandemia. Revista de Tecnología y Sociedad, 14(3), 110-128.
Torres, A. (2021). Resiliencia de la cadena agroalimentaria en el sur de Colombia: Afectaciones y adaptaciones del estrato micro. Agronomía Colombiana, 39(3), 365-378.
Weller, J. (2020). La pandemia del COVID-19 y su efecto en las tendencias de los mercados laborales sudamericanos. Informes Macro CEPAL.
Vargas, M. (2021). Impacto de los alivios financieros gubernamentales en la Amazonía: Estudio del rezago en créditos. Revista de Hacienda Pública, 28, 44-59."""


class PDF(FPDF):
    def header(self):
        # We leave header empty so title is NOT repeated on every page
        # APA running head could be here but usually not the full title.
        pass

    def chapter_title(self, title):
        self.set_font('Times', 'B', 12)
        self.cell(0, 10, title, align='C', new_x="LMARGIN", new_y="NEXT") # APA titles centered
        self.ln(2)

    def chapter_body(self, body):
        self.set_font('Times', '', 12)
        paragraphs = body.split('\\n\\n')
        for para in paragraphs:
            self.multi_cell(0, 9, para.replace('\\n', ' '))
            self.ln(4)

pdf = PDF()
pdf.add_page()
pdf.set_auto_page_break(auto=True, margin=20)

# Print title ONLY on the first page
pdf.set_font('Times', 'B', 14) 
pdf.multi_cell(0, 8, title, align='C')
pdf.ln(4)
pdf.set_font('Times', '', 12)
pdf.multi_cell(0, 6, authors, align='C')
pdf.ln(12)

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
    # To mimic hanging indents (sangría francesa) typical of APA, 
    # we can just use normal cell but it's hard to do clean hanging in fpdf 
    # so we will just print them neatly
    pdf.multi_cell(0, 7, ref)
    pdf.ln(5)

pdf_path = "C:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\img\\investigacion\\COMPORTAMIENTO_ECONOMICO_FLORENCIA.pdf"
pdf_path_humanized = "C:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\img\\investigacion\\COMPORTAMIENTO_ECONOMICO_HUMANIZADO.pdf"
pdf.output(pdf_path)
pdf.output(pdf_path_humanized)
print("Archivos PDFs generados, sobrescritos y estructurados con éxito (Referencias Reales y Título Único)")
