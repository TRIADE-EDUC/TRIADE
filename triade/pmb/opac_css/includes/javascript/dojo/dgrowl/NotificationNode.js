// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: NotificationNode.js,v 1.2 2020/08/27 13:49:48 qvarin Exp $

define([ "dojo/text", "dojo/_base/declare", "dijit/_WidgetBase", "dijit/_TemplatedMixin", "dojo/dom-construct", "dgrowl/NotificationNode", "dojo/dom-class", "dojo/_base/event", "dojo/_base/lang", "dojo/text!./NotificationNode.html"],
	   function(t, declare, base, templated, domCon, NotificationNode, domClass, event, lang, templateString)
{
	return declare('dGrowl',
	[base, templated],
	{
		'templateString':templateString,
		'title':'',
		'message':'',
		'duration':5000,
		'sticky':false,
		'stickyClass':'',
		'constructor':function(a)
		{
			if(a.channel == undefined)
				console.error('NotificationNode requires a "channel" definition!');
			if(a.sticky === true)
				this.stickyClass = 'dGrowl-notification-sticky';
		},
		'postCreate':function()
		{
			this.inherited(arguments);
			if(this.sticky === false)
				setTimeout(lang.hitch(this, this.killme), this.duration);
		},
		'show':function()
		{
			var v = this.domNode.clientHeight; // trigger reflow so transition animates... hack!!!!!!!
			domClass.add(this.domNode, 'dGrowl-visible');
			this.onShow(this);
		},
		'onShow':function(){},
		'onHide':function(){},
		'killme':function(evt)
		{
			if(evt)
				event.stop(evt);
			// delay on destroy for animation to do its thing...
			if (this.domNode) {
				domClass.remove(this.domNode, 'dGrowl-visible');
				setTimeout(lang.hitch(this, this.destroy),1100);
				this.onHide(this);
			}
		}
	});
});
