<?php
/**
 * 魔力别名演示页面
 */

// 引入公共文件
require_once(dirname(__FILE__) . "/../test.cn/include/bittorrent.php");
dbconn();

// 检查用户是否登录
if (!$CURUSER) {
    stdmsg("错误", "请先登录");
    stdfoot();
    exit;
}

stdhead("魔力别名演示");

// 获取魔力别名设置
$bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
$bonusUnit = get_setting('fortune_wheel.bonus_unit') ?: '魔力';

echo "<h1>魔力别名功能演示</h1>";

echo "<div style='background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px; padding: 20px; margin: 20px 0;'>";
echo "<h2>当前设置</h2>";
echo "<p><strong>魔力值别名:</strong> {$bonusName}</p>";
echo "<p><strong>魔力单位别名:</strong> {$bonusUnit}</p>";
echo "</div>";

echo "<div style='background: #f0f4ff; border: 1px solid #4a67de; border-radius: 8px; padding: 20px; margin: 20px 0;'>";
echo "<h2>效果展示</h2>";
echo "<p>原文: 当前魔力: " . number_format($CURUSER['seedbonus']) . "</p>";
echo "<p>现在显示: 当前{$bonusUnit}: " . number_format($CURUSER['seedbonus']) . "</p>";
echo "<hr>";
echo "<p>原文: 每次抽奖消耗 1000 魔力值</p>";
echo "<p>现在显示: 每次抽奖消耗 1000 {$bonusName}</p>";
echo "<hr>";
echo "<p>原文: 魔力值不足</p>";
echo "<p>现在显示: {$bonusName}不足</p>";
echo "</div>";

echo "<div style='background: #f0fff4; border: 1px solid #22c55e; border-radius: 8px; padding: 20px; margin: 20px 0;'>";
echo "<h2>使用说明</h2>";
echo "<ul>";
echo "<li>管理员可以在后台设置中自定义魔力值和魔力的别名</li>";
echo "<li>设置后，所有相关页面都会自动使用新的别名</li>";
echo "<li>包括转盘页面、个人记录、首页展示等</li>";
echo "<li>支持任何自定义名称，如：憨豆、积分、金币等</li>";
echo "</ul>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='fortune-wheel.php' style='background: #4a67de; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>前往转盘页面</a>";
echo "</div>";

stdfoot();
?>
