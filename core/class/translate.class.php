<?php

/**
 * Description of config
 *
 * @author Antoine Bonnefoy & Gevrey Loïc
 */
/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../php/core.inc.php';

class translate {
    /*     * *************************Attributs****************************** */

    protected static $translation;
    protected static $language;

    /*     * ***********************Methode static*************************** */

    public static function getTranslation() {
        if (!isset(static::$translation) || !isset(static::$translation[self::getLanguage()])) {
            static::$translation = array(
                self::getLanguage() => self::loadTranslation(),
            );
        }
        return static::$translation[self::getLanguage()];
    }

    public static function exec($_content, $_name, $_backslash = false) {
        $language = self::getLanguage();
        $modify = false;
        $translate = self::getTranslation();
        preg_match_all("/{{(.*?)}}/", $_content, $matches);
        foreach ($matches[1] as $text) {
            $replace = false;
            if (isset($translate[$_name])) {
                if (isset($translate[$_name][$text])) {
                    $replace = $translate[$_name][$text];
                }
            }
            if ($replace === false && isset($translate['common'])) {
                if (isset($translate['common'][$text])) {
                    $replace = $translate['common'][$text];
                }
            }
            if ($replace === false) {
                $modify = true;
                if (!isset($translate[$_name])) {
                    $translate[$_name] = array();
                }
                $translate[$_name][$text] = $text;
            }
            if ($_backslash && $replace !== false) {
                $replace = str_replace("'", "\'", $replace);
            }
            if ($replace === false) {
                $replace = $text;
            }

            $_content = str_replace('{{' . $text . '}}', $replace, $_content);
        }
        if ($modify && self::getLanguage() != 'fr_FR') {
            static::$translation[self::getLanguage()] = $translate;
            self::saveTranslation($language);
        }
        return $_content;
    }

    public static function getPathTranslationFile($_language) {
        return dirname(__FILE__) . '/../i18n/' . $_language . '.php';
    }

    public static function loadTranslation() {
        $return = array();
        if (self::getLanguage() != 'fr_FR') {
            if (file_exists(self::getPathTranslationFile(self::getLanguage()))) {
                $return = jeedom::print_r_reverse(file_get_contents(self::getPathTranslationFile(self::getLanguage())));
                foreach (plugin::listPlugin(true) as $plugin) {
                    $return = array_merge($return, $plugin->getTranslation(self::getLanguage()));
                }
            }
        }
        return $return;
    }

    public static function saveTranslation() {
        $core = array();
        $plugins = array();
        foreach (self::getTranslation(self::getLanguage()) as $page => $translation) {
            if (strpos($page, 'plugins/') === false) {
                $core[$page] = $translation;
            } else {
                $plugin = substr($page, strpos($page, 'plugins/') + 8);
                $plugin = substr($plugin, 0, strpos($plugin, '/'));
                if (!isset($plugins[$plugin])) {
                    $plugins[$plugin] = array();
                }
                $plugins[$plugin][$page] = $translation;
            }
        }
        file_put_contents(self::getPathTranslationFile(self::getLanguage()), print_r($core, true));
        foreach ($plugins as $plugin_name => $translation) {
            $plugin = new plugin($plugin_name);
            $plugin->saveTranslation(self::getLanguage(), $translation);
        }
    }

    public static function getLanguage() {
        if (!isset(static::$language)) {
            static::$language = config::byKey('language', 'core', 'fr_FR');
        }
        return static::$language;
    }

    /*     * *********************Methode d'instance************************* */
}

?>
