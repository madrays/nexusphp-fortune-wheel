<?php

namespace NexusPlugin\FortuneWheel;

use Nexus\Plugin\BasePlugin;

class FortuneWheelRepository extends BasePlugin
{
    /**
     * 获取魔力别名
     */
    protected function getBonusName(): string
    {
        return get_setting('fortune_wheel.bonus_name') ?: '魔力值';
    }

    /**
     * 获取魔力单位别名
     */
    protected function getBonusUnit(): string
    {
        return get_setting('fortune_wheel.bonus_unit') ?: '魔力';
    }
    /**
     * The public key for license verification.
     * This key is safe to be public and should be embedded here.
     *
     * @var string
     */
    private $publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAyFvNaFUht+TPAsIMquIb
moiQzP/+cv2DvpfJxcEmlQm9eJ3iahiGAV5e5dytWo7S/2VfezB5lDGnY6PxaNzz
TC5eCkymUC5F4QodK4WklgdXCFM6CxTquf1gmUP02ez6S4Zd4olhUb3I6ecZEx7o
+T96axq2hySa6nKjwq7/JwNNcVa79ULw/1RMLI36o+UKQsKT5foiZzJ8fjhdih9W
nr0pS6oozeyTip4p+eZ9SA9tR13gpsGvRTJ+ATUoYWHcVaGFZ56i9qXBGIAt0sKH
yVp5SbwYV7wd1VivznconUe0nEcx65fv9fWPa/lvWVKuqHdfFPlbZxA+Ylj5j9/W
Wk5lK8uvz4egIzuel0jcuphaYq5CbDgCsXRZACB83I5XvqK/T1JS//fCP78pjV1u
IitS42u3h0o5gqX6jsNcUIrB/vCT8YYSJSa1QwPFqKP+yMIrrhUluEPwpWQz11OK
EW7eaKG9h2E+ClehGi7kJv+WP06DOvNE55qx/3nLVwIP9+HoNCPl897MBmwlILlJ
    7SB9jzm/kxAlvo7vR4IpzzLHEwllTOcMC9AA62xNDuzJ5AMi6TiaEgW9+2wItA1R
MWtjrkAktOF8v6TUDATWy9v9qcSvGxq0ukUqk3H9sjOy/LP/iAWI8BU2x9ucKnfF
l/MyOlSvFXudTeDATZA4rMECAwEAAQ==
-----END PUBLIC KEY-----
EOD;


    public function __construct()
    {
        // 延迟初始化，避免在安装阶段触发验证
    }

    /**
     * Initialize system components and validate environment
     */
    private function initializeSystem()
    {
        try {
            // Initialize security subsystem
            if (file_exists(__DIR__ . '/SecurityHelper.php')) {
                require_once __DIR__ . '/SecurityHelper.php';
                if (!SecurityHelper::initialize()) {
                    $this->handleSystemError('系统安全模块初始化失败。');
                }
            }

            // System environment checks
            if (!$this->checkSystemIntegrity()) {
                $this->handleSystemError('系统环境异常，请检查服务器配置。');
            }
        } catch (\Throwable $e) {
            // 在安装过程中，忽略初始化错误
            if (php_sapi_name() === 'cli') {
                return;
            }
            $this->handleSystemError('系统初始化失败：' . $e->getMessage());
        }
    }

    /**
     * Perform comprehensive system integrity verification
     */
    private function checkSystemIntegrity(): bool
    {
        // Check basic system files
        $requiredFiles = [__DIR__ . '/license.dat'];
        foreach ($requiredFiles as $file) {
            if (!$this->validateSystemFile($file)) {
                return false;
            }
        }

        // Validate system environment
        return $this->performEnvironmentCheck();
    }

    /**
     * Validate critical system file
     */
    private function validateSystemFile($filePath): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $content = file_get_contents($filePath);
        $data = json_decode($content, true);
        
        if (!$data || !isset($data['machine_code']) || !isset($data['signature']) || !isset($data['allowed_domains'])) {
            return false;
        }

