<?php

// get_machine_code.php
// INSTRUCTION FOR DEVELOPER: Send this file to your customer.
// The customer must run this on their server and send the output 'Machine Code' back to you.

// Suppress errors for functions that might be disabled in php.ini
error_reporting(0);

/**
 * Generates a machine fingerprint string from various server properties.
 * We use a combination of properties to ensure uniqueness and stability.
 *
 * @return string The raw fingerprint string.
 */
function getMachineFingerprint(): string
{
    $fingerprintData = [
        // OS and architecture
        PHP_OS,
        php_uname('s'), // Operating system name
        php_uname('m'), // Machine type (e.g., x86_64)

        // PHP environment
        zend_version(),
        PHP_ZTS ? 'ZTS' : 'NTS', // Z-Thread-Safety
        
        // A stable measure of the server's main disk.
        // Rounded to GB to avoid small fluctuations.
        disk_total_space('/') ? round(disk_total_space('/') / (1024 * 1024 * 1024)) : 'N/A'
    ];

    // Create a stable JSON representation.
    return json_encode($fingerprintData);
}

// Use SHA256 to create a shorter, fixed-length, user-friendly machine code.
$machineCode = hash('sha256', getMachineFingerprint());

// Detect current domain
$currentDomain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '命令行执行';

header('Content-Type: text/plain');
?>
====================================================
           幸运大转盘Pro - 授权专用机器码
====================================================

请将下面的信息完整复制并发送给插件作者：

【机器码】
<?php echo $machineCode; ?>

【检测到的当前域名】
<?php echo $currentDomain; ?>

【重要说明】
1. 请同时告知插件作者您网站的所有域名（含子域名）
   例如: www.yoursite.com, yoursite.com, m.yoursite.com
2. 只有在授权域名列表中的域名才能正常使用插件

====================================================
(此脚本仅用于生成一次性机器码，可安全删除)
