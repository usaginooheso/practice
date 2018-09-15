<?php
require_once(__DIR__.'/config.php');

$bbsApp = new \MyApp\BBS();

$posts = $bbsApp->getAllPosts();
$totalPosts = $bbsApp->getTotalPosts();
$totalPages = $bbsApp->getTotalPages();
$page = $bbsApp->getViewPage();
$viewFrom = $bbsApp->getViewFrom();
$viewTo = $bbsApp->getViewTo();

//POSTだった場合
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$bbsApp->post();
}

$error = $bbsApp->getError();

// var_dump($posts);
// exit;

 ?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset='utf-8'>
	<title>BBS sample</title>
	<link rel="stylesheet" href="styles.css">
</head>
<body>

<header id="header">
	<h1>BBS Sample</h1>
</header>

<div id="bbs">

<section><div id="posting_form">
	<h2>新規投稿</h2>

	<form action="" method="post">
		名前：<input type="text" name="myname" value=""><br>
		本文：<input type="text" name="mymessage" value="">
		<span id="error"><?=h($error);?></span>
		<br>
		<input type="hidden" name='token' value="<?= h($_SESSION['token'])?>">
		<input type="submit" value="送信"></input>
	</form>
</div></section>

<section><div id="view_posts">
	<h2>投稿一覧</h2>
	<p id="show_from_to">
		<?=h($totalPosts)?>件中
		<?php if ($viewFrom === $viewTo):?>
			<?=h($viewTo)?>件目を表示
		<?php else:?>
			<?=h($viewFrom)?>〜<?=h($viewTo)?>件目を表示
		<?php endif;?>
	</p>
	<ul>
		<?php if(count($posts)):?>
			<!-- <?php foreach ($posts as $post):?>
				<?php list($no, $name,$message,$date) = explode("\t", $post);?>
					<li class="posts">
						<p class="no"><?=h(sprintf('%04d',$no));?></p>
						<p class="message"><?=h($message);?></p>
						<p class="name">(<?=h($name);?>)</p>
						<p class="date">(<?=h($date);?>)</p>
					</li>
			<?php endforeach;?> -->

			<?php foreach ($posts as $post):?>
				<li class="posts">
					<p class="no"><?=h(sprintf('%04d',$post['id']));?></p>
					<p class="message"><?=h($post['body']);?></p>
					<p class="name">(<?=h($post["username"]);?>)</p>
					<p class="date">(<?=h($post['created']);?>)</p>
				</li>
			<?php endforeach;?>

		<?php else:?>
			<p>投稿はまだありません</p>
		<?php endif;?>
	</ul>
</div></section>


<!-- ページネーション############ -->
<section><div id="paging">
	<!-- 前へ #################### -->
	<?php if($page > 1):?>
		<a href='?p=<?=h($page - 1);?>'>&laquo;前へ</a>
	<?php endif;?>

	<!-- ページ数 #################### -->
	<?php if ($totalPages > 1):?>
		<?php for ($i = 1; $i <= $totalPages; $i++):?>
			<a href='?p=<?=h($i)?>'><?=h($i)?></a>
		<?php endfor?>
	<?php endif?>

	<!-- 次へ -->
	<?php if($page < $totalPages):?>
		<a href='?p=<?=h($page + 1);?>'>次へ&raquo;</a>
	<?php else:?>

	<?php endif;?>
</div></section>

</div>
</body>
</html>
