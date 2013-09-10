DMS
===

PageLines DMS

Adds html5.js to allow IE8 to recognize html5 and thus fix major layout problems. 
Adds respond.js to enable media queries in IE8.
Adds option under Global>Advanced to enable IE8 compatibility.

<h2>KNOWN ISSUES</h2>
The .sortable-first class goes away in IE8 and this CSS: 
<pre>
.row > [class*="span"]:first-child, .row > section:first-of-type, .row .sortable-first, .row-fluid > [class*="span"]:first-child, .row-fluid > section:first-of-type, .row-fluid .sortable-first, .editor-row > [class*="span"]:first-child, .editor-row > section:first-of-type, .editor-row .sortable-first {
margin-left: 0;
clear: both;
}
</pre>
does not produce the desired effect of removing left margins, as .sortable-first is removed in IE8. <strong>This is a PageLines DMS issue</strong>. 

<h2>WORKAROUNDS</h2>
Attached screenshots show a blog page with Magazine layout without the option (before) and with the option enabled and indicated CSS added in child theme(after). This code is available for copy/paste in the "help" area of the new option. 
This CSS is to be used ONLY if you don't use sections on LESS THEN 12 spans with OFFSET TO LEFT. 
<pre>
/*IE8 first section fix*/
.ie8 .section-plcolumn:first-child {
margin-left: 0 !important;
clear: both;
}
.ie8 section:first-child {
margin-left: 0 !important;
clear: both;
}
</pre>

If you use sections with left offset, add the class "first" to the first column in a group of columns, then the CSS: 

.ie8 .first {
margin-left: 0 !important;
clear: both;
}

<h3>Before</h3>
![before](https://f.cloud.github.com/assets/1617798/1113588/c9d7d66a-1a00-11e3-85ca-f32c5027a2ef.png)

<h3>After</h3>
![after](https://f.cloud.github.com/assets/1617798/1113592/d0a42264-1a00-11e3-9b78-7c7d98738dcf.png)

<h3>The Option</h3>
![the option](https://f.cloud.github.com/assets/1617798/1113614/501b86cc-1a01-11e3-806b-2ea8499a8605.png)

<h4>NOTE</h4>
The child theme also has the border-box model implemented, with the exception of the PL Editor toolbox which breaks with border-box.
<pre>
*, *:before, *:after {
-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;
 }
 .toolbox-handle, .toolbox-handle:before, .toolbox-handle:after {
 -moz-box-sizing: content-box; -webkit-box-sizing: content-box; box-sizing: content-box;
 }
</pre>
