<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Relaciona cada materia con las carreras en cuya malla figura (una materia puede estar en varias carreras), para que
 * al cargar una actividad de extensión la lista de materias se acote a las carreras elegidas. Las relaciones salen de
 * las mallas curriculares de cada carrera; se pueden editar en Extensión > Parámetros > Materias.
 *
 * Idempotente: crea la tabla una sola vez y no repite una relación existente.
 */
return new class extends Migration
{
    /** carrera (nombre_fantasia) => materias de su malla */
    private const MALLAS = [
        'Ciencias de la Educación' => [
            'Bases biológicas de la Educación', 'Comunicación Oral y Escrita', 'Curriculum Educacional I',
            'Currículum Educacional', 'Desarrollo Personal y Social', 'Didáctica Específica I',
            'Didáctica Específica II', 'Didáctica Específica II Educ Superior y Formación Docente', 'Didáctica General',
            'Educación Bilingüe', 'Educación Comparada', 'Educación Inclusiva',
            'Educación Permanente', 'Estadística Educacional', 'Evaluación Institucional',
            'Evaluación del Aprendizaje', 'Filosofía y Ética de la Educación', 'Fundamentos Biológicos de la Educación',
            'Gestión Administrativa y Financiera', 'Gestión Organizacional y Estructural', 'Gestión Pedagógica Institucional',
            'Gestión de la vinculación social', 'Historia de la Educación', 'Legislación Educacional y Ética',
            'Lengua Española', 'Lengua Extranjera: Inglés', 'Lengua Guaraní',
            'Lengua y Cultura Japonesa', 'Lógica', 'Matemática',
            'Metodología de la Investigación Científica', 'Metodología del Trabajo Intelectual', 'Orientación Educacional',
            'Pedagogía General', 'Política Educativa', 'Proyecto Educativo',
            'Práctica Profesional Supervisada', 'Psicología General y Evolutiva', 'Psicología Social',
            'Psicología de la Educación', 'Realidad Social, Cultural y Educativa del Paraguay', 'Seminario I',
            'Seminario II', 'Sociología y Antropología de la Educación', 'TICs (Tecnologías de la Información y la Comunicación)',
            'Taller de Tesina I', 'Tecnología Educativa', 'Tutoría de Tesina II - Elaboración y Defensa de Tesina',
            'Ética Profesional',
        ],
        'Psicología' => [
            'Antropología Social y Cultural', 'Bases Biológicas del Comportamiento', 'Comunicación Oral y Escrita',
            'Desarrollo Personal y Social', 'Elaboración de Informe Psicológico', 'Entrevista Psicológica',
            'Estadística Descriptiva', 'Estadística Inferencial', 'Filosofía y Lógica',
            'Lengua Guaraní', 'Lengua Inglesa', 'Lengua y Cultura Japonesa',
            'Metodología de la Investigación Científica', 'Metodología: Técnicas de Trabajo Intelectual', 'Neurobiología',
            'Neuropsicología', 'Pasantía y Práctica Profesional I', 'Pasantía y Práctica Profesional II',
            'Psicofarmacología I', 'Psicofarmacología II', 'Psicología Clínica I',
            'Psicología Clínica II', 'Psicología Diferencial', 'Psicología Evolutiva',
            'Psicología Experimental', 'Psicología General', 'Psicología Social',
            'Psicología de la Personalidad', 'Psicología del Aprendizaje', 'Psicometría',
            'Psicopatología I', 'Psicopatología II', 'Realidad Social, Cultural y Educativa del Paraguay',
            'Salud y Enfermedad', 'Sociología General', 'Tecnología de la Información y la Comunicación (TIC)',
            'Teorías y Escuelas Psicológicas', 'Teorías y Técnicas Exploratoria I', 'Teorías y Técnicas Exploratoria II',
            'Teorías y Técnicas Exploratoria III', 'Teorías y Técnicas Exploratorias', 'Teorías y Técnicas Exploratorias IV',
            'Trastornos Psicopatológico de la Adolescencia', 'Trastornos Psicopatológico de la Niñez', 'Tutoría de Trabajo de Grado I',
            'Tutoría de Trabajo de Grado II', 'Técnica Proyectivas Profundas I', 'Técnica Proyectivas Profundas II',
            'Técnicas Psicoterapéuticas I', 'Técnicas Psicoterapéuticas II', 'Ética Profesional',
        ],
        'Ciencias del Deporte' => [
            'Acondicionamiento Físico - Fuerza y Musculación', 'Actividades Físicas y Deportivas para Grupos Especiales', 'Anatomía Aplicada al Deporte',
            'Antropometría y Biomecánica Deportiva', 'Bioquímica y Nutrición Deportiva', 'Comunicación Oral y Escrita',
            'Control Biomédico y Fisioterapia Deportiva', 'Currículo y Planificación Curricular', 'Deporte: Atletismo',
            'Deporte: Baloncesto y Voleibol', 'Deporte: Futbol/Futsal', 'Deporte: Hándbol',
            'Deporte: Natación', 'Deportes Olímpicos Individuales', 'Deportes Olímpicos de Combate',
            'Desarrollo Personal', 'Elaboración y Gestión de Proyectos Deportivos', 'Electiva I (Evaluación del Aprendizaje)',
            'Electiva II (Identificación, Selección y Desarrollo de Talentos)', 'Electiva III (KARATE)', 'Electiva IV (Actividad Física de tiempo libre y en contacto con la naturaleza)',
            'Estadística Aplicada al Deporte', 'Filosofía de la Educación', 'Filosofía y Sociología de la Educación',
            'Fisiología del Ejercicio', 'Fundamentación Metodológica y Planificación del Entrenamiento Deportivo', 'Gimnasia Artística y Aeróbica',
            'Gimnasia Básica Educación Rítmica y Expresión Corporal', 'Introducción a las Tecnologías de Información y las Comunicación', 'Juegos Tradicionales e Inclusivos y Recreación',
            'Legislación Deportiva', 'Legislación y Ética Profesional Deportiva', 'Lengua Extranjera: Inglés',
            'Lengua Guaraní', 'Lengua y Cultura Japonesa', 'Medicina Deportiva y Primeros Auxilios',
            'Metodología de la Investigación', 'Pedagogía y Didáctica General', 'Práctica Profesional Supervisada Deportiva I',
            'Práctica Profesional Supervisada Deportiva II', 'Práctica Profesional Supervisada a la Educación Física I (Educación Física – Nivel Inicial y EEB)', 'Práctica Profesional Supervisada a la Educación Física II',
            'Psicología Deportiva', 'Psicología General y Evolutiva', 'Psicomotricidad',
            'Taller de Trabajo Final de Grado (TFG)', 'Teoría y Metodología de la Educación Física y Didáctica de la Educación Física', 'Trabajo Final de Grado (TFG)',
            'Ética Profesional',
        ],
        'Educación Inicial' => [
            'Antropología Social y Cultural', 'Articulación entre el Primer Grado y el Preescolar', 'Comunicación Oral y Escrita: Castellano',
            'Comunicación Oral y Escrita: Guaraní', 'Cuidado y Promoción de la Salud del Infante', 'Desarrollo y Prospectiva de la Educación Inicial',
            'Didáctica General', 'Didáctica de la Lengua y la Literatura Infantil I', 'Didáctica de la Lengua y la Literatura Infantil II',
            'Didáctica de las Matemáticas en Educación Inicial', 'Educación Intercultural Bilingüe', 'Estadística Descriptiva',
            'Filosofía de la Educación', 'Fundamentos Biofisiológicos del Desarrollo del Aprendizaje', 'Fundamentos Biológicos de la Educación',
            'Fundamentos Curriculares y de la Evaluación', 'Fundamentos Teóricos de la Educación y la Enseñanza', 'Fundamentos de la Psicología General',
            'Gestión de la Implementación Curricular en la Educación Inicial', 'Gestión de la Vinculación Social, Comunitaria y Redes de Apoyo en Educación Inicial', 'Historia de la Política y de la Legislación Educativa en la Educación Inicial',
            'Inclusión y Diversidad en Educación Inicial', 'Lengua Extranjera: Inglés', 'Lengua Inglesa',
            'Lengua y Cultura Japonesa', 'Lenguajes Expresivos I (Expresión Musical)', 'Lenguajes Expresivos II (Expresión Corporal)',
            'Lenguajes Expresivos III (Expresión Plástica)', 'Metodología de la Investigación Científica I', 'Metodología de la Investigación Científica II',
            'Metodología de la Investigación Educativa', 'Metodología de la Investigación en Educación Inicial', 'Metodología del Trabajo Intelectual',
            'Modelos Psicopedagógicos de la Atención Integral a la Primera Infancia', 'Neurociencia y Educación Temprana', 'Orientación Educacional',
            'Pasantía Técnico - Pedagógica I', 'Pasantía Técnico - Pedagógica II', 'Pasantía Técnico - Pedagógica III',
            'Pasantía Técnico - Pedagógica Práctica Profesional IV', 'Pedagogía General', 'Proyecto Integrado de Saberes y Experiencias',
            'Psicología Evolutiva', 'Psicología de la Educación', 'Psicología del Desarrollo y del Aprendizaje I',
            'Psicología del Desarrollo y del Aprendizaje II', 'Psicomotricidad I', 'Psicomotricidad II',
            'Seminario Temático', 'Sociología de la educación', 'Sociología y Antropología de la Educación',
            'Taller Vivencial I', 'Taller Vivencial II', 'Taller Vivencial III',
            'Taller de Tesina I', 'Taller de Tesina II', 'Tecnología Educativa como Recurso Didáctico en Educación Inicial',
            'Tutoría de Tesina',
        ],
        'Educación Escolar Básica' => [
            'Antropología Social y Cultural', 'Antropología de la Educación', 'Articulación Pedagógica didáctica',
            'Castellano', 'Currículum Educacional', 'Desarrollo Personal y Orientación Vocacional',
            'Didáctica General', 'Educación Bilingüe', 'Educación Inclusiva',
            'Elaboración de informe final', 'Estadística', 'Evaluación de los Aprendizajes',
            'Filosofía de la Educación', 'Fundamentos Biológicos de la Educación', 'Gestión y Administración Educativa',
            'Guaraní', 'Legislación y Sistema Educativo Paraguayo', 'Lengua Extranjera',
            'Matemática', 'Metodología de la Investigación II', 'Metodología de la Investigación III (Taller de tesis)',
            'Metodología de la investigación I', 'Optativa 1', 'Optativa 2',
            'Optativa 3', 'Optativa 4', 'Pedagogía General',
            'Práctica 1', 'Práctica 2', 'Práctica 3',
            'Práctica 4', 'Práctica 5', 'Práctica en instituciones educativas',
            'Psicología Evolutiva', 'Psicología de la Educación', 'Sociología de la educación',
            'Teoría de la Educación', 'Teoría y Didáctica de la Matemática', 'Teoría y didáctica de Trabajo y Tecnología',
            'Teoría y didáctica de la Educ. para la Salud', 'Teoría y didáctica de la Educación Artística', 'Teoría y didáctica de la Educación Física',
            'Teoría y didáctica de la Lengua Castellana', 'Teoría y didáctica de la Lengua Guaraní', 'Teoría y didáctica de las Ciencias Naturales',
            'Teoría y didáctica de las Ciencias Sociales', 'Teoría y didáctica del Des. Pers. y Profesional', 'Tutoría 1',
            'Tutoría 2', 'Ética profesional del docente',
        ],
        'Derecho' => [
            'Científica I', 'Científica II', 'Comunicación Oral y Escrita',
            'Consultorio Jurídico', 'Criminología', 'Deontología Jurídica',
            'Derecho Administrativo', 'Derecho Aeronáutico', 'Derecho Agrario y Ambiental',
            'Derecho Civil - Personas', 'Derecho Civil - Reales y Cosas', 'Derecho Civil – Contratos',
            'Derecho Civil – Hechos y Actos Jurídicos', 'Derecho Civil – Obligaciones', 'Derecho Civil – Sucesiones',
            'Derecho Constitucional', 'Derecho Cooperativo', 'Derecho Electoral',
            'Derecho Informático', 'Derecho Internacional Privado', 'Derecho Internacional Público',
            'Derecho Marítimo', 'Derecho Mercantil I', 'Derecho Mercantil II',
            'Derecho Penal I', 'Derecho Penal II', 'Derecho Político',
            'Derecho Procesal Civil I', 'Derecho Procesal Civil II', 'Derecho Procesal Laboral',
            'Derecho Procesal Penal', 'Derecho Romano I', 'Derecho Romano II',
            'Derecho Tributario', 'Derecho de la Niñez y la Adolescencia', 'Derecho del Trabajo y Seguridad Social',
            'Derechos Humanos', 'Derechos Intelectuales', 'Derechos de la Integración',
            'Economía Política', 'Filosofía del Derecho', 'Finanzas Publicas',
            'Guaraní Jurídico Paraguayo', 'Historia Diplomática del Paraguay', 'Idioma Extranjero - Lengua y Cultura Japonesa',
            'Idioma Extranjero - Portugués', 'Introducción a las Ciencias Jurídicas', 'Legislación Deportiva',
            'Lógica Jurídica', 'Medicina Legal', 'Medios Alternativos de Resolución de Conflictos',
            'Metodología de la Investigación', 'Quiebras', 'Sociología jurídica',
            'Técnica Jurídica: Redacción de Escritos y Análisis de Jurisprudencia', 'Técnica Legislativa', 'Técnica de Litigación Adversarial',
            'Ética Profesional',
        ],
        'Notariado' => [
            'Actuación Notarial Administrativa y Judicial', 'Clínica Notarial', 'Derecho Administrativo',
            'Derecho Agrario y Ambiental', 'Derecho Civil - Personas', 'Derecho Civil - Reales y Cosas',
            'Derecho Civil – Contratos', 'Derecho Civil – Hechos y Actos Jurídicos', 'Derecho Civil – Obligaciones',
            'Derecho Civil – Sucesiones', 'Derecho Constitucional', 'Derecho Internacional Privado',
            'Derecho Mercantil I', 'Derecho Mercantil II', 'Derecho Notarial',
            'Derecho Penal I', 'Derecho Penal II', 'Derecho Procesal Civil I',
            'Derecho Procesal Civil II', 'Derecho Procesal Laboral', 'Derecho Procesal Penal',
            'Derecho Registral', 'Derecho Romano I', 'Derecho Romano II',
            'Derecho Tributario', 'Derecho de la Niñez y la Adolescencia', 'Derecho del Trabajo y Seguridad Social',
            'Derechos Intelectuales', 'Economía Política', 'Filosofía del Derecho',
            'Finanzas Publicas', 'Guaraní Jurídico Paraguayo', 'Informática, Expresión y Redacción Notarial',
            'Introducción a las Ciencias Jurídicas', 'Introducción al estudio del Derecho Notarial I', 'Lógica Jurídica',
            'Metodología de la Investigación Científica I', 'Metodología de la Investigación Científica II', 'Pasantía Profesional Supervisada',
            'Quiebras', 'Técnica Notarial', 'Ética Notarial',
        ],
        'Ingeniería en Informática' => [
            'Administración y Sistemas de Redes', 'Algebra', 'Algoritmia y Estructura de Datos I',
            'Algoritmia y Estructura de Datos II', 'Análisis y Proyectos de los Sistemas', 'Arquitectura de Máquina I',
            'Arquitectura de Máquina II', 'Auditoría Informática', 'Base de Dato I',
            'Base de dato II', 'Calculo Diferencial', 'Computación Gráfica y Programación Web',
            'Comunicación Oral y Escrita', 'Contabilidad General', 'Cálculo Integral',
            'Derecho Informático', 'Economía y Gestión', 'Electrónica Digital',
            'Emprendedurismo y Liderazgo', 'Física I', 'Física II',
            'Geometría Analítica', 'Gerencia de Proyectos en Informática', 'Ingeniería Legal',
            'Ingeniería del Software I', 'Ingeniería del Software II', 'Inglés Técnico',
            'Inteligencia Artificial', 'Introducción a la Informática', 'Investigación de Operaciones I',
            'Investigación de Operaciones II', 'Lenguaje de Programación I', 'Lenguaje de Programación II',
            'Lenguaje de Programación III', 'Lenguaje de Programación IV', 'Lenguaje de Programación V',
            'Marketing', 'Matemática Discreta', 'Metodología de la Investigación',
            'Metodología del Desarrollo de Software', 'Probabilidades y Estadística', 'Proyecto de Base de Datos',
            'Proyecto de fin de carrera', 'Redes I', 'Redes II',
            'Robótica', 'Seguridad Informática', 'Simulación y Animación Asistida por Computadoras',
            'Sistema Orientado Objeto I', 'Sistema Orientado Objeto II', 'Sistema distribuido',
            'Sistemas Operativos', 'Técnica de Control y Calidad de Software',
        ],
        'Ingeniería Electromecánica' => [
            'Administración y Mantenimiento Industrial', 'Adquisición y Procesamiento de Datos', 'Aire Acondicionado y Refrigeración',
            'Algebra', 'Algebra Lineal', 'Análisis Numérico',
            'Análisis Vectorial', 'Análisis de Circuitos Eléctricos I', 'Análisis de Circuitos Eléctricos II',
            'Análisis de elementos finitos', 'Calculo Diferencial', 'Centrales Eléctricas',
            'Comunicación Oral y Escrita', 'Cálculo Integral', 'Dibujo Técnico',
            'Dinámica de Máquinas y vibraciones', 'Diseño asistido por Computadora', 'Economía y Evaluación de Proyectos',
            'Ecuaciones Diferenciales', 'Electrónica Básica', 'Elementos de Máquinas',
            'Física I', 'Física II', 'Física III',
            'Geometría', 'Geometría Analítica', 'Gestión de Calidad',
            'Higiene, Seguridad e Ingeniería Ambiental', 'Informática', 'Ingeniería Legal',
            'Inglés Técnico', 'Instalaciones Eléctricas', 'Instalaciones Industriales',
            'Introducción a la Dinámica de fluidos computacional', 'Mecánica Vectorial I', 'Mecánica Vectorial II',
            'Mecánica de Fluidos', 'Máquinas Eléctricas I', 'Máquinas Eléctricas II',
            'Máquinas Hidráulicas', 'Máquinas Térmicas I', 'Máquinas Térmicas II',
            'Optativa I - Máquinas Eléctricas II (Soldadura)', 'Optativa II - Instalaciones Eléctricas Industriales', 'Optativa III - Aire Acondicionado y refrigeración',
            'Organización Industrial', 'Pasantías Supervisadas', 'Probabilidad y Estadística',
            'Química General', 'Resistencia de Materiales I', 'Resistencia de Materiales II',
            'Sistemas de Control', 'Subestaciones Eléctricas', 'Tecnología Mecánica I',
            'Tecnología Mecánica II', 'Tecnología de los Materiales', 'Termodinámica',
            'Trabajo Final de Grado', 'Transferencia de Calor', 'Transmisión y Distribución de EE',
            'Trigonometría',
        ],
        'Diseño Gráfico' => [
            'Antropología Simbólica', 'Book ´s y Catalogos', 'Comunicación EN EL ARTE',
            'Deontología', 'Desarrollo Personal', 'Dibujo Publicitario I',
            'Dibujo Publicitario II', 'Dibujo Técnico', 'Diseño Computarizado II',
            'Diseño Computarizado III', 'Diseño Publicitario', 'Diseño Web I',
            'Diseño Web II', 'Diseño Web III', 'Diseño Web IV',
            'Diseño computarizado I', 'Diseño e Imagen Empresarial', 'Diseño y proyecto de Investigación',
            'Edición de Audio I', 'Edición de Audio II', 'Emprendedurismo y liderazgo empresarial',
            'Estética y creatividad', 'Expresión gráfica', 'Fotodiseño Stop Motion',
            'Fotografía Digital I', 'Fotografía Digital II', 'Historia del Arte I',
            'Historia del Arte II', 'Historia del diseño', 'Informatica Aplicada',
            'Laboratorio experimental de morfología', 'Maqueteria I', 'Mercadotecnia',
            'Metodología de la investigación social', 'Morfologia del diseño', 'Packaging',
            'Poducción Audiovisual III', 'Practica profesional I', 'Producción Audiovisual I',
            'Producción Audiovisual II', 'Producción Gráfica', 'Práctica Profesional II',
            'Taller de Diseño III', 'Taller de diseño I', 'Taller de diseño II',
            'Taller de la Estampa I', 'Taller de la Estampa II', 'Taller de la Estampa III',
            'Taller de la Estampa IV', 'Técnicas Gráficas',
        ],
        'Ingeniería Agronómica' => [
            'Administración Rural', 'Administración y legislación', 'Agricultura alternativa',
            'Agroindustrias', 'Anteproyecto de Tesis', 'Análisis financiero de proyectos',
            'Biología', 'Bioquímica', 'Biotecnología agrícola',
            'Botánica general', 'Botánica sistemática', 'Climas y suelos',
            'Comercialización Agrícola', 'Comunicación Oral y Escrita', 'Conservación de recursos naturales',
            'Construcciones rurales', 'Ecología', 'Economía',
            'Emprendimiento Empresarial Rural', 'Entomología', 'Equipos y maquinaria agrícolas',
            'Estadística', 'Fertilidad y manejo de suelo', 'Fisiología vegetal',
            'Fitopatología', 'Fruticultura', 'Física aplicada',
            'Genética', 'Horticultura', 'Informática',
            'Ingeniería rural', 'Inglés Técnico', 'Introducción a la Agronomía',
            'Matemática', 'Metodología de la Investigación', 'Microbiología agrícola',
            'Métodos estadísticos', 'Pasantía laboral', 'Pasantía rural',
            'Pasantía rural agrícola', 'Pasturas y forrajes', 'Patología animal',
            'Producción agrícola', 'Producción y análisis de semillas', 'Protección vegetal',
            'Proyectos educativos (Estructura y funcionamiento de mercado agrícola)', 'Química', 'Química aplicada',
            'Recursos hídricos y métodos de riegos', 'Silvicultura', 'Tecnología de productos agrícolas',
            'Tesis', 'Topografía', 'Zoología',
            'Zootecnia', 'Ética Profesional',
        ],
        'Ciencias Contables' => [
            'Administración I - Diseño Organizacional', 'Administración II - Planificación Estratégica', 'Administración III - Organización y Método',
            'Administración IV - Talento Humano', 'Administración Presupuestaria', 'Auditoria Gubernamental',
            'Auditoria I - Introducción a la Auditoria', 'Auditoria II - El Control Interno', 'Auditoria III - Administración de la Auditoria',
            'Auditoría Informática', 'Comercio Exterior', 'Comunicación Oral y Escrita',
            'Contabilidad Gubernamental', 'Contabilidad I - Contabilidad General', 'Contabilidad II - Contabilidad Financiera.',
            'Contabilidad III - Contabilidad de Costos', 'Contabilidad IV - Los Estados Contables', 'Contabilidad V - Bancos y Seguros',
            'Contabilidad VI - Contabilidad de Gestión', 'Cultura y Lengua Japonesa', 'Derecho Civil y Comercial',
            'Elaboración y Evaluación de Proyectos', 'Estadística', 'Finanzas I - Administración Financiera',
            'Finanzas II - Diagnostico y Planif. Financiera.', 'Guaraní', 'Informática I',
            'Informática II - Contabilidad Excel', 'Inglés Técnico I', 'Inglés Técnico II',
            'Introducción a la Administración', 'Introducción a la Economía', 'Legislación Bancaria',
            'Legislación Laboral', 'Legislación Tributaria', 'Macroeconomía',
            'Marketing I - Comercialización', 'Marketing II - Marketing Estratégico', 'Marketing III - Investigación y Desarrollo de Mercados',
            'Matemática Financiera', 'Matemática I - Algebra', 'Matemática II - Analítica y Cálculo',
            'Matemática III', 'Matemática IV', 'Mercado de Capitales y Dinero',
            'Mercadotecnia', 'Metodología de la Investigación', 'Microeconomía',
            'Portugués', 'Quiebras', 'Régimen Legal de Empresas',
            'Sistema de Información Gerencial', 'Sociología', 'Tecnologías de la Información y la Comunicación',
            'Teoría de la Administración', 'Trabajo de Grado I', 'Ética Profesional',
        ],
        'Administración de Empresas' => [
            'Administración Financiera, Dirección y Control', 'Administración I - Diseño Organizacional', 'Administración II - Planificación Estratégica',
            'Administración III - Organización y Método', 'Administración IV - Talento Humano', 'Administración Pública',
            'Auditoria de Gestión Administrativa', 'Auditoría Financiera', 'Comercio Exterior',
            'Comportamiento del Consumidor', 'Comunicación Oral y Escrita', 'Contabilidad I - Contabilidad General',
            'Contabilidad II - Contabilidad Financiera.', 'Costos I - Costos y Presupuestos', 'Costos II - Contabilidad Costos',
            'Cultura y Lengua Japonesa', 'Derecho Civil y Comercial', 'Derecho del Trabajo y Seguridad Social',
            'Economía Internacional', 'Elaboración y Evaluación de Proyectos', 'Estadística',
            'Finanzas I - Administración Financiera', 'Finanzas II - Diagnóstico y Planificación Financiera.', 'Finanzas III - Finanzas Internacionales',
            'Geografía Económica', 'Gestión de Operaciones', 'Guaraní',
            'Informática I', 'Informática II - Contabilidad Excel', 'Inglés Técnico I',
            'Inglés Técnico II', 'Introducción a la Administración', 'Introducción a la Economía',
            'Legislación Tributaria', 'Macroeconomía II', 'Marketing I - Comercialización',
            'Marketing II - Marketing Estratégico', 'Marketing III - Investigación y Desarrollo de Mercados', 'Marketing IV - Desarrollo de Productos',
            'Matemática Financiera', 'Matemática I - Algebra', 'Matemática II - Analítica y Cálculo',
            'Matemática III', 'Matemática IV', 'Mercado de Capitales y Dinero',
            'Metodología de la Investigación', 'Microeconomía', 'Portugués',
            'Psicología Organizacional', 'PyMES y Empresas Familiares', 'Quiebras',
            'Realidad Social, Cultural y Educativa del Paraguay', 'Sistema de Información Gerencial', 'Sociología',
            'Taller de Casos Prácticos', 'Tecnologías de la Información y Comunicación', 'Trabajo de Grado I',
            'Ética Profesional',
        ],
        'Ingeniería Comercial' => [
            'Administración I - Diseño Organizacional', 'Administración II - Planificación Estratégica', 'Administración III - Organización y Método',
            'Administración IV - Recursos Humanos', 'Administración Pública', 'Administración V - Dirección y Control',
            'Auditoría Financiera', 'Comercio Exterior', 'Comunicación Oral y Escrita',
            'Contabilidad I - Contabilidad General', 'Contabilidad II - Contabilidad Financiera.', 'Costos I - Costos y Presupuestos',
            'Costos II - Contabilidad de Costos', 'Cultura y Lengua Japonesa', 'Derecho Civil y Comercial',
            'Econometría', 'Economía Internacional', 'Elaboración y Evaluación de Proyectos',
            'Estadística I', 'Estadística II', 'Finanzas I - Adm. Financiera',
            'Finanzas II - Diagnostico y Planif. Financiera.', 'Finanzas III - Decisiones Financieras', 'Finanzas IV - Finanzas Corporativas',
            'Geografía Económica', 'Gestión de Operaciones', 'Gestión de la Calidad',
            'Guaraní', 'Informática I', 'Informática II - Contabilidad Excel',
            'Inglés Técnico I', 'Inglés Técnico II', 'Introducción a la Administración',
            'Introducción a la Economía', 'Legislación Bancaria', 'Legislación Tributaria',
            'Macroeconomía', 'Marketing I - Comercialización', 'Marketing II - Marketing Estratégico',
            'Marketing III - Investigación y Desarrollo de Mercados', 'Marketing IV - Desarrollo de Productos', 'Marketing Internacional',
            'Marketing V - Precios y Distrib. Canales', 'Matemática Financiera', 'Matemática I - Algebra',
            'Matemática II - Analítica y Cálculo', 'Matemática III', 'Matemática IV - Integrales',
            'Metodología de la Investigación', 'Microeconomía', 'Portugués',
            'Psicología Organizacional', 'Quiebras', 'Realidad Social, Cultural y Educativa del Paraguay',
            'Régimen Legal de Empresas', 'Sistema de Información Gerencial', 'Sociología',
            'Tecnología de la Información y la Comunicación', 'Ética Profesional',
        ],
    ];

    public function up(): void
    {
        if (!Schema::hasTable('carreras_materias')) {
            Schema::create('carreras_materias', function (Blueprint $table) {
                $table->id();
                $table->foreignId('carrera_id')->constrained('carreras')->cascadeOnDelete();
                $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
                $table->unique(['carrera_id', 'materia_id']);
            });
        }

        $carreras = DB::table('carreras')->get();
        $materias = DB::table('materias')->get()->mapWithKeys(fn ($m) => [$this->clave($m->nombre_fantasia) => $m->id]);
        $existentes = DB::table('carreras_materias')->get()->mapWithKeys(fn ($r) => [$r->carrera_id . '-' . $r->materia_id => true]);

        foreach (self::MALLAS as $nombreCarrera => $nombresMaterias) {
            $carrera = $carreras->first(fn ($c) => $this->clave($c->nombre_fantasia) === $this->clave($nombreCarrera));
            if (!$carrera) {
                continue;
            }
            $filas = [];
            foreach ($nombresMaterias as $nombreMateria) {
                $materiaId = $materias[$this->clave($nombreMateria)] ?? null;
                if ($materiaId && !isset($existentes[$carrera->id . '-' . $materiaId])) {
                    $filas[] = ['carrera_id' => $carrera->id, 'materia_id' => $materiaId];
                }
            }
            foreach (array_chunk($filas, 200) as $trozo) {
                DB::table('carreras_materias')->insert($trozo);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('carreras_materias');
    }

    private function clave(?string $texto): string
    {
        return preg_replace('/[^a-z0-9]/', '', Str::lower(Str::ascii((string) $texto)));
    }
};
