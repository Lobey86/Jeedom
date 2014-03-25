<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

define('PHP', 'php');
define('CSS', 'css');
define('JS', 'js');
define('CLASSJS', 'class.js');
define('AJAX', 'ajax');
define('COM', 'com');
define('CONFIG', 'config');
define('PLUGINS', 'plugins');
define('CORE', 'core');
define('MODAL', 'modal');
define('API', 'api');

function include_file($_folder, $_fn, $_type, $_plugin = '') {
    $found = false;
    if ($_folder == '3rdparty') {
        $found = true;
        $_folder = $_folder;
        $_fn = $_fn . '.' . $_type;
        $path = dirname(__FILE__) . "/../../$_folder/$_fn";
    }
    if ($_folder == CORE) {
        $found = true;
        if ($_type == 'class') {
            $_folder .= '/class';
            $_fn = $_fn . '.class.php';
        }
        if ($_folder == AJAX) {
            $_folder .= '/ajax';
            $_fn = $_fn . '.ajax.php';
        }
        if ($_type == COM) {
            $_folder .= '/com';
            $_fn = $_fn . '.com.php';
        }
        if ($_type == CONFIG) {
            $_folder .= '/config';
            $_fn = $_fn . '.config.php';
        }
        if ($_type == PHP) {
            $_folder .= '/php';
            $_fn = $_fn . '.php';
        }
        if ($_type == JS) {
            $_folder .= '/js';
            $_fn = $_fn . '.js';
        }
        if ($_type == CSS) {
            $_folder .= '/css';
            $_fn = $_fn . '.css';
        }
        if ($_type == CLASSJS) {
            $_folder .= '/js';
            $_fn = $_fn . '.class.js';
        }
        if ($_type == API) {
            $_folder .= '/api';
            $_fn = $_fn . '.api.php';
        }
    }

    if (!$found) {
        if ($_type == MODAL) {
            $_folder = $_folder . '/modal';
            $_fn = $_fn . '.php';
        }
        if ($_type == PHP) {
            $_folder = $_folder . '/php';
            $_fn = $_fn . '.php';
        }
        if ($_type == CSS) {
            $_folder = $_folder . '/css';
            $_fn = $_fn . '.css';
        }
        if ($_type == JS) {
            $_folder = $_folder . '/js';
            $_fn = $_fn . '.js';
        }
        if ($_type == CLASSJS) {
            $_folder = $_folder . '/js';
            $_fn = $_fn . '.class.js';
        }
        if ($_type == API) {
            $_folder .= '/api';
            $_fn = $_fn . '.api.php';
        }
    }
    if ($_plugin != '') {
        $_folder = 'plugins/' . $_plugin . '/' . $_folder;
    }
    $path = dirname(__FILE__) . "/../../$_folder/$_fn";
    if (file_exists($path)) {
        if ($_type == PHP || $_folder == AJAX || $_type == 'class' || $_type == COM || $_type == CONFIG || $_type == MODAL || $_type == API)
            require_once($path);
        else if ($_type == CSS)
            echo "<link href=\"$_folder/$_fn\" rel=\"stylesheet\" />";
        else if ($_type == JS || $_type == CLASSJS)
            echo "<script type=\"text/javascript\" src=\"$_folder/$_fn\"></script>";
    } else {
        throw new Exception("File not found : $_fn at $_folder : $path");
    }
}

function getTemplate($_folder, $_version, $_filename, $_plugin = '') {
    $path = dirname(__FILE__) . '/../../';
    if (trim($_plugin) == '') {
        $path .= $_folder . '/template/' . $_version . '/' . $_filename . '.html';
    } else {
        $path .= 'plugins/' . $_plugin . '/core/template/' . $_version . '/' . $_filename . '.html';
    }
    if (file_exists($path)) {
        return file_get_contents($path);
    } else {
        throw new Exception("Fichier non trouvé : $_filename à $_folder / $_version (" . trim($_plugin) . ") : $path");
    }
}

function template_replace($_array, $_subject) {
    return str_replace(array_keys($_array), array_values($_array), $_subject);
}

function init($_name, $_default = '') {
    if (isset($_REQUEST[$_name])) {
        return $_REQUEST[$_name];
    }
    if (isset($_GET[$_name])) {
        return $_GET[$_name];
    }
    if (isset($_POST[$_name])) {
        return $_POST[$_name];
    }
    return $_default;
}

function sendVarToJS($_varName, $_value) {
    if (is_array($_value)) {
        echo '<script>';
        echo 'var ' . $_varName . ' = jQuery.parseJSON("' . addslashes(json_encode($_value)) . '");';
        echo '</script>';
    } else {
        echo '<script>';
        echo 'var ' . $_varName . ' = "' . $_value . '";';
        echo '</script>';
    }
}

