// ---------------------------------------------------------------------------- //
// ---------------------------------------------------------------------------- //

 ejs_box2_actual = 0;
 ejs_box2_html_flag = 0;

 function ejs_box2_go()
         {
         if(document.getElementById)
                 {
                 ejs_box2_char = 1;
                 ejs_box2_affich(ejs_box2_actual)
                 ejs_box2_actual++;
                 if(ejs_box2_actual >= ejs_box2_message.length)
                         ejs_box2_actual = 0;
                 }
         }

 function ejs_box2_affich(lactual)
         {
         var pix = ejs_box2_message[lactual].charAt(ejs_box2_char);
         if(pix == "<")
                 ejs_box2_html_flag = 1;
         if(pix == ">")
                 ejs_box2_html_flag = 0;
         var texte = ejs_box2_message[lactual].substring(0,ejs_box2_char);
         document.getElementById("ejs_box2_box").innerHTML = texte;
         if(ejs_box2_char < ejs_box2_message[lactual].length)
                 {
                 ejs_box2_char++;
                 if(ejs_box2_html_flag == 1)
                         ejs_box2_affich(lactual);
                 else
                         setTimeout("ejs_box2_affich("+lactual+")",50)
                 }
         else
                 setTimeout("ejs_box2_go()",3000)
         }

window.onload = ejs_box2_go;

// ---------------------------------------------------------------------------- //
// ---------------------------------------------------------------------------- //
