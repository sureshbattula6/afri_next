<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryScan;
use App\Models\InventoryScanMaster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InventoryScanController extends Controller
{

    public function storeData(Request $request)
    {
        try {

            Log::info('Inventory Scan' .$request->all());
            $user_id = $request->input('user_id');
            $dataItems = $request->input('data');

            foreach ($dataItems as $data) {
                $inventoryScan = InventoryScan::create([
                    'user_id' => $user_id,
                    'barcode' => $data['barcode'],
                    'quantity' => $data['quantity'],
                    'item_id' => $data['item_id'],
                    'item_code' => $data['item_code'],
                ]);

                for ($i = 0; $i < $data['quantity']; $i++) {
                    InventoryScanMaster::create([
                        'inventory_scan_id' => $inventoryScan->id,
                        'barcode' => $data['barcode'],
                        'item_code' => $data['item_code'],
                    ]);
                }

                //Log::info('Inventory Scan and Masters Created: ', $inventoryScan->toArray());
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Inventory Scan and Master data posted successfully for every quantity!',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function Inventorydetails()
    {
        try {
            $inventory = InventoryScan::orderBy('id', 'desc')->get();
            $res = [
                'success' => true,
                'inventories' => $inventory,
                'message' => "Inventory  Scan Details...",
            ];

            return response()->json($res);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    public function BarcodeScannerDetails(Request $request)
    {
        try {

            $barcode = $request->input('barcode');

            $barcodeDetails = DB::table('stock_item_masters')
                ->where('barcode', $barcode)
                ->get();

            $res = [
                'success' => true,
                'barcodeDetails' => $barcodeDetails,
                'message' => 'Barcode Scan Details...!',
            ];

            return response()->json($res);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
        }
    }

    //public function test(){

    //} 

}
