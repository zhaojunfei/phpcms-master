<?php
/**
 * 磁盘存储驱动（默认）
 */
require_once 'storage_interface.class.php';

class disk_storage implements storage_interface {

    private $upload_path; // 本地存储根目录（绝对路径）
    private $upload_url;  // 本地存储访问根 URL

    public function __construct($config = array()) {
        $this->upload_path = isset($config['upload_path']) ? rtrim($config['upload_path'], '/') . '/' : '';
        $this->upload_url  = isset($config['upload_url'])  ? rtrim($config['upload_url'], '/')  . '/' : '';
    }

    public function put($localFile, $remotePath) {
        pc_base::load_sys_func('dir');
        $savefile = $this->upload_path . $remotePath;
        $dir = dirname($savefile);
        if (!dir_create($dir)) return false;
        if (!@is_dir($dir)) return false;
        @chmod($dir, 0777);
        if (@copy($localFile, $savefile)) {
            @chmod($savefile, 0644);
            @unlink($localFile);
            return true;
        }
        return false;
    }

    public function delete($remotePath) {
        $file = $this->upload_path . $remotePath;
        @unlink($file);
        // 删除可能存在的缩略图
        $thumbs = glob(dirname($file) . '/*' . basename($file));
        if ($thumbs) foreach ($thumbs as $thumb) @unlink($thumb);
        return true;
    }

    public function getUrl($remotePath, $expires = 3600) {
        return $this->upload_url . $remotePath;
    }
}
