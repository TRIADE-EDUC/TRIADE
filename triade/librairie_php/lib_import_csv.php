<?php
/**
 *function CSV2Array
 *Convert CSV-text to 2d-array
 *(delimeter = ';', line ending = '\r\n' - only for windows platform)
 * @param string $query (query to database)
 * @return array
 */
function CSV2Array($content)
   {
     if ($content{strlen($content)-1}!="\r" && $content{strlen($content)-1}!="\n")
         $content .= "\r\n";

     $arr=array();
     $temp=$content;
     $tma=array();
     while (strlen($temp)>0)
           {
           if ($temp{0}=='"')
               {
               $temp=substr($temp,1);
               $str='';
               while (1)
                   {
                   $matches=array();
                   if (!preg_match('/^(.*?)"("*?)(;|\r\n)(.*)$/is',$temp,$matches))
                     return $arr;

                   $temp=$matches[4];
                   if (fmod(strlen($matches[2]),2)>0)
                       {
                       $str.=$matches[1].$matches[2].'"'.$matches[3];
                       continue;
                       }
                     else
                       {
                       $tma[]=preg_replace('/""/','"',$str.$matches[1].$matches[2]);
                       if ($matches[3]!=';')
                           {
                           $arr[]=$tma;
                           $tma=array();
                           }
                       break;
                       }
                   }
               }
             else
               {
               $matches=array();
               if (!preg_match('/^([^;\r\n]*)(;|\r\n)(.*)$/is',$temp,$matches))
                   return $arr;
               $tma[]=$matches[1];
               $temp=$matches[3];
               if ($matches[2]!=';')
                   {
                   $arr[]=$tma;
                   $tma=array();
                   }
               }
           }
     return $arr;
   }

/*
// --------- Test ---------------------------------------
$str=file_get_contents("data/fichier_ASCII/$fichier");
$rows=CSV2Array($str);
echo '<table border="1" cellSpacing="2" cellPadding="2">';
for($i=0;$i<count($rows);$i++)
   {
   echo '<tr>';
   for ($j=0;$j<count($rows[$i]);$j++)
       {
         echo '<td>'.$rows[$i][$j]. '</td>';
       }
   echo '</tr>';
   }
echo '</table>';
// -----------------------------------------------------
*/

?>