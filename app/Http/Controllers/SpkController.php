<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Spk;
use Carbon\Carbon;

class SpkController extends Controller
{
    public function showSpkForm()
    {
        $pengguna = Auth::user();
        // Mendapatkan kode produksi terbaru dari hari ini
        $latestKode = Spk::whereDate('created_at', Carbon::today())
                        ->orderBy('kode_produksi', 'desc')
                        ->pluck('kode_produksi')
                        ->first();
        
        // Menentukan urutan berdasarkan kode produksi terbaru
        if ($latestKode) {
            // Mengambil urutan dari kode produksi terbaru
            $latestSequence = intval(substr($latestKode, 1, strpos($latestKode, '.') - 1));
            $nextSequence = $latestSequence + 1; // Urutan berikutnya
        } else {
            $nextSequence = 1; // Urutan pertama
        }
        
        // Membuat kode produksi baru
        $kodeProduksi = 'A' . str_pad($nextSequence, 1, '0', STR_PAD_LEFT) . '.' . Carbon::now()->format('ymd');
        
        // Pass Kode Produksi and current user to view
        return view('cs.pengajuan', [
            'kodeProduksi' => $kodeProduksi,
            'id_pengguna' => $pengguna->id, // Mengirimkan ID pengguna yang benar
        ]);
    }


    public function store(Request $request)
    {
        // Validasi data input
        $request->validate([
            'kode_produksi' => 'required|string',
            'katalog_produksi' => 'required|string',
            'material' => 'required|string',
            'warna' => 'required|string',
            'jumlah' => 'required|integer',
            'tanggal_masuk' => 'required|date',
            'deadline' => 'required|date',
            'detail' => 'nullable|string',
            'desain' => 'nullable|file|mimes:pdf,jpeg,png|max:2048',
            'id_vendor' => 'required|string',
            'size_s' => 'required|integer',
            'size_m' => 'required|integer',
            'size_l' => 'required|integer',
            'size_xl' => 'required|integer',
            'size_xxl' => 'required|integer',
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('desain')) {
            $file = $request->file('desain');
            $filePath = $file->store('uploads/desain', 'public');
        }

        // Create new SPK
        Spk::create([
            'kode_produksi' => $request->kode_produksi,
            'id_pengguna' => Auth::id(), // Mengambil ID pengguna yang sedang login
            'katalog_produksi' => $request->katalog_produksi,
            'material' => $request->material,
            'warna' => $request->warna,
            'jumlah' => $request->jumlah,
            'tanggal_masuk' => $request->tanggal_masuk,
            'deadline' => $request->deadline,
            'detail' => $request->detail,
            'desain' => $filePath,
            'id_vendor' => $request->id_vendor,
            'size_s' => $request->size_s,
            'size_m' => $request->size_m,
            'size_l' => $request->size_l,
            'size_xl' => $request->size_xl,
            'size_xxl' => $request->size_xxl,
        ]);

        return redirect()->route('spk.form')->with('success', 'SPK berhasil diajukan.');
    }
}
