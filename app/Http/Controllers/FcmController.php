<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FcmController extends Controller
{
    public function saveToken(Request $request)
    {
        $request->user()->update(['fcm_token' => $request->token]);
        return response()->json(['token_saved' => true]);
    }

    public function removeToken(Request $request)
    {
        $request->user()->update(['fcm_token'=>null]);
        return response()->json(['message'=>'Token removed successfully']);
    }
}
