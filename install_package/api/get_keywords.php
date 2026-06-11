<?php

/**
 * 获取关键字接口
 * @author Snow & Love <phpcms@msn.com>
 * @version 1.1
 */
defined('IN_PHPCMS') or exit('No permission resources.');
define('API_URL_GET_KEYWORDS', 'https://bosonnlp.com/analysis/key');

$number = intval($_GET['number']);
$title = $_POST['data'];
echo get_keywords($title, $number);

function get_keywords($title, $number = 3)
{
    $title = trim(strip_tags($title));
    if (empty($title)) {
        return '';
    }
    $params = array(
        'http' => array(
            'method'  => 'POST',
            'timeout' => 5,
            'header'  => "Content-type:application/x-www-form-urlencoded",
            'content' => http_build_query(array('data' => $title)),
        )
    );
    $rs = file_get_contents(API_URL_GET_KEYWORDS, false, stream_context_create($params));
    if (!$rs) {
        return '';
    }
    $data = json_decode($rs, true);
    if (!$data || empty($data)) {
        return '';
    }
    if (function_exists('array_column')) {
        $keywords = array_column(array_slice($data, 0, $number), 1);
    } else {
        foreach (array_slice($data, 0, $number) as $v) {
            $keywords[] = $v[1];
        }
    }
    if (CHARSET != 'utf-8') {
        return iconv('utf-8', 'gbk', implode(' ', $keywords));
    } else {
        return implode(' ', $keywords);
    }
}
