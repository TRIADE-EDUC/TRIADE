// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: Link.js,v 1.2 2017/01/20 09:54:51 tsamson Exp $

define([
        "dojo/_base/declare", 
        "dojo/_base/lang", 
        "dojo/topic", 
        "dojo/dom", 
        "dojo/dom-class", 
        "dojo/query", 
        "d3/d3"
    ], function(declare,lang, topic, dom, domClass, query, d3){
	return declare(null, {
		source: null,
		id: null,
		target: null,
		temporary: null,
		constructor: function(data){
			this.source = data.source;
			this.id = data.id;
			this.target = data.target;
			if(data.temporary){
				this.temporary = true;
			}else{
				this.temporary = false;
			}
//			topic.subscribe('Graph', lang.hitch(this, this.handleEvents));
//			topic.subscribe('FormsList', lang.hitch(this, this.handleEvents));
			
		},
		handleEvents: function(evtType, evtArgs){
//			console.log('HandleEvents Link.js: ', 'evtType: ', evtType, ' evtArgs: ', evtArgs);
			switch(evtType){
				case '':
					break;
			}
		},
		
		id:function (d){
			return d.id
		},
	});
});
	