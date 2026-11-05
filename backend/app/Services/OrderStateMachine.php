<?php
namespace App\Services;
use App\Models\Order;use Illuminate\Http\Exceptions\HttpResponseException;
class OrderStateMachine {
 public function transition(Order $order,string $to,string $role,?string $reason=null):void {
  $allowed=['PENDING'=>['PAID'=>['admin','cashier'],'CANCELLED'=>['admin']],'PAID'=>['SHIPPED'=>['admin'],'CANCELLED'=>['admin']],'SHIPPED'=>['COMPLETED'=>['admin']],'COMPLETED'=>[],'CANCELLED'=>[]];
  if(!isset($allowed[$order->status][$to])||!in_array($role,$allowed[$order->status][$to],true)) throw new HttpResponseException(response()->json(['success'=>false,'message'=>'Transisi status order tidak valid'],409));
  if($to==='CANCELLED'&&blank($reason)) throw new HttpResponseException(response()->json(['success'=>false,'message'=>'cancel_reason wajib diisi'],422));
  $order->status=$to;$field=['PAID'=>'paid_at','SHIPPED'=>'shipped_at','COMPLETED'=>'completed_at','CANCELLED'=>'cancelled_at'][$to];$order->{$field}=now();if($to==='CANCELLED')$order->cancel_reason=$reason;$order->save();
 }
}
