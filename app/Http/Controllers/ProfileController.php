<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048', 
        ]);

        $user = $request->user();

        $path = $request->file('avatar')->store('avatars', 'cloudinary');
        $url = Storage::disk('cloudinary')->url($path);

        $user->update([
            'avatar' => $url
        ]);

        return response()->json([
            'message' => 'Avatar atualizado com sucesso!',
            'avatar_url' => $url
        ]);
    }
}