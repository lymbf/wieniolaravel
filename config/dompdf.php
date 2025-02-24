<?php

return [

    'show_warnings' => false,

    'public_path' => null,

    'convert_entities' => true,

    'options' => [
        'font_dir' => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),
        'temp_dir' => sys_get_temp_dir(),
        
        /*
         * To pozwala Dompdf na wczytywanie plików z folderu public
         */
        'chroot' => public_path(),

        'allowed_protocols' => [
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],

        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'portrait',
        'default_font' => 'serif',

        /*
         * Rozdzielczość DPI
         */
        'dpi' => 96,

        /*
         * Włącz obsługę plików zdalnych i lokalnych
         */
        'enable_php' => false,
        'enable_javascript' => true,
        'enable_remote' => true, // <--- kluczowe
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => true,
    ],

];
