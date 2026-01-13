<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Service\FileService;
use Illuminate\Support\Facades\Auth;

class AdminFileController extends Controller
{
    public function show(string $type, FileService $fileService)
    {
        $admin = Auth::guard('admin_api')->user();
        abort_if(!$admin, 401);

        $path = match ($type) {
            'personal' => $admin->personal_image,
            'id'       => $admin->id_image,
            default    => abort(404),
        };

        abort_if(!$path, 404, 'الصورة غير موجودة');

        return response()->file(
            $fileService->getPrivateFile($path)
        );
    }
}
