
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


exports.action = function(data, callback, config, SARAH) {
    var debug = true;
    config = config.plugins.jeedom;
    if (data.method == 'execute') {
        log('--------Execute--------');
        var jsonrpc = generateJsonRpc();
        jsonrpc.method = 'execute';
        for (var i in data) {
            jsonrpc.params[i] = data[i];
        }
        sendJsonRequest(jsonrpc, readReturn);
    }else if (data.method == 'update') {
        log('--------Update--------');
        var jsonrpc = generateJsonRpc();
        jsonrpc.method = 'updateXml';
        sendJsonRequest(jsonrpc, updateXml);
    }else{
         callback({tts: 'Aucune méthode correspondance'});
    }

    function updateXml(_xml) {
        log('Ecriture du fichier xml');
        var fs = require('fs');
        fs.writeFile("plugins/jeedom/jeedom.xml", _xml, function(err) {
            if (err) {
                callback({tts: err});
            } else {
                callback({tts: 'Mise à jour du xml réussi'});
            }
        });
    }

    function readReturn(_return) {
        callback({tts: _return});
    }

    function processReturn(_return, intCallback) {
        if (_return === false) {
            callback({tts: 'Echec de la requete à jeedom (retour=faux)'});
        }
        if (isset(_return.error)) {
            if (isset(_return.error.message)) {
                callback({tts: _return.error.message});
            } else {
                callback({tts: 'Echec de la requete à jeedom (no return message'});
            }
        } else {
            log('-------REQUEST SUCCESS-------');
            intCallback(_return.result);
        }
    }

    function sendJsonRequest(_jsonrpc, intCallback) {
        log('Adresse : ' + config.addrJeedom + '/core/api/jeeApi.php');
        log('Request :');
        log(_jsonrpc);
        var request = require('request');
        request({
            url: config.addrJeedom + '/core/api/jeeApi.php',
            method: 'POST',
            form: {request: JSON.stringify(_jsonrpc)}
        },
        function(err, response, json) {
            if (err || response.statusCode != 200) {
                processReturn(false, intCallback);
                return 0;
            }
            processReturn(JSON.parse(json), intCallback);
            return 0;
        });
    }

    function log(_input) {
        if (debug) {
            console.log(_input);
        }
    }

    function generateJsonRpc() {
        var jsonrpc = {};
        jsonrpc.id = Math.floor(Math.random() * 10001);
        jsonrpc.params = {};
        jsonrpc.params.api = config.apikeyJeedom;
        jsonrpc.params.plugin = 'sarah';
        jsonrpc.jsonrpc = '2.0';
        return jsonrpc;
    }

    function isset() {
        var a = arguments,
                l = a.length,
                i = 0,
                undef;

        if (l === 0) {
            throw new Error('Empty isset');
        }

        while (i !== l) {
            if (a[i] === undef || a[i] === null) {
                return false;
            }
            i++;
        }
        return true;
    }
};
