
<?php
defined('IN_PHPCMS') or exit('No permission resources.');

pc_base::load_app_func('global');
pc_base::load_sys_class('format', '', 0);

class index {
    private $db;
    private $category;

    public function __construct() {
        $this->db = pc_base::load_model('content_model');
        $this->category = getcache('category_content_1','commons');
    }

    // 列表接口：index.php?m=api&c=index&a=lists&catid=1&page=1

	public function lists() {
		$catid = intval($_GET['catid']);
		if (!$catid) {
			$this->json(['code' => 400, 'msg' => '缺少 catid 参数']);
			return;
		}

		$categorys = getcache('category_content_1', 'commons');
		if (!isset($categorys[$catid])) {
			$this->json(['code' => 404, 'msg' => '栏目不存在']);
			return;
		}

		$category = $categorys[$catid];
		$type = $category['type'];

		if ($type == 1) {
			// 单页栏目，取 page 表数据
			$page_db = pc_base::load_model('page_model');
			$info = $page_db->get_one(['catid' => $catid]);

			$this->json([
				'code' => 200,
				'type' => 'page',
				'data' => $info
			]);
			return;

		} elseif ($type == 2) {
			// 外部链接，直接返回 URL
			$this->json([
				'code' => 200,
				'type' => 'link',
				'data' => $category
			]);
			return;

		} else {
			// 普通栏目，查文章列表
			$content_db = pc_base::load_model('content_model');
			$content_db->set_model($category['modelid']);

			$datas = $content_db->select(['catid' => $catid, 'status' => 99], '*', 10, 'id DESC');

			$this->json([
				'code' => 200,
				'type' => 'list',
				'data' => $datas
			]);
		}
	}
    // 详情接口：index.php?m=api&c=index&a=show&catid=1&id=100
    public function show() {
        $catid = intval($_GET['catid']);
        $id = intval($_GET['id']);

        if (!$catid || !$id || !isset($this->category[$catid])) {
            $this->json(['code' => 1, 'msg' => '栏目或ID无效']);
            return;
        }

        $modelid = $this->category[$catid]['modelid'];
        $this->db->set_model($modelid);

        $info = $this->db->get_one(['id' => $id, 'catid' => $catid, 'status' => 99]);

        if (!$info) {
            $this->json(['code' => 1, 'msg' => '内容不存在']);
            return;
        }

        // 如果有副表内容，合并之
        $this->db->table_name = $this->db->table_name . '_data';
        $more = $this->db->get_one(['id' => $id]);
        if ($more) {
            $info = array_merge($info, $more);
        }

        $this->json([
            'code' => 0,
            'msg' => 'success',
            'data' => $info
        ]);
    }

    private function json($data) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

