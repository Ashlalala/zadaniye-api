<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\VerificationService;
use App\Traits\VerificationCodeTrait;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use VerificationCodeTrait;

    public function sendVerificationCodeUsingTrait(Request $request)
    {
        $user = Auth::user();

        $code = $this->sendVerificationCode($user, $request->query()['method']);


        return $code;
        return response()->json(['message' => 'Verification code sent'],); //I included $code here in the response just for simplicity. Of cource it should not be here
    }




    /**
     * Update the specified user setting.
     *
     */
    public function updateSetting(Request $request)
    {
        $user = $request->user();

        // I chose to put the verification code entered by the user in the url params (to be retrieved from ->query())
        // instead of the form data, but could have done otherwise if it wasneeded.
        if ($this->verifyVerificationCode($user, $request->query()['code'])) {
            $user->update($request->all());
            return $user;
        } else {
            return response()->json(['error' => 'Invalid verification code'], 400);
        }
    }

    // public function verifySetting(Request $request)
    // {
    //     $user = $request->user();

    //     // Validate request...
    //     $code = $request->input('code');

    //     if ($user->verification_code === $code) {
    //         // Update user setting...
    //         $user->settings()->updateOrCreate(
    //             ['user_id' => $user->id, 'setting_name' => $user->temp_setting],
    //             ['setting_value' => $request->input('value')]
    //         );

    //         return response()->json(['message' => 'Setting updated']);
    //     } else {
    //     }
    // }
}
