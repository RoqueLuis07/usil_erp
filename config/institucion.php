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
];