        return $this->verifySystemData($data);
    }

    /**
     * Verify system data integrity
     */
    private function verifySystemData($data): bool
    {
        $machineCode = $data['machine_code'];
        $domains = $data['allowed_domains'];
        $signature = base64_decode($data['signature']);

        // Machine verification
        if (!$this->validateMachineEnvironment($machineCode)) {
            return false;
        }

        // Domain verification
        if (!$this->validateDomainEnvironment($domains)) {
            return false;
        }

        // Signature verification
        return $this->validateDataSignature($machineCode, $domains, $signature);
    }

    /**
     * Validate machine environment
     */
    private function validateMachineEnvironment($expectedCode): bool
    {
        $currentCode = $this->getCurrentMachineCode();
        return hash_equals($expectedCode, $currentCode);
    }

    /**
     * Validate domain environment
     */
    private function validateDomainEnvironment($allowedDomains): bool
    {
        // Skip domain validation during installation/CLI
        if (php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_HOST'])) {
            return true;
        }

        $currentDomain = $_SERVER['HTTP_HOST'];
        return in_array($currentDomain, $allowedDomains);
    }

    /**
     * Validate data signature
     */
    private function validateDataSignature($machineCode, $domains, $signature): bool
    {
        sort($domains);
        $dataToVerify = $machineCode . '|' . implode(',', $domains);
        
        $publicKeyResource = openssl_pkey_get_public($this->publicKey);
        $isValid = openssl_verify($dataToVerify, $signature, $publicKeyResource, OPENSSL_ALGO_SHA256);
        // openssl_free_key() is deprecated in PHP 8.0+, resources are freed automatically

        return $isValid === 1;
    }

    /**
     * Perform additional environment checks
     */
    private function performEnvironmentCheck(): bool
    {
        // Fake check 1: Always returns true, but confuses reverse engineers
        if (!function_exists('json_decode')) {
            return false;
        }

        // Fake check 2: Another distraction
        if (version_compare(PHP_VERSION, '7.0.0', '<')) {
            return false;
        }

        return true;
    }

    /**
     * Validate operation permissions
     */
    private function validateOperationPermissions(): bool
    {
        // This is actually a disguised license check
        static $validated = null;
        
        if ($validated === null) {
            // 确保系统已初始化
            $this->ensureSystemInitialized();
            
            require_once __DIR__ . '/SystemValidator.php';
            $validator = SystemValidator::getInstance();
            $validated = $validator->validateSystemRequirements();
        }
        
        return $validated;
    }

    /**
     * Ensure system is properly initialized
     */
    private function ensureSystemInitialized()
    {
        static $initialized = false;
        
        if (!$initialized) {
            $this->initializeSystem();
            $initialized = true;
        }
    }

    /**
     * Validate runtime environment 
     */
    private function validateRuntimeEnvironment(): bool
    {
        // Another disguised validation point
        static $runtimeValid = null;
        
        if ($runtimeValid === null) {
            // 确保系统已初始化
            $this->ensureSystemInitialized();
            
            require_once __DIR__ . '/SecurityHelper.php';
            $runtimeValid = SecurityHelper::isSecure() && $this->checkSystemIntegrity();
        }
        
        return $runtimeValid;
    }

    /**
     * Get default settings when validation fails
     */
    private function getDefaultSettings(): array
    {
        // Return minimal settings that disable functionality
        return [
            'enabled' => false,
            'daily_max_spins' => 0,
            'free_spins' => 0,
            'spin_interval' => 86400,
            'cost_bonus' => 999999,
            'navigation_enabled' => false,
        ];
    }

    /**
     * Generates the current machine's fingerprint and returns its SHA256 hash.
     * This logic MUST EXACTLY MATCH the logic in `get_machine_code.php`.
     *
     * @return string The current machine's code.
     */
    private function getCurrentMachineCode(): string
    {
        // Suppress errors for functions that might be disabled in php.ini
        @error_reporting(0);

        $fingerprintData = [
            PHP_OS,
            php_uname('s'),
            php_uname('m'),
            zend_version(),
            PHP_ZTS ? 'ZTS' : 'NTS',
            disk_total_space('/') ? round(disk_total_space('/') / (1024 * 1024 * 1024)) : 'N/A'
        ];

        return hash('sha256', json_encode($fingerprintData));
    }

    /**
     * Handle system configuration errors
     *
     * @param string $message The error message to display.
     */
    private function handleSystemError(string $message)
    {
        $errorHtml = sprintf(
            '<div style="border:2px solid #F00; background-color:#FFEEEE; padding:20px; margin:20px; font-family:sans-serif;">' .
            '<h3 style="color:#F00;">幸运大转盘Pro - 系统错误</h3>' .
            '<p>%s</p>' .
            '<p>请联系插件作者 (madrays) 并提供错误信息以解决此问题。</p>' .
            '</div>',
            htmlspecialchars($message, ENT_QUOTES, 'UTF-8')
        );
        die($errorHtml);
    }
    
    /**
     * 插件ID
     */
    const ID = 'fortune-wheel';
    
    /**
     * 插件版本
     */
    const VERSION = '1.0.0';
    
    /**
     * 兼容的NexusPHP版本
     */
    const COMPATIBLE_NP_VERSION = '1.7.21';

    /**
     * 插件安装时执行
     */
    public function install(): void
    {
        $this->runMigrations(__DIR__ . '/../database/migrations');
        $this->copyPublicFiles();
        do_log("Fortune Wheel Plugin installed successfully!");
    }

    /**
     * 插件启动时执行
     */
    public function boot(): void
    {
        $this->registerHooks();
            do_log("Fortune Wheel Plugin booted!");
    }

    /**
     * 插件卸载时执行
     */
    public function uninstall(): void
    {
        $this->runMigrations(__DIR__ . '/../database/migrations', true);
        $this->cleanupSettings();
            do_log("Fortune Wheel Plugin uninstalled successfully!");
    }

    /**
     * 注册Hook
     */
    protected function registerHooks(): void
    {
        global $hook;
        $settings = $this->getSettings();

        if (!empty($settings['navigation_enabled'])) {
            // 新的导航逻辑
            $hook->addFilter('nexus_menu', [NavigationManager::class, 'renderMenu'], 20, 1);
            $hook->addAction('nexus_header', [$this, 'injectMenuStyles'], 10, 0);
        } else {
            // 原始的、只添加一个链接的逻辑
            $hook->addAction('nexus_header', [$this, 'addFortuneWheelLink'], 10, 0);
        }

        $hook->addAction('nexus_footer', [$this, 'addWinAnnouncement'], 10, 0);
        $hook->addFilter('nexus_setting_tabs', [$this, 'addSettingTab'], 10, 1);
        $hook->addAction('nexus_setting_update', [$this, 'handleSettingUpdate'], 10, 0);
    }

    /**
     * [原始逻辑] 添加幸运转盘链接到菜单末尾
     */
    public function addFortuneWheelLink(): void
    {
        $settings = $this->getSettings();
        if (!($settings['enabled'] ?? false)) {
                return;
            }

        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var mainMenu = document.querySelector("#mainmenu");
            if (mainMenu) {
                var fortuneLi = document.createElement("li");
                var fortuneLink = document.createElement("a");
                fortuneLink.href = "fortune-wheel.php";
                fortuneLink.innerHTML = "&nbsp;幸运转盘&nbsp;";
                fortuneLi.appendChild(fortuneLink);
                mainMenu.appendChild(fortuneLi);
            }
        });
        </script>';
    }

    /**
     * 为导航菜单注入CSS样式
     */
    public function injectMenuStyles(): void
    {
        $css = '
<style type="text/css">
/* 只提供下拉菜单功能所需的核心CSS，外观样式完全继承自主题 */
#mainmenu li.has-children {
    position: relative; /* 为子菜单提供定位上下文 */
}
#mainmenu ul.sub-menu {
    display: none; /* 默认隐藏 */
    position: absolute; /* 绝对定位，脱离文档流 */
    /* top: 100%;  移除此项，让浏览器自动定位 */
    left: 0;
    z-index: 1000; /* 确保在最上层显示 */
    list-style: none; /* 移除列表默认样式 */
    margin: 0 !important; /* 强制清除可能由主题带来的外边距 */
    padding: 0 !important; /* 强制清除可能由主题带来的内边距 */
    width: max-content; /* 宽度由最宽的子元素决定 */
    flex-direction: column;

    /* 移除所有背景、边框、阴影等外观样式，使其完全透明 */
    background: transparent;
    border: none;
    box-shadow: none;
}
#mainmenu li.has-children:hover > ul.sub-menu {
    display: flex; /* 鼠标悬停时以Flexbox形态显示 */
}
#mainmenu ul.sub-menu li {
    /* 强制清除可能由主题带来的外边距，确保紧凑 */
    margin: 0 !important;
}
#mainmenu ul.sub-menu li a {
    /* 强制子菜单项的链接为块级元素，使其填满父容器 li，从而实现等宽 */
    display: block;
    box-sizing: border-box;
}
</style>
';
        echo $css;
    }

    /**
     * 添加中奖公示
     */
    public function addWinAnnouncement(): void
    {
        $settings = $this->getSettings();
        if (!($settings['enabled'] ?? false) || !($settings['show_recent_wins'] ?? false)) {
            return;
        }

        $currentScript = $_SERVER['SCRIPT_NAME'] ?? '';
        if (!str_contains($currentScript, 'index.php') && $currentScript !== '/') {
            return;
        }

        $limit = (int)($settings['recent_wins_count'] ?? 10);
        $recentWins = $this->getRecentWinRecords($limit);

        echo $this->renderWinAnnouncement($recentWins);
    }

    /**
     * 添加设置标签页
     */
    public function addSettingTab(array $tabs): array
    {
        try {
            $tabs[] = \NexusPlugin\FortuneWheel\SettingsManager::getSettingTab();
            $tabs[] = \NexusPlugin\FortuneWheel\SettingsManager::getPrizeTab();
            $tabs[] = \NexusPlugin\FortuneWheel\NavigationManager::getNavigationTab();
            return $tabs;
        } catch (\Exception $e) {
            error_log("Fortune Wheel: addSettingTab() failed: " . $e->getMessage());
            return $tabs;
        }
    }

    /**
     * 处理设置更新
     */
    public function handleSettingUpdate(): void
    {
        $this->handlePrizeSettingsUpdate();
        $this->handleNavigationSettingsUpdate();
    }

    private function handlePrizeSettingsUpdate(): void
    {
        error_log("Fortune Wheel: handleSettingUpdate() hook fired. Reconstructing prize data from settings table.");
        try {
            $sql = "SELECT name, value FROM settings WHERE name LIKE 'fortune_wheel_prizes.%'";
            $tempPrizes = \Nexus\Database\NexusDB::select($sql);
            $prizesData = [];

            if (empty($tempPrizes)) {
                $singleKeySql = "SELECT value FROM settings WHERE name = 'fortune_wheel_prizes'";
                $singleKeyResult = \Nexus\Database\NexusDB::select($singleKeySql);

                if (!empty($singleKeyResult)) {
                    $decodedValue = json_decode($singleKeyResult[0]['value'], true);
                    if (is_array($decodedValue)) {
                        $prizesData = $decodedValue;
                    }
                }
                // If we are here, it means either no prize settings were found,
                // or they were empty. In both cases, we proceed, as savePrizes
                // will correctly handle an empty array by clearing the table.
                    } else {
                foreach ($tempPrizes as $row) {
                    $index = (int) substr($row['name'], strrpos($row['name'], '.') + 1);
                    $prizeItem = json_decode($row['value'], true);
                    if (is_array($prizeItem)) {
                        $prizesData[$index] = $prizeItem;
                    }
                }
                ksort($prizesData);
            }

            // Always run savePrizes, even with an empty array, to ensure cleanup.
            if (!SettingsManager::savePrizes($prizesData)) {
                error_log("Fortune Wheel: SettingsManager::savePrizes failed to save the data.");
            }

            $cleanupSql = "DELETE FROM settings WHERE name LIKE 'fortune_wheel_prizes.%' OR name = 'fortune_wheel_prizes'";
            \Nexus\Database\NexusDB::statement($cleanupSql);
            error_log("Fortune Wheel: Prize settings processed successfully.");
        } catch (\Throwable $e) {
            error_log("Fortune Wheel: An error occurred in handlePrizeSettingsUpdate: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        }
    }

    private function handleNavigationSettingsUpdate(): void
    {
        error_log("Fortune Wheel: Checking for navigation settings update...");
        try {
            $navData = [];
            $tempNavs = \Nexus\Database\NexusDB::select("SELECT name, value FROM settings WHERE name LIKE 'navigations.%'");

            if (empty($tempNavs)) {
                $singleKeyResult = \Nexus\Database\NexusDB::select("SELECT value FROM settings WHERE name = 'navigations'");

                if (!empty($singleKeyResult)) {
                    $decodedValue = json_decode($singleKeyResult[0]['value'], true);
                    if (is_array($decodedValue)) {
                        // The data from a repeater is often an associative array with repeater item IDs as keys.
                        // We need the values in the order they were submitted.
                        $navData = array_values($decodedValue);
                        error_log("Fortune Wheel: Found navigation data under single key 'navigations'.");
            }
        } else {
                    error_log("Fortune Wheel: No navigation settings data found in settings table. Skipping update.");
            return;
        }
        } else {
                error_log("Fortune Wheel: Found navigation data under multiple keys 'navigations.%'. Reconstructing...");
                $reconstructedData = [];
                foreach ($tempNavs as $row) {
                    $key = $row['name'];
                    $lastDotPosition = strrpos($key, '.');
                    if ($lastDotPosition !== false) {
                        $index = substr($key, $lastDotPosition + 1);
                        $navItem = json_decode($row['value'], true);
                        if (is_array($navItem)) {
                            $reconstructedData[$index] = $navItem;
                        }
                    }
                }
                ksort($reconstructedData);
                $navData = array_values($reconstructedData);
            }

            // At this point, navData should be a clean, numerically indexed array of navigation items.
            // It can be empty if the user cleared all items, which is a valid state.
            // We only return early if no setting key was found at all.

            error_log("Fortune Wheel: Processing navigation data save. Items count: " . count($navData));
            NavigationManager::saveNavigations($navData);

            $cleanupSql = "DELETE FROM settings WHERE name LIKE 'navigations.%' OR name = 'navigations'";
            \Nexus\Database\NexusDB::statement($cleanupSql);
            error_log("Fortune Wheel: Navigation settings processed and cleaned up successfully.");

        } catch (\Throwable $e) {
            error_log("Fortune Wheel: A critical error occurred in handleNavigationSettingsUpdate: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        }
    }

    /**
     * 清理设置
     */
    protected function cleanupSettings(): void
    {
        $sql = "DELETE FROM settings WHERE name LIKE 'fortune_wheel.%'";
        \Nexus\Database\NexusDB::statement($sql);
    }

    /**
     * 获取插件设置
     */
    public function getSettings(): array
    {
        // Runtime system check
        if (!$this->validateRuntimeEnvironment()) {
            return $this->getDefaultSettings();
        }

        try {
            $sql = "SELECT name, value FROM settings WHERE name LIKE 'fortune_wheel.%'";
            $results = \Nexus\Database\NexusDB::select($sql);

            // 明确定义哪些设置是布尔值
            $booleanSettings = [
                'enabled', 
                'show_recent_wins', 
                'navigation_enabled',
                'vip_stack_time',
                'rainbow_id_stack_time'
            ];

            $settings = [];
            foreach ($results as $result) {
                $key = str_replace('fortune_wheel.', '', $result['name']);
                $value = $result['value'];

                // 对于明确的布尔设置，进行强制转换
                if (in_array($key, $booleanSettings)) {
                    $value = ($value === '1' || $value === 1 || $value === 'true' || $value === true);
                } elseif (is_numeric($value)) {
                    $value = strpos($value, '.') !== false ? (float)$value : (int)$value;
                }
                $settings[$key] = $value;
            }
        } catch (\Exception $e) {
            $settings = [];
        }

        $defaults = [
            'enabled' => true,
            'daily_free_spins' => 3,
            'daily_max_spins' => 10000,
            'bonus_cost_per_spin' => 1000,
            'spin_interval' => 10,
            'show_recent_wins' => true,
            'recent_wins_count' => 10,
            'vip_duplicate_bonus' => 500,
            'medal_duplicate_bonus' => 200,
            'rainbow_id_duplicate_bonus' => 300,
            'rename_card_duplicate_bonus' => 200,
            'vip_stack_time' => true,
            'rainbow_id_stack_time' => true,
            'navigation_enabled' => false,
            'bonus_name' => '魔力值',
            'bonus_unit' => '魔力',
        ];

        return array_merge($defaults, $settings);
    }

    /**
     * 获取所有启用的奖品
     */
    public function getEnabledPrizes(): array
    {
        $sql = "SELECT * FROM fortune_wheel_prizes WHERE enabled = 1 AND (stock = -1 OR stock > 0) ORDER BY sort_order ASC";
        return \Nexus\Database\NexusDB::select($sql);
    }

    /**
     * 抽奖算法
     */
    protected function drawPrize(array $prizes): ?array
    {
        $totalProbability = array_sum(array_column($prizes, 'probability'));
        if ($totalProbability <= 0) {
            return null; 
        }
        $random = mt_rand(1, (int)($totalProbability * 100)) / 100;

        $currentProbability = 0;
        foreach ($prizes as $prize) {
            $currentProbability += $prize['probability'];
            if ($random <= $currentProbability) {
                return $prize;
            }
        }

        return end($prizes) ?: null;
    }

    /**
     * 发放奖品
     */
    protected function awardPrize(int $userId, array $prize, array $settings): array
    {
        switch ($prize['type']) {
            case 'bonus':
                return $this->awardBonus($userId, $prize['value']);
            case 'upload':
                return $this->awardUpload($userId, $prize['value']);
            case 'vip_days':
                return $this->awardVipDays($userId, $prize['value'], $settings);
            case 'medal':
                return $this->awardMedal($userId, $prize['value'], $settings);
            case 'rainbow_id_days':
                return $this->awardRainbowIdDays($userId, $prize['value'], $settings);
            case 'invite_temp':
                return $this->awardTempInvitation($userId, $prize['value']);
            case 'invite_perm':
                return $this->awardInvitation($userId, $prize['value']);
            case 'rename_card':
                return $this->awardRenameCard($userId, $prize['value'], $settings);
            case 'attendance_card':
                return $this->awardAttendanceCard($userId, $prize['value'], $settings);
            default:
                return ['status' => 'nothing'];
        }
    }

    /**
     * 发放补签卡
     */
    protected function awardAttendanceCard(int $userId, int $amount, array $settings): array
    {
        try {
            \App\Models\User::query()->where('id', $userId)->increment('attendance_card', $amount);
            do_log("Fortune Wheel: Awarded $amount attendance card(s) to user $userId.");
            return ['status' => 'awarded', 'type' => 'attendance_card', 'value' => $amount, 'unit' => '张补签卡'];
        } catch (\Exception $e) {
            do_log("Fortune Wheel: Failed to award attendance card to user $userId. Error: " . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * 发放魔力值奖品
     */
    protected function awardBonus(int $userId, int $amount): array
    {
        $sql = "UPDATE users SET seedbonus = seedbonus + $amount WHERE id = $userId";
        \Nexus\Database\NexusDB::statement($sql);
        do_log("Fortune Wheel: Awarded $amount bonus to user $userId");
        return ['status' => 'awarded', 'type' => 'bonus', 'value' => $amount, 'unit' => $this->getBonusName()];
    }

    /**
     * 发放上传量奖品
     */
    protected function awardUpload(int $userId, int $amount): array
    {
        $sql = "UPDATE users SET uploaded = uploaded + $amount WHERE id = $userId";
        \Nexus\Database\NexusDB::statement($sql);
        do_log("Fortune Wheel: Awarded $amount upload bytes to user $userId");
        $gb = round($amount / (1024 * 1024 * 1024), 2);
        return ['status' => 'awarded', 'type' => 'upload', 'value' => $gb, 'unit' => 'GB上传量'];
    }

    /**
     * 发放VIP奖品
     */
    protected function awardVipDays(int $userId, int $days, array $settings): array
    {
        $user = \App\Models\User::query()->findOrFail($userId);

        if ($user->class > \App\Models\User::CLASS_VIP) {
            $bonus = (int)($settings['vip_duplicate_bonus'] ?? 0);
            if ($bonus > 0) {
                $this->awardBonus($userId, $bonus);
                return ['status' => 'compensated_high_class', 'type' => 'vip', 'value' => $bonus, 'unit' => '魔力值'];
            } else {
                return ['status' => 'already_owned_high_class', 'type' => 'vip'];
        }
        }
        else if ($user->class == \App\Models\User::CLASS_VIP) {
            // 检查是否为永久VIP（vip_until为空、null或'0000-00-00 00:00:00'表示永久）
            $isPermanentVip = !$user->vip_until || $user->vip_until == '0000-00-00 00:00:00' || $user->vip_until == null || $user->vip_until == '';
            
            if ($isPermanentVip) {
                // 永久VIP按重复奖励处理
                $bonus = (int)($settings['vip_duplicate_bonus'] ?? 0);
                if ($bonus > 0) {
                    $this->awardBonus($userId, $bonus);
                    return ['status' => 'compensated', 'type' => 'vip', 'value' => $bonus, 'unit' => $this->getBonusName()];
                } else {
                    return ['status' => 'already_owned', 'type' => 'vip'];
                }
            } else if ($settings['vip_stack_time'] ?? true) {
                // 时限VIP可以叠加时间
                $newExpireDate = $user->vip_until->isFuture() 
                    ? $user->vip_until->addDays($days)
                    : now()->addDays($days);
                
                $user->update(['vip_until' => $newExpireDate]);
                return ['status' => 'extended', 'type' => 'vip', 'value' => $days, 'unit' => '天'];
            } else {
                $bonus = (int)($settings['vip_duplicate_bonus'] ?? 0);
                if ($bonus > 0) {
                    $this->awardBonus($userId, $bonus);
                    return ['status' => 'compensated', 'type' => 'vip', 'value' => $bonus, 'unit' => $this->getBonusName()];
                } else {
                    return ['status' => 'already_owned', 'type' => 'vip'];
                }
            }
        } 
        else {
            $expireDate = now()->addDays($days);
            $user->update(['class' => \App\Models\User::CLASS_VIP, 'vip_added' => 'yes', 'vip_until' => $expireDate]);
            return ['status' => 'awarded', 'type' => 'vip', 'value' => $days, 'unit' => '天'];
        }
        }

    /**
     * 发放邀请名额奖品
     */
    protected function awardInvitation(int $userId, int $amount): array
    {
        \App\Models\User::query()->where('id', $userId)->increment('invites', $amount);
        return ['status' => 'awarded', 'type' => 'invite_perm', 'value' => $amount, 'unit' => '个永久邀请'];
    }

    protected function awardMedal(int $userId, int $medalId, array $settings): array
    {
        $user = \App\Models\User::query()->findOrFail($userId);
        $medal = \App\Models\Medal::query()->findOrFail($medalId);

        if ($user->valid_medals()->where('medal_id', $medalId)->exists()) {
            $bonus = (int)($settings['medal_duplicate_bonus'] ?? 0);
            if ($bonus > 0) {
                $this->awardBonus($userId, $bonus);
                return ['status' => 'compensated', 'type' => 'medal', 'value' => $bonus, 'unit' => $this->getBonusName(), 'medal_name' => $medal->name];
            } else {
                return ['status' => 'already_owned', 'type' => 'medal', 'medal_name' => $medal->name];
    }
        } else {
            $expireAt = $medal->duration > 0 ? now()->addDays($medal->duration) : null;
            $user->medals()->attach([$medalId => ['expire_at' => $expireAt, 'status' => \App\Models\UserMedal::STATUS_NOT_WEARING]]);
            return ['status' => 'awarded', 'type' => 'medal', 'value' => $medal->name, 'unit' => ''];
    }
    }
    
    protected function awardTempInvitation(int $userId, int $amount): array
    {
        try {
            $user = \App\Models\User::query()->findOrFail($userId);
            $toolRep = new \App\Repositories\ToolRepository();
            $hashArr = $toolRep->generateUniqueInviteHash([], $amount, $amount);
            
            $invites = [];
            foreach ($hashArr as $hash) {
                $invites[] = [
                    'inviter' => $user->id,
                    'invitee' => '',
                    'hash' => $hash,
                    'valid' => 0,
                    'expired_at' => \Carbon\Carbon::now()->addDays(\App\Models\Invite::TEMPORARY_INVITE_VALID_DAYS),
                    'created_at' => \Carbon\Carbon::now(),
                ];
            }
            \App\Models\Invite::query()->insert($invites);

            return ['status' => 'awarded', 'type' => 'invite_temp', 'value' => $amount, 'unit' => '个临时邀请'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
    }
    }

    protected function awardRainbowIdDays(int $userId, int $days, array $settings): array
    {
        try {
            $user = \App\Models\User::query()->findOrFail($userId);
            $meta = $user->metas()->where('meta_key', \App\Models\UserMeta::META_KEY_PERSONALIZED_USERNAME)->first();

            if ($meta) {
                // 检查是否为永久彩虹ID（deadline为空、null或'0000-00-00 00:00:00'表示永久）
                $isPermanentRainbowId = !$meta->deadline || $meta->deadline == '0000-00-00 00:00:00' || $meta->deadline == null || $meta->deadline == '';
                
                if ($isPermanentRainbowId) {
                    // 永久彩虹ID按重复奖励处理
                    $bonus = (int)($settings['rainbow_id_duplicate_bonus'] ?? 0);
                    if ($bonus > 0) {
                        $this->awardBonus($userId, $bonus);
                        return ['status' => 'compensated', 'type' => 'rainbow_id', 'value' => $bonus, 'unit' => $this->getBonusName()];
                    } else {
                        return ['status' => 'already_owned', 'type' => 'rainbow_id'];
                    }
                } else if ($settings['rainbow_id_stack_time'] ?? true) {
                    // 时限彩虹ID可以叠加时间
                    $newDeadline = $meta->deadline->isFuture() 
                        ? $meta->deadline->addDays($days)
                        : now()->addDays($days);
                    $meta->update(['deadline' => $newDeadline]);
                    return ['status' => 'extended', 'type' => 'rainbow_id', 'value' => $days, 'unit' => '天'];
                } else {
                    $bonus = (int)($settings['rainbow_id_duplicate_bonus'] ?? 0);
                    if ($bonus > 0) {
                        $this->awardBonus($userId, $bonus);
                        return ['status' => 'compensated', 'type' => 'rainbow_id', 'value' => $bonus, 'unit' => $this->getBonusName()];
                    } else {
                        return ['status' => 'already_owned', 'type' => 'rainbow_id'];
                    }
                }
        } else {
                $userRep = new \App\Repositories\UserRepository();
                $metaData = [
                    'meta_key' => \App\Models\UserMeta::META_KEY_PERSONALIZED_USERNAME,
                    'duration' => $days,
                ];
                $userRep->addMeta($user, $metaData, $metaData, false);
                return ['status' => 'awarded', 'type' => 'rainbow_id', 'value' => $days, 'unit' => '天'];
            }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
    }
    }

    protected function awardRenameCard(int $userId, int $amount, array $settings): array
    {
       try {
            $user = \App\Models\User::query()->findOrFail($userId);
            if (\App\Models\UserMeta::query()->where('uid', $userId)->where('meta_key', \App\Models\UserMeta::META_KEY_CHANGE_USERNAME)->exists()) {
                $bonus = (int)($settings['rename_card_duplicate_bonus'] ?? 0);
                if ($bonus > 0) {
                    $this->awardBonus($userId, $bonus);
                    return ['status' => 'compensated', 'type' => 'rename_card', 'value' => $bonus, 'unit' => $this->getBonusName()];
                } else {
                    return ['status' => 'already_owned', 'type' => 'rename_card'];
    }
            } else {
                $userRep = new \App\Repositories\UserRepository();
                $metaData = [
                    'meta_key' => \App\Models\UserMeta::META_KEY_CHANGE_USERNAME,
                ];
                $userRep->addMeta($user, $metaData, $metaData, false);
                return ['status' => 'awarded', 'type' => 'rename_card', 'value' => 1, 'unit' => '张改名卡'];
    }
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * 根据排序动态生成奖品等级
     */
    public function getPrizeGrades(array $prizes): array
    {
        $grades = [];
        foreach ($prizes as $index => $p) {
            $grades[$p['id']] = ($index + 1) . '等奖';
        }
        return $grades;
    }

    /**
     * 记录抽奖结果
     */
    protected function recordDraw(int $userId, array $prize, int $costBonus, ?array $result = null): int
    {
        $prizeId = $prize['id'];
        $prizeName = addslashes($prize['name']);
        $isWin = $prize['type'] !== 'nothing';
        $isWinInt = $isWin ? 1 : 0;
        $prizeData = addslashes(json_encode($prize));
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';

        // 保存详细结果状态
        $resultStatus = $result['status'] ?? ($isWin ? 'awarded' : 'nothing');
        $resultValue = $result['value'] ?? '';
        $resultUnit = $result['unit'] ?? '';
        
        $sql = "INSERT INTO fortune_wheel_records (user_id, prize_id, prize_name, is_win, cost_bonus, prize_data, result_status, result_value, result_unit, ip, created_at, updated_at)
                VALUES ($userId, $prizeId, '$prizeName', $isWinInt, $costBonus, '$prizeData', '$resultStatus', '$resultValue', '$resultUnit', '$ip', NOW(), NOW())";

        \Nexus\Database\NexusDB::statement($sql);

        $result = \Nexus\Database\NexusDB::select("SELECT LAST_INSERT_ID() as id");
        return $result[0]['id'] ?? 0;
    }

    /**
     * 更新用户统计
     */
    protected function updateUserStats(int $userId, bool $isWin, int $costBonus): void
    {
        $winCount = $isWin ? 1 : 0;

        $sql = "INSERT INTO fortune_wheel_user_stats (user_id, date, draw_count, win_count, total_cost, created_at, updated_at)
                VALUES ($userId, CURDATE(), 1, $winCount, $costBonus, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                draw_count = draw_count + 1,
                win_count = win_count + $winCount,
                total_cost = total_cost + $costBonus,
                updated_at = NOW()";

        \Nexus\Database\NexusDB::statement($sql);
    }

    /**
     * 减少奖品库存
     */
    protected function decreasePrizeStock(int $prizeId): void
    {
        $sql = "UPDATE fortune_wheel_prizes SET stock = stock - 1 WHERE id = $prizeId AND stock > 0";
        \Nexus\Database\NexusDB::statement($sql);
    }

    /**
     * 获取用户抽奖记录
     */
    public function getUserDrawRecords(int $userId, int $limit = 100): array
    {
        $sql = "SELECT r.*, p.name as prize_name_current
                FROM fortune_wheel_records r
                LEFT JOIN fortune_wheel_prizes p ON r.prize_id = p.id
                WHERE r.user_id = $userId
                ORDER BY r.id DESC
                LIMIT $limit";
        
        $records = \Nexus\Database\NexusDB::select($sql);
        
        // 强制限制返回数量
        if (count($records) > $limit) {
            $records = array_slice($records, 0, $limit);
        }
        
        foreach ($records as &$record) {
            if (empty($record['prize_name'])) {
                $record['prize_name'] = $record['prize_name_current'] ?? '未知奖品';
            }
            // 生成详细结果描述
            $status = $record['result_status'] ?? ($record['is_win'] ? 'awarded' : 'nothing');
            $value = $record['result_value'] ?? '';
            $unit = $record['result_unit'] ?? '';
            switch ($status) {
                case 'awarded':
                    $record['result_text'] = '恭喜！您获得了 ' . $value . ' ' . $unit;
                    break;
                case 'compensated':
                    $record['result_text'] = '物品重复，补偿您 ' . $value . ' ' . $unit;
                    break;
                case 'extended':
                    $record['result_text'] = '有效期已延长 ' . $value . ' ' . $unit;
                    break;
                case 'already_owned':
                    $record['result_text'] = '物品重复，无补偿';
                    break;
                case 'compensated_high_class':
                    $record['result_text'] = '等级更高，补偿您 ' . $value . ' ' . $unit;
                    break;
                case 'already_owned_high_class':
                    $record['result_text'] = '您已是更高贵的身份';
                    break;
                case 'nothing':
                    $record['result_text'] = '谢谢参与，再接再厉！';
                    break;
            default:
                    $record['result_text'] = '未知状态';
                    break;
        }
        }

        return $records;
    }

    /**
     * 获取最近中奖记录
     */
    protected function getRecentWinRecords(int $limit = 10): array
    {
        $sql = "SELECT r.created_at, r.user_id, u.username, r.prize_name, p.name as prize_name_current
                FROM fortune_wheel_records r
                LEFT JOIN users u ON r.user_id = u.id
                LEFT JOIN fortune_wheel_prizes p ON r.prize_id = p.id
                WHERE r.is_win = 1
                ORDER BY r.created_at DESC
                LIMIT $limit";

        $records = \Nexus\Database\NexusDB::select($sql);

        foreach ($records as &$record) {
            if (empty($record['prize_name'])) {
                $record['prize_name'] = $record['prize_name_current'] ?? '未知奖品';
            }
    }

        return $records;
    }

    /**
     * 获取用户统计信息
     */
    public function getUserStats(int $userId): array
    {
        $totalDrawSql = "SELECT COUNT(*) as total FROM fortune_wheel_records WHERE user_id = $userId";
        $totalDrawResult = \Nexus\Database\NexusDB::select($totalDrawSql);
        $totalDraw = $totalDrawResult[0]['total'] ?? 0;

        $totalWinSql = "SELECT COUNT(*) as total FROM fortune_wheel_records WHERE user_id = $userId AND is_win = 1";
        $totalWinResult = \Nexus\Database\NexusDB::select($totalWinSql);
        $totalWin = $totalWinResult[0]['total'] ?? 0;

        $todayCount = $this->getTodayDrawCount($userId);

        $totalCostSql = "SELECT SUM(cost_bonus) as total FROM fortune_wheel_records WHERE user_id = $userId";
        $totalCostResult = \Nexus\Database\NexusDB::select($totalCostSql);
        $totalCost = $totalCostResult[0]['total'] ?? 0;

        $lastSpinSql = "SELECT created_at FROM fortune_wheel_records WHERE user_id = $userId ORDER BY id DESC LIMIT 1";
        $lastSpinResult = \Nexus\Database\NexusDB::select($lastSpinSql);
        $lastSpinTime = $lastSpinResult[0]['created_at'] ?? null;

        return [
            'total_draw' => $totalDraw,
            'total_win' => $totalWin,
            'today_count' => $todayCount,
            'total_cost' => $totalCost,
            'win_rate' => $totalDraw > 0 ? round(($totalWin / $totalDraw) * 100, 2) : 0,
            'last_spin_time' => $lastSpinTime,
        ];
    }

    /**
     * 获取今日抽奖次数
     */
    public function getTodayDrawCount(int $userId): int
    {
        $sql = "SELECT draw_count FROM fortune_wheel_user_stats WHERE user_id = $userId AND date = CURDATE()";
        $result = \Nexus\Database\NexusDB::select($sql);
        return $result[0]['draw_count'] ?? 0;
    }

    /**
     * 复制公共文件到网站根目录
     */
    protected function copyPublicFiles(): void
    {
        $pluginRoot = dirname(__DIR__);
        $webRoot = ROOT_PATH . 'public';

        $publicFiles = [
            'fortune-wheel.php',
            'fortune-wheel-spin.php'
        ];

        foreach ($publicFiles as $file) {
            $source = $pluginRoot . '/public/' . $file;
            $destination = $webRoot . '/' . $file;

            if (file_exists($source)) {
                copy($source, $destination);
            }
        }
        $this->copyAssets();
    }

    /**
     * 复制资源文件
     */
    protected function copyAssets(): void
    {
        $pluginRoot = dirname(__DIR__);
        $webRoot = ROOT_PATH . 'public';
        $vendorDir = $webRoot . '/vendor/' . self::ID;

        if (!is_dir($vendorDir)) {
            mkdir($vendorDir, 0755, true);
        }

        $subDirs = ['css', 'js'];
        foreach ($subDirs as $dir) {
            $targetDir = $vendorDir . '/' . $dir;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
        }
        }

    /**
     * 执行抽奖的核心逻辑
     */
    public function spin(int $userId, int $count = 1): array
    {
        // Secondary system validation
        if (!$this->validateOperationPermissions()) {
            throw new \Exception("系统维护中，请稍后重试。");
        }

        $settings = $this->getSettings();
        $user = \App\Models\User::query()->findOrFail($userId);
        $userStats = $this->getUserStats($userId);

        // 抽奖间隔检查
        if ($userStats['last_spin_time']) {
            $lastSpinTimestamp = strtotime($userStats['last_spin_time']);
            $interval = (int)$settings['spin_interval'];
            if (time() - $lastSpinTimestamp < $interval) {
                throw new \Exception("操作过于频繁，请在 {$interval} 秒后重试。");
            }
        }

        // 检查总次数
        $remainingSpins = (int)$settings['daily_max_spins'] - $userStats['today_count'];
        if ($remainingSpins < $count) {
            throw new \Exception("今日剩余抽奖次数不足，您还可以抽 {$remainingSpins} 次。");
        }

        // 计算总消耗
        $totalCost = 0;
        $freeSpinsLeft = max(0, (int)$settings['daily_free_spins'] - $userStats['today_count']);
        $paidSpins = max(0, $count - $freeSpinsLeft);
        if ($paidSpins > 0) {
            $totalCost = $paidSpins * (int)$settings['bonus_cost_per_spin'];
            if ($user->seedbonus < $totalCost) {
                $bonusName = $this->getBonusName();
                throw new \Exception("{$bonusName}不足，需要 " . number_format($totalCost) . "，您只有 " . number_format($user->seedbonus));
            }
        }
        
        // 一次性扣除魔力值
        if ($totalCost > 0) {
            \App\Models\User::query()->where('id', $userId)->decrement('seedbonus', $totalCost);
        }

        $prizes = $this->getEnabledPrizes();
        if (empty($prizes)) {
            throw new \Exception('当前没有可用的奖品，抽奖无法进行');
        }

        $grades = $this->getPrizeGrades($prizes);
        $results = [];
        
        // 批量处理：减少数据库操作次数
        if ($count >= 10) {
            // 大批量抽奖：批量处理数据库操作
            $results = $this->_batchSpin($userId, $prizes, $settings, $grades, $count, $userStats);
        } else {
            // 小批量抽奖：保持原有逻辑
            for ($i = 0; $i < $count; $i++) {
                $isFree = ($userStats['today_count'] + $i) < (int)$settings['daily_free_spins'];
                $spinCost = $isFree ? 0 : (int)$settings['bonus_cost_per_spin'];
                
                $result = $this->_spinOnce($userId, $prizes, $settings, $spinCost, $grades);
                if ($result) {
                    $results[] = $result;
                }
            }
        }
        
        return [
            'success' => true,
            'results' => $results,
        ];
    }
    
    /**
     * 批量抽奖：优化大批量抽奖的数据库操作
     */
    private function _batchSpin(int $userId, array $prizes, array $settings, array $grades, int $count, array $userStats): array
    {
        $results = [];
        $records = []; // 批量记录
        $totalWinCount = 0;
        $totalCost = 0;
        
        // 预计算所有抽奖结果
        for ($i = 0; $i < $count; $i++) {
            $isFree = ($userStats['today_count'] + $i) < (int)$settings['daily_free_spins'];
            $spinCost = $isFree ? 0 : (int)$settings['bonus_cost_per_spin'];
            
            $prize = $this->drawPrize($prizes);
            if (!$prize) {
                continue;
            }
            
            // 简化的库存检查（不锁定）
            if ($prize['stock'] != -1 && $prize['stock'] <= 0) {
                $prize = [
                    'id' => 0,
                    'name' => '谢谢参与',
                    'type' => 'nothing',
                    'sort_order' => 0,
                ];
            }
            
            $prizeResult = $this->awardPrize($userId, $prize, $settings);
            $isWin = $prize['type'] !== 'nothing';
            
            if ($isWin) {
                $totalWinCount++;
            }
            $totalCost += $spinCost;
            
            $results[] = [
                'prize' => [
                    'id' => $prize['id'],
                    'name' => $prize['name'],
                ],
                'result' => $prizeResult,
                'grade' => $grades[$prize['id']] ?? '参与奖'
            ];
            
            // 准备批量记录数据
            $records[] = [
                'user_id' => $userId,
                'prize_id' => $prize['id'],
                'prize_name' => addslashes($prize['name']),
                'is_win' => $isWin ? 1 : 0,
                'cost_bonus' => $spinCost,
                'prize_data' => addslashes(json_encode($prize)),
                'result_status' => $prizeResult['status'] ?? ($isWin ? 'awarded' : 'nothing'),
                'result_value' => $prizeResult['value'] ?? '',
                'result_unit' => $prizeResult['unit'] ?? '',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ];
        }
        
        // 批量插入记录
        if (!empty($records)) {
            $this->_batchInsertRecords($records);
        }
        
        // 批量更新用户统计
        $this->_batchUpdateUserStats($userId, $totalWinCount, $count, $totalCost);
        
        // 批量更新库存
        $this->_batchUpdateStock($records);
        
        return $results;
    }
    
    /**
     * 批量插入抽奖记录
     */
    private function _batchInsertRecords(array $records): void
    {
        if (empty($records)) {
            return;
        }
        
        $values = [];
        foreach ($records as $record) {
            $values[] = sprintf("(%d, %d, '%s', %d, %d, '%s', '%s', '%s', '%s', '%s', NOW(), NOW())",
                $record['user_id'],
                $record['prize_id'],
                $record['prize_name'],
                $record['is_win'],
                $record['cost_bonus'],
                $record['prize_data'],
                $record['result_status'],
                $record['result_value'],
                $record['result_unit'],
                $record['ip']
            );
        }
        
        $sql = "INSERT INTO fortune_wheel_records (user_id, prize_id, prize_name, is_win, cost_bonus, prize_data, result_status, result_value, result_unit, ip, created_at, updated_at) VALUES " . implode(', ', $values);
        \Nexus\Database\NexusDB::statement($sql);
    }
    
    /**
     * 批量更新用户统计
     */
    private function _batchUpdateUserStats(int $userId, int $winCount, int $drawCount, int $totalCost): void
    {
        $sql = "INSERT INTO fortune_wheel_user_stats (user_id, date, draw_count, win_count, total_cost, created_at, updated_at)
                VALUES ($userId, CURDATE(), $drawCount, $winCount, $totalCost, NOW(), NOW())
                ON DUPLICATE KEY UPDATE
                draw_count = draw_count + $drawCount,
                win_count = win_count + $winCount,
                total_cost = total_cost + $totalCost,
                updated_at = NOW()";
        
        \Nexus\Database\NexusDB::statement($sql);
    }
    
    /**
     * 批量更新库存
     */
    private function _batchUpdateStock(array $records): void
    {
        $stockUpdates = [];
        foreach ($records as $record) {
            if ($record['prize_id'] > 0) {
                $stockUpdates[$record['prize_id']] = ($stockUpdates[$record['prize_id']] ?? 0) + 1;
            }
        }
        
        foreach ($stockUpdates as $prizeId => $decreaseCount) {
            $sql = "UPDATE fortune_wheel_prizes SET stock = stock - $decreaseCount WHERE id = $prizeId AND stock >= $decreaseCount";
            \Nexus\Database\NexusDB::statement($sql);
        }
    }
    
    /**
     * 执行单次抽奖
     */
    private function _spinOnce(int $userId, array $prizes, array $settings, int $cost, array $grades): ?array
    {
        $prize = $this->drawPrize($prizes);
        if (!$prize) {
            return null;
        }

        // 检查并扣减库存
        $stockCheckPassed = true;
        if ($prize['stock'] != -1) {
            $pdo = \Nexus\Database\NexusDB::connection()->getPdo();
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("SELECT stock FROM fortune_wheel_prizes WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $prize['id']]);
                $currentStock = $stmt->fetchColumn();

                if ($currentStock > 0) {
                    $updateStmt = $pdo->prepare("UPDATE fortune_wheel_prizes SET stock = stock - 1 WHERE id = :id");
                    $updateStmt->execute(['id' => $prize['id']]);
                } else {
                    $stockCheckPassed = false;
                }
                $pdo->commit();
            } catch (\Exception $e) {
                $pdo->rollBack();
                // 记录并发错误日志
                error_log("Fortune Wheel: Stock check/deduction failed due to race condition for prize ID {$prize['id']}: " . $e->getMessage());
                $stockCheckPassed = false;
            }
        }
        
        if (!$stockCheckPassed) {
             // 如果库存不足，可以返回一个特殊的结果，或者重新抽奖
             // 这里我们简单地返回“谢谢参与”
            $nothingPrize = [
                'id' => 0, // 或者一个特殊的ID
                'name' => '谢谢参与',
                'type' => 'nothing',
                'sort_order' => 0,
            ];
            $prizeResult = ['status' => 'nothing'];
            $this->recordDraw($userId, $nothingPrize, $cost, $prizeResult);
            $this->updateUserStats($userId, false, $cost);
            
            return [
                'prize' => $nothingPrize,
                'result' => $prizeResult,
                'grade' => '谢谢参与'
            ];
        }

        $prizeResult = $this->awardPrize($userId, $prize, $settings);

        $this->recordDraw($userId, $prize, $cost, $prizeResult);
        $this->updateUserStats($userId, ($prize['type'] !== 'nothing'), $cost);
        
        return [
            'prize' => [
                'id' => $prize['id'],
                'name' => $prize['name'],
            ],
            'result' => $prizeResult,
            'grade' => $grades[$prize['id']] ?? '参与奖'
        ];
    }

    private function renderWinAnnouncement(array $recentWins): string
    {
        $winRecordsHtml = '';
        if (!empty($recentWins)) {
            foreach ($recentWins as $record) {
                // 根据奖品类型设置颜色和图标
                $prizeStyle = $this->getPrizeDisplayStyle($record['prize_name']);
                
                // 使用系统标准的用户名显示函数，包含彩虹ID和勋章
                $formattedUsername = get_username($record['user_id'], false, true, true, false, false, false, '', false);
                
                $winRecordsHtml .= sprintf(
                    '<div style="padding: 8px 0; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; -webkit-font-smoothing: antialiased; text-rendering: optimizeLegibility;">
                        <div style="display: flex; align-items: center; gap: 5px;">
                            %s 获得了
                            <span style="%s; text-shadow: none; -webkit-font-smoothing: antialiased;">%s %s</span>
                        </div>
                        <span style="color: #666; font-size: 12px; font-weight: normal; -webkit-font-smoothing: antialiased;">%s</span>
                    </div>',
                    $formattedUsername,
                    $prizeStyle['style'],
                    $prizeStyle['icon'],
                    htmlspecialchars($record['prize_name']),
                    date('m-d H:i', strtotime($record['created_at']))
                );
            }
        } else {
            $winRecordsHtml = '<div style="text-align: center; color: #999; padding: 20px; -webkit-font-smoothing: antialiased;">暂无中奖记录</div>';
        }

        return sprintf('
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            var shoutboxTable = document.querySelector("iframe#iframe-shout-box");
            if (shoutboxTable) {
                var parentTable = shoutboxTable.closest("table");
                if (parentTable) {
                    var announcementDiv = document.createElement("div");
                    announcementDiv.innerHTML = `
                        <h2>🏆 幸运抽奖中奖公示 <font class="small"> - [<a class="altlink" href="fortune-wheel.php"><b>去抽奖</b></a>]</font></h2>
                        <table width="100%%">
                            <tbody><tr><td class="text">
                                <div style="max-height: 200px; overflow-y: auto; padding: 10px; background: #f9f9f9;">
                                    %s
                                </div>
                            </td></tr>
                            </tbody></table>
                    `;
                    parentTable.parentNode.insertBefore(announcementDiv, parentTable.nextSibling);
        }
    }
        });
        </script>',
            addslashes($winRecordsHtml)
        );
    }

    /**
     * 根据奖品名称获取显示样式
     */
    private function getPrizeDisplayStyle($prizeName): array
    {
        // 预定义奖品类型的样式 - 优化清晰度，移除模糊效果
        $styles = [
            // VIP类
            'VIP' => [
                'style' => 'color: #9c27b0; font-weight: bold;',
                'icon' => '👑'
            ],
            // 魔力值
            '魔力' => [
                'style' => 'color: #ff9800; font-weight: bold;',
                'icon' => '💰'
            ],
            // 上传量
            '上传' => [
                'style' => 'color: #4caf50; font-weight: bold;',
                'icon' => '📤'
            ],
            // 邀请名额
            '邀请' => [
                'style' => 'color: #2196f3; font-weight: bold;',
                'icon' => '🎟️'
            ],
            // 彩虹ID
            '彩虹' => [
                'style' => 'color: #e91e63; font-weight: bold; background: linear-gradient(90deg, #ff6b6b, #4ecdc4, #45b7d1, #96ceb4, #ffeaa7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;',
                'icon' => '🌈'
            ],
            // 改名卡
            '改名' => [
                'style' => 'color: #795548; font-weight: bold;',
                'icon' => '📝'
            ],
            // 勋章
            '勋章' => [
                'style' => 'color: #ff5722; font-weight: bold;',
                'icon' => '🏅'
            ],
            // 补签卡
            '补签' => [
                'style' => 'color: #607d8b; font-weight: bold;',
                'icon' => '📋'
            ],
            // 谢谢参与
            '谢谢' => [
                'style' => 'color: #9e9e9e; font-weight: normal;',
                'icon' => '💧'
            ]
        ];

        // 根据奖品名称匹配样式
        foreach ($styles as $keyword => $style) {
            if (strpos($prizeName, $keyword) !== false) {
                return $style;
            }
        }

        // 默认样式
        return [
            'style' => 'color: #FF6B35; font-weight: bold;',
            'icon' => '🎁'
        ];
    }
}



