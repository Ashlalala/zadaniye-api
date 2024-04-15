<?php

namespace App\Traits;

use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCode;
use App\Models\User;
use Seshac\Otp\Otp;


trait VerificationCodeTrait
{
    public $test;

    private function generateVerificationCode($identifier)
    {
        $otp =  Otp::setValidity(6)  // otp validity time in mins
            ->setLength(4)  // Lenght of the generated otp
            ->setMaximumOtpsAllowed(5) // Number of times allowed to regenerate otps
            ->generate($identifier);

        return $otp;
    }

    public function sendVerificationCode(User $user, $method)
    {
        $code = $this->generateVerificationCode($user->phone_number)->token;


        $user->verification_code = $code;
        $user->save();


        return $code; // ignore this line, it is here just for simplicity, the code after
        // this line is the "real" code written as pseudo code. I commented out the last 2 lines to prevent errors.


        if ($method == 'email') Mail::to($user->email)->send(new VerificationCode($code));
        // else if($method=='sms') SMS::to($user->number)->send(new VerificationCode($code));
        // else if($method=='telegram') Telegram::to($user->telegram)->send(new VerificationCode($code));
    }


    public function verifyVerificationCode(User $user, $code)
    {
        $validated = Otp::setAllowedAttempts(5)
            ->validate($user->phone_number, $code);

        if ($validated) {
            return $user->verification_code  == $code;
        }

        return false;
    }
}
