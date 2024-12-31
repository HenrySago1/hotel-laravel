<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\Registro;

class ApiDeviceController extends Controller
{
    public function update($uuid)
    {
        $device = Device::where('uuid', $uuid)->first();
        if ($device) {
            $device->status = !$device->status;
            $device->save();
            $response = Http::post(' https://api-smartcontract-hoteliot-green-feather-5725.fly.dev/api/change-status', [
                'numberRoom' =>  $device->habitacion->numero_habitacion,
            ])->throw();


            $responseData = $response->json();

            if (isset($responseData['trxhash'])) {
                $requestData['trxhash'] = $responseData['trxhash'];
                $registro = Registro::create($requestData);
            } else {
                return response()->json(['error' => 'Transaction hash not found.'], 400);
            }
            return response()->json([
                'success' => true,
                'message' => 'Status cambiado correctamente',
                'new_status' => $device->status,
                'Device' => $device
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Dispositivo no encontrado'
            ], 404);
        }
    }

    public function search($uuid)
    {
        $device = Device::where('uuid', $uuid)->first();
        if ($device) {
            return response()->json(['success' => true, 'Device' => $device], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Guía no encontrada'], 404);
        }
    }
}
