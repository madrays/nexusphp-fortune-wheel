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
        border-radius: 12px;
    padding: 20px;
        box-shadow: 0 4px 12px var(--shadow-color);
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
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
}

    .stat-card {
    text-align: center;
    }
    .stat-card .value {
        font-size: 1.6em;
        font-weight: 600;
        color: var(--primary-color);
}
    .stat-card .label {
        font-size: 0.8em;
        color: #6c757d;
        margin-top: 2px;
}

    .featured-prize {
        background: linear-gradient(145deg, #fdfdfd, #f1f3f6);
        border-radius: 12px;
        padding: 30px 20px;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    text-align: center;
        min-height: 180px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .featured-prize h2 {
        margin: 0 0 10px 0;
        font-size: 1.5em;
        color: #333;
        font-weight: 600;
    }
    .featured-prize p {
        margin: 0;
        font-size: 1em;
        color: #555;
        line-height: 1.5;
}
    .featured-prize .placeholder h2 {
        color: #aaa;
    }
     .featured-prize .placeholder p {
        color: #bbb;
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
        transition: all 0.2s ease;
    display: flex;
        justify-content: space-between;
    align-items: center;
        background: #f8f9fa;
    }
    .prize-item:hover, .prize-item.active {
        background: var(--primary-color);
        color: white;
        transform: translateX(5px);
    }
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

    .actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .spin-btn {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 1em;
    cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
}
    .spin-btn:hover:not(:disabled) {
    transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 123, 255, 0.4);
}
    .spin-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
        box-shadow: none;
}

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
    }
    .records-table th, .records-table td {
        padding: 8px 10px;
    text-align: left;
    border-bottom: 1px solid #eee;
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
                <ul class="prizes-list" id="prizes-list">
                    <?php foreach ($prizes as $prize): ?>
                        <li class="prize-item" data-prize-id="<?= $prize['id'] ?>" data-prize-name="<?= htmlspecialchars($prize['name']) ?>" data-prize-desc="<?= htmlspecialchars($prize['description']) ?>" data-prize-type="<?= htmlspecialchars($prize['type']) ?>">
                            <span><?= htmlspecialchars($prize['name']) ?></span>
                            <span class="grade"><?= htmlspecialchars($grades[$prize['id']] ?? '参与奖') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="right-panel">
            <div class="featured-prize" id="featured-prize">
                <div class="placeholder">
                    <div class="featured-prize-icon">✨</div>
                    <h2>选择奖品</h2>
                    <p>查看详情</p>
            </div>
            </div>
             <div class="rules-container" style="margin-top:20px;">
                <ul>
                    <li>每日可抽奖 <strong><?= (int)($settings['daily_max_spins'] ?? 0) ?></strong> 次，前 <strong><?= (int)($settings['daily_free_spins'] ?? 0) ?></strong> 次免费。</li>
                    <li>免费次数用尽后，每次抽奖消耗 <strong><?= number_format($costPerSpin) ?></strong> <?= $bonusName ?>。</li>
                    <li>系统支持单次、10次、20次、50次连续抽奖。</li>
                    <li>将鼠标悬停在左侧奖品列表上可查看奖品详情。</li>
                </ul>
            </div>
             <div class="actions-container" style="margin-top:20px;">
                <div class="actions">
                    <button class="spin-btn" data-count="1">抽1次</button>
                    <button class="spin-btn" data-count="10">抽10次</button>
                    <button class="spin-btn" data-count="20">抽20次</button>
                    <button class="spin-btn" data-count="50">抽50次</button>
            </div>
            </div>
        </div>
    </div>

    <div class="history-container">
        <div class="history-toggle">📋 我的抽奖记录</div>
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
                        <td class="<?= $record['is_win'] ? 'win' : 'lose' ?>"><?= $record['is_win'] ? '🎉' : '💧' ?></td>
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
            <h2 id="modal-title">抽奖结果</h2>
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
    const spinButtons = document.querySelectorAll('.spin-btn');
    const resultModal = document.getElementById('result-modal');
    const modalClose = resultModal.querySelector('.modal-close');
    let isSpinning = false;
    let animationFrameId;

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
        featuredPrize.innerHTML = `
            <div class="featured-prize-icon">${icon}</div>
            <h2>${name}</h2>
            <p>${desc}</p>
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

    // Spin logic
    spinButtons.forEach(button => {
        button.addEventListener('click', () => {
            if (isSpinning) return;
            isSpinning = true;
            toggleButtons(false);
            const count = button.dataset.count;
            playAnimation(() => performSpin(count));
        });
    });

    function playAnimation(callback) {
        let duration = 3000;
        let startTime = performance.now();

        function animate(currentTime) {
            const elapsedTime = currentTime - startTime;
            const randomIndex = Math.floor(Math.random() * prizeItems.length);
            const randomItem = prizeItems[randomIndex];

            prizeItems.forEach(item => item.classList.remove('active'));
            randomItem.classList.add('active');
            prizesList.scrollTop = randomItem.offsetTop - prizesList.clientHeight / 2 + randomItem.clientHeight / 2;
            updateFeaturedPrize(randomItem);

            if (elapsedTime < duration) {
                animationFrameId = requestAnimationFrame(animate);
            } else {
                callback();
            }
        }
        animationFrameId = requestAnimationFrame(animate);
    }

    function performSpin(count) {
        const formData = new FormData();
        formData.append('count', count);
        
        fetch('fortune-wheel-spin.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showResults(data.results);
                } else {
                    alert('抽奖失败: ' + data.message);
                    resetSpinningState();
            }
        })
        .catch(error => {
            console.error('Error:', error);
                alert('发生未知错误，请稍后重试。');
                resetSpinningState();
        });
    }
    
    function showResults(results) {
        const modalTitle = document.getElementById('modal-title');
        const modalBody = document.getElementById('modal-body');
        modalTitle.textContent = `抽奖结果 (${results.length}个)`;
        modalBody.innerHTML = '';
        results.forEach(item => {
            const message = getResultMessage(item.result, item.prize.name);
            const resultDiv = document.createElement('div');
            resultDiv.className = `result-item ${item.result.status === 'nothing' ? 'nothing' : 'win'}`;
            resultDiv.innerHTML = `<strong>${item.grade}: ${item.prize.name}</strong> - <span>${message}</span>`;
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
        toggleButtons(true);
    }
    
    function toggleButtons(enabled) {
        spinButtons.forEach(button => button.disabled = !enabled);
    }

    function closeModal() {
        resultModal.classList.remove('visible');
        resetSpinningState();
        setTimeout(() => location.reload(), 200);
    }

    modalClose.addEventListener('click', closeModal);
    resultModal.addEventListener('click', (e) => e.target === resultModal && closeModal());
});
</script>
