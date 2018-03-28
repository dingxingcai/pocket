<?php
/**
 * Created by PhpStorm.
 * User: dxc1993
 * Date: 2018/3/20
 * Time: 17:32
 */

namespace App\GraphQL\Type;

use App\Post;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;
use GraphQL;
use GraphQL\Type\Definition\InputType;

class UserType extends GraphQLType
{
    protected $inputObject=true;

    protected $attributes = [
        'name' => 'user',
        'description' => 'test A User'
    ];


    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'primary key'
            ],
            'uid' => [
                'type' => Type::string(),
                'description' => 'user id'
            ],
            'usercode' => [
                'type' => Type::string(),
                'description' => '用户编码'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => '用户姓名'
            ],
            'loginat' => [
                'type' => Type::string(),
                'description' => '登录时间'

            ],
            'password' => [
                'type' => Type::string(),
                'description' => 'password'
            ],
            'telephone' => [
                'type' => Type::string(),
                'description' => 'telephone'
            ],
            'offset' => [
                'type' => Type::int(),
                'description' => '分页数量'
            ],
            'login' => [
                'type' => Type::string(),
                'description' => '登录的标识'
            ],
            'token' => [
                'type' => Type::string(),
                'description' => '用户token'
            ],

        ];
    }

    public function resolvePostField($root, $args)
    {



        if (isset($args['uid'])) {
            return Post::where('uid', $args['uid'])->get();
        }

//        return $root->posts;

        return Post::where('uid',$root->uid)->get();
    }


}