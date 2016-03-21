
<?php
    $dom = new DOMDocument();
    $dom->formatOutput=true;
    
    $PageContents = urldecode($_POST['PageContents']);
    $dom->loadHTML($PageContents);
    $dom->saveHTMLFile('latestpage.html');
	echo urldecode($_POST['url']) . " has been saved."
?>