<?php

namespace MyApp;

class BBS {
	private $_totalPosts;		//トータル投稿数
	private $_allPostedData;		//全投稿データ（配列・新しいもの順）
	private $_totalPages;		//トータルページ数
	private $_viewPage; 		//表示する（表示中の）ページ
	private $_viewFrom;
	private $_viewTo;

	private $_name;
	private $_title;
	private $_message;
	private $_imageType;
	private $_imageFileName;
	private $_thumbExists;

	private $_db;

	public function __construct() {
		$this->_createToken();

		//表示するページ（GETがセットされていたらそれを表示ページにする）
		if (isset($_GET['p']) && preg_match('/^[1-9][0-9]*$/', $_GET['p'])) {
			$this->_viewPage = $_GET['p'];
		} else {
			$this->_viewPage = 1;
		}

		$this->_connectDB();
	}

	public function getAllPosts() {
		//offsetを算出
		$offset = ($this->_viewPage - 1) * VIEW_POSTS_PER_PAGE;
		$this->_viewFrom = $offset +1;

		// ファイルから投稿データを取り出す
		// $posts = file(DATAFILE);
		// $posts = array_reverse($posts); // 配列を逆順にする
		// $this->_allPostedData = array_slice($posts, $offset, VIEW_POSTS_PER_PAGE);
		//投稿数を取得
		//$this->_totalPosts = count($posts);

		// DBから投稿データを取り出す
		$all = array_fill(0,4,0);
		$sql = "select id, username, title, message, imagePath, thumbExists, created from posts order by id desc limit ? offset ?";
		$stmt = $this->_db->prepare($sql);
		$stmt->execute(array(VIEW_POSTS_PER_PAGE, $offset));
		$posts = $stmt->fetchAll(\PDO::FETCH_ASSOC);
		$this->_allPostedData = $posts;

		// トータル投稿数を取得
		$sql = "select count(id) from posts";
		$this->_totalPosts = $this->_db->query($sql)->fetchColumn();

		// 終了
	    $stmt = null;
	    $pdo = null;

		//必要なページ数を取得
		//$this->_getTotalPages();
		$this->_totalPages = ceil($this->_totalPosts / VIEW_POSTS_PER_PAGE);

		//○件目から○件目までの「まで」の数
		$this->_viewTo = $offset + VIEW_POSTS_PER_PAGE;
		if ($this->_viewTo > $this->_totalPosts) {
			$this->_viewTo = $this->_totalPosts;
		}

		return $this->_allPostedData;
	}

	//全投稿数を取得
	public function getTotalPosts() {
		return $this->_totalPosts;
	}

	//必要なページ数を取得
	public function getTotalPages() {
		return $this->_totalPages;
	}

	public function getViewPage() {
		return $this->_viewPage;
	}

