@php
    $title = $interdepen->exists ? 'Edit' : 'Tambah';
    $route = $interdepen->exists ? route('interdepen.update', $interdepen->id) : route('interdepen.store');
    $method = $interdepen->exists ? 'PUT' : 'POST';
@endphp

@extends('layouts.dashboard.main')

@section('content')
    <div class="pb-3">
        <h3>{{ $title }} Interdepen </h3>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col">
            <div class="card mb-grid">
                <div class="card-body">
                    <form action="{{ $route }}" method="POST">
                        @csrf
                        @method($method)
                        
                        {{-- REF INTERDEPEN (JENIS HUBUNGAN) --}}
                        <div class="form-group">
                            <label class="form-label" for="ref_interdepen_id">Jenis Interdepen</label>
                            <select class="form-control js-example-basic-single" name="ref_interdepen_id" id="ref_interdepen_id">
                                <option value="">Pilih Jenis Interdepen</option>
                                @foreach ($refInterdepens as $ref)
                                    <option value="{{ $ref->id }}" @selected($ref->id == (old('ref_interdepen_id') ?? $interdepen->ref_interdepen_id))>
                                        {{ $ref->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SISTEM ELEKTRONIK (ASAL) --}}
                        <div class="form-group">
                            <label class="form-label" for="sistem_elektronik_id">Sistem Elektronik (Asal)</label>
                            <select class="form-control js-example-basic-single" name="sistem_elektronik_id" id="sistem_elektronik_id">
                                <option value="">Pilih Sistem Elektronik</option>
                                @foreach ($iivs as $iiv)
                                    <option value="{{ $iiv->id }}" @selected($iiv->id == (old('sistem_elektronik_id') ?? $interdepen->sistem_elektronik_id))>
                                        {{ $iiv->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SISTEM IIV (TUJUAN) --}}
                        <div class="form-group">
                            <label class="form-label" for="sistem_iiv_id">Sistem IIV (Tujuan)</label>
                            <select class="form-control js-example-basic-single" name="sistem_iiv_id" id="sistem_iiv_id">
                                <option value="">Pilih Sistem Tujuan</option>
                                @foreach ($iivs as $iiv)
                                    <option value="{{ $iiv->id }}" @selected($iiv->id == (old('sistem_iiv_id') ?? $interdepen->sistem_iiv_id))>
                                        {{ $iiv->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="form-group">
                            <label class="form-label" for="deskripsi_interdepen">Deskripsi</label>
                            <textarea class="form-control" id="deskripsi_interdepen" name="deskripsi_interdepen" rows="3" placeholder="Deskripsi Keterhubungan">{{ old('deskripsi_interdepen') ?? $interdepen->deskripsi_interdepen }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2({
                width: '100%'
            });
        });
    </script>
@endpush
