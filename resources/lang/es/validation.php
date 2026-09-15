<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validación del idioma
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de idioma contienen los mensajes de error predeterminados utilizados por
    | La clase validadora. Algunas de estas reglas tienen múltiples versiones tales
    | como las reglas de tamaño. Siéntase libre de modificar cada uno de estos mensajes aquí.
    |
    */

    'accepted' => 'El campo :attribute debe ser aceptado.',
    'accepted_if' => 'El campo :attribute debe ser aceptado cuando :other es :value.',
    'active_url' => 'El campo :attribute no es una URL válida.',
    'after' => 'El campo :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El campo :attribute sólo puede contener letras.',
    'alpha_dash' => 'El campo :attribute sólo puede contener letras, números, guiones y guiones bajos.',
    'alpha_num' => 'El campo :attribute sólo puede contener letras y números.',
    'array' => 'El campo :attribute debe ser un arreglo.',
    'ascii' => 'El :attribute solo debe contener símbolos y caracteres alfanuméricos de un solo byte.',
    'before' => 'El campo :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El campo :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'array' => 'El campo :attribute debe tener entre :min y :max elementos.',
        'file' => 'El campo :attribute debe tener entre :min y :max kilobytes.',
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
        'string' => 'El campo :attribute debe tener entre :min y :max caracteres.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'El campo de confirmación de :attribute no coincide.',
    'current_password' => 'La contraseña actual no es correcta',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_equals' => 'El campo :attribute debe ser una fecha igual a :date.',
    'date_format' => 'El campo :attribute no corresponde con el formato :format.',
    'decimal' => 'El :attribute debe tener :decimal decimales.',
    'declined' => 'El campo :attribute debe de ser rechazado.',
    'declined_if' => 'El campo :attribute debe ser rechazado cuando :other es :value.',
    'different' => 'Los campos :attribute y :other deben ser diferentes.',
    'digits' => 'El campo :attribute debe tener :digits dígitos.',
    'digits_between' => 'El campo :attribute debe tener entre :min y :max dígitos.',
    'dimensions' => 'El campo :attribute tiene dimensiones de imagen inválidas.',
    'distinct' => 'El campo :attribute tiene un valor duplicado.',
    'doesnt_end_with' => 'El campo :attribute no puede finalizar con uno de los siguientes valores: :values.',
    'doesnt_start_with' => 'El campo :attribute no puede comenzar con uno de los siguientes valores: :values.',
    'email' => 'El formato del :attribute no es válido.',
    'ends_with' => 'El campo :attribute debe terminar con alguno de los valores: :values.',
    'enum' => 'El valor seleccionado :attribute no es válido.',
    'exists' => 'El valor seleccionado :attribute no es válido.',
    'extensions' => 'El campo :attribute debe ser un archivo con extensión: :values',
    'file' => 'El campo :attribute debe ser un archivo.',
    'filled' => 'El campo :attribute debe tener un valor.',
    'gt' => [
        'array' => 'El campo :attribute debe tener mas de :value elementos.',
        'file' => 'El campo :attribute debe ser mayor que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor que :value.',
        'string' => 'El campo :attribute debe ser mayor a :value caracteres.',
    ],
    'gte' => [
        'array' => 'El campo :attribute debe tener :value elementos o más.',
        'file' => 'El campo :attribute debe ser mayor o igual que :value kilobytes.',
        'numeric' => 'El campo :attribute debe ser mayor o igual que :value.',
        'string' => 'El campo :attribute debe ser mayor o igual a :value caracteres.',
    ],
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El campo :attribute seleccionado no es válido.',
    'in_array' => 'El campo :attribute no existe en :other.',
    'integer' => 'El campo :attribute debe ser un entero.',
    'ip' => 'El campo :attribute debe ser una dirección IP válida.',
    'ipv4' => 'El campo :attribute debe ser una dirección IPv4 válida.',
    'ipv6' => 'El campo :attribute debe ser una dirección IPv6 válida.',
    'json' => 'El campo :attribute debe ser una cadena JSON válida.',
    'lowercase' => 'El :attribute debe estar en minúsculas.',
    'lt' => [
        'array' => 'El campo :attribute puede tener menos de :max elementos.',
        'file' => 'El campo :attribute debe ser menor de :max kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor que :max.',
        'string' => 'El campo :attribute debe ser menor de :max caracteres.',
    ],
    'lte' => [
        'array' => 'El campo :attribute no puede tener más de :max elementos.',
        'file' => 'El campo :attribute debe ser menor o igual que :max kilobytes.',
        'numeric' => 'El campo :attribute debe ser menor o igual que :max.',
        'string' => 'El campo :attribute debe ser menor o igual que :max caracteres.',
    ],
    'mac_address' => 'El campo :attribute debe ser una dirección MAC válida.',
    'max' => [
        'array' => 'El campo :attribute puede tener hasta :max elementos.',
        'file' => 'El campo :attribute no puede pasar los :max kilobytes.',
        'numeric' => 'El campo :attribute no debe de ser mayor a :max.',
        'string' => 'El campo :attribute debe ser menor que :max caracteres.',
    ],
    'max_digits' => 'El campo :attribute no debe de tener mas de :max dígitos.',
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'mimetypes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
        'file' => 'El campo :attribute debe tener al menos :min kilobytes.',
        'numeric' => 'El campo :attribute debe tener al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'min_digits' => 'El campo :attribute debe ser como mínimo de :min dígitos.',
    'missing' => 'El campo :attribute debe faltar.',
    'missing_if' => 'El campo :attribute debe faltar cuando :other es :value',
    'missing_unless' => 'El campo :attribute debe faltar a menos que :other sea :value.',
    'missing_with' => 'El campo :attribute debe faltar cuando :values está presente.',
    'missing_with_all' => 'El campo :attribute debe faltar cuando :values están presentes',
    'multiple_of' => 'El campo :attribute debe ser un múltiplo de :value.',
    'not_in' => 'El valor seleccionado :attribute no es válido.',
    'not_regex' => 'El formato del campo :attribute no es válido.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'password' => [
        'letters' => 'El campo :attribute debe contener al menos una letra.',
        'mixed' => 'El campo :attribute debe contener al menos una letra mayúscula y una minúscula.',
        'numbers' => 'El campo :attribute debe contener al menos un número.',
        'symbols' => 'El campo :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'El valor del campo :attribute aparece en alguna filtración de datos. Por favor indica un valor diferente.',
    ],
    'present' => 'El campo :attribute debe estar presente.',
    'prohibited' => 'El campo :attribute no está permitido.',
    'prohibited_if' => 'El campo :attribute no está permitido cuando :other es :value.',
    'prohibited_unless' => 'El campo :attribute no está permitido si :other no está en :values.',
    'prohibits' => 'El campo :attribute no permite que :other esté presente.',
    'regex' => 'El formato del campo :attribute no es válido.',
    'required' => 'El campo :attribute es requerido.',
    'required_array_keys' => 'El campo :attribute debe contener entradas para: :values.',
    'required_if' => 'El campo :attribute es requerido cuando :other es :value.',
    'required_if_accepted' => 'El campo :attribute es requerido cuando :other es aceptado.',
    'required_unless' => 'El campo :attribute es requerido a menos que :other esté presente en :values.',
    'required_with' => 'El campo :attribute es requerido cuando :values está presente.',
    'required_with_all' => 'El campo :attribute es requerido cuando :values están presentes.',
    'required_without' => 'El campo :attribute es requerido cuando :values no está presente.',
    'required_without_all' => 'El campo :attribute es requerido cuando ninguno de los valores :values está presente.',
    'same' => 'El campo :attribute debe coincidir con :other.',
    'size' => [
        'array' => 'El campo :attribute debe contener :size elementos.',
        'file' => 'El campo :attribute debe tener :size kilobytes.',
        'numeric' => 'El campo :attribute debe ser :size.',
        'string' => 'El campo :attribute debe tener :size caracteres.',
    ],
    'starts_with' => 'El :attribute debe empezar con uno de los siguientes valores :values',
    'string' => 'El campo :attribute debe ser una cadena.',
    'timezone' => 'El campo :attribute debe ser una zona horaria válida.',
    'unique' => 'El :attribute ya existe.',
    'uploaded' => 'El campo :attribute no ha podido ser cargado.',
    'uppercase' => 'El campo :attribute debe estar en mayúsculas',
    'url' => 'El formato de :attribute no es válido.',
    'ulid' => 'El :attribute debe ser un ULID valido.',
    'uuid' => 'El :attribute debe ser un UUID valido.',

    /*
    |--------------------------------------------------------------------------
    | Validación del idioma personalizado
    |--------------------------------------------------------------------------
    |
    | Aquí puede especificar mensajes de validación personalizados para atributos utilizando el
    | convención "attribute.rule" para nombrar las líneas. Esto hace que sea rápido
    | especifique una línea de idioma personalizada específica para una regla de atributo dada.
    |
    */

    'custom' => [
        'permission' => [
            'required' => 'Debe seleccionar algún permiso de la lista para continuar.',
        ],
        'tipo_malla' => [
            'unique' => 'La combinación de esta carrera con este tipo de malla ya existe.'
        ],
        'carrera' => [
            'unique' => 'El alumno ya se encuentra matriculado en esta combinación de semestre, programa y carrera.'
        ],
        'carrera_siu' => [
            'required_if' => 'El campo carrera SIU es requerido.',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Atributos de validación personalizados
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de idioma se utilizan para intercambiar los marcadores de posición de atributo.
    | con algo más fácil de leer, como la dirección de correo electrónico.
    | de "email". Esto simplemente nos ayuda a hacer los mensajes un poco más limpios.
    |
    */

    'attributes' => [
        'name' => 'nombre',
        'password' => 'contraseña',
        'current_password' => 'contraseña actual',
        'new_password' => 'nueva contraseña',
        'password_confirmation' => 'confirmar contraseña',
        'email' => 'correo electrónico',
        'telefono' => 'número de teléfono',
        'ci' => 'cédula de identidad',

        //Alumnos
            'primer_nombre_alumno' => 'primer nombre',
            'primer_apellido_alumno' => 'primer apellido',
            'numero_documento' => 'número de documento',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'celular' => 'N° de celular',
            'email_personal' => 'correo personal',
            'direccion' => 'dirección',
            'formacion' => 'formación',
            'institucion_educativa' => 'institución educativa',
            'primer_nombre_familiar1' => 'primer nombre',
            'primer_apellido_familiar1' => 'primer apellido',
            'relacion_familiar1' => 'relación familiar',
            'celular_familiar1' => 'N° de celular',
            'email_familiar1' => 'correo',
            'primer_nombre_familiar2' => 'primer nombre',
            'primer_apellido_familiar2' => 'primer apellido',
            'relacion_familiar2' => 'relación familiar',
            'celular_familiar2' => 'N° de celular',
            'email_familiar2' => 'correo',
            'email_laboral' => 'correo',
            'telefono_laboral' => 'N° de teléfono',
            'tipo_legajo' => 'tipo de legajo',
            'legajo' => 'archivo',

        //Docentes
            'primer_nombre_docente' => 'primer nombre',
            'primer_apellido_docente' => 'primer apellido',
            'numero_documento' => 'número de documento',
            'fecha_nacimiento' => 'fecha de nacimiento',
            'celular' => 'N° de celular',
            'email_personal' => 'correo personal',
            'direccion' => 'dirección',
            'nivel_academico' => 'nivel académico',
            'capcacitacion_didactica' => 'capacitación didáctica',
            'tipo_legajo' => 'tipo de legajo',
            'titulo_obtenido' => 'título obtenido',
            'institucion_educativa' => 'institución educativa',
            'pais' => 'país',
            'legajo' => 'archivo',

        //Matriculaciones
            'carrera_siu' => 'carrera SIU',
            'tipo_pago' => 'tipo de pago',

        //Programas
            'duracion' => 'duración',
            'cantidad_creditos' => 'cantidad de créditos',

        //Carreras
            'nombre_fantasia' => 'nombre fantasía',
            'tipo_carrera' => 'tipo de carrera',
            'cantidad_semestres' => 'cantidad de semestres',

        //Formacion Academica
            'nombre_formacion_academica' => 'nombre',

        //Instituciones Educativas
            'nombre_institucion_educativa' => 'nombre',
            'tipo_institucion_educativa' => 'tipo',

        //Mallas
            'tipo_malla' => 'tipo de malla',
            'detalles.*.materia' => 'materia',
            'detalles.*.semestre' => 'semestre',
            'detalles.*.carga_horaria' => 'carga horaria',
            'detalles.*.cantidad_creditos' => 'cantidad de créditos',
            'detalles.*.area_curricular' => 'área curricular',
            'detalles.*.doble_grado' => 'doble grado',

        //Semestres
            'nombre_semestre' => 'nombre',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'detalles.*.malla' => 'carrera',
            'detalles.*.coordinador' => 'coordinador',
            'detalles.*.fecha_inicio_matriculacion' => 'fecha de inicio de matriculación',
            'detalles.*.fecha_fin_matriculacion' => 'fecha de fin de matriculación',

        //Semestre Mallas
            'feha_inicio_matriculacion' => 'fecha de inicio de matriculación',
            'feha_fin_matriculacion' => 'fecha de fin de matriculación',
            'precio_matricula' => 'precio de matrícula',
            'precio_contado' => 'precio al contado',
            'precio_cuota' => 'precio de las cuotas',
            'cantidad_cuotas' => 'cantidad de cuotas',
            'fecha_inicio_vencimiento_cuota' => 'fecha de vencimiento de la 1ra cuota',
            'dia_vencimiento_cuota' => 'día de vencimiento de las cuotas',
            'precio_multa' => 'precio de multa por día',
            'dias_gracia' => 'días de gracia',

        //Semestre Malla Materias
            'detalles.*.dia' => 'día de la semana',
            'detalles.*.hora_inicio' => 'hora de inicio',
            'detalles.*.hora_fin' => 'hora de fin',

        //Convalidaciones Externas - Internas
            'numero_solicitud' => 'número de solicitud',
            'universidad_origen' => 'universidad de origen',
            'facultad_origen' => 'facultad de origen',
            'carrera_origen' => 'carrera de origen',
            'certificado' => 'certificado de estudios',
            'detalles.*.materia_origen' => 'materia',
            'detalles.*.calificacion_origen' => 'calificación',

        //Extensiones Universitarias
            'nombre_proyecto' => 'nombre del proyecto',
            'tipo_extension' => 'tipo de extensión',
            'cantidad_horas_proyecto' => 'horas del proyecto',
            'detalles.*.alumno' => 'alumno',
            'detalles.*.cantidad_horas_alumno' => 'cantidad de horas',

        //Mallas Espejo
            'malla_paraguay' => 'malla Paraguay',
            'malla_siu' => 'malla SIU',
            'detalles.*.materia_paraguay' => 'materia Paraguay',
            'detalles.*.materia_siu' => 'materia SIU',
    ],

    'values' => [
        'tipo_legajo' => [
            '1' => 'foto carnet',
            '2' => 'documento de identidad',
            '3' => 'título de bachiller',
            '4' => 'certificado de estudios del colegio',
            '5' => 'título de bachiller traducido al inglés',
            '6' => 'certificado de estudios traducido al inglés',
            '7' => 'currículum vitae normalizado',
            '8' => 'título de grado',
            '9' => 'título de posgrado',
            '10' => 'capacitación didáctica'
        ],
    ]

];
