<?php
require_once(dirname(__FILE__) . '/../include/bittorrent.php');
dbconn();

stdhead("幸运抽奖");

if (!$CURUSER) {
    stdmsg("错误", "请先登录后访问。");
    stdfoot();
    exit;
}

$view_path = dirname(__FILE__) . '/../packages/fortune-wheel-plugin/resources/views/wheel.php';

if (!file_exists($view_path)) {
    stdmsg("错误", "视图文件丢失 (code: FWP-01)，请联系管理员。");
    stdfoot();
    exit;
}

try {
    $repo = new \NexusPlugin\FortuneWheel\FortuneWheelRepository();
    $settings = $repo->getSettings();
    $prizes = $repo->getEnabledPrizes();
    $userStats = $repo->getUserStats($CURUSER['id']);

    if (!($settings['enabled'] ?? false)) {
        stdmsg("提示", "幸运抽奖活动暂未开放。");
        stdfoot();
        exit;
    }
    
    $costPerSpin = $settings['bonus_cost_per_spin'] ?? 1000;
    $freeSpinsLeft = max(0, ($settings['daily_free_spins'] ?? 0) - $userStats['today_count']);
    
    $grades = $repo->getPrizeGrades($prizes);

    $userRecords = $repo->getUserDrawRecords($CURUSER['id'], 100);

} catch (\Exception $e) {
    stdmsg("错误", "加载抽奖数据时出错 (code: FWP-02): " . $e->getMessage());
    stdfoot();
    exit;
}

require_once($view_path);

stdfoot();
?>