function resizeImage($contents, $width, $height) {
// Cacul des nouvelles dimensions
    $width_orig = imagesx($contents);
    $height_orig = imagesy($contents);
    $ratio_orig = $width_orig / $height_orig;
    if ($width / $height > $ratio_orig) {
        $dest_width = ceil($height * $ratio_orig);
        $dest_height = $height;
    } else {
        $dest_height = ceil($width / $ratio_orig);
        $dest_width = $width;
    }

    $dest_image = imagecreatetruecolor($width, $height);
    $wh = imagecolorallocate($dest_image, 0xFF, 0xFF, 0xFF);
    imagefill($dest_image, 0, 0, $wh);

    $milieu_dest_x = $width / 2;
    $milieu_dest_y = $height / 2;
    $milieu_source_x = $dest_width / 2;
    $milieu_source_y = $dest_height / 2;
    $offcet_x = $milieu_dest_x - $milieu_source_x;
    $offcet_y = $milieu_dest_y - $milieu_source_y;
    if ($dest_image && $contents) {
        if (!imagecopyresampled($dest_image, $contents, $offcet_x, $offcet_y, 0, 0, $dest_width, $dest_height, $width_orig, $height_orig)) {
            error_log("Error image copy resampled");
            return false;
        }
    }
// start buffering
    ob_start();
    imagejpeg($dest_image);
    $contents = ob_get_contents();
    ob_end_clean();
    return $contents;
}

function getmicrotime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float) $usec + (float) $sec);
}

function redirect($_url, $_forceType = null) {
    switch ($_forceType) {
        case 'JS':
            echo '<script type="text/javascript">';
            echo "window.location.href='$_url';";
            echo '</script>';
            break;
        case 'PHP':
            exit(header("Location: $_url"));
            break;
        default:
            if (headers_sent()) {
                echo '<script type="text/javascript">';
                echo "window.location.href='$_url';";
                echo '</script>';
            } else {
                exit(header("Location: $_url"));
            }
            break;
    }
    return;
}

function convertDuration($time) {
    if ($time >= 86400) {
        $jour = floor($time / 86400);
        $reste = $time % 86400;
        $heure = floor($reste / 3600);
        $reste = $reste % 3600;
        $minute = floor($reste / 60);
        $seconde = $reste % 60;
        $result = $jour . 'j ' . $heure . 'h ' . $minute . 'min ' . $seconde . 's';
    } elseif ($time < 86400 AND $time >= 3600) {
        $heure = floor($time / 3600);
        $reste = $time % 3600;
        $minute = floor($reste / 60);
        $seconde = $reste % 60;
        $result = $heure . 'h ' . $minute . 'min ' . $seconde . ' s';
    } elseif ($time < 3600 AND $time >= 60) {
        $minute = floor($time / 60);
        $seconde = $time % 60;
        $result = $minute . 'min ' . $seconde . 's';
    } elseif ($time < 60) {
        $result = $time . 's';
    }
    return $result;
}

function getClientIp() {
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    }
    return '';
}

function mySqlIsHere() {
    require_once dirname(__FILE__) . '/../class/DB.class.php';
    return is_object(DB::getConnection());
}

function displayExeption($e) {
    $message = '<span id="span_errorMessage">' . $e->getMessage() . '</span>';
    if (DEBUG) {
        $message.='<a class="pull-right bt_errorShowTrace cursor">Show traces</a>';
        $message.='<br/><pre class="pre_errorTrace" style="display : none;">' . print_r($e->getTrace(), true) . '</pre>';
    }
    return $message;
}

function is_json($_string) {
    return ((is_string($_string) && (is_object(json_decode($_string)) || is_array(json_decode($_string))))) ? true : false;
}

function cleanPath($path) {
    $out = array();
    foreach (explode('/', $path) as $i => $fold) {
        if ($fold == '' || $fold == '.')
            continue;
        if ($fold == '..' && $i > 0 && end($out) != '..')
            array_pop($out);
        else
            $out[] = $fold;
    } return ($path{0} == '/' ? '/' : '') . join('/', $out);
}

function getRootPath() {
    return cleanPath(dirname(__FILE__) . '/../../');
}

function hadFileRight($_allowPath, $_path) {
    $path = cleanPath($_path);
    foreach ($_allowPath as $right) {
        if (strpos($right, '/') !== false || strpos($right, '\\') !== false) {
            if (strpos($right, '/') !== 0 || strpos($right, '\\') !== 0) {
                $right = getRootPath() . '/' . $right;
            }
            if (dirname($path) == $right || $path == $right) {
                return true;
            }
        } else {
            if (basename(dirname($path)) == $right || basename($path) == $right) {
                return true;
            }
        }
    }
    return false;
}

