<?php
/**
 * 幸运转盘管理页面
 */

require_once(dirname(__FILE__) . "/../../../include/bittorrent.php");
dbconn();

// 检查管理员权限
if (!$CURUSER || $CURUSER['class'] < UC_SYSOP) {
    stderr("错误", "权限不足");
}

// 获取插件实例
$plugin = \Nexus\Plugin\Plugin::getById('fortune-wheel');
if (!$plugin) {
    stderr("错误", "插件未找到");
}

$action = $_GET['action'] ?? 'list';

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $data = [
            'name' => $_POST['name'] ?? '',
            'type' => $_POST['type'] ?? '',
            'value' => (float)($_POST['value'] ?? 0),
            'probability' => (float)($_POST['probability'] ?? 0),
            'color' => $_POST['color'] ?? '#FF6B35',
            'enabled' => isset($_POST['enabled']) ? 1 : 0,
            'sort_order' => (int)($_POST['sort_order'] ?? 0),
            'description' => $_POST['description'] ?? '',
        ];

        if ($action === 'add') {
            $plugin->createPrize($data);
            $message = "奖品添加成功";
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $plugin->updatePrize($id, $data);
            $message = "奖品更新成功";
        }
        
        header("Location: admin.php?message=" . urlencode($message));
        exit;
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $plugin->deletePrize($id);
        header("Location: admin.php?message=" . urlencode("奖品删除成功"));
        exit;
    }
}

// 页面标题
$page_title = "幸运转盘管理";
stdhead($page_title);

// 显示消息
if (isset($_GET['message'])) {
    echo '<div style="background: #d4edda; color: #155724; padding: 10px; margin: 10px 0; border: 1px solid #c3e6cb; border-radius: 4px;">' . htmlspecialchars($_GET['message']) . '</div>';
}

if ($action === 'list') {
    // 奖品列表
    $prizes = $plugin->getAllPrizes();
    
    echo '<h2>奖品管理</h2>';
    echo '<p><a href="admin.php?action=add" style="background: #007bff; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px;">添加奖品</a></p>';
    
    if (empty($prizes)) {
        echo '<p>暂无奖品，<a href="admin.php?action=add">点击添加</a></p>';
    } else {
        echo '<table class="main" border="1" cellspacing="0" cellpadding="5">';
        echo '<tr class="colhead">';
        echo '<td>ID</td><td>名称</td><td>类型</td><td>数值</td><td>概率</td><td>颜色</td><td>状态</td><td>排序</td><td>操作</td>';
        echo '</tr>';
        
        foreach ($prizes as $prize) {
            echo '<tr>';
            echo '<td>' . $prize['id'] . '</td>';
            echo '<td>' . htmlspecialchars($prize['name']) . '</td>';
            echo '<td>' . htmlspecialchars($prize['type']) . '</td>';
            echo '<td>' . htmlspecialchars($prize['value']) . '</td>';
            echo '<td>' . $prize['probability'] . '%</td>';
            echo '<td style="background-color: ' . htmlspecialchars($prize['color']) . '; color: white;">' . htmlspecialchars($prize['color']) . '</td>';
            echo '<td>' . ($prize['enabled'] ? '启用' : '禁用') . '</td>';
            echo '<td>' . $prize['sort_order'] . '</td>';
            echo '<td>';
            echo '<a href="admin.php?action=edit&id=' . $prize['id'] . '">编辑</a> | ';
            echo '<a href="admin.php?action=delete&id=' . $prize['id'] . '" onclick="return confirm(\'确定删除吗？\')">删除</a>';
            echo '</td>';
            echo '</tr>';
        }
        
        echo '</table>';
    }
    
} elseif ($action === 'add' || $action === 'edit') {
    // 添加/编辑表单
    $prize = null;
    if ($action === 'edit') {
        $id = (int)($_GET['id'] ?? 0);
        $prize = $plugin->getPrizeById($id);
        if (!$prize) {
            stderr("错误", "奖品不存在");
        }
    }
    
    echo '<h2>' . ($action === 'add' ? '添加奖品' : '编辑奖品') . '</h2>';
    echo '<form method="post">';
    if ($action === 'edit') {
        echo '<input type="hidden" name="id" value="' . $prize['id'] . '">';
    }
    
    echo '<table class="main" border="1" cellspacing="0" cellpadding="5">';
    echo '<tr><td>奖品名称:</td><td><input type="text" name="name" value="' . htmlspecialchars($prize['name'] ?? '') . '" required></td></tr>';
    
    echo '<tr><td>奖品类型:</td><td>';
    echo '<select name="type" required>';
    $types = ['bonus' => '魔力值', 'vip' => 'VIP天数', 'upload' => '上传量', 'download' => '下载量', 'medal' => '勋章', 'custom' => '自定义'];
    foreach ($types as $key => $label) {
        $selected = ($prize['type'] ?? '') === $key ? 'selected' : '';
        echo '<option value="' . $key . '" ' . $selected . '>' . $label . '</option>';
    }
    echo '</select>';
    echo '</td></tr>';
    
    echo '<tr><td>数值:</td><td><input type="number" name="value" value="' . ($prize['value'] ?? 0) . '" step="0.01"></td></tr>';
    echo '<tr><td>中奖概率(%):</td><td><input type="number" name="probability" value="' . ($prize['probability'] ?? 0) . '" step="0.01" min="0" max="100" required></td></tr>';
    echo '<tr><td>颜色:</td><td><input type="color" name="color" value="' . htmlspecialchars($prize['color'] ?? '#FF6B35') . '"></td></tr>';
    echo '<tr><td>启用:</td><td><input type="checkbox" name="enabled" ' . (($prize['enabled'] ?? 1) ? 'checked' : '') . '></td></tr>';
    echo '<tr><td>排序:</td><td><input type="number" name="sort_order" value="' . ($prize['sort_order'] ?? 0) . '"></td></tr>';
    echo '<tr><td>描述:</td><td><textarea name="description" rows="3" cols="50">' . htmlspecialchars($prize['description'] ?? '') . '</textarea></td></tr>';
    echo '<tr><td colspan="2"><input type="submit" value="保存"> <a href="admin.php">返回列表</a></td></tr>';
    echo '</table>';
    echo '</form>';
    
} elseif ($action === 'delete') {
    // 删除确认
    $id = (int)($_GET['id'] ?? 0);
    $prize = $plugin->getPrizeById($id);
    if (!$prize) {
        stderr("错误", "奖品不存在");
    }
    
    echo '<h2>删除奖品</h2>';
    echo '<p>确定要删除奖品 "' . htmlspecialchars($prize['name']) . '" 吗？</p>';
    echo '<form method="post">';
    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<input type="submit" value="确定删除" style="background: #dc3545; color: white; padding: 8px 16px; border: none; border-radius: 4px;">';
    echo ' <a href="admin.php">取消</a>';
    echo '</form>';
}

stdfoot();
?>
