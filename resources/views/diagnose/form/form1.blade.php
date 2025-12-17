@extends('layouts.dashboard.main')

@section('content')

    {{-- Tampilkan error bila ada --}}
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
        <h5 class="card-header">Identitas Sistem Elektronik yang akan dianalisis</h5>
        <div class="card-body">
            <form action="{{ route('diagnose.form.form1.store') }}" method="post">
                @csrf

                {{-- ================================================
                     DATA DYNAMIC MODEL (AUTO MUNCUL DARI app/Models)
                     ================================================= --}}
                @foreach ($modelsData as $model)
                    <div class="mb-3">
                        <label class="form-label">
                            {{ ucwords(str_replace('_', ' ', $model['modelName'])) }}
                        </label>

                        <select class="form-control js-example-basic-single"
                                name="{{ $model['variableName'] }}">
                            
                            <option value="">-- Pilih {{ $model['modelName'] }} --</option>

                            @foreach ($model['rows'] as $row)
                                <option value="{{ $row->id }}"
                                    {{ old($model['variableName']) == $row->id ? 'selected' : '' }}>
                                    {{ $row->{$model['labelColumn']} }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

                {{-- ========================
                    NAMA SISTEM
                ========================= --}}
                <div class="mb-3">
                    <label for="nama_sistem" class="form-label">Nama Sistem</label>
                    <input type="text" name="nama_sistem" class="form-control" id="nama_sistem"
                        value="{{ old('nama_sistem') ?? @$data_form1['nama_sistem'] }}"
                        placeholder="Masukkan nama sistem...">
                </div>

                {{-- ========================
                    DESKRIPSI SISTEM
                ========================= --}}
                <div class="mb-3">
                    <label for="deskripsi_sistem" class="form-label">Deskripsi Sistem</label>
                    <textarea class="form-control" name="deskripsi_sistem" id="deskripsi_sistem" rows="3"
                        placeholder="Jelaskan fungsi sistem secara singkat...">{{ old('deskripsi_sistem') ?? @$data_form1['deskripsi_sistem'] }}</textarea>
                </div>

                {{-- =============================
                    RADIO KETERHUBUNGAN SISTEM
                ============================== --}}
                <div class="mb-3 form-check">
                    <label class="form-check-label" for="kesamaan_sistem">Apakah Sistem memiliki keterhubungan?</label>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="kesamaan_sistem"
                            id="kesamaan_sistem_ya" value="1"
                            {{ (old('kesamaan_sistem') ?? @$data_form1['kesamaan_sistem']) == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="kesamaan_sistem_ya">Ya</label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="kesamaan_sistem"
                            id="kesamaan_sistem_tidak" value="0"
                            {{ (old('kesamaan_sistem') ?? @$data_form1['kesamaan_sistem']) == '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="kesamaan_sistem_tidak">Tidak</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Berikutnya</button>
            </form>
        </div>
    </div>

@endsection
