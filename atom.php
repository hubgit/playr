<?php 
require dirname(__FILE__) . '/main.php';

$db = new DB;
$result = $db->query("SELECT * FROM playlists ORDER BY id DESC LIMIT 10");

$date = date(DATE_ATOM);

header("Content-Type: application/atom+xml; charset=utf-8");
print '<?xml version="1.0" encoding="utf-8"?>';
?>

<feed xmlns="http://www.w3.org/2005/Atom">
    <title>Playr: New Playlists</title>
    <subtitle>New Playlists</subtitle>
    <id>tag:playr.hubmed.org,2005:new</id>
    <link rel="alternate" type="text/html" href="http://playr.hubmed.org/new.php" />
    <link rel="self" type="application/atom+xml" href="http://projects.hubmed.org/playr/atom.php"/>
    <author>
      <name>Playr</name>
      <email>alf@hubmed.org</email>
    </author>
    <icon>http://playr.hubmed.org/favicon.ico</icon>
    <updated><?php print $date; ?></updated>

<?php while ($item = mysql_fetch_object($result)): ?>
<?php $flash_params['playlistfile'] = 'play.php?url=' . rawurlencode($item->url) . '&format=.xspf'; ?>
    <entry>
      <link rel="alternate" type="text/html" href="<?php p($item->url, 'attr'); ?>" />
      <title><?php p($item->title); ?></title>
      <id>tag:playr.hubmed.org,2005:atom,<?php p($item->id, 'attr'); ?></id>
      <updated><?php print $date; ?></updated>
      <content type="xhtml">
        <div xmlns="http://www.w3.org/1999/xhtml">
          <p>Play <a href="<?php p(FLASH_PLAYER . '?' . http_build_query($flash_params), 'attr'); ?>">Flash</a></p>
          <p><a href="<?php p($item->url, 'attr'); ?>">Original page</a></p>
        </div>
      </content>
    </entry>	
<?php endwhile; ?>

</feed>
