<?php

namespace Faj1\Utils\Redis;
use Redis;
use Workerman\Connection\TcpConnection;

class ClientRedisManager
{
    private Redis $redis;
    private int $workerId;

    public function __construct(int $workerId)
    {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->workerId = $workerId;
    }

    // ------------------------------ 核心保存逻辑 ------------------------------

    /**
     * 保存客户端信息（自动关联worker和类型）
     * @param TcpConnection $con
     * @param string $clientType
     */
    public function saveClient(TcpConnection $con, string $clientType): void
    {
        $uid = $con->uid;

        // 保存基本客户端信息到哈希（包含归属worker和类型）
        $this->redis->hMSet("clients:info:$uid", [
            'ip' => $con->getRemoteIp(),
            'port' => $con->getRemotePort(),
            'type' => $clientType,
            'worker_id' => $this->workerId,
            'connect_time' => time(),
            'uid' => $uid,
            'status' => 'online'
        ]);

        // 多维度索引存储（便于快速查询）
        $this->redis->sAdd("clients:type:$clientType", $uid);      // 按类型存储
        $this->redis->sAdd("clients:worker:$this->workerId", $uid);// 按worker存储
        $this->redis->sAdd("clients:types", $clientType);          // 维护所有类型
        $this->redis->sAdd("clients:workers", $this->workerId);    // 维护所有worker
    }

    // ------------------------------ 清理相关方法 ------------------------------

    /**
     * 清理当前worker下的所有客户端（Worker退出时调用）
     */
    public function cleanCurrentWorkerClients(): void
    {
        $uidSetKey = "clients:worker:$this->workerId";
        $clientUids = $this->redis->sMembers($uidSetKey);
        if (empty($clientUids)) {
            return;
        }

        // 获取数据后立刻删除集合，防止后续保存时重复添加
        $this->redis->del($uidSetKey);  // 提前解除与worker的关联

        // 后续清理步骤保持不变
        $pipe = $this->redis->pipeline();
        foreach ($clientUids as $uid) {
            $clientType = $this->redis->hGet("clients:info:$uid", 'type');
            // 如果该客户端已被其他worker接管则不会删除类型索引
            $actualWorkerId = $this->redis->hGet("clients:info:$uid", 'worker_id');
            if ($clientType && $actualWorkerId == $this->workerId) {
                $pipe->sRem("clients:type:$clientType", $uid);
            }
            $pipe->del("clients:info:$uid");
        }
        $pipe->exec();
    }


    /**
     * 删除单个客户端（连接关闭时调用）
     * @param string $uid
     */
    public function deleteClient(string $uid): void
    {
        // 获取类型和worker信息
        $clientType = $this->redis->hGet("clients:info:$uid", 'type');
        $workerId = $this->redis->hGet("clients:info:$uid", 'worker_id');

        // 管道批量操作
        $pipe = $this->redis->pipeline();
        $pipe->sRem("clients:type:$clientType", $uid);
        $pipe->sRem("clients:worker:$workerId", $uid);
        $pipe->del("clients:info:$uid");
        $pipe->exec();
    }

    // ------------------------------ 查询相关方法 ------------------------------

    /**
     * 获取某类型客户端总数
     * @param string $clientType
     * @return int
     */
    public function getClientCountByType(string $clientType): int
    {
        return $this->redis->sCard("clients:type:$clientType");
    }

    /**
     * 获取当前worker的在线客户端数
     * @return int
     */
    public function getCurrentWorkerClientCount(): int
    {
        return $this->redis->sCard("clients:worker:$this->workerId");
    }

    /**
     * 获取客户端的完整信息
     * @param string $uid
     * @return array|null
     */
    public function getClientDetail(string $uid): ?array
    {
        $data = $this->redis->hGetAll("clients:info:$uid");
        return empty($data) ? null : $data;
    }

    /**
     * 获取某类型下的所有客户端UID
     * @param string $clientType
     * @return array
     */
    public function getUidsByType(string $clientType): array
    {
        return $this->redis->sMembers("clients:type:$clientType");
    }

    // ------------------------------ 辅助方法 ------------------------------

    /**
     * 列出所有已注册的客户端类型
     * @return array
     */
    public function getAllClientTypes(): array
    {
        return $this->redis->sMembers("clients:types");
    }

    /**
     * 检查客户端是否存在
     * @param string $uid
     * @return bool
     */
    public function isClientExists(string $uid): bool
    {
        return $this->redis->exists("clients:info:$uid");
    }
}
