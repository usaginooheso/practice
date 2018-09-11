<?php

require_once(__DIR__.'/functions.php');
require_once(__DIR__.'/BBS.php');

session_start();

define('DATAFILE', __DIR__ . '/bbs.dat');
define('VIEW_POSTS_PER_PAGE', 10);//1ページ中に何件まで表示するか

//ここから画像投稿機能
define('MAX_SIZE_UPLOAD', 1 * 1024 * 1024);	//1MBまで
define('THUMB_WIDTH', 400);	//サムネイル生成の閾値（px）
//画像・サムネイル用のフォルダ
define('IMAGES_DIR', __DIR__ . '/images');
define('THUMBS_DIR', __DIR__ . '/thumbs');

//GDの存在チェック
if (!function_exists('imagecreatetruecolor')) {
  echo 'GD not installed';
  exit;
}
