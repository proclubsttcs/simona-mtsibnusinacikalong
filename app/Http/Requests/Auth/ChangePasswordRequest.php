<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Form Request untuk validasi ganti password
 */
class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            'password_baru'   => ['required', 'string', 'min:8', 'confirmed'],
        ];

        // Jika bukan first-time (must_change_password = false), wajib isi password lama
        if (! Auth::user()->must_change_password) {
            $rules['password_lama'] = ['required', 'string'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'password_lama.required'           => 'Password lama wajib diisi.',
            'password_baru.required'           => 'Password baru wajib diisi.',
            'password_baru.min'                => 'Password baru minimal 8 karakter.',
            'password_baru.confirmed'          => 'Konfirmasi password baru tidak cocok.',
            'password_baru_confirmation.required' => 'Konfirmasi password wajib diisi.',
        ];
    }
}
