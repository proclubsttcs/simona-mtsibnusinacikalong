<?php

namespace App\Http\Requests\WaliKelas;

use Illuminate\Foundation\Http\FormRequest;

class StorePelanggaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isWaliKelas();
    }

    public function rules(): array
    {
        $user = auth()->user();

        return [
            'siswa_id' => [
                'required',
                'integer',
                // Pastikan siswa memang di kelas wali kelas yang login
                function ($attribute, $value, $fail) use ($user) {
                    $siswa = \App\Models\Siswa::find($value);
                    if (! $siswa || $siswa->user_id !== $user->id) {
                        $fail('Siswa tidak ditemukan di kelas Anda.');
                    }
                },
            ],
            'jenis_pelanggaran_id' => ['required', 'exists:jenis_pelanggaran,id'],
            'tanggal_pelanggaran'  => ['required', 'date', 'before_or_equal:today'],
            'poin_diberikan'       => ['required', 'integer', 'min:1', 'max:200'],
            'keterangan'           => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'siswa_id.required'              => 'Siswa wajib dipilih.',
            'jenis_pelanggaran_id.required'  => 'Jenis pelanggaran wajib dipilih.',
            'jenis_pelanggaran_id.exists'    => 'Jenis pelanggaran tidak valid.',
            'tanggal_pelanggaran.required'   => 'Tanggal pelanggaran wajib diisi.',
            'tanggal_pelanggaran.before_or_equal' => 'Tanggal pelanggaran tidak boleh lebih dari hari ini.',
            'poin_diberikan.required'        => 'Poin wajib diisi.',
            'poin_diberikan.min'             => 'Poin minimal 1.',
        ];
    }
}
