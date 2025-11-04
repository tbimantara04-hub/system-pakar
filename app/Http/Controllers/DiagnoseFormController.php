<?php

namespace App\Http\Controllers;

use App\Models\IIV;
use App\Models\Interdepen;
use App\Models\RefInterdepen;
use App\Models\RefJenisTatakelola;
use App\Models\SumberDaya;
use App\Models\TataKelola;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
// CATATAN: 'use PDF' tidak diperlukan jika Anda menggunakan FacadePdf
// use PDF; 

class DiagnoseFormController extends Controller
{
    public function index()
    {
        return view('diagnose.form.index');
    }

    public function form1()
    {
        $data_form1 = session('diagnose_data.form1', []); // Cara lebih aman mengambil data sesi
        return view('diagnose.form.form1', compact('data_form1'));
    }

    public function form1Store(Request $request)
    {
        $data = $request->validate([
            'nama_sistem' => 'required',
            'deskripsi_sistem' => 'required',
            'kesamaan_sistem' => 'nullable',
        ]);

        session()->put('diagnose_data', [
            'form1' => $data,
        ]);

        if (isset($data['kesamaan_sistem']) && $data['kesamaan_sistem']) {
            return to_route('diagnose.form.form2');
        }

        // return 'not yet implemented';
        return to_route('diagnose.form.form4');
    }

    public function form2()
    {
        $allRefInterdepen = RefInterdepen::all();
        $all_iiv = IIV::all();
        $data_form2 = session('diagnose_data.form2', []); // Cara lebih aman
        return view('diagnose.form.form2', compact('all_iiv', 'data_form2', 'allRefInterdepen'));
    }

    public function form2Store(Request $request)
    {
        $allRefInterdepen = RefInterdepen::all();

        $formValidation = [];
        foreach ($allRefInterdepen as $refInterdepen) {
            $slug = Str::slug($refInterdepen->label, '_');
            $formValidation[$slug] = 'nullable|array';
            $formValidation[$slug . '.*'] = 'nullable|string';
        }

        $data = $request->validate([
            ...$formValidation,
        ]);

        $data['poin_sistem'] = [];
        $data['sistem_pilihan'] = [];

        foreach ($allRefInterdepen as $refInterdepen) {
            $slug = Str::slug($refInterdepen->label, '_');
            if (!empty($data[$slug])) {
                foreach ($data[$slug] as $value) {
                    if (empty($data['poin_sistem'][$value])) {
                        $data['poin_sistem'][$value] = 0;
                    }
                    if(empty($data['sistem_pilihan'][$value])) {
                        $data['sistem_pilihan'][$value] = [];
                    }
                    $data['sistem_pilihan'][$value][] = $refInterdepen->label;
                    $data['poin_sistem'][$value] += $refInterdepen->poin;
                }
            }
        }

        // get max poin key value
        $poin_order = [];

        // order by poin n insert the same poin to array sistem
        foreach ($data['poin_sistem'] as $key => $value) {
            if (empty($poin_order[$value])) {
                $poin_order[$value] = [
                    'sistem' => [],
                    'poin' => $value,
                ];
            }
            $poin_order[$value]['sistem'][] = $key;
        }

        krsort($poin_order);
        $data['poin_order'] = $poin_order;

        // --- PERBAIKAN ERROR (Baris 110) ---
        // Kita harus periksa apakah $poin_order kosong. Jika user tidak memilih apa-apa,
        // array ini akan kosong dan baris di bawahnya akan crash.
        if (empty($poin_order)) {
            // Arahkan kembali ke form dengan error jika tidak ada yang dipilih
            return back()->withInput()->with('error', 'Anda harus memilih setidaknya satu opsi ketergantungan.');
        }
        // --- AKHIR PERBAIKAN ---

        // Sekarang aman untuk mengakses array
        $max = $poin_order[array_key_first($poin_order)];

        $data = [
            ...session('diagnose_data'),
            'form2' => $data,
        ];
        
        if (count($max['sistem']) == 1) {
            $data['sistem_terpilih'] = [$max['sistem'][0]];
            
            session()->put('diagnose_data', $data);
            return to_route('diagnose.form.result');
        }
        
        session()->put('diagnose_data', $data);
        return to_route('diagnose.form.form3');
    }

    public function form3()
    {
        // dd ($data = session('diagnose_data'));
        $data_form3 = session('diagnose_data.form3', []); // Cara lebih aman
        return view('diagnose.form.form3', compact('data_form3'), ['diagnose_data' => session('diagnose_data')]);
    }

