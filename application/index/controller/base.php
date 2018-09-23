<?php
namespace app\index\controller;

use think;
use think\Db;
use think\Controller;

class base extends Controller
{
    public $test;   //声明公共变量

    function __construct() {
        parent::__construct();
        $this->assign('title', '贝拉小蜜蜂官网 - 婴幼儿洗护用品');
        $this->assign('keywords', '贝拉小蜜蜂官网 - 婴幼儿洗护用品');
        $this->assign('description', '贝拉小蜜蜂官网 - 婴幼儿洗护用品');
        /********************导航条****************************/
        //关于我们
        $sql = 'SELECT * FROM blb_article_cat WHERE parent_id = 14 and show_in_nav=1 order by sort_order,cat_id';
        $nav_about = Db::query($sql);
        $this->assign('nav_about', $nav_about);
        //产品家园
        $sql = 'SELECT * FROM blb_category WHERE show_in_nav=1 and is_show=1 order by sort_order,cat_id';
        $nav_category = Db::query($sql);
        $this->assign('nav_category', $nav_category);
        //孕婴课堂
        $sql = 'SELECT * FROM blb_article_cat WHERE parent_id = 9 and show_in_nav=1 order by sort_order,cat_id';
        $nav_classroom_child = Db::query($sql);
        $this->assign('nav_classroom_child', $nav_classroom_child);
        //加盟代理
        $sql = 'SELECT * FROM blb_article_cat WHERE parent_id = 52 and show_in_nav=1 order by sort_order,cat_id';
        $nav_join = Db::query($sql);
        $this->assign('nav_join', $nav_join);
    }
}