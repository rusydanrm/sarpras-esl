<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Gedung;
use App\Exports\LaporanBarangExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->only(['gedung_id', 'kondisi', 'sumber_dana']);

        $query = Barang::with(['ruangan.gedung']);
        $this->terapkanFilter($query, $filter);

        $preview = $query->orderBy('nama_barang')->paginate(10)->withQueryString();
        $gedungList = Gedung::orderBy('nama_gedung')->get();

        if ($request->ajax()) {
            return view('laporan._table', compact('preview'));
        }

        return view('laporan.index', compact('preview', 'gedungList', 'filter'));
    }

    public function eksporExcel(Request $request)
    {
        $filter = $request->only(['gedung_id', 'kondisi', 'sumber_dana']);

        return ExcelFacade::download(new LaporanBarangExport($filter), 'laporan-sarpras-' . now()->format('Ymd-His') . '.xlsx');
    }

    public function eksporPdf(Request $request)
    {
        $filter = $request->only(['gedung_id', 'kondisi', 'sumber_dana']);

        $query = Barang::with(['ruangan.gedung']);
        $this->terapkanFilter($query, $filter);
        $data = $query->orderBy('nama_barang')->get();

        $pdf = Pdf::loadView('laporan.pdf', ['data' => $data, 'tanggal' => now()->format('d/m/Y H:i')]);

        return $pdf->download('laporan-sarpras-' . now()->format('Ymd-His') . '.pdf');
    }

    private function terapkanFilter($query, array $filter): void
    {
        if (!empty($filter['gedung_id'])) {
            $query->whereHas('ruangan', fn($q) => $q->where('gedung_id', $filter['gedung_id']));
        }
        if (!empty($filter['kondisi'])) {
            if ($filter['kondisi'] === 'Baik') {
                $query->where('kondisi_baik', '>', 0);
            } elseif ($filter['kondisi'] === 'Rusak Ringan') {
                $query->where('kondisi_rusak_ringan', '>', 0);
            } elseif ($filter['kondisi'] === 'Rusak Berat') {
                $query->where('kondisi_rusak_berat', '>', 0);
            }
        }
        if (!empty($filter['sumber_dana'])) {
            $query->where('sumber_dana', 'like', '%' . $filter['sumber_dana'] . '%');
        }
    }
}