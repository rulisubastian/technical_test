<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Order,OrderItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller {
    public function summary(Request $r) {
        return response()->json([
            'success'=>true,
            'message'=>'OK',
            'data'=>Cache::remember('dashboard:summary',
                now()->addMinutes(5),fn()=>[
                    'orders'=>Order::count(),
                    'revenue'=>Order::whereIn('status',['PAID','SHIPPED','COMPLETED'])->sum('total_amount'),
                    'pending'=>Order::where('status','PENDING')->count()
                ]
            )
        ]);
    }
    
    public function sales(Request $r) {
        abort_unless($r->user()->isAdmin(),403);
        $v = $r->validate(['from'=>'required|date','to'=>'required|date|after_or_equal:from','group_by'=>'nullable|in:day,month']);
        $fmt = ($v['group_by']??'day')==='month' ? 'YYYY-MM':'YYYY-MM-DD';
        $data = Order::query()->selectRaw("to_char(order_date, ?) period, count(*) total_orders, sum(total_amount) revenue",[$fmt])
            ->whereIn('status',['PAID','SHIPPED','COMPLETED'])->whereBetween('order_date',[$v['from'],$v['to'].' 23:59:59'])
            ->groupBy('period')->orderBy('period')->get();
            
        return response()->json([
            'success'=>true,
            'message'=>'OK',
            'data'=>$data
        ]);
    }
    
    public function topProducts(Request $r) {
        abort_unless($r->user()->isAdmin(),403);
        $v = $r->validate(['from'=>'required|date','to'=>'required|date|after_or_equal:from','limit'=>'nullable|integer|min:1|max:100']);
        $data = OrderItem::query()->join('orders','orders.id','=','order_items.order_id')->join('products','products.id','=','order_items.product_id')
            ->whereIn('orders.status',['PAID','SHIPPED','COMPLETED'])
            ->whereBetween('orders.order_date',[$v['from'],$v['to'].' 23:59:59'])
            ->groupBy('products.id','products.product_name')
            ->selectRaw('products.id, products.product_name, sum(order_items.quantity) quantity_sold, sum(order_items.subtotal) revenue')
            ->orderByDesc('quantity_sold')
            ->limit($v['limit'] ?? 10)
            ->get();

        return response()->json([
            'success'=>true,
            'message'=>'OK',
            'data'=>$data
        ]);
    }
}
