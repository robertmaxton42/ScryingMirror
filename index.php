
<?php
    if (!$_POST) return; //Break if there's no POST.

    $dom = new DOMDocument();
    $dom->formatOutput = true;
    
    //Get the webpage by POST from the GreaseMonkey plugin
    //Parse into a DOM, and grab the head and body tags while we're at it.
    $PageContents = urldecode($_POST['PageContents']);
    $dom->loadHTML($PageContents);
    $head = $dom->getElementsByTagName('head')->item(0);
    $body = $dom->getElementsByTagName('body')->item(0);

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
    $hrefs = array();

    //Populate hrefs
    foreach ($links as $link) {
        $hrefs[] = $link->attributes->getNamedItem('href')->nodeValue;
    }

    //Convert to JSON
    $jsLinks = json_encode($hrefs);
    $hrefsJSON = "var links = " . $jsLinks . ";\n";
    $hrefsScriptNode = $dom->createTextNode($hrefsJSON);
    $hrefsScriptTag = $dom->createElement('script');
    
    $hrefsScriptTag->appendChild($hrefsScriptNode);
    $head->appendChild($hrefsScriptTag);

    //Inject javascript: rewrite links in order to their proper destinations.
    $rewriteScript =
        "function rewriteLinks() {
            var aTags = document.getElementsByTagName('a');

            for (i = 0; i < aTags.length; i++)
                aTags[i].setAttribute('href', links[i]);
        }";
    $rewriteScriptNode = $dom->createTextNode($rewriteScript);
    $rewriteScriptTag = $dom->createElement('script');
    $rewriteScriptTag->appendChild($rewriteScriptNode);
    $head->appendChild($rewriteScriptTag);

    //Call rewriteLinks() onLoad
    $body->setAttributes('onLoad', 'rewriteLinks()');

    //Save the modified webpage
    $dom->saveHTMLFile('latestpage.html');


    /*//Debug code.
    print_r($hrefs);*/
?>