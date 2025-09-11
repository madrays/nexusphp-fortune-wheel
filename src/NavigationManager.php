<?php

namespace NexusPlugin\FortuneWheel;

use Illuminate\Support\Facades\DB;
use Nexus\Database\NexusDB;

class NavigationManager
{
    protected static $tableName = 'navigations';

    public static function getNavigationTab(): \Filament\Forms\Components\Tabs\Tab
    {
        return \Filament\Forms\Components\Tabs\Tab::make('导航管理')
            ->id('navigations_manager')
            ->schema([
                \Filament\Forms\Components\Repeater::make('navigations')
                    ->label('主导航菜单')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('name')->label('名称')->required(),
                        \Filament\Forms\Components\TextInput::make('url')->label('链接')
                            ->helperText('如果此菜单有子菜单且链接为空，则不可点击。')
                            ->live(),
                        \Filament\Forms\Components\Toggle::make('is_external')->label('外链')->helperText('勾选后将直接跳转到填写的链接，不拼接站点域名'),
                        \Filament\Forms\Components\Select::make('permission')->label('所需权限')
                            ->options(self::getPermissionOptions()),
                        \Filament\Forms\Components\Toggle::make('new_tab')->label('新标签页打开'),
                        \Filament\Forms\Components\Repeater::make('children')
                            ->label('子菜单')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('name')->label('名称')->required(),
                                \Filament\Forms\Components\TextInput::make('url')->label('链接')->required(),
                                \Filament\Forms\Components\Toggle::make('is_external')->label('外链')->helperText('勾选后将直接跳转到填写的链接，不拼接站点域名'),
                                \Filament\Forms\Components\Select::make('permission')->label('所需权限')
                                    ->options(self::getPermissionOptions()),
                                \Filament\Forms\Components\Toggle::make('new_tab')->label('新标签页打开'),
                            ])
                            ->addActionLabel('添加子菜单项')
                            ->reorderableWithButtons(),
                    ])
                    ->afterStateHydrated(function ($component, $state) {
                        if (empty($state)) {
                            $component->state(self::getNavigationTreeForForm());
                        }
                    })
                    ->addActionLabel('添加主菜单项')
                    ->reorderableWithButtons()
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
            ]);
    }

    public static function getPermissionOptions(): array
    {
        return [
            null => '所有人可见 (默认)',
            'users' => '普通用户 (users)',
            'staffmem' => '管理组成员 (staffmem)',
            'admin' => '站点管理员 (admin)',
            'topten' => 'Top 10 访问者 (topten)',
            'offers' => '候补区访问者 (offers)',
            'requests' => '求种区访问者 (requests)',
            'log' => '日志查看者 (log)',
        ];
    }

    public static function saveNavigations(array $navData): void
    {
        error_log("Navigations: Attempting to get PDO connection...");
        $pdo = DB::connection()->getPdo();
        error_log("Navigations: PDO connection obtained.");

        try {
            error_log("Navigations: Beginning transaction...");
            $pdo->beginTransaction();
            error_log("Navigations: Transaction began.");
            
            error_log("Navigations: Executing DELETE statement...");
            $pdo->exec("DELETE FROM " . self::$tableName);
            error_log("Navigations: DELETE statement executed.");

            if (empty($navData)) {
                error_log("Navigations: No new data, committing transaction...");
                $pdo->commit();
                error_log("Navigations: Transaction committed for empty data.");
                return;
            }

            error_log("Navigations: Starting recursive insert...");
            self::insertNavItemsRecursively($navData, 0);
            error_log("Navigations: Recursive insert finished.");
            
            error_log("Navigations: Committing final transaction...");
            $pdo->commit();
            error_log("Navigations: Final transaction committed successfully.");

        } catch (\Throwable $e) {
            error_log("Navigations: An error occurred, attempting to rollback transaction...");
            $pdo->rollBack();
            error_log("Navigations: Transaction rolled back.");
            error_log('NavigationManager save error: ' . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
            throw $e;
        }
    }

    private static function insertNavItemsRecursively(array $items, int $parentId): void
    {
        foreach (array_values($items) as $sortIndex => $item) {
            if (empty($item['name'])) {
                error_log("Navigations: Skipping item with empty name: " . json_encode($item));
                continue;
            }

            $sortOrder = ($sortIndex + 1) * 10;
            
            $name = "'" . addslashes($item['name']) . "'";
            $url = "'" . addslashes($item['url'] ?? '') . "'";
            $permission = !empty($item['permission']) ? "'" . addslashes($item['permission']) . "'" : "NULL";
            $new_tab = (int) ($item['new_tab'] ?? 0);
            $is_external = (int) ($item['is_external'] ?? 0);

            $sql = "INSERT INTO " . self::$tableName . " (name, url, permission, new_tab, sort_order, parent_id, created_at, updated_at, is_external) 
                    VALUES ($name, $url, $permission, $new_tab, $sortOrder, $parentId, NOW(), NOW(), $is_external)";
            
            \Nexus\Database\NexusDB::statement($sql);

            $newParentIdResult = \Nexus\Database\NexusDB::select("SELECT LAST_INSERT_ID() as id");
            if (empty($newParentIdResult) || !isset($newParentIdResult[0]['id'])) {
                throw new \Exception('Failed to retrieve LAST_INSERT_ID() after insert.');
            }
            $newParentId = $newParentIdResult[0]['id'];

            if (!empty($item['children']) && is_array($item['children'])) {
                self::insertNavItemsRecursively($item['children'], $newParentId);
            }
        }
    }

    protected static function getNavigationTreeForForm(): array
    {
        try {
            $items = NexusDB::select("SELECT * FROM " . self::$tableName . " ORDER BY parent_id ASC, sort_order ASC");
            $tree = [];
            $children = [];
            foreach ($items as $item) {
                if ($item['parent_id']) {
                    $children[$item['parent_id']][] = $item;
                }
            }
            foreach ($items as $item) {
                if (!$item['parent_id']) {
                    $item['children'] = $children[$item['id']] ?? [];
                    $tree[] = $item;
                }
            }
            return $tree;
        } catch (\Exception $e) {
            error_log('NavigationManager get tree error: ' . $e->getMessage());
            return [];
        }
    }

    public static function renderMenu($menuHtml)
    {
        try {
            $items = NexusDB::select("SELECT * FROM " . self::$tableName . " ORDER BY parent_id ASC, sort_order ASC");
            if (empty($items)) {
                return $menuHtml; // 如果没有自定义导航，返回原始菜单
            }
            return self::buildMenuHtml($items);
        } catch (\Exception $e) {
            error_log('NavigationManager Error: ' . $e->getMessage());
            return $menuHtml; // 出错时返回原始菜单
        }
    }

    private static function buildMenuHtml(array $items)
    {
        $html = '<ul id="mainmenu" class="menu">';
        $children = [];

        foreach ($items as $item) {
            if (!empty($item['permission']) && !user_can($item['permission'])) {
                continue;
            }
            if ($item['parent_id']) {
                $children[$item['parent_id']][] = $item;
            }
        }

        foreach ($items as $item) {
            if ($item['parent_id'] == 0) {
                if (!empty($item['permission']) && !user_can($item['permission'])) {
                    continue;
                }
                $hasChildren = isset($children[$item['id']]);
                $link = self::generateLink($item);

                // Add 'has-children' class to parent li
                $liClass = self::getSelectedClass($item);
                if ($hasChildren) {
                    if (strpos($liClass, 'class=') !== false) {
                        $liClass = substr_replace($liClass, ' has-children', -1, 0);
                    } else {
                        $liClass = ' class="has-children"';
                    }
                }

                $html .= '<li' . $liClass . '>';

                if ($hasChildren) {
                    if (!empty($item['url'])) {
                        // 有子菜单且有链接，允许点击
                        $html .= '<a href="' . self::generateLink($item) . '"' . ($item['new_tab'] ? ' target="_blank"' : '') . '>' . htmlspecialchars($item['name']) . '</a>';
                    } else {
                        // 有子菜单且无链接，不可点击
                        $html .= '<a href="javascript:void(0);" style="cursor: default;" rel="sub-menu">' . htmlspecialchars($item['name']) . '</a>';
                    }
                } else {
                    // 无子菜单，常规链接
                    $html .= '<a href="' . self::generateLink($item) . '"' . ($item['new_tab'] ? ' target="_blank"' : '') . '>' . htmlspecialchars($item['name']) . '</a>';
                }

                if ($hasChildren) {
                    // Add 'sub-menu' class to child ul
                    $html .= '<ul class="sub-menu">';
                    foreach ($children[$item['id']] as $child) {
                        $childLink = self::generateLink($child);
                        $html .= '<li><a href="' . $childLink . '"' . ($child['new_tab'] ? ' target="_blank"' : '') . '>' . htmlspecialchars($child['name']) . '</a></li>';
                    }
                    $html .= '</ul>';
                }
                $html .= '</li>';
            }
        }

        $html .= '</ul>';
        return $html;
    }

    private static function generateLink($item)
    {
        if (!empty($item['is_external'])) {
            return $item['url'];
        }
        // 内链：相对路径或拼接域名
        return htmlspecialchars($item['url']);
    }
    
    private static function getSelectedClass($item)
    {
        $script_name = basename($_SERVER['SCRIPT_NAME'] ?? '');
        // Ensure the URL is a string before parsing to avoid deprecation warning.
        $path_component = parse_url((string)($item['url'] ?? ''), PHP_URL_PATH);
        $url_path = basename((string)($path_component ?? ''));

        if (!empty($url_path) && $script_name == $url_path && $url_path != 'index.php') {
            return ' class="selected"';
        }
        
        if (($item['url'] ?? '') == 'index.php' && $script_name == 'index.php') {
            return ' class="selected"';
        }
        
        return '';
    }
} 