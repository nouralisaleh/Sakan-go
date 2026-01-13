<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;

class FilePolicy
{

    public function view($authUser, string $path): bool
    {

        if ($authUser instanceof Admin) {
            return true;
        }

        if ($authUser instanceof User) {
            return str_contains($path, (string) $authUser->id);
        }

        return false;
    }
    
}