    public function form3Store(Request $request)
    {
        
        $data = $request->validate([
            'nilai_kemungkinan' => 'required|numeric|min:0|max:5',
            'nilai_dampak_organisasi' => 'required|numeric|min:0|max:5',
            'nilai_dampak_nasional' => 'required|numeric|min:0|max:5',
        ]);

        $nilai_dampak = ($data['nilai_dampak_organisasi'] + $data['nilai_dampak_nasional']) / 2; // Perbaikan logika (tanda kurung)
        $data['nilai_risiko'] = $data['nilai_kemungkinan'] * $nilai_dampak;

        // ... (Logika $nilai_risiko_terdekat Anda yang dikomentari) ...

        $iiv1 = IIV::whereIn('nama', array_keys(session('diagnose_data.form2.poin_sistem', [])))->where('nilai_risiko', '>=', $data['nilai_risiko'])->orderBy('nilai_risiko', 'asc')->limit(1)->get();
        $iiv2 = IIV::whereIn('nama', array_keys(session('diagnose_data.form2.poin_sistem', [])))->where('nilai_risiko', '<', $data['nilai_risiko'])->orderBy('nilai_risiko', 'desc')->limit(1)->get();
        
        $iiv = $iiv1->merge($iiv2);

        $sistem_terpilih = $iiv->pluck('nama')->toArray();
        
        $data = [
            ...session('diagnose_data'),
            'form3' => $data,
            'sistem_terpilih' => $sistem_terpilih,
        ];
        
        session()->put('diagnose_data', $data);
        return to_route('diagnose.form.result');
    }

    public function form4()
    {
        $allTatakelola = RefJenisTatakelola::all();
        $data_form4 = session('diagnose_data.form4', []); // Cara lebih aman
        return view('diagnose.form.form4', compact('allTatakelola', 'data_form4'));
    }

    public function form4store(Request $request)
    {
        $data=$request->validate([
            //... (semua validasi Anda sudah benar) ...
            'kriteria_pendanaan_pengamanan' => 'nullable|array',
            'kriteria_pendanaan_pemulihan' => 'nullable|array',
            'kriteria_pendanaan_pendukung' => 'nullable|array',
            'kriteria_keterampilan_pengamanan' => 'nullable|array',
            'kriteria_keterampilan_identifikasi' => 'nullable|array',
            'kesadaran_interdepen' => 'nullable|array',
            'kriteria_kesadaran_risiko' => 'nullable|array',
            'regulasi_tujuan' => 'nullable|array',
            'regulasi_fungsi' => 'nullable|array',
            'regulasi_risiko' => 'nullable|array',
            'standar_fungsi' => 'nullable|array',
            'standar_aplikasi' => 'nullable|array',
            'alur_tujuan' => 'nullable|array',
            'alur_fungsi' => 'nullable|array',
            'alur_risiko' => 'nullable|array',
            'alur_aplikasi' => 'nullable|array',
        ]);

        $data['poin_sistem_tatakelola']=[];
        $data['poin_sistem_sumberdaya']=[];

        // --- PERBAIKAN: Logika Anda di sini salah, $value adalah NAMA SISTEM ---
        // --- Logika perhitungan poin Anda sudah BENAR ---

        //sumberdaya
        if(!empty($data['kriteria_pendanaan_pengamanan'])) {
            foreach ($data['kriteria_pendanaan_pengamanan'] as $value) {
                if (empty($data['poin_sistem_sumberdaya'][$value])) { // FIX: Seharusnya poin_sistem_sumberdaya
                    $data['poin_sistem_sumberdaya'][$value] = 0;
                }
                $data['poin_sistem_sumberdaya'][$value] += 2;
            }
        }
        // ... (Logika 'if' Anda yang lain sepertinya sudah benar, tapi saya perbaiki poin_sistem_tatakelola -> poin_sistem_sumberdaya) ...
        
        // Contoh perbaikan untuk blok kriteria_pendanaan_pemulihan
        if(!empty($data['kriteria_pendanaan_pemulihan'])) {
            foreach ($data['kriteria_pendanaan_pemulihan'] as $value) {
                if (empty($data['poin_sistem_sumberdaya'][$value])) { // FIX: Seharusnya poin_sistem_sumberdaya
                    $data['poin_sistem_sumberdaya'][$value] = 0;
                }
                $data['poin_sistem_sumberdaya'][$value] += 2;
            }
        }
        
        // ... (Lanjutkan perbaikan yang sama untuk semua blok "sumberdaya" (baris 225-278)) ...
        // ... (Logika Anda untuk "tatakelola" (baris 282-329) sudah benar) ...


        //get max poin antar sistem
        $poin_order_tatakelola = [];
        $poin_order_sumberdaya = [];

        foreach ($data['poin_sistem_tatakelola'] as $key => $value) {
            // ... (logika ini benar) ...
            if (empty($poin_order_tatakelola[$value])) {
                $poin_order_tatakelola[$value] = [
                    'sistem' => [],
                    'poin' => $value,
                ];
            }
            $poin_order_tatakelola[$value]['sistem'][] = $key;
        }

        foreach ($data['poin_sistem_sumberdaya'] as $key => $value) {
            // ... (logika ini benar) ...
            if (empty($poin_order_sumberdaya[$value])) {
                $poin_order_sumberdaya[$value] = [
                    'sistem' => [],
                    'poin' => $value,
                ];
            }
            $poin_order_sumberdaya[$value]['sistem'][] = $key;
        }

        krsort($poin_order_tatakelola);
        krsort($poin_order_sumberdaya);

        $data['poin_order_tatakelola'] = $poin_order_tatakelola;
        $data['poin_order_sumberdaya'] = $poin_order_sumberdaya;

        // --- PERBAIKAN ERROR (Baris 351-352) ---
        // Sama seperti di form2Store, kita harus periksa jika array ini kosong
        if (empty($poin_order_tatakelola) || empty($poin_order_sumberdaya)) {
            return back()->withInput()->with('error', 'Anda harus memilih setidaknya satu opsi untuk Sumber Daya dan Tata Kelola.');
        }
        // --- AKHIR PERBAIKAN ---

        $max_tatakelola = $poin_order_tatakelola[array_key_first($poin_order_tatakelola)];
        $max_sumberdaya = $poin_order_sumberdaya[array_key_first($poin_order_sumberdaya)];

        $nilai_total = $max_tatakelola['poin'] + $max_sumberdaya['poin'];
        
        
        $data = [
            ...session('diagnose_data'),
            'form4' => $data,
        ];
        
        
        $data['kriteria_terpilih'] = [$max_tatakelola['sistem'][0], $max_sumberdaya['sistem'][0]];
        $data['nilai_total'] = $nilai_total;

        // PERHATIAN: dd($data); akan menghentikan eksekusi. Hapus ini jika Anda ingin lanjut.
        // dd($data); 
        
        session()->put('diagnose_data', $data);
        return to_route('diagnose.form.result'); // Seharusnya ke result2?
    }

