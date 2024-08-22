<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class ClientController extends Controller
{
    public function index(Request $request)
    {
        $pageSize = $request->input('pageSize', 10);
        $client = Client::orderBy('updated_at', 'desc')->paginate($pageSize);
        return response()->json($client);
    }

    public function clientByUser($id, Request $request){
        $pageSize = $request->input('pageSize', 10);
        $client = Client::where('user_id', $id)->orderBy('updated_at', 'desc')->paginate($pageSize);
        return response()->json($client);
    }

    public function destroy($id)
    {

        $client = Client::find($id);
        if (!$client) {
            return response()->json(['message' => 'Không tìm thấy khách hàng'], 404);
        }
        $client->delete();
        return response()->json(['message' => 'Xóa khách hàng thành công'], 200);
    }

    public function show($id)
    {
        $client = Client::find($id);
        Log::info($client);
        return response()->json($client, 200);
    }

    public function update($id, Request $request)
    {
        $client = Client::find($id);
        $client->update(
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'cccd' => $request->cccd,
                'address' => $request->address,
                'email' => $request->email,
                'finance' => $request->finance,
                'searcharea' => $request->searcharea,
                'area' => $request->area,
                'intendtime' => $request->intendtime,
                'business' => $request->business,
                'personnumber' => $request->personnumber,
                'numbercars' => $request->numbercars,
                'numbermotor' => $request->numbermotor,
                'note' => $request->note,
                'birth_year' => $request->birth_year,
            ]
        );
        Log::info($client);
        return response()->json($client, 200);
    }


    public function export(){
        $clients = Client::all();
        Log::info($clients);
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Tên khách hàng');
        $sheet->setCellValue('B1', 'Số điện thoại');
        $sheet->setCellValue('C1', 'Địa chỉ');
        $sheet->setCellValue('D1', 'Khu vực cần tìm');
        $sheet->setCellValue('E1', 'Mục đích kinh doanh');
        $sheet->setCellValue('F1', 'Diện tích cần tìm');
        $row = 2;

        foreach ($clients as $client) {
            $sheet->setCellValue('A' . $row, $client->name);
            $sheet->setCellValue('B' . $row, $client->phone);
            $sheet->setCellValue('C' . $row, $client->address);
            $sheet->setCellValue('D' . $row, $client->searcharea);
            $sheet->setCellValue('E' . $row, $client->business);
            $sheet->setCellValue('F' . $row, $client->area);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'clients.xlsx';
        return response()->stream(function() use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function store(Request $request)
    {


        $client = Client::create(
            [
                'name' => $request->name,
                'phone' => $request->phone,
                'cccd' => $request->cccd,
                'address' => $request->address,
                'email' => $request->email,
                'finance' => $request->finance,
                'searcharea' => $request->searcharea,
                'area' => $request->area,
                'intendtime' => $request->intendtime,
                'business' => $request->business,
                'personnumber' => $request->personnumber,
                'numbercars' => $request->numbercars,
                'numbermotor' => $request->numbermotor,
                'note' => $request->note,
                'birth_year' => $request->birth_year,
                'user_id' => $request->user_id,
            ]
        );


        return response()->json($client, 200);
    }
}
