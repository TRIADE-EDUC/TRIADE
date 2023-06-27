/**
 * Utility methods
 *
 * @category   HTML
 * @package    Ajax
 * @license    http://www.opensource.org/licenses/lgpl-license.php  LGPL
 *
 * See Main.js for author/license details
 */
// {{{ HTML_AJAX_Util
/**
 * All the utilities we will be using thorough the classes
 */
var HTML_AJAX_Util = {
    // Set the element event
    registerEvent: function(element, event, handler) 
    {
        //var element = document.getElementById(id);
        if (typeof element.addEventListener != "undefined") {   //Dom2
            element.addEventListener(event, handler, false);
        } else if (typeof element.attachEvent != "undefined") { //IE 5+
            element.attachEvent("on" + event, handler);
        } else {
            if (element["on" + event] != null) {
                var oldHandler = element["on" + event];
                element["on" + event] = function(e) {
                    oldHander(e);
                    handler(e);
                };
            } else {
                element["on" + event] = handler;
            }
        }
    },
    // get the target of an event, automatically checks window.event for ie
    eventTarget: function(event) 
    {
        if (!event) var event = window.event;
        if (event.target) return event.target; // w3c
        if (event.srcElement) return event.srcElement; // ie 5
    },
    // gets the type of a variable or its primitive equivalent as a string
    getType: function(inp) 
    {
        var type = typeof inp, match;
        if(type == 'object' && !inp)
        {
            return 'null';
        }
        if (type == "object") {
            if(!inp.constructor)
            {
                return 'object';
            }
            var cons = inp.constructor.toString();
            if (match = cons.match(/(\w+)\(/)) {
                cons = match[1].toLowerCase();
            }
            var types = ["boolean", "number", "string", "array"];
            for (key in types) {
                if (cons == types[key]) {
                    type = types[key];
                    break;
                }
            }
        }
        return type;
    },
    // repeats the input string the number of times given by multiplier. exactly like PHP's str_repeat()
    strRepeat: function(inp, multiplier) {
        var ret = "";
        while (--multiplier > 0) ret += inp;
        return ret;
    },
    // recursive variable dumper similar in output to PHP's var_dump(), the differences being: this function displays JS types and type names; JS doesn't provide an object number like PHP does
    varDump: function(inp, printFuncs, _indent, _recursionLevel)
    {
        if (!_recursionLevel) _recursionLevel = 0;
        if (!_indent) _indent = 1;
        var tab = this.strRepeat("  ", ++_indent);    
        var type = this.getType(inp), out = type;
        var consrx = /(\w+)\(/;
        consrx.compile();
        if (++_recursionLevel > 6) {
            return tab + inp + "Loop Detected\n";
        }
        switch (type) {
            case "boolean":
            case "number":
                out += "(" + inp.toString() + ")";
                break;
            case "string":
                out += "(" + inp.length + ") \"" + inp + "\"";
                break;
            case "function":
                if (printFuncs) {
                    out += inp.toString().replace(/\n/g, "\n" + tab);
                }
                break;
            case "array":
            case "object":
                var atts = "", attc = 0;
                try {
                    for (k in inp) {
                        atts += tab + "[" + (/\D/.test(k) ? "\"" + k + "\"" : k)
                            + "]=>\n" + tab + this.varDump(inp[k], printFuncs, _indent, _recursionLevel);
                        ++attc;
                    }
                } catch (e) {}
                if (type == "object") {
                    var objname, objstr = inp.toString();
                    if (objname = objstr.match(/^\[object (\w+)\]$/)) {
                        objname = objname[1];
                    } else {
                        try {
                            objname = inp.constructor.toString().match(consrx)[1];
                        } catch (e) {
                            objname = 'unknown';
                        }
                    }
                    out += "(" + objname + ") ";
                }
                out += "(" + attc + ") {\n" + atts + this.strRepeat("  ", _indent - 1) +"}";
                break;
        }
        return out + "\n";
    },
    quickPrint: function(input) {
        var ret = "";
        for(var i in input) {
            ret += i+':'+input[i]+"\n";
        }
        return ret;
    },
    //compat function for stupid browsers in which getElementsByTag with a * dunna work
    getAllElements: function(parentElement)
    {
        //check for idiot browsers
        if( document.all)
        {
            if(!parentElement) {
                var allElements = document.all;
            }
            else
            {
                var allElements = [], rightName = new RegExp( parentElement, 'i' ), i;
                for( i=0; i<document.all.length; i++ ) {
                    if( rightName.test( document.all[i].parentElement ) )
                    allElements.push( document.all[i] );
                }
            }
            return allElements;
        }
        //real browsers just do this
        else
        {
            if (!parentElement) { parentElement = document.body; }
            return parentElement.getElementsByTagName('*');
        }
    },
    getElementsByClassName: function(className, parentElement) {
        var allElements = HTML_AJAX_Util.getAllElements(parentElement);
        var items = [];
        var exp = new RegExp('(^| )' + className + '( |$)');
        for(var i=0,j=allElements.length; i<j; i++)
        {
            if(exp.test(allElements[i].className))
            {
                items.push(allElements[i]);
            }
        }
        return items;
    },
    htmlEscape: function(inp) {
        var rxp, chars = [
            ['&', '&amp;'],
            ['<', '&lt;'],
            ['>', '&gt;']
        ];
        for (i in chars) {
            inp.replace(new RegExp(chars[i][0]), chars[i][1]);
        }
        return inp;
    },
    // return the base of the given absolute url
    baseURL: function(absolute) {
        var qPos = absolute.indexOf('?');
        if (qPos >= 0) {
            absolute = absolute.substr(0, qPos);
        }
        var slashPos = absolute.lastIndexOf('/');
        if (slashPos < 0) {
            return absolute;
        }
        return absolute.substr(0, slashPos + 1);
    },
    // return the query string from a url
    queryString: function(url) {
        var qPos = url.indexOf('?');
        if (qPos >= 0) {
            return url.substr(qPos+1);
        }
    },
    // return the absolute path to the given relative url
    absoluteURL: function(rel, absolute) {
        if (/^https?:\/\//i.test(rel)) {
            return rel;
        }
        if (!absolute) {
            var bases = document.getElementsByTagName('base');
            for (i in bases) {
                if (bases[i].href) {
                    absolute = bases[i].href;
                    break;
                }
            }
            if (!absolute) {
                absolute = window.location.href;
            }
        }
        if (rel == '') {
            return absolute;
        }
        if (rel.substr(0, 2) == '//') {
            // starts with '//', replace everything but the protocol
            var slashesPos = absolute.indexOf('//');
            if (slashesPos < 0) {
                return 'http:' + rel;
            }
            return absolute.substr(0, slashesPos) + rel;
        }
        var base = this.baseURL(absolute);
        var absParts = base.substr(0, base.length - 1).split('/');
        var absHost = absParts.slice(0, 3).join('/') + '/';
        if (rel.substr(0, 1) == '/') {
            // starts with '/', append it to the host
            return absHost + rel;
        }
        if (rel.substr(0, 1) == '.' && rel.substr(1, 1) != '.') {
            // starts with '.', append it to the base
            return base + rel.substr(1);
        }
        // remove everything upto the path and beyond 
        absParts.splice(0, 3);
        var relParts = rel.split('/');
        var loopStart = relParts.length - 1;
        relParts = absParts.concat(relParts);
        for (i = loopStart; i < relParts.length;) {
            if (relParts[i] == '..') {
                if (i == 0) {
                    return absolute;
                }
                relParts.splice(i - 1, 2);
                --i;
                continue;
            }
            i++;
        }
        return absHost + relParts.join('/');
    }
}
// }}}
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
