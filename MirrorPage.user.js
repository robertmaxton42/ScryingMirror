// ==UserScript==
// @name        Mirror Page
// @namespace   mailto:linkhyrule5@gmail.com
// @description POSTs page to dynamic page mirror
// @include     http://*.syosetu.com/*
// @version     1
// @grant       GM_xmlhttpRequest
// ==/UserScript==

var ihtml = document.getElementsByTagName("html")[0].innerHTML;

String.prototype.hashCode = function() {
    var hash = 0;
    if (this.length == 0) {
        return hash;
    }
    for (var i = 0; i < this.length; i++) {
        char = this.charCodeAt(i);
        hash = ((hash<<5)-hash)+char;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
}

var hash = ihtml.hashCode();

GM_xmlhttpRequest({
    method: 'POST',
    url: 'http://dynamicmirror.herokuapp.com/index.php',
    //url: 'http://localhost:21802/index.php',
    data: "pageContents=" + encodeURIComponent(ihtml) + "&url=" + encodeURIComponent(document.URL) + "&hash=" + encodeURIComponent(hash),
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    onerror: function (response) { alert("Error in POST"); },
    onload: function (response) {
        //window.alert(response.responseText); //generic debug code.
        //window.location.href = 'http://localhost:21802/latestpage.html';
        window.location.href = 'http://dynamicmirror.herokuapp.com/' + hash + '.html';
    }
});
