<?php
/**
 * MinIO 存储驱动（S3 兼容）
 * 基于 AWS Signature V4 签名实现，不依赖外部 SDK
 */
require_once 'storage_interface.class.php';

class minio_storage implements storage_interface {

    private $endpoint;   // 如 http://127.0.0.1:9000
    private $access_key;
    private $secret_key;
    private $bucket;
    private $region;     // 默认 us-east-1
    private $use_ssl;
    private $cdn_domain; // CDN/自定义访问域名（配置后 getUrl 返回持久直链）

    public function __construct($config = array()) {
        $this->endpoint   = rtrim(isset($config['endpoint']) ? $config['endpoint'] : 'http://127.0.0.1:9000', '/');
        $this->access_key = isset($config['access_key']) ? $config['access_key'] : '';
        $this->secret_key = isset($config['secret_key']) ? $config['secret_key'] : '';
        $this->bucket     = isset($config['bucket']) ? $config['bucket'] : '';
        $this->region     = isset($config['region']) ? $config['region'] : 'us-east-1';
        $this->use_ssl    = !empty($config['secure']);
        $this->cdn_domain = isset($config['cdn_domain']) ? rtrim($config['cdn_domain'], '/') : '';
        if (strpos($this->endpoint, 'http') !== 0) {
            $this->endpoint = ($this->use_ssl ? 'https://' : 'http://') . $this->endpoint;
        }
    }

