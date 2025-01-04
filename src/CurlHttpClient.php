<?php

namespace Faj1\Utils;

class CurlHttpClient
{
    private array $defaultOptions = [
        CURLOPT_RETURNTRANSFER => true, // 返回字符串而非直接输出
        CURLOPT_TIMEOUT => 30,          // 默认超时时间
        CURLOPT_FOLLOWLOCATION => true, // 支持重定向
        CURLOPT_HEADER => false,        // 是否返回响应头
    ];

    private ?string $error = null;

    /**
     * 发起 HTTP 请求
     *
     * @param string $method 请求方法 (GET, POST, PUT, DELETE, etc.)
     * @param string $url 请求 URL
     * @param array $data 请求数据
     * @param array $headers 请求头
     * @param array $customOptions 自定义的 cURL 选项
     *
     * @return mixed 返回 HTTP 响应，失败返回 false
     */
    public function request(string $method, string $url, array $data = [], array $headers = [], array $customOptions = []): mixed
    {
        $method = strtoupper($method);

        // 初始化 cURL
        $ch = curl_init();

        // cURL 基础选项
        $options = $this->defaultOptions;
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CUSTOMREQUEST] = $method;

        // 设置数据 (针对 GET 和非 GET 请求)
        if (!empty($data)) {
            if ($method === 'GET') {
                $options[CURLOPT_URL] = $url . '?' . http_build_query($data);
            } else {
                $options[CURLOPT_POSTFIELDS] = is_array($data) ? http_build_query($data) : $data;
            }
        }

        // 设置请求头
        if (!empty($headers)) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        // 合并用户自定义选项
        $options = $options + $customOptions;

        // 应用选项到 cURL
        curl_setopt_array($ch, $options);

        // 执行请求
        $response = curl_exec($ch);

        // 错误处理
        if (curl_errno($ch)) {
            $this->error = curl_error($ch);
            $response = false;
        }

        // 关闭 cURL
        curl_close($ch);

        return $response;
    }

    /**
     * 发送 GET 请求
     *
     * @param string $url 请求 URL
     * @param array $params 请求参数
     * @param array $headers 自定义请求头
     * @return mixed 响应结果
     */
    public function get(string $url, array $params = [], array $headers = []): mixed
    {
        return $this->request('GET', $url, $params, $headers);
    }

    /**
     * 发送 POST 请求
     *
     * @param string $url 请求 URL
     * @param array|string $data 请求数据
     * @param array $headers 自定义请求头
     * @return mixed 响应结果
     */
    public function post(string $url, array|string $data = [], array $headers = []): mixed
    {
        return $this->request('POST', $url, $data, $headers);
    }

    /**
     * 发送 PUT 请求
     *
     * @param string $url 请求 URL
     * @param array|string $data 请求数据
     * @param array $headers 自定义请求头
     * @return mixed 响应结果
     */
    public function put(string $url, array|string $data = [], array $headers = []): mixed
    {
        return $this->request('PUT', $url, $data, $headers);
    }

    /**
     * 发送 DELETE 请求
     *
     * @param string $url 请求 URL
     * @param array $data 请求数据
     * @param array $headers 自定义请求头
     * @return mixed 响应结果
     */
    public function delete(string $url, array $data = [], array $headers = []): mixed
    {
        return $this->request('DELETE', $url, $data, $headers);
    }

    /**
     * 获取最后一次的错误信息
     *
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }
}
