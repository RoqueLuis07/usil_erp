<?php

/*
 * Identidad de la institución para los informes/PDF. No depende de la tabla
 * `empresas` (que hoy está vacía en producción) ni de archivos en storage
 * (que no viajan con el código): el logo vive en public/images.
 */
return [
    'nombre' => env('INSTITUCION_NOMBRE', 'Universidad Nihon Gakko'),
    'lema' => env('INSTITUCION_LEMA', 'Esfuerzo y disciplina para el éxito'),
    'logo' => 'images/logo-nihon-gakko.png',
    // Texto legal opcional bajo el nombre (p. ej. "Creada por Ley N° ..."). Vacío = no se imprime.
    'ley' => env('INSTITUCION_LEY'),
    // Datos opcionales que usan los contratos y constancias; vacíos se reemplazan por una frase neutra.
    'sitio_web' => env('INSTITUCION_SITIO_WEB'),
    'direccion' => env('INSTITUCION_DIRECCION'),
    'ciudad' => env('INSTITUCION_CIUDAD'),
    // Firmantes de certificados y constancias (nombre con título, p. ej. "Abg. Nombre Apellido").
    'secretario_general' => env('INSTITUCION_SECRETARIO_GENERAL'),
    'rector' => env('INSTITUCION_RECTOR'),
];
