<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryScan extends Model
{
    use HasFactory;
    protected $table = 'inventory_scan';

    protected $fillable = ['user_id', 'barcode', 'quantity','item_id','item_code'];
}
