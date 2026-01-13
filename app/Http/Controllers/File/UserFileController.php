<?php

namespace App\Http\Controllers\File;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Service\FileService;
use Illuminate\Support\Facades\Auth;

class UserFileController extends Controller
{
    public function show(string $type, User $user, FileService $fileService)
    {
        $authUser = Auth::guard('user_api')->user();
        abort_if(!$authUser, 401);

        // يمنع المستخدم يشوف صور غيره
        abort_if($authUser->id !== $user->id, 403);

        $path = match ($type) {
            'personal' => $user->profile?->personal_image,
            'id'       => $user->profile?->id_image,
            default    => abort(404),
        };

        abort_if(!$path, 404, 'الصورة غير موجودة');

        return response()->file(
            $fileService->getPrivateFile($path)
        );
    }
}

