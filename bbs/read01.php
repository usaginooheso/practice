<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset='utf-8'>
	<title>掲示板（投稿一覧）</title>
</head>
<body>

<section>
	<h1>投稿一覧</h1>
	<?php

	// BBSのデータ
	$dataFile = 'bbs.dat';

	// ファイルを読み込み専用でオープンする
	$fp = fopen($dataFile, 'r');

	// ファイルの終端に達するまでループ
	while (!feof($fp)) {
		// ファイルから一行読み込む
		$line = fgets($fp);

		// 読み込んだ行を出力する
		echo $line;
		echo "<br>";
	}

	// ファイルをクローズする
	fclose($fp);

	?>
	</section>

</body>
</html>
