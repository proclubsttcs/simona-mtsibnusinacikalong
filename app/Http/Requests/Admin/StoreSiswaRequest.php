<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validasi form tambah siswa baru
 */
class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nis'             => ['required', 'string', 'max:20', 'unique:siswa,nis'],
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
            'nis.required'             => 'NIS wajib diisi.',
            'nis.unique'               => 'NIS sudah digunakan siswa lain.',
            'nis.max'                  => 'NIS maksimal 20 karakter.',
            'nama.required'            => 'Nama siswa wajib diisi.',
            'kelas.required'           => 'Kelas wajib dipilih.',
            'jenis_kelamin.required'   => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'         => 'Jenis kelamin tidak valid.',
            'foto.image'               => 'File foto harus berupa gambar.',
            'foto.mimes'               => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
            'foto.max'                 => 'Ukuran foto maksimal 2MB.',
            'nama_orang_tua.required'  => 'Nama orang tua/wali wajib diisi.',
            'no_hp_orang_tua.required' => 'Nomor HP orang tua wajib diisi.',
            'alamat.required'          => 'Alamat wajib diisi.',
            'user_id.required'         => 'Wali kelas wajib dipilih.',
            'user_id.exists'           => 'Wali kelas tidak ditemukan.',
        ];
    }
}
