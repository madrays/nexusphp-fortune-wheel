<?php
/**
 * 测试魔力别名功能
 */

// 引入公共文件
require_once(dirname(__FILE__) . "/test.cn/include/bittorrent.php");
dbconn();

// 检查用户是否登录
if (!$CURUSER) {
    echo "请先登录";
    exit;
}

echo "<h1>魔力别名功能测试</h1>";

// 测试获取设置
$bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
$bonusUnit = get_setting('fortune_wheel.bonus_unit') ?: '魔力';

echo "<h2>当前设置</h2>";
echo "<p>魔力值别名: <strong>{$bonusName}</strong></p>";
echo "<p>魔力单位别名: <strong>{$bonusUnit}</strong></p>";

// 测试BonusNameHelper
require_once('src/BonusNameHelper.php');

echo "<h2>BonusNameHelper 测试</h2>";
echo "<p>getBonusName(): <strong>" . \NexusPlugin\FortuneWheel\BonusNameHelper::getBonusName() . "</strong></p>";
echo "<p>getBonusUnit(): <strong>" . \NexusPlugin\FortuneWheel\BonusNameHelper::getBonusUnit() . "</strong></p>";

// 测试文本替换
$testTexts = [
    '当前魔力: 1000',
    '消耗魔力: 500',
    '魔力值不足',
    '需要更多魔力值',
    '您的魔力不足，需要 1000 魔力值',
];

echo "<h2>文本替换测试</h2>";
foreach ($testTexts as $text) {
    $replaced = \NexusPlugin\FortuneWheel\BonusNameHelper::replaceBonusText($text);
    echo "<p>原文: {$text}</p>";
    echo "<p>替换后: <strong>{$replaced}</strong></p>";
    echo "<hr>";
}

// 测试奖品类型名称
echo "<h2>奖品类型名称测试</h2>";
$prizeTypes = ['bonus', 'upload', 'vip_days', 'medal', 'nothing'];
foreach ($prizeTypes as $type) {
    $typeName = \NexusPlugin\FortuneWheel\BonusNameHelper::getPrizeTypeName($type);
    echo "<p>{$type}: <strong>{$typeName}</strong></p>";
}

// 测试不足消息
echo "<h2>不足消息测试</h2>";
$message = \NexusPlugin\FortuneWheel\BonusNameHelper::getInsufficientBonusMessage(1000, 500);
echo "<p><strong>{$message}</strong></p>";

echo "<h2>测试完成</h2>";
echo "<p><a href='fortune-wheel.php'>返回转盘页面</a></p>";
?>
