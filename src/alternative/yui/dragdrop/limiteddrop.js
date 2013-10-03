/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
/**
 * The MIT License
 *
 * Copyright 2013 Eric VILLARD <dev@eviweb.fr>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @package     evidev-yui-dd
 * @author      Eric VILLARD <dev@eviweb.fr>
 * @copyright	(c) 2013 Eric VILLARD <dev@eviweb.fr>
 * @license     http://opensource.org/licenses/MIT MIT License
 */
/**
 * Limited Drop Target
 * 
 * @param {Integer} limit       number of allowed drag items to be dropped
 * @param {Object}  onEvents    object of event handlers
 */
YUI.add('evidev-yui-dd-limiteddrop', function(Y) {
    /**
     * static attributes to use during class extension
     * 
     * @type object
     */
    var statics = {
        NAME: 'limiteddrop',
        ATTRS: {
            limit: {
                value: 0,
                writeOnce: true
            },
            onEvents: {
            }
        },
        EVENTS: {
            /**
             * fires on object initialization
             * 
             * @event drop:init
             * @param {EventFacade} event An Event Facade object with the following specific property added:
             * <dl>
             *  <dt>drop</dt><dd>the limited drop target</dd>
             * </dl>
             * @bubbles DDM
             * @type {CustomEvent}
             */
            INIT: 'drop:init',
            
            /**
             * fires on update of the number of items
             * 
             * @event drop:update
             * @param {EventFacade} event An Event Facade object with the following specific property added:
             * <dl>
             *  <dt>drop</dt><dd>the drop target</dd>
             *  <dt>drag</dt><dd>the active drag item</dd>
             *  <dt>cause</dt><dd>the cause the event is fired for</dd>
             * </dl>
             * @bubbles DDM
             * @type {CustomEvent}
             */
            UPDATE: 'drop:update',
            
            /**
             * fires when the limit of allowed items is reached
             * 
             * @event drop:full
             * @param {EventFacade} event An Event Facade object with the following specific property added:
             * <dl>
             *  <dt>drop</dt><dd>the limited drop target</dd>
             * </dl>
             * @bubbles DDM
             * @type {CustomEvent}
             */
            FULL: 'drop:full',
            
            /**
             * fires when the limit of allowed items is exceeded
             * 
             * @event drop:outofbounds
             * @param {EventFacade} event An Event Facade object with the following specific property added:
             * <dl>
             *  <dt>drop</dt><dd>the limited drop target</dd>
             *  <dt>overflow</dt><dd>the over number of elements</dd>
             * </dl>
             * @bubbles DDM
             * @type {CustomEvent}
             */
            OUTOFBOUNDS: 'drop:outofbounds'
        }
    },
    /**
     * class prototype
     */
    LD = function() {
        LD.superclass.constructor.apply(this, arguments);

        var _me = this,
                _registered = [],
                _drag = false,
                _locked = false;

        /**
         * check target capacity
         * 
         * @returns {Boolean}   returns true if the drop target can accept more 
         *                      dropped items, else returns false
         */
        function _checkCapacity() {
            var overflow = _me.get('limit') - _count();
            if (overflow < 0) {
                _me.fire(LD.EVENTS.OUTOFBOUNDS, {
                    drop: _me,
                    overflow: -(overflow)
                });
                return false;
            } else if (_me.isFull()) {
                _me.fire(LD.EVENTS.FULL, {
                    drop: _me
                });
                return false;
            }
            return true;
        }

        /**
         * count the number of dropped items
         * 
         * @returns {Integer}
         */
        function _count() {
            return _me.getDrags().size();
        }

        /**
         * register a drag object to be watched by the drop target
         * 
         * @param {Drag} drag   drag object to register
         */
        function _register(drag) {
            var id = drag.get('node').get('id');
            if (!_registered[id]) {
                drag.on('drag:start', _ondragstart);
                //drag.on('drag:end', _ondragend);                
                drag.on('drag:drophit', _ondragdrophit)
                drag.on('drag:dropmiss', _ondragdropmiss)
                _registered[id] = true;
            }
        }

        /**
         * unregister a drag object watched by the drop target
         * 
         * @param {Drag} drag   drag object to unregister
         */
        function _unregister(drag) {
            var id = drag.get('node').get('id');
            if (_registered[id]) {
                drag.detach('drag:start', _ondragstart);
                //drag.detach('drag:end', _ondragend);
                drag.detach('drag:drophit', _ondragdrophit)
                drag.detach('drag:dropmiss', _ondragdropmiss)
                delete _registered[id];
            }
        }

        /**
         * publish events
         */
        function _publishEvents() {
            for (var x in LD.EVENTS) {
                _me.publish(LD.EVENTS[x], {
                    type: LD.EVENTS[x],
                    emitFacade: true,
                    bubbles: true,
                    preventable: false,
                    queuable: false,
                    prefix: 'drop'
                });

                var event = _me.get('onEvents')[LD.EVENTS[x]];
                if (typeof event !== 'undefined') {
                    _me.on(LD.EVENTS[x], event);
                }
            }
        }

        /**
         * handler for drop:enter event
         * 
         * @param {EventFacade} e   event facade object passed by the source
         */
        function _ondropenter(e) {
            _register(e.drag);
        }

        /**
         * handler for drop:exit event
         * 
         * @param {EventFacade} e   event facade object passed by the source
         */
        function _ondropexit(e) {
            _unregister(e.drag);
        }

        /**
         * handler for drop:hit event
         * 
         * @param {EventFacade} e   event facade object passed by the source
         */
        function _ondrophit(e) {
        }

        /**
         * handler for drag:drophit event
         * 
         * @param {EventFacade} e   event facade object passed by the source
         */
        function _ondragdrophit(e) {
        }

        /**
         * handler for drag:dropmiss event
         * 
         * @param {EventFacade} e   event facade object passed by the source
         */
        function _ondragdropmiss(e) {
        }

        /**
         * handler for drop:full event
         * 
         * @param {EventFacade} e   event facade object passed by the source
         */
        function _ondropfull(e) {
            if (!_locked) {
                _me.lock();
            }
        }

        /**
         * handler for drag:start event
         * 
         * @param {EventFacade} e   event facade object passed by the source
         */
        function _ondragstart(e) {
            var drag = Y.DD.DDM.activeDrag;
            var drop = Y.DD.DDM.getDrop(drag.get('node').ancestor());
            _drag = true;

            if (drop === _me) {
                if (_checkCapacity() && _locked) {
                    _me.unlock();
                }
                _me.fire(LD.EVENTS.UPDATE, {
                    drop: _me,
                    drag: drag,
                    cause: 'itemleave'
                });
            }
        }

        /**
         * handler for drag:end event
         * 
         * @param {EventFacade} e   event facade object passed by the source
         */
        function _ondragend(e) {
            var drag = Y.DD.DDM.activeDrag;
            var drop = Y.DD.DDM.getDrop(drag.get('node').ancestor());
            _drag = false;
            
            if (drop === _me) {
                _me.fire(LD.EVENTS.UPDATE, {
                    drop: _me,
                    drag: drag,
                    cause: 'newitem'
                });
                _checkCapacity();
            } else {
                drop.fire('drop:enter', {
                    drop: drop,
                    drag: drag
                })
            }
        }

        /**
         * get dropped drag items
         * 
         * @returns {ArrayList}
         * @api
         */
        this.getDrags = function() {
            var nodes = this.get('node').all('.' + Y.DD.DDM.CSS_PREFIX + '-draggable');
            var draggables = [];
            nodes.each(function(node) {
                if (!_drag || !node.hasClass(Y.DD.DDM.CSS_PREFIX + '-dragging')) {
                    draggables.push(Y.DD.DDM.getDrag(node));
                }
            });
            return new Y.ArrayList(draggables);
        }

        /**
         * lock the drop target
         * 
         * @api
         */
        this.lock = function() {
            this.set('lock', true);
            this.get('node').all('.' + Y.DD.DDM.CSS_PREFIX + '-drop').each(function(node) {
                Y.DD.DDM.getDrop(node).set('lock', true);
            });
            Y.DD.DDM._deactivateTargets();
            _locked = true;
        };

        /**
         * unlock the drop target
         * 
         * @api
         */
        this.unlock = function() {
            this.set('lock', false);
            this.get('node').all('.' + Y.DD.DDM.CSS_PREFIX + '-drop').each(function(node) {
                Y.DD.DDM.getDrop(node).set('lock', false);
            });
            Y.DD.DDM._activateTargets();
            _locked = false;
        };

        /**
         * check if the drop target is full
         * 
         * @returns {Boolean}   returns true if its limit is reached, else returns false
         * @api
         */
        this.isFull = function() {
            var limit = this.get('limit');
            return limit > 0 && _count() >= limit;
        };

        // register private events
        this.on('drop:enter', _ondropenter);
        this.on('drop:hit', _ondrophit);
        this.on('drop:exit', _ondropexit);
        this.on(LD.EVENTS.FULL, _ondropfull);
        Y.DD.DDM.on('drag:end', _ondragend);

        // initialization
        _publishEvents();

        var _draggables = this.getDrags();
        _draggables.each(function(drag) {
            _register(drag);
        });

        _checkCapacity();
        
        this.fire(LD.EVENTS.INIT, {
            drop: this
        });
    };


    // extends Y.DD.Drop
    Y.extend(LD, Y.DD.Drop, {
        initializer: function(config) {            
        },
    }, statics);
    
    // namespace
    E = typeof E != 'undefined' ? E : {};
    E.yui = E.yui || {};
    E.yui.dd = E.yui.dd || {};
    E.yui.dd.LimitedDrop = LD;

}, '@VERSION@', {
    requires: ['base', 'node', 'dd-drop', 'collection', 'event-custom-base']
});

