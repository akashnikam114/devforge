<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    public function download(string $view, array $data, string $fileName)
    {
        return Pdf::loadView($view, $data)->download($fileName);
    }
}
