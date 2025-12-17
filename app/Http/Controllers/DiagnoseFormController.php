<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RefJenisTatakelola;
use App\Models\RefInstansi;
use App\Models\RefTujuan;
use App\Models\RefDampak;
use App\Models\RefFungsi;
use App\Models\RefInterdepen;
use App\Models\IIV; // Menggantikan Iiv agar sesuai dengan standar penamaan kelas
use Illuminate\Support\Facades\Log;

class DiagnoseFormController extends Controller
{
    public function index()
    {
        return redirect()->route('diagnose.form.form1');
    }

    // ==========================================================
    // FORM 1 (IDENTITAS)
    // ==========================================================
    public function form1()
    {
        // Ambil data referensi yang dibutuhkan untuk Form 1
        $instansis = []; // RefInstansi
        $tujuans = [];   // RefTujuan
        $dampaks = [];   // RefDampak

        try {
            if (class_exists(RefInstansi::class)) {
                $instansis = RefInstansi::all();
            }
            if (class_exists(RefTujuan::class)) {
                $tujuans = RefTujuan::all();
            }
            if (class_exists(RefDampak::class)) {
                $dampaks = RefDampak::all();
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data referensi Form 1: ' . $e->getMessage());
        }
        
        // Kirim semua variabel ke View
        return view('diagnose.form.form1', [
            'data_form1' => session('form1'),
            'instansis' => $instansis, // Variabel diubah dari $ref_instansi menjadi $instansis
            'tujuans' => $tujuans,
            'dampaks' => $dampaks
        ]);
    }

    public function form1Store(Request $request)
    {
        // TODO: Tambahkan validasi Request::validate(...) di sini
        session(['form1' => $request->all()]);
        Log::info('FORM1 DISIMPAN', $request->all());
        return redirect()->route('diagnose.form.form2');
    }

    // ==========================================================
    // FORM 2 (INTERDEPENDENSI, TUJUAN, SUMBER DAYA)
    // ==========================================================
    public function form2()
    {
        $fungsis = []; // RefFungsi
        $interdepens = []; // RefInterdepen
        $sistemElektroniks = []; // Data sistem elektronik lain (IIV)

        try {
            if (class_exists(RefFungsi::class)) {
                $fungsis = RefFungsi::all();
            }
            if (class_exists(RefInterdepen::class)) {
                $interdepens = RefInterdepen::all();
            }
            // Asumsi untuk interdependensi, kita butuh daftar sistem lain yang sudah diinput
            if (class_exists(IIV::class)) {
                $sistemElektroniks = IIV::all(); 
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data referensi Form 2: ' . $e->getMessage());
        }

        return view('diagnose.form.form2', [
            'data_form2' => session('form2'),
            'fungsis' => $fungsis,
            'interdepens' => $interdepens,
            'sistemElektroniks' => $sistemElektroniks,
        ]);
    }

    public function form2Store(Request $request)
    {
        // TODO: Tambahkan validasi Request::validate(...) di sini
        session(['form2' => $request->all()]);
        Log::info('FORM2 DISIMPAN', $request->all());
        return redirect()->route('diagnose.form.form3');
    }

    // ==========================================================
    // FORM 3 (RISIKO)
    // ==========================================================
    public function form3()
    {
        // Asumsi Form 3 membutuhkan data Risiko, Kendali, dll.
        // Data Risiko dan Kendali biasanya diisi, bukan dipilih, tapi kita siapkan datanya jika ada referensi
        
        return view('diagnose.form.form3', [
            'data_form3' => session('form3')
        ]);
    }

    public function form3Store(Request $request)
    {
        // TODO: Tambahkan validasi Request::validate(...) di sini
        session(['form3' => $request->all()]);
        Log::info('FORM3 DISIMPAN', $request->all());
        return redirect()->route('diagnose.form.form4');
    }

    // ==========================================================
    // FORM 4 (TATA KELOLA)
    // ==========================================================
    public function form4()
    {
        // Ambil data Tata Kelola untuk checklist di Form 4
        $allTatakelola = [];
        try {
            if (class_exists(RefJenisTatakelola::class)) {
                $allTatakelola = RefJenisTatakelola::all();
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengambil RefJenisTatakelola: ' . $e->getMessage());
        }

        return view('diagnose.form.form4', [
            'data_form4' => session('form4'),
            'allTatakelola' => $allTatakelola
        ]);
    }

    public function form4Store(Request $request)
    {
        // =========================
        // 1. CEK APAKAH MASUK CONTROLLER
        // =========================
        \Log::info('FORM4 >>> REQUEST MASUK', $request->all());

        // =========================
        // 2. VALIDASI SESUAI FORM
        // =========================
        $validated = $request->validate([
            'kriteria_pendanaan_pengamanan'     => 'required|array|min:1',
            'kriteria_pendanaan_pemulihan'      => 'required|array|min:1',
            'kriteria_pendanaan_pendukung'      => 'required|array|min:1',
            'kriteria_keterampilan_pengamanan'  => 'required|array|min:1',
            'kriteria_keterampilan_identifikasi'=> 'required|array|min:1',
            'kesadaran_interdepen'              => 'required|array|min:1',
            'kriteria_kesadaran_risiko'         => 'required|array|min:1',

            'regulasi_tujuan'   => 'required|in:0,1',
            'regulasi_fungsi'   => 'required|in:0,1',
            'regulasi_risiko'   => 'required|in:0,1',
            'standart_fungsi'   => 'required|in:0,1',
            'standart_aplikasi' => 'required|in:0,1',
            'alur_tujuan'       => 'required|in:0,1',
            'alur_fungsi'       => 'required|in:0,1',
            'alur_risiko'       => 'required|in:0,1',
            'alur_aplikasi'     => 'required|in:0,1',
        ]);

        \Log::info('FORM4 >>> VALIDASI LOLOS', $validated);

        // =========================
        // 3. SIMPAN KE SESSION
        // =========================
        session([
            'form4' => $validated
        ]);

        \Log::info('FORM4 >>> SESSION TERSIMPAN', session('form4'));

        // =========================
        // 4. LANJUT KE RESULT
        // =========================
        return redirect()->route('diagnose.form.result');
    }


    public function result()
    {
        $form1 = session('form1');
        $form2 = session('form2');
        $form3 = session('form3');
        $form4 = session('form4');

        // Tambahan logging untuk debug: Cek apakah session ada
        Log::info('RESULT: Cek Session', [
            'form1' => $form1 ? 'ada' : 'kosong',
            'form2' => $form2 ? 'ada' : 'kosong',
            'form3' => $form3 ? 'ada' : 'kosong',
            'form4' => $form4 ? 'ada' : 'kosong',
        ]);

        // Cek kelengkapan data
        if (!$form4) {
            Log::error('RESULT: Form4 kosong, redirect ke form4');
            return redirect()->route('diagnose.form.form4')
                ->withErrors('Data Form 4 belum lengkap. Pastikan semua field diisi.');
        }

        $diagnose_data = [
            'form1' => $form1,
            'form2' => $form2,
            'form3' => $form3,
            'form4' => $form4,
            'sistem_terpilih' => session('sistem_terpilih', [])
        ];

        $iiv = [];
        try {
            if (class_exists(IIV::class)) {
                $iiv = IIV::with('refInstansi')->get();
            }
        } catch (\Exception $e) {
            Log::error('Gagal mengambil data IIV: ' . $e->getMessage());
        }

        Log::info('MENUJU RESULT', compact('diagnose_data', 'iiv'));

        return view('diagnose.form.result', compact('diagnose_data', 'iiv'));
    }

    public function reset()
    {
        session()->forget(['form1','form2','form3','form4', 'sistem_terpilih']);
        return redirect()->route('diagnose.form.form1');
    }

    public function print()
    {
        $hasil = [
            'form1' => session('form1'),
            'form2' => session('form2'),
            'form3' => session('form3'),
            'form4' => session('form4'),
        ];

        return view('diagnose.form.print', compact('hasil'));
    }
}