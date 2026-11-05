<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;use Illuminate\Database\Eloquent\SoftDeletes;
class Product extends Model {use SoftDeletes;protected $fillable=['product_code','product_name','stock','price','low_stock_threshold'];protected function casts():array{return ['price'=>'decimal:2','stock'=>'integer','low_stock_threshold'=>'integer'];}public function movements(){return $this->hasMany(StockMovement::class);}}
