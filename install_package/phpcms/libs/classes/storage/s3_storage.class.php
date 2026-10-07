<?php
/**
 * 亚马逊云 S3 存储驱动（AWS Signature V4，S3 兼容）
 * 复用 MinIO 驱动的 S3 实现，endpoint 默认指向 AWS S3
 * 支持 CDN/自定义域名配置：配置 cdn_domain 后返回持久直链
 */
require_once 'minio_storage.class.php';

class s3_storage extends minio_storage {

    public function __construct($config = array()) {
        // 未指定 endpoint 时按 region 拼 AWS S3 地址
        if (empty($config['endpoint'])) {
            $region = isset($config['region']) ? trim($config['region']) : '';
            $secure = !empty($config['secure']);
            $scheme = $secure ? 'https://' : 'http://';
            if ($region) {
                $config['endpoint'] = $scheme . 's3.' . $region . '.amazonaws.com';
            } else {
                $config['endpoint'] = $scheme . 's3.amazonaws.com';
            }
        }
        parent::__construct($config);
    }
}
