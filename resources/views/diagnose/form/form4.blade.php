@extends('layouts.dashboard.main')

@section('content')
    {{-- MENAMPILKAN ERROR VALIDASI --}}
    @if ($errors->any())
        <div class="alert alert-danger mt-3 mb-3">
            <h4><i class="icon fas fa-ban"></i> Peringatan!</h4>
            <strong>Mohon lengkapi data berikut agar bisa lanjut:</strong>
            <ul class="mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">Perhitungan Kemiripan Sistem</h5>
        <div class="card-body">
            <form action="{{ route('diagnose.form.form4.store') }}" method="post">
                @csrf
                
                {{-- BAGIAN 1: PENILAIAN TATA KELOLA --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Penilaian Tata Kelola</strong>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Untuk setiap variabel, silakan pilih (Bisa lebih dari satu).
                        </div>

                        {{-- Loop Fields: Pendanaan Pengamanan --}}
                        <div class="mb-3">
                            <label for="kriteria_pendanaan_pengamanan" class="form-label">Kriteria Pendanaan Pengamanan</label>
                            <select class="js-example-basic-multiple form-control" id="kriteria_pendanaan_pengamanan" name="kriteria_pendanaan_pengamanan[]" multiple="multiple">
                                @foreach ($allTatakelola as $tk)
                                    <option value="{{ $tk->deskripsi_jenis }}" @selected(in_array($tk->deskripsi_jenis, old('kriteria_pendanaan_pengamanan') ?? (@$data_form4['kriteria_pendanaan_pengamanan'] ?? [])))>
                                        {{ $tk->deskripsi_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Loop Fields: Pendanaan Pemulihan --}}
                        <div class="mb-3">
                            <label for="kriteria_pendanaan_pemulihan" class="form-label">Kriteria Pendanaan Pemulihan</label>
                            <select class="js-example-basic-multiple form-control" id="kriteria_pendanaan_pemulihan" name="kriteria_pendanaan_pemulihan[]" multiple="multiple">
                                @foreach ($allTatakelola as $tk)
                                    <option value="{{ $tk->deskripsi_jenis }}" @selected(in_array($tk->deskripsi_jenis, old('kriteria_pendanaan_pemulihan') ?? (@$data_form4['kriteria_pendanaan_pemulihan'] ?? [])))>
                                        {{ $tk->deskripsi_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Loop Fields: Pendanaan Pendukung --}}
                        <div class="mb-3">
                            <label for="kriteria_pendanaan_pendukung" class="form-label">Kriteria Pendanaan Pendukung</label>
                            <select class="js-example-basic-multiple form-control" id="kriteria_pendanaan_pendukung" name="kriteria_pendanaan_pendukung[]" multiple="multiple">
                                @foreach ($allTatakelola as $tk)
                                    <option value="{{ $tk->deskripsi_jenis }}" @selected(in_array($tk->deskripsi_jenis, old('kriteria_pendanaan_pendukung') ?? (@$data_form4['kriteria_pendanaan_pendukung'] ?? [])))>
                                        {{ $tk->deskripsi_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Loop Fields: Keterampilan Pengamanan --}}
                        <div class="mb-3">
                            <label for="kriteria_keterampilan_pengamanan" class="form-label">Kriteria Keterampilan Pengamanan</label>
                            <select class="js-example-basic-multiple form-control" id="kriteria_keterampilan_pengamanan" name="kriteria_keterampilan_pengamanan[]" multiple="multiple">
                                @foreach ($allTatakelola as $tk)
                                    <option value="{{ $tk->deskripsi_jenis }}" @selected(in_array($tk->deskripsi_jenis, old('kriteria_keterampilan_pengamanan') ?? (@$data_form4['kriteria_keterampilan_pengamanan'] ?? [])))>
                                        {{ $tk->deskripsi_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Loop Fields: Keterampilan Identifikasi --}}
                        <div class="mb-3">
                            <label for="kriteria_keterampilan_identifikasi" class="form-label">Kriteria Keterampilan Identifikasi</label>
                            <select class="js-example-basic-multiple form-control" id="kriteria_keterampilan_identifikasi" name="kriteria_keterampilan_identifikasi[]" multiple="multiple">
                                @foreach ($allTatakelola as $tk)
                                    <option value="{{ $tk->deskripsi_jenis }}" @selected(in_array($tk->deskripsi_jenis, old('kriteria_keterampilan_identifikasi') ?? (@$data_form4['kriteria_keterampilan_identifikasi'] ?? [])))>
                                        {{ $tk->deskripsi_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                         {{-- Loop Fields: Kesadaran Interdependensi --}}
                         <div class="mb-3">
                            <label for="kesadaran_interdepen" class="form-label">Kesadaran Interdependensi</label>
                            <select class="js-example-basic-multiple form-control" id="kesadaran_interdepen" name="kesadaran_interdepen[]" multiple="multiple">
                                @foreach ($allTatakelola as $tk)
                                    <option value="{{ $tk->deskripsi_jenis }}" @selected(in_array($tk->deskripsi_jenis, old('kesadaran_interdepen') ?? (@$data_form4['kesadaran_interdepen'] ?? [])))>
                                        {{ $tk->deskripsi_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                         {{-- Loop Fields: Kesadaran Risiko --}}
                         <div class="mb-3">
                            <label for="kriteria_kesadaran_risiko" class="form-label">Kriteria Kesadaran Risiko</label>
                            <select class="js-example-basic-multiple form-control" id="kriteria_kesadaran_risiko" name="kriteria_kesadaran_risiko[]" multiple="multiple">
                                @foreach ($allTatakelola as $tk)
                                    <option value="{{ $tk->deskripsi_jenis }}" @selected(in_array($tk->deskripsi_jenis, old('kriteria_kesadaran_risiko') ?? (@$data_form4['kriteria_kesadaran_risiko'] ?? [])))>
                                        {{ $tk->deskripsi_jenis }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 2: PENILAIAN SUMBER DAYA --}}
                <div class="card">
                    <div class="card-header">
                        <strong>Penilaian Sumber Daya</strong>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Pilih <strong>Ya</strong> atau <strong>Tidak</strong>.
                        </div>

                        {{-- Helper Function untuk Radio Button agar rapi --}}
                        @php
                            $radioFields = [
                                'regulasi_tujuan' => 'Regulasi Tujuan',
                                'regulasi_fungsi' => 'Regulasi Fungsi',
                                'regulasi_risiko' => 'Regulasi Risiko',
                                'standart_fungsi' => 'Standart Fungsi',
                                'standart_aplikasi' => 'Standart Aplikasi',
                                'alur_tujuan' => 'Alur Tujuan',
                                'alur_fungsi' => 'Alur Fungsi',
                                'alur_risiko' => 'Alur Risiko',
                                'alur_aplikasi' => 'Alur Aplikasi',
                            ];
                        @endphp

                        @foreach ($radioFields as $field => $label)
                        <div class="mb-3 border-bottom pb-2">
                            <div class="row align-items-center">
                                <div class="col">
                                    <label class="form-label m-0">{{ $label }}</label>
                                </div>
                                <div class="col-auto">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}" id="{{ $field }}_1" value="1" @checked(old($field, @$data_form4[$field]) == '1')>
                                        <label class="form-check-label" for="{{ $field }}_1">Ya</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="{{ $field }}" id="{{ $field }}_0" value="0" @checked(old($field, @$data_form4[$field]) == '0')>
                                        <label class="form-check-label" for="{{ $field }}_0">Tidak</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>

                <br>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">Lihat Hasil Diagnosa <i class="fas fa-arrow-right"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.js-example-basic-multiple').select2({
                theme: "classic", // Opsional, sesuaikan tema
                placeholder: "Pilih salah satu atau lebih...",
                allowClear: true,
                tags: true
            });
        });
    </script>
@endpush