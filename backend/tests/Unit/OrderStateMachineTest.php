<?php
use App\Models\Order;use App\Services\OrderStateMachine;
it('allows pending to paid for cashier',function(){ $o=new Order(['status'=>'PENDING']);app(OrderStateMachine::class)->transition($o,'PAID','cashier');expect($o->status)->toBe('PAID');});
it('rejects invalid transition',function(){ $o=new Order(['status'=>'COMPLETED']);expect(fn()=>app(OrderStateMachine::class)->transition($o,'CANCELLED','admin'))->toThrow(\Illuminate\Http\Exceptions\HttpResponseException::class);});
