<?php

namespace App\Http\Controllers;

use App\Models\ReportClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Report\Xml\Report;

class ReportClientController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);

        $reports = ReportClient::orderBy('updated_at', 'desc')->paginate($pageSize);

        return response()->json($reports, 200);
    }
    public function getReportByUser($id, Request $request)
    {
        $pageSize = $request->input('pageSize', 10);

        $reports = ReportClient::orderBy('updated_at', 'desc')->where('user_id', $id)->paginate($pageSize);

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
        $post->update($request->all());
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
            Storage::delete('public/upload/images/report/' . $directoryName . '/' . basename($image->image));
        }

        $report->delete();
        return response()->json(['message' => 'Xóa thành công'], 200);
    }
}
