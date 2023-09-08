/**
 * JavaScript library for use with HTML_AJAX
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to:
 * Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   HTML
 * @package    Ajax
 * @author     Joshua Eichorn <josh@bluga.net>
 * @author     Arpad Ray <arpad@php.net>
 * @author     David Coallier <davidc@php.net>
 * @author     Elizabeth Smith <auroraeosrose@gmail.com>
 * @copyright  2005 Joshua Eichorn, Arpad Ray, David Coallier, Elizabeth Smith
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 */

/**
 *  Functions for compatibility with older browsers
 */
if (!String.fromCharCode && !String.prototype.fromCharCode) {
    String.prototype.fromCharCode = function(code)
    {
        var h = code.toString(16);
        if (h.length == 1) {
            h = '0' + h;
        }
        return unescape('%' + h);
    }
}
if (!String.charCodeAt && !String.prototype.charCodeAt) {
    String.prototype.charCodeAt = function(index)
    {
        var c = this.charAt(index);
        for (i = 1; i < 256; i++) {
            if (String.fromCharCode(i) == c) {
                return i;
            }
        } 
    }
}
// http://www.crockford.com/javascript/remedial.html
if (!Array.splice && !Array.prototype.splice) {
    Array.prototype.splice = function(s, d)
    {
        var max = Math.max,
        min = Math.min,
        a = [], // The return value array
        e,  // element
        i = max(arguments.length - 2, 0),   // insert count
        k = 0,
        l = this.length,
        n,  // new length
        v,  // delta
        x;  // shift count

        s = s || 0;
        if (s < 0) {
            s += l;
        }
        s = max(min(s, l), 0);  // start point
        d = max(min(typeof d == 'number' ? d : l, l - s), 0);    // delete count
        v = i - d;
        n = l + v;
        while (k < d) {
            e = this[s + k];
            if (!e) {
                a[k] = e;
            }
            k += 1;
        }
        x = l - s - d;
        if (v < 0) {
            k = s + i;
            while (x) {
                this[k] = this[k - v];
                k += 1;
                x -= 1;
            }
            this.length = n;
        } else if (v > 0) {
            k = 1;
            while (x) {
                this[n - k] = this[l - k];
                k += 1;
                x -= 1;
            }
        }
        for (k = 0; k < i; ++k) {
            this[s + k] = arguments[k + 2];
        }
        return a;
    }
}
if (!Array.push && !Array.prototype.push) {
    Array.prototype.push = function()
    {
        for (var i = 0, startLength = this.length; i < arguments.length; i++) {
            this[startLength + i] = arguments[i];
        }
        return this.length;
    }
}
if (!Array.pop && !Array.prototype.pop) {
    Array.prototype.pop = function()
    {
        return this.splice(this.length - 1, 1)[0];
    }
}

/**
 * HTML_AJAX static methods, this is the main proxyless api, it also handles global error and event handling
 */
