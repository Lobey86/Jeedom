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

    /*     * ***********************Methode static*************************** */

    public static function getTranslation($_language) {
        if (!isset(static::$translation) || !isset(static::$translation[$_language])) {
            static::$translation = array(
                $_language => self::loadTranslation($_language),
            );
        }
        return static::$translation[$_language];
    }

    public static function exec($_content, $_name, $_language = 'fr_FR', $_backslash = false) {
        $modify = false;
        $translate = self::getTranslation($_language);
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
        if ($modify && $_language != 'fr_FR') {
            static::$translation[$_language] = $translate;
            self::saveTranslation($_language);
        }
        return $_content;
    }

    public static function getPathTranslationFile($_language) {
        return dirname(__FILE__) . '/../i18n/' . $_language . '.php';
    }

    public static function loadTranslation($_language) {
        $return = array();
        if ($_language != 'fr_FR') {
            if (file_exists(self::getPathTranslationFile($_language))) {
                $return = jeedom::print_r_reverse(file_get_contents(self::getPathTranslationFile($_language)));
                foreach (plugin::listPlugin(true) as $plugin) {
                    $return = array_merge($return, $plugin->getTranslation($_language));
                }
            }
        }
        return $return;
    }

    public static function saveTranslation($_language) {
        $core = array();
        $plugins = array();
        foreach (self::getTranslation($_language) as $page => $translation) {
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
        file_put_contents(self::getPathTranslationFile($_language), print_r($core, true));
        foreach ($plugins as $plugin_name => $translation) {
            $plugin = new plugin($plugin_name);
            $plugin->saveTranslation($_language, $translation);
        }
    }

    /*     * *********************Methode d'instance************************* */
}

?>
