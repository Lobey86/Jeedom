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
        if ($_language != 'fr_FR') {
            if (file_exists(self::getPathTranslationFile($_language))) {
                $content = file_get_contents(self::getPathTranslationFile($_language));
                return self::print_r_reverse($content);
            }
        }
        return array();
    }

    public static function saveTranslation($_language) {
        file_put_contents(self::getPathTranslationFile($_language), print_r(self::getTranslation($_language), true));
    }

    private static function print_r_reverse(&$output) {
        $expecting = 0; // 0=nothing in particular, 1=array open paren '(', 2=array element or close paren ')'
        $lines = explode("\n", $output);
        $result = null;
        $topArray = null;
        $arrayStack = array();
        $matches = null;
        while (!empty($lines) && $result === null) {
            $line = array_shift($lines);
            $trim = trim($line);
            if ($trim == 'Array') {
                if ($expecting == 0) {
                    $topArray = array();
                    $expecting = 1;
                } else {
                    trigger_error("Unknown array.");
                }
            } else if ($expecting == 1 && $trim == '(') {
                $expecting = 2;
            } else if ($expecting == 2 && preg_match('/^\[(.+?)\] \=\> (.+)$/', $trim, $matches)) { // array element
                list ($fullMatch, $key, $element) = $matches;
                if (trim($element) == 'Array') {
                    $topArray[$key] = array();
                    $newTopArray = & $topArray[$key];
                    $arrayStack[] = & $topArray;
                    $topArray = & $newTopArray;
                    $expecting = 1;
                } else {
                    $topArray[$key] = $element;
                }
            } else if ($expecting == 2 && $trim == ')') { // end current array
                if (empty($arrayStack)) {
                    $result = $topArray;
                } else { // pop into parent array
                    // safe array pop
                    $keys = array_keys($arrayStack);
                    $lastKey = array_pop($keys);
                    $temp = & $arrayStack[$lastKey];
                    unset($arrayStack[$lastKey]);
                    $topArray = & $temp;
                }
            } else if (!empty($trim)) {
                $result = $line;
            }
        }
        $output = implode("\n", $lines);
        return $result;
    }

    /*     * *********************Methode d'instance************************* */
}

?>