function ls($folder = "", $pattern = "*", $recursivly = false, $options = array('files', 'folders')) {
    if ($folder) {
        $current_folder = realpath('.');
        if (in_array('quiet', $options)) { // If quiet is on, we will suppress the 'no such folder' error
            if (!file_exists($folder))
                return array();
        }
        if (!chdir($folder))
            return array();
    }
    $get_files = in_array('files', $options);
    $get_folders = in_array('folders', $options);
    $both = array();
    $folders = array();
    // Get the all files and folders in the given directory.
    if ($get_files)
        $both = glob($pattern, GLOB_BRACE + GLOB_MARK);
    if ($recursivly or $get_folders)
        $folders = glob("*", GLOB_ONLYDIR + GLOB_MARK);

    //If a pattern is specified, make sure even the folders match that pattern.
    $matching_folders = array();
    if ($pattern !== '*')
        $matching_folders = glob($pattern, GLOB_ONLYDIR + GLOB_MARK);

    //Get just the files by removing the folders from the list of all files.
    $all = array_values(array_diff($both, $folders));
    if ($recursivly or $get_folders) {
        foreach ($folders as $this_folder) {
            if ($get_folders) {
                //If a pattern is specified, make sure even the folders match that pattern.
                if ($pattern !== '*') {
                    if (in_array($this_folder, $matching_folders))
                        array_push($all, $this_folder);
                } else
                    array_push($all, $this_folder);
            }

            if ($recursivly) {
                // Continue calling this function for all the folders
                $deep_items = ls($pattern, $this_folder, $recursivly, $options); # :RECURSION:
                foreach ($deep_items as $item) {
                    array_push($all, $this_folder . $item);
                }
            }
        }
    }

    if ($folder)
        chdir($current_folder);

    if (in_array('datetime_asc', $options)) {
        global $current_dir;
        $current_dir = $folder;
        usort($all, function($a, $b) {
            return filemtime($GLOBALS['current_dir'] . '/' . $a) < filemtime($GLOBALS['current_dir'] . '/' . $b);
        });
    }
    if (in_array('datetime_desc', $options)) {
        global $current_dir;
        $current_dir = $folder;
        usort($all, function($a, $b) {
            return filemtime($GLOBALS['current_dir'] . '/' . $a) > filemtime($GLOBALS['current_dir'] . '/' . $b);
        });
    }

    return $all;
}

function getGitRepo() {
    return Git::open(dirname(__FILE__) . '/../..');
}

function removeCR($_string) {
    $_string = str_replace("\n", '', $_string);
    $_string = str_replace("\r\n", '', $_string);
    $_string = str_replace("\r", '', $_string);
    $_string = str_replace("\n\r", '', $_string);
    return trim($_string);
}

function getVersion($_name) {
    global $VERSION;
    if (isset($VERSION[$_name])) {
        return $VERSION[$_name];
    }
    return false;
}

function rcopy($src, $dst, $_emptyDest = true, $_exclude = array()) {
    if ($_emptyDest && file_exists($dst)) {
        rrmdir($dst);
    }
    if (is_dir($src)) {
        if (!file_exists($dst)) {
            mkdir($dst);
        }
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file != "." && $file != ".." && !in_array($file, $_exclude)) {
                if (!rcopy("$src/$file", "$dst/$file", $_exclude)) {
                    return false;
                }
            }
        }
    } else if (file_exists($src)) {
        return copy($src, $dst);
    }
    return true;
}

// removes files and non-empty directories
function rrmdir($dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                rrmdir("$dir/$file");
            }
        }
        if (!rmdir($dir)) {
            return false;
        }
    } else if (file_exists($dir)) {
        return unlink($dir);
    }
    return true;
}

function convertDayEnToFr($_day) {
    if ($_day == 'Monday' || $_day == 'Mon') {
        return 'Lundi';
    }
    if ($_day == 'monday' || $_day == 'mon') {
        return 'lundi';
    }

    if ($_day == 'Thuesday' || $_day == 'Tue') {
        return 'Mardi';
    }
    if ($_day == 'thuesday' || $_day == 'tue') {
        return 'mardi';
    }

    if ($_day == 'Wednesday' || $_day == 'Wed') {
        return 'Mercredi';
    }
    if ($_day == 'wednesday' || $_day == 'wed') {
        return 'mercredi';
    }

    if ($_day == 'Thursday' || $_day == 'Thu') {
        return 'Jeudi';
    }
    if ($_day == 'thursday' || $_day == 'thu') {
        return 'Jeudi';
    }

    if ($_day == 'Friday' || $_day == 'Fri') {
        return 'Vendredi';
    }
    if ($_day == 'friday' || $_day == 'fri') {
        return 'vendredi';
    }

    if ($_day == 'Saturday' || $_day == 'Sat') {
        return 'Samedi';
    }
    if ($_day == 'saturday' || $_day == 'sat') {
        return 'samedi';
    }

    if ($_day == 'Sunday' || $_day == 'Sun') {
        return 'Dimanche';
    }
    if ($_day == 'sunday' || $_day == 'sun') {
        return 'dimanche';
    }

    return $_day;
}

function create_zip($source_arr, $destination) {
    if (is_string($source_arr))
        $source_arr = array($source_arr); // convert it to array

    if (!extension_loaded('zip')) {
        throw new Exception('Extension php ZIP non chargée');
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        throw new Exception('Impossible de creer l\'archive ZIP dans le dossier de destination : ' . $destination);
    }

    foreach ($source_arr as $source) {
        if (!file_exists($source))
            continue;
        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

            foreach ($files as $file) {
                if (strpos($file, $source) === false) {
                    continue;
                }
                if ($file == $source . '/..') {
                    continue;
                }
                if ($file == $source . '/.') {
                    continue;
                }
                $file = str_replace('\\', '/', realpath($file));
                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            $zip->addFromString(basename($source), file_get_contents($source));
        }
    }

    return $zip->close();
}
