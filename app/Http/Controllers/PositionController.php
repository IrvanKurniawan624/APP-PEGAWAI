<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Validation\ValidationException;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::latest()->paginate(5);
        return view('positions.index', compact('positions'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama_jabatan' => 'required|string|max:255',
                'gaji_pokok' => 'required|numeric',
                'potongan_per_hari' => 'required|numeric',
            ]);

            $position = Position::create($request->all());
            return ApiFormatter::success(200, "Data Berhasil Ditambahkan", $position);

        } catch (ValidationException $e) {
            return ApiFormatter::validate(json_encode($e->errors()));
        } catch (\Exception $e) {
            return ApiFormatter::error(500, "Terjadi kesalahan", $e->getMessage());
        }
    }

    public function show($id)
    {
        $position = Position::find($id);
        if (!$position) {
            return ApiFormatter::error(404, "Data tidak ditemukan");
        }
        return ApiFormatter::success(200, "Detail data berhasil diambil", $position);
    }

    public function edit($id)
    {
        $position = Position::find($id);
        if (!$position) {
            return ApiFormatter::error(404, "Data tidak ditemukan");
        }
        return ApiFormatter::success(200, "Data berhasil diambil untuk edit", $position);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_jabatan' => 'required|string|max:255',
                'gaji_pokok' => 'required|numeric',
                'potongan_per_hari' => 'required|numeric',
            ]);

            $position = Position::find($id);
            if (!$position) {
                return ApiFormatter::error(404, "Data tidak ditemukan");
            }

            $position->update($request->all());
            return ApiFormatter::success(200, "Data berhasil diperbarui", $position);
        } catch (ValidationException $e) {
            return ApiFormatter::validate(json_encode($e->errors()));
        }
    }

    public function destroy($id)
    {
        $position = Position::find($id);
        if (!$position) {
            return ApiFormatter::error(404, "Data tidak ditemukan");
        }
        $position->delete();
        return ApiFormatter::success(200, "Data berhasil dihapus");
    }
}
