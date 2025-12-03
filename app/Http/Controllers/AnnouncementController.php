<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Helpers\ApiFormatter;
use Illuminate\Validation\ValidationException;

class AnnouncementController extends Controller
{
    public function index()
    {
        $items = Announcement::latest()->paginate(10);
        return view('announcement.admin', compact('items'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'type'    => 'required|in:pemberitahuan,urgent,event,apresiasi',
                'title'   => 'required|string|max:200',
                'message' => 'required|string'
            ]);

            $announcement = Announcement::create([
                'type'    => $request->type,
                'title'   => ucfirst($request->title),
                'message' => $request->message
            ]);

            return ApiFormatter::success(200, "Announcement berhasil ditambahkan", $announcement);

        } catch (ValidationException $e) {
            return ApiFormatter::validate(json_encode($e->errors()));
        } catch (\Exception $e) {
            return ApiFormatter::error(500, "Terjadi kesalahan", $e->getMessage());
        }
    }

    public function show($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return ApiFormatter::error(404, "Data tidak ditemukan");
        }

        return ApiFormatter::success(200, "Detail announcement berhasil diambil", $announcement);
    }

    public function edit($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return ApiFormatter::error(404, "Data tidak ditemukan");
        }

        return ApiFormatter::success(200, "Data berhasil diambil untuk edit", $announcement);
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'type'    => 'required|in:pemberitahuan,urgent,event,apresiasi',
                'title'   => 'required|string|max:200',
                'message' => 'required|string'
            ]);

            $announcement = Announcement::find($id);

            if (!$announcement) {
                return ApiFormatter::error(404, "Data tidak ditemukan");
            }

            $announcement->update([
                'type'    => $request->type,
                'title'   => ucfirst($request->title),
                'message' => $request->message
            ]);

            return ApiFormatter::success(200, "Announcement berhasil diperbarui", $announcement);

        } catch (ValidationException $e) {
            return ApiFormatter::validate(json_encode($e->errors()));
        }
    }

    public function destroy($id)
    {
        $announcement = Announcement::find($id);

        if (!$announcement) {
            return ApiFormatter::error(404, "Data tidak ditemukan");
        }

        $announcement->delete();

        return ApiFormatter::success(200, "Announcement berhasil dihapus");
    }
}