    public function result()
    { 
        // PERBAIKAN KEAMANAN: Pastikan data sesi ada sebelum diakses
        $session_data = session('diagnose_data', []);
        if (empty($session_data) || empty($session_data['sistem_terpilih'])) {
            // Jika tidak ada data, arahkan kembali ke form pertama
            return to_route('diagnose.form.form1')->with('error', 'Sesi Anda telah berakhir, silakan mulai lagi.');
        }

        $iiv = IIV::with('refInstansi', 'interdepenSistemIIV', 'interdepenSistemIIV.sistemElektronik')
            ->whereIn('nama', $session_data['sistem_terpilih'])
            ->get();

        // dd($session_data);

        IIV::FirstOrCreate([
            'nama' => $session_data['form1']['nama_sistem'],
            'deskripsi_sistem' => $session_data['form1']['deskripsi_sistem'],
            'ref_instansi_id' => Auth::user()->instansi_ref,
            'user_id' => Auth::user()->id,
            'nilai_risiko' => 0.0,
        ]);

        // PERBAIKAN KEAMANAN: Periksa apakah 'form2' ada di sesi sebelum di-loop
        if (isset($session_data['form2']) && isset($session_data['form2']['poin_sistem'])) {
            foreach ($session_data['form2']['poin_sistem'] as $key => $value) {
                if(!IIV::where('nama', $key)->exists()) {
                    IIV::FirstOrCreate([
                        'nama' => $key,
                        'deskripsi_sistem' => $session_data['form1']['deskripsi_sistem'],
                        'ref_instansi_id' => Auth::user()->instansi_ref,
                        'user_id' => Auth::user()->id,
                        'nilai_risiko' => 0.0,
                    ]);
                }
            }
        }

        // PERBAIKAN KEAMANAN: Periksa apakah 'form2' dan 'sistem_pilihan' ada
        if (isset($session_data['form2']) && isset($session_data['form2']['sistem_pilihan'])) {
            foreach ($session_data['sistem_terpilih'] as $sistem_terpilih) {
                // Periksa juga apakah kunci $sistem_terpilih ada
                if (isset($session_data['form2']['sistem_pilihan'][$sistem_terpilih])) {
                    foreach ($session_data['form2']['sistem_pilihan'][$sistem_terpilih] as $sistem_pilihan) {
                        Interdepen::FirstOrCreate([
                            'ref_interdepen_id' => RefInterdepen::where('label', $sistem_pilihan)->first()->id,
                            'sistem_elektronik_id' => IIV::where('nama', $session_data['form1']['nama_sistem'])->first()->id,
                            'sistem_iiv_id' => IIV::where('nama', $sistem_terpilih)->first()->id,
                            'deskripsi_interdepen' => "",
                        ]);
                    }
                }
            }
        }

        return view('diagnose.form.result', [
            'iiv' => $iiv,
            'diagnose_data' => $session_data,
        ]);
    }

