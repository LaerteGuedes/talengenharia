<?php
class FeedController extends ZC_Controller_Action {
	public function indexAction() {
		$dbPagina = new Db_PagPagina();
		$vo = $dbPagina->fetchAll(null,'ID DESC',10);

		$entries = array();
		foreach ($vo AS $o) {
			$entry = array(
				'title'       => "{$o->TITULO}",
				'link'        => "http://" . $_SERVER['SERVER_NAME'].$o->getLink(),
				'publishdate' => $o->DATA,
                'guid' => "http://" . $_SERVER['SERVER_NAME'].$o->getLink(),
				'description' => "{$o->RESUMO}",
			);
			 array_push($entries, $entry);
		 }
		 
		 $rss = array(
		  'title'   => 'Site',
		   'link'    => "http://" . $_SERVER['SERVER_NAME'],
		   'charset' => 'UTF-8',
		   'entries' => $entries
		 );

		 // Import the array
		 $feed = Zend_Feed::importArray($rss, 'rss');
		 $feed->send();

//		 // Write the feed to a variable
//		 $rssFeed = $feed->saveXML();
//
//		 // Write the feed to a file residing in /public/rss
//		 $fh = fopen($this->config->feed->popular_games, "w");
//		 fwrite($fh, $rssFeed);
//		 fclose($fh);
  
		exit;
	}
}