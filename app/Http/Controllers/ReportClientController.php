<?php

namespace App\Http\Controllers;

use App\Models\ReportClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class ReportClientController extends Controller
{
    public function getReportByUser($id)
    {
        $reports = ReportClient::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reports, 200);
    }

    public function show($id)
    {
        $report = ReportClient::findOrFail($id);
        return response()->json($report, 200);
    }

    public function update($id, Request $request)
    {
        $post = ReportClient::find($id);
        Log::info($request->all());
        $post->update([
            'name' => $request->name,
        ]);
        return response()->json($post, 200);
    }

    public function store(Request $request){
        $report = ReportClient::create($request->all());
        return response()->json($report, 201);
    }

    public function destroy($id)
    {

        $directoryName = 'report-' . $id;
        $report = ReportClient::find($id);
        $images = $report->images;


        foreach ($images as $image) {
            $image->delete();
            Storage::delete('public/upload/images/report/' . $directoryName . '/' . basename($image->image_path));
        }

        $report->delete();
        return response()->json(['message' => 'Xóa thành công'], 200);
    }
}
