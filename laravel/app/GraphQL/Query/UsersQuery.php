<?php
/**
 * Created by PhpStorm.
 * User: dxc1993
 * Date: 2018/3/20
 * Time: 18:09
 */

namespace App\GraphQL\Query;

use App\User;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class UsersQuery extends Query
{

    protected $attributes = [
        'name' => 'user'
    ];

    public function type()
    {

        return Type::listOf(GraphQL::type('user'));
    }


    public function args()
    {
        return [
            'id' => ['name' => 'id', Type::int()],
            'uid' => ['name' => 'uid', Type::string()],
            'usercode' => ['name' => 'usercode', Type::string()],
            'name' => ['name' => 'name', Type::string()],
            'offset' => ['name' => 'offset', Type::int()],
        ];
    }

    public function resolve($root, $args)
    {

        $query = User::query();
        $user = new User();
        if (isset($args['id'])) {
            $user = $query->where('id', $args['id']);
        }

        if (isset($args['uid'])) {
            $user = $query->where('uid', $args['id']);
        }

        if (isset($args['usercode'])) {
            $user = $query->where('usercode', $args['usercode']);
        }

        if (isset($args['name'])) {
            $user = $query->where('name', 'like', '%' . $args['name'] . '%');
        }


        if (isset($args['offset'])) {
            $limit = 5;
            $offset = ($args['offset'] - 1) * $limit;
            $user = $query->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();
            return $user;
        } else {
            return $user->get();
        }

    }


}