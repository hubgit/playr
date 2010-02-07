<!DOCTYPE html>
<?php 
require dirname(__FILE__) . '/main.php';

$db = new DB;

$start = isset($_GET['start']) ? (int) $_GET['start'] : 0;
$start = max($start, 0);

$n = 100;
$previous = max($start - $n, 0);

$result = $db->query("SELECT * FROM playlists ORDER BY id DESC LIMIT %d,%d", $start, $n);
?>
<html>
  <head>
    <meta charset=utf-8>
    <title>Playr: New Playlists</title>
	  
    <link rel=stylesheet href=style.css> 
    <link rel=alternate type=application/atom+xml title="Atom feed of new playlists" href=atom.php>
  </head>
	
  <body>
<?php include dirname(__FILE__) . '/nav.php'; ?>
	  
	  <table id=playlist>
	    <colgroup span=1>
	    <colgroup span=1>
	    <colgroup span=4 class=play>
	    <thead>
	      <tr>
		      <th>Original page</th>
		      <th># of files</th>
		      <th colspan=4>Play</th>
	      </tr>
	    </thead>
	    
	    <tbody>	
<?php while ($item = mysql_fetch_object($result)): ?>
	      <tr>
		      <td><a href="<?php p($item->url, 'attr'); ?>"><?php print(truncate($item->title, 90, '...')); ?></a></td>
		      <td class=file><?php p($item->files); ?></td>
<?php foreach (array('m3u', 'xspf') as $format): ?>
	        <td><a class=play href="play.php?url=<?php p(rawurlencode($item->url), 'attr'); ?>&format=.<?php print $format; ?>"><?php print strtoupper($format); ?></a></td>
<?php endforeach; ?>
          <td><a class=play href="player.swf?repeat=list&autostart=true&skin=bekle.swf&playlist=bottom&playlistsize=400&playlistfile=<?php p(rawurlencode(sprintf('play.php?url=%s&format=.xspf', rawurlencode($item->url))), 'attr'); ?>">Flash</a></td>
		      <td><?php if ($item->changes): ?><a class="play" href="play.php?url=<?php p(rawurlencode($item->url), 'attr'); ?>&format=.rss">Podcast</a><?php else: ?> <?php endif; ?></td>
	      </tr>
<?php endwhile; ?>
	    </tbody>
    </table>
    
    <nav id=prevnext>
      <?php if ($start): ?><a href="new.php?start=<?php print $previous; ?>">Newer</a><?php endif; ?>
      <a href="new.php?start=<?php print $start + $n; ?>">Older</a>
    </nav>
  </body>
</html>

