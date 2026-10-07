<?php
/**
 * 阿里云 OSS 存储驱动
 * 基于 OSS V1 签名（HMAC-SHA1）实现，不依赖外部 SDK
 */
require_once 'storage_interface.class.php';

class oss_storage implements storage_interface {

    private $endpoint;      // 如 oss-cn-hangzhou.aliyuncs.com
    private $access_id;
    private $access_secret;
    private $bucket;
    private $use_ssl;
    private $cdn_domain;    // CDN/自定义访问域名（配置后 getUrl 返回持久直链）

    public function __construct($config = array()) {
        $this->endpoint      = isset($config['endpoint']) ? trim($config['endpoint']) : 'oss-cn-hangzhou.aliyuncs.com';
        $this->access_id     = isset($config['access_id']) ? $config['access_id'] : '';
        $this->access_secret = isset($config['access_secret']) ? $config['access_secret'] : '';
        $this->bucket        = isset($config['bucket']) ? $config['bucket'] : '';
        $this->use_ssl       = !empty($config['is_https']);
        $this->cdn_domain    = isset($config['cdn_domain']) ? rtrim($config['cdn_domain'], '/') : '';
    }

    private function baseUrl() {
        return ($this->use_ssl ? 'https://' : 'http://') . $this->bucket . '.' . $this->endpoint;
    }

    public function put($localFile, $remotePath) {
        if (!is_file($localFile)) return false;
        $body = file_get_contents($localFile);
        $key = ltrim($remotePath, '/');
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $contentType = $this->mime($key);
        $md5 = base64_encode(md5($body, true));

        $resource = '/' . $this->bucket . '/' . $key;
        $stringToSign = "PUT\n" . $md5 . "\n" . $contentType . "\n" . $date . "\n" . $resource;
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->access_secret, true));

        $url = $this->baseUrl() . '/' . $key;
        $headers = array(
            'Authorization: OSS ' . $this->access_id . ':' . $signature,
            'Date: ' . $date,
            'Content-Type: ' . $contentType,
            'Content-MD5: ' . $md5,
            'Content-Length: ' . strlen($body),
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        @unlink($localFile);
        return ($httpCode >= 200 && $httpCode < 300);
    }

    public function delete($remotePath) {
        $key = ltrim($remotePath, '/');
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $resource = '/' . $this->bucket . '/' . $key;
        $stringToSign = "DELETE\n\n\n" . $date . "\n" . $resource;
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->access_secret, true));

        $url = $this->baseUrl() . '/' . $key;
        $headers = array(
            'Authorization: OSS ' . $this->access_id . ':' . $signature,
            'Date: ' . $date,
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode >= 200 && $httpCode < 300);
    }

    /**
     * 生成带时效的签名 URL（私有空间访问）
     */
    public function getUrl($remotePath, $expires = 3600) {
        $key = ltrim($remotePath, '/');
        // 配置了 CDN/自定义域名则返回持久直链
        if (!empty($this->cdn_domain)) {
            return $this->cdn_domain . '/' . $this->urlEncode($key);
        }
        $expiresTime = SYS_TIME + $expires;
        $resource = '/' . $this->bucket . '/' . $key;
        $stringToSign = "GET\n\n\n" . $expiresTime . "\n" . $resource;
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->access_secret, true));

        $url = $this->baseUrl() . '/' . rawurlencode($key);
        $url = $this->baseUrl() . '/' . $this->urlEncode($key);
        return $url . '?OSSAccessKeyId=' . urlencode($this->access_id) .
            '&Expires=' . $expiresTime .
            '&Signature=' . urlencode($signature);
    }

    private function mime($key) {
        $ext = strtolower(pathinfo($key, PATHINFO_EXTENSION));
        $map = array(
            'jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif',
            'bmp'=>'image/bmp','webp'=>'image/webp','svg'=>'image/svg+xml','mp4'=>'video/mp4',
            'zip'=>'application/zip','pdf'=>'application/pdf','txt'=>'text/plain','doc'=>'application/msword',
            'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls'=>'application/vnd.ms-excel','xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'mp3'=>'audio/mpeg','wav'=>'audio/wav',
        );
        return isset($map[$ext]) ? $map[$ext] : 'application/octet-stream';
    }

    private function urlEncode($str) {
        return str_replace('%2F', '/', rawurlencode($str));
    }
}
