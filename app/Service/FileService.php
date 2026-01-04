<?php

namespace App\Service;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class FileService

{

    public function getPrivateFile(string $path): string

    {

        if (!Storage::disk('private')->exists($path)) {

            throw new FileNotFoundException('File not found.');
        }

        return Storage::disk('private')->path($path);

    }

}

