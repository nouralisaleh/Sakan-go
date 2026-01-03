<?php

namespace App\Http\Controllers\File;

use App\Models\User;
use App\Service\FileService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class FileController extends Controller
{
    public function show(string $type, $id, FileService $fileService)
    {
        $isAdmin = Auth::guard('admin_api')->check();
        $authUser = Auth::guard('user_api')->user();

        if (!$isAdmin && !$authUser) {
            abort(401);
        }

        $user = \App\Models\User::find($id);
        $admin = \App\Models\Admin::find($id);

        $target = $user ?: $admin;
        abort_if(!$target, 404, 'المستخدم غير موجود');

        $path = null;
    if ($target instanceof \App\Models\Admin) {
        $path = ($type === 'personal') ? $target->personal_image : $target->id_image;
    } else {
        $path = ($type === 'personal') ? $target->profile?->personal_image : $target->profile?->id_image;
    }

    abort_if(!$path, 404, 'الصورة غير موجودة');

    return response()->file($fileService->getPrivateFile($path));
    }
}
