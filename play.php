<?php

$p = new Playlist;

require dirname(__FILE__) . '/main.php';
Config::set('DEBUG', 'OFF');

$p->fetch();
$p->headers();

$p->parse();
if (empty($p->links))
  exit();
if (count($p->links) > 3)
  $p->store();
  
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
    $api = new API;
    
    $http = array('header' => 'User-Agent: Playr (http://playr.hubmed.org)', 'timeout' => 60);
    $this->xml = $api->get_data($this->url, array(), 'html', $http);

    $this->base = dirname($this->url);
    $nodes = $this->xml->xpath("head/base/@href");
    if (!empty($nodes))
      $this->base = (string) $nodes[0];
      
    $this->title = $url;
    $nodes = $this->xml->xpath("head/title");
    if (!empty($nodes))
      $this->title = (string) $nodes[0];
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
    $nodes = $this->xml->xpath("//a");
    
    if (!empty($nodes))
      foreach ($nodes as $node)
        if ($link = $this->check_link((string) $node['href']))
          $this->links[] = array(
           'url' => $this->absolute_url($link),
           'title' => (string) $node,
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
            <title><?php p($title); ?></title>
            <link><?php p($url); ?></link>
            <description><?php p($url); ?></description>
            <?php foreach ($this->links as $link): ?>
            <item>
              <title><?php p($link['title']); ?></title>
              <guid><?php p($link['url']); ?></guid>
              <enclosure url="<?php p($link['url'], 'attr'); ?>" length="1" type="audio/mpeg"/>
            </item>
            <?php endforeach; ?>    
	        </channel>
	      </rss>
	    <?php	
      break;
      
      case 'xspf':
        print '<?xml version="1.0" encoding="UTF-8"?>';
	      ?>

        <playlist version="1" xmlns = "http://xspf.org/ns/0/">
          <trackList>
            <?php foreach ($this->links as $link): ?>
            <track>
              <location><?php p($link['url']); ?></location>
              <title><?php p($link['title']); ?></title>
              <annotation><?php p($link['title']); ?></annotation>
	            <info><?php p($url); ?></info>
            </track>
            <?php endforeach; ?>  
	        </trackList>
	      </playlist>
	    <?php
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


  function absolute_url($url){
    /* return if already absolute URL */
    if (parse_url($url, PHP_URL_SCHEME) != '') 
      return $url;

    $first = substr($url, 0, 1);

    /* anchors and queries */
    if ($first == '#' || $first == '?')
      return $this->base . $url;

    /* parse base URL and convert to local variables: $scheme, $host, $path */
    extract(parse_url($this->base));

    /* remove non-directory element from path */
    $path = preg_replace('#/[^/]*$#', '', $path);

    /* destroy path if relative url points to root */
    if ($first == '/') 
      $path = '';

    /* dirty absolute URL */
    $url = "$host$path/$url";

    /* replace '//' or '/./' or '/foo/../' with '/' */
    for ($n = 1; $n > 0; $url = preg_replace(array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#'), '/', $url, -1, $n)) {}

    /* absolute URL is ready! */
    return $scheme . '://' . $url;
  }                                
}

