<?php
// This file is now a pure view and expects variables to be passed from the controller.
// $prizes, $userStats, $settings, $CURUSER, $costPerSpin, $freeSpinsLeft, $grades, $userRecords

// 获取魔力别名
$bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
$bonusUnit = get_setting('fortune_wheel.bonus_unit') ?: '魔力';
?>
<style>
    :root {
        --primary-color: #008cff;
        --secondary-color: #005bb5;
        --text-color: #333;
        --card-bg: #ffffff;
        --shadow-color: rgba(0, 0, 0, 0.08);
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --font-family: 'Segoe UI', 'Microsoft YaHei', sans-serif;
    }

    .fortune-wheel-wrapper {
        font-family: var(--font-family);
        background: transparent;
        color: var(--text-color);
        margin: 20px auto;
        padding: 0;
        width: 100%;
        max-width: 1200px;
}

    .main-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 20px;
        align-items: flex-start;
    }

    .left-panel {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .right-panel {
        position: sticky;
        top: 20px;
    }
    
    .stats-container, .prize-list-container, .actions-container, .rules-container {
        background: var(--card-bg);
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 8px 20px var(--shadow-color);
        border: 1px solid rgba(0,0,0,0.06);
    }

    .history-container {
        background: var(--card-bg);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 12px var(--shadow-color);
        margin-top: 20px;
}

.user-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 16px;
}

    .stat-card {
        text-align: center;
        background: linear-gradient(180deg, #fff, #f7fbff);
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 12px;
        padding: 14px 10px;
    }
    .stat-card .value {
        font-size: 1.8em;
        font-weight: 800;
        background: linear-gradient(90deg, #008cff, #00c6ff);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-fill-color: transparent;
    }
    .stat-card .label {
        font-size: 0.85em;
        color: #748092;
        margin-top: 4px;
        letter-spacing: .2px;
    }

    .featured-prize {
        background: #fff;
        border-radius: 12px;
        padding: 24px 20px;
        box-shadow: 0 8px 18px var(--shadow-color);
        text-align: center;
        min-height: 200px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(0,0,0,0.06);
        position: relative;
    }
    /* ===== 可爱白底粉色小电视，仅限卡片内部使用 ===== */
    .featured-prize .tv { position: relative; width: 100%; max-width: 420px; margin: 0 auto; }
    .featured-prize .tv .ear { position: relative; height: 36px; }
    .featured-prize .tv .ear .ear-left, .featured-prize .tv .ear .ear-right {
        position: absolute; top: -2px; width: 18px; height: 44px; background: #ff7eb3; border-radius: 8px;
        animation: tvEar 2.6s ease-in-out infinite;
    }
    .featured-prize .tv .ear .ear-left { left: 22%; transform: rotate(-18deg); }
    .featured-prize .tv .ear .ear-right { right: 22%; transform: rotate(18deg); }
    @keyframes tvEar { 0%,100% { transform: translateY(0) rotate(var(--r,0)); } 50% { transform: translateY(-3px) rotate(var(--r,0)); } }
    .featured-prize .tv .body { position: relative; background: #fff; border: 6px solid #ff7eb3; border-radius: 14px; padding: 10px; }
    /* 炫酷展示卡（居中、无溢出）：渐变环 + 内发光 */
    .featured-prize .neo-card {
        position: relative; width: 100%; max-width: 520px; margin: 0 auto;
        border-radius: 16px; padding: 24px 24px; min-height: 160px;
        background: #fff; border: 1px solid rgba(0,0,0,0.06);
        box-shadow: 0 8px 18px rgba(0,0,0,0.06);
        overflow: hidden; display: flex; flex-direction: column;
        align-items: center; justify-content: center; gap: 8px;
        box-sizing: border-box; text-align: center;
    }
    /* 居中不偏移的光晕（不旋转，不平移） */
    .featured-prize .neo-halo { position: absolute; inset: 0; transform: none; pointer-events: none; border-radius: 16px; background: radial-gradient(ellipse at center, rgba(0,140,255,.10), transparent 65%); }
    .featured-prize .neo-glow { position: absolute; inset: 8px; border-radius: 12px; pointer-events: none; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.04), 0 10px 26px rgba(0,0,0,0.06); }
    .featured-prize .neo-emoji { position: relative; z-index: 1; font-size: 34px; line-height: 1; animation: iconFloat 2.2s ease-in-out infinite; }
    .featured-prize .neo-title { position: relative; z-index: 1; margin: 0; font-weight: 800; font-size: 22px; line-height: 1.2; background-size: 200% 100%; animation: fpShine 3s ease-in-out infinite; }
    .featured-prize .neo-desc { position: relative; z-index: 1; margin: 0; font-size: 13px; color: #666; }
    .featured-prize .tv .face { position: absolute; inset: 0; pointer-events: none; }
    .featured-prize .tv .eye-left, .featured-prize .tv .eye-right { position: absolute; top: 34%; width: 24%; height: 8px; background: #ff7eb3; border-radius: 6px; animation: eyeBlink 3.8s ease-in-out infinite; }
    .featured-prize .tv .eye-left { left: 14%; transform: rotate(-5deg); }
    .featured-prize .tv .eye-right { right: 14%; transform: rotate(5deg); }
    @keyframes eyeBlink { 0%,98%,100% { height: 8px; } 96% { height: 2px; } }
    .featured-prize .tv .mouth-left, .featured-prize .tv .mouth-right { position: absolute; bottom: 28%; width: 22px; height: 22px; border: 5px solid #ff7eb3; border-top: none; background: transparent; }
    .featured-prize .tv .mouth-left { left: 40%; border-radius: 0 0 60% 40%/0 0 100% 100%; }
    .featured-prize .tv .mouth-right { right: 40%; border-radius: 0 0 40% 60%/0 0 100% 100%; }
    .featured-prize .tv .feet { position: relative; height: 18px; margin-top: 8px; }
    .featured-prize .tv .foot-left, .featured-prize .tv .foot-right { position: absolute; bottom: 0; width: 42px; height: 16px; border-radius: 0 0 16px 16px; background: #ff7eb3; }
    .featured-prize .tv .foot-left { left: 30%; }
    .featured-prize .tv .foot-right { right: 30%; }
    .featured-prize .tv .screen .icon { font-size: 30px; display: inline-block; animation: iconFloat 2.2s ease-in-out infinite; transform-origin: 50% 60%; }
    @keyframes iconFloat { 0% { transform: translateY(0); } 50% { transform: translateY(-4px); } 100% { transform: translateY(0); } }
    .featured-prize .tv .screen .title { margin-top: 6px; font-weight: 700; font-size: 18px; line-height: 1.2; }
    .featured-prize .tv .screen .desc { margin-top: 6px; font-size: 12px; color: #666; }
    .featured-prize .tv .scanlines { position: absolute; inset: 0; pointer-events: none; background: repeating-linear-gradient(to bottom, rgba(0,0,0,0.04) 0, rgba(0,0,0,0.04) 1px, transparent 2px, transparent 4px); opacity: .12; animation: scanMove 3s linear infinite; }
    @keyframes scanMove { 0% { background-position: 0 0; } 100% { background-position: 0 8px; } }
    .featured-prize h2,
    .fw-prize-title {
        margin: 0 0 10px 0;
        font-size: 1.5em;
        color: #222;
        font-weight: 600;
        text-shadow: none; /* 避免外部样式影响 */
    }
    .featured-prize p {
        margin: 0;
        font-size: 1em;
        color: #555;
        line-height: 1.5;
}
    .featured-prize .placeholder h2 {
        background: linear-gradient(90deg, #008cff, #00c6ff);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-fill-color: transparent;
        color: transparent;
        text-shadow: none;
    }
     .featured-prize .placeholder p {
        color: #bbb;
    }
    .win-badge {
        display: inline-block; padding: 6px 10px; border-radius: 999px;
        background: linear-gradient(135deg, #00d2ff, #008cff);
        color: #fff; font-weight: 600; font-size: 12px; letter-spacing: .3px;
        box-shadow: 0 8px 18px rgba(0,140,255,.35);
        margin-bottom: 10px;
    }

    .rules-container ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
        font-size: 0.9em;
        line-height: 1.8;
        color: #555;
}

    .rules-container ul li strong {
        color: var(--primary-color);
        font-weight: 600;
    }

    .featured-prize-icon {
        font-size: 3em;
        margin-bottom: 15px;
        line-height: 1;
    }

    /* 渐变文字通用类（用于实际奖品名） */
    .fw-gradient-text {
        background: linear-gradient(90deg, #008cff, #00c6ff);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        text-fill-color: transparent;
        color: transparent;
        text-shadow: none;
    }
    /* 按奖品等级的 15 套渐变方案 */
    .fw-grad-g1  { background: linear-gradient(90deg, #ffb200, #ff6b00); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g2  { background: linear-gradient(90deg, #8a2be2, #00c6ff); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g3  { background: linear-gradient(90deg, #00d2ff, #00b894); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g4  { background: linear-gradient(90deg, #ff7eb3, #ff758c); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g5  { background: linear-gradient(90deg, #7f7fd5, #86a8e7); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g6  { background: linear-gradient(90deg, #00c9ff, #92fe9d); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g7  { background: linear-gradient(90deg, #f83600, #f9d423); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g8  { background: linear-gradient(90deg, #a18cd1, #fbc2eb); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g9  { background: linear-gradient(90deg, #6a11cb, #2575fc); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g10 { background: linear-gradient(90deg, #11998e, #38ef7d); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g11 { background: linear-gradient(90deg, #f7971e, #ffd200); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g12 { background: linear-gradient(90deg, #fc5c7d, #6a82fb); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g13 { background: linear-gradient(90deg, #00cdac, #02aab0); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g14 { background: linear-gradient(90deg, #f43b47, #453a94); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-g15 { background: linear-gradient(90deg, #ed6ea0, #ec8c69); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }
    .fw-grad-others { background: linear-gradient(90deg, #008cff, #00c6ff); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; color: transparent; }

    /* 右侧卡片特效：柔和光晕 + 扫光（轻量，不刺眼） */
    .featured-prize::before {
        content: ""; position: absolute; inset: -2px; border-radius: 12px;
        background: radial-gradient(520px 100px at 50% -10%, rgba(0,140,255,.12), transparent 60%);
        pointer-events: none;
    }
    .featured-prize .shine { position: absolute; top: 0; left: -160%; width: 120%; height: 100%;
        background: linear-gradient(65deg, transparent 0%, rgba(255,255,255,.22) 18%, rgba(255,255,255,.10) 36%, transparent 60%);
        transform: skewX(-18deg); pointer-events: none; display: none; }
    .featured-prize.pulsing .shine { display: block; animation: shineMove 2.2s ease-in-out infinite; }
    @keyframes shineMove { 0% { left: -160%; } 55% { left: 160%; } 100% { left: 160%; } }


    .prizes-list {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 400px;
        overflow-y: scroll;
    position: relative;
    }
    .prizes-list::-webkit-scrollbar {
        display: none; /* Chrome, Safari, Opera*/
    }
    .prizes-list {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }
    .prize-item {
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 8px;
        cursor: pointer;
        transition: all 0.1s ease;
    display: flex;
        justify-content: space-between;
    align-items: center;
        background: #f8f9fa;
    }
    .prize-item:hover, .prize-item.active {
        background: var(--primary-color);
        color: white;
        transform: translateX(5px);
        box-shadow: 0 6px 16px rgba(0, 140, 255, 0.35), inset 0 0 0 1px rgba(255,255,255,0.15);
    }
    .prize-item .prize-name { font-weight: 600; }
    .prize-item .grade {
        font-weight: 600;
        padding: 2px 6px;
        border-radius: 4px;
        background: var(--warning-color);
    color: white;
        font-size: 0.8em;
    }
    .prize-item:hover .grade, .prize-item.active .grade {
        background: white;
        color: var(--primary-color);
}

    /* 更快的脉冲动画 */
    .featured-prize.pulsing {
        animation: pulse 0.6s ease-in-out infinite;
    }
    @keyframes pulse {
        0% { box-shadow: 0 0 0 rgba(0, 140, 255, 0.0); transform: translateZ(0); }
        50% { box-shadow: 0 8px 20px rgba(0, 140, 255, 0.3); transform: translateZ(0); }
        100% { box-shadow: 0 0 0 rgba(0, 140, 255, 0.0); transform: translateZ(0); }
    }

    .actions { display: flex; align-items: center; justify-content: center; gap: 14px; flex-wrap: wrap; }
    .actions-primary { margin-bottom: 10px; }
    .actions-multi { margin-top: 4px; }
    /* 主按钮：抽1次，突出显示 */
    .spin-btn--primary {
        position: relative;
        background: linear-gradient(90deg, #0ea5ff, #44d1ff);
        color: #fff; border: 0;
        padding: 12px 28px; border-radius: 14px;
        font-size: 1.08em; font-weight: 800; letter-spacing: .2px;
        cursor: pointer;
        transition: transform .1s ease, box-shadow .1s ease, background .15s ease;
        box-shadow: 0 12px 28px rgba(14,165,255,.35), inset 0 -2px 0 rgba(255,255,255,.25);
    }
    .spin-btn--primary:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 16px 32px rgba(14,165,255,.38); background: linear-gradient(90deg, #0794ef, #32c9ff); }
    .spin-btn--primary:active:not(:disabled) { transform: translateY(0); box-shadow: 0 10px 22px rgba(14,165,255,.30); }

    /* 连抽按钮组：轻量胶囊分段，区分但不喧宾 */
    .spin-btn--seg { background: #fff; border: 1px solid rgba(0,0,0,0.08); color: #0f172a; padding: 9px 18px; font-size: .95em; font-weight: 700; border-radius: 999px; cursor: pointer; transition: background .1s ease, color .1s ease, box-shadow .1s ease; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
    .spin-btn--seg:hover { background: #f4f8ff; box-shadow: 0 4px 12px rgba(0,0,0,.08); }
    .spin-btn--seg:active { background: #eef4ff; box-shadow: 0 2px 8px rgba(0,0,0,.05); }
    .spin-btn--seg[data-count="10"] { color: #5367d8; }
    .spin-btn--seg[data-count="20"] { color: #11998e; }
    .spin-btn--seg[data-count="50"] { color: #ff6b9a; }
    .spin-btn--seg:disabled { color: #a0a7b4; }

    /* 通用禁用态（防连轴）：与当前逻辑一致，抽奖期间禁用所有按钮 */
    .spin-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(0,140,255,.34);
    }
    .spin-btn:active:not(:disabled) {
        transform: translateY(0);
        box-shadow: 0 6px 18px rgba(0,140,255,.28);
    }
    .spin-btn:disabled {
        background: #cfd6dd;
        cursor: not-allowed;
        box-shadow: none;
    }
    /* 小屏自适应 */
    @media (max-width: 560px) {
        .actions { gap: 10px; }
        .actions-primary { width: 100%; }
        .spin-btn--primary { width: 100%; }
        .actions-multi { width: 100%; justify-content: center; }
        .spin-btn--seg { flex: 1 1 auto; text-align: center; min-width: 28%; }
    }

    /* 顶部统计头部装饰 */
    .stats-head { display: flex; align-items: center; gap: 8px; margin-bottom: 12px; }
    .stats-head .dot { width: 8px; height: 8px; border-radius: 50%; background: linear-gradient(90deg, #008cff, #00c6ff); box-shadow: 0 0 0 3px rgba(0,140,255,.12); }
    .stats-head .title { font-weight: 800; color: #1f2937; letter-spacing: .3px; }

    .history-toggle {
        cursor: pointer;
        font-weight: 600;
        color: var(--primary-color);
        padding: 10px;
    text-align: center;
        margin-top: 10px;
        background: #f8f9fa;
        border-radius: 8px;
}
    .records-table {
    width: 100%;
        margin-top: 15px;
    border-collapse: collapse;
        background: #fff; /* 历史记录白底 */
        border-radius: 8px;
        overflow: hidden;
    }
    .records-table th, .records-table td {
        padding: 8px 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
}
    .records-table thead th {
        background: #f7fafc;
        color: #333;
        font-weight: 600;
    }
    .records-table .win { color: var(--success-color); font-weight: bold; }
    .records-table .lose { color: var(--danger-color); }
    .history-content { display: none; margin-top: 15px;}

    /* Modal */
    .modal-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center;
        z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s ease;
    }
    .modal-overlay.visible { opacity: 1; visibility: visible; }
    .modal-content {
        background: var(--card-bg); padding: 25px; border-radius: 12px;
        width: 90%; max-width: 600px; max-height: 80vh; overflow-y: auto;
    }
    .modal-close { float: right; cursor: pointer; font-size: 1.5em; color: #aaa; }
    .results-grid { margin-top: 20px; display: flex; flex-direction: column; gap: 10px; }
    .result-item { background: #f8f9fa; padding: 10px; border-radius: 8px; border-left: 4px solid var(--success-color); }
    .result-item.nothing { border-left-color: #6c757d; }

</style>
<div class="fortune-wheel-wrapper">
    <div class="main-grid">
        <div class="left-panel">
            <div class="stats-container">
        <div class="stats-head"><span class="dot"></span><span class="title">我的抽奖信息</span></div>
        <div class="user-stats">
                    <div class="stat-card">
                        <div class="value" id="bonus-value"><?= number_format($CURUSER['seedbonus']) ?></div>
                        <div class="label">当前<?= $bonusUnit ?></div>
                    </div>
                    <div class="stat-card">
                        <div class="value" id="cost-value"><?= number_format($costPerSpin) ?></div>
                        <div class="label">每次消耗</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" id="today-count"><?= $userStats['today_count'] ?> / <?= $settings['daily_max_spins'] ?></div>
                        <div class="label">今日已抽</div>
                    </div>
                    <div class="stat-card">
                        <div class="value" id="free-count"><?= $freeSpinsLeft ?></div>
                        <div class="label">免费次数</div>
                    </div>
                </div>
            </div>
            <div class="prize-list-container">
                <div class="stats-head" style="margin-bottom:10px;"><span class="dot"></span><span class="title">奖品列表</span></div>
                <ul class="prizes-list" id="prizes-list">
                    <?php foreach ($prizes as $prize): ?>
                        <li class="prize-item" data-prize-id="<?= $prize['id'] ?>" data-prize-name="<?= htmlspecialchars($prize['name']) ?>" data-prize-desc="<?= htmlspecialchars($prize['description']) ?>" data-prize-type="<?= htmlspecialchars($prize['type']) ?>" data-prize-grade="<?= htmlspecialchars($grades[$prize['id']] ?? '参与奖') ?>">
                            <?php 
                                $gradeClass = 'fw-grad-others';
                                $glabel = (string)($grades[$prize['id']] ?? '');
                                if (preg_match('/^(\d+)/u', $glabel, $m)) {
                                    $num = (int)$m[1];
                                    if ($num >= 1) {
                                        if ($num > 15) { $num = 15; }
                                        $gradeClass = 'fw-grad-g' . $num;
                                    }
                                }
                            ?>
                            <span class="prize-name <?= $gradeClass ?>"><?= htmlspecialchars($prize['name']) ?></span>
                            <span class="grade"><?= htmlspecialchars($grades[$prize['id']] ?? '参与奖') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="right-panel">
            <div class="featured-prize" id="featured-prize">
                <div class="neo-card">
                    <div class="neo-halo"></div>
                    <div class="neo-glow"></div>
                    <div class="neo-emoji">✨</div>
                    <div class="neo-title fw-prize-title fw-grad-others">选择奖品</div>
                    <div class="neo-desc">查看详情</div>
                </div>
            </div>
             <div class="rules-container" style="margin-top:20px;">
                <div class="stats-head" style="margin-bottom:10px;"><span class="dot"></span><span class="title">抽奖说明</span></div>
                <ul>
                    <li>每日可抽奖 <strong><?= (int)($settings['daily_max_spins'] ?? 0) ?></strong> 次，前 <strong><?= (int)($settings['daily_free_spins'] ?? 0) ?></strong> 次免费。</li>
                    <li>免费次数用尽后，每次抽奖消耗 <strong><?= number_format($costPerSpin) ?></strong> <?= $bonusName ?>。</li>
                    <li>系统支持单次、10次、20次、50次连续抽奖。</li>
                    <li>将鼠标悬停在左侧奖品列表上可查看奖品详情。</li>
                </ul>
            </div>
            <div class="actions-container" style="margin-top:20px;">
                <div class="stats-head" style="margin-bottom:10px;"><span class="dot"></span><span class="title">抽奖操作</span></div>
            <div class="actions actions-primary">
                <button class="spin-btn--primary" data-count="1">
                    <span class="btn-text">立即抽奖</span>
                    <span class="btn-loading" style="display: none;">加载中...</span>
                </button>
            </div>
            <div class="actions actions-multi">
                <button class="spin-btn--seg" data-count="10">
                    <span class="btn-text">连抽10次</span>
                    <span class="btn-loading" style="display: none;">加载中...</span>
                </button>
                <button class="spin-btn--seg" data-count="20">
                    <span class="btn-text">连抽20次</span>
                    <span class="btn-loading" style="display: none;">加载中...</span>
                </button>
                <button class="spin-btn--seg" data-count="50">
                    <span class="btn-text">连抽50次</span>
                    <span class="btn-loading" style="display: none;">加载中...</span>
                </button>
            </div>
            </div>
        </div>
    </div>

    <div class="history-container">
        <div class="stats-head" style="margin-bottom:10px;"><span class="dot"></span><span class="title">我的抽奖记录</span></div>
        <div class="history-toggle">📋 展开/收起</div>
        <div class="history-content">
        <?php if (empty($userRecords)): ?>
                <p style="text-align: center; padding: 20px;">暂无抽奖记录</p>
        <?php else: ?>
            <table class="records-table">
                <thead><tr><th>时间</th><th>奖品</th><th>结果</th><th>消耗<?= $bonusUnit ?></th></tr></thead>
                <tbody>
                    <?php foreach ($userRecords as $record): ?>
                        <tr>
                            <td><?= date('m-d H:i', strtotime($record['created_at'])) ?></td>
                            <td><?= htmlspecialchars($record['prize_name'] ?? '未知') ?></td>
                        <td class="<?= $record['is_win'] ? 'win' : 'lose' ?>"><?php
                            $emoji = $record['is_win'] ? '🎉' : '💧';
                            $status = $record['result_status'] ?? ($record['is_win'] ? 'awarded' : 'nothing');
                            $value = $record['result_value'] ?? '';
                            $unit = $record['result_unit'] ?? '';
                            // 构造详细发放描述
                            switch ($status) {
                                case 'awarded':
                                    $detail = $value !== '' ? "获得 {$value} {$unit}" : '获得奖励';
                                    break;
                                case 'compensated':
                                    $detail = $value !== '' ? "重复物品，补偿 {$value} {$unit}" : '重复物品，已补偿';
                                    break;
                                case 'extended':
                                    $detail = $value !== '' ? "有效期延长 {$value} {$unit}" : '有效期已延长';
                                    break;
                                case 'already_owned':
                                    $detail = '物品重复，无补偿';
                                    break;
                                case 'compensated_high_class':
                                    $detail = $value !== '' ? "等级更高，补偿 {$value} {$unit}" : '等级更高，已补偿';
                                    break;
                                case 'already_owned_high_class':
                                    $detail = '您已是更高贵的身份';
                                    break;
                                case 'nothing':
                                default:
                                    $detail = '谢谢参与';
                                    break;
                            }
                            echo $emoji . ' ' . htmlspecialchars($detail);
                        ?></td>
                            <td><?= number_format($record['cost_bonus']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

    <div class="modal-overlay" id="result-modal">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h2 id="modal-title" class="fw-gradient-text" style="margin:0 0 10px 0;">抽奖结果</h2>
            <div id="modal-body" class="results-grid"></div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const prizesList = document.getElementById('prizes-list');
    const featuredPrize = document.getElementById('featured-prize');
    const prizeItems = document.querySelectorAll('.prize-item');
    const historyToggle = document.querySelector('.history-toggle');
    const historyContent = document.querySelector('.history-content');
    // 兼容不同按钮样式类名：主按钮与连抽按钮
    const spinButtons = document.querySelectorAll('.spin-btn, .spin-btn--primary, .spin-btn--seg');
    const resultModal = document.getElementById('result-modal');
    const modalClose = resultModal.querySelector('.modal-close');
    let isSpinning = false;
    let lastResults = [];
    let lastWinning = null; // 用于右侧卡片同步
    let pageFullyLoaded = false; // 页面完全加载标志
    let lastClickTime = 0; // 防止快速重复点击
    let resultsFullyDisplayed = true; // 抽奖结果是否完全显示
    let currentAnimation = null; // 当前动画的取消函数

    // 页面完全加载检查
    function checkPageFullyLoaded() {
        // 检查所有关键元素是否已加载
        const criticalElements = [
            prizesList,
            featuredPrize,
            spinButtons.length > 0,
            prizeItems.length > 0
        ];
        
        const allLoaded = criticalElements.every(element => element !== null && element !== false);
        
        // 额外检查：确保DOM完全稳定
        const isDOMStable = document.readyState === 'complete' && 
                           document.querySelectorAll('script').length > 0 &&
                           window.jQuery !== undefined; // 如果使用了jQuery
        
        if (allLoaded && isDOMStable) {
            pageFullyLoaded = true;
        }
    }

    // 更新按钮状态
    function updateButtonStates() {
        spinButtons.forEach(button => {
            const btnText = button.querySelector('.btn-text');
            const btnLoading = button.querySelector('.btn-loading');
            
            if (!pageFullyLoaded || isSpinning || !resultsFullyDisplayed) {
                button.disabled = true;
                button.style.opacity = '0.6';
                button.style.pointerEvents = 'none'; // 完全禁用鼠标事件
                if (!pageFullyLoaded) {
                    button.title = '页面加载中，请稍候...';
                    if (btnText && btnLoading) {
                        btnText.style.display = 'none';
                        btnLoading.style.display = 'inline';
                    }
                } else if (isSpinning) {
                    button.title = '抽奖进行中...';
                    if (btnText && btnLoading) {
                        btnText.style.display = 'none';
                        btnLoading.style.display = 'inline';
                    }
                } else if (!resultsFullyDisplayed) {
                    button.title = '结果加载中，请稍候...';
                    if (btnText && btnLoading) {
                        btnText.style.display = 'none';
                        btnLoading.style.display = 'inline';
                    }
                }
            } else {
                button.disabled = false;
                button.style.opacity = '1';
                button.style.pointerEvents = 'auto'; // 恢复鼠标事件
                button.title = '';
                if (btnText && btnLoading) {
                    btnText.style.display = 'inline';
                    btnLoading.style.display = 'none';
                }
            }
        });
    }

    // 初始检查
    checkPageFullyLoaded();
    updateButtonStates();
    
    // 如果还没完全加载，继续检查
    if (!pageFullyLoaded) {
        const checkInterval = setInterval(() => {
            checkPageFullyLoaded();
            updateButtonStates();
            if (pageFullyLoaded) {
                clearInterval(checkInterval);
            }
        }, 100);
        
        // 5秒后强制启用（防止无限等待）
        setTimeout(() => {
            if (!pageFullyLoaded) {
                pageFullyLoaded = true;
                clearInterval(checkInterval);
                updateButtonStates();
            }
        }, 5000);
    }

    // Hover effect for prize list
    prizeItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            if (isSpinning) return;
            updateFeaturedPrize(item);
            prizeItems.forEach(i => i.classList.remove('active'));
            item.classList.add('active');
        });
    });

    function updateFeaturedPrize(item) {
        const name = item.dataset.prizeName;
        const desc = item.dataset.prizeDesc || '祝您好运';
        const type = item.dataset.prizeType;
        const icon = getPrizeIcon(type);
        const gradeEl = Array.from(prizeItems).find(i => (i.dataset.prizeName || '') === name)?.querySelector('.grade');
        const gradeText = (gradeEl ? gradeEl.textContent : '').trim();
        const titleClass = getGradeClassByText(gradeText);
        featuredPrize.innerHTML = `
            <div class="neo-card">
                <div class="neo-halo"></div>
                <div class="neo-glow"></div>
                <div class="neo-emoji">${icon}</div>
                <div class="neo-title fw-prize-title ${titleClass}">${name}</div>
                <div class="neo-desc">${desc}</div>
            </div>
        `;
    }

    function getPrizeIcon(type) {
        switch (type) {
            case 'bonus': return '💰';
            case 'upload': return '🚀';
            case 'vip_days': return '👑';
            case 'medal': return '🏅';
            case 'invite_temp': return '✉️';
            case 'invite_perm': return '🧧';
            case 'rainbow_id_days': return '🌈';
            case 'rename_card': return '🏷️';
            case 'attendance_card': return '🗓️';
            case 'nothing': return '🤔';
            default: return '💎';
        }
    }

    // History toggle
    historyToggle.addEventListener('click', () => {
        const isHidden = historyContent.style.display === 'none' || historyContent.style.display === '';
        historyContent.style.display = isHidden ? 'block' : 'none';
        historyToggle.textContent = isHidden ? '收起记录' : '📋 我的抽奖记录';
    });

    // 自动轮转：鼠标未悬停且未抽奖时，自动轮播展示卡片内容
    let autoRotateTimer = null;
    let hoverOnPrizeList = false;
    prizesList.addEventListener('mouseenter', () => { hoverOnPrizeList = true; stopAutoRotate(); });
    prizesList.addEventListener('mouseleave', () => { hoverOnPrizeList = false; startAutoRotate(); });
    function startAutoRotate() {
        if (autoRotateTimer) return;
        autoRotateTimer = setInterval(() => {
            if (isSpinning || hoverOnPrizeList) return;
            const items = Array.from(prizeItems);
            if (!items.length) return;
            const currentIndex = Math.max(0, items.findIndex(i => i.classList.contains('active')));
            const nextIndex = (currentIndex + 1) % items.length;
            const nextItem = items[nextIndex];
            items.forEach(i => i.classList.remove('active'));
            nextItem.classList.add('active');
            prizesList.scrollTop = nextItem.offsetTop - prizesList.clientHeight / 2 + nextItem.clientHeight / 2;
            updateFeaturedPrize(nextItem);
        }, 2000); // 从2.5秒减少到2秒
    }
    function stopAutoRotate() { if (autoRotateTimer) { clearInterval(autoRotateTimer); autoRotateTimer = null; } }
    startAutoRotate();

    // Spin logic (先获取结果，再做定向动画，确保动画与中奖一致；连抽仅一次动画)
    spinButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault(); // 防止默认行为
            e.stopPropagation(); // 阻止事件冒泡
            
            const currentTime = Date.now();
            
            // 防重复点击：2秒内只允许一次点击
            if (currentTime - lastClickTime < 2000) {
                return false;
            }
            lastClickTime = currentTime;
            
            // 检查页面是否完全加载
            if (!pageFullyLoaded) {
                return false;
            }
            
            // 检查是否正在抽奖
            if (isSpinning) {
                return false;
            }
            
            // 检查结果是否完全显示
            if (!resultsFullyDisplayed) {
                return false;
            }
            
            // 检查按钮是否被禁用
            if (button.disabled) {
                return false;
            }
            isSpinning = true;
            resultsFullyDisplayed = false; // 开始抽奖时标记结果未完全显示
            toggleButtons(false);
            const count = button.dataset.count;
            featuredPrize.classList.add('pulsing');
            performSpin(count);
            
            return false;
        });
    });

    // 定向动画：滚动到目标奖品，确保动画一定会执行
    function animateToTargetPrize(targetPrizeId, onDone) {
        const items = Array.from(prizeItems);
        const getIndexById = (id) => items.findIndex(i => parseInt(i.dataset.prizeId) === parseInt(id));
        let targetIndex = getIndexById(targetPrizeId);
        if (targetIndex < 0) {
            // 兜底：目标不在列表时，选第一个
            targetIndex = 0;
        }

        // 当前高亮项索引
        let currentIndex = items.findIndex(i => i.classList.contains('active'));
        if (currentIndex < 0) currentIndex = 0;

        // 增加动画时长和步数，确保动画明显可见
        const totalDuration = 2500; // 2.5秒，更长的动画时间
        const totalSteps = Math.max(20, Math.min(30, items.length * 2)); // 更多步数：20-30步
        const stepDuration = totalDuration / totalSteps;

        let step = 0;
        let animationId = null; // 用于取消动画

        function stepOnce() {
            // 检查动画是否被取消
            if (animationId === null) {
                return;
            }

            // 线性插值到目标位置
            const progress = step / totalSteps;
            const easeProgress = 1 - Math.pow(1 - progress, 3); // easeOutCubic，更平滑的减速
            
            // 计算当前应该高亮的索引
            const stepsToTarget = ((targetIndex - currentIndex + items.length) % items.length);
            const currentStep = Math.floor(easeProgress * stepsToTarget);
            const newIndex = (currentIndex + currentStep) % items.length;

            // 更新高亮
            items.forEach(i => i.classList.remove('active'));
            const currentItem = items[newIndex];
            if (currentItem) {
                currentItem.classList.add('active');
                prizesList.scrollTop = currentItem.offsetTop - prizesList.clientHeight / 2 + currentItem.clientHeight / 2;
                updateFeaturedPrize(currentItem);
            }

            step++;
            if (step <= totalSteps) {
                animationId = setTimeout(stepOnce, stepDuration);
            } else {
                // 确保最终停在目标位置
                items.forEach(i => i.classList.remove('active'));
                const targetItem = items[targetIndex];
                if (targetItem) {
                    targetItem.classList.add('active');
                    prizesList.scrollTop = targetItem.offsetTop - prizesList.clientHeight / 2 + targetItem.clientHeight / 2;
                    updateFeaturedPrize(targetItem);
                }
                onDone && onDone();
            }
        }

        // 开始动画
        animationId = 1; // 标记动画开始
        stepOnce();
        
        // 返回取消函数
        return () => {
            if (animationId) {
                clearTimeout(animationId);
                animationId = null;
            }
        };
    }

    function performSpin(count) {
        // 确保页面完全加载
        if (!pageFullyLoaded) {
            resetSpinningState();
            return;
        }
        
        const formData = new FormData();
        formData.append('count', count);
        
        fetch('fortune-wheel-spin.php', { 
            method: 'POST', 
            body: formData,
            cache: 'no-cache',
            credentials: 'same-origin'
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    lastResults = Array.isArray(data.results) ? data.results : [];
                    lastWinning = pickPrimaryResult(lastResults);
                    const targetId = lastWinning?.prize?.id || chooseTargetPrizeId(lastResults);
                    
                    // 取消之前的动画（如果有）
                    if (currentAnimation) {
                        currentAnimation();
                        currentAnimation = null;
                    }
                    
                    // 开始新的动画
                    currentAnimation = animateToTargetPrize(targetId, () => {
                        featuredPrize.classList.remove('pulsing');
                        syncFeaturedWithWinning(lastWinning);
                        syncLeftListHighlightByName(lastWinning?.prize?.name);
                        showResults(lastResults);
                        currentAnimation = null; // 清除动画引用
                        
                        // 抽奖动画完成后，延迟标记结果已完全显示
                        setTimeout(() => {
                            resultsFullyDisplayed = true;
                            updateButtonStates(); // 更新按钮状态
                        }, 2000); // 减少到2秒，因为动画已经2.5秒了
                    });
                } else {
                    alert('抽奖失败: ' + data.message);
                    featuredPrize.classList.remove('pulsing');
                    resultsFullyDisplayed = true; // 失败时也标记结果已显示
                    resetSpinningState();
                }
            })
            .catch(error => {
                alert('网络错误，请检查网络连接后重试。');
                featuredPrize.classList.remove('pulsing');
                resultsFullyDisplayed = true; // 错误时也标记结果已显示
                resetSpinningState();
            });
    }

    // 选择用于展示动画落点的目标奖品ID：
    // 优先使用第一条结果（与用户直觉一致）；无则兜底列表第一项
    function chooseTargetPrizeId(results) {
        if (!results || !results.length) {
            return parseInt(prizeItems[0]?.dataset.prizeId) || 0;
        }
        const first = results.find(r => r && r.prize && r.prize.id);
        if (first) return first.prize.id;
        return parseInt(prizeItems[0]?.dataset.prizeId) || 0;
    }

    // 选择“展示用”的主结果：优先第一条；若无则 null
    function pickPrimaryResult(results) {
        if (!results || !results.length) return null;
        return results[0];
    }

    // 同步右侧卡片到中奖详情（图标 + 标题 + 描述 + 徽章）
    function syncFeaturedWithWinning(winning) {
        if (!winning || !winning.prize) return;
        const typeIcon = `<span class=\"fx-emoji\">${getPrizeIcon(winning.prize.type)}</span>`;
        const statusMsg = getResultMessage(winning.result || {}, winning.prize.name);
        const titleClass = getGradeClassByText((winning.grade || '').toString());
        featuredPrize.innerHTML = `
            <div class="win-badge">${winning.result?.status === 'nothing' ? '未中奖' : '中奖结果'}</div>
            <div class="neo-card">
                <div class="neo-halo"></div>
                <div class="neo-glow"></div>
                <div class="neo-emoji">${typeIcon}</div>
                <div class="neo-title fw-prize-title ${titleClass}">${winning.prize.name}</div>
                <div class="neo-desc">${statusMsg}</div>
            </div>
        `;
    }

    function getGradeClassByText(gradeText) {
        if (!gradeText) return 'fw-grad-others';
        const m = gradeText.match(/^(\d+)/);
        if (!m) return 'fw-grad-others';
        let num = parseInt(m[1], 10);
        if (isNaN(num) || num < 1) return 'fw-grad-others';
        if (num > 15) num = 15; // 归一到 1~15
        return `fw-grad-g${num}`;
    }

    // 根据奖品名称同步左侧列表高亮（文本精确匹配，避免类名副作用）
    function syncLeftListHighlightByName(prizeName) {
        if (!prizeName) return;
        const items = Array.from(prizeItems);
        const matched = items.find(i => (i.dataset.prizeName || '').trim() === String(prizeName).trim());
        if (!matched) return;
        items.forEach(i => i.classList.remove('active'));
        matched.classList.add('active');
        prizesList.scrollTop = matched.offsetTop - prizesList.clientHeight / 2 + matched.clientHeight / 2;
    }
    
    function showResults(results) {
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        modalTitle.textContent = `抽奖结果 (${results.length}个)`;
        modalBody.innerHTML = '';
        results.forEach(item => {
            const message = getResultMessage(item.result, item.prize.name);
            const icon = getPrizeIcon(item.prize?.type);
            const gradClass = getGradeClassByText((item.grade || '').toString());
            const badge = item.result?.status === 'nothing' ? '<span style="display:inline-block;padding:2px 6px;border-radius:6px;background:#e9ecef;color:#6c757d;font-size:12px;margin-left:6px;">未中奖</span>' : '<span style="display:inline-block;padding:2px 6px;border-radius:6px;background:#e8f5e9;color:#2e7d32;font-size:12px;margin-left:6px;">中奖</span>';
            const resultDiv = document.createElement('div');
            resultDiv.className = `result-item ${item.result.status === 'nothing' ? 'nothing' : 'win'}`;
            resultDiv.innerHTML = `<strong class="${gradClass}">${icon} ${item.grade}: ${item.prize.name}</strong> ${badge} - <span>${message}</span>`;
            modalBody.appendChild(resultDiv);
        });
        resultModal.classList.add('visible');
    }

    function getResultMessage(result, prizeName) {
        switch (result.status) {
            case 'awarded': return `恭喜！您获得了 ${result.value} ${result.unit || ''}`;
            case 'compensated': return `物品重复，补偿您 ${result.value} ${result.unit}`;
            case 'extended': return `有效期已延长 ${result.value} ${result.unit}`;
            case 'already_owned': return `物品重复，无补偿`;
            case 'compensated_high_class': return `等级更高，补偿您 ${result.value} ${result.unit}`;
            case 'already_owned_high_class': return `您已是更高贵的身份`;
            case 'nothing': return '谢谢参与，再接再厉！';
            default: return '未知状态';
        }
    }

    function resetSpinningState() {
        isSpinning = false;
        resultsFullyDisplayed = true; // 重置结果显示状态
        lastClickTime = 0; // 重置点击时间
        
        // 取消当前动画
        if (currentAnimation) {
            currentAnimation();
            currentAnimation = null;
        }
        
        toggleButtons(true);
        // 确保按钮状态正确更新
        updateButtonStates();
    }
    
    function toggleButtons(enabled) {
        spinButtons.forEach(button => {
            button.disabled = !enabled;
            // 同步更新按钮状态显示
            updateButtonStates();
        });
    }

    // 获取最新的用户状态
    function updateUserStats() {
        fetch('fortune-wheel.php', {
            method: 'GET',
            cache: 'no-cache'
        })
        .then(response => response.text())
        .then(html => {
            // 解析HTML获取最新的用户状态
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            
            // 更新抽奖次数显示
            const newStatsElement = doc.querySelector('.stats-item[data-type="today_count"] .value');
            if (newStatsElement) {
                const currentStatsElement = document.querySelector('.stats-item[data-type="today_count"] .value');
                if (currentStatsElement) {
                    currentStatsElement.textContent = newStatsElement.textContent;
                }
            }
            
            // 更新余额显示
            const newBalanceElement = doc.querySelector('.stats-item[data-type="balance"] .value');
            if (newBalanceElement) {
                const currentBalanceElement = document.querySelector('.stats-item[data-type="balance"] .value');
                if (currentBalanceElement) {
                    currentBalanceElement.textContent = newBalanceElement.textContent;
                }
            }
            
        })
        .catch(error => {
            // 静默处理错误
        });
    }

    function closeModal() {
        resultModal.classList.remove('visible');
        // 延迟重置状态，确保动画完成
        setTimeout(() => {
            // 恢复到预设态：右侧卡片显示“选择奖品/查看详情”，左侧高亮清除
            try {
                const items = Array.from(prizeItems);
                items.forEach(i => i.classList.remove('active'));
                // 恢复右侧预设文案
                featuredPrize.innerHTML = `
                    <div class="neo-card">
                        <div class="neo-halo"></div>
                        <div class="neo-glow"></div>
                        <div class="neo-emoji">✨</div>
                        <div class="neo-title fw-prize-title fw-grad-others">选择奖品</div>
                        <div class="neo-desc">查看详情</div>
                    </div>
                `;
            } catch (e) { /* 忽略恢复过程中的非致命错误 */ }

            resetSpinningState();
            // 更新用户状态而不刷新页面
            updateUserStats();
        }, 500);
    }

    modalClose.addEventListener('click', closeModal);
    resultModal.addEventListener('click', (e) => e.target === resultModal && closeModal());
});
</script>
