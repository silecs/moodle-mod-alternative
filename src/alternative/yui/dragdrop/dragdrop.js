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
 * registration drag & drop feature
 *
 * @package    mod
 * @subpackage alternative
 * @author     Eric VILLARD <dev@eviweb.fr>
 * @copyright  2012 Silecs http://www.silecs.info/societe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Limited Drop Target
 * 
 * @param {String}  constraintNodeId        constraint node ID to constraint dragging
 * @param {String}  draggableClass          draggable class name
 * @param {String}  dropableClass           dropable class name
 * @param {String}  optionClass             class name of option nodes
 * @param {String}  registrationClass       class name of registration nodes
 * @param {String}  availableClass          class name of nodes containing availability related information
 * @param {String}  remainClass             class name of nodes containing information about remaining places
 * @param {String}  ajaxurl                 url of remote script
 * @param {Integer} id                      alternative ID
 */
YUI.add('moodle-mod_alternative-dragdrop', function(Y) {
    /**
     * static attributes to use during class extension
     * 
     * @type object
     */
    var statics = {
        NAME: 'alternativedd',
        ATTRS: {
            /**
             * please see class header above for attribute descriptions
             */
            constraintNodeId: {},
            draggableClass: {},
            dropableClass: {},
            optionClass: {},
            registrationClass: {},
            availableClass: {},
            remainClass: {},
            ajaxurl: {},
            id: {}
        }
    }

    /**
     * class prototype
     */
    var ALTDD = function() {
        ALTDD.superclass.constructor.apply(this, arguments);
        var _me = this,
                _start = true,
                _data = [],
                _freezedDrops = [],
                /* AJAX event handlers */
                _ajaxStart,
                _ajaxSuccess,
                _ajaxFailure;

        /**
         * private immutable class to store data
         * 
         * @param {Integer} option  option ID
         * @param {Integer} user    user ID
         * @return {DATA}   returns data instance
         */
        function Data(option, user) {
            /**
             * option ID readonly accessor
             * 
             * @returns {Integer}
             */
            this.getOption = function() {
                return option;
            }

            /**
             * user ID readonly accessor
             * 
             * @returns {Integer}
             */
            this.getUser = function() {
                return user;
            }
        }

        /**
         * drag nodes initialization
         */
        function _init_draggable() {
            var selector = '#' + _me.get('constraintNodeId') + ' .' + _me.get('draggableClass'),
                    drags = Y.all(selector);
            drags.each(function(v, k) {
                var dd = new E.yui.dd.RemovableDrag({
                    node: v,
                    target: {
                        padding: '0 0 0 20'
                    },
                    groups: _me.groups,
                    onEvents: {
                        'drag:remove': _onremove
                    }
                }).plug(Y.Plugin.DDProxy, {
                    moveOnEnd: false
                }).plug(Y.Plugin.DDConstrained, {
                    constrain2node: '#' + _me.get('constraintNodeId'),
                    stickY: true
                }).plug(Y.Plugin.DDNodeScroll, {
                    node: v.get('parentNode')
                }).plug(Y.Plugin.DDWinScroll);
                dd.on('drag:mouseDown', _onmousedown);
                dd.on('drag:end', _ondragend);
            });
        }

        /**
         * drop nodes initialization
         */
        function _init_dropable() {
            var selector = '#' + _me.get('constraintNodeId') + ' .' + _me.get('dropableClass'),
                    dropables = Y.all(selector);
            dropables.each(function(v, k) {
                var avail = _closest(v, '.' + _me.get('availableClass')).getHTML(),
                        drop = new E.yui.dd.LimitedDrop({
                    node: v,
                    limit: avail != '∞' ? avail : 0,
                    onEvents: {
                        'drop:update': _onupdate
                    },
                    groups: _me.groups,
                    padding: '20 0 20 0'
                });
            });
        }

        /**
         * look for the closest node that matches the <i>search</i> selector
         * 
         * @param {Node}        node        node to start the research from
         * @param {string}      search      css selector
         * @returns {null|Node} returns the found node or null
         */
        function _closest(node, search) {
            var constraint = Y.one('#' + _me.get('constraintNodeId'));
            var _node = node, found = false;
            while (_node && !found && _node != constraint) {
                var _search = _node.one(search);
                found = _search != null;
                if (!found) {
                    _node = _node.ancestor();
                } else {
                    _node = _search
                }
            }
            return found ? _node : null;
        }

        /**
         * recalculate registrations and remain places number
         * 
         * @param {CustomEvent} e   event object
         */
        function _recalc(e) {
            var node = e.drop.get('node'),
                    regs_node = _closest(node, '.' + _me.get('registrationClass')),
                    remain_node = _closest(node, '.' + _me.get('remainClass')),
                    avail_node = _closest(node, '.' + _me.get('availableClass')),
                    avail = avail_node.getHTML(),
                    val = e.drop.getDrags().size();

            if (avail != '∞') {
                avail = parseInt(avail);
                remain_node.setHTML(avail - val);
            }
            regs_node.setHTML(val);
        }

        /**
         * update user registration
         * 
         * @param {CustomEvent} e   event object
         */
        function _updateRegs(e) {
            var dropnode = e.drop.get('node'),
                    dragnode = e.drag.get('node'),
                    option_node = _closest(dropnode, '.' + _me.get('optionClass')),
                    option = option_node.getData('optionid'),
                    user = dragnode.getData('userid');

            if (_start) {
                _data['onstart'] = new Data(option, user);
            } else {
                _data['onend'] = new Data(option, user);
                _request(e, ALTDD.ACTIONS.update);
            }
            _start = !_start;
        }

        /**
         * update event handler
         * 
         * @param {CustomEvent} e   event object
         */
        function _onupdate(e) {
            if (e.drag) {
                _recalc(e);            
                _updateRegs(e);
            }
        }

        /**
         * remove event handler
         * 
         * @param {CustomEvent} e   event object
         */
        function _onremove(e) {
            var dragnode = e.drag.get('node'),
                    dropnode = e.drop.get('node'),
                    option_node = _closest(dropnode, '.' + _me.get('optionClass')),
                    option = option_node.getData('optionid'),
                    user = dragnode.getData('userid'),
                    regid = dragnode.getData('regid');
            _data['onstart'] = new Data(option, user);
            _data['onend'] = new Data(regid, user);
            _ajaxSuccess = _recalc;
            _ajaxFailure = _recalc;
            _request(e, ALTDD.ACTIONS.remove);
        }

        /**
         * release data object
         */
        function _releaseData() {
            delete _data['onstart'];
            delete _data['onend'];
        }

        /**
         * check whether a drag object with the same user id already exists in
         * a given drop target
         * 
         * @param {LimitedDrop}     drop    the drop target to search in
         * @param {RemovableDrag}   drag    the current drag object
         * @param {String}          user    user id
         * @returns {Boolean}       returns true if a drag object with the same 
         *                          user id is found, else returns false
         */
        function _alreadyExist(drop, drag, user) {
            var dragnode = drag.get('node'),
                    dropnode = drop.get('node'),
                    selector = '.' + _me.get('draggableClass') + '[data-userid="' + user + '"]',
                    searchnode = dropnode.one(selector);
            return searchnode && (dragnode !== searchnode);
        }

        /**
         * mouse down event handler
         * used to prevent registration doublons
         * 
         * @param {CustomEvent} e   event object
         */
        function _onmousedown(e) {
            var drag = this,
                dragnode = drag.get('node'),
                    user = dragnode.getData('userid'),
                    selector = '#' + _me.get('constraintNodeId') + ' .' + _me.get('dropableClass');
            _freezedDrops = [];
            Y.all(selector).each(function(dn) {
                var drop = Y.DD.DDM.getDrop(dn);
                if (_alreadyExist(drop, drag, user)) {
                    _freezedDrops.push({
                        oldlock: drop.isLocked(),
                        drop: drop
                    });
                    drop.lock();                    
                }
            });
        }
        
        /**
         * drag end event handler
         * 
         * @param {CustomEvent} e   event object
         */
        function _ondragend(e) {
            Y.Array.each(_freezedDrops, function(fd) {
                if (!fd.oldlock) {
                    fd.drop.unlock();                    
                }                
            });
            _freezedDrops = [];
        }

        /**
         * perform the AJAX request
         * 
         * @param {CustomEvent} e       event object
         * @param {String}      action  action requested
         */
        function _request(e, action) {
            var uri = M.cfg.wwwroot + _me.get('ajaxurl'),
                    spinner = M.util.add_spinner(Y, e.drag.get('node')),
                    params = {
                id: _me.get('id'),
                sesskey: M.cfg.sesskey,
                userid: _data['onend'].getUser(),
                oldoptionid: _data['onstart'].getOption(),
                newoptionid: _data['onend'].getOption(),
                action: action
            };

            Y.io(uri, {
                method: 'POST',
                data: params,
                on: {
                    start: function(tid) {
                        spinner.show();
                        if (_ajaxStart) {
                            _ajaxStart.apply(_me, [e, tid]);
                            _ajaxStart = null;
                        }
                    },
                    success: function(tid, response) {
                        var responsetext = Y.JSON.parse(response.responseText);
                        if (_ajaxSuccess) {
                            _ajaxSuccess.apply(_me, [e, tid, response]);
                            _ajaxSuccess = null;
                        }
                        window.setTimeout(function(e) {
                            spinner.hide();
                        }, 250);
                        _releaseData();
                    },
                    failure: function(tid, response) {
                        var responsetext = Y.JSON.parse(response.responseText);
                        if (_ajaxFailure) {
                            _ajaxFailure.apply(_me, [e, tid, response]);
                            _ajaxFailure = null;
                        }
                        spinner.hide();
                        _releaseData();
                    }
                },
                context: this
            });
        }

        // initialization
        _init_draggable();
        _init_dropable();
    };

    // actions
    ALTDD.ACTIONS = {
        update: 'modify',
        remove: 'remove'
    }

    // extension
    Y.extend(ALTDD, M.core.dragdrop, {
        initializer: function(config) {
            this.groups = ['altdd'];
            this.samenodeclass = config.draggableClass;
            this.parentnodeclass = config.dropableClass;
            this.goingup = false;
            this.lasty = 0;
        }
    }, statics);

    // namespace
    M.mod_alternative = M.mod_alternative || {};

    // static factory
    M.mod_alternative.init_dragdrop = function(config) {
        return new ALTDD(config);
    };
}, '@VERSION@', {
    requires: ['base', 'node', 'io', 'dom', 'dd', 'dd-scroll', 'moodle-core-dragdrop', 'evidev-yui-dd-limiteddrop', 'evidev-yui-dd-removabledrag']
});
