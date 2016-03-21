// ==UserScript==
// @name        Mirror Page
// @namespace   mailto:linkhyrule5@gmail.com
// @description POSTs page to dynamic page mirror
// @include     http://*.syosetu.com/*
// @version     1
// @grant       GM_xmlhttpRequest
// ==/UserScript==

var ihtml = document.getElementsByTagName("html")[0].innerHTML;
GM_xmlhttpRequest({
    method: 'POST',
    url: 'http://dynamicmirror.heroku.com/index.php',
    data: "PageContents=" + encodeURIComponent(ihtml) + "&URL=" + encodeURIComponent(document.URL),
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
});

//window.setTimeout(function(){window.location.href = http://localhost:26553/latest;}