<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4 0004
 * Time: 09:18
 */

namespace Src\Auth;

use think\Db;
use think\Exception;

class Auth
{

    protected $auth_group = 'auth_group';//用户角色表(用户组表)

    protected $group_access = "auth_group_access";//用户明细表(用户属于哪个组)

    protected $auth_rule = "auth_rule";//权限码表


    protected $user = 'user';//用户表


    function __construct($auth_group = 'auth_group', $group_access = 'auth_group_access', $auth_rule = 'auth_rule', $user = 'user')
    {

        $this->auth_group = $auth_group;

        $this->group_access = $group_access;

        $this->auth_rule = $auth_rule;

        $this->user = $user;

    }

    /**
     * 判断权限，没有权限返回false,有权限返回所有权限列表,超级管理员返回true,
     * Create by Peter
     * @param $rule
     * @param $uid
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function check($rule, $uid)
    {


        //转小写
        $rule = strtolower($rule);

        $list = $this->getRuleList($uid);


        if ($list === true) return true;

        if (!$list) return [];

        if (in_array($rule, $this->all_lowed($list))) return $list;

        return false;


    }


    function all_lowed(array $array){

        $new_arr=[];


        foreach ($array as $key=>$value){

            $new_arr[]=(strtolower($value));

        }


        return $new_arr;
    }


    /**
     * 获取权限列表
     * Create by Peter
     * @param $uid
     * @return array|bool
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function getRuleList($uid)
    {


        $re = Db::table($this->group_access)
            ->alias('ga')
            ->join($this->auth_group . " ag", 'ga.group_id = ag.id', 'left')
            ->where('ga.uid', $uid)
            ->find();


        if (!$re) return [];


        //超级管理员
        if ($re['group_id'] === 0) return true;


        $res = Db::table($this->auth_rule)->where('id', 'in', $re['rules'])->select();


        if (!$res) return [];


        $res = array_column($res, 'name');

//        $arr = [];
//        //全部转小写
//        foreach ($res as $key => $value) {
//
//            $v = strtolower($value);
//
//            $arr[] = $v;
//        }


        return $res;


    }


    /**
     * 对当前用户的可见的菜单连接进行筛选
     * Create by Peter
     * @param $menu 完整列表,见配置文件数组
     * @param $auth array 拥有的列表，授权类返回的数组
     * @return array
     */
    function filter_menu($menu, $auth = [])
    {

        $new_menu = array();
        foreach ($menu as $key => $value) {


            foreach ($value as $key1 => $value1) {

                if (in_array(($value1), $auth)) {

                    $new_menu[$key][$key1] = $value1;
                }


            }


        }


        return $new_menu;


    }

    /**
     * 添加修改权限码
     * Create by Peter
     * @param $data
     * @throws Exception
     * @throws \think\exception\PDOException
     */
    function auth_update($data)
    {


        if ($data['id']) {


            $re = Db::table($this->auth_rule)->update($data);

        } else {

            $re = Db::table($this->auth_rule)->insert($data);
        }

    }


    /**
     * 添加/编辑用户信息
     * @param array $data
     * @return bool 成功返回自增id，失败返回false
     */
    function save_user($data)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (isset($data['id'])) {
            //更新
            try {
                Db::table($this->user)->update($data);
                return true;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            //添加
            try {
                Db::table($this->user)->insert($data);
            } catch (\Exception $e) {
                return false;
            }
        }
        return Db::name($this->user)->getLastInsID();
    }

    /**
     * 删除用户信息
     * @param int $id 用户表主键id
     * @return int
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    function del_user($id)
    {
        if (empty($id)) {
            return false;
        }
        return Db::table($this->user)->delete($id);
    }

    /**
     * 检查用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @return array|bool|false|\PDOStatement|string|\think\Model
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    function check_login($username, $password)
    {
        $data = Db::table($this->user)->where('username', $username)->find();
        if (empty($data)) {
            return false;
        }
        if (password_verify($password, $data['password'])) {
            return $data;
        }
        return false;
    }
}