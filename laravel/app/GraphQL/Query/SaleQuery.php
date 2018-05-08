<?php
/**
 * Created by PhpStorm.
 * User: dxc1993
 * Date: 2018/3/23
 * Time: 15:26
 */

namespace App\GraphQL\Query;

use App\BillIndex;
use App\Library\Helper;
use App\Ptype;
use App\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use GraphQL;
use Rebing\GraphQL\Support\SelectFields;
use DB;


//销售类占比
class SaleQuery extends Query
{
//    public function authorize(array $args)
//    {
//        return !\Auth::guest();
//    }

    protected $attributes = [
        'name' => 'sale'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('sale'));
    }


    public function args()
    {

    }


    public function resolve($root, $args)
    {

        //统计音频类商品的销售额
        $sales = DB::connection('sqlsrv')->select("select CONVERT(varchar(10), b.billdate, 23) as 'date', sum(r.total) as  'money'
from billindex b left join retailBill r on b.BillNumberID = r.BillNumberID inner join ptype p on p.typeId = r.PtypeId
where datediff(dd,b.BillDate,getdate()) <= 15  and b.BillType = 305   and p.Parid = '00010'
and  b.BillDate <= CONVERT(varchar(30),getdate(),23)  group by p.ParID,b.BillDate order by date asc;");
        foreach ($sales as &$sale) {
            //统计当天的总计的销售额
            $totalMoney = DB::connection('sqlsrv')->select("select  sum(TotalInMoney) as 'totalMoney'  from billindex
where  BillType = 305 and  BillDate = '{$sale->date}';");
            $sale->totalMoney = $totalMoney[0]->totalMoney;
            $sale->count = Helper::getNum($sale->money, $totalMoney[0]->totalMoney);
        }

        return $sales;


        //从订单中心获取
//        $sales = DB::connection('mysql')->select("select DATE_FORMAT(b.billdate,'%Y-%m-%d') as 'date',
//sum(r.total) as  'money'
//from billindex b left join retailBill r on b.BillNumberID = r.BillNumberID
//inner join dim_sku p on p.sku_id = r.PtypeId
//where  b.BillType = 305
//and p.category = '音频'
//and  b.BillDate >= DATE_SUB(curdate(),INTERVAL 14 DAY)
//group by p.category,b.BillDate order by date ASC limit 15;");
//
//        foreach ($sales as &$sale) {
//            //统计当天的总计的销售额
//            $totalMoney = DB::connection('mysql')->select("select  sum(TotalInMoney) as 'totalMoney'  from billindex
//where  BillType = 305 and  BillDate = '{$sale->date}';");
//
//            //减去当天总计的销售退货额
//            $totalRefundMoney = DB::connection('mysql')->select("select  sum(TotalInMoney) as 'totalMoney'  from billindex
//where  BillType = 215 and  BillDate = '{$sale->date}';");
//            if ($totalRefundMoney[0]->totalMoney) {
//                $totalRefundMoney = $totalRefundMoney[0]->totalMoney;
//            } else {
//                $totalRefundMoney = 0;
//            }
//
//            $sale->totalMoney = $totalMoney[0]->totalMoney;
//            $sale->count = Helper::getNum($sale->money, ($totalMoney[0]->totalMoney) - $totalRefundMoney);
//        }
//
//        return $sales;


    }
}