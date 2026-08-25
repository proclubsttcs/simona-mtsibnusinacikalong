<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi form update data siswa
 */
class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $siswaId = $this->route('siswa')->id;

        return [
            'nis'             => ['required', 'string', 'max:20', Rule::unique('siswa', 'nis')->ignore($siswaId)],
            'nama'            => ['required', 'string', 'max:255'],
            'kelas'           => ['required', 'string', 'max:20'],
            'jenis_kelamin'   => ['required', 'in:L,P'],
            'foto'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'nama_orang_tua'  => ['required', 'string', 'max:255'],
            'no_hp_orang_tua' => ['required', 'string', 'max:20'],
            'alamat'          => ['required', 'string', 'max:500'],
            'user_id'         => ['required', 'exists:users,id'],
            'is_active'       => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required'   => 'NIS wajib diisi.',
            'nis.unique'     => 'NIS sudah digunakan siswa lain.',
            'nama.required'  => 'Nama siswa wajib diisi.',
            'kelas.required' => 'Kelas wajib dipilih.',
            'user_id.required' => 'Wali kelas wajib dipilih.',
            'user_id.exists'   => 'Wali kelas tidak ditemukan.',
        ];
    }
}
