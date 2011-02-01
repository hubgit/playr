<?
require dirname(__FILE__) . '/main.php';

$db = new DB;
$result = $db->query("SELECT * FROM playlists ORDER BY id DESC LIMIT 10");

$now = time();

$feed = new Atom('Playr: New Playlists', array('name' => 'Playr'), array('link' => array('text/html' => 'http://playr.hubmed.org/new.php')));

while ($item = mysql_fetch_object($result)){
  $flash_params['playlistfile'] = play_url('xspf', $item->url);
  $entry = $feed->addEntry('tag:playr.hubmed.org,2005:atom,' . $item->id, $item->title, $now, NULL, array('link' => array('text/html' => $item->url)));
  $content = $feed->addContent($entry);

  $p = $feed->addTextChild($content, 'p', 'Play ');
  $a = $feed->addTextChild($p, 'a', 'Flash');
  $a->setAttribute('href', url(FLASH_PLAYER, $flash_params));

  $p = $feed->dom->createElement('p');
  $content->appendChild($p);
  $a = $feed->addTextChild($p, 'a', 'Original page');
  $a->setAttribute('href', $item->url);
}

$feed->output();

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
    <updated><? print $date; ?></updated>

<? while ($item = mysql_fetch_object($result)): ?>
<? $flash_params['playlistfile'] = play_url('xspf', $item->url); ?>
    <entry>
      <link rel="alternate" type="text/html" href="<? h($item->url); ?>" />
      <title><? h($item->title); ?></title>
      <id>tag:playr.hubmed.org,2005:atom,<? h($item->id); ?></id>
      <updated><? print $date; ?></updated>
      <content type="xhtml">
        <div xmlns="http://www.w3.org/1999/xhtml">
          <p>Play <a href="<? h(FLASH_PLAYER . '?' . http_build_query($flash_params)); ?>">Flash</a></p>
          <p><a href="<? h($item->url); ?>">Original page</a></p>
        </div>
      </content>
    </entry>
<? endwhile; ?>

</feed>

