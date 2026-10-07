<?php
/**
 * 存储驱动工厂：根据配置选择并实例化对应存储驱动
 * 支持：disk（磁盘，默认）/ minio（MinIO）/ oss（阿里云OSS）/ cos（腾讯云COS）/ qiniu（七牛）
 */
require_once 'storage_interface.class.php';
require_once 'disk_storage.class.php';
require_once 'minio_storage.class.php';
require_once 'oss_storage.class.php';
require_once 'cos_storage.class.php';
require_once 'qiniu_storage.class.php';
require_once 's3_storage.class.php';

class storage_factory {

    /**
     * 获取当前配置的存储驱动实例（单例）
     */
    public static function get() {
        static $instance = null;
        if ($instance !== null) return $instance;

        // 优先读取独立存储配置文件 caches/configs/storage.php（后台"存储设置"保存的位置）
        $storage = null;
        $storage_config_file = defined('CACHE_PATH') ? CACHE_PATH.'configs/storage.php' : '';
        if ($storage_config_file && file_exists($storage_config_file)) {
            $storage = @include $storage_config_file;
        }
        if (!is_array($storage) || empty($storage['driver'])) {
            $storage = pc_base::load_config('system', 'storage');
        }
        $driver = isset($storage['driver']) ? $storage['driver'] : 'disk';
        $config = isset($storage[$driver]) ? $storage[$driver] : array();

        switch ($driver) {
            case 'minio':
                $instance = new minio_storage($config);
                break;
            case 'oss':
                $instance = new oss_storage($config);
                break;
            case 'cos':
                $instance = new cos_storage($config);
                break;
            case 'qiniu':
                $instance = new qiniu_storage($config);
                break;
            case 's3':
                $instance = new s3_storage($config);
                break;
            case 'disk':
                // 磁盘驱动默认取 system 的附件路径配置
                if (empty($config)) {
                    $config = array(
                        'upload_path' => pc_base::load_config('system', 'upload_path'),
                        'upload_url'  => pc_base::load_config('system', 'upload_url'),
                    );
                }
                $instance = new disk_storage($config);
                break;
            default:
                // 自定义/未知远程驱动（S3 兼容对象存储）按 s3 处理，实现可扩展新增驱动
                $instance = new s3_storage($config);
                break;
        }
        return $instance;
    }
}

/**
 * 全局函数：生成存储文件的访问 URL
 * - 磁盘驱动：返回静态 URL
 * - 远程驱动（MinIO/OSS/COS/七牛）：返回带时效的签名 URL
 *
 * @param string $filepath 存储中的相对路径（如 2026/10/07/xxx.jpg）
 * @param int    $expires  有效期（秒），默认 3600
 * @return string
 */
function storage_url($filepath, $expires = 3600) {
    $storage = storage_factory::get();
    return $storage->getUrl($filepath, $expires);
}
