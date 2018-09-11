<?php

// BBSのデータ
$dataFile = 'bbs.dat';

// ファイルから1行ずつ読み込んで、配列にして$posts に格納
$posts = file_get_contents($dataFile);

// 改行を<br>に変換
$posts = str_replace("\n", "<br>", $posts);

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
        <?php if ($posts !== ''):?>
            <?php list($name, $message) = explode("\t", $posts);?>
            <li>
                （<?= $name;?>）
                <?= $message;?>
            </li>
        <?php else:?>
            <p>投稿はまだありません</p>
        <?php endif;?>
	</ul>
</section>

</body>
</html>
