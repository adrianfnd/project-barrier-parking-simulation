<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class BarrierController extends Controller
{
    public function __construct()
    {
        $this->middleware('basic.auth');
    }

    public function index()
    {
        $logs = Cache::get('barrier_logs', []);
        return view('barrier', compact('logs'));
    }

    public function handleRequest(Request $request)
    {
        $clientType = $request->input('ClientType');
        $gateNo = $request->input('GateNo');
    
        $response = [
            'ResponseType' => 199,
            'Status' => 1
        ];
    
        if ($clientType == 121) {
            $gateStatus = 'Open';
            $sireneStatus = 'Off';

            $this->createParkingEntry($request);
        } elseif ($clientType == 122 && $request->input('ActType') == 2) {
            $gateStatus = 'Closed';
            $sireneStatus = 'On';
        } else {
            $gateStatus = 'Open';
            $sireneStatus = 'Off';
        }
    
        $this->logAction($request->all(), $response, $gateNo);
    
        Cache::put("gate{$gateNo}Status", $gateStatus, now()->addSeconds(5));
        Cache::put("sirene{$gateNo}Status", $sireneStatus, now()->addSeconds(5));
    
        return response()->json($response);
    }

    private function logAction($request, $response, $gateNo)
    {
        $logEntry = [
            'timestamp' => now()->toDateTimeString(),
            'gate' => $gateNo,
            'request' => json_encode($request),
            'response' => json_encode($response)
        ];

        $logs = Cache::get('barrier_logs', []);
        array_unshift($logs, $logEntry);
        $logs = array_slice($logs, 0, 10);
        Cache::put('barrier_logs', $logs, 60 * 1);
    }
    
    public function getLogs()
    {
        $logs = Cache::get('barrier_logs', []);
        $gate1Status = Cache::get('gate1Status', 'Ready');
        $gate2Status = Cache::get('gate2Status', 'Ready');
        $sirene1Status = Cache::get('sirene1Status', 'Off');
        $sirene2Status = Cache::get('sirene2Status', 'Off');
        
        return response()->json([
            'logs' => $logs,
            'gate1Status' => $gate1Status,
            'gate2Status' => $gate2Status,
            'sirene1Status' => $sirene1Status,
            'sirene2Status' => $sirene2Status
        ]);
    }

    private function createParkingEntry(Request $request)
    {
        $response = Http::post('http://127.0.0.1:8000/api/parkir-masuk/cashless', [
            'id_client' => '1A2C5BD543',
            'tgl_masuk' => now()->toDateTimeString(),
            'foto_masuk' => $request->input('foto_masuk'),
        ]);

        if ($response->successful()) {
            $this->logAction(['message' => 'Parking entry created successfully'], $response->json(), $request->input('GateNo'));
        } else {
            $this->logAction(['error' => 'Failed to create parking entry'], $response->json(), $request->input('GateNo'));
        }
    }
}
