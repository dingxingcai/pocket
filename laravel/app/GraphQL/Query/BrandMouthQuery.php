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


//各品牌的销售额占比(截止到目前为止当月的)
class BrandMouthQuery extends Query
{
//    public function authorize(array $args)
//    {
//        return !\Auth::guest();
//    }

    protected $attributes = [
        'name' => 'brandMouth'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('brand'));
    }


    public function args()
    {

    }


    public function resolve($root, $args)
    {


        $date = date('Y-m-1', time());

        //一个月的销售额占比，统计的是到目前为止的
        $brands = DB::connection('sqlsrv')->select("select  p.ParID,sum(r.total) as  'money'
from billindex b left join retailBill r on b.BillNumberID = r.BillNumberID inner join ptype p on
p.typeId = r.PtypeId
where  b.BillType = 305 and b.redword = 0 and b.draft = 0 and  b.BillDate <= CONVERT(varchar(30),getdate(),23)
and b.BillDate >= '{$date}' group by p.ParID;");

        //统计总计的销售额
        $totalMoney = DB::connection('sqlsrv')->select("select  sum(TotalInMoney) as 'totalMoney'  from billindex
where  BillType = 305 and RedWord = 0 and draft = 0 and  BillDate <= CONVERT(varchar(30),getdate(),23)
and BillDate >= '{$date}';");
        if ($totalMoney[0]->totalMoney) {
            $totalMoney = $totalMoney[0]->totalMoney;
        } else {
            $totalMoney = 0;
        }


        //统计总计的退货单总额，要减掉总的退货单额
        $totalRefundMoney = DB::connection('sqlsrv')->select("select  sum(TotalInMoney) as 'totalMoney'  from billindex
where  BillType = 215 and RedWord = 0 and draft = 0 and  BillDate <= CONVERT(varchar(30),getdate(),23)
and BillDate >= '{$date}';");
        if ($totalRefundMoney[0]->totalMoney) {
            $refund = $totalRefundMoney[0]->totalMoney;
        } else {
            $refund = 0;
        }

        foreach ($brands as &$brand) {
            $ptype = Ptype::select('FullName')->where('typeId', $brand->ParID)->first();
            $brand->name = $ptype->FullName;
            $brand->count = Helper::getNum($brand->money, $totalMoney - $refund);
        }


        return $brands;



        //从订单中心获取
//        $date = date('Y-m-1', time());
//        $brands = DB::connection()->select("select d.category as 'name',sum(r.total) as 'money' from BillIndex b
//left join retailBill r on r.BillNumberId = b.BillNumberId
//left join dim_sku d on d.sku_id = r.PtypeId
//where b.BillType = 305 and
//d.category is not NULL and
//b.BillDate >= '{$date}'
//group by d.category;");
//
//        //统计当月总计的销售额
//        $totalMoney = DB::connection()->select("select  sum(TotalMoney) as 'totalMoney'  from billindex
//where  BillType = 305 and RedWord = 0 and  BillDate >='{$date}' ;");
//        if ($totalMoney[0]->totalMoney) {
//            $totalMoney = $totalMoney[0]->totalMoney;
//        } else {
//            $totalMoney = 0;
//        }
//
//        //统计当月的总计的退货销售额
//        $totalRefundMoney = DB::connection()->select("select  sum(TotalMoney) as 'totalRefundMoney'  from billindex
//where  BillType = 215 and RedWord = 0 and  BillDate >='{$date}' ;");
//        if ($totalRefundMoney[0]->totalRefundMoney) {
//            $totalRefundMoney = $totalRefundMoney[0]->totalRefundMoney;
//        } else {
//            $totalRefundMoney = 0;
//        }
//
//        foreach ($brands as &$brand) {
//            $brand->count = Helper::getNum($brand->money, ($totalMoney - $totalRefundMoney));
//        }
//
//        return $brands;


    }
}