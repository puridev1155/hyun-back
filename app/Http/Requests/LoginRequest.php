<?php

namespace App\Http\Requests;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            /*'email' => ['required', 'string', 'email'], */
            'email' => ['required', 'string'],
            'password' => ['required', 'string']
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $removedUser = User::onlyTrashed()->where("email", $this->input('email'))->first();

        if($removedUser)
            throw ValidationException::withMessages([
                "email" => "해당 계정정보는 정지된 상태입니다 고객센터로 문의바랍니다."
            ]);

        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            $this->loggingLogin(false, null, __('auth.failed'));

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);

        }

        $this->loggingLogin(true, Auth::user()->name, null);
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        $this->loggingLogin(false, null, trans('auth.throttle', ['seconds' => $seconds, 'minutes' => ceil($seconds / 60)]));
        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')) . '|' . $this->ip();
    }

    public function loggingLogin($result, $name, $error_message)
    {
       /*  if ($this->getHost() === config('app.admin_domain')) {
            LoginLog::create([
                'result' => $result,
                'login_id' => $this->email,
                'name' => $name,
                'error_message' => $error_message,
                'login_ip' => $this->ip()
            ]);
        } */
    }

}
