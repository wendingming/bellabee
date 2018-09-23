<?php
namespace app\index\controller;
use think\Db;

class article extends base
{
    public function index()
    {
        if (input('param.id') != '') {
            $id = intval(input('param.id'));
        }else{
            $id = 10;
        }
        $sql = "SELECT * FROM blb_article_cat WHERE cat_id={$id} order by sort_order,cat_id";
        $data_article_cat = Db::query($sql);
        $this->assign('datamain', $data_article_cat[0]);
        //SELECT article_id, title, author, add_time, file_url, open_type FROM `bellabeeuser`.`blb_article` WHERE is_open = 1 AND cat_id IN ('9','10','11','12','82','73') ORDER BY article_type DESC, article_id DESC
        //$sql = "SELECT * FROM blb_article WHERE cat_id={$id} and is_open=1 order by article_type desc,cat_id desc";
        //$data_article = Db::query($sql);
        $data_article = Db::name('article')->where(
            array(
                'is_open'=>1,
                'cat_id'=>$id)
        )->order('article_type desc,cat_id desc')->paginate(3);
        $content='';
        $pattern = '/<p>(.*?)<\/p>/';
        if(preg_match_all('/<p.*?>(.*?)(?=<\/p>)/', $data_article[0]['content'], $matches)) {
            $contentlist = $matches[1];
            if(count($contentlist)>1) {
                $content='<p>' .$contentlist[0] .'</p><p>'.$contentlist[1] .'</p>';
            }else{
                $content = '<p>' .i_array_column($contentlist,'</p><p>') .'</p>';
            }
        }
        //print_r($content);die;
        //$data_article = $this->get_cat_articles($id);
        $this->assign('data', $data_article);
        $this->assign('content', $content);
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
        $sql = "SELECT * FROM blb_article WHERE article_id={$id} order by article_id";
        $data_article = Db::query($sql);
        if(count($data_article)==0){
            $this->error('找不到该记录');exit;
        }
        $this->assign('data', $data_article[0]);
        $sql = "SELECT * FROM blb_article_cat WHERE cat_id={$data_article[0]['cat_id']} order by sort_order,cat_id";
        $data_article_cat = Db::query($sql);
        $this->assign('datamain', $data_article_cat[0]);
        $sql = "
            select * from blb_article where article_id in
                (select
                case
                when SIGN(CAST(article_id AS SIGNED)-{$id})>0 THEN MIN(article_id)
                when SIGN(CAST(article_id AS SIGNED)-{$id})<0 THEN MAX(article_id)
                end
                from blb_article
                where article_id !={$id} and cat_id={$data_article[0]['cat_id']} and is_open=1
                GROUP BY SIGN(CAST(article_id AS SIGNED)-{$id})
                ORDER BY SIGN(CAST(article_id AS SIGNED)-{$id})
                )
            ORDER BY article_id
                ";
        $data_article_per_next = Db::query($sql);
        $data_article_per = array();
        $data_article_next = array();
        if(count($data_article_per_next)>0){
            if(count($data_article_per_next)==1){
                if($data_article_per_next[0]['article_id']>$id){
                    //第一条
                    $data_article_next = $data_article_per_next[0];
                    $data_article_per['have'] = 'no';
                    $data_article_next['have'] = 'yes';
                }else{
                    //最后一条
                    $data_article_per = $data_article_per_next[0];
                    $data_article_per['have'] = 'yes';
                    $data_article_next['have'] = 'no';
                }
            }else{
                $data_article_per = $data_article_per_next[0];
                $data_article_next = $data_article_per_next[1];
                $data_article_next['have'] = 'yes';
                $data_article_per['have'] = 'yes';
            }
        }else{
            //没有前后记录
            $data_article_per['have'] = 'no';
            $data_article_next['have'] = 'no';
        }
        $this->assign('data_per_next', $data_article_per_next);
        $this->assign('data_per', $data_article_per);
        $this->assign('data_next', $data_article_next);
        //渲染模板
        return $this -> fetch();
    }
}
?>
