<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{

    public function detail(){
        $config = Config::first();
        return response()->json(['status'=> 'success','data'=>$config]);
    }

    public function update(Request $request){ 
        Log::info($request->all());
        $config = Config::first();
        if ($request->hasFile('logo')) {
            // Xóa ảnh cũ nếu có
            if ($config->logo) {
                Storage::disk('public')->delete($config->logo);
            }

            $logo = $request->file('logo');
           
            $logoFileName = 'image_' . $logo->getClientOriginalName();
            $logoFilePath = 'storage/config/' . $logoFileName;
            Storage::putFileAs('public/config', $logo, $logoFileName);
            $path = "http://127.0.0.1:8000/".$logoFilePath;
        } else {
            $path = $config->logo;
        }

        // Cập nhật dữ liệu
        $config->update([
            'title' => $request->title,
            'description' => $request->description ?? "",
            'logo' => $path,
            'keyword' => $request->keyword ?? "",
        ]);

        return response()->json(['status'=> 'success','data'=>$config]);
    }
}
