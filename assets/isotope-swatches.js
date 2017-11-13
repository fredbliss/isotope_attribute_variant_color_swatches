(function(_win, _doc, jQuery, MooTools) {
    "use strict";


    function serializeForm(form) {
        if (jQuery) {
            return jQuery(form).serialize();
        } else if (MooTools) {
            return form.toQueryString();
        } else {
            return formToQueryString(form);
        }
    }

    _win.IsotopeSwatches = (function() {
        var loadMessage = 'Loading product data …';

        function initProduct(config) {
            var formParent = _doc.getElementById(config.formId).parentNode;

            if (formParent) {
                registerEvents(formParent, config);
            }
        }

        function registerEvents(formParent, config) {
            var i, el,
                xhr = new XMLHttpRequest(),
                form = formParent.getElementsByTagName('form')[0];

            if (!form) return;

            xhr.open(form.getAttribute('method').toUpperCase(), encodeURI(form.getAttribute('action')));
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onload = function() {
                if (xhr.status === 200) {
                    ajaxSuccess(xhr.responseText);
                } else if (xhr.status !== 200) {
                    Isotope.hideBox();
                }
            };


            function ajaxSuccess(txt) {
                var div = _doc.createElement('div'),
                    scripts = '',
                    script, i;

                txt = txt.replace(/<script([^>]*)>([\s\S]*?)<\/script>/gi, function(all, attr, code){
                    var type = attr.match(/type=['"]?([^"']+)/);

                    if (type !== null && type[1] !== 'text/javascript') {
                        return all;
                    }

                    scripts += code + '\n';
                    return '';
                });

                div.innerHTML = txt;

                // Remove all error messages
                var errors = div.getElementsByTagName('p');
                for(i=0; i<errors.length; i++) {
                    if (errors[i].className.search(/(^| )error( |$)/) != -1) {
                        errors[i].parentNode.removeChild(errors[i]);
                    }
                }

                formParent.innerHTML = '';
                for(i = 0; i<div.childNodes.length; i++) {
                    formParent.appendChild(div.childNodes[i]);
                }

                registerEvents(formParent, config);

                Isotope.hideBox();

                if (scripts) {
                    script = _doc.createElement('script');
                    script.text = scripts;
                    _doc.head.appendChild(script);
                    _doc.head.removeChild(script);
                }
            }

            //if (config.attributes) {



                //for (i=0; i<config.attributes.length; i++) {
                    var els = _doc.getElementsByClassName('swatch');
                    var sel = _doc.getElementsByName(config.attributes[0])[0];

                    //hide select box
                    sel.parentNode.setAttribute('style','display:none;');

                    if (els.length>0) {
                        for(var i = 0, len = els.length; i < len; i++) {
                            els[i].addEventListener('click', function(event) {

                                if (xhr.readyState > 1) {
                                    xhr.abort();
                                }



                                var opts = sel.options;

                                for (var opt, j = 0; opt = opts[j]; j++) {
                                    if (opt.value == event.target.getAttribute('data-id')) {
                                        sel.selectedIndex = j;
                                        break;
                                    }
                                }

                                Isotope.displayBox(loadMessage);
                                xhr.send(serializeForm(form));
                            });
                        }
                    }
                //}
            //}
        }

        return {
            'attachSwatch': function(products) {
                var i;

                // Check if products is an array
                if (Object.prototype.toString.call(products) === '[object Array]' && products.length > 0) {
                    for (i=0; i<products.length; i++) {
                        initProduct(products[i]);
                    }
                }
            },

            'setLoadMessage': function(message) {
                loadMessage = message || 'Loading product data …';
            }
        };
    })();
})(window, document, window.jQuery, window.MooTools);
