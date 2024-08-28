<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryScanMaster extends Model
{
    use HasFactory;

    protected $table = "inventory_scan_items";

    protected $fillable = [
        'inventory_scan_id', 
        'barcode', 
        'item_code', 
    ];

}
