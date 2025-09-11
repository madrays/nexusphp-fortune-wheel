<?php
/**
 * 幸运转盘API接口
 */

require_once(dirname(__FILE__) . "/../../../include/bittorrent.php");
dbconn();

// 检查管理员权限
if (!$CURUSER || $CURUSER['class'] < UC_SYSOP) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => '权限不足']);
    exit;
}

// 获取插件实例
$plugin = \Nexus\Plugin\Plugin::getById('fortune-wheel');
if (!$plugin) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => '插件未找到']);
    exit;
}

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'get_prize':
        $prizeId = (int)($_GET['id'] ?? 0);
        if ($prizeId <= 0) {
            echo json_encode(['success' => false, 'message' => '无效的奖品ID']);
            exit;
        }
        
        $prize = $plugin->getPrizeById($prizeId);
        if ($prize) {
            echo json_encode(['success' => true, 'prize' => $prize]);
        } else {
            echo json_encode(['success' => false, 'message' => '奖品不存在']);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => '无效的操作']);
        break;
}
?>
