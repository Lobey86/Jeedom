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

class gCalendar extends eqLogic {
    
}

class gCalendarCmd extends cmd {
    /*     * *************************Attributs****************************** */


    /*     * ***********************Methode static*************************** */


    /*     * *********************Methode d'instance************************* */

    public function preSave() {
        if ($this->getConfiguration('calendarUrl') == '') {
            throw new Exception('L\'url de l\'agenda ne peut etre vide');
        }
    }

    public function execute($_options = array()) {
        try {
            $oAgenda = new GoogleAgenda($this->getConfiguration('calendarUrl'));
            // Le tableau d'options suivant contient les valeurs par défaut
            $aEvents = $oAgenda->getEvents(array(
                'startmin' => date('Y-m-d'),
                'startmax' => '',
                'sortorder' => 'ascending',
                'orderby' => 'starttime',
                'maxresults' => '5',
                'startindex' => '1',
                'search' => '',
                'singleevents' => 'true',
                'futureevents' => 'false',
                'timezone' => 'Europe/Paris',
                'showdeleted' => 'false'
            ));
            $date = date('Y-m-d H:i:s');
            $result = '';
            foreach ($aEvents as $oEvent) {
                if ($oEvent->getStartDate() < $date && $oEvent->getEndDate() > $date) {
                    $result .= $oEvent->getTitle() . ' - ';
                }
            }
            return trim($result, ' - ');
        } catch (GoogleAgendaException $e) {
            throw $e;
        }
        return false;
    }

    /*     * **********************Getteur Setteur*************************** */
}

/**
 * Classe d'entité d'évènement Google Agenda
 * @author Shivato Web
 * @version 1.0
 *
 */
class GoogleAgendaEvent {
    /*     * *************************Attributs****************************** */

    protected $_sTitle;
    protected $_dStartDate;
    protected $_dEndDate;
    protected $_sAddress;
    protected $_sDescription;
    protected $_sAuthorName;
    protected $_sAuthorEmail;
    protected $_dPublishedDate;
    protected $_dUpdatedDate;
    protected $_sUrlDetail;
    protected $_aPersons = array();
    protected $_aReminders = array();
    protected $_dOriginalDate;
    protected $_bRecurs = false;

    /*     * ***********************Methode static*************************** */

    /**
     * Constructeur
     * @return void
     */
    public function __construct() {
        
    }

    /**
     * setteur titre
     * @param string $sTitle
     * @return void
     */
    public function setTitle($sTitle) {
        $this->_sTitle = $sTitle;
    }

    /**
     * getteur titre
     * @return string
     */
    public function getTitle() {
        return $this->_sTitle;
    }

    /**
     * setteur date de début
     * @param date $dStartDate
     * @return void
     */
    public function setStartDate($dStartDate) {
        $this->_dStartDate = $dStartDate;
    }

    /**
     * getteur date de début
     * @return date
     */
    public function getStartDate() {
        return $this->_dStartDate;
    }

    /**
     * setteur date de fin
     * @param date $dEndDate
     * @return void
     */
    public function setEndDate($dEndDate) {
        $this->_dEndDate = $dEndDate;
    }

    /**
     * getteur date de fin
     * @return date
     */
    public function getEndDate() {
        return $this->_dEndDate;
    }

    /**
     * setteur adresse
     * @param string $sAddress
     * @return void
     */
    public function setAddress($sAddress) {
        $this->_sAddress = $sAddress;
    }

    /**
     * getteur adresse
     * @return string
     */
    public function getAddress() {
        return $this->_sAddress;
    }

    /**
     * setteur description
     * @param string $sDescription
     * @return void
     */
    public function setDescription($sDescription) {
        $this->_sDescription = $sDescription;
    }

    /**
     * getteur description
     * @return string
     */
    public function getDescription() {
        return $this->_sDescription;
    }

    /**
     * setteur date de publication
     * @param date $dPublishedDate
     * @return void
     */
    public function setPublishedDate($dPublishedDate) {
        $this->_dPublishedDate = $dPublishedDate;
    }

    /**
     * getteur date de publication
     * @return date
     */
    public function getPublishedDate() {
        return $this->_dPublishedDate;
    }

    /**
     * setteur date de modification
     * @param date $dModifiedDate
     * @return void
     */
    public function setUpdatedDate($dUpdatedDate) {
        $this->_dUpdatedDate = $dUpdatedDate;
    }

