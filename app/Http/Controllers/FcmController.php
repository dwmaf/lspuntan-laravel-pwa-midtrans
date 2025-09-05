<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

class FcmController extends Controller
{
    public function saveToken(Request $request)
    {
        $request->user()->update(['fcm_token' => $request->token]);
        return response()->json(['token_saved' => true]);
    }
}