/************* Start debug functions ***************/
function my_trace(str) {
	trace(str);
	my_debug(str);
}


Array.prototype.trace = function(){
   trace("(Array){");
   for(ob in this){
      //trace(ob);
      this.trace_eval(this[ob],ob,1);
   }
   trace("}");
}

Array.prototype.trace_eval = function(val,object,level){
s = "   ";
space = "";
   for(i=0;level > i;i++){
      space += s;
   }
val_type = typeof val;
   switch(val_type){
   case "string":   
      trace(space+"["+object+"] => "+val);
      break;
   case "object":
      trace(space+"["+object+"] => "+"(Array/Object){");
      for(ob in val){
         this.trace_eval(val[ob],ob,level+1);
      }
      trace(space+"}");
      break;
   case "function":
      trace(space+"["+object+"] => Function ");
      break;
   }
}
/************* end debug functions ***************/