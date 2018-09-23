<?php
namespace app\index\controller;
use think\Db;

class category extends base
{
    public function index()
    {
        if (input('param.id') != '') {
            $id = intval(input('param.id'));
        }else{
            $id = 1;
        }
        $this->assign('id', $id);
        //$sql = "SELECT * FROM blb_category WHERE cat_id={$id}";
        //$data_category_main = Db::query($sql);
        $data_category_main = Db::name('category')
            ->where(
                array(
                    'cat_id'=>$id
                )
            )
            ->find();
        if(count($data_category_main)<1){
            $this->error('没有该类别');exit;
        }
        $this->assign('data_cat', $data_category_main);
        $data_category = Db::name('goods')->where(
            array(
                'is_on_sale'=>1,
                'is_alone_sale'=>1,
                'is_delete'=>0,
                'is_real'=>1,
                'cat_id'=>$id)
        )->paginate(9);
        $this->assign('data', $data_category);
        //渲染模板
        return $this -> fetch();
    }
}
?>
