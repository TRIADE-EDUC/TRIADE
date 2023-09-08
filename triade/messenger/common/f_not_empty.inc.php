<?php

function f_not_empty($option)
{
  if ($option != '')
    return true;
  else
    return false;
}

function f_is_empty($option)
{
  if ($option == '')
    return true;
  else
    return false;
}

?>