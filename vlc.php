<!DOCTYPE html>
<?php require dirname(__FILE__) . '/main.php'; ?>
<html>
  <head>
    <meta charset=utf-8>
    <title>Playr VLC</title>
    <style>
    #playlist { -moz-box-shadow: 0 3px 10px 2px #777 }
    </style>
  </head>
	
  <body>
    <?php if (!$_GET['url']): ?>
  	<form method=get>
      <input name=url size=50 title=URL> <input type=submit value=play>
    </form>
    <?php else: ?>
    <div>
      <embed type="application/x-vlc-plugin" id="playlist" autoplay="yes" loop="no" hidden="no" target="http://playr.hubmed.org/playlist.cgi?url=<?php p(rawurlencode($_GET['url'])); ?>&format=.xspf"/>
    </div>
    
    <script>
    var VLCController = {
      playlist: document.getElementById("playlist"),
      play: function(){ VLCController.playlist.play(); return false; },
      stop: function(){ VLCController.playlist.stop(); return false; },
      fullscreen: function(){ VLCController.playlist.fullscreen(); return false; },
    }    
    </script>
    
    <div>
      <a href="#play" onclick="VLCController.play();">Play</a>
      <a href="#stop" onclick="VLCController.stop();">Stop</a>
      <a href="#fullscreen" onclick="VLCController.fullscreen();">Fullscreen</a>
    </div>
    <?php endif; ?>

  </body>
</html>
