<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\{User,Product,Customer,Order,OrderItem,StockMovement};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder {
    public function run():void{
        $admin=User::firstOrCreate(
            ['email'=>'admin@azatech.id'],
            ['name'=>'Azatech Admin','password'=>Hash::make('password'),'role'=>'admin']
        );

        $cashier=User::firstOrCreate(
            ['email'=>'cashier@azatech.id'],
            ['name'=>'Azatech Cashier','password'=>Hash::make('password'),'role'=>'cashier']
        );

        for($i=1;$i<=60;$i++) {
            Product::firstOrCreate(
                ['product_code'=>sprintf('P%04d',$i)],
                ['product_name'=>'Product '.$i,'stock'=>100,'price'=>random_int(5000,150000),'low_stock_threshold'=>5]
            );
        }

        // for($i=1;$i<=35;$i++) {
        //     Customer::firstOrCreate(
        //         ['id'=>(string)Str::uuid()],
        //         ['customer_name'=>'Customer '.$i,'customer_address'=>'Jakarta','gender'=>$i%2?'Pria':'Wanita','phone'=>'0812'.str_pad((string)$i,8,'0',STR_PAD_LEFT)]
        //     );
        // }

        for($i=1;$i<=35;$i++) {
            Customer::firstOrCreate(
                ['customer_name'=>'Customer '.$i],
                [
                    'customer_address'=>'Jakarta',
                    'gender'=>$i%2?'Pria':'Wanita',
                    'phone'=>'0812'.str_pad((string)$i,8,'0',STR_PAD_LEFT)
                ]
            );
        }

        $products=Product::all();
        $customers=Customer::all();

        OrderItem::truncate();
        Order::truncate();

        for($i=1;$i<=160;$i++) {
            $status=['PENDING','PAID','SHIPPED','COMPLETED','CANCELLED'][($i%5)];
            $o=Order::create([
                'order_number'=>'SEED-'.str_pad((string)$i,6,'0',STR_PAD_LEFT),
                'customer_id'=>$customers->random()->id,
                'created_by'=>$i%2?$cashier->id:$admin->id,
                'status'=>$status,
                'total_amount'=>0,
                'order_date'=>now()->subDays(random_int(0,89))
            ]);
            
            $total=0;
            foreach($products->random(2) as $p) {
                $qty=random_int(1,3);
                $sub=$qty*$p->price;
                OrderItem::create([
                    'order_id'=>$o->id,
                    'product_id'=>$p->id,
                    'quantity'=>$qty,
                    'unit_price'=>$p->price,
                    'subtotal'=>$sub
                ]);
                $total+=$sub;
            }
            $o->update(['total_amount'=>$total]);
        }
    }
}