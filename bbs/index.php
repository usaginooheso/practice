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

 ?>



<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset='utf-8'>
	<title>BBS sample</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<link rel="stylesheet" href="normalize.css">
	<link rel="stylesheet" href="styles.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

</head>
<body class="bg-light">

<header id="header">
	<h1>BBS Sample</h1>
	<a href="post.php">投稿</a>
</header>

<main>
	<div class="container py-2 px-5">
		<section>
			<div id="num_post" class="">
				<h2 class="mt-5">投稿一覧</h2>
				<p id="show_from_to">
					<?=h($totalPosts)?>件中
					<?php if ($viewFrom === $viewTo):?>
						<?=h($viewTo)?>件目を表示
					<?php else:?>
						<?=h($viewFrom)?>〜<?=h($viewTo)?>件目を表示
					<?php endif;?>
				</p>
			</div>
		</section>

		<section>
			<ul>
				<?php if(count($posts)):?>
					<?php foreach ($posts as $post):?>
						<div class="my-5 border p-5">
							<li>

								<div class="row">
									<div class="col-sm-1 col-xl-1">
										<p><?=h(sprintf('%04d',$post['id']));?></p>
									</div>
									<div class="col-sm-5 col-xl-2">
										<p><?=h($post['created']);?></p>
									</div>
									<div class="col-sm-6 col-xl-9">
									</div>
								</div>

								<div class="row">
									<div class="col">
										<p><?=h($post["username"]);?></p>
									</div>
								</div>

								<div class="row">
									<div class="col">
										<p><?=h($post['message']);?></p>
									</div>
								</div>

								<?php if(isset($post['imagePath'])):?>
									<div class="row">
										<div class="col">
											<img src="images/<?=h($post['imagePath'])?>" width="200px">
										</div>
									</div>
								<?php endif;?>

							</li>
						</div>
					<?php endforeach;?>

				<?php else:?>
					<p>投稿はまだありません</p>
				<?php endif;?>
			</ul>
		</section>


		<!-- ページネーション############ -->
		<section>
			<div id="paging">
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
			</div>
		</section>
	</div>
</main>




    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>
