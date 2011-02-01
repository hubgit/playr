<!DOCTYPE html>
<?
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
<? include dirname(__FILE__) . '/nav.php'; ?>

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
<? while ($item = mysql_fetch_object($result)): ?>
	      <tr>
		      <td><a href="<? h($item->url); ?>"><? print(truncate($item->title, 90, '...')); ?></a></td>
		      <td class=file><? h($item->files); ?></td>
<? foreach (array('m3u', 'xspf') as $format): ?>
	        <td><a class=play href="<? h(play_url($format, $item->url)); ?>"><? h(strtoupper($format)); ?></a></td>
<? endforeach; ?>
          <td><a class=play href="<? h(url(FLASH_PLAYER, array('repeat' => 'list', 'autostart' => 'true', 'skin' => 'bekle.swf', 'playlist' => 'bottom', 'playlistsize' => '400', 'playlistfile' => play_url('xspf', $item->url)))); ?>">Flash</a></td>
		      <td><? if ($item->changes): ?><a class="play" href="<? h(play_url('rss', $item->url)); ?>">Podcast</a><? else: ?> <? endif; ?></td>
	      </tr>
<? endwhile; ?>
	    </tbody>
    </table>

    <nav id=prevnext>
      <? if ($start): ?><a href="new.php?start=<? print $previous; ?>">Newer</a><? endif; ?>
      <a href="new.php?start=<? print $start + $n; ?>">Older</a>
    </nav>
  </body>
</html>

