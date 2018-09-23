<?php
namespace app\index\controller;
use think\Db;

class element extends base
{
    public function index()
    {
        $data_element = Db::name('element')->where(
            array(
                'is_show'=>1
            )
        )->select();
        $this->assign('data', $data_element);
        //渲染模板
        return $this -> fetch();
    }

    public function show()
    {
        if (input('param.id') != '') {
            $id = intval(input('param.id'));
        }else{
            $this->error('错误的参数');exit;
        }
        //元素内容
        $data_element = Db::name('element')->where(
            array(
                'is_show'=>1,
                'element_id'=>$id
            )
        )->find();
        if(count($data_element)>0){
            $this->assign('datamain', $data_element);
            //前后元素
            $sql = "
            select * from blb_element where element_id in
                (select
                case
                when SIGN(CAST(element_id AS SIGNED)-{$id})>0 THEN MIN(element_id)
                when SIGN(CAST(element_id AS SIGNED)-{$id})<0 THEN MAX(element_id)
                end
                from blb_element
                where element_id !={$id} and is_show=1
                GROUP BY SIGN(CAST(element_id AS SIGNED)-{$id})
                ORDER BY SIGN(CAST(element_id AS SIGNED)-{$id})
                )
            ORDER BY element_id
                ";
            $data_element_per_next = Db::query($sql);
            $data_element_per = array();
            $data_element_next = array();
            if(count($data_element_per_next)>0){
                if(count($data_element_per_next)==1){
                    if($data_element_per_next[0]['element_id']>$id){
                        //第一条
                        $data_element_next = $data_element_per_next[0];
                        $data_element_per['have'] = 'no';
                        $data_element_next['have'] = 'yes';
                    }else{
                        //最后一条
                        $data_element_per = $data_element_per_next[0];
                        $data_element_per['have'] = 'yes';
                        $data_element_next['have'] = 'no';
                    }
                }else{
                    $data_element_per = $data_element_per_next[0];
                    $data_element_next = $data_element_per_next[1];
                    $data_element_next['have'] = 'yes';
                    $data_element_per['have'] = 'yes';
                }
            }else{
                //没有前后记录
                $data_element_per['have'] = 'no';
                $data_element_next['have'] = 'no';
            }
            $this->assign('data_per_next', $data_element_per_next);
            $this->assign('data_per', $data_element_per);
            $this->assign('data_next', $data_element_next);
        }else{
            $this->error('找不到该元素');exit;
        }
        //相关商品
        $data_element_goods = Db::name('element_cat')->where(
            array(
                'cat_id'=>$id
            )
        )->select();
        $data_element_goods = Db::field('G.goods_id,E.cat_id as element_id,G.goods_name,G.goods_thumb')
            ->table(['blb_element_cat'=>'E','blb_goods'=>'G'])
            ->where('E.goods_id=G.goods_id and E.cat_id='.$id .' and G.is_on_sale=1 and G.is_alone_sale=1 and G.is_delete=0 and G.is_real=1')
            ->limit(9)->select();
        //print_r($data_element_goods);die;
        if(count($data_element_goods)>0){
            $this->assign('datagoods', $data_element_goods);
        }else{
            $this->assign('datagoods', array());
        }
        //渲染模板
        return $this -> fetch();
    }
}
?>
