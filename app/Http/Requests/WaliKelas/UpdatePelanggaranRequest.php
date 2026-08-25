<?php

namespace App\Http\Requests\WaliKelas;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePelanggaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Pastikan pelanggaran masih 'menunggu' dan milik wali kelas ini
        $pelanggaran = $this->route('pelanggaran');
        return auth()->user()->isWaliKelas()
            && $pelanggaran->user_id === auth()->id()
            && $pelanggaran->status === 'menunggu';
    }

    public function rules(): array
    {
        $user = auth()->user();

        return [
            'siswa_id' => [
                'required',
                'integer',
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
            'siswa_id.required'             => 'Siswa wajib dipilih.',
            'jenis_pelanggaran_id.required' => 'Jenis pelanggaran wajib dipilih.',
            'tanggal_pelanggaran.required'  => 'Tanggal wajib diisi.',
            'tanggal_pelanggaran.before_or_equal' => 'Tanggal tidak boleh lebih dari hari ini.',
            'poin_diberikan.required'       => 'Poin wajib diisi.',
        ];
    }
}
