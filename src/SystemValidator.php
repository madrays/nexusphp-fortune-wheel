<?php

namespace NexusPlugin\FortuneWheel;

/**
 * System environment validator
 * Handles various system integrity checks
 */
class SystemValidator
{
    private static $instance = null;
    private $validationCache = [];

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Check if system meets requirements
     */
    public function validateSystemRequirements(): bool
    {
        $cacheKey = 'sys_req_' . date('YmdH');
        
        if (isset($this->validationCache[$cacheKey])) {
            return $this->validationCache[$cacheKey];
        }

        $result = $this->performValidation();
        $this->validationCache[$cacheKey] = $result;
        
        return $result;
    }

    /**
     * Internal validation logic
     */
    private function performValidation(): bool
    {
        // Check 1: Configuration files
        if (!$this->validateConfigFiles()) {
            return false;
        }

        // Check 2: Environment parameters  
        if (!$this->validateEnvironment()) {
            return false;
        }

        // Check 3: System permissions
        return $this->validatePermissions();
    }

    private function validateConfigFiles(): bool
    {
        $configFile = dirname(__FILE__) . '/license.dat';
        
        if (!file_exists($configFile)) {
            return false;
        }

        $content = file_get_contents($configFile);
        $data = json_decode($content, true);

        return is_array($data) && isset($data['machine_code']) && isset($data['allowed_domains']);
    }

    private function validateEnvironment(): bool
    {
        // Disguised machine code validation
        $expectedFingerprint = $this->getSystemFingerprint();
        
        if (!$expectedFingerprint) {
            return false;
        }

        return $this->verifySystemFingerprint($expectedFingerprint);
    }

    private function validatePermissions(): bool
    {
        // Disguised domain validation
        $configFile = dirname(__FILE__) . '/license.dat';
        $content = file_get_contents($configFile);
        $data = json_decode($content, true);
        
        if (!isset($data['allowed_domains'])) {
            return false;
        }

        // Skip domain validation during installation/CLI
        if ($this->isInstallationEnvironment()) {
            return true;
        }

        $currentDomain = $_SERVER['HTTP_HOST'] ?? '';
        return in_array($currentDomain, $data['allowed_domains']);
    }

    /**
     * Check if we're in installation environment
     */
    private function isInstallationEnvironment(): bool
    {
        return php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_HOST']);
    }

    private function getSystemFingerprint()
    {
        $configFile = dirname(__FILE__) . '/license.dat';
        $content = file_get_contents($configFile);
        $data = json_decode($content, true);
        
        return $data['machine_code'] ?? null;
    }

    private function verifySystemFingerprint($expected): bool
    {
        $current = $this->generateCurrentFingerprint();
        return hash_equals($expected, $current);
    }

    private function generateCurrentFingerprint(): string
    {
        $data = [
            PHP_OS,
            php_uname('s'),
            php_uname('m'),
            zend_version(),
            PHP_ZTS ? 'ZTS' : 'NTS',
            disk_total_space('/') ? round(disk_total_space('/') / (1024 * 1024 * 1024)) : 'N/A'
        ];

        return hash('sha256', json_encode($data));
    }
}
