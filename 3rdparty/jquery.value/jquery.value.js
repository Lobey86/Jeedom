
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


(function($) {
    function init(_value, _default) {
        if (!isset(_default)) {
            _default = '';
        }
        if (!isset(_value)) {
            return _default;
        }
        return _value;
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

    jQuery.fn.findAtDepth = function(selector, maxDepth) {
        var depths = [], i;

        if (maxDepth > 0) {
            for (i = 1; i <= maxDepth; i++) {
                depths.push('> ' + new Array(i).join('* > ') + selector);
            }

            selector = depths.join(', ');
            return this.find(selector).first();
        }
        return this.find(selector);
    };


    $.fn.value = function(_value) {
        if (isset(_value)) {
            if ($(this).length > 1) {
                $(this).each(function() {
                    $(this).value(_value);
                });
            } else {
                if ($(this).is('input')) {
                    if ($(this).attr('type') == 'checkbox') {
                        $(this).prop('checked', (init(_value) == 1) ? true : false);
                    } else {
                        $(this).val(init(_value));
                    }
                }
                if ($(this).is('select')) {
                    if (init(_value) == '') {
                        $(this).find('option:first').prop('selected', true);
                    } else {
                        $(this).val(init(_value));
                    }
                }
                if ($(this).is('textarea')) {
                    $(this).val(init(_value));
                }
                if ($(this).is('span') || $(this).is('div') || $(this).is('p')) {
                    $(this).html(init(_value));
                }
                if ($(this).is('pre')) {
                    $(this).html(init(_value));
                }
                $(this).trigger('change');
            }
        } else {
            var value = '';
            if ($(this).is('input') || $(this).is('select') || $(this).is('textarea')) {
                if ($(this).attr('type') == 'checkbox') {
                    value = ($(this).is(':checked')) ? '1' : '0';
                } else {
                    value = $(this).val();
                }
            }
            if ($(this).is('div') || $(this).is('span') || $(this).is('p')) {
                value = $(this).text();
            }
            if ($(this).is('a') && $(this).attr('value') != undefined) {
                value = $(this).attr('value');
            }
            if (value == '') {
                value = $(this).val();
            }

            if ($(this).prop('notEmpty') && $.trim(value) == '') {
                throw('Le champ ' + $(this).attr('key') + ' ne peut etre vide');
            }
            if ($(this).prop('mustNumber') && isNaN(value)) {
                throw('Le champ ' + $(this).attr('key') + ' doit etre un nombre');
            }
            return value;

        }
    };

    $.fn.getValues = function(_attr, _depth) {
        var values = [];
        if ($(this).length > 1) {
            $(this).each(function() {
                var value = {};
                $(this).findAtDepth(_attr, init(_depth, 0)).each(function() {
                    try {
                        var elValue = JSON.parse($(this).value());
                    } catch (e) {
                        var elValue = $(this).value();
                    }
                    if ($(this).attr('data-l1key') != undefined && $(this).attr('data-l1key') != '') {
                        var l1key = $(this).attr('data-l1key');
                        if ($(this).attr('data-l2key') !== undefined) {
                            var l2key = $(this).attr('data-l2key');
                            if (!isset(value[l1key])) {
                                value[l1key] = {};
                            }
                            if ($(this).attr('data-l3key') !== undefined) {
                                var l3key = $(this).attr('data-l3key');
                                if (!isset(value[l1key][l2key])) {
                                    value[l1key][l2key] = {};
                                }
                                if (isset(value[l1key][l2key][l3key])) {
                                    if (!is_array(value[l1key][l2key][l3key])) {
                                        value[l1key][l2key][l3key] = [value[l1key][l2key][l3key]];
                                    }
                                    value[l1key][l2key][l3key].push(elValue);
                                } else {
                                    value[l1key][l2key][l3key] = elValue;
                                }
                            } else {
                                if (isset(value[l1key][l2key])) {
                                    if (!is_array(value[l1key][l2key])) {
                                        value[l1key][l2key] = [value[l1key][l2key]];
                                    }
                                    value[l1key][l2key].push(elValue);
                                } else {
                                    value[l1key][l2key] = elValue;
                                }
                            }
                        } else {
                            if (isset(value[l1key])) {
                                if (!is_array(value[l1key])) {
                                    value[l1key] = [value[l1key]];
                                }
                                value[l1key].push(elValue);
                            } else {
                                value[l1key] = elValue;
                            }
                        }
                    }
                });
                values.push(value);
            });
        }
        if ($(this).length == 1) {
            var value = {};
            $(this).findAtDepth(_attr, init(_depth, 0)).each(function() {
                if ($(this).attr('data-l1key') != undefined && $(this).attr('data-l1key') != '') {
                    try {
                        var elValue = JSON.parse($(this).value());
                    } catch (e) {
                        var elValue = $(this).value();
                    }
                    var l1key = $(this).attr('data-l1key');
                    if ($(this).attr('data-l2key') !== undefined) {
                        var l2key = $(this).attr('data-l2key');
                        if (!isset(value[l1key])) {
                            value[l1key] = {};
                        }
                        if ($(this).attr('data-l3key') !== undefined) {
                            var l3key = $(this).attr('data-l3key');
                            if (!isset(value[l1key][l2key])) {
                                value[l1key][l2key] = {};
                            }
                            if (isset(value[l1key][l2key][l3key])) {
                                if (!is_array(value[l1key][l2key][l3key])) {
                                    value[l1key][l2key][l3key] = [value[l1key][l2key][l3key]];
                                }
                                value[l1key][l2key][l3key].push(elValue);
                            } else {
                                value[l1key][l2key][l3key] = elValue;
                            }
                        } else {
                            if (isset(value[l1key][l2key])) {
                                if (!is_array(value[l1key][l2key])) {
                                    value[l1key][l2key] = [value[l1key][l2key]];
                                }
                                value[l1key][l2key].push(elValue);
                            } else {
                                value[l1key][l2key] = elValue;
                            }
                        }
                    } else {
                        if (isset(value[l1key])) {
                            if (!is_array(value[l1key])) {
                                value[l1key] = [value[l1key]];
                            }
                            value[l1key].push(elValue);
                        } else {
                            value[l1key] = elValue;
                        }
                    }
                }
            });
            values.push(value);
        }
        return values;
    }

    $.fn.setValues = function(_object, _attr) {
        for (var i in _object) {
            if (!is_array(_object[i]) && !is_object(_object[i])) {
                $(this).find(_attr + '[data-l1key="' + i + '"]').value(_object[i]);
            } else {
                for (var j in _object[i]) {
                    if (is_array(_object[i][j]) || is_object(_object[i][j])) {
                        for (var k in _object[i][j]) {
                            $(this).find(_attr + '[data-l1key="' + i + '"][data-l2key="' + j + '"][data-l3key="' + k + '"]').value(_object[i][j][k]);
                        }
                    } else {
                        $(this).find(_attr + '[data-l1key="' + i + '"][data-l2key="' + j + '"]').value(_object[i][j]);
                    }
                }
            }
        }
    }

})(jQuery);


