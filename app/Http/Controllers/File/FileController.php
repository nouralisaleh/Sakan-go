<?php

namespace App\Http\Controllers\File;

use App\Models\User;
use App\Models\Admin;
use App\Service\FileService;
use App\Policies\FilePolicy;
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

        $user = User::find($id);
        $admin = Admin::firstOrFail();
        $target = $user ?: $admin;
        abort_if(!$target, 404, 'المستخدم غير موجود');

        $path = null;
        if ($target instanceof Admin) {
            $path = ($type === 'personal') ? $target->personal_image : $target->id_image;
        } else {
            $path = ($type === 'personal') ? $target->profile?->personal_image : $target->profile?->id_image;
        }

        abort_if(!$path, 404, 'الصورة غير موجودة');
        $auth = $isAdmin
            ? Auth::guard('admin_api')->user()
            : $authUser;

        $policy = app(FilePolicy::class);

        if (! $policy->view($auth, $path)) {
            abort(403, 'غير مصرح لك مشاهدة هذا الملف');
        }

        return response()->file($fileService->getPrivateFile($path));
    }
}
