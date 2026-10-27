<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UnitBagian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('unit');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->filled('cari')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->cari . '%')
                    ->orWhere('email', 'like', '%' . $request->cari . '%');
            });
        }

        // Akun pending ditampilkan paling atas supaya tidak terlewat
        $pengguna = $query->orderByRaw("FIELD(status, 'pending', 'aktif', 'nonaktif')")
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        $unitList = UnitBagian::orderBy('nama_unit')->get();
        $jumlahPending = User::where('status', 'pending')->count();
        $totalPengguna = User::count();
        $totalAktif = User::where('status', 'aktif')->count();
        $totalNonaktif = User::where('status', 'nonaktif')->count();

        $roleCounts = [
            'admin' => User::where('role', 'admin')->count(),
            'dosen_tendik' => User::where('role', 'dosen_tendik')->count(),
            'teknisi' => User::where('role', 'teknisi')->count(),
            'pimpinan' => User::where('role', 'pimpinan')->count(),
        ];

        if ($request->ajax()) {
            return view('pengguna._table', compact('pengguna'));
        }

        return view('pengguna.index', compact(
            'pengguna',
            'unitList',
            'jumlahPending',
            'totalPengguna',
            'totalAktif',
            'totalNonaktif',
            'roleCounts'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,dosen_tendik,teknisi,pimpinan',
            'unit_id' => 'nullable|exists:unit_bagian,unit_id',
        ]);

        User::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'unit_id' => $validated['unit_id'] ?? null,
            'status' => 'aktif',
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $pengguna)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email,' . $pengguna->user_id . ',user_id',
            'role' => 'required|in:admin,dosen_tendik,teknisi,pimpinan',
            'unit_id' => 'nullable|exists:unit_bagian,unit_id',
            'status' => 'required|in:pending,aktif,nonaktif',
        ]);

        // Cegah admin menonaktifkan/downgrade akun sendiri, biar tidak terkunci sistem
        if ($pengguna->user_id === auth()->id() && $validated['status'] !== 'aktif') {
            return back()->withErrors(['status' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.']);
        }

        $pengguna->update($validated);

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->user_id === auth()->id()) {
            return back()->withErrors(['umum' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $pengguna->delete();

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}