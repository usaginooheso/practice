<?php

namespace MyApp;

class BBS {
	public $totalPosts;		//トータル投稿数
	private $_allPosts; //全投稿データ（配列）
	public $showPosts;		//全投稿データ（配列・新しいもの順）
	public $totalPages;		//トータルページ数
	public $viewPage; 		//表示する（表示中の）ページ
	public $offset; 		//DBから取得するときの開始位置
	//全投稿を表示するために必要となる総ページ数は定数
	public $viewFrom;
	public $viewTo;

	public function __construct() {
		$this->_createToken();

		//表示するページ（GETがセットされていたらそれを表示ページにする）
		if (isset($_GET['p']) && preg_match('/^[1-9][0-9]*$/', $_GET['p'])) {
			$this->viewPage = $_GET['p'];
		} else {
			$this->viewPage = 1;
		}

	}

	public function getShowPosts() {
		//offsetを算出
		$this->offset = ($this->viewPage - 1) * VIEW_POSTS_PER_PAGE;
		$this->viewFrom = $this->offset +1;

		// ファイルから1行ずつ読み込んで、配列にして格納
		$all = file(DATAFILE);
		$this->_allPosts = array_reverse($all);
		//全投稿（配列）を逆順にする
		$this->showPosts = array_slice($this->_allPosts, $this->offset, VIEW_POSTS_PER_PAGE);

		$this->_getTotalPosts();	//全投稿数を取得
		$this->_getTotalPages();		//必要なページ数を取得

		//○件目から○件目までの「まで」の数
		$this->viewTo = $this->offset + VIEW_POSTS_PER_PAGE;
		if ($this->viewTo > $this->totalPosts) {
			$this->viewTo = $this->totalPosts;
		}

		return $this->showPosts;
	}

	//全投稿数を取得
	private function _getTotalPosts() {
		$this->totalPosts = count($this->_allPosts);
	}

	//必要なページ数を取得
	public function _getTotalPages() {
		$num = ceil($this->totalPosts / VIEW_POSTS_PER_PAGE);
		$this->totalPages = $num;
	}

	private function _createToken() {
		if (!isset($_SESSION['token'])) {
			// Tokenを生成
			$bytes = openssl_random_pseudo_bytes(16);
			$token = bin2hex($bytes);
			$_SESSION['token'] = $token;
		}
	}

	private function _validateToken() {
		if (!isset($_SERVER['token']) && !isset($_POST['token']) || $_SESSION['token'] !== $_POST['token']) {
			throw new \Exception('不正な処理です');
		}
	}

	private function _validateName() {
		if (isset($_POST['myname']) && is_string($_POST['myname'])) {
			//名前が入力されていない場合は名無しさんとする
			if ($_POST['myname'] === '') {
				$this->_name = '名無しさん';
			} else {
				$this->_name = trim($_POST['myname']);
				$this->_name = str_replace("\t", " ", $this->_name);
			}
		} else {
			//echo '名前が不正送信されました';
			throw new \Exception('名前が不正送信されました');

		}
	}

	private function _validateMessage() {
		if (isset($_POST['mymessage']) && is_string($_POST['mymessage'])) {
			if ($_POST['mymessage'] === '') {
				// echo '本文が入力されていません';
				throw new \Exception('本文が入力されていません');
			} else {
				//本文の整形
				$this->_message = trim($_POST['mymessage']);
				$this->_message = str_replace("\t", " ", $this->_message);
			}
		} else {
			throw new \Exception('本文が不正送信されました');
		}
	}

	public function post() {
		try {
			//$this->_getTotalPosts();
			$this->_validateName();
			$this->_validateMessage();
			$this->_validateToken();

			//何件目の投稿か
			$postNo = $this->totalPosts +1;

			//投稿日時
			$postedDate = date('Y-m-d H:i');

			//書き込みデータの整形
			$newPost = $postNo . "\t" . $postedDate . "\t"	. $this->_name . "\t" . $this->_message . "\n";

			// 投稿をファイルに書き出す
			$fp = fopen(DATAFILE, 'ab');
			flock($fp, LOCK_EX); //排他ロックをかける
			fwrite($fp, $newPost);
			flock($fp, LOCK_UN); //ロック解除
			fclose($fp);
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/bbs/');

		} catch(\Exception $e) {
			//エラーをセッションに入れる
			$_SESSION['error'] = $e->getMessage();

			//リダイレクト
			//header('Location: http://' . $_SERVER['HTTP_HOST'] . '/bbs/');
		}
	} //postここまで

	// エラー処理
	public function getError() {
		$err = null;
		if (isset($_SESSION['error'])) {
			$err = $_SESSION['error'];
			unset($_SESSION['error']);
		}
		return $err;
	}
} //クラスここまで
