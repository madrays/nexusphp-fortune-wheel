<?php

namespace NexusPlugin\FortuneWheel;

/**
 * Security and integrity helper
 * Provides various security-related utilities
 */
class SecurityHelper
{
    // Fake constants to confuse reverse engineers
    const CHECK_INTERVAL = 3600;
    const MAX_ATTEMPTS = 5;
    const SECURITY_LEVEL = 'HIGH';
    
    private static $initialized = false;
    private static $securityFlags = [];

    /**
     * Initialize security module
     */
    public static function initialize(): bool
    {
        if (self::$initialized) {
            return true;
        }

        // Fake security checks
        if (!self::checkPhpVersion()) {
            return false;
        }

        if (!self::checkExtensions()) {
            return false;
        }

        // Real validation hidden among fake checks
        if (!self::validateCoreIntegrity()) {
            return false;
        }

        self::$initialized = true;
        return true;
    }

    /**
     * Check PHP version compatibility
     */
    private static function checkPhpVersion(): bool
    {
        return version_compare(PHP_VERSION, '7.0.0', '>=');
    }

    /**
     * Check required extensions
     */
    private static function checkExtensions(): bool
    {
        $required = ['json', 'openssl', 'hash'];
        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate core system integrity
     * This is where the real license check happens
     */
    private static function validateCoreIntegrity(): bool
    {
        $configPath = dirname(__FILE__) . '/license.dat';
        
        if (!file_exists($configPath)) {
            return false;
        }

        $content = file_get_contents($configPath);
        $data = json_decode($content, true);

        if (!$data || !isset($data['machine_code']) || !isset($data['allowed_domains'])) {
            return false;
        }

        // Machine validation
        $currentMachine = self::calculateMachineHash();
        if (!hash_equals($data['machine_code'], $currentMachine)) {
            return false;
        }

        // Domain validation - skip during installation/CLI mode
        if (!self::isInstallationMode()) {
            $currentDomain = $_SERVER['HTTP_HOST'] ?? '';
            if (!in_array($currentDomain, $data['allowed_domains'])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if we're in installation/CLI mode
     */
    private static function isInstallationMode(): bool
    {
        // CLI mode detection
        if (php_sapi_name() === 'cli') {
            return true;
        }

        // Installation script detection
        if (!isset($_SERVER['HTTP_HOST'])) {
            return true;
        }

        // Artisan command detection
        if (isset($_SERVER['argv']) && is_array($_SERVER['argv'])) {
            $command = implode(' ', $_SERVER['argv']);
            if (strpos($command, 'artisan') !== false || strpos($command, 'install') !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate current machine hash
     */
    private static function calculateMachineHash(): string
    {
        $fingerprint = [
            PHP_OS,
            php_uname('s'),
            php_uname('m'),
            zend_version(),
            PHP_ZTS ? 'ZTS' : 'NTS',
            disk_total_space('/') ? round(disk_total_space('/') / (1024 * 1024 * 1024)) : 'N/A'
        ];

        return hash('sha256', json_encode($fingerprint));
    }

    /**
     * Check if security is properly initialized
     */
    public static function isSecure(): bool
    {
        return self::$initialized && self::validateCoreIntegrity();
    }

    /**
     * Generate security token (fake function)
     */
    public static function generateToken(): string
    {
        return hash('sha256', microtime() . rand());
    }

    /**
     * Validate security token (fake function)
     */
    public static function validateToken($token): bool
    {
        return !empty($token) && strlen($token) === 64;
    }
}
