
<?php
defined('IN_PHPCMS') or exit('No permission resources.');

class category {
    public function info() {
        $catid = intval($_GET['catid']);
        if (!$catid) return $this->error('缺少catid');

        $category = getcache('category_content_1', 'commons');
        if (!isset($category[$catid])) return $this->error('栏目不存在');

        $this->success($category[$catid]);
    }

    public function children() {
        $catid = intval($_GET['catid']);
        $category = getcache('category_content_1', 'commons');
        $data = [];

        foreach ($category as $id => $cat) {
            if ($cat['parentid'] == $catid) {
                $data[] = $cat;
            }
        }

        $this->success($data);
    }

    private function success($data) {
	header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['code' => 200, 'data' => $data], JSON_UNESCAPED_UNICODE); exit;
    }

    private function error($msg) {
	header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['code' => 400, 'msg' => $msg], JSON_UNESCAPED_UNICODE); exit;
    }
}

