<?php

namespace App\Http\Controllers;

use App\Models\Gedung;
use App\Models\JenisBarang;
use App\Models\Vendor;
use App\Models\UnitBagian;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    // Peta tipe URL -> [Model class, nama kolom utama, aturan validasi tambahan]
    private array $peta = [
        'gedung' => ['model' => Gedung::class, 'kolom' => 'nama_gedung', 'label' => 'Gedung'],
        'jenis-barang' => ['model' => JenisBarang::class, 'kolom' => 'nama_jenis', 'label' => 'Jenis Barang'],
        'vendor' => ['model' => Vendor::class, 'kolom' => 'nama_vendor', 'label' => 'Vendor'],
        'unit' => ['model' => UnitBagian::class, 'kolom' => 'nama_unit', 'label' => 'Unit/Bagian'],
    ];

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $tab = $request->get('tab', 'gedung');
        if (!array_key_exists($tab, $this->peta)) {
            abort(404);
        }

        $konfig = $this->peta[$tab];
        $data = $konfig['model']::orderBy($konfig['kolom'])->paginate(10)->withQueryString();

        return view('master-data.index', [
            'tab' => $tab,
            'data' => $data,
            'konfig' => $konfig,
            'daftarTab' => $this->peta,
        ]);
    }

    public function store(Request $request, string $tipe)
    {
        $this->authorizeAdmin();
        $konfig = $this->ambilKonfig($tipe);

        $validated = $request->validate([
            $konfig['kolom'] => 'required|string|max:150',
            'kontak' => 'nullable|string|max:100', // khusus vendor, diabaikan model lain
            'masa_pakai_tahun' => 'nullable|integer|min:1|max:50', // khusus jenis-barang
        ]);

        $konfig['model']::create($validated);

        return redirect()->route('master-data.index', ['tab' => $tipe])->with('success', $konfig['label'] . ' berhasil ditambahkan.');
    }

    public function update(Request $request, string $tipe, int $id)
    {
        $this->authorizeAdmin();
        $konfig = $this->ambilKonfig($tipe);

        $item = $konfig['model']::findOrFail($id);

        $validated = $request->validate([
            $konfig['kolom'] => 'required|string|max:150',
            'kontak' => 'nullable|string|max:100',
            'masa_pakai_tahun' => 'nullable|integer|min:1|max:50',
        ]);

        $item->update($validated);

        return redirect()->route('master-data.index', ['tab' => $tipe])->with('success', $konfig['label'] . ' berhasil diperbarui.');
    }

    public function destroy(string $tipe, int $id)
    {
        $this->authorizeAdmin();
        $konfig = $this->ambilKonfig($tipe);

        try {
            $konfig['model']::findOrFail($id)->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withErrors(['umum' => $konfig['label'] . ' tidak dapat dihapus karena masih digunakan oleh data lain.']);
        }

        return redirect()->route('master-data.index', ['tab' => $tipe])->with('success', $konfig['label'] . ' berhasil dihapus.');
    }

    private function ambilKonfig(string $tipe): array
    {
        if (!array_key_exists($tipe, $this->peta)) {
            abort(404);
        }

        return $this->peta[$tipe];
    }

    private function authorizeAdmin(): void
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya Admin yang dapat mengelola master data.');
        }
    }
}