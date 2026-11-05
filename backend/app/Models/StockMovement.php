<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StockMovement extends Model {public $timestamps=false;protected $fillable=['product_id','type','quantity_change','stock_after','reference_type','reference_id','note','created_by','created_at'];protected function casts():array{return ['created_at'=>'datetime','quantity_change'=>'integer','stock_after'=>'integer'];}}
