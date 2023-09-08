<?php
// ----------------------------------------------------------------------------
//Lecture variable en GET
// ----------------------------------------------------------------------------
  error_reporting(0);
  if (!isset($drprf)) $drprf=$_GET['drprf'];
  if (!isset($dragd)) $dragd=$_GET['dragd'];
  if (!isset($drnt)) $drnt=$_GET['drnt'];
  if (!isset($dradm)) $dradm=$_GET['dradm'];
  if (!isset($lang)) $lang=$_GET['lang'];
  if (!isset($file)) $file=$_GET['file'];


  if (file_exists("files.".$lang."/toc.php")) $AIDE_FILES="files.".$lang."/"; else $AIDE_FILES="files/";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">
<html>

  <head>
    <meta name="generator" content="HelpNDoc Free">
    <title>Phenix V. 5.00 - Aide Version 2.1.13</title>
  </head>
<?php
  if ($file!="") {
?>
    <frameset rows="*" cols="*">
      <frame src="<?php echo $AIDE_FILES.$file ?>" name="FrameMain">
    </frameset>
</noscript>
</html>
  
<?php
  } else {
?>
  <frameset rows="*" cols="200,*">
    <frame src="<?php echo $AIDE_FILES."toc.php?drprf=".$drprf."&dragd=".$dragd."&drnt=".$drnt."&dradm=".$dradm; ?>" name="FrameTOC">
    <frame src="<?php echo $AIDE_FILES; ?>{E4CF7E7D-35BE-4448-A6A5-92834672B4EF}.htm" name="FrameMain">
<?php
  }
?>
  </frameset>
</html>