    /**
     * getteur date de modification
     * @return date
     */
    public function getUpdatedDate() {
        return $this->_dUpdatedDate;
    }

    /**
     * setteur url détail
     * @param string $sUrlDetail
     * @return void
     */
    public function setUrlDetail($sUrlDetail) {
        $this->_sUrlDetail = $sUrlDetail;
    }

    /**
     * getteur url détail
     * @return string
     */
    public function getUrlDetail() {
        return $this->_sUrlDetail;
    }

    /**
     * setteur du nom de l'auteur de l'évènement
     * @param string $sAuthorName
     * @return void
     */
    public function setAuthorName($sAuthorName) {
        $this->_sAuthorName = $sAuthorName;
    }

    /**
     * getteur du nom de l'auteur de l'évènement
     * @return string
     */
    public function getAuthorName() {
        return $this->_sAuthorName;
    }

    /**
     * setteur du mail de l'auteur de l'évènement
     * @param string $sAuthorEmail
     * @return void
     */
    public function setAuthorEmail($sAuthorEmail) {
        $this->_sAuthorEmail = $sAuthorEmail;
    }

    /**
     * getteur du mail de l'auteur de l'évènement
     * @return string
     */
    public function getAuthorEmail() {
        return $this->_sAuthorEmail;
    }

    /**
     * setteur des personnes attaché à l'évènement
     * @param array $aPersons
     * @return void
     */
    public function setPersons(array $aPersons) {
        $this->_aPersons = $aPersons;
    }

    /**
     * getteur des personnes attaché à l'évènement
     * retourne un tableau d'objet de type stdClass() : $aPersons[0]->name, $aPersons[0]->email, $aPersons[0]->role, $aPersons[0]->status
     * @return array
     */
    public function getPersons() {
        return $this->_aPersons;
    }

    /**
     * setteur des rappels attaché à l'évènement
     * @param array $aReminders
     * @return void
     */
    public function setReminders(array $aReminders) {
        $this->_aReminders = $aReminders;
    }

    /**
     * getteur des rappels attaché à l'évènement
     * retourne un tableau d'objet de type stdClass() : $aReminders[0]->type, $aReminders[0]->minutes
     * @return array
     */
    public function getReminders() {
        return $this->_aReminders;
    }

    /**
     * setteur date d'origine
     * @param date $dDate
     * @return void
     */
    public function setOriginalDate($dOriginalDate) {
        $this->_dOriginalDate = $dOriginalDate;
    }

    /**
     * getteur date d'origine
     * @return date
     */
    public function getOriginalDate() {
        return $this->_dOriginalDate;
    }

    /**
     * setteur évènement récurrent
     * @param bool $bRecurs
     * @return void
     */
    public function setRecurs($bRecurs) {
        $this->_bRecurs = $bRecurs;
    }

    /**
     * getteur évènement récurrent
     * @return bool
     */
    public function getRecurs() {
        return $this->_bRecurs;
    }

}

/**
 * Classe de lecture d'un agenda Google
 * @author Shivato Web
 * @version 1.0
 * @link http://www.shivato-web.com/blog/php/tuto-classe-de-parsing-google-agenda-en-php
 * @example :
 * $oAgenda = new GoogleAgenda($sFeed);
 * $aEvents = $oAgenda->getEvents($aOptions);
 * $oAgenda->getTitle();
 * foreach ($aEvents as $oEvent) {
 *      $oEvent->getTitle();
 *      $oEvent->getStartDate();
 *      $oEvent->getEndDate();
 *      $oEvent->getAddress();
 *      $oEvent->getDescription();
 * }
 * $aEventsNext = $oAgenda->getNextEvents();
 * $aEventsPrevious = $oAgenda->getPreviousEvents(); $aEventsPrevious == $aEvents
 *
 * Les urls sont accessibles si on est logué sur le bon compte de l'agenda ou si l'agenda a été rendu public
 */
class GoogleAgenda {

    //variables interne de la classe
    protected $_sFeed;
    protected $_dStartMin;
    protected $_dStartMax;
    protected $_sSortorder;
    protected $_sOrderby;
    protected $_iMaxResults;
    protected $_iStartIndex;
    protected $_sUrlNext;
    protected $_sUrlPrevious;
    protected $_aEvents;
    protected $_sSearch;
    protected $_bSingleEvents;
    protected $_bFutureEvents;
    protected $_sTimezone;
    protected $_bShowDeleted;
    //variables disponible
    protected $_dUpdatedDate;
    protected $_sTitle;
    protected $_sSubtitle;
    protected $_sUrlPublic;
    protected $_sAuthorName;
    protected $_sAuthorEmail;

