<?php
namespace app\index\controller;

class Index extends base
{
    public function index()
    {

        $url = "http://www.bellabee.cn/info.php";
        $this->assign('url', $url);
        $flash_img = $this->getcurl($url);
        $data = array();
        if($flash_img !=''){
            $data = json_decode($flash_img,true);
            //print_r($data);die;
        }
        $this->assign('flash_data', $data);
        //渲染模板
        return $this -> fetch();
    }

    function getcurl($url)
    {
        $ch = curl_init();
        //设置选项，包括URL
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        //返回处理josn
        return $output;
    }
    function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = object_array($value);
            }
        }
        return $array;
    }
}
?>
