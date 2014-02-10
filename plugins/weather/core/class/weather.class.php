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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class weather extends eqLogic {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */

    public static function getIconFromCondition($_condition) {
        switch ($_condition) {
            case 'Partiellement nuageux':
                return 'H';
            case 'Nuageux':
                return 'N';
            case 'Partiellement nuageux / vent':
                return 'I';
            case 'Venteux':
                return 'F';
            case 'Ensoleillé':
                return 'B';
            case 'Passable':
                return 'A';
            case 'Averse':
                return 'R';
            case 'Pluie faible':
                return 'Q';
            case 'Pluie':
                return 'R';
            case 'Brouillard':
                return 'M';
            default:
                return '';
        }
    }

    public static function convertCondition($_condition) {
        switch ($_condition) {
            case 'Partly Cloudy':
                return 'Partiellement nuageux';
            case 'Mostly Cloudy':
                return 'Nuageux';
            case 'Cloudy':
                return 'Nuageux';
            case 'Partly Cloudy/Wind':
                return 'Partiellement nuageux / vent';
            case 'Fair/Windy':
                return 'Venteux';
            case 'Sunny':
                return 'Ensoleillé';
            case 'Fair':
                return 'Passable';
            case 'Showers':
                return 'Averse';
            case 'Light Rain':
                return 'Pluie faible';
            case 'Rain':
                return 'Pluie';
            case 'Fog':
                return 'Brouillard';
            case 'Scattered Showers':
                return 'Peu nuageux';
            case 'AM Showers':
                return 'Pluie l\'après-midi';
            default:
                return $_condition;
        }
    }

    /*     * *********************Methode d'instance************************* */

    public function preUpdate() {
        if ($this->getConfiguration('city') == '') {
            throw new Exception('L\identifiant de la ville ne peut être vide');
        }
    }

    public function postInsert() {
        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Température');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '-1');
        $weatherCmd->setConfiguration('data', 'temp');
        $weatherCmd->setUnite('°C');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Humidité');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '-1');
        $weatherCmd->setConfiguration('data', 'humidity');
        $weatherCmd->setUnite('%');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Pression');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '-1');
        $weatherCmd->setConfiguration('data', 'pressure');
        $weatherCmd->setUnite('Pa');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Condition Actuelle');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '-1');
        $weatherCmd->setConfiguration('data', 'condition');
        $weatherCmd->setUnite('');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('string');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Vitesse du vent');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '-1');
        $weatherCmd->setConfiguration('data', 'wind_speed');
        $weatherCmd->setUnite('km/h');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Direction du vent');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '-1');
        $weatherCmd->setConfiguration('data', 'wind_direction');
        $weatherCmd->setUnite('');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('string');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Lever du soleil');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '-1');
        $weatherCmd->setConfiguration('data', 'sunrise');
        $weatherCmd->setUnite('');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Coucher du soleil');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '-1');
        $weatherCmd->setConfiguration('data', 'sunset');
        $weatherCmd->setUnite('');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Température Min');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '0');
        $weatherCmd->setConfiguration('data', 'low');
        $weatherCmd->setUnite('°C');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Température Max');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '0');
        $weatherCmd->setConfiguration('data', 'high');
        $weatherCmd->setUnite('°C');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Condition');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '0');
        $weatherCmd->setConfiguration('data', 'condition');
        $weatherCmd->setUnite('');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('string');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Température Min +1');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '1');
        $weatherCmd->setConfiguration('data', 'low');
        $weatherCmd->setUnite('°C');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Température Max +1');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '1');
        $weatherCmd->setConfiguration('data', 'high');
        $weatherCmd->setUnite('°C');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('numeric');
        $weatherCmd->save();

        $weatherCmd = new weatherCmd();
        $weatherCmd->setName('Condition +1');
        $weatherCmd->setEqLogic_id($this->id);
        $weatherCmd->setConfiguration('day', '1');
        $weatherCmd->setConfiguration('data', 'condition');
        $weatherCmd->setUnite('');
        $weatherCmd->setType('info');
        $weatherCmd->setSubType('string');
        $weatherCmd->save();
    }

    public function toHtml($_version = 'dashboard', $_withValue = true) {
        if ($this->getIsEnable() != 1) {
            return '';
        }
        $weather = $this->getWeatherArea();
        if (!is_array($weather)) {
            return false;
        }
        $replace = array(
            '#icone#' => self::getIconFromCondition($weather['condition']['text']),
            '#city#' => $weather['location']['city'],
            '#condition#' => $weather['condition']['text'],
            '#temperature#' => $weather['condition']['temperature'],
            '#windspeed#' => $weather['wind']['speed'],
            '#humidity#' => $weather['atmosphere']['humidity'],
            '#pressure#' => $weather['atmosphere']['pressure'],
            '#sunrise#' => $weather['astronomy']['sunrise'],
            '#sunset#' => $weather['astronomy']['sunset'],
            '#eqLink#' => $this->getLinkToConfiguration(),
        );
        $return = template_replace($replace, getTemplate('core', $_version, 'current', 'weather'));
        $i = 0;
        foreach ($weather['forecast'] as $forecast) {
            $replace = array(
                '#day#' => $forecast['day'],
                '#condition#' => $forecast['condition'],
                '#low_temperature#' => $forecast['low_temperature'],
                '#hight_temperature#' => $forecast['high_temperature'],
            );
            $return .= template_replace($replace, getTemplate('core', $_version, 'forecast', 'weather'));
        }
        return $return;
    }

    public function getShowOnChild() {
        return true;
    }

    private static function parseXmlWeather($xml) {
        $weather = simplexml_load_string($xml);
        $channel_yweather = $weather->channel->children("http://xml.weather.yahoo.com/ns/rss/1.0");
        foreach ($channel_yweather as $x => $channel_item) {
            foreach ($channel_item->attributes() as $k => $attr) {
                $yw_channel[$x][$k] = $attr;
            }
        }
        $item_yweather = $weather->channel->item->children("http://xml.weather.yahoo.com/ns/rss/1.0");
        foreach ($item_yweather as $x => $yw_item) {
            foreach ($yw_item->attributes() as $k => $attr) {
                if ($k == 'day') {
                    $day = $attr;
                }
                if ($x == 'forecast') {
                    $yw_forecast[$x][$day . ''][$k] = $attr;
                } else {
                    $yw_forecast[$x][$k] = $attr;
                }
            }
        }

        $return = array();
        $return['condition']['text'] = (string) $yw_forecast['condition']['text'][0];
        $return['condition']['text'] = self::convertCondition($return['condition']['text']);

        $return['condition']['temperature'] = (string) $yw_forecast['condition']['temp'][0];
        $return['location']['city'] = (string) $yw_channel['location']['city'][0];
        $return['atmosphere']['humidity'] = (string) $yw_channel['atmosphere']['humidity'][0];
        $return['atmosphere']['pressure'] = (string) $yw_channel['atmosphere']['pressure'][0];
        $return['wind']['speed'] = (string) $yw_channel['wind']['speed'][0];
        $return['wind']['direction'] = (string) $yw_channel['wind']['direction'][0];

        $return['astronomy']['sunrise'] = (string) $yw_channel['astronomy']['sunrise'][0];
        $return['astronomy']['sunrise'] = date("Hi", strtotime($return['astronomy']['sunrise']));

        $return['astronomy']['sunset'] = (string) $yw_channel['astronomy']['sunset'][0];
        $return['astronomy']['sunset'] = date("Hi", strtotime($return['astronomy']['sunset']));
        $day = 0;
        foreach ($yw_forecast['forecast'] as $forecast) {
            $return['forecast'][$day]['day'] = (string) $forecast['day'][0];
            $return['forecast'][$day]['day'] = convertDayEnToFr($return['forecast'][$day]['day']);
            
            $return['forecast'][$day]['condition'] = (string) $forecast['text'][0];
            $return['forecast'][$day]['condition'] = self::convertCondition($return['forecast'][$day]['condition']);
            $return['forecast'][$day]['low_temperature'] = (string) $forecast['low'][0];
            $return['forecast'][$day]['high_temperature'] = (string) $forecast['high'][0];
            $day++;
        }
        return $return;
    }

    /*     * **********************Getteur Setteur*************************** */

    public function getWeatherArea() {
        return $this->getWeatherFromYahooXml();
    }

    public function getWeatherFromYahooXml() {
        if ($this->getConfiguration('city') == '') {
            return false;
        }
        $cache = cache::byKey('yahooWeatherXml' . $this->getConfiguration('city'));
        if ($cache->getValue() === '' || $cache->getValue() == 'false') {
            $request = new com_http('http://weather.yahooapis.com/forecastrss?w=' . urlencode($this->getConfiguration('city')) . '&u=c');
            $xmlMeteo = $request->exec(5000, 0);
            cache::set('yahooWeatherXml' . $this->getConfiguration('city'), $xmlMeteo, 7200);
        } else {
            $xmlMeteo = $cache->getValue();
        }
        return self::parseXmlWeather($xmlMeteo);
    }

}

class weatherCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function dontRemoveCmd() {
        return true;
    }

    public function execute($_options = array()) {
        $eqLogic_weather = weather::byId($this->eqLogic_id);
        $weather = $eqLogic_weather->getWeatherArea();

        if (!is_array($weather)) {
            return false;
        }

        if ($this->getConfiguration('day') == -1) {
            if ($this->getConfiguration('data') == 'condition') {
                return $weather['condition']['text'];
            }
            if ($this->getConfiguration('data') == 'temp') {
                return $weather['condition']['temperature'];
            }
            if ($this->getConfiguration('data') == 'humidity') {
                return $weather['atmosphere']['humidity'];
            }
            if ($this->getConfiguration('data') == 'pressure') {
                return $weather['atmosphere']['pressure'];
            }
            if ($this->getConfiguration('data') == 'wind_speed') {
                return $weather['wind']['speed'];
            }
            if ($this->getConfiguration('data') == 'wind_direction') {
                return $weather['wind']['direction'];
            }
            if ($this->getConfiguration('data') == 'sunrise') {
                return $weather['astronomy']['sunrise'];
            }
            if ($this->getConfiguration('data') == 'sunset') {
                return $weather['astronomy']['sunset'];
            }
        }

        if ($this->getConfiguration('data') == 'condition') {
            return $weather['forecast'][$this->getConfiguration('day')]['condition'];
        }
        if ($this->getConfiguration('data') == 'low') {
            return $weather['forecast'][$this->getConfiguration('day')]['low_temperature'];
        }
        if ($this->getConfiguration('data') == 'high') {
            return $weather['forecast'][$this->getConfiguration('day')]['high_temperature'];
        }
        return false;
    }

    /*     * **********************Getteur Setteur*************************** */
}

?>