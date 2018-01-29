<?php
function cleanData($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = strip_tags($data); //removes HTML and PHP tags from a string
  return $data;
}//end cleanData

?>