<?php
namespace app\index\controller;
use think\Db;

class join extends base
{
    public function index()
    {
        if (input('param.id') != '') {
            $id = intval(input('param.id'));
            $this->assign('id', $id);
        }else{
            $this->error('错误的参数');exit;
        }
        //新闻文章内容
        $data_join = Db::name('article_cat')->where(
            array(
                'cat_id'=>$id
            )
        )->find();
        $this->assign('data', $data_join);
        //渲染模板
        return $this -> fetch();
    }
    public function act_add_message(){
        /* 没有验证码时，用时间来限制机器人发帖或恶意发评论 */
        if (!isset($_SESSION['send_time']))
        {
            $_SESSION['send_time'] = 0;
        }

        $cur_time = $this->gmtime();
        if (($cur_time - $_SESSION['send_time']) < 1) // 小于30秒禁止发评论
        {
            $this->error('您的操作太快，您至少30秒以后才能提交！');exit;
        }
        $quyu = isset($_POST['user_qy']) ? trim($_POST['user_qy'])     : '';
        $content = isset($_POST['user_content']) ? trim($_POST['user_content']) : '';
        $message = array(
            'parent_id'   => 0,
            'user_id'   => 1,
            'user_name'   => isset($_POST['user_name']) ? trim($_POST['user_name'])     : '',
            'user_email'   => isset($_POST['user_tel']) ? trim($_POST['user_tel'])     : '',
            'msg_title'   => isset($_POST['user_qq']) ? trim($_POST['user_qq'])     : '',
            'msg_content' =>  $quyu.$content,
            'msg_type'   => 0,
            'msg_status'   => 1,
            'msg_time'   => time(),
            'order_id'   => isset($_POST['id']) ? trim($_POST['id'])     : '0',
            'msg_area'      => 0
        );

        $result = Db::table('blb_feedback')->insert($message);

        if ($result)
        {
            $this->success('您的留言已提交，我们会及时联系您的。');exit;
        }
        else
        {
            $this->error('错误的留言');exit;
        }

    }
    function gmtime()
    {
        return (time() - date('Z'));
    }
}
?>
