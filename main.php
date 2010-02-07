<?php

require '/opt/libapi/main.php';
require dirname(__FILE__) . '/db.php';

define('ROOT_PATH', sprintf('http://%s%s/', $_SERVER['SERVER_NAME'], dirname($_SERVER['SCRIPT_NAME'])));
define('PLAYER', ROOT_PATH . 'play.php');
define('FLASH_PLAYER', ROOT_PATH . 'player.swf');

$flash_params = array(
 'repeat' => 'list',
 'autostart' => 'true',
 'skin' => 'bekle.swf',
 'playlist' => 'bottom',
 'playlistsize' => 400,
 'playlistfile' => '',
 );
 
function p(){
  $arguments = func_get_args();
  call_user_func_array(array('API', 'output'), $arguments);
}

function truncate($string, $length, $suffix = ''){
  if (mb_strlen($string) <= $length)
    return $string;

  if ($dots)
    $length -= mb_strlen($suffix) + 1;

  $string = mb_substr($string, 0, $length);

  if ($suffix)
    $string .= ' ' . $suffix;

  return $string;
}

