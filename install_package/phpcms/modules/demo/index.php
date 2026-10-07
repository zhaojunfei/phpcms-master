<?php
defined('IN_PHPCMS') or exit('No permission resources.');

class index {
    function __construct() {
        $ss = 'session_'.pc_base::load_config('system','session_storage'); pc_base::load_sys_class($ss);
        $this->db = pc_base::load_model('demo_model');
        $this->badword_cache = getcache('badword', 'commons');
    }
    private function check_badword($text) {
        if (empty($this->badword_cache) || !is_array($this->badword_cache)) return array(true, $text);
        foreach ($this->badword_cache as $bw) {
            $word = $bw['badword']; if (!$word) continue;
            if (mb_strpos($text, $word, 0, 'UTF-8') !== false) {
                if (intval($bw['level']) === 1) return array(false, $word);
                $text = str_replace($word, $bw['replaceword'] ?: '***', $text);
            }
        }
        return array(true, $text);
    }
    public function init() {
        $msg = '';
        if (isset($_POST['dosubmit'])) {
            if (empty($_SESSION['code']) || strtolower(trim($_POST['code'])) != $_SESSION['code']) {
                $msg = '验证码错误，请重新输入'; $_SESSION['code'] = '';
            } else {
                $_SESSION['code'] = '';
                $title = trim($_POST['title']);
                $content = trim($_POST['content']);
                $inputtime = trim($_POST['inputtime']);
                if ($title === '') { $msg = '标题为必填项'; }
                else {
                    $data = array('title' => trim($_POST['title']), 'content' => trim($_POST['content']), 'inputtime' => trim($_POST['inputtime']), 'inputtime' => SYS_TIME);
                    $hit = '';
                    foreach ($data as $k => $v) { list($ok, $data[$k]) = $this->check_badword($v); if (!$ok) { $hit = $v; break; } }
                    if ($hit) { $msg = '内容包含敏感词：' . $hit; }
                    else { $this->db->insert($data); $msg = '提交成功！'; $_POST = array(); }
                }
            }
        }
        include $this->_form_tpl($msg);
    }
    private function _form_tpl($msg) {
        pc_base::load_sys_class('form', '', 0);
        $old_title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '';
        $old_content = isset($_POST['content']) ? htmlspecialchars($_POST['content']) : '';
        $old_inputtime = isset($_POST['inputtime']) ? htmlspecialchars($_POST['inputtime']) : '';
        $code_img = form::checkcode('checkcode', 4, 16, 100, 36);
        ob_start();
        ?>
<!DOCTYPE html><html lang="zh-CN"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1"><title>示例模块</title>
<style>
body{font-family:'Microsoft YaHei',Arial,sans-serif;max-width:560px;margin:40px auto;padding:0 20px;color:#333;background:#f9f9f9}
h1{font-size:22px;border-bottom:2px solid #4a90d9;padding-bottom:10px}
.box{background:#fff;padding:24px;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.08)}
.row{margin-bottom:16px}.row label{display:block;margin-bottom:6px;font-size:14px;color:#555}
.row input,.row textarea{width:100%;padding:8px 10px;border:1px solid #ddd;border-radius:4px;font-size:14px;box-sizing:border-box}
.row textarea{height:80px;resize:vertical}
.msg{padding:10px 14px;border-radius:4px;margin-bottom:16px;font-size:14px}
.msg.err{background:#fde8e8;color:#c0392b}.msg.ok{background:#e8f8ee;color:#27ae60}
.code-row{display:flex;gap:10px;align-items:center}.code-row input{flex:1}.code-row img{cursor:pointer;border:1px solid #ddd;border-radius:4px}
.btn{background:#4a90d9;color:#fff;border:none;padding:10px 24px;border-radius:4px;font-size:15px;cursor:pointer}
.req{color:#c0392b}
</style></head><body><h1>示例模块</h1><div class="box">
<?php if($msg){?><div class="msg <?php echo strpos($msg,'成功')!==false?'ok':'err';?>"><?php echo htmlspecialchars($msg);?></div><?php }?>
<form method="post" action="">
  <div class="row"><label>标题 <span class="req">*</span></label><input type="text" name="title" value="<?php echo $old_title; ?>"  required></div>
  <div class="row"><label>内容</label><textarea name="content"><?php echo $old_content; ?></textarea></div>
  <div class="row"><label>录入时间</label><input type="datetime-local" name="inputtime" value="<?php echo $old_inputtime; ?>"  required></div>
  <div class="row"><label>验证码 <span class="req">*</span></label><div class="code-row"><input type="text" name="code" maxlength="4" required><?php echo $code_img; ?></div></div>
<button type="submit" name="dosubmit" value="1" class="btn">提交</button>
</form></div></body></html>
        <?php echo ob_get_clean(); }
}