    const MAX_RESULTS_DEFAULT = 5;

    /**
     * Définie l'agenda avec lequel on travail
     * @param string $sFeed url de l'agenda
     * @param bool $bFull permet d'avoir toutes les variables rempli séparément, sinon met adresse, date... dans description (default true)
     * @return void
     * @throws GoogleAgendaException si l'url n'est pas valide
     */
    public function __construct($sFeed, $bFull = true) {
        if ($bFull) {
            $sFeed = mb_strrchr($sFeed, 'basic', true) . 'full';
        }

        $sFeedContent = @file_get_contents($sFeed);
        if ($sFeedContent !== false && !empty($sFeedContent)) {
            $this->_sFeed = $sFeed;
        } else {
            throw new GoogleAgendaException('L\'url [' . $sFeed . '] n\'est pas valide.');
        }
    }

    /**
     * getteur de la date de maj de l'agenda
     * @return date
     */
    public function getUpdatedDate() {
        return $this->_dUpdatedDate;
    }

    /**
     * getteur du titre de l'agenda
     * @return string
     */
    public function getTitle() {
        return $this->_sTitle;
    }

    /**
     * getteur du sous titre de l'agenda
     * @return string
     */
    public function getSubtitle() {
        return $this->_sSubtitle;
    }

    /**
     * getteur de l'url public de l'agenda
     * @return string
     */
    public function getUrlPublic() {
        return $this->_sUrlPublic;
    }

    /**
     * getteur du nom de l'auteur de l'agenda
     * @return string
     */
    public function getAuthorName() {
        return $this->_sAuthorName;
    }

    /**
     * getteur de l'adresse email de l'auteur de l'agenda
     * @return string
     */
    public function getAuthorEmail() {
        return $this->_sAuthorEmail;
    }

    /**
     * Getteur des évènements selon les paramètres
     * Options :
     * (date Y-m-d) startmin : date du début de la lecture (default : date du jour)
     * (date Y-m-d) startmax : date de la fin de la lecture (ne prend pas les évènement de la date) (default : null)
     * (string) sortorder : tri des évènements, options disponible : ascending, descending (default : ascending)
     * (string) orderby : ordre des évènements, options disponible : starttime, lastmodified (default : starttime)
     * (int) maxresults : nombre d'évènements retournés (default : self::MAX_RESULTS_DEFAULT)
     * (int) startindex : page de résultat de la lecture (default : 1)
     * (string) search : texte recherché dans les évènements (default : null)
     * (string) singleevents : prend les évènements récurrents à leur date, sinon toutes les dates suivantes sont dans le premier évènement récurrent trouvé
     *               (déconseillé, ne marche pas vraiment bien), options : 'true', 'false' (default : 'true')
     * (string) futureevents : ne prend que les évènements à venir ou prend aussi ceux déjà passé de la première journée, options : 'true', 'false' (default : 'false')
     * (string) timezone : défini le fuseau horaire (default : Europe/Paris)
     * (string) showdeleted : prend en compte les évènements supprimés, options : 'true', 'false' (default : 'false')
     * @param array $aOptions (options : startmin, startmax, sortorder, orderby, maxresults, startindex, search, singleevents, futureevents, timezone, showdeleted)
     * @return array tableau d'objets des évènements de l'agenda
     * @link options : http://code.google.com/intl/fr/apis/calendar/data/2.0/reference.html#Parameters
     * @link options : http://code.google.com/intl/fr/apis/gdata/docs/2.0/reference.html#Queries
     */
    public function getEvents(array $aOptions = array()) {
        //récupération des options
        $this->_dStartMin = isset($aOptions['startmin']) ? $aOptions['startmin'] : date('Y-m-d');
        $this->_dStartMax = isset($aOptions['startmax']) ? $aOptions['startmax'] : '';
        $this->_sSortorder = isset($aOptions['sortorder']) ? $aOptions['sortorder'] : 'ascending';
        $this->_sOrderby = isset($aOptions['orderby']) ? $aOptions['orderby'] : 'starttime';
        $this->_iMaxResults = isset($aOptions['maxresults']) ? $aOptions['maxresults'] : self::MAX_RESULTS_DEFAULT;
        $this->_iStartIndex = isset($aOptions['startindex']) ? $aOptions['startindex'] : 1;
        $this->_sSearch = isset($aOptions['search']) ? $aOptions['search'] : '';
        $this->_bSingleEvents = isset($aOptions['singleevents']) ? $aOptions['singleevents'] : 'true';
        $this->_bFutureEvents = isset($aOptions['futureevents']) ? $aOptions['futureevents'] : 'false';
        $this->_sTimezone = isset($aOptions['timezone']) ? $aOptions['timezone'] : 'Europe/Paris';
        $this->_bShowDeleted = isset($aOptions['showdeleted']) ? $aOptions['showdeleted'] : 'false';

        //construction de l'url avec les options reçus
        $sUrl = $this->_sFeed . '?' .
                (!empty($this->_dStartMin) ? 'start-min=' . $this->_dStartMin . '&' : '' ) .
                (!empty($this->_dStartMax) ? 'start-max=' . $this->_dStartMax . '&' : '' ) .
                (!empty($this->_sSortorder) ? 'sortorder=' . $this->_sSortorder . '&' : '' ) .
                (!empty($this->_sOrderby) ? 'orderby=' . $this->_sOrderby . '&' : '' ) .
                (!empty($this->_iMaxResults) ? 'max-results=' . $this->_iMaxResults . '&' : '' ) .
                (!empty($this->_iStartIndex) ? 'start-index=' . $this->_iStartIndex . '&' : '' ) .
                (!empty($this->_sSearch) ? 'q=' . $this->_sSearch . '&' : '' ) .
                (!empty($this->_bSingleEvents) ? 'singleevents=' . $this->_bSingleEvents . '&' : '' ) .
                (!empty($this->_bFutureEvents) ? 'futureevents=' . $this->_bFutureEvents . '&' : '' ) .
                (!empty($this->_sTimezone) ? 'ctz=' . $this->_sTimezone . '&' : '' ) .
                (!empty($this->_bShowDeleted) ? 'showdeleted=' . $this->_bShowDeleted . '&' : '' );

        $this->loadUrl($sUrl);

        return $this->_aEvents;
    }

