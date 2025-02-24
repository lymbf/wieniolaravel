<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Domyślny sterownik obróbki obrazów
    |--------------------------------------------------------------------------
    |
    | Intervention Image obsługuje dwa sterowniki: "GD" i "Imagick".
    | Jeśli Imagick jest dostępny, zaleca się jego użycie, ponieważ jest
    | bardziej wydajny i obsługuje więcej formatów.
    |
    | Obsługiwane: "gd", "imagick"
    |
    */

   'driver' => 'gd',


    /*
    |--------------------------------------------------------------------------
    | Konfiguracja przechowywania plików
    |--------------------------------------------------------------------------
    |
    | Ścieżka do przechowywania plików tymczasowych i przetworzonych obrazów.
    | Laravel używa `storage_path('app/public')`, co oznacza, że musisz mieć
    | prawidłowo skonfigurowany `storage:link` dla poprawnego działania.
    |
    */

    'storage_path' => storage_path('app/public'),

    /*
    |--------------------------------------------------------------------------
    | Maksymalny rozmiar przesyłanych obrazów (w MB)
    |--------------------------------------------------------------------------
    |
    | Możesz ustawić maksymalny rozmiar przesyłanych obrazów. Jeśli plik
    | przekroczy ten limit, aplikacja zwróci błąd.
    |
    */

    'max_upload_size' => env('IMAGE_MAX_UPLOAD_SIZE', 20), // W MB

    /*
    |--------------------------------------------------------------------------
    | Kompresja obrazów
    |--------------------------------------------------------------------------
    |
    | Domyślna jakość kompresji dla JPG/JPEG. Wartości od 0 do 100.
    | Im wyższa wartość, tym lepsza jakość, ale większy rozmiar pliku.
    |
    */

    'compression_quality' => env('IMAGE_COMPRESSION_QUALITY', 80),

    /*
    |--------------------------------------------------------------------------
    | Domyślne wymiary obrazów
    |--------------------------------------------------------------------------
    |
    | Możesz ustawić domyślną szerokość i wysokość dla przesyłanych obrazów.
    | Jeśli oryginalny obraz jest większy, zostanie przeskalowany.
    |
    */

    'resize' => [
        'width' => env('IMAGE_RESIZE_WIDTH', 1920),
        'height' => env('IMAGE_RESIZE_HEIGHT', null), // null = proporcjonalne skalowanie
    ],
];
