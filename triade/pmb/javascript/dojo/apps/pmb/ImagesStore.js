// +-------------------------------------------------+
// � 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: ImagesStore.js,v 1.1 2017/11/30 10:22:23 dgoron Exp $


define(["dojo/_base/declare", "apps/pmb/Store", "dojo/topic", "dojo/_base/lang","dojo/request/xhr", "dojo/store/Memory"], function(declare, PMBStore, topic, lang, xhr, Memory){
	
	  return declare([PMBStore], {
		  allowAjaxLoading:true,
		  images:null,
		  waiting:null,
		  constructor:function(){
			  this.inherited(arguments);
			  this.images = new Memory();
		  },
		  getImage:function(code){
			  if(this.images.query({'images':'images'}).length == 0 || (this.images.query({'images':'images'}).length != 0 && this.images.query({'images':'images'})[0].loaded != true && this.images.query({'images':'images'})[0].loading != true)){
				if(this.allowAjaxLoading){
					if(this.images.data.length == 0){
						this.images.setData([{'images':'images', loaded:false}]);
					}else{
						this.images.add({'images':'images', loaded:false});
					}
					this.initImages();
					this.images.query({'images':'images'})[0].loaded = true;
					return this.data[code]; 
				}else{
					return "";
				}
			}else{
				return this.data[code];
			}  
		  },
		  initImages:function(){
			  this.images.query({'images':'images'})[0].loading = true;
			  if(this.images.query({'images':'images'}).length == 0 || (this.images.query({'images':'images'}).length != 0 && this.images.query({'images':'images'})[0].loaded != true)){
				  xhr(this.url+'&action=get_images', {
						handleAs:'json',
						sync:true,
				  }).then(lang.hitch(this, this.gotImages));  
			  }
		  },
		  gotImages:function(imagesAjax){
			  if(this.data.length == 0){
				  this.setData(imagesAjax);
			  }else{
				  for(var i=0 ; i<imagesAjax.length ; i++){
					  this.add(imagesAjax[i]);  
				  }
			  }
			  this.images.query({'images':'images'})[0].loading = false;
		  },
	  });
});