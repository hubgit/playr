<!DOCTYPE html>
<? require dirname(__FILE__) . '/main.php'; ?>
<html>
  <head>
    <meta charset=utf-8>
    <title>Playr</title>

    <link rel=stylesheet href=style.css>
  </head>

  <body>
<? include dirname(__FILE__) . '/nav.php'; ?>

  <article>
	  <h1>Playr</h1>

    <section>
      <h2>Bookmarklets</h2>
      <p>Create playlists from pages of music or video files with these bookmarklets:</p>

      <ul id=bookmarklets>
  <? foreach (array('m3u', 'rss', 'xspf') as $format): ?>
        <li><a href="javascript:location.href='<? h(play_url($format)); ?>&url=' + encodeURIComponent(location.href);"><? print strtoupper($format); ?></a>
  <? endforeach; ?>
		    <li><a href="javascript:location.href='<? h(url(FLASH_PLAYER, array('repeat' => 'list', 'autostart' => 'true', 'skin' => 'bekle.swf', 'playlist' => 'bottom', 'playlistsize' => 400))); ?>&playlistfile='+ encodeURIComponent('<? h(play_url('xspf')); ?>&url='+encodeURIComponent(location.href))">Flash Audio</a>
      </ul>
     </section>

     <section>
      <p>Alternatively, enter the address of any web page that contains links to music files:</p>

      <form action=play.php method=get>
        <input type=hidden name=format value=m3u>
        <input name=url size=50 title=URL> <input type=submit value=play>
      </form>
     </section>

     <section>
      <h2>Instructions</h2>

	    <p>The bookmarklets on this page can be used on any web page that contains links to audio files; those pages will be added to <a href=new.php>the database of new playlists</a>, too.</p>
	    <p>To use the bookmarklets, drag the link to your browser's bookmarks toolbar, navigate to a page containing linked audio files, then press the bookmarklet to generate a playlist.</p>	    <p>M3U playlists play best in WinAMP or QuickTime, SMIL playlists play best in RealPlayer, while Flash playlists use the <a href="http://musicplayer.sourceforge.net/">XSPF Web Music Player</a> to play within a web browser. RSS playlists are media feeds that can be subscribed to in iTunes using the 'Subscribe to Podcast' menu option, as well as other podcast clients.</p>
	    <p>The '<a href=new.php>New Playlists</a>' section contains all the playlists that other people have made, including a link to the original page.</p>
	    <p>The '<a href=blogs.php>Music Weblogs</a>' page contains playlists that change often. These are the best playlists to subscribe to in media feed aggregators such as iTunes, via the 'Podcast' link.<p>
	    <p>For any questions, suggestions or bug reports, <a href=mailto:alf@hubmed.org>send an email</a>.</p>
	   </section>
	 </article>

  </body>
</html>

