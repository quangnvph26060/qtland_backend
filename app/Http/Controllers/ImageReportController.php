<?php

namespace App\Http\Controllers;

use App\Models\ReportClient;
use App\Models\ReportImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ImageReportController extends Controller
{
    function upload(Request $request)
    {
        $appUrl = env('APP_URL');
        if ($request->hasFile('files')) {
            $report_id = $request->report_id;
            $i = 1;
            foreach ($request->file('files') as $file) {
                $directoryName = 'report-' . ($report_id);
                $storagePath = 'public/upload/images/report/' . $directoryName . '/' . $i . '.' . $file->getClientOriginalExtension();
                $storageUrl = 'storage/upload/images/report/' . $directoryName . '/' . $i . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($storagePath, file_get_contents($file));
                ReportImage::create([
                    'report_id' => $report_id,
                    'image' => $appUrl."/" . $storageUrl
                ]);
                $i = $i + 1;
            }
            return ['success' => 'Upload thành công'];
        } else {
            return ['error' => 'Không tìm thấy file để upload'];
        }
    }

    function update(Request $request)
    {
        $appUrl = env('APP_URL');
        $report_id = $request->report_id;
        // Xóa hết các ảnh trong thư mục của bài viết
        $directoryName = ' -' . $report_id;
        $directoryPath = 'public/upload/images/report/' . $directoryName;
        $post = ReportClient::find($request->report_id);
        $images = $post->images;

        $deleted_files = json_decode($request->deleted_files);

        foreach ($images as $image) {
            $check = false;
            for ($i = 0; $i < count($deleted_files); $i++) {
                if ($image->id == $deleted_files[$i]->id) {
                    $check = true;
                    break;
                }
            }
            if ($check == true) {
                preg_match('/(\d+)/', basename($image->image), $matches);
                $i = $matches[0];
                continue;
            }
            $image->delete();
            Storage::delete('public/upload/images/report/' . $directoryName . '/' . basename($image->image));
        }
        if($request->file('files')){
            $i = 1;
            foreach ($request->file('files') as $file) {
                // Lưu ảnh mới vào thư mục của bài viết
                $storagePath = $directoryPath . '/' . $i . '.' . $file->getClientOriginalExtension();
                Log::info($storagePath);
                $storageUrl = 'storage/upload/images/report/' . $directoryName . '/' . $i . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($storagePath, file_get_contents($file));

                // Tạo bản ghi cho ảnh mới trong cơ sở dữ liệu
                ReportImage::create([
                    'report_id' => $report_id,
                    'image' => $appUrl."/" . $storageUrl
                ]);

                $i++;
            }
        }


        return response()->json($post, 200);
    }
}