    /**
     * Getteur des évènements suivants avec les mêmes paramètres
     * @return array tableau d'objets des évènements de l'agenda, un tableau vide si l'url n'existe pas
     */
    public function getNextEvents() {
        if (!empty($this->_sUrlNext)) {
            $this->loadUrl($this->_sUrlNext);
            return $this->_aEvents;
        } else {
            return array();
        }
    }

    /**
     * Getteur des évènements précédents avec les mêmes paramètres
     * Utilisable si la fonction getNextEvents() a été utilisés ou si l'option start-index > 1 a été utilisé
     * @return array tableau d'objets des évènements de l'agenda, un tableau vide si l'url n'existe pas
     */
    public function getPreviousEvents() {
        if (!empty($this->_sUrlPrevious)) {
            $this->loadUrl($this->_sUrlPrevious);
            return $this->_aEvents;
        } else {
            return array();
        }
    }

    /**
     * Charge l'url du flux xml de l'agenda et rempli les valeurs de l'instance correspondant à l'agenda
     * @param string $sUrl
     * @return void
     */
    protected function loadUrl($sUrl) {
        $this->_aEvents = array();

        //lecture du fichier XML
        $oXml = simplexml_load_file($sUrl);
        if ($oXml !== false) {
            $this->_dUpdatedDate = isset($oXml->updated) ? date('Y-m-d H:i:s', strtotime($oXml->updated)) : '';
            $this->_sTitle = isset($oXml->title) ? (string) $oXml->title : '';
            $this->_sSubtitle = isset($oXml->subtitle) ? (string) $oXml->subtitle : '';
            $this->_sAuthorName = isset($oXml->author) && isset($oXml->author->name) ? (string) $oXml->author->name : '';
            $this->_sAuthorEmail = isset($oXml->author) && isset($oXml->author->email) ? (string) $oXml->author->email : '';
            $this->_sUrlPublic = '';
            $this->_sUrlNext = '';
            $this->_sUrlPrevious = '';
            if (isset($oXml->link)) {
                foreach ($oXml->link as $oLink) {
                    if ($oLink->attributes()->rel == 'alternate') {
                        $this->_sUrlPublic = (string) $oLink->attributes()->href;
                    } else if ($oLink->attributes()->rel == 'next') {
                        $this->_sUrlNext = (string) $oLink->attributes()->href;
                    } else if ($oLink->attributes()->rel == 'previous') {
                        $this->_sUrlPrevious = (string) $oLink->attributes()->href;
                    }
                }
            }

            if (isset($oXml->entry)) {
                foreach ($oXml->entry as $oDataEvent) {
                    $this->setEvent($oDataEvent);
                }
            }
        }
    }

