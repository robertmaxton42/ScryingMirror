
<?php
    $PageContents = $_POST['PageContents'];
	$PageContents = urldecode($PageContents);
	file_put_contents('latestpage.html', $PageContents);
	echo urldecode($_POST['url']) . " has been saved."
?>