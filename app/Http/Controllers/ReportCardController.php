<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use App\Models\ReportClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReportCardController extends Controller
{
    function upload(Request $request)
    {
        $appUrl = env('APP_URL');
        if ($request->hasFile('filecard')) {
            $report_id = $request->report_id;
            $i = 1;
            foreach ($request->file('filecard') as $file) {
                $directoryName = 'report-' . ($report_id);
                $storagePath = 'public/upload/images/card/' . $directoryName . '/' . $i . '.' . $file->getClientOriginalExtension();
                $storageUrl = 'storage/upload/images/card/' . $directoryName . '/' . $i . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($storagePath, file_get_contents($file));
                ReportCard::create([
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
        $directoryPath = 'public/upload/images/card/' . $directoryName;
        $post = ReportClient::find($request->report_id);
        $images = $post->card;

        $deleted_files = json_decode($request->deleted_file_card);

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
            Storage::delete('public/upload/images/card/' . $directoryName . '/' . basename($image->image));
        }
        if($request->file('filecard')){
            $i = 1;
            foreach ($request->file('filecard') as $file) {
                // Lưu ảnh mới vào thư mục của bài viết
                $storagePath = $directoryPath . '/' . $i . '.' . $file->getClientOriginalExtension();
                Log::info($storagePath);
                $storageUrl = 'storage/upload/images/card/' . $directoryName . '/' . $i . '.' . $file->getClientOriginalExtension();
                Storage::disk('local')->put($storagePath, file_get_contents($file));

                // Tạo bản ghi cho ảnh mới trong cơ sở dữ liệu
                ReportCard::create([
                    'report_id' => $report_id,
                    'image' =>   $appUrl."/" . $storageUrl
                ]);

                $i++;
            }
        }


        return response()->json($post, 200);
    }
}
