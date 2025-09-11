<?php

namespace NexusPlugin\FortuneWheel;

class BonusNameHelper
{
    /**
     * 获取魔力值别名
     */
    public static function getBonusName(): string
    {
        return get_setting('fortune_wheel.bonus_name') ?: '魔力值';
    }

    /**
     * 获取魔力单位别名
     */
    public static function getBonusUnit(): string
    {
        return get_setting('fortune_wheel.bonus_unit') ?: '魔力';
    }

    /**
     * 替换文本中的魔力相关词汇
     */
    public static function replaceBonusText(string $text): string
    {
        $bonusName = self::getBonusName();
        $bonusUnit = self::getBonusUnit();
        
        // 替换常见的魔力相关词汇
        $replacements = [
            '魔力值' => $bonusName,
            '魔力' => $bonusUnit,
            '当前魔力' => '当前' . $bonusUnit,
            '消耗魔力' => '消耗' . $bonusUnit,
            '魔力不足' => $bonusName . '不足',
            '需要更多魔力值' => '需要更多' . $bonusName,
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }

    /**
     * 获取带魔力别名的消息
     */
    public static function getInsufficientBonusMessage(int $needed, int $current): string
    {
        $bonusName = self::getBonusName();
        return "{$bonusName}不足，需要 " . number_format($needed) . "，您只有 " . number_format($current);
    }

    /**
     * 获取奖品类型的显示名称
     */
    public static function getPrizeTypeName(string $type): string
    {
        switch ($type) {
            case 'bonus':
                return self::getBonusName();
            case 'upload':
                return '上传量';
            case 'vip_days':
                return 'VIP天数';
            case 'rainbow_id_days':
                return '彩虹ID天数';
            case 'invite_temp':
                return '临时邀请';
            case 'invite_perm':
                return '永久邀请';
            case 'medal':
                return '勋章';
            case 'rename_card':
                return '改名卡';
            case 'attendance_card':
                return '补签卡';
            case 'nothing':
                return '谢谢参与';
            default:
                return $type;
        }
    }
}
