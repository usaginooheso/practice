<?php

// BBSのデータ
$dataFile = 'bbs.dat';

// ファイルから1行ずつ読み込んで、配列にして$posts に格納
$posts = file($dataFile);

 ?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset='utf-8'>
	<title>掲示板（投稿一覧）</title>
</head>
<body>

<section>
	<h1>投稿一覧</h1>
	<ul>
		<?php if(count($posts)):?>
			<?php foreach ($posts as $post):?>

				<?php list($name,$message) = explode("\t", $post);?>
					<li>
						(<?= $name;?>)
						<?= $message;?><br>
					</li>
			<?php endforeach;?>
		<?php else:?>
			<p>投稿はまだありません</p>
		<?php endif;?>
	</ul>
</section>

</body>
</html>
