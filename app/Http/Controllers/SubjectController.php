<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = DB::table('subjects')->get();
        return view('admin.subjects', compact('subjects'));
    }

    public function store(Request $request)
    {
        DB::table('subjects')->insert([
            'name' => $request->name,
            'jenjang' => $request->jenjang,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Mata Pelajaran berhasil ditambah!');
    }

    public function update(Request $request, $id)
    {
        DB::table('subjects')->where('id', $id)->update([
            'name' => $request->name,
            'jenjang' => $request->jenjang,
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Mata Pelajaran berhasil diupdate!');
    }

    public function destroy($id)
    {
        DB::table('subjects')->where('id', $id)->delete();
        return back()->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}