<?php
/**
 * 七牛云存储驱动
 * 上传使用上传凭证（PutPolicy），私有空间下载使用带时效的下载凭证
 */
require_once 'storage_interface.class.php';

class qiniu_storage implements storage_interface {

    private $access_key;
    private $secret_key;
    private $bucket;
    private $domain;      // 空间访问域名（如 cdn.example.com 或 x.xxxx.clouddn.com）
    private $use_ssl;
    private $upload_host; // 上传域名，默认 https://up.qiniup.com
    private $cdn_domain;  // CDN/自定义访问域名（配置后 getUrl 返回持久直链）

    public function __construct($config = array()) {
        $this->access_key = isset($config['access_key']) ? $config['access_key'] : '';
        $this->secret_key = isset($config['secret_key']) ? $config['secret_key'] : '';
        $this->bucket     = isset($config['bucket']) ? $config['bucket'] : '';
        $this->domain     = rtrim(isset($config['domain']) ? $config['domain'] : '', '/');
        $this->use_ssl    = !empty($config['is_https']);
        $this->upload_host = rtrim(isset($config['upload_host']) ? $config['upload_host'] : 'https://up.qiniup.com', '/');
        $this->cdn_domain  = isset($config['cdn_domain']) ? rtrim($config['cdn_domain'], '/') : '';
    }

    private function base64UrlSafe($data) {
        return str_replace(array('+', '/'), array('-', '_'), base64_encode($data));
    }

    public function put($localFile, $remotePath) {
        if (!is_file($localFile)) return false;
        $key = ltrim($remotePath, '/');
        $scope = $this->bucket . ':' . $key;
        $putPolicy = json_encode(array(
            'scope' => $scope,
            'deadline' => time() + 3600,
        ));
        $encodedPolicy = $this->base64UrlSafe($putPolicy);
        $sign = hash_hmac('sha1', $encodedPolicy, $this->secret_key, true);
        $token = $this->access_key . ':' . $this->base64UrlSafe($sign) . ':' . $encodedPolicy;

        $url = $this->upload_host;
        $postFields = array(
            'key' => $key,
            'token' => $token,
            'file' => new CURLFile($localFile),
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        @unlink($localFile);
        return ($httpCode >= 200 && $httpCode < 300);
    }

    public function delete($remotePath) {
        // 七牛删除需调用管理 API：POST /delete/<encodedEntryURI>，需要额外管理凭证。
        // 此处通过带时效的签名 URL 无法删除；返回 false 并提示（默认七牛删除建议使用官方工具/SDK）。
        return false;
    }

    /**
     * 生成带时效的下载签名 URL（私有空间）
     */
    public function getUrl($remotePath, $expires = 3600) {
        $key = ltrim($remotePath, '/');
        // 配置了 CDN/自定义域名则返回持久直链
        if (!empty($this->cdn_domain)) {
            return $this->cdn_domain . '/' . $key;
        }
        $baseURL = ($this->use_ssl ? 'https://' : 'http://') . $this->domain . '/' . $key;
        $deadline = time() + $expires;
        $urlToSign = $baseURL . '?e=' . $deadline;
        $sign = hash_hmac('sha1', $urlToSign, $this->secret_key, true);
        $token = $this->access_key . ':' . $this->base64UrlSafe($sign);
        return $urlToSign . '&token=' . $token;
    }
}
