<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4 0004
 * Time: 09:18
 */

namespace Auth\Start;
use think\Db;

class Auth
{



    function test(){



        $re=Db::table('test')->select();

        print_r($re);
    }

}