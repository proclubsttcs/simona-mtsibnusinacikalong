<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisPelanggaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class JenisPelanggaranController extends Controller
{
    public function index(Request $request): View
    {
        $query = JenisPelanggaran::withCount('pelanggaran')->orderBy('kategori')->orderBy('nama');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%');
        }

        $jenis = $query->paginate(20)->withQueryString();

        return view('admin.jenis-pelanggaran.index', compact('jenis'));
    }

    public function create(): View
    {
        return view('admin.jenis-pelanggaran.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode'       => ['required','string','max:20','unique:jenis_pelanggaran,kode'],
            'nama'       => ['required','string','max:255'],
            'kategori'   => ['required','in:ringan,sedang,berat,sangat_berat'],
            'poin'       => ['required','integer','min:1','max:200'],
            'keterangan' => ['nullable','string','max:255'],
            'is_active'  => ['boolean'],
        ], [
            'kode.unique'   => 'Kode sudah digunakan.',
            'poin.required' => 'Poin wajib diisi.',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        JenisPelanggaran::create($data);

        return redirect()->route('admin.jenis-pelanggaran.index')
            ->with('success', "Jenis pelanggaran \"{$data['nama']}\" berhasil ditambahkan.");
    }

    public function edit(JenisPelanggaran $jenisPelanggaran): View
    {
        return view('admin.jenis-pelanggaran.edit', compact('jenisPelanggaran'));
    }

    public function update(Request $request, JenisPelanggaran $jenisPelanggaran): RedirectResponse
    {
        $data = $request->validate([
            'kode'       => ['required','string','max:20', Rule::unique('jenis_pelanggaran','kode')->ignore($jenisPelanggaran->id)],
            'nama'       => ['required','string','max:255'],
            'kategori'   => ['required','in:ringan,sedang,berat,sangat_berat'],
            'poin'       => ['required','integer','min:1','max:200'],
            'keterangan' => ['nullable','string','max:255'],
            'is_active'  => ['boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $jenisPelanggaran->update($data);

        return redirect()->route('admin.jenis-pelanggaran.index')
            ->with('success', "Jenis pelanggaran berhasil diperbarui.");
    }

    public function destroy(JenisPelanggaran $jenisPelanggaran): RedirectResponse
    {
        if ($jenisPelanggaran->pelanggaran()->exists()) {
            return back()->with('error',
                'Tidak dapat menghapus jenis pelanggaran yang sudah digunakan.');
        }

        $jenisPelanggaran->delete();

        return back()->with('success', 'Jenis pelanggaran berhasil dihapus.');
    }

    public function toggleStatus(JenisPelanggaran $jenisPelanggaran): RedirectResponse
    {
        $jenisPelanggaran->update(['is_active' => ! $jenisPelanggaran->is_active]);
        $status = $jenisPelanggaran->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Jenis pelanggaran berhasil {$status}.");
    }
}
