<?php

//ini_set('display_errors', FALSE);

require '/opt/libapi/main.php';
require dirname(__FILE__) . '/config.php';

define('PATH', sprintf('http://%s%s/', $_SERVER['SERVER_NAME'], dirname($_SERVER['SCRIPT_NAME'])));
define('FLASH_PLAYER', PATH . 'player.swf');

$flash_params = array(
 'repeat' => 'list',
 'autostart' => 'true',
 'skin' => 'bekle.swf',
 'playlist' => 'bottom',
 'playlistsize' => 400,
 'playlistfile' => '',
 );


function play_url($format, $url = NULL){
  return url(PATH . 'play.php', array('url' => $url, 'format' => $format));
}

