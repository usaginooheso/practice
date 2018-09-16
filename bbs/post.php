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
<body>

<header id="header">
	<h1>BBS Sample</h1>
</header>

<main>
	<section>
		<div class="container my-5">
			<div id="posting_form" class="row justify-content-sm-center bg-light py-4">

				<div class="col-sm-auto col-lg-6">
					<h2>新規投稿</h2>
					<span id="error"><?=h($error);?></span>
					<form action="" method="post" enctype="multipart/form-data">
						<div class="form-group">
							<label class="control-label">タイトル</label>
							<input type="text" name="mytitle" value="" class="form-control">
						</div>
						<div class="form-group">
							<label class="control-label">名前</label>
							<input type="text" name="myname" value="" class="form-control">
						</div>

						<div class="form-group">
							<label class="control-label">本文</label>
							<textarea rows="10" cols="60" name="mymessage" value="" class="form-control"></textarea>
						</div>

						<div class="input-group">
							<label class="input-group-btn">
								<span class="btn btn-primary btn-lg">
									ファイルを選ぶ
									<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo h(MAX_FILE_SIZE)?>">
									<input type="file" name="image" style="display:none">
								</span>
							</label>
							<input type="text" class="form-control" readonly>
						</div><!-- input-group -->
						<div class="form-group mt-4">
							<input type="submit" value="投稿" class="btn btn-primary btn-lg"></input>
						</div>
						<input type="hidden" name='token' value="<?= h($_SESSION['token'])?>">
					</form>
				</div><!-- col-sm-auto -->

			</div><!-- posting_form -->
		</div><!-- Container -->
	</section>
</main>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
	<script>
		$(document).on('change', ':file', function() {
		    var input = $(this),
		    numFiles = input.get(0).files ? input.get(0).files.length : 1,
		    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
		    input.parent().parent().next(':text').val(label);
		});
	</script>
  </body>
</html>