    /**
     * 上传：S3 PUT Object（AWS Signature V4）
     */
    public function put($localFile, $remotePath) {
        if (!is_file($localFile)) return false;
        $body = file_get_contents($localFile);
        if ($body === false) return false;

        $key = ltrim($remotePath, '/');
        $now = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');
        $payloadHash = hash('sha256', $body);

        $canonicalHeaders = "host:" . $this->hostHeader() . "\n" .
                            "x-amz-content-sha256:" . $payloadHash . "\n" .
                            "x-amz-date:" . $now . "\n";
        $signedHeaders = 'host;x-amz-content-sha256;x-amz-date';

        $canonicalRequest = "PUT\n" .
            "/" . $this->bucket . "/" . $this->urlEncode($key) . "\n\n" .
            $canonicalHeaders . "\n" .
            $signedHeaders . "\n" .
            $payloadHash;

        $scope = $date . '/' . $this->region . '/s3/aws4_request';
        $stringToSign = "AWS4-HMAC-SHA256\n" . $now . "\n" . $scope . "\n" . hash('sha256', $canonicalRequest);
        $signature = $this->sign($stringToSign, $date);

        $authorization = "AWS4-HMAC-SHA256 Credential=" . $this->access_key . "/" . $scope .
            ", SignedHeaders=" . $signedHeaders .
            ", Signature=" . $signature;

        $url = $this->endpoint . '/' . $this->bucket . '/' . $this->urlEncode($key);
        $headers = array(
            'Authorization: ' . $authorization,
            'x-amz-content-sha256: ' . $payloadHash,
            'x-amz-date: ' . $now,
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

    /**
     * 删除：S3 DELETE Object
     */
    public function delete($remotePath) {
        $key = ltrim($remotePath, '/');
        $now = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');
        $payloadHash = hash('sha256', '');

        $canonicalHeaders = "host:" . $this->hostHeader() . "\n" .
                            "x-amz-content-sha256:" . $payloadHash . "\n" .
                            "x-amz-date:" . $now . "\n";
        $signedHeaders = 'host;x-amz-content-sha256;x-amz-date';

        $canonicalRequest = "DELETE\n" .
            "/" . $this->bucket . "/" . $this->urlEncode($key) . "\n\n" .
            $canonicalHeaders . "\n" .
            $signedHeaders . "\n" .
            $payloadHash;

        $scope = $date . '/' . $this->region . '/s3/aws4_request';
        $stringToSign = "AWS4-HMAC-SHA256\n" . $now . "\n" . $scope . "\n" . hash('sha256', $canonicalRequest);
        $signature = $this->sign($stringToSign, $date);

        $authorization = "AWS4-HMAC-SHA256 Credential=" . $this->access_key . "/" . $scope .
            ", SignedHeaders=" . $signedHeaders .
            ", Signature=" . $signature;

        $url = $this->endpoint . '/' . $this->bucket . '/' . $this->urlEncode($key);
        $headers = array(
            'Authorization: ' . $authorization,
            'x-amz-content-sha256: ' . $payloadHash,
            'x-amz-date: ' . $now,
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
     * 生成对象的公共访问 URL（桶已配置公共读，持久不过期）
     * 返回形如 http://host:port/bucket/key 的直链
     */
    public function getUrl($remotePath, $expires = 3600) {
        $key = ltrim($remotePath, '/');
        // 配置了 CDN/自定义域名则返回持久直链（CDN 域名/key）
        if (!empty($this->cdn_domain)) {
            return $this->cdn_domain . '/' . $this->urlEncode($key);
        }
        // 否则返回桶公共直链（需桶已配置公共读）
        return $this->endpoint . '/' . $this->bucket . '/' . $this->urlEncode($key);
    }

    private function hostHeader() {
        $host = parse_url($this->endpoint, PHP_URL_HOST);
        $port = parse_url($this->endpoint, PHP_URL_PORT);
        if ($port) $host .= ':' . $port;
        return $host;
    }

    /**
     * 列出 bucket 下的对象（S3 ListObjects V2），用于目录浏览
     * @param string $prefix    目录前缀，如 '2026/10/'
     * @param string $delimiter 分隔符，'/' 表示按目录分组
     * @return array array('dirs'=>子目录前缀列表, 'files'=>array(array('name','path','size')))
     */
    public function list($prefix = '', $delimiter = '/', $maxKeys = 1000) {
        $prefix = ltrim($prefix, '/');
        $now = gmdate('Ymd\THis\Z');
        $date = gmdate('Ymd');

        $params = array('delimiter' => $delimiter, 'list-type' => '2', 'max-keys' => $maxKeys);
        if ($prefix !== '') $params['prefix'] = $prefix;
        ksort($params);
        $q = array();
        foreach ($params as $k => $v) $q[] = rawurlencode($k) . '=' . rawurlencode($v);
        $canonicalQuery = implode('&', $q);

        // GET 请求体为空，用空串的 sha256 作为 HashedPayload（S3 标准 Header 签名方式）
        $payloadHash = hash('sha256', '');
        $canonicalRequest = "GET\n/" . $this->bucket . "\n" . $canonicalQuery . "\n" .
            "host:" . $this->hostHeader() . "\n\nhost\n" . $payloadHash;

        $scope = $date . '/' . $this->region . '/s3/aws4_request';
        $stringToSign = "AWS4-HMAC-SHA256\n" . $now . "\n" . $scope . "\n" . hash('sha256', $canonicalRequest);
        $signature = $this->sign($stringToSign, $date);
        $authorization = "AWS4-HMAC-SHA256 Credential=" . $this->access_key . "/" . $scope .
            ", SignedHeaders=host, Signature=" . $signature;

        $url = $this->endpoint . '/' . $this->bucket . '?' . $canonicalQuery;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $authorization, 'x-amz-date: ' . $now));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $xml = curl_exec($ch);
        curl_close($ch);

        $dirs = array(); $files = array();
        if ($xml && preg_match('/<ListBucketResult.*?<\/ListBucketResult>/s', $xml, $m)) {
            $inner = $m[0];
            if (preg_match_all('/<CommonPrefixes>\s*<Prefix>(.*?)<\/Prefix>\s*<\/CommonPrefixes>/s', $inner, $cp)) {
                foreach ($cp[1] as $p) $dirs[] = html_entity_decode($p);
            }
            if (preg_match_all('/<Contents>\s*<Key>(.*?)<\/Key>.*?<Size>(\d+)<\/Size>/s', $inner, $ct)) {
                foreach ($ct[1] as $i => $key) {
                    $k = html_entity_decode($key);
                    $files[] = array('name' => basename($k), 'path' => $k, 'size' => $ct[2][$i]);
                }
            }
        }
        return array('dirs' => $dirs, 'files' => $files);
    }

    private function sign($stringToSign, $shortDate) {
        $kDate = hash_hmac('sha256', $shortDate, 'AWS4' . $this->secret_key, true);
        $kRegion = hash_hmac('sha256', $this->region, $kDate, true);
        $kService = hash_hmac('sha256', 's3', $kRegion, true);
        $kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);
        return hash_hmac('sha256', $stringToSign, $kSigning);
    }

    private function urlEncode($str) {
        return str_replace('%2F', '/', rawurlencode($str));
    }
}
