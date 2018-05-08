<?php
/**
 * Created by PhpStorm.
 * User: dxc1993
 * Date: 2018/3/23
 * Time: 15:26
 */

namespace App\GraphQL\Query;

use App\BillIndex;
use App\NVipCardSign;
use App\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use GraphQL;
use Rebing\GraphQL\Support\SelectFields;
use DB;


//自动推送数据图片
class VipQuery extends Query
{
//    public function authorize(array $args)
//    {
//        return !\Auth::guest();
//    }

    protected $attributes = [
        'name' => 'autoPost'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('vip'));
    }


    public function args()
    {

    }


    public function resolve($root, $args)
    {
        //七天新增的总会员数量
        $vips = DB::connection('sqlsrv')->select("select top 7 CONVERT(varchar(10), CreateDate, 23) as 'date', count(*) as 'vipNums' from nVipCardSign where createDate < CONVERT(varchar(30),getdate(),23)  GROUP BY CreateDate order by CreateDate desc;");

        //统计有消费行为的会员数量
        $buyVips = DB::connection('sqlsrv')->select("select count(t.total) as buyNums from (select n.VipCardID ,count(n.VipCardID) as total from nVipCardSign n left join BillIndex b on
n.VipCardID = b.VipCardID where b.VipCardID != -1  group by n.VipCardID) t;");
        $buys = [
            'date' => '有消费行为会员数',
            'vipNums' => $buyVips[0]->buyNums
        ];
        $vips[] = $buys;

        //统计总的会员数量
        $total = NVipCardSign::select('VipCardID')->count();
        $info = [
            'date' => '总计会员数',
            'vipNums' => $total
        ];
        $vips[] = $info;

        return $vips;




        //从订单中心获取数据*******************************************

//        $vips = DB::connection('mysql')->select("select
//CreateDate as 'date',
//count(*) as 'vipNums'
//from dim_vip
//where createDate <= date_sub(curdate(),interval 1 day)
//GROUP BY CreateDate order by CreateDate desc limit 7 ;");
//
//        //统计有消费行为的会员数量
//        $buyVips = DB::connection('mysql')->select("select count(t.total) as buyNums from (select n.VipCardID ,count(n.VipCardID) as total from dim_vip n left join BillIndex b on
//n.VipCardID = b.VipCardID where b.VipCardID != -1  group by n.VipCardID) t;");
//        $buys = [
//            'date' => '有消费行为会员数',
//            'vipNums' => $buyVips[0]->buyNums
//        ];
//        $vips[] = $buys;
//
//        //统计总计的会员数量
//        $total = DB::connection()->select("select count(*) as num from dim_vip");
//
//        $info = [
//            'date' => '总计会员数',
//            'vipNums' => $total[0]->num
//        ];
//
//        $vips[] = $info;
//        return $vips;


    }
}