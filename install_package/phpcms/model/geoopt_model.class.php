<?php
defined('IN_PHPCMS') or exit('No permission resources.');
pc_base::load_sys_class('model', '', 0);
/**
 * GEO 优化模块模型
 * 操作 v9_geo_faq（FAQ 问答对，关联内容）
 */
class geoopt_model extends model {
    public function __construct() {
        $this->db_config = pc_base::load_config('database');
        $this->db_setting = 'default';
        $this->table_name = 'geo_faq';
        parent::__construct();
    }
    /**
     * 读取 GEO 配置（不存在则返回默认）
     */
    public function get_config() {
        $c = @include(CACHE_PATH.'configs'.DIRECTORY_SEPARATOR.'geoopt.php');
        if (!is_array($c)) $c = array();
        $def = array(
            'site_name' => '站点名称',
            'site_desc' => '',
            'schema_enable' => '1',
            'faq_enable' => '1',
        );
        return array_merge($def, $c);
    }
    /**
     * 保存 GEO 配置
     */
    public function set_config($data) {
        $c = "<?php\nreturn ".var_export($data, true).";\n";
        return @file_put_contents(CACHE_PATH.'configs'.DIRECTORY_SEPARATOR.'geoopt.php', $c) !== false;
    }
    /**
     * 列出所有内容模型(type=0)及其表名
     */
    public function content_models() {
        $r = pc_base::load_model('sitemodel_model');
        $list = $r->select(array('type'=>'0','disabled'=>'0'));
        return $list;
    }
    /**
     * 某内容表总条数
     */
    public function count_content($tb) {
        $sql = "SELECT COUNT(*) AS n FROM `v9_$tb`";
        $r = $this->db->query($sql);
        $row = $this->db->fetch_array($r);
        return $row ? intval($row['n']) : 0;
    }
    /**
     * 某内容表某字段非空条数
     */
    public function count_has($tb, $field) {
        $sql = "SELECT COUNT(*) AS n FROM `v9_$tb` WHERE `$field`<>''";
        $r = $this->db->query($sql);
        $row = $this->db->fetch_array($r);
        return $row ? intval($row['n']) : 0;
    }
    /**
     * 某模型下有 FAQ 的内容条数（按 contentid 去重）
     */
    public function count_faq_by_model($modelid) {
        $sql = "SELECT COUNT(DISTINCT contentid) AS n FROM `v9_geo_faq` WHERE modelid='$modelid'";
        $r = $this->db->query($sql);
        $row = $this->db->fetch_array($r);
        return $row ? intval($row['n']) : 0;
    }
    /**
     * 某模型下 FAQ 总条数
     */
    public function count_faq_total_by_model($modelid) {
        $sql = "SELECT COUNT(*) AS n FROM `v9_geo_faq` WHERE modelid='$modelid'";
        $r = $this->db->query($sql);
        $row = $this->db->fetch_array($r);
        return $row ? intval($row['n']) : 0;
    }
    /**
     * 某内容(跨表不确定，直接按 contentid) 的 FAQ 条数
     */
    public function count_faq($contentid) {
        $sql = "SELECT COUNT(*) AS n FROM `v9_geo_faq` WHERE contentid='$contentid'";
        $r = $this->db->query($sql);
        $row = $this->db->fetch_array($r);
        return $row ? intval($row['n']) : 0;
    }
    /**
     * 某内容表按关键词列出内容
     */
    public function list_content($tb, $kw='') {
        $where = "1=1";
        if ($kw !== '') $where .= " AND (`title` LIKE '%$kw%' OR `keywords` LIKE '%$kw%')";
        $sql = "SELECT `id`,`title`,`keywords`,`description`,`inputtime` FROM `v9_$tb` WHERE $where ORDER BY `id` DESC";
        $r = $this->db->query($sql);
        $out = array();
        while ($row = $this->db->fetch_array($r)) $out[] = $row;
        return $out;
    }
    /**
     * 按内容ID 取 FAQ 列表（前台 show 页调用）
     */
    public function faqs_by_content($contentid) {
        return $this->select(array('contentid'=>intval($contentid)), '*', '', 'listorder ASC, id ASC');
    }
}
