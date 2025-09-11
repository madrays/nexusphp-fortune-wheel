<?php

namespace NexusPlugin\FortuneWheel;

class PrizeManager
{
    /**
     * 获取所有奖品数据用于表单
     */
    public static function getFormData(): array
    {
        try {
            $sql = "SELECT * FROM fortune_wheel_prizes ORDER BY sort_order, id";
            $prizes = \Nexus\Database\NexusDB::select($sql);
            
            $formData = [];
            foreach ($prizes as $prize) {
                $formData[] = [
                    'id' => $prize['id'],
                    'name' => $prize['name'],
                    'type' => $prize['type'],
                    'value' => (float)$prize['value'],
                    'probability' => (float)$prize['probability'],
                    'description' => $prize['description'] ?? '',
                    'enabled' => (bool)$prize['enabled'],
                    'sort_order' => (int)$prize['sort_order'],
                    'stock' => (int)($prize['stock'] ?? -1),
                ];
            }

            error_log("PrizeManager: Loaded " . count($formData) . " prizes from database");
            
            return $formData;
        } catch (\Exception $e) {
            error_log("PrizeManager: Failed to get form data: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 保存表单数据到数据库
     */
    public static function saveFormData(array $formData): bool
    {
        try {
            // 清空现有奖品
            \Nexus\Database\NexusDB::statement("DELETE FROM fortune_wheel_prizes");
            
            // 插入新奖品
            foreach ($formData as $index => $prize) {
                if (empty($prize['name']) || empty($prize['type'])) {
                    continue;
                }
                
                $name = sqlesc($prize['name']);
                $type = sqlesc($prize['type']);
                $value = (float)($prize['value'] ?? 0);
                $probability = (float)($prize['probability'] ?? 0);
                $description = sqlesc($prize['description'] ?? '');
                $enabled = $prize['enabled'] ? 1 : 0;
                $sortOrder = $prize['sort_order'] ?? $index;
                
                $stock = (int)($prize['stock'] ?? -1);

                $sql = "INSERT INTO fortune_wheel_prizes (name, type, value, probability, description, enabled, sort_order, stock, created_at, updated_at)
                        VALUES ($name, $type, $value, $probability, $description, $enabled, $sortOrder, $stock, NOW(), NOW())";
                
                \Nexus\Database\NexusDB::statement($sql);
            }
            
            error_log("PrizeManager: Successfully saved " . count($formData) . " prizes");
            return true;
        } catch (\Exception $e) {
            error_log("PrizeManager: Failed to save form data: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 获取奖品类型选项
     */
    public static function getTypeOptions(): array
    {
        $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
        return [
            'bonus' => $bonusName,
            'upload' => '上传量',
            'vip_days' => 'VIP天数',
            'rainbow_id_days' => '彩虹ID天数',
            'invite_temp' => '临时邀请',
            'invite_perm' => '永久邀请',
            'medal' => '勋章',
            'rename_card' => '改名卡',
            'nothing' => '谢谢参与',
        ];
    }

    /**
     * 获取默认奖品数据
     */
    public static function getDefaultPrize(): array
    {
        return [
            'name' => '',
            'type' => 'bonus',
            'value' => 0,
            'probability' => 0,
            'description' => '',
            'enabled' => true,
            'sort_order' => 0,
            'stock' => -1,
        ];
    }
}
