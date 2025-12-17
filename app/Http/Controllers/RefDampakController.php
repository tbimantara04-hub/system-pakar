<?php

namespace App\Http\Controllers;

use App\DataTables\RefDampakDataTable;
use App\Models\RefDampak;
use Illuminate\Http\Request;

class RefDampakController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(RefDampakDataTable $dataTable)
    {
        return $dataTable->render('pages.ref-dampak.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $refDampak = new RefDampak();
        return view('pages.ref-dampak.form', compact('refDampak'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'indikator_dampak' => ['required', 'string', 'max:255']
        ]);

        RefDampak::create($data);

        return to_route('ref-dampak.index')
            ->with('success', 'Data berhasil disimpan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RefDampak $refDampak)
    {
        return view('pages.ref-dampak.form', compact('refDampak'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RefDampak $refDampak)
    {
        $data = $request->validate([
            'indikator_dampak' => ['required', 'string', 'max:255']
        ]);

        $refDampak->update($data);

        return to_route('ref-dampak.index')
            ->with('success', 'Data berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RefDampak $refDampak)
    {
        $refDampak->delete();

        return to_route('ref-dampak.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
