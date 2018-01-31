
<?php
    $dom = new DOMDocument();
    $dom->formatOutput=true;
    
    $PageContents = urldecode($_POST['PageContents']);
    $dom->loadHTML($PageContents);
    $baseTag = $dom->createElement("base");
    $baseTag->setAttribute('href', urldecode($_POST['URL']));
    $dom->getElementsByTagName('head')->item(0)->appendChild($baseTag);
    $dom->saveHTMLFile('latestpage.html');
?>