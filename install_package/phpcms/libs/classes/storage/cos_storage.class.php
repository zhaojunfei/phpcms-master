<?php
/**
 * 腾讯云 COS 存储驱动
 * 基于 COS V5 签名算法（HMAC-SHA1）实现，不依赖外部 SDK
 */
require_once 'storage_interface.class.php';

class cos_storage implements storage_interface {

    private $region;
    private $secret_id;
    private $secret_key;
    private $bucket;     // 如 test-1250000000
    private $use_ssl;
    private $host;
    private $cdn_domain; // CDN/自定义访问域名（配置后 getUrl 返回持久直链）

    public function __construct($config = array()) {
        $this->region      = isset($config['region']) ? trim($config['region']) : 'ap-guangzhou';
        $this->secret_id   = isset($config['secret_id']) ? $config['secret_id'] : '';
        $this->secret_key  = isset($config['secret_key']) ? $config['secret_key'] : '';
        $this->bucket      = isset($config['bucket']) ? $config['bucket'] : '';
        $this->use_ssl     = !empty($config['is_https']);
        $this->cdn_domain  = isset($config['cdn_domain']) ? rtrim($config['cdn_domain'], '/') : '';
        $this->host = $this->bucket . '.cos.' . $this->region . '.myqcloud.com';
    }

    private function baseUrl() {
        return ($this->use_ssl ? 'https://' : 'http://') . $this->host;
    }

    public function put($localFile, $remotePath) {
        if (!is_file($localFile)) return false;
        $body = file_get_contents($localFile);
        $key = '/' . ltrim($remotePath, '/');
        $now = time();

        $contentType = $this->mime($key);
        $headers = array(
            'host' => $this->host,
            'content-type' => $contentType,
        );
        $auth = $this->sign('put', $key, '', $headers, $now, $now + 3600);

        $url = $this->baseUrl() . $key;
        $reqHeaders = array(
            'Authorization: ' . $auth,
            'Content-Type: ' . $contentType,
            'Content-Length: ' . strlen($body),
            'Host: ' . $this->host,
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $reqHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        @unlink($localFile);
        return ($httpCode >= 200 && $httpCode < 300);
    }

    public function delete($remotePath) {
        $key = '/' . ltrim($remotePath, '/');
        $now = time();
        $headers = array('host' => $this->host);
        $auth = $this->sign('delete', $key, '', $headers, $now, $now + 3600);

        $url = $this->baseUrl() . $key;
        $reqHeaders = array(
            'Authorization: ' . $auth,
            'Host: ' . $this->host,
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $reqHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode >= 200 && $httpCode < 300);
    }

    /**
     * 生成带时效的预签名 URL
     */
    public function getUrl($remotePath, $expires = 3600) {
        // 配置了 CDN/自定义域名则返回持久直链
        if (!empty($this->cdn_domain)) {
            return $this->cdn_domain . '/' . ltrim($remotePath, '/');
        }
        $key = '/' . ltrim($remotePath, '/');
        $now = time();
        $end = $now + $expires;
        $headers = array('host' => $this->host);

        $signKey = hash_hmac('sha1', $now . ';' . $end, $this->secret_key);
        $httpString = "get\n" . $key . "\n\nhost=" . $this->host . "\n";
        $stringToSign = "sha1\n" . $now . ';' . $end . "\n" . sha1($httpString);
        $signature = hash_hmac('sha1', $stringToSign, $signKey);

        $url = $this->baseUrl() . $key;
        return $url . '?q-sign-algorithm=sha1' .
            '&q-ak=' . rawurlencode($this->secret_id) .
            '&q-sign-time=' . $now . ';' . $end .
            '&q-key-time=' . $now . ';' . $end .
            '&q-header-list=host' .
            '&q-url-param-list=' .
            '&q-signature=' . $signature;
    }

    private function sign($method, $uri, $query, $headers, $start, $end) {
        $keyTime = $start . ';' . $end;
        $signKey = hash_hmac('sha1', $keyTime, $this->secret_key);

        // header 按 name 小写、字典序排序
        $sorted = array();
        foreach ($headers as $k => $v) {
            $sorted[strtolower($k)] = trim($v);
        }
        ksort($sorted);
        $headerStr = array();
        $headerList = array();
        foreach ($sorted as $k => $v) {
            $headerStr[] = rawurlencode($k) . '=' . rawurlencode($v);
            $headerList[] = rawurlencode($k);
        }
        $httpString = strtolower($method) . "\n" . $uri . "\n" . $query . "\n" . implode('&', $headerStr) . "\n";
        $stringToSign = "sha1\n" . $keyTime . "\n" . sha1($httpString);
        $signature = hash_hmac('sha1', $stringToSign, $signKey);

        return 'q-sign-algorithm=sha1&q-ak=' . rawurlencode($this->secret_id) .
            '&q-sign-time=' . $keyTime .
            '&q-key-time=' . $keyTime .
            '&q-header-list=' . implode(';', $headerList) .
            '&q-url-param-list=' .
            '&q-signature=' . $signature;
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
}
