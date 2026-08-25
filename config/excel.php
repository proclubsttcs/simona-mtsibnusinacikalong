<?php

return [
    'exports' => [
        'chunk_size'        => 1000,
        'pre_calculate_formulas' => false,
        'strict_null_comparison' => false,

        'csv' => [
            'delimiter'              => ',',
            'enclosure'              => '"',
            'line_ending'            => PHP_EOL,
            'use_bom'                => false,
            'include_separator_line' => false,
            'excel_compatibility'    => false,
            'output_encoding'        => '',
            'test_auto_filter'       => false,
        ],

        'properties' => [
            'creator'        => 'SiMON — MTs Ibnu Sina',
            'lastModifiedBy' => 'SiMON',
            'title'          => 'Export SiMON',
            'description'    => 'Data diekspor dari Sistem Monitoring Pelanggaran Siswa MTs Ibnu Sina',
            'subject'        => 'Laporan SiMON',
            'keywords'       => 'simon,pelanggaran,siswa,laporan',
            'category'       => 'Laporan',
            'manager'        => '',
            'company'        => 'MTs Ibnu Sina',
        ],
    ],

    'imports' => [
        'read_only'           => true,
        'ignore_empty'        => false,
        'heading_row'         => ['formatter' => 'slug'],
        'csv'                 => [
            'delimiter'  => ',',
            'enclosure'  => '"',
            'escape'     => '\\',
            'contiguous' => false,
            'input_encoding' => 'UTF-8',
        ],
        'properties'          => [],
    ],

    'extension_detector' => [
        'xlsx'  => \Maatwebsite\Excel\Excel::XLSX,
        'xlsm'  => \Maatwebsite\Excel\Excel::XLSX,
        'xltx'  => \Maatwebsite\Excel\Excel::XLSX,
        'xltm'  => \Maatwebsite\Excel\Excel::XLSX,
        'xls'   => \Maatwebsite\Excel\Excel::XLS,
        'xlt'   => \Maatwebsite\Excel\Excel::XLS,
        'ods'   => \Maatwebsite\Excel\Excel::ODS,
        'ots'   => \Maatwebsite\Excel\Excel::ODS,
        'slk'   => \Maatwebsite\Excel\Excel::SLK,
        'xml'   => \Maatwebsite\Excel\Excel::XML,
        'gnumeric' => \Maatwebsite\Excel\Excel::GNUMERIC,
        'htm'   => \Maatwebsite\Excel\Excel::HTML,
        'html'  => \Maatwebsite\Excel\Excel::HTML,
        'csv'   => \Maatwebsite\Excel\Excel::CSV,
        'tsv'   => \Maatwebsite\Excel\Excel::TSV,
        'pdf'   => \Maatwebsite\Excel\Excel::DOMPDF,
    ],

    'value_binder' => [
        'default' => \Maatwebsite\Excel\DefaultValueBinder::class,
    ],

    'cache' => [
        'driver' => 'memory',
        'stores' => [
            'memory' => ['driver' => 'memory'],
            'null'   => ['driver' => 'null'],
            'batch'  => ['driver' => 'batch', 'memory_limit' => 60000, 'location' => storage_path('framework/laravel-excel')],
        ],
    ],

    'transactions' => [
        'handler' => 'db',
        'db'      => ['connection' => null],
    ],

    'temporary_files' => [
    'local_path'          => storage_path('framework/laravel-excel'),
    'local_permissions'   => 0755,
    'remote_disk'         => null,
    'remote_prefix'       => null,
    'force_resync_remote' => null,
],
];