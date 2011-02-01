<?

$p = new Playlist;

require dirname(__FILE__) . '/main.php';
Config::set('DEBUG', 'OFF');

$p->fetch();

$p->parse();

if (empty($p->links))
  exit('No audio files found');

if (count($p->links) > 3)
  $p->store();
//debug($p);

$p->headers();
$p->output();

class Playlist {
  function __construct(){
    if (!$url = $_GET['url'])
      exit('url parameter required');

    if (parse_url($url, PHP_URL_SCHEME) != 'http')
      exit('valid url parameter required');

    $this->url = preg_replace('/\#*$/', '', $url);
    $this->format = $_GET['format'] ? str_replace('.', '', $_GET['format']) : 'm3u';
  }

   function fetch(){
    $this->api = new API;
    $this->api->cache = TRUE;

    $http = array('header' => 'User-Agent: Playr (http://playr.hubmed.org)', 'timeout' => 60);
    $this->api->get_data($this->url, array(), 'html-dom', $http);

    $this->base = preg_match('/\/$/', $this->url) ? $this->url : dirname($this->url);
    $nodes = $this->api->xpath->query("head/base/@href");
    if ($nodes->length)
      $this->base = $nodes->item(0)->nodeValue;

    $this->title = $url;
    $nodes = $this->api->xpath->query("head/title");
    if ($nodes->length)
      $this->title = trim($nodes->item(0)->nodeValue);
  }

  function headers(){
    //header('Content-type: text/plain;charset=utf-8'); return;
    switch($this->format){
      case 'm3u':
      default:
        header('Content-type: audio/mpegurl; charset=UTF-8');
        header('Content-Disposition: inline; filename=playlist.m3u');
      break;

      case 'rss':
        header('Content-type: text/xml; charset=UTF-8');
        header('Content-Disposition: inline; filename=playlist-rss.xml');
      break;

      case 'xspf':
        header('Content-type: application/xspf+xml; charset=UTF-8');
        header('Content-Disposition: inline; filename=playlist.xspf');
      break;
    }
  }

  function parse(){
    $this->links = array();
    $nodes = $this->api->xpath->query("//a/@href");

    if (!$nodes->length)
      $nodes = $this->api->xpath->query("//enclosure/@url");

    if ($nodes->length)
      foreach ($nodes as $node)
        if ($link = $this->check_link($node->value))
          $this->links[$link] = array(
           'url' => absolute_url($link, $this->base),
           'title' => empty($node->ownerElement->textContent) ? basename($link) : trim($node->ownerElement->textContent),
           );
  }

  function output(){
    switch($this->format){
      case 'm3u':
      default:
        printf("#EXTM3U\n#EXTINF - info - -1,%s\n", str_replace("\n", '', $this->title));
        foreach ($this->links as $link)
          printf("#EXTINF:-1,%s\n%s\n", str_replace("\n", '', $link['title']), $link['url']);
      break;

      case 'rss':
        print '<?xml version="1.0" encoding="UTF-8"?>';
	      ?>
        <rss xmlns:itunes="http://www.itunes.com/DTDs/Podcast-1.0.dtd" version="2.0">
          <channel>
            <title><? h($title); ?></title>
            <link><? h($url); ?></link>
            <description><? h($url); ?></description>
            <? foreach ($this->links as $link): ?>
            <item>
              <title><? h($link['title']); ?></title>
              <guid><? h($link['url']); ?></guid>
              <enclosure url="<? h($link['url']); ?>" length="1" type="audio/mpeg"/>
            </item>
            <? endforeach; ?>
	        </channel>
	      </rss>
	    <?
      break;

      case 'xspf':
        print '<?xml version="1.0" encoding="UTF-8"?>';
	      ?>

        <playlist version="1" xmlns="http://xspf.org/ns/0/">
          <trackList>
            <? foreach ($this->links as $link): ?>
            <track>
              <location><? h($link['url']); ?></location>
              <title><? h($link['title']); ?></title>
              <annotation><? h($link['title']); ?></annotation>
	            <info><? h($url); ?></info>
            </track>
            <? endforeach; ?>
	        </trackList>
	      </playlist>
	    <?
      break;
    }
  }

  function check_link($link){
    $link = preg_replace('/^\/insta\.m3u\?url\=/', '', $link);

    $patterns = array(
     '/(\.|\%2E)mp[\dg](\W.*)?$/',
     '/\.m\da$/',
     '/\.mov?$/',
     '/mp3\.php\?/',
     '/freetrack.php\?id\=.+$/',
     '/index.php\?fileid\=.+$/',
     '/download.cfm\?mp3id\=.+$/',
     '/modules.php\?name\=Downloads\&d_op\=getit\&lid\=.+$/',
     );

    foreach ($patterns as $pattern)
      if (preg_match($pattern, $link))
        return $link;
  }

  function store(){
    $db = new DB;

    $count = count($this->links);

    $result = $db->query("SELECT * FROM `playlists` WHERE `url` = '%s'", $this->url);
    if (mysql_num_rows($result)){
      $item = mysql_fetch_object($result);
      if ($count != $item->files)
        $item->changes++;
      $db->query("UPDATE playlists SET title = '%s', files = %d, changes = %d WHERE url = '%s'", $this->title, $count, $item->changes, $this->url);
    }
    else{
      $db->query("INSERT INTO playlists (url, title, files) VALUES ('%s', '%s', %d)", $this->url, $this->title, $count);
    }
  }
}

