<?php

return [
    'title' => '幸运转盘',
    'description' => '消耗魔力抽取各种奖品',
    
    'labels' => [
        'current_bonus' => function() { return '当前' . (get_setting('fortune_wheel.bonus_unit') ?: '魔力'); },
        'cost_per_draw' => '每次消耗',
        'today_drawn' => '今日已抽',
        'daily_limit' => '每日限制',
        'total_draws' => '总抽奖次数',
        'win_rate' => '中奖率',
        'start_draw' => '开始抽奖',
        'drawing' => '转盘转动中...',
        'my_records' => '我的抽奖记录',
        'time' => '时间',
        'prize' => '奖品',
        'result' => '结果',
        'cost' => function() { return '消耗' . (get_setting('fortune_wheel.bonus_unit') ?: '魔力'); },
        'won' => '🎉 中奖',
        'lost' => '😢 未中奖',
        'no_records' => '暂无抽奖记录',
    ],
    
    'messages' => [
        'plugin_disabled' => '幸运转盘暂未开放',
        'user_not_found' => '用户不存在',
        'insufficient_class' => '用户等级不足',
        'insufficient_bonus' => function() {
            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
            return $bonusName . '不足，需要 :amount';
        },
        'daily_limit_reached' => '今日抽奖次数已用完',
        'no_prizes' => '暂无可用奖品',
        'congratulations' => '🎉 恭喜中奖！',
        'sorry' => '😢 很遗憾',
        'thank_you' => '谢谢参与',
        'draw_failed' => '抽奖失败，请重试',
        'network_error' => '网络错误，请重试',
    ],
    
    'prizes' => [
        'bonus' => function() { return get_setting('fortune_wheel.bonus_unit') ?: '魔力'; },
        'vip' => 'VIP',
        'medal' => '勋章',
        'upload' => '上传量',
        'download' => '下载量',
        'invitation' => '邀请名额',
        'nothing' => '谢谢参与',
    ],
    
    'admin' => [
        'settings' => '转盘设置',
        'prizes_management' => '奖品管理',
        'statistics' => '抽奖统计',
        'enabled' => '启用转盘',
        'cost_bonus' => function() {
            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
            return '每次抽奖消耗' . $bonusName;
        },
        'vip_extra_bonus' => function() {
            $bonusUnit = get_setting('fortune_wheel.bonus_unit') ?: '魔力';
            return 'VIP用户中VIP奖品额外' . $bonusUnit;
        },
        'daily_limit' => '每日抽奖次数限制',
        'min_user_class' => '最低用户等级要求',
        'prize_name' => '奖品名称',
        'prize_type' => '奖品类型',
        'prize_value' => '奖品数值',
        'probability' => '中奖概率(%)',
        'quantity' => '奖品数量',
        'sort_order' => '排序',
        'enabled_prize' => '启用',
        'add_prize' => '添加奖品',
        'edit_prize' => '编辑奖品',
        'delete_prize' => '删除奖品',
        'today_draws' => '今日抽奖',
        'total_draws' => '总抽奖',
        'today_wins' => '今日中奖',
        'total_wins' => '总中奖',
        'today_win_rate' => '今日中奖率',
        'total_win_rate' => '总中奖率',
    ],
];
