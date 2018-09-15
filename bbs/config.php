<?php

require_once(__DIR__.'/functions.php');
require_once(__DIR__.'/BBS.php');

session_start();

// 掲示板機能
define('DATAFILE', __DIR__ . '/bbs.dat');
define('VIEW_POSTS_PER_PAGE', 10);//1ページ中に何件まで表示するか

// 画像投稿機能
define('MAX_SIZE_UPLOAD', 1 * 1024 * 1024);	//1MBまで
define('THUMB_WIDTH', 400);	//サムネイル生成の閾値（px）

// 画像・サムネイル用のフォルダ
define('IMAGES_DIR', __DIR__ . '/images');
define('THUMBS_DIR', __DIR__ . '/thumbs');

//GDの存在チェック
if (!function_exists('imagecreatetruecolor')) {
  echo 'GD not installed';
  exit;
}

// DB
define('DB_NAME', 'bbs_php');
define('DB_USERNAME', 'dbuser');
define('DB_PASSWORD', 'sample');
define('DB_CHAR', 'utf8');
define('DB_HOST', 'localhost');
define(
  'DB_DSN', 'mysql:host='.DB_HOST.';dbname='.DB_NAME.(';charset='.DB_CHAR));

define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST']);
