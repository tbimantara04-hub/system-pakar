<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RefJenisTatakelola;
use Illuminate\Support\Facades\Log;

class DiagnoseFormController extends Controller
{
    public function index()
    {
        return redirect()->route('diagnose.form.form1');
    }

    public function form1()
    {
        return view('diagnose.form.form1', [
            'data_form1' => session('form1')
        ]);
    }

    public function form1Store(Request $request)
    {
        session(['form1' => $request->all()]);
        Log::info('FORM1 DISIMPAN', $request->all());
        return redirect()->route('diagnose.form.form2');
    }

    public function form2()
    {
        return view('diagnose.form.form2', [
            'data_form2' => session('form2')
        ]);
    }

    public function form2Store(Request $request)
    {
        session(['form2' => $request->all()]);
        Log::info('FORM2 DISIMPAN', $request->all());
        return redirect()->route('diagnose.form.form3');
    }

    public function form3()
    {
        return view('diagnose.form.form3', [
            'data_form3' => session('form3')
        ]);
    }

    public function form3Store(Request $request)
    {
        session(['form3' => $request->all()]);
        Log::info('FORM3 DISIMPAN', $request->all());
        return redirect()->route('diagnose.form.form4');
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
    // 4. FINAL DEBUG (OPTIONAL)
    // Aktifkan jika ingin berhenti di sini
    // =========================
    // dd('✅ FORM4 BERHASIL DISIMPAN', session('form4'));

    // =========================
    // 5. LANJUT KE RESULT
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

        if (!$form4) {
            Log::error('RESULT: Form4 kosong, redirect ke form4');
            return redirect()->route('diagnose.form.form4')
                ->withErrors('Data Form 4 belum lengkap. Pastikan semua field diisi.');
        }

        Log::info('MENUJU RESULT', compact('form1','form2','form3','form4'));

        $hasil = [
            'form1' => $form1,
            'form2' => $form2,
            'form3' => $form3,
            'form4' => $form4,
        ];

        return view('diagnose.form.result', compact('hasil'));
    }

    public function reset()
    {
        session()->forget(['form1','form2','form3','form4']);
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