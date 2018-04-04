<?php

// Translates a website to selected language

namespace BronyCenter;

class Translation
{
    private static $instance = null;
    private $o_config = null;
    private $a_translation = null;
    private $a_translation_custom = null;

    public function __construct()
    {
        $this->o_config = Config::getInstance();

        $websiteLanguage = $this->o_config->getSettings('system')['language'] ?? 'en';
        $defaultTranslationPath = __DIR__ . '/../partials/translation/';

        // Fallback to english translation if selected one doesn't exist
        if (!file_exists($defaultTranslationPath . $websiteLanguage . '.php')) {
            $websiteLanguage = 'en';
            $translationPath = $defaultTranslationPath . $websiteLanguage . '.php';
        }

        require_once($defaultTranslationPath . $websiteLanguage . '.php');
        $this->a_translation = $translationArray;

        // Override changes if custom translation exists
        if (file_exists($defaultTranslationPath . 'custom-' . $websiteLanguage . '.php')) {
            require_once($defaultTranslationPath . 'custom-' . $websiteLanguage . '.php');
            $this->a_translation_custom = $translationArray;
        }
    }

    public static function getInstance($reset = false) {
        if (!self::$instance || $reset === true) {
            self::$instance = new Translation();
        }

        return self::$instance;
    }

    public function getString(string $category = '', string $string = '', array $parameters = []) : string
    {
        if (empty($this->a_translation[$category]) || empty($this->a_translation[$category][$string])) {
            $translation = $this->a_translation['common']['unknownTranslation'];
        } else if (!empty($this->a_translation_custom[$category]) && !empty($this->a_translation_custom[$category][$string])) {
            $translation = $this->a_translation_custom[$category][$string];
        } else {
            $translation = $this->a_translation[$category][$string];
        }

        if (count($parameters)) {
            for ($i = 0; $i < count($parameters); $i++) {
                $parameterPosition = strpos($translation, '{param}');

                if ($parameterPosition !== false) {
                    $translation = substr_replace($translation, $parameters[$i], $parameterPosition, 7);
                }
            }
        }

        return $translation;
    }
}
