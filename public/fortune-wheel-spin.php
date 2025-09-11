<?php
/**
 * 幸运转盘抽奖接口
 */

// 引入公共文件
require_once(dirname(__FILE__) . "/../include/bittorrent.php");
dbconn();

// 响应JSON
header('Content-Type: application/json; charset=utf-8');

// 检查用户是否登录
if (!$CURUSER) {
    echo json_encode(['success' => false, 'message' => '请先登录']);
    exit;
}

try {
    // 获取插件实例和仓库
$plugin = \Nexus\Plugin\Plugin::getById('fortune-wheel');
if (!$plugin) {
        throw new \Exception("插件 'fortune-wheel' 未找到或未启用。");
    }
    $repo = new \NexusPlugin\FortuneWheel\FortuneWheelRepository();

    $count = isset($_POST['count']) ? (int)$_POST['count'] : 1;
    if (!in_array($count, [1, 10, 20, 50])) {
        $count = 1;
    }

    // 调用核心抽奖逻辑
    $result = $repo->spin($CURUSER['id'], $count);

    // 直接输出由 spin() 方法返回的、格式正确的JSON
    echo json_encode($result);
    exit;

} catch (\Exception $e) {
    // 捕获所有异常，并以统一的JSON格式返回错误信息
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
