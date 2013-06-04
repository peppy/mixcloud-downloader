<?php
$MIXCLOUD_FIRST_SERVER = 13;
$MIXCLOUD_LAST_SERVER = 22;

$url = $_REQUEST['url'];

if ($url)
{
	if (strpos($url, 'http://www.mixcloud.com/') !== 0)
		exit();

	$content = file_get_contents($url);

	if (preg_match('/data-preview-url="([^"]*)"/', $content, $m))
	{
		$result = str_replace('previews','cloudcasts/originals',$m[1]);
		$result = preg_replace('/stream[0-9][0-9]/', 'streamXX', $result);

		if (preg_match('/meta property="og:title" content="([^"]*)" \/>/', $content, $m))
			$title = $m[1];
		else
			$title = substr($result, strrpos($result, '/') + 1);

		for ($i = $MIXCLOUD_FIRST_SERVER; $i <= $MIXCLOUD_LAST_SERVER; $i++)
		{
			$testUrl = str_replace('streamXX', "stream$i", $result);
			$headers = get_headers($testUrl, 1);

			if ($headers[0] == 'HTTP/1.1 200 OK')
			{
				$result = $testUrl;
				echo "<div><a href='$result' download='$title.mp3'>$title</a></div>";
				return;
			}
		}
	}

	echo "<div>An error occurred</div>";
	return;
}
?>

<!DOCTYPE html>
<html>
	<head>
		<link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.min.css" rel="stylesheet">
		<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
	</head>

	<style>
	.maindiv {
		padding: 30px;
	}

	.info {
		padding-bottom: 20px;
	}
	</style>

	<body>
		<div class='maindiv'>
			<form id='mainform'>
				<input type="text" name="url" class="input-block-level" placeholder="Enter Mixcloud URL">
			</form>
			<div class='info'>
				Note that this may take up a 30 seconds to process.<br/>
				You may queue multiple URLs even while another is being processed.
			</div>

			<div id='results'>
			</div>

			<div id='loading' style='display: none'>
				<div class="progress progress-striped active">
				  <div class="bar" style="width: 100%;"></div>
				</div>
			</div>
		</div>

		<script>
			$("#mainform").submit(function() {
				$('#loading').fadeIn(200);
				$.post(window.location, $("#mainform").serialize(), function(data) {
					$('#results').append(data);
					$('#loading').hide();
				});
				return false;
			});
		</script>
	</body>

	<a href="https://github.com/peppy/mixcloud-downloader"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_gray_6d6d6d.png" alt="Fork me on GitHub"></a>
</html>