    /**
     * Crée un nouvel objet GoogleAgendaEvent et l'affecte au tableau d'évènements
     * @param SimpleXMLElement $oData
     * @return void
     */
    protected function setEvent(SimpleXMLElement $oData) {
        $oEvent = new GoogleAgendaEvent();
        $oDataChild = $oData->children('http://schemas.google.com/g/2005');

        $oEvent->setTitle(isset($oData->title) ? (string) $oData->title : '');
        $oEvent->setPublishedDate(isset($oData->published) ? date('Y-m-d H:i:s', strtotime($oData->published)) : '');
        $oEvent->setUpdatedDate(isset($oData->updated) ? date('Y-m-d H:i:s', strtotime($oData->updated)) : '');
        $oEvent->setAuthorName(isset($oData->author) && isset($oData->author->name) ? (string) $oData->author->name : '');
        $oEvent->setAuthorEmail(isset($oData->author) && isset($oData->author->email) ? (string) $oData->author->email : '');
        $oEvent->setDescription(isset($oData->content) ? (string) $oData->content : '');
        $oEvent->setAddress(isset($oDataChild->where) ? (string) $oDataChild->where->attributes()->valueString : '');

        if (isset($oData->link)) {
            foreach ($oData->link as $oLink) {
                if ($oLink->attributes()->rel == 'alternate') {
                    $oEvent->setUrlDetail((string) $oLink->attributes()->href);
                    break;
                }
            }
        }

        if (isset($oDataChild->who)) {
            $aPersons = array();
            foreach ($oDataChild->who as $oWho) {
                $aPersons[] = $this->parsePerson($oWho);
            }
            $oEvent->setPersons($aPersons);
        }

        if (isset($oDataChild->originalEvent)) {
            $oEvent->setOriginalDate((string) $oDataChild->originalEvent->when->attributes()->startTime);
        }

        if (isset($oDataChild->when)) {
            $oEvent->setStartDate(date('Y-m-d H:i:s', strtotime($oDataChild->when->attributes()->startTime)));
            $oEvent->setEndDate(date('Y-m-d H:i:s', strtotime($oDataChild->when->attributes()->endTime)));

            if (isset($oDataChild->when->reminder)) {
                $aReminders = array();
                foreach ($oDataChild->when->reminder as $oReminder) {
                    $oReminderEvent = new stdClass();
                    $oReminderEvent->type = (string) $oReminder->attributes()->method;
                    $oReminderEvent->minutes = (string) $oReminder->attributes()->minutes;
                    $aReminders[] = $oReminderEvent;
                }
                $oEvent->setReminders($aReminders);
            }
        }

        if (isset($oDataChild->recurrence)) {
            $oEvent->setRecurs(true);
        }

        $this->_aEvents[] = $oEvent;
    }

    /**
     * Parse les informations des personnes participantes
     * @param SimpleXMLElement $oPerson
     * @return stdClass
     */
    protected function parsePerson(SimpleXMLElement $oPerson) {
        if ($oPerson->attributes()->rel == 'http://schemas.google.com/g/2005#event.organizer') {
            $sRole = 'Organisateur';
        } else {
            $sRole = 'Invité';
        }

        if (isset($oPerson->attendeeStatus)) {
            switch ($oPerson->attendeeStatus->attributes()->value) {
                case 'http://schemas.google.com/g/2005#event.accepted' :
                    $sStatus = 'Présent';
                    break;
                case 'http://schemas.google.com/g/2005#event.invited' :
                    $sStatus = 'Invité';
                    break;
                case 'http://schemas.google.com/g/2005#event.declined' :
                    $sStatus = 'Absent';
                    break;
                case 'http://schemas.google.com/g/2005#event.tentative' :
                    $sStatus = 'Peut-être';
                    break;
                default :
                    $sStatus = 'Présent';
            }
        } else {
            $sStatus = 'Présent';
        }

        $oPersonEvent = new stdClass();
        $oPersonEvent->name = (string) $oPerson->attributes()->valueString;
        $oPersonEvent->email = (string) $oPerson->attributes()->email;
        $oPersonEvent->role = $sRole;
        $oPersonEvent->status = $sStatus;
        return $oPersonEvent;
    }

}

class GoogleAgendaException extends Exception {
    
}

?>
