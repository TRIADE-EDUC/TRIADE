// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: dialog_notice.js,v 1.8 2021/05/20 09:18:53 jlaurent Exp $


define(["dojo/_base/declare", "dojo/dom", "apps/pmb/PMBDialog", "dojo/on", "dojo/dom-construct", "dijit/layout/TabContainer","dojo/_base/lang", "dojo/query", "dijit/layout/ContentPane"], function(declare, dom, Dialog, on, domConstruct, TabContainer, lang, query, ContentPane){
	
    return declare(Dialog, {
        listIds:null,
        contentViewer:null,
        paginator:null,
        ajaxUrl:"",
        nbAffiche: 10,
        currentId:0,
        constructor: function(){
            this.ajaxUrl = "./ajax.php?module=ajax&categ=notice&show_map=0&show_expl=1&show_explnum=1&id=";
        },
        /*
         * Ajoute une notice � la popup, stocke l'id de la notice
         */
        addNotice:function(idNotice){
            if(this.listIds==null){
                    this.listIds = [];
            }
            this.listIds.push(idNotice);
            if(this.listIds.length == 1){
                    this.contentViewer.set('href', this.ajaxUrl+idNotice);
                    this.paginator.set('content', "<div name='paginator' align='center' id='paginator'></div>")
            }
            this.showPagin();
        },
        /*
         * V�rifie si l'id de notice fourni en param�tre est pr�sent dans la popup
         */
        checkPresence:function(idNotice){
            if(this.listIds!=null&&this.listIds.indexOf(idNotice)!=-1){
                    return true;
            }
            else{
                    return false;
            }
        },
        onHide:function(){
            this.listIds = null;
            this.currentId = 0;
            this.contentViewer.destroyDescendants();
        },
        buildRendering:function(){
            this.inherited(arguments);
            this.contentViewer = new ContentPane({
                style: "height: 400px; display:inline; width: 100%;"
            });
            this.paginator = new ContentPane({
                    style: "height: 30px; width: 100%;"
            }); 
            this.addChild(this.contentViewer);
            this.addChild(this.paginator);
        },
        clickPaginator:function(e){
            this.currentId = parseInt(e.target.innerHTML)-1;
            this.contentViewer.set('href', this.ajaxUrl+this.listIds[this.currentId]);
            this.showPagin();
        },
        clickPrev:function(){
            this.currentId = this.currentId-1;
            this.contentViewer.set('href', this.ajaxUrl+this.listIds[this.currentId]);
            this.showPagin();
        },
        clickBegin:function(){
            this.currentId = 0;
            this.contentViewer.set('href', this.ajaxUrl+this.listIds[this.currentId]);
            this.showPagin();
        },
        clickNext:function(){
            this.currentId = this.currentId+1;
            this.contentViewer.set('href', this.ajaxUrl+this.listIds[this.currentId]);
            this.showPagin();
        },
        clickEnd:function(){
            this.currentId = this.listIds.length-1;
            this.contentViewer.set('href', this.ajaxUrl+this.listIds[this.currentId]);
            this.showPagin();
        },
        showPagin:function(){

            /*
             * Remove existing pagin 
             * 
             */

            this.paginator.set('content', "<div name='paginator' align='center' id='paginator'></div>")

            var nbAffiche = 9;
            var min = 0;
            var max = 0;
            var total = this.listIds.length;
            var afficheCote = ((nbAffiche-1)/2);
            if((this.currentId + 1) - afficheCote > 0){
                    min = ((this.currentId + 1) - afficheCote)-1 ;
            } else{
                    min = 0;
            }
            if((afficheCote +(this.currentId + 1)) >= total-1){
                    max = total;
                    if(total > nbAffiche){
                            min = total - (nbAffiche)
                    }
            } else{	
                    max = (min+(nbAffiche-1));	    		
            }

            //Ajouter bouton prev, next end & begin
            if(this.currentId > afficheCote){
                    var hrefBegin = domConstruct.create('a');
                    var callbackBegin = lang.hitch(this, this.clickBegin);
                    hrefBegin.innerHTML = "<<";
                    //hrefBegin.href = "#";
                    hrefBegin.style.margin = '5px';
                    hrefBegin.style.textDecoration='none'; 
                    on(hrefBegin, 'click', callbackBegin)
                    dom.byId('paginator').appendChild(hrefBegin);
            }
            if(this.currentId != 0){
                    //Append bouton d�but & prev
                    var hrefPrev = domConstruct.create('a');
                    var callbackPrev = lang.hitch(this, this.clickPrev);

                    hrefPrev.innerHTML = "<";
                    //hrefPrev.href = "#";
                    hrefPrev.style.margin = '5px';
                    hrefPrev.style.textDecoration='none';
                    on(hrefPrev, 'click', callbackPrev)
                    dom.byId('paginator').appendChild(hrefPrev);

            }
            for(var i=min ; i<max ; i++){
                    if(i == this.currentId){
                            var newStrong = domConstruct.create('strong');
                            newStrong.innerHTML = i+1;
                            newStrong.style.margin = '5px';
                            dom.byId('paginator').appendChild(newStrong);
                    } else{
                            var newHref = domConstruct.create('a');
                            var callbackClickPage = lang.hitch(this, this.clickPaginator);
                            newHref.innerHTML = i+1;
                            //newHref.href = "#";
                            newHref.style.margin = '5px';
                            newHref.style.textDecoration='none';
                            on(newHref, 'click', callbackClickPage)
                            dom.byId('paginator').appendChild(newHref);
                    }
            }	
            if(this.currentId != (max-1)){
                    //Append bouton d�but & prev
                    var hrefNext = domConstruct.create('a');
                    var callbackNext = lang.hitch(this, this.clickNext);
                    hrefNext.innerHTML = ">";
                    //hrefNext.href = "#";
                    hrefNext.style.margin = '5px';
                    hrefNext.style.textDecoration = 'none';
                    on(hrefNext, 'click', callbackNext)
                    dom.byId('paginator').appendChild(hrefNext);
            }
            if(this.currentId < (total-1)-afficheCote){
                    var hrefEnd = domConstruct.create('a');
                    var callbackEnd = lang.hitch(this, this.clickEnd);    			
                    hrefEnd.innerHTML = ">>";
                    //hrefEnd.href = "#";
                    hrefEnd.style.margin = '5px';
                    hrefEnd.style.textDecoration = 'none';
                    on(hrefEnd, 'click', callbackEnd)
                    dom.byId('paginator').appendChild(hrefEnd);
            }
            //console.log('max min ', max, min)
        },

    });
});
