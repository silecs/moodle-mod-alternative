// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information
 *
 * @package    mod
 * @subpackage alternative
 * @author     Eric VILLARD <dev@eviweb.fr>
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
* @namespace
*/
M.mod_alternative = M.mod_alternative || {};

/**
* This function is initialized from PHP
*
* @param {Object} Y YUI instance
*/
M.mod_alternative.init = function(Y) {
    this.Y = Y;    
    this.initSettingOptions();
}

/**
 * get the checkbox to enable the group binding feature
 * 
 * @returns YUI Node
 */
M.mod_alternative.getBindingCB = function() {
    return this.Y.one('input[name="groupbinding"][type="checkbox"]');
}

/**
 * get the checkbox to force the group matching feature
 * 
 * @returns YUI Node
 */
M.mod_alternative.getMatchingCB = function() {
    return this.Y.one('input[name="groupmatching"][type="checkbox"]');
}

/**
 * get the checkbox to force a one to one relationship between options and groups
 * 
 * @returns YUI Node
 */
M.mod_alternative.getOneToOneCB = function() {
    return this.Y.one('input[name="grouponetoone"][type="checkbox"]');
}

/**
 * get all group select menus
 * 
 * @returns YUI NodeList
 */
M.mod_alternative.getAllGroupSelectors = function() {
    return this.Y.all('select[name^="option[group]"]');
}

/**
 * get the hidden field related to a menu, here to save its value
 * this allow the value to be submitted by the form even the menu is disabled
 * 
 * @returns YUI Node
 */
M.mod_alternative.getHiddenSaver = function(source) {
    var name = source.get('name').replace('group', 'groupid');
    return this.Y.one('input[name="' + name + '"][type="hidden"]');
}

/**
 * get all group options of a given menu or all group options
 * 
 * @param YUI Node  context context node or null
 * @param string    value   option value
 * @returns YUI NodeList
 */
M.mod_alternative.getGroupOptions = function(context, value) {
    var optionfilter = value && value.length > 0 ? '[value="' + value + '"]' : '';
    return context ?
        context.all('option' + optionfilter) :
        this.Y.all('select[name^="option[group]"] option' + optionfilter);
}

/**
 * initialize the feature setting options
 */
M.mod_alternative.initSettingOptions = function() {
    var me = this,
        binding = this.getBindingCB(),
        matching = this.getMatchingCB(),
        onetoone = this.getOneToOneCB();
    
    this.enabled = binding.get('checked');
    this.forced = matching.get('checked');
    this.onetoone = onetoone.get('checked');
   
    // checkbox for enabling group selectors    
    binding.on('click', function(e) {
        me.enabled = this.get('checked');
        me.onEnabled();
    });    
    // checkbox for forcing group matching
    matching.on('click', function(e) {
        me.forced = this.get('checked');
        me.onForced();
    });
    // checkbox for forcing group matching
    onetoone.on('click', function(e) {
        me.onetoone = this.get('checked');
        me.onOneToOne();
    });
    
    this.onEnabled();
    if (this.forced) {
        this.onForced();
    } else {
        this.onOneToOne();
    }
}

/**
 * initialize select boxes for selecting groups
 */
M.mod_alternative.initGroupSelectors = function() {
    var me = this;
    var disabled = this.enabled ? '' : 'disabled';
    var selects = this.getAllGroupSelectors();
    selects.each(function(select) {
        me.getHiddenSaver(this).set('value', select.get('value'));
        if (!me.forced) {
            select.set('disabled', disabled);
        }
        if (!me.enabled) {
            select.detachAll('change');
        } else {            
            select.on('change', function(e) {
                var oldValue = me.getHiddenSaver(select).get('value');
                var curValue = select.get('value');
                // save current value
                me.getHiddenSaver(this).set('value', curValue);               
                if (me.onetoone) {
                    // disable other options with the same current value
                    me.disableOptionsWithValue(curValue, select);
                    // enable other options with the same old value
                    me.releaseAllOptions(me.Y, oldValue);
                }                
                // update old value
                oldValue = curValue;
            });
        }
    });
}

/**
 * called when the group binding feature is clicked
 */
M.mod_alternative.onEnabled = function() {
    var disabled = this.enabled ? '' : 'disabled';
    this.getMatchingCB().set('disabled', disabled);
    this.getOneToOneCB().set('disabled', this.forced ? 'disabled' : disabled);
    this.initGroupSelectors();
}

/**
 * called when the group matching feature is clicked
 */
M.mod_alternative.onForced = function() {
    var disabled = this.enabled && !this.forced ? '' : 'disabled';
    this.getOneToOneCB().set('disabled', disabled);
    this.toggleGroupMatching();
    if (!this.forced) {
        this.onOneToOne();
    }
}

/**
 * called when the one to one feature is clicked
 */
M.mod_alternative.onOneToOne = function() {
    if (this.onetoone) {
        this.excludeOptionDoublons();
    } else {
        this.releaseAllOptions();
    }
}

/**
 * ensure that no group doublons exist
 */
M.mod_alternative.excludeOptionDoublons = function() {
    var me = this;
    var options = this.getGroupOptions();
    var selected = [];
    options.each(function(option) {
        var value = option.get('value');        
        if (value > -1 && option.get('selected')) {
            var key = value.toString();            
            if (!selected[key]) {
                selected[key] = true;
                me.disableOptionsWithValue(value, option.ancestor());
            } else {
                option.set('selected', '');
                option.ancestor().one('option[value="-1"]').set('selected', 'selected');
                me.getHiddenSaver(option.ancestor()).set('value', "-1");
            }
        }
    });
}

/**
 * release all disabled group options
 * 
 * @param YUI Node  context     node context to get options from
 * @param string    value       value to compare with option value  
 */
M.mod_alternative.releaseAllOptions = function(context, value) {
    var options = this.getGroupOptions(context, value);
    options.each(function(option) {
        option.set('disabled', '');
    });
}

/**
 * disable group options with a certain value
 * 
 * @param string    value       value to compare with option values
 * @param YUI Node  parent      parent of the current node to compare with parents 
 *                              of browsed options
 */
M.mod_alternative.disableOptionsWithValue = function(value, parent) {
    var me = this;
    // get all options of the same value
    var options = this.getGroupOptions(this.Y, value);
    options.each(function(option) {
        if (value > -1 && option.ancestor() !== parent) {
            if (option.get('selected')) {
                option.set('selected', '');
                option.ancestor().one('option[value="-1"]').set('selected', 'selected');
                me.getHiddenSaver(option.ancestor()).set('value', "-1");
            }
            option.set('disabled', 'disabled');
        }
    });
}

/**
 * called when the group matching feature is clicked to switch on or off the group menus
 */
M.mod_alternative.toggleGroupMatching = function() {
    var me = this;
    var selects = this.getAllGroupSelectors();
    selects.each(function(select) {
        var disabled = select.get('disabled');
        if (!disabled) {
            var num = select.get('id').split('_').pop();
            var textvalue = me.Y.one('input[name*="option[name][' + num + ']"]').get('value');
            var options = me.getGroupOptions(select);
            var optsize = options.size();
            for (var i=optsize - 1, found=false; i >= 0; i--) {
                var option = options.item(i);
                var selected = option.getHTML() === textvalue;            
                found = found || selected;
                if (i === 0 && !found) {
                    selected = true;
                }
                option.set('selected', selected ? 'selected' : '');
            }
        }
        me.getHiddenSaver(select).set('value', select.get('value'));
        select.set('disabled', disabled ? '' : 'disabled');
    });
}
