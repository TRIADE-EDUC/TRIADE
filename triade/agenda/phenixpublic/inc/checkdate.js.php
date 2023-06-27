<?php
  /**************************************************************************\
  * Phenix Agenda                                                            *
  * http://phenix.gapi.fr                                                    *
  * Written by    Stephane TEIL            <phenix-agenda@laposte.net>       *
  * Contributors  Christian AUDEON (Omega) <christian.audeon@gmail.com>      *
  *               Maxime CORMAU (MaxWho17) <maxwho17@free.fr>                *
  *               Mathieu RUE (Frognico)   <matt_rue@yahoo.fr>               *
  *               Bernard CHAIX (Berni69)  <ber123456@free.fr>               *
  * --------------------------------------------                             *
  *  This program is free software; you can redistribute it and/or modify it *
  *  under the terms of the GNU General Public License as published by the   *
  *  Free Software Foundation; either version 2 of the License, or (at your  *
  *  option) any later version.                                              *
  \**************************************************************************/
?>
  <SCRIPT type="text/javascript">
  <!--
    /* ******************************************************
    **     Controle si une date respecte le calendrier     **
    ****************************************************** */
    function DateIsValid_val(jj, mm, aaaa) {
      if (jj < 1 || jj > 31 || mm < 1 || mm > 12)
        return false;
      if (mm == 2) {
        if (jj == 30 || jj == 31)
          return false;
        if (jj == 29)
          return ((aaaa % 4 == 0 && aaaa % 100 != 0) || aaaa % 400 == 0);
      } else if (jj == 31)
        return !( mm == 4 || mm == 6 || mm == 9 || mm == 11 );
      return true;
    }

    function chk_date_format(jj_mm_aaaa) {
      //Declarations
      var delim_char;
      var tab_jma;
      var msg1;
      var msg2;
      var ctrlOK;

      //Corps de la fonction
      //Initialisations
      delim_char = '/';
      msg1 = "<?php echo trad('CHECKDATE_MSG1'); ?>";
      msg2 = "<?php echo trad('CHECKDATE_MSG2'); ?>";

      //Verification de la longueur du param
      if (jj_mm_aaaa.value.length !== 10 && jj_mm_aaaa.value.length !== 8) {
        if (jj_mm_aaaa.value != '') {
          window.alert(msg1);
          jj_mm_aaaa.value = '';
          jj_mm_aaaa.focus();
          return false;
        }
      } else {
        //Decoupage de la date en jj, mm, aaaa
        tab_jma = jj_mm_aaaa.value.split(delim_char);
        //Verification de la longueur du tableau (3 cases) :
        // [jj][mm][aaaa]
        if (tab_jma.length !== 3) {
          window.alert(msg1);
          jj_mm_aaaa.value = '';
          jj_mm_aaaa.focus();
          return false;
        } else {
          //Adaptation des dates en jj/mm/aa en jj/mm/aaaa (pivot 60)
          if (tab_jma[2].length == 2 && !(isNaN(tab_jma[2]))) {
            var _pivot = 60;
            tab_jma[2] = (Number(tab_jma[2])>_pivot) ? (Number(tab_jma[2])+1900).toString() : (Number(tab_jma[2])+2000).toString();
            jj_mm_aaaa.value = tab_jma[0] + delim_char + tab_jma[1] + delim_char + tab_jma[2];
          }
          //Verification de la validite des chaines de caracteres
          //jj, mm, aaaa
          if ((tab_jma[0].length !== 2) || (tab_jma[1].length !== 2) || (tab_jma[2].length !== 4 && tab_jma[2].length !== 2) ||
            (isNaN(tab_jma[0])) || (isNaN(tab_jma[1])) || (isNaN(tab_jma[2])) || (tab_jma[2] <= 0)) {
            window.alert(msg1);
            jj_mm_aaaa.value = '';
            jj_mm_aaaa.focus();
            return false;
          } else {
            //Verification de la date dans le calendrier
            ctrlOK = DateIsValid_val(tab_jma[0], tab_jma[1], tab_jma[2]);
            if (! ctrlOK) {
              window.alert(msg2);
              jj_mm_aaaa.value = '';
              jj_mm_aaaa.focus();
              return false;
            }
          }
        }
      }
      return true;
    }

    function evalDate(_date) {
      //Declarations
      var delim_char;
      var tab_jma;

      //Corps de la fonction
      //Initialisations
      delim_char = '/';
      //Decoupage de la date en jj, mm, aaaa
      tab_jma = _date.split(delim_char);
      // Pas besoin de verif, car la fonction precedente l'a deja faite
      return (tab_jma[2]+tab_jma[1]+tab_jma[0]);
    }

    //N'autorise que [0-9] et / comme saisie
    function onlyChar(ev) {
      ev || (ev=window.event);
      if ((ev.keyCode < 47) || (ev.keyCode > 57)) {
        ev.returnValue=false;
      }
      if ((ev.which < 47) || (ev.which > 57)) {
        return (false);
      }
      return (true);
    }
  //-->
  </SCRIPT>