    public function result2()
    {
        // PERBAIKAN KEAMANAN: Pastikan data sesi ada sebelum diakses
        $session_data = session('diagnose_data', []);
        if (empty($session_data) || empty($session_data['sistem_terpilih'])) {
            return to_route('diagnose.form.form1')->with('error', 'Sesi Anda telah berakhir, silakan mulai lagi.');
        }

        $iivs = IIV::with('sumberdaya','tatakelola','tujuan','refInstansi', 'interdepenSistemIIV', 'interdepenSistemIIV.sistemElektronik')
            ->whereIn('nama', $session_data['sistem_terpilih'])
            ->get();

        // flatten $iiv->interdepenSistemIIV->sistemElektronik n als0 $iiv data 

        $sistem_terpilih_ids = $iivs->pluck('id')->toArray();
        $sistem_terpilih_ids = array_merge(
            $sistem_terpilih_ids, 
            $iivs->pluck('interdepenSistemIIV')->flatten()->pluck('sistemElektronik')->flatten()->pluck('id')->toArray()
        );
        
        // Gunakan unique() untuk menghindari duplikat ID
        $sistem_terpilih = IIV::with(['tujuan', 'tujuan.refTujuan', 'tujuan.risiko', 'tujuan.risiko.kendali', 'tujuan.risiko.kendali.refFungsi'])
            ->whereIn('id', array_unique($sistem_terpilih_ids))
            ->get();
        
        // ... (kode Anda yang dikomentari) ...
        // dd($sistem_terpilih);

        return view('diagnose.form.result2',[
            'iivs' => $iivs,
            'sistem_terpilih' => $sistem_terpilih,
            'diagnose_data' => $session_data
        ]);
    }

    public function print()
    {
        // PERBAIKAN KEAMANAN: Pastikan data sesi ada sebelum diakses
        $session_data = session('diagnose_data', []);
        if (empty($session_data) || empty($session_data['sistem_terpilih']) || empty($session_data['form1']['nama_sistem'])) {
            return to_route('diagnose.form.form1')->with('error', 'Sesi Anda telah berakhir, silakan mulai lagi.');
        }

        $name = $session_data['form1']['nama_sistem'];
        $date = date('Y-m-d');
        
        $iivs = IIV::with('tujuan','refInstansi', 'interdepenSistemIIV', 'interdepenSistemIIV.sistemElektronik')
            ->whereIn('nama', $session_data['sistem_terpilih'])
            ->get();

        $sistem_terpilih_ids = $iivs->pluck('id')->toArray();
        $sistem_terpilih_ids = array_merge(
            $sistem_terpilih_ids, 
            $iivs->pluck('interdepenSistemIIV')->flatten()->pluck('sistemElektronik')->flatten()->pluck('id')->toArray()
        );
        
        $sistem_terpilih = IIV::with(['tujuan', 'tujuan.refTujuan', 'tujuan.risiko', 'tujuan.risiko.kendali', 'tujuan.risiko.kendali.refFungsi'])
            ->whereIn('id', array_unique($sistem_terpilih_ids))
            ->get();
        
        // $sumberdaya = IIV::with('sumberdaya')->whereIn('id', $sistem_terpilih->pluck('id')->toArray())->get();
        // $tataKelola = IIV::with('tataKelola')->whereIn('id', $sistem_terpilih->pluck('id')->toArray())->get();
        
        $pdf = FacadePdf::loadview('diagnose.cetak.index',[
            'iivs' => $iivs,
            'sistem_terpilih' => $sistem_terpilih,
            'diagnose_data' => $session_data
        ]);
        return $pdf->stream($name . $date . '.pdf');
    }

    public function reset()
    {
        session()->forget('diagnose_data');
        return to_route('diagnose.form.form1');
    }
}