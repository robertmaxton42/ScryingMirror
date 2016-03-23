
<?php
    $dom = new DOMDocument();
    $dom->formatOutput = true;
    
    //Get the webpage by POST from the GreaseMonkey plugin
    //Parse into a DOM, and grab the head tag while we're at it.
    $PageContents = urldecode($_POST['PageContents']);
    $dom->loadHTML($PageContents);
    $head = $dom->getElementsByTagName('head')->item(0);

    //Rebase relative links.
    //Google Translate duplicates this work, I think.
    $baseTag = $dom->createElement('base');
    $baseTag->setAttribute('href', urldecode($_POST['URL']));
    $baseTag->setAttribute('title', "Added by Dynamic Reader"); //Debug code
    $head->appendChild($baseTag);

    /*Build table of all links on the page.
     *Note that it's not necessary to reparse relative links, since the <base> tag
     *inserted above gets carried over into the Google Translate iframe.*/
    $links = $dom->getElementsByTagName('a');
    $jsLinks = json_encode($links);
    $scriptdata = "var links = " . $js_links . ";\n";
    $scriptText = $dom->createTextNode($scriptdata);
    $scriptTag = $dom->createElement('script');
    
    $scriptTag->appendChild($scriptText);
    $head->appendChild($scriptTag);

    //Inject javascript: rewrite links in order to their proper destinations.

    //Save the modified webpage
    $dom->saveHTMLFile('latestpage.html');


    //Debug code.
    echo $jsLinks;
?>