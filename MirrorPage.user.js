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
    url: 'http://dynamicmirror.herokuapp.com/index.php',
    data: "PageContents=" + encodeURIComponent(ihtml) + "&URL=" + encodeURIComponent(document.URL),
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    onerror: function (response) { alert("Error in POST"); },
    onload: function (response) { window.location.href = 'http://dynamicmirror.herokuapp.com/latestpage.html';}
});
