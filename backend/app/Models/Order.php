<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {protected $fillable=['order_number','customer_id','created_by','status','total_amount','order_date','paid_at','shipped_at','completed_at','cancelled_at','cancel_reason'];protected function casts():array{return ['total_amount'=>'decimal:2','order_date'=>'datetime','paid_at'=>'datetime','shipped_at'=>'datetime','completed_at'=>'datetime','cancelled_at'=>'datetime'];}public function items(){return $this->hasMany(OrderItem::class);}public function customer(){return $this->belongsTo(Customer::class);}public function creator(){return $this->belongsTo(User::class,'created_by');}}
