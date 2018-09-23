<?php
namespace app\index\controller;
use think\Db;

class story extends base
{
    public function index()
    {
        $id = intval(input('param.id'));
        $sql = "SELECT * FROM blb_article_cat WHERE cat_id={$id} order by sort_order,cat_id";
        $data_about = Db::query($sql);
        $this->assign('data', $data_about[0]);
        //渲染模板
        return $this -> fetch();
    }
}
?>