var HTML_AJAX = {
	defaultServerUrl: false,
	defaultEncoding: 'Null',
    queues: false,
    clientPools: {},
    // get an HttpClient, supply a name to use the pool of that name or the default if it isn't found
    httpClient: function(name) {
        if (name) {
            if (this.clientPools[name]) {
                return this.clientPools[name].getClient();
            }
        }
        return this.clientPools['default'].getClient();
    },
    // Pushing the given request to queue specified by it, in default operation this will immediately make a request
    // request might be delayed or never happen depending on the queue setup
    // making a sync request to a non immediate queue will cause you problems so just don't do it
    makeRequest: function(request) {
        if (!HTML_AJAX.queues[request.queue]) {
            var e = new Error('Unknown Queue: '+request.queue);
            if (HTML_AJAX.onError) {
                HTML_AJAX.onError(e);
                return false;
            }
            else {
                throw(e);
            }
        }
        else {
            var qn = request.queue;
            var q = HTML_AJAX.queues[qn];

            HTML_AJAX.queues[request.queue].addRequest(request);
            return HTML_AJAX.queues[request.queue].processRequest();
        }
    },
    // get a serializer object for a specific encoding
    serializerForEncoding: function(encoding) {
        for(var i in HTML_AJAX.contentTypeMap) {
            if (encoding == HTML_AJAX.contentTypeMap[i] || encoding == i) {
                return eval("new HTML_AJAX_Serialize_"+i+";");
            }
        }
        return new HTML_AJAX_Serialize_Null();
    },
	fullcall: function(url,encoding,className,method,callback,args, customHeaders, grab) {
        var serializer = HTML_AJAX.serializerForEncoding(encoding);

        var request = new HTML_AJAX_Request(serializer);
		if (callback) {
            request.isAsync = true;
		}
        request.requestUrl = url;
        request.className = className;
        request.methodName = method;
        request.callback = callback;
        request.args = args;
        if (customHeaders) {
            request.customHeaders = customHeaders;
        }
        if (grab) {
            request.grab = true;
            if (!args || !args.length) {
                request.requestType = 'GET';
            }
        }

        return HTML_AJAX.makeRequest(request);
	},
	call: function(className,method,callback) {
        var args = new Array();
        for(var i = 3; i < arguments.length; i++) {
            args.push(arguments[i]);
        }
		return HTML_AJAX.fullcall(HTML_AJAX.defaultServerUrl,HTML_AJAX.defaultEncoding,className,method,callback,args);
	},
	grab: function(url,callback) {
		return HTML_AJAX.fullcall(url,'Null',false,null,callback, '', false, true);
	},
	replace: function(id) {
        var callback = function(result) {
            document.getElementById(id).innerHTML = result;
        }
		if (arguments.length == 2) {
			// grab replacement
            HTML_AJAX.grab(arguments[1],callback);
		}
		else {
			// call replacement
			var args = new Array();
			for(var i = 3; i < arguments.length; i++) {
				args.push(arguments[i]);
			}
			HTML_AJAX.fullcall(HTML_AJAX.defaultServerUrl,HTML_AJAX.defaultEncoding,arguments[1],arguments[2],callback,args, false, true);
		}
	},
    append: function(id) {
        var callback = function(result) {
            document.getElementById(id).innerHTML += result;
        }
        if (arguments.length == 2) {
            // grab replacement
            HTML_AJAX.grab(arguments[1],callback);
        }
        else {
            // call replacement
            var args = new Array();
            for(var i = 3; i < arguments.length; i++) {
                args.push(arguments[i]);
            }
            HTML_AJAX.fullcall(HTML_AJAX.defaultServerUrl,HTML_AJAX.defaultEncoding,arguments[1],arguments[2],callback,args, false, true);
        }
    }, 
    // override to add top level loading notification (start)
    Open: function(request) {
    },
    // override to add top level loading notification (finish)
    Load: function(request) {
    },
    /*
    // A really basic error handler 
    onError: function(e) {
        msg = "";
        for(var i in e) {
            msg += i+':'+e[i]+"\n";
        }
        alert(msg);
    },
    */
    // Class postfix to content-type map
        contentTypeMap: {
        'JSON':         'application/json',
        'Null':         'text/plain',
        'Error':        'application/error',
        'PHP':          'application/php-serialized',
		'HA' :           'application/html_ajax_action',
        'Urlencoded':   'application/x-www-form-urlencoded'
    },
    // used internally to make queues work, override Load or onError to perform custom events when a request is complete
    // fires on success and error
    requestComplete: function(request,error) {
        for(var i in HTML_AJAX.queues) {
            if (HTML_AJAX.queues[i].requestComplete) {
                HTML_AJAX.queues[i].requestComplete(request,error);
            }
        }
    },
    // submits a form through ajax. both arguments can be either DOM nodes or IDs, if the target is omitted then the form is set to be the target
    formSubmit: function (form, target, customRequest)
    {
        if (typeof form == 'string') {
            form = document.getElementById(form);
            if (!form) {
                // let the submit be processed normally
                return false;
            }
        }
        if (typeof target == 'string') {
            target = document.getElementById('target');
        }
        if (!target) {
            target = form;
        }
        var action = form.action;
        var el, type, value, name, nameParts, useValue = true;
        var out = '', tags = form.elements;
        childLoop:
        for (i in tags) {
            el = tags[i];
            if (!el || !el.getAttribute) {
                continue;
            }
            name = el.getAttribute('name');
            if (!name) {
                // no element name so skip
                continue;
            }
            // find the element value
            type = el.nodeName.toLowerCase();
            switch (type) {
            case 'input':
                var inpType = el.getAttribute('type');
                switch (inpType) {
                case 'submit':
                    type = 'button';
                    break;
                case 'checkbox':
                case 'radio':
                    if (el.checked) {
                        value = 'checked';
                        useValue = false;
                        break;
                    }
                    // unchecked radios/checkboxes don't get submitted
                    continue childLoop;
                case 'text':
                default:
                    type = 'text';
                    // continue for value
                    break;
                }
                break;
            case 'button':
            case 'textarea':
            case 'select':
                break;
            default:
                // unknown element
                continue childLoop;
            }
            if (useValue) {
                value = el.value;
            }
            // add element to output array
            out += escape(name) + '=' + escape(value) + '&';
            useValue = true;
        } // end childLoop
        var callback = function(result) {
            target.innerHTML = result;
        }

        var serializer = HTML_AJAX.serializerForEncoding('Null');
        var request = new HTML_AJAX_Request(serializer);
        request.isAsync = true;
        request.callback = callback;

        switch (form.method.toLowerCase()) {
        case 'post':
            var headers = {};
            headers['Content-Type'] = 'application/x-www-form-urlencoded';
            request.customHeaders = headers;
            request.requestType = 'POST';
            request.requestUrl = action;
            request.args = out;
            break;
        default:
            if (action.indexOf('?') == -1) {
                out = '?' + out.substr(0, out.length - 1);
            }
            request.requestUrl = action+out;
            request.requestType = 'GET';
        }

        if(customRequest) {
            for(var i in customRequest) {
                request[i] = customRequest[i];
            }
        }
        HTML_AJAX.makeRequest(request);
        return true;
    } // end formSubmit()
}




