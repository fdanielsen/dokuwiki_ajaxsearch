<?php
/**
 * XMLHttpRequest handler for search functions.
 *
 * Currently only provides fulltext search in titles and pages, returning
 * an ordered JSON result list with details about each hit.
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');

require_once(DOKU_INC.'inc/init.php');
require_once(DOKU_INC.'inc/common.php');
require_once(DOKU_INC.'inc/fulltext.php');
require_once(DOKU_INC.'inc/pageutils.php');

session_write_close();

header('Content-Type', 'application/json');

if (empty($_GET['q'])) {
  echo json_encode(array('error' => 'Missing query value.'));
  exit;
}

$result = array();

$titleMatches = ft_pageLookup($_GET['q'], false, true);
foreach ($titleMatches as $pagename => $title) {
  $meta = p_get_metadata($pagename);
  $check = false;
  $hid = sectionID($title, $check);
  $url = wl($pagename) . "#{$hid}";

  $result[] = array(
    'page' => $pagename,
    'meta' => $meta,
    'numhits' => 1,
    'title' => $title,
    'url' => $url,
  );
}

$matches = ft_pageSearch($_GET['q'], $highlight);
foreach ($matches as $pagename => $numhits) {
  $meta = p_get_metadata($pagename);
  $url = wl($pagename);

  $result[] = array(
    'page' => $pagename,
    'meta' => $meta,
    'numhits' => $numhits,
    'title' => $meta['title'],
    'url' => $url,
  );
}

echo json_encode($result);
