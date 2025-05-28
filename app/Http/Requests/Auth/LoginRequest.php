<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'], // PASTIKAN DI SINI KEY-NYA ADALAH 'login', BUKAN 'email'
            'password' => ['required', 'string'],
            // 'remember' => ['boolean'], // Opsional untuk "Remember Me", bisa ditambahkan jika perlu
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Tentukan apakah input 'login' adalah email atau username
        $loginField = filter_var($this->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Membuat array credentials berdasarkan $loginField
        $credentials = [
            $loginField => $this->input('login'), // Menggunakan $loginField sebagai key dinamis
            'password' => $this->input('password'),
        ];

        // Mencoba untuk autentikasi
        if (! Auth::attempt($credentials, $this->boolean('remember'))) { // $this->boolean('remember') akan false jika 'remember' tidak dikirim
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                // Mengembalikan error ke field 'login' di frontend
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) { // Max 5 attempts
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [ // Mengembalikan error ke field 'login'
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}