// small classes that I don't want to put in there own file

function HTML_AJAX_Serialize_Null() {}
HTML_AJAX_Serialize_Null.prototype = {
	contentType: 'text/plain; charset=UTF-8;',
	serialize: function(input) {
		return new String(input).valueOf();
	},
	
	unserialize: function(input) {
		return new String(input).valueOf();	
	}
}

// serialization class for JSON, wrapper for JSON.stringify in json.js
function HTML_AJAX_Serialize_JSON() {}
HTML_AJAX_Serialize_JSON.prototype = {
	contentType: 'application/json; charset=UTF-8',
	serialize: function(input) {
		return HTML_AJAX_JSON.stringify(input);
	},
	unserialize: function(input) {
        try {
            return eval(input);
        } catch(e) {
            // sometimes JSON encoded input isn't created properly, if eval of it fails we use the more forgiving but slower parser so will at least get something
            return HTML_AJAX_JSON.parse(input);
        }
	}
}

function HTML_AJAX_Serialize_Error() {}
HTML_AJAX_Serialize_Error.prototype = {
	contentType: 'application/error; charset=UTF-8',
	serialize: function(input) {
        var ser = new HTML_AJAX_Serialize_JSON();
        return ser.serialize(input);
	},
	unserialize: function(input) {
        var ser = new HTML_AJAX_Serialize_JSON();
        var data = new ser.unserialize(input);

        var e = new Error('PHP Error: '+data.errStr);
        for(var i in data) {
            e[i] = data[i];
        }
        throw e;
	}
}

// Processing Queues

// simple queue, just processes the request immediately
function HTML_AJAX_Queue_Immediate() {}
HTML_AJAX_Queue_Immediate.prototype = {
    request: false,
    addRequest: function(request) {
        this.request = request;
    },
    processRequest: function() {
        var client = HTML_AJAX.httpClient();
        client.request = this.request;
        return client.makeRequest();
    }
    // requestComplete: function() {} // this is also possible but this queue doesn't need it
}

// Single Buffer queue with interval
// works by attempting to send a request every x miliseconds
// if an item is currently in the queue when a new item is added it will be replaced
// simple queue, just processes the request immediately
// the first request starts the interval timer
function HTML_AJAX_Queue_Interval_SingleBuffer(interval) {
    this.interval = interval;
}
HTML_AJAX_Queue_Interval_SingleBuffer.prototype = {
    request: false,
    _intervalId: false,
    addRequest: function(request) {
        this.request = request;
    },
    processRequest: function() {
        if (!this._intervalId) {
            this.runInterval();
            this.start();
        }
    }, 
    start: function() {
        var self = this;
        this._intervalId = setInterval(function() { self.runInterval() },this.interval);
    },
    stop: function() {
        clearInterval(this._intervalId);
    },
    runInterval: function() {
        if (this.request) {
            var client = HTML_AJAX.httpClient();
            client.request = this.request;
            this.request = false;
            client.makeRequest();
        }
    }
}


// create a default queue, has to happen after the Queue class has been defined
HTML_AJAX.queues = new Object();
HTML_AJAX.queues['default'] = new HTML_AJAX_Queue_Immediate();

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
