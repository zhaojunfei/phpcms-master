<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_app_class('admin','admin',0);
/**
 * GEO（生成式引擎优化，大模型优化）模块后台控制器
 * 能力：内容结构化(schema) / 内容问答式优化(FAQ) / GEO效果统计
 */
class admin_geoopt extends admin {
    private $db;
    public function __construct() {
        parent::__construct();
        $this->db = pc_base::load_model('geoopt_model');
    }

    /**
     * GEO 效果统计面板
     */
    public function init() {
        $conf = $this->db->get_config();
        $models = $this->db->content_models();
        // 聚合各内容模型统计
        $stat = array(
            'total' => 0, 'title' => 0, 'keywords' => 0, 'desc' => 0,
            'faq_contents' => 0, 'score_sum' => 0,
            'models' => array(),
        );
        $faq_total = 0;
        foreach ($models as $m) {
            $table = 'content'; // 占位
            $tb = $m['tablename'];
            $cnt = $this->db->count_content($tb);
            $has_keywords = $this->db->count_has($tb, 'keywords');
            $has_desc = $this->db->count_has($tb, 'description');
            $model_faq = $this->db->count_faq_by_model($m['modelid']);
            $stat['total'] += $cnt;
            $stat['keywords'] += $has_keywords;
            $stat['desc'] += $has_desc;
            $stat['faq_contents'] += $model_faq;
            $faq_total += $this->db->count_faq_total_by_model($m['modelid']);
            // 每条内容完成度：有标题(20)+关键词(20)+摘要(20)+FAQ(20)+schema(20)
            $avg = $cnt > 0 ? round(($has_desc + $has_keywords) / $cnt * 40 + ($cnt > 0 ? 20 : 0) + ($model_faq>0 ? 0 : 0), 1) : 0;
            $stat['models'][] = array(
                'name' => $m['name'], 'tablename' => $tb, 'total' => $cnt,
                'keywords' => $has_keywords, 'desc' => $has_desc,
                'faq_contents' => $model_faq,
            );
            $stat['score_sum'] += $cnt > 0 ? $cnt * $avg : 0;
        }
        $stat['faq_total'] = $faq_total;
        $stat['schema_enable'] = $conf['schema_enable'];
        // 平均完成度
        $stat['avg_score'] = $stat['total'] > 0 ? round($stat['score_sum'] / $stat['total'], 1) : 0;
        // 用于面板展示的 JSON 数据
        $stat['chart'] = array(
            'total' => $stat['total'],
            'keywords' => $stat['keywords'],
            'desc' => $stat['desc'],
            'faq_contents' => $stat['faq_contents'],
            'schema_enable' => $stat['schema_enable'],
            'avg_score' => $stat['avg_score'],
        );
        include $this->admin_tpl('geoopt_index');
    }

    /**
     * 内容 GEO 优化列表（跨全部内容模型，含完成度评分）
     */
    public function list() {
        $models = $this->db->content_models();
        $kw = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $pagesize = 15;
        // 收集所有内容
        $rows = array();
        $all_total = 0;
        foreach ($models as $m) {
            $tb = $m['tablename'];
            $list = $this->db->list_content($tb, $kw);
            foreach ($list as $r) {
                $cid = $r['id'];
                $faq_count = $this->db->count_faq($cid);
                $score = 0;
                if (!empty($r['title'])) $score += 20;
                if (!empty($r['keywords'])) $score += 20;
                if (!empty($r['description'])) $score += 20;
                if ($faq_count > 0) $score += 20;
                $score += 20; // schema 默认启用
                $rows[] = array(
                    'id' => $cid, 'model' => $m['name'], 'tablename' => $tb,
                    'title' => $r['title'], 'keywords' => $r['keywords'],
                    'description' => $r['description'], 'inputtime' => $r['inputtime'],
                    'faq_count' => $faq_count, 'score' => $score,
                );
                $all_total++;
            }
        }
        // 排序：完成度低的优先
        usort($rows, function($a, $b){ return $a['score'] <=> $b['score']; });
        $total = count($rows);
        $pages = pages($total, $page, $pagesize);
        $start = ($page - 1) * $pagesize;
        $page_rows = array_slice($rows, $start, $pagesize);
        include $this->admin_tpl('geoopt_list');
    }

    /**
     * GEO 站点配置
     */
    public function config() {
        if (isset($_POST['dosubmit'])) {
            $data = array(
                'site_name' => isset($_POST['site_name']) ? trim($_POST['site_name']) : '',
                'site_desc' => isset($_POST['site_desc']) ? trim($_POST['site_desc']) : '',
                'schema_enable' => isset($_POST['schema_enable']) ? '1' : '0',
                'faq_enable' => isset($_POST['faq_enable']) ? '1' : '0',
            );
            if ($this->db->set_config($data)) {
                showmessage('配置已保存', '?m=geoopt&c=admin_geoopt&a=config&pc_hash='.$_GET['pc_hash']);
            } else showmessage('配置保存失败', HTTP_REFERER);
        } else {
            $conf = $this->db->get_config();
            include $this->admin_tpl('geoopt_config');
        }
    }

    /**
     * FAQ 列表
     */
    public function faq() {
        $where = '1=1';
        $kw = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
        if ($kw !== '') $where .= " AND (`question` LIKE '%$kw%' OR `answer` LIKE '%$kw%')";
        $cid = isset($_GET['contentid']) ? intval($_GET['contentid']) : 0;
        if ($cid) $where .= " AND `contentid`='$cid'";
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $pagesize = 15;
        $list = $this->db->listinfo($where, 'listorder ASC, id DESC', $page, $pagesize);
        $pages = $this->db->pages;
        include $this->admin_tpl('geoopt_faq');
    }

    /**
     * FAQ 添加
     */
    public function faq_add() {
        if (isset($_POST['dosubmit'])) {
            $info = isset($_POST['info']) ? $_POST['info'] : array();
            $info['contentid'] = intval($info['contentid']);
            $info['inputtime'] = SYS_TIME;
            $info['id'] = $this->db->insert($info, true);
            showmessage('FAQ 添加成功', '?m=geoopt&c=admin_geoopt&a=faq&pc_hash='.$_GET['pc_hash']);
        } else {
            include $this->admin_tpl('geoopt_faq_add');
        }
    }

    /**
     * FAQ 编辑
     */
    public function faq_edit() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if (!$id) showmessage('参数错误');
        if (isset($_POST['dosubmit'])) {
            $info = isset($_POST['info']) ? $_POST['info'] : array();
            $info['contentid'] = intval($info['contentid']);
            $this->db->update($info, array('id'=>$id));
            showmessage('FAQ 更新成功', '?m=geoopt&c=admin_geoopt&a=faq&pc_hash='.$_GET['pc_hash']);
        } else {
            $data = $this->db->get_one(array('id'=>$id));
            if (!$data) showmessage('记录不存在');
            include $this->admin_tpl('geoopt_faq_add');
        }
    }

    /**
     * FAQ 删除
     */
    public function faq_delete() {
        $ids = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? array(intval($_GET['id'])) : array());
        if (!$ids) showmessage('请选择要删除的记录');
        foreach ((array)$ids as $id) {
            $this->db->delete(array('id' => intval($id)));
        }
        showmessage('删除成功', '?m=geoopt&c=admin_geoopt&a=faq&pc_hash='.$_GET['pc_hash']);
    }
}
