<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
class admin_demo extends admin {
    private $db;
    public function __construct() {
        parent::__construct();
        $this->db = pc_base::load_model('demo_model');
    }
    public function init() {
        $where = '1=1';
                $kw_title = isset($_GET['title']) ? trim($_GET['title']) : '';
        if ($kw_title !== '') $where .= " AND `title` LIKE '%$kw_title%'";

        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $pagesize = 15;
        $list = $this->db->listinfo($where, 'id DESC', $page, $pagesize);
        $pages = $this->db->pages;
        include $this->admin_tpl('demo_list');
    }
    public function add() {
        if (isset($_POST['dosubmit'])) {
            $info = isset($_POST['info']) ? $_POST['info'] : array();
                    if (!isset($info['inputtime']) || empty($info['inputtime'])) $info['inputtime'] = SYS_TIME;

            $info['id'] = $this->db->insert($info, true);
            showmessage('添加成功', '?m=demo&c=admin_demo&a=init');
        } else {
            include $this->admin_tpl('demo_add');
        }
    }
    public function edit() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) showmessage('参数错误');
        if (isset($_POST['dosubmit'])) {
            $info = isset($_POST['info']) ? $_POST['info'] : array();
            $this->db->update($info, array('id'=>$id));
            showmessage('更新成功', '?m=demo&c=admin_demo&a=init');
        } else {
            $data = $this->db->get_one(array('id'=>$id));
            if (!$data) showmessage('记录不存在');
            include $this->admin_tpl('demo_add');
        }
    }
    public function delete() {
        $ids = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? array(intval($_GET['id'])) : array());
        if (!$ids) showmessage('请选择要删除的记录');
        foreach ((array)$ids as $id) {
            $this->db->delete(array('id' => intval($id)));
        }
        showmessage('删除成功', '?m=demo&c=admin_demo&a=init&pc_hash='.$_GET['pc_hash']);
    }
}
