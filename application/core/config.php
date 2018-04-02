<?php

namespace BronyCenter;

class Config
{
    private static $instance = null;
    private $settings = null;
    private $versions = null;

    public function __construct()
    {
        $this->settings = $this->readSettingsFiles();
        $this->versions = $this->readVersionsFiles();
    }

     public static function getInstance(bool $reset = false)
     {
         if (!self::$instance || $reset === true) {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    // TODO Throw an exception if settings file has not been found
    private function readSettingsFiles() : array
    {
        if (file_exists(__DIR__ . '/../../settings.dev.ini')) {
            $settings = parse_ini_file(__DIR__ . '/../../settings.dev.ini', true);
        } else if (file_exists(__DIR__ . '/../../settings.ini')) {
            $settings = parse_ini_file(__DIR__ . '/../../settings.ini', true);
        }

        return $settings ?: [];
    }

    // TODO Throw an exception if version file has not been found
    // TODO Throw an exception if version files have not been found (if modification is used)
    private function readVersionsFiles() : array
    {
        $versions = [];

        $versions['software'] = parse_ini_file(__DIR__ . '/../../version.ini', false);

        if (file_exists(__DIR__ . '/../../version.dev.ini')) {
            $versions['website'] = parse_ini_file(__DIR__ . '/../../version.dev.ini', false);
        }

        return $versions ?: [];
    }

    public function getSettings(string $section = '') : array
    {
        if (empty($section)) {
            $settings = $this->settings;
        } else if (array_key_exists($section, $this->settings)) {
            $settings = $this->settings[$section];
        }

        return $settings ?: [];
    }

    public function getVersions(string $section = '') : array
    {
        if (empty($section)) {
            $versions = $this->versions;
        } else if (array_key_exists($section, $this->versions)) {
            $versions = $this->versions[$section];
        }

        return $versions ?: [];
    }

    public function isUsingCustomVersion() : bool
    {
        if (empty($this->versions['website'])) {
            return false;
        }

        return true;
    }

    public function getWebsiteTitle(bool $software = false) : string
    {
        if ($software !== true) {
            $title = $this->versions['website']['title'] ?? $this->versions['software']['title'] ?? 'No title';
        } else {
            $title = $this->versions['software']['title'] ?? 'No title';
        }

        return $title ?? 'No title';
    }

    public function getWebsiteVersion(bool $software = false) : string
    {
        if ($software !== true) {
            $version = $this->versions['website']['version'] ?? $this->versions['software']['version'] ?? 'No version';
        } else {
            $version = $this->versions['software']['version'] ?? 'No version';
        }

        return $version ?? 'No version';
    }

    public function getWebsiteDate(bool $software = false) : string
    {
        if ($software !== true) {
            $date = $this->versions['website']['date'] ?? $this->versions['software']['date'] ?? 'No date';
        } else {
            $date = $this->versions['software']['date'] ?? 'No date';
        }

        return $date ?? 'No date';
    }

    public function getWebsiteCommit(bool $software = false) : int
    {
        if ($software !== true) {
            $commit = $this->versions['website']['commit'] ?? $this->versions['software']['commit'] ?? 0;
        } else {
            $commit = $this->versions['software']['commit'] ?? 0;
        }

        return intval($commit) ?: 0;
    }
}
