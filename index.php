
<?php
    if (!$_POST) return; //Break if there's no POST.

    $dom = new DOMDocument();
    $dom->formatOutput = true;
    
    //Get the webpage by POST from the GreaseMonkey plugin
    //Parse into a DOM, and grab the head and body tags while we're at it.
    $PageContents = urldecode($_POST['pageContents']);
    $dom->loadHTML($PageContents);
    $head = $dom->getElementsByTagName('head')->item(0);
    $body = $dom->getElementsByTagName('body')->item(0);

    //Rebase relative links.
    $baseTag = $dom->createElement('base');
    $baseTag->setAttribute('href', urldecode($_POST['url']));
    $baseTag->setAttribute('title', "Added by Dynamic Reader"); //Debug code
    $head->appendChild($baseTag);

    /*Build table of all links on the page.
     *Note that it's not necessary to reparse relative links, since the <base> tag
     *inserted above gets carried over into the Google Translate iframe.*/
    $links = $dom->getElementsByTagName('a');
    $hrefs = array();

    //Populate hrefs
    //Use random number to eliminate conflicts.
    $llength = $links->length;
    $rnd = rand();
    $idh = 'a' . $rnd;

    //Name all unnamed links.
    for ($i = 0; $i < $llength; $i++) {
        $link = $links->item($i);
        $currid = $link->attributes->getNamedItem('id')->nodeValue;
        if (empty($currid)) {
            $id = $idh . $i;
            $idAttr = $dom->createAttribute('id');
            $idAttr->value = $id;
            $link->appendChild($idAttr);
        }
        else
            $id = $currid;
        $hrefs[$id] = $link->attributes->getNamedItem('href')->nodeValue;
    }

    //Convert to JSON
    $jsLinks = json_encode($hrefs);
    $hrefsJSON = "var links = " . $jsLinks . ";\n";
    $hrefsScriptNode = $dom->createTextNode($hrefsJSON);
    $hrefsScriptTag = $dom->createElement('script');
    
    $hrefsScriptTag->appendChild($hrefsScriptNode);
    $head->appendChild($hrefsScriptTag);

    //Inject javascript: rewrite links in order to their proper destinations.
    $rewriteScript = "
    function rewriteLinks() {
        var aTags = document.getElementsByTagName('a');
        var aMap = new Map();

        /*Build map of anchor id to tag.
         *Note that this will end up mapping all links without an id to empty string
         *This is fine, though, because those must have been added between server load
         *and client load - aka, Google Translate*/
        for (var aTag of aTags)
            aMap.set(aTag.getAttribute('id'), aTag);

        //Then iterate through all the links we already knew about
        for (var key in links)
            aMap.get(key).setAttribute('href', links[key]);
    }

    //Append rewriteLinks() to onLoad
    //addLoadEvent() from htmlgoodies.com, credited to Simon Willison.

    //function addLoadEvent(func) {
      //var oldonload = window.onload;
      //if (typeof window.onload != 'function') {
        //window.onload = func;
      //} else {
        //window.onload = function() {
          //if (oldonload) {
            //oldonload();
          //}
          //func();
        //}
      //}
    //}";

    $rewriteScriptNode = $dom->createTextNode($rewriteScript);
    $rewriteScriptTag = $dom->createElement('script');
    $rewriteScriptTag->appendChild($rewriteScriptNode);
    $head->appendChild($rewriteScriptTag);

    //addLoadEvent has to occur /after/ the body tag has been loaded, however...
    $addLoadEventTag = $dom->createElement('script');
    $addLoadEventTag->appendChild($dom->createTextNode('addLoadEvent(rewriteLinks);'));
    
    //shoddy workaround because prependChild doesn't exist...
    //addLoadEvent should now be the first thing read.
    $first = $body->childNodes->item(0);
    $body->insertBefore($addLoadEventTag, $first);

    /*Unfortunately, we can't actually always depend on the page to load!
     *So, also add script to end of body.
     *Hopefully doing both will also future-proof from Google Translate changing their
     *own link rewrite script*/
    $forceRewriteTag = $dom->createElement('script');
    $forceRewriteTag->appendChild($dom->createTextNode('rewriteLinks();'));
    $body->appendChild($forceRewriteTag);

    //Save the modified webpage
    $dom->saveHTMLFile(urldecode($_POST['hash']) + '.html');
    print_r($_POST['hash']);
    //$dom->saveHTMLFile('latestpage.html');

    /*//Debug code.
    print_r($hrefs);*/
?>