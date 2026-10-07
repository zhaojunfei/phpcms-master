<?php
/**
 * 存储驱动接口
 * 所有存储驱动（磁盘/MinIO/阿里云OSS/腾讯云COS/七牛）必须实现本接口
 */
interface storage_interface {

    /**
     * 上传本地文件到存储
     * @param string $localFile  本地文件绝对路径（临时上传文件）
     * @param string $remotePath 存储中的相对路径（如 2026/10/07/20261007xxxx.jpg）
     * @return bool 成功返回 true，失败返回 false
     */
    public function put($localFile, $remotePath);

    /**
     * 从存储删除文件
     * @param string $remotePath 存储中的相对路径
     * @return bool
     */
    public function delete($remotePath);

    /**
     * 生成文件的访问 URL
     * @param string $remotePath 存储中的相对路径
     * @param int    $expires    有效期（秒）；磁盘驱动忽略此参数，远程驱动用于生成带时效签名 URL
     * @return string
     */
    public function getUrl($remotePath, $expires = 3600);
}
