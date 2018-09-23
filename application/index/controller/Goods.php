<?php
namespace app\index\controller;
use think\Db;

class goods extends base
{
    public function index()
    {
        if (input('param.id') != '') {
            $id = intval(input('param.id'));
        }else{
            $id = 1;
        }
        $this->assign('id', $id);
        $data_goods = Db::name('goods')->where(
            array(
                'is_on_sale'=>1,
                'is_alone_sale'=>1,
                'is_delete'=>0,
                'is_real'=>1,
                'goods_id'=>$id)
        )->find();
        $this->assign('data', $data_goods);
        $data_category = Db::name('category')
            ->where(
                array(
                    'cat_id'=>$data_goods['cat_id']
                )
            )
            ->find();
        if(count($data_category)<1){
            $this->error('没有该类别');exit;
        }
        $this->assign('data_cat', $data_category);

        $data_element = Db::field('G.element_id,G.element_name,G.element_logo')
            ->table(['blb_element_cat'=>'E','blb_element'=>'G'])
            ->where('E.cat_id=G.element_id and E.goods_id='.$id)
            ->limit(9)->select();
        $this->assign('data_element', $data_element);
        //渲染模板
        return $this -> fetch();
    }
}
?>
