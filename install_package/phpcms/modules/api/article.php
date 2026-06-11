
<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_func('util', 'content');
pc_base::load_app_func('global', 'content');

class article {

    public function show() {
        $catid = intval($_GET['catid']);
        $id = intval($_GET['id']);
        if (!$catid || !$id) {
            return $this->error('参数错误');
        }

        $content_db = pc_base::load_model('content_model');
        $content_db->set_model($catid);
        $r = $content_db->get_content($catid, $id);
        if (!$r) return $this->error('内容不存在');

        $this->success($r);
    }

    public function lists() {
        $catid = intval($_GET['catid']);
        $page = max(1, intval($_GET['page']));
        $pagesize = isset($_GET['pagesize']) ? intval($_GET['pagesize']) : 10;

        if (!$catid) return $this->error('缺少栏目ID');

        $content_db = pc_base::load_model('content_model');
        $content_db->set_model($catid);

        $where = 'status=99';
        $datas = $content_db->listinfo($where, 'id DESC', $page, $pagesize);
        $this->success($datas);
    }

    public function position() {
        $posid = intval($_GET['posid']);
        $limit = intval($_GET['limit'] ?: 5);

        if (!$posid) return $this->error('缺少推荐位ID');

        $position_data_db = pc_base::load_model('position_data_model');
        $rs = $position_data_db->select("posid=$posid AND thumb != ''", '*', $limit, 'listorder DESC');

        $this->success($rs);
    }

    private function success($data) {
        echo json_encode(['code' => 200, 'data' => $data], JSON_UNESCAPED_UNICODE); exit;
    }

    private function error($msg) {
        echo json_encode(['code' => 400, 'msg' => $msg], JSON_UNESCAPED_UNICODE); exit;
    }
}

