<?php

namespace App\Http\Controllers\File;

use App\Models\User;
use App\Service\FileService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class FileController extends Controller
{
    public function show(string $type, User $user, FileService $fileService)
    {
        $admin = Auth::guard('admin_api')->check();
        $authUser = Auth::guard('user_api')->user();

        // لا أدمن ولا يوزر
        if (!$admin && !$authUser) {
            abort(401);
        }

        // يوزر → فقط ملفه
        if ($authUser && $authUser->id !== $user->id) {
            abort(403);
        }

        // 🔥 جيب البروفايل
        $profile = $user->profile;

        abort_if(!$profile, 404);

        $path = match ($type) {
            'personal' => $profile->personal_image,
            'id'       => $profile->id_image,
            default    => null,
        };

        abort_if(!$path, 404);

        return response()->file(
            $fileService->getPrivateFile($path)
        );
    }
}
