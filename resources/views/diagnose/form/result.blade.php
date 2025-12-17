@extends('layouts.dashboard.main')

@section('content')
    {{-- Tampilkan Error jika ada (Jaga-jaga) --}}
    @if ($errors->any())
        <div class="alert alert-danger mt-5">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mt-5">
        <h5 class="card-header">Hasil Diagnosa (Result)</h5>
        <div class="card-body">
            {{-- Bagian 1: Info Sistem --}}
            <div class="mb-3">
                <label for="nama_sistem" class="form-label">Nama Sistem</label>
                <input type="text" name="nama_sistem" class="form-control" id="nama_sistem"
                    value="{{ $diagnose_data['form1']['nama_sistem'] ?? '-' }}" readonly>
            </div>

            <div class="mb-3">
                <label for="deskripsi_sistem" class="form-label">Deskripsi Sistem</label>
                <textarea class="form-control" name="deskripsi_sistem" id="deskripsi_sistem" rows="3" readonly>{{ $diagnose_data['form1']['deskripsi_sistem'] ?? '-' }}</textarea>
            </div>

            <hr>

            {{-- Bagian 2: Skor Interdependensi --}}
            <h5>Perolehan Nilai Interdependensi Sistem</h5>
            <p class="text-muted small">Berdasarkan input form sebelumnya.</p>
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Nama Sistem Terinput</th>
                            <th scope="col">Nilai Poin</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Cek apakah data ada dan berbentuk array --}}
                        @if(isset($diagnose_data['form2']['poin_order']) && is_array($diagnose_data['form2']['poin_order']) && count($diagnose_data['form2']['poin_order']) > 0)
                            
                            {{-- Gunakan nama variabel $poin_item (bukan $poin_order) untuk menghindari konflik --}}
                            @foreach ($diagnose_data['form2']['poin_order'] as $poin_item)
                                @if(isset($poin_item['sistem']) && is_array($poin_item['sistem']))
                                    @foreach ($poin_item['sistem'] as $nilai_sistem)
                                        <tr>
                                            <td>{{ $nilai_sistem }}</td>
                                            {{-- Akses poin dari $poin_item --}}
                                            <td>{{ $poin_item['poin'] ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                            
                        @else
                            <tr>
                                <td colspan="2" class="text-center">Tidak ada data poin interdependensi atau Form 2 dilewati.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <br>

            {{-- Bagian 3: Sistem Rekomendasi (Hasil Akhir) --}}
            <h4 class="text-primary">Rekomendasi Sistem Terpilih</h4>
            <p>Sistem yang memiliki nilai keterhubungan paling tinggi:</p>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th scope="col">Nama Sistem</th>
                            {{-- Tampilkan kolom Instansi hanya jika data IIV tersedia --}}
                            @if (isset($iiv) && count($iiv) > 0)
                                <th scope="col">Instansi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Skenario 1: Data dari Database (IIV) --}}
                        @if (isset($iiv) && count($iiv) > 0)
                            @foreach ($iiv as $item)
                                <tr>
                                    <td><strong>{{ $item->nama }}</strong></td>
                                    <td>{{ $item->refInstansi->nama_instansi ?? '-' }}</td>
                                </tr>
                            @endforeach
                        
                        {{-- Skenario 2: Data dari Session (Fallback) --}}
                        @elseif (isset($diagnose_data['sistem_terpilih']) && is_array($diagnose_data['sistem_terpilih']) && count($diagnose_data['sistem_terpilih']) > 0)
                            @foreach ($diagnose_data['sistem_terpilih'] as $nama)
                                <tr>
                                    <td><strong>{{ $nama }}</strong></td>
                                </tr>
                            @endforeach

                        {{-- Skenario 3: Tidak ada data --}}
                        @else
                            <tr>
                                <td colspan="2" class="text-center text-danger">
                                    <em>Belum bisa memberikan rekomendasi (Data tidak cukup atau tidak ditemukan).</em>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <br>
            
            {{-- Tombol Aksi --}}
            <div class="row text-center mt-4">
                <div class="col">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">Kembali ke Dashboard</a>
                    
                    {{-- Tombol Detail hanya muncul jika ada hasil --}}
                    @if ((isset($iiv) && count($iiv) > 0) || (isset($diagnose_data['sistem_terpilih']) && count($diagnose_data['sistem_terpilih']) > 0))
                        <a href="{{ route('diagnose.form.result2') }}" class="btn btn-success">Lihat Detail Lanjut</a>
                    @endif
                </div>
            </div>

        </div>  
    </div>
@endsection