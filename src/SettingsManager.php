<?php

namespace NexusPlugin\FortuneWheel;

class SettingsManager
{
    /**
     * 获取设置标签页结构
     */
    public static function getSettingTab(): \Filament\Forms\Components\Tabs\Tab
    {
        return \Filament\Forms\Components\Tabs\Tab::make('幸运转盘设置')
            ->id('fortune_wheel_settings')
            ->schema(array_merge(
                [
                    \Filament\Forms\Components\Placeholder::make('made_by_madrays')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString('
                            <style>
                                li[x-sortable-handle] { display: none !important; }
                                .madrays-credit {
                                    background-color: #f0f4ff;
                                    border-left: 4px solid #4a67de;
                                    padding: 12px 16px;
                                    margin-bottom: 1.5rem;
                                    border-radius: 4px;
                                    font-size: 0.9em;
                                    color: #333;
                                }
                                .madrays-credit strong {
                                    color: #4a67de;
                                    font-weight: 600;
                                }
                            </style>
                            <div class="madrays-credit">
                                <strong>made_by_madrays</strong> - 本插件为定制开发，非开源产品，请勿分发。
                            </div>
                        ')),
                    \Filament\Forms\Components\Toggle::make('fortune_wheel.navigation_enabled')
                        ->label('启用自定义导航栏')
                        ->helperText('开启后，下方“导航管理”标签页的设置将生效，并完全接管主导航栏。关闭则恢复为系统默认导航栏，并自动在末尾添加“幸运转盘”链接。')
                        ->default(false),
                ],
                self::getSettingSchema()
            ))
            ->columns(2);
    }

    /**
     * 获取奖品管理标签页结构
     */
    public static function getPrizeTab(): \Filament\Forms\Components\Tabs\Tab
    {
        return \Filament\Forms\Components\Tabs\Tab::make('奖品管理')
            ->id('fortune_wheel_prizes')
            ->schema(self::getPrizeSchema())
            ->columns(1);
    }

    /**
     * 获取设置表单结构
     */
    protected static function getSettingSchema(): array
    {
        return [
            \Filament\Forms\Components\Section::make('基本设置')
                ->schema([
                    \Filament\Forms\Components\Toggle::make('fortune_wheel.enabled')
                        ->label('启用幸运转盘')
                        ->helperText('关闭后用户将无法访问幸运转盘功能')
                        ->default(true),

                    \Filament\Forms\Components\TextInput::make('fortune_wheel.daily_free_spins')
                        ->label('每日免费次数')
                        ->numeric()
                        ->default(3)
                        ->minValue(0)
                        ->maxValue(100)
                        ->helperText('用户每天可以免费抽奖的次数'),

                    \Filament\Forms\Components\TextInput::make('fortune_wheel.daily_max_spins')
                        ->label('每日最大次数')
                        ->numeric()
                        ->default(10)
                        ->minValue(0)
                        ->maxValue(1000)
                        ->helperText('用户每天最多可以抽奖的次数（包括付费）'),

                    \Filament\Forms\Components\TextInput::make('fortune_wheel.bonus_cost_per_spin')
                        ->label('付费抽奖消耗')
                        ->numeric()
                        ->default(1000)
                        ->minValue(0)
                        ->suffix(function () {
                            return get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                        })
                        ->helperText(function () {
                            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                            return "超出免费次数后，每次抽奖消耗的{$bonusName}";
                        }),

                    \Filament\Forms\Components\TextInput::make('fortune_wheel.spin_interval')
                        ->label('抽奖间隔')
                        ->numeric()
                        ->default(10)
                        ->minValue(0)
                        ->suffix('秒')
                        ->helperText('两次抽奖之间的最小间隔时间'),

                    \Filament\Forms\Components\Select::make('fortune_wheel.min_user_class')
                        ->label('最低用户等级要求')
                        ->options(self::getUserClassOptions())
                        ->default(\App\Models\User::CLASS_USER)
                        ->preload()
                        ->helperText('只有达到此等级及以上的用户才能参与抽奖'),
                ])
                ->columns(2),

            \Filament\Forms\Components\Section::make('魔力别名设置')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('fortune_wheel.bonus_name')
                        ->label('魔力值别名')
                        ->default('魔力值')
                        ->maxLength(20)
                        ->helperText('自定义魔力值的显示名称，如：憨豆、积分、金币等'),

                    \Filament\Forms\Components\TextInput::make('fortune_wheel.bonus_unit')
                        ->label('魔力单位别名')
                        ->default('魔力')
                        ->maxLength(20)
                        ->helperText('自定义魔力的单位名称，如：憨豆、积分、金币等'),
                ])
                ->columns(2),

            \Filament\Forms\Components\Section::make('重复中奖设置')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('fortune_wheel.vip_duplicate_bonus')
                        ->label(function () {
                            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                            return "VIP重复中奖{$bonusName}";
                        })
                        ->numeric()
                        ->default(500)
                        ->suffix(function () {
                            return get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                        })
                        ->helperText(function () {
                            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                            return "用户重复中奖VIP时给予的{$bonusName}";
                        }),

                    \Filament\Forms\Components\TextInput::make('fortune_wheel.rainbow_id_duplicate_bonus')
                        ->label(function () {
                            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                            return "彩虹ID重复中奖{$bonusName}";
                        })
                        ->numeric()
                        ->default(300)
                        ->suffix(function () {
                            return get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                        })
                        ->helperText(function () {
                            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                            return "用户重复中奖彩虹ID时给予的{$bonusName}";
                        }),

                    \Filament\Forms\Components\TextInput::make('fortune_wheel.rename_card_duplicate_bonus')
                        ->label(function () {
                            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                            return "改名卡重复中奖{$bonusName}";
                        })
                        ->numeric()
                        ->default(200)
                        ->suffix(function () {
                            return get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                        })
                        ->helperText(function () {
                            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                            return "用户重复中奖改名卡时给予的{$bonusName}";
                        }),

                    \Filament\Forms\Components\TextInput::make('fortune_wheel.medal_duplicate_bonus')
                        ->label(function () {
                            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                            return "勋章重复中奖{$bonusName}";
                        })
                        ->numeric()
                        ->default(400)
                        ->suffix(function () {
                            return get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                        })
                        ->helperText(function () {
                            $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                            return "用户重复中奖勋章时给予的{$bonusName}";
                        }),

                    \Filament\Forms\Components\Toggle::make('fortune_wheel.vip_stack_time')
                        ->label('VIP时间叠加')
                        ->default(true)
                        ->helperText('是否允许VIP时间叠加'),

                    \Filament\Forms\Components\Toggle::make('fortune_wheel.rainbow_id_stack_time')
                        ->label('彩虹ID时间叠加')
                        ->default(true)
                        ->helperText('是否允许彩虹ID时间叠加'),
                ])
                ->columns(2),

            \Filament\Forms\Components\Section::make('显示设置')
                ->schema([
                    \Filament\Forms\Components\Toggle::make('fortune_wheel.show_recent_wins')
                        ->label('显示中奖公示')
                        ->default(true)
                        ->helperText('是否在首页显示最近的中奖记录'),

                    \Filament\Forms\Components\TextInput::make('fortune_wheel.recent_wins_count')
                        ->label('公示记录数量')
                        ->numeric()
                        ->default(10)
                        ->minValue(1)
                        ->maxValue(100)
                        ->helperText('首页显示的中奖记录数量'),
                ])
                ->columns(2),
        ];
    }

    /**
     * 获取奖品管理表单结构
     */
    protected static function getPrizeSchema(): array
    {
        return [
            \Filament\Forms\Components\Placeholder::make('prize_info')
                ->label('')
                ->content(function () {
                    $prizeCount = count(self::getAllPrizes());
                    return new \Illuminate\Support\HtmlString(
                        '<div style="padding: 15px; background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px; margin-bottom: 20px;">' .
                        '<h3 style="margin: 0 0 10px 0; color: #0369a1;">奖品管理说明</h3>' .
                        '<p style="margin: 0; color: #0369a1;">当前奖品数量: <strong>' . $prizeCount . '</strong></p>' .
                        '<p style="margin: 5px 0 0 0; color: #0369a1;">请使用下方的表格来管理奖品，支持添加、编辑、删除和排序。</p>' .
                        '<p style="margin: 5px 0 0 0; color: #0369a1;"><strong>奖品等级说明：</strong>排序数值越小，奖品等级越靠前（如：1=一等奖，2=二等奖，3=三等奖）。</p>' .
                        '</div>'
                    );
                })
                ->columnSpanFull(),

            \Filament\Forms\Components\Repeater::make('fortune_wheel_prizes')
                ->label('奖品列表')
                ->schema([
                    \Filament\Forms\Components\Grid::make(3)
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('name')
                                ->label('奖品名称')
                                ->required(),

                            \Filament\Forms\Components\Select::make('type')
                                ->label('奖品类型')
                                ->options(function () {
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
                                        'attendance_card' => '补签卡',
                                        'nothing' => '谢谢参与',
                                    ];
                                })
                                ->required()
                                ->reactive(),

                            \Filament\Forms\Components\TextInput::make('value')
                                ->label(fn (callable $get) => self::getValueLabel($get('type')))
                                ->helperText(fn (callable $get) => self::getValueHelperText($get('type')))
                                ->numeric()
                                ->required(fn (callable $get) => !in_array($get('type'), ['nothing', 'medal']))
                                ->visible(fn (callable $get) => !in_array($get('type'), ['nothing', 'medal'])),
                            
                            \Filament\Forms\Components\Select::make('medal_id')
                                ->label('选择勋章')
                                ->options(self::getMedalOptions())
                                ->required(fn (callable $get) => $get('type') === 'medal')
                                ->visible(fn (callable $get) => $get('type') === 'medal')
                                ->helperText('选择一枚勋章作为奖品'),
                        ]),

                    \Filament\Forms\Components\Grid::make(4)
                        ->schema([
                            \Filament\Forms\Components\TextInput::make('probability')
                                ->label('概率(%)')
                                ->numeric()
                                ->step(0.01)
                                ->minValue(0)
                                ->maxValue(100)
                                ->required(),

                            \Filament\Forms\Components\TextInput::make('stock')
                                ->label('库存')
                                ->numeric()
                                ->default(-1)
                                ->helperText('-1=无限制'),

                            \Filament\Forms\Components\TextInput::make('sort_order')
                                ->label('排序')
                                ->numeric()
                                ->default(0)
                                ->helperText('数值越小等级越靠前（1=一等奖，2=二等奖）'),

                            \Filament\Forms\Components\Toggle::make('enabled')
                                ->label('启用')
                                ->default(true),
                        ]),

                    \Filament\Forms\Components\Textarea::make('description')
                        ->label('描述')
                        ->rows(2)
                        ->columnSpanFull(),
                ])
                ->defaultItems(0)
                ->addActionLabel('添加奖品')
                ->reorderableWithButtons()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                ->columnSpanFull()
                ->afterStateHydrated(function ($component, $state) {
                    if (empty($state)) {
                        $prizes = self::getAllPrizes();
                        $formData = [];
                        foreach ($prizes as $prize) {
                            $prizeData = [
                                'name' => $prize['name'],
                                'type' => $prize['type'],
                                'value' => (float)$prize['value'],
                                'probability' => (float)$prize['probability'],
                                'stock' => (int)$prize['stock'],
                                'sort_order' => (int)$prize['sort_order'],
                                'enabled' => (bool)$prize['enabled'],
                                'description' => $prize['description'] ?? '',
                            ];

                            if ($prizeData['type'] === 'upload' && is_numeric($prizeData['value']) && $prizeData['value'] > 0) {
                                $prizeData['value'] = round((float)$prize['value'] / (1024 * 1024 * 1024), 2);
                            } elseif ($prizeData['type'] === 'medal') {
                                $prizeData['medal_id'] = (int)$prize['value'];
                            }
                            $formData[] = $prizeData;
                        }
                        $component->state($formData);
                    }
                }),
        ];
    }

    /**
     * 获取所有奖品数据
     */
    public static function getAllPrizes(): array
    {
        try {
            $sql = "SELECT * FROM fortune_wheel_prizes ORDER BY sort_order, id";
            return \Nexus\Database\NexusDB::select($sql);
        } catch (\Exception $e) {
            error_log("SettingsManager: Failed to get prizes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * 处理奖品数据保存
     */
    public static function savePrizes(array $prizesData): bool
    {
        try {
            // 清空现有奖品
            \Nexus\Database\NexusDB::statement("DELETE FROM fortune_wheel_prizes");

            $allSuccessful = true;
            // 插入新奖品
            foreach ($prizesData as $index => $prize) {
                if (empty($prize['name']) || empty($prize['type'])) {
                    continue;
                }

                // CONVERSION LOGIC FOR SAVING
                if ($prize['type'] === 'upload' && is_numeric($prize['value'])) {
                    // Convert GB to Bytes
                    $prize['value'] = (float)$prize['value'] * 1024 * 1024 * 1024;
                } elseif ($prize['type'] === 'medal' && isset($prize['medal_id'])) {
                    // Use medal_id as the value
                    $prize['value'] = (int)$prize['medal_id'];
                }

                if (!self::createPrize($prize)) {
                    $allSuccessful = false;
                    error_log("SettingsManager: Loop failed to create prize with name: " . ($prize['name'] ?? 'N/A'));
            }
            }

            if ($allSuccessful) {
                error_log("SettingsManager: All prizes were processed successfully.");
            } else {
                error_log("SettingsManager: One or more prizes failed to be created.");
            }
            
            return $allSuccessful;
        } catch (\Exception $e) {
            error_log("SettingsManager: An exception occurred in savePrizes: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 创建奖品
     */
    public static function createPrize(array $data): bool
    {
        try {
            // FINAL SOLUTION: Mimic the working `recordDraw` function.
            // Manually escape strings with addslashes() and quote them.
            // This avoids both prepared statements and the fatal sqlesc() error.
            $name = "'" . addslashes($data['name']) . "'";
            $type = "'" . addslashes($data['type']) . "'";
            $description = "'" . addslashes($data['description'] ?? '') . "'";

            $value = (float)($data['value'] ?? 0);
            $probability = (float)($data['probability'] ?? 0);
            $stock = (int)($data['stock'] ?? -1);
            $enabled = ($data['enabled'] ?? true) ? 1 : 0;
            $sortOrder = (int)($data['sort_order'] ?? 0);

            $sql = "INSERT INTO fortune_wheel_prizes (name, type, value, probability, stock, description, enabled, sort_order, created_at, updated_at)
                    VALUES ($name, $type, $value, $probability, $stock, $description, $enabled, $sortOrder, NOW(), NOW())";

            \Nexus\Database\NexusDB::statement($sql);
            return true;
        } catch (\Exception $e) {
            error_log("SettingsManager: Exception in createPrize: " . $e->getMessage());
            return false;
        }
    }

    protected static function getMedalOptions(): array
    {
        try {
            // Per user feedback, remove filtering to load ALL medals,
            // allowing admins to award rare/non-sale items.
            $sql = "SELECT id, name FROM medals ORDER BY name";
            $medals = \Nexus\Database\NexusDB::select($sql);

            $options = [];
            foreach ($medals as $medal) {
                $options[$medal['id']] = $medal['name'];
            }
            return $options;
        } catch (\Exception $e) {
            error_log("SettingsManager: Failed to load medals: " . $e->getMessage());
            return ['error' => '加载勋章失败'];
        }
    }

    protected static function getValueLabel(?string $type = null): string
    {
        switch ($type) {
            case 'upload':
                return '上传量 (GB)';
            case 'vip_days':
                return 'VIP 天数';
            case 'bonus':
                $bonusName = get_setting('fortune_wheel.bonus_name') ?: '魔力值';
                return $bonusName;
            case 'rainbow_id_days':
                return '彩虹ID天数';
            case 'invite_temp':
            case 'invite_perm':
                return '邀请数量';
            case 'rename_card':
                return '改名卡数量 (一般为1)';
            case 'attendance_card':
                return '补签卡数量';
            default:
                return '数值';
        }
    }

    protected static function getValueHelperText(?string $type = null): string
    {
        switch ($type) {
            case 'upload':
                return '请输入GB单位的数值，系统会自动转换为字节存储。';
            case 'medal':
                return '请从上方的下拉框中选择勋章。';
            case 'nothing':
                return '此类型无需填写数值。';
            default:
                return '请输入相应的奖品数值。';
        }
    }

    /**
     * 获取用户等级选项
     */
    protected static function getUserClassOptions(): array
    {
        $options = [];
        // 尝试使用 get_user_class_name 获取所有可能的等级
        if (function_exists('get_user_class_name')) {
            // 扫描正常范围，避免卡死
            for ($i = 0; $i <= 20; $i++) {
                $name = get_user_class_name($i, false, false, true);
                if (!empty($name)) {
                    $options[$i] = $name;
                }
            }
        }
        // 如果上述方法未获取到，尝试使用 App\Models\User::listAllClass()
        if (empty($options) && method_exists(\App\Models\User::class, 'listAllClass')) {
            $options = \App\Models\User::listAllClass();
        }
        // 最后回退到默认的 listClass 范围
        if (empty($options)) {
            $options = \App\Models\User::listClass(0, 20);
        }
        
        // 添加额外的特殊等级选项
        $extraOptions = [
            'staffmem' => '管理组成员 (staffmem)',
            'admin' => '站点管理员 (admin)',
            'topten' => 'Top 10 访问者 (topten)',
            'offers' => '候补区访问者 (offers)',
            'requests' => '求种区访问者 (requests)',
            'log' => '日志查看者 (log)',
        ];
        
        // 合并额外选项
        foreach ($extraOptions as $key => $value) {
            $options[$key] = $value;
        }
        
        return $options;
    }
}
