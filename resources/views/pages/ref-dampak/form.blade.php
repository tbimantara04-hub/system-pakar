@php
$title = $refDampak->exists ? 'Edit' : 'Tambah';
$route = $refDampak->exists ? 
route('ref-dampak.update', $refDampak->id) :
route('ref-dampak.store');
$method = $refDampak->exists ? 'PUT' : 'POST';
@endphp


@extends('layouts.dashboard.main')

@section('content')
<div class="pb-3">
    <h3>{{ $title }} Ref Dampak</h3>
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
                    <div class="form-group">
                        <label class="form-label" for="indikator_dampak">Indikator Dampak</label>
                        <input type="text" class="form-control" id="indikator_dampak" name="indikator_dampak" value="{{ old('indikator_dampak') ?? $refDampak->indikator_dampak }}" placeholder="Indikator Dampak">
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
