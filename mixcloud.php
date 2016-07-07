<?php
define("MIXCLOUD_FIRST_SERVER", 13);
define("MIXCLOUD_LAST_SERVER", 22);

if(isset($_REQUEST["url"])) {
	$url = $_REQUEST["url"];	
}


if (isset($url)) {
	if(!$url) {
		exit();
	}
	$urlprefix = "http://www.mixcloud.com/";

	$alturls = array(
		"https://www.mixcloud.com/",
		"http://www.mixcloud.com/",
		"https://mixcloud.com/",
		"http://mixcloud.com/",
		"https://x.mixcloud.com/",
		"http://x.mixcloud.com/",
	);

	foreach($alturls as $alturl) {
		if (substr($url, 0, strlen($alturl)) === $alturl) {
			$url = substr($url, strlen($alturl));
			break;
		}
	}

	$url = $urlprefix . $url;

	$content = file_get_contents($url);

	if (!preg_match("/m-preview=\"([^\"]*)\"/", $content, $m)) {
		die("An error occurred (Is the mixcloud/cloudcast url correct?)");
	} else {
		$result = str_replace("previews", "c/originals", $m[1]);
		$result = preg_replace("/stream[0-9]/", "streamXX", $result);
		$result = preg_replace("/audiocdn[0-9]/", "streamXX", $result);
		
		if (preg_match("/meta property=\"og:title\" content=\"([^\"]*)\" \/>/", $content, $m)) {
			$title = $m[1];
		} else {
			$title = substr($result, strrpos($result, "/") + 1);
		}

		//getting server subdomain by pixel 
		preg_match("/https:\\/\\/(stream[0-9])\\.mixcloud\\.com\\/1x1\\.gif/", $content, $pxm);
		$result = str_replace("streamXX", $pxm[1], $result);
		$testUrl = str_replace(".mp3", ".m4a", $result);
		$testUrl = str_replace("originals/", "m4a/64/", $testUrl);


		$xreturn = "No server found for download";
		$return = $xreturn;
		$headers = get_headers($testUrl, 1);

		if ($headers[0] === "HTTP/1.1 200 OK") {
			$return = "<a href=\"" . $testUrl . "\" download=\"" . $title . ".mp3\">" . $title . "</a>";
		}
		echo $return;
	}
	exit();
}
?><!DOCTYPE html>
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
		<div class="maindiv">
			<form id="mainform">
				<input type="text" name="url" class="input-block-level" placeholder="Enter Mixcloud URL">
			</form>
			<div class="info">
				Note that this may take up a 30 seconds to process.<br/>
				You may queue multiple URLs even while another is being processed.
			</div>

			<div id="results">
			</div>

			<div id="loading" style="display: none;">
				<div class="progress progress-striped active">
				  <div class="bar" style="width: 100%;"></div>
				</div>
			</div>
		</div>

		<script>
			$('#mainform').submit(function() {
				$('#loading').fadeIn(200);
				$.post(window.location, $('#mainform').serialize(), function(data) {
					$('#results').append('<div>' + data + '</div>');
					$('#loading').hide();
				});
				return false;
			});
		</script>
	</body>

	<a href="https://github.com/peppy/mixcloud-downloader"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_gray_6d6d6d.png" alt="Fork me on GitHub"></a>
</html>
