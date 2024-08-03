<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(){
        $client = Client::get();
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
}