	public function getViewFrom() {
		return $this->_viewFrom;
	}
	public function getViewTo() {
		return $this->_viewTo;
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

	private function _validateTitle() {
		if (isset($_POST['mytitle']) && is_string($_POST['mytitle'])) {
			if ($_POST['mytitle'] === '') {
				throw new \Exception('タイトルが入力されていません');
			} else {
				//タイトルの整形
				$this->_title = trim($_POST['mytitle']);
				$this->_title = str_replace("\t", " ", $this->_title);
			}
		} else {
			throw new \Exception('本文が不正送信されました');
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

	private function _validateImage() {
		if (
			!isset($_FILES['image']) ||
			!isset($_FILES['image']['error'])
		) {
			throw new \Exception('画像のアップロードエラーです');
		}

		switch ($_FILES['image']['error']) {
			case UPLOAD_ERR_OK:
				return true;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new \Exception('ファイルサイズが大きすぎます');
			default:
				throw new \Exception('エラーです');
		}
	}

	private function _validateImageType() {
		$this->_imageType = exif_imagetype($_FILES['image']['tmp_name']);

		switch ($this->_imageType) {
			case IMAGETYPE_GIF:
				return 'gif';
			case IMAGETYPE_JPEG:
				return 'jpg';
			case IMAGETYPE_PNG:
				return 'png';
			default:
				throw new \Exception('画像はGIF/JPEG/PNGのみです');
		}
	}

	private function _saveImage($ext) {
		// 画像のファイル名を決める
		$this->_imageFileName = sprintf(
			'%s_%s.%s',
			time(),
			sha1(uniqid(mt_rand(),true)),
			$ext
		);

		// 画像の保存先のパス
		$savePath = IMAGES_DIR . '/' . $this->_imageFileName;

		//tmpから$savePathにファイルを移動（移動できたらtrueが返る）
		$res = move_uploaded_file($_FILES['image']['tmp_name'], $savePath);

		if ($res === false) {
			throw new \Exception('画像のアップロードに失敗しました');
		}
	}

	private function _createThumbnail($imagePath) {
		// 画像の寸法をチェック
		$imageSize = getimagesize($imagePath);
		$imageW = $imageSize[0];
		$imageH = $imageSize[1];

		// 指定したサイズより大きかったらサムネイルを作る
		if ($imageW > THUMB_WIDTH_LIMIT) {
			$this->_createThumbnailMain($imagePath, $imageW, $imageH);
		} else {
			$this->_thumbExists = false;
		}
	}

	private function _createThumbnailMain($imagePath, $imageW, $imageH) {
		// 画像の種類に合わせて画像リソースを作る
		switch ($this->_imageType) {
			case IMAGETYPE_GIF:
				$srcImage = imagecreatefromgif($imagePath);
				break;
			case IMAGETYPE_JPEG:
				$srcImage = imagecreatefromjpeg($imagePath);
				break;
			case IMAGETYPE_PNG:
				$srcImage = imagecreatefrompng($imagePath);
				break;
		}

		//サムネイルの高さを算出
		$thumbH = round($imageH * THUMB_WIDTH_LIMIT / $imageW);

		//メモリ上にサムネイル画像リソースを確保
		$thumbImage = imagecreatetruecolor(THUMB_WIDTH_LIMIT, $thumbH);

		//元の画像を縮小しリソースにコピーペーストする
		imagecopyresampled($thumbImage, $srcImage, 0,0,0,0, THUMB_WIDTH_LIMIT, $thumbH, $imageW, $imageH);

		// 画像をサムネフォルダに保存
		switch ($this->_imageType) {
			case IMAGETYPE_GIF:
				imagegif($thumbImage, THUMBS_DIR. '/' .$this->_imageFileName);
				break;
			case IMAGETYPE_JPEG:
				imagejpeg($thumbImage, THUMBS_DIR. '/' .$this->_imageFileName);
				break;
			case IMAGETYPE_PNG:
				imagepng($thumbImage, THUMBS_DIR. '/' .$this->_imageFileName);
				break;
		}

		$this->_thumbExists = true;
	}

	private function _connectDB() {
		//DB接続
		try {
			$this->_db = new \PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
			$this->_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			$this->_db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
		} catch (\PDOException $e) {
			throw new \Exception('Failed to connect DB');
			exit;
		}
	}

	private function _savePosttoDB($thumbExists) {
		$sql = "insert into posts (username, title, message, imagePath, thumbExists, created) values (:name, :title, :msg, :imagePath, :thumbExists, now())";
		$stmt = $this->_db->prepare($sql);
		$stmt->bindValue(':name', $this->_name, \PDO::PARAM_STR);
		$stmt->bindValue(':title', $this->_title, \PDO::PARAM_STR);
		$stmt->bindValue(':msg', $this->_message, \PDO::PARAM_STR);
		$stmt->bindValue(':imagePath', $this->_imageFileName, \PDO::PARAM_STR);
		$stmt->bindValue(':thumbExists', $thumbExists, \PDO::PARAM_BOOL);
		$stmt->execute();
	}

	private function _savePosttoFile() {
		//書き込みデータの整形
		$postNo = $this->_totalPosts +1; //投稿no
		$postedDate = date('Y-m-d H:i');	//投稿日時
		$newPost = $postNo . "\t" . $postedDate . "\t"	. $this->_name . "\t" . $this->_message . "\n";

		// 投稿をファイルに書き出す
		$fp = fopen(DATAFILE, 'ab');
		flock($fp, LOCK_EX); //排他ロックをかける
		fwrite($fp, $newPost);
		flock($fp, LOCK_UN); //ロック解除
		fclose($fp);
		header('Location: http://' . $_SERVER['HTTP_HOST'] . '/bbs2/');
	}

	public function post() {
		try {
			$this->_validateName();
			$this->_validateTitle();
			$this->_validateMessage();
			$this->_validateToken();

			if ($_FILES['image']['tmp_name'] !== '') {
				$this->_validateImage();
				$ext = $this->_validateImageType();
				$this->_saveImage($ext);
				$this->_createThumbnail($imagePath);
			}

			//投稿の保存
			//$this->_savePosttoFile();
			$this->_savePosttoDB($thumbExists);

			//リダイレクト
			header('Location: http://' . $_SERVER['HTTP_HOST'] . '/bbs2/');
			exit;

		} catch(\Exception $e) {
			//エラーをセッションに入れる
			$_SESSION['error'] = $e->getMessage();

			//リダイレクト
			//header('Location: http://' . $_SERVER['HTTP_HOST'] . '/bbs2/');
			//exit;
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
