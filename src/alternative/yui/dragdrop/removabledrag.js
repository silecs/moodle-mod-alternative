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
 * Removable Drag Object
 * 
 * @param {Object}  onEvents    object of event handlers
 */
YUI.add('evidev-yui-dd-removabledrag', function(Y) {
    /**
     * static attributes to use during class extension
     * 
     * @type object
     */
    var statics = {
        NAME: 'removabledrag',
        ATTRS: {
            onEvents: {
                value: {}
            }
        },
        EVENTS: {
            /**
             * fires on object initialization
             * 
             * @event drag:init
             * @param {EventFacade} event An Event Facade object with the following specific property added:
             * <dl>
             *  <dt>drag</dt><dd>the current drag object</dd>
             * </dl>
             * @bubbles DDM
             * @type {CustomEvent}
             */
            INIT: 'drag:init',
            
            /**
             * fires on object deletion
             * 
             * @event drag:remove
             * @param {EventFacade} event An Event Facade object with the following specific property added:
             * <dl>
             *  <dt>drag</dt><dd>the removed drag object</dd>
             * </dl>
             * @bubbles DDM
             * @type {CustomEvent}
             */
            REMOVE: 'drag:remove',
        }
    },
    
    /**
     * class prototype
     */
    RD = function() {
        RD.superclass.constructor.apply(this, arguments);

        var _me = this;

        /**
         * publish events
         */
        function _publishEvents() {
            for (var x in RD.EVENTS) {
                _me.publish(RD.EVENTS[x], {
                    type: RD.EVENTS[x],
                    emitFacade: true,
                    bubbles: true,
                    preventable: false,
                    queuable: false,
                    prefix: 'drop'
                });

                var event = _me.get('onEvents')[RD.EVENTS[x]];
                if (typeof event !== 'undefined') {
                    _me.on(RD.EVENTS[x], event);
                }
            }
        }
        
        /**
         * remove
         */
        function _remove() {
            _me.fire(RD.EVENTS.REMOVE, {
                drag: _me
            });
            _me.destroy();
            _me.get('node').remove(true);
            
        };
        
        /**
         * add close button
         */
        function _addCloseButton() {
            _me.get('node').addClass('removabledrag');
            _me.get('node').appendChild('<i class="removabledrag-close"></i>').on('click', function(e) {
                _remove();
            });
        }

        /**
         * add default styles to page
         */
        function _addDefaultStyle() {
            var id = 'evidev-yui-dd-removabledrag-styles',
                node = '<style></style>',
                style;
            style = "@font-face {\n";
            style+= "   font-family: 'modern_pictogramsnormal';\n";
            style+= "   src: url(data:application/x-font-woff;charset=utf-8;base64," + RD.FONT_BASE64 + ") format('woff');\n";
            style+= "   font-weight: normal;\n";
            style+= "   font-style: normal;\n";
            style+= "}\n\n";
            style+= ".removabledrag {\n";
            style+= "   clear: both;\n";
            style+= "}\n\n";
            style+= ".removabledrag-close {\n";
            style+= "   font-family: 'modern_pictogramsnormal';\n";
            style+= "   float:right;\n";
            style+= "   font-weight: normal;\n";
            style+= "   font-style: normal;\n";
            style+= "   cursor:pointer;\n";
            style+= "   font-size: 1.3em;\n";
            style+= "   display: inline-block;\n";
            style+= "   line-height: 1em;\n";
            style+= "}\n\n";
            style+= ".removabledrag-close:before {\n";
            style+= "   content: '" + RD.CLOSE_BUTTON + "';\n";
            style+= "}\n\n";

            if (!Y.one('#' + id)) {
                Y.one('head').appendChild(node).setAttribute('id', id).setHTML(style);
            }
        }
        
        // initialization
        _publishEvents();
        _addDefaultStyle();
        _addCloseButton();
        
        this.fire(RD.EVENTS.INIT, {
            drag: this
        });
    };
    
    RD.FONT_BASE64 = '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';
    RD.CLOSE_BUTTON = '&#120;'

    // extends Y.DD.Drag
    Y.extend(RD, Y.DD.Drag, {
        initializer: function(config) {            
        },
    }, statics);
    
    // namespace
    E = typeof E != 'undefined' ? E : {};
    E.yui = E.yui || {};
    E.yui.dd = E.yui.dd || {};
    E.yui.dd.RemovableDrag = RD;

}, '@VERSION@', {
    requires: ['base', 'node', 'dd-drag', 'collection', 'event-custom-base']
});

