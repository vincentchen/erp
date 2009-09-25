



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<!-- ViewVC - http://viewvc.org/
by Greg Stein - mailto:gstein@lyra.org -->
<!--
~ SourceForge.net: Create, Participate, Evaluate
~ Copyright (c) 1999-2009 SourceForge, Inc. All rights reserved.
-->
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="description" content="The world's largest development and download repository of Open Source code and applications" />
<meta name="keywords" content="Open Source, Development, Developers, Projects, Downloads, OSTG, VA Software, SF.net, SourceForge" />
<title>SourceForge.net Repository - [web-erp] Log of /webERP/includes/MiscFunctions.php</title>
<meta name="generator" content="ViewVC 1.0.5" />
<link rel="stylesheet" type="text/css" href="http://static.sourceforge.net/css/phoneix/style.php?secure=0&amp;20080417-1657" media="all" />
<link rel="stylesheet" type="text/css" href="http://static.sourceforge.net/css/phoneix/header.php?secure=0&amp;20080417-1657" media="all" />
<link rel="stylesheet" type="text/css" href="http://static.sourceforge.net/css/phoneix/table.php?secure=0&amp;20080417-1657" media="all" />
<link rel="stylesheet" type="text/css" href="/viewvc-static/styles.css" media="all" />
<script type="text/javascript" src="https://static.sourceforge.net/include/jquery/jquery-1.2.6.min.js"></script>
<script type="text/javascript">
jQuery.noConflict();
</script>
<script type="text/javascript" src="https://static.sourceforge.net/include/jquery/jquery.cookie.js"></script>
<script type="text/javascript">
var jsonly = document.createElement('link');
jsonly.setAttribute('rel', 'stylesheet');
jsonly.setAttribute('type', 'text/css');
jsonly.setAttribute('href', 'https://static.sourceforge.net/css/phoneix/jsonly.css?secure=1&amp;20080417-1257');
document.getElementsByTagName('head')[0].appendChild(jsonly);
</script>
<script type="text/javascript" src="https://static.sourceforge.net/include/jquery/jquery.cluetip.js"></script>
<script type="text/javascript" src="https://static.sourceforge.net/include/jquery/jquery.dimensions-1.2.0.js"></script>
<link rel="stylesheet" href="https://static.sourceforge.net/css/phoneix/jquery.cluetip.php?secure=1" media="screen"/>
<script type="text/javascript">
//<![CDATA[
var ie6SelectsShowing = true;
function toggleIE6Selects(){
if(jQuery.browser.msie && jQuery.browser.version == '6.0'){
if(ie6SelectsShowing){
jQuery('select').hide();
ie6SelectsShowing = false;
} else {
jQuery('select').show();
ie6SelectsShowing = true;
}
}
}
jQuery(function(){
jQuery('.pop').cluetip({sticky: true, titleAttribute: 'title', local:true, cursor: 'pointer', dropShadow: true, activation: 'click' });
jQuery('.jt').cluetip({cluetipClass: 'jtip', positionBy: false, arrows: false, dropShadow: true, local:true, mouseOutClose: true});
jQuery('.jt_sticky').cluetip({cluetipClass: 'jtip', positionBy: false, arrows: false, dropShadow: true, local:true, closePosition: 'title', sticky:true});
jQuery('.ph_sticky').click(toggleIE6Selects).cluetip({cluetipClass: 'default', arrows: false, cursor: 'pointer', dropShadow: true, local:true, closePosition: 'title', sticky:true, activation: 'click', onShow: function(){jQuery('#cluetip-close a').click(toggleIE6Selects);}});
jQuery('.ajax_sticky').cluetip({cluetipClass: 'default', width: '400', cursor: 'pointer', positionBy: 'mouse', arrows: false, dropShadow: true, closePosition: 'title', sticky:true, ajaxSettings: {success: function(msg){alert( "Data Saved: " + msg );}}});
});
//]]>
</script>
<!--[if IE]>
<link rel="stylesheet" type="text/css" media="screen" href="https://static.sourceforge.net/css/phoneix/iestyles.php?secure=1&amp;20080417-1257" />
<![endif]-->
<!--[if lte IE 6]>
<link rel="stylesheet" type="text/css" media="screen" href="https://static.sourceforge.net/css/phoneix/ie6styles.php?secure=1&amp;20080417-1257" />
<![endif]-->
<script type="text/javascript" src="https://static.sourceforge.net/sfx.js"></script>
<style type="text/css">
#breadcrumb {margin: 0; padding: 0; background: none; }
#breadcrumb li {list-style: none; padding: 0 .2em 0 0; display: inline; }
#breadcrumb li:before {color: #aaa; content: "/"}
#breadcrumb li:first-child:before {content: ""}
h2 {margin: 0 0 -.5em;}
table th.vc_header {background-color: #fff;}
table th.vc_header_sort a {text-decoration: none; color: #000 !important;}
hr {background: #ccc; border: none;}
/* grr ie */
#bd .titlebar, table tr th {_background: url("http://c.fsdn.com/sf/images/phoneix/gloss_ieblows.png");}
table tbody tr td, .yui-gf.title .yui-u.first h2 {_background-image: none;}
#bd { _background: #fff url("http://c.fsdn.com/sf/images/phoneix/grad_ieblows.png") repeat-x 0 6px;}
#hd .yui-u.first h1 { _background: transparent; _filter:progid:DXImageTransform.Microsoft.AlphaImageLoader (src='http://c.fsdn.com/sf/images/phoneix/sf_phoneix.png',sizingMethod='');}
#hd {_background: none;}
</style>
<!-- BEGIN: AdSolution-Tag 4.2: Global-Code [PLACE IN HTML-HEAD-AREA!] -->
<!-- DoubleClick Random Number -->
<script language="JavaScript" type="text/javascript">
dfp_ord=Math.random()*10000000000000000;
dfp_tile = 1;
</script>
<!-- End DoubleClick Random Number -->
<script type="text/javascript">
var google_page_url = 'http://sourceforge.net/projects/web-erp/';
var sourceforge_project_name = 'web-erp';
var sourceforge_project_description = '';
</script>
<!-- END: AdSolution-Tag 4.2: Global-Code -->
<!-- End OSDN NavBar gid: -->
<!-- after META tags -->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
</head>
<body>
<div id="doc3" class="yui-t6 login">
<div id="hd">
<div id="logo_container">
<h1>
<a title="" href="http://sourceforge.net/">SourceForge.net</a>
</h1>
<ul class="jump">
<li>
</li>
</ul>
</div>
<div id="top_bar">
<div id="login_container">
<ul>
<li>
<a href="http://sourceforge.net/community/">Community</a>
</li>
<li>
<a href="http://p.sf.net/sourceforge/about">About</a>
</li>
<li>
<a title="Get help and support on SourceForge.net" href="http://p.sf.net/sourceforge/getsupport">Help</a>
</li>
</ul>
</div>
<div id="search_container">
<form id="searchform" name="searchform" method="get" action="http://sourceforge.net/search/">
<input type="hidden" value="soft" name="type_of_search"/>
<span class="left">
<input class="text" type="text" tabindex="1" name="words"/>
</span>
<span class="right">
<button value="Search" tabindex="0" type="submit"></button>
</span>
</form>
</div>
</div>
</div>
<div id="bd">
<!-- begin content -->
<a name="content">
</a>
<!-- Breadcrumb Trail -->
<br />

<ul id="breadcrumb">
<li class="begin"> <a href="http://sourceforge.net/">SF.net</a></li>
<li> <a href="http://sourceforge.net/softwaremap/">Projects</a></li>
<li> SCM Repositories</li>

<li>
<a href="/viewvc/web-erp/">

web-erp</a>
</li>

<li>
<a href="/viewvc/web-erp/webERP/">

webERP</a>
</li>

<li>
<a href="/viewvc/web-erp/webERP/includes/">

includes</a>
</li>

<li>


MiscFunctions.php
</li>

</ul>

<div id="yui-main">
<div class="yui-b">
<h1>
SCM Repositories -
<a href="http://sourceforge.net/projects/web-erp">web-erp</a>
</h1>

<p style="margin:0;">

<a href="/viewvc/web-erp/webERP/includes/"><img src="/viewvc-static/images/back_small.png" width="16" height="16" alt="Parent Directory" /> Parent Directory</a>




</p>

<hr />
<table class="auto track" style="width: 30em;">



<tr>
<td>Links to HEAD:</td>
<td>
(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=markup">view</a>)
(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=annotate">annotate</a>)
</td>
</tr>



<tr>
<td>Sticky Tag:</td>
<td><form method="get" action="/viewvc/web-erp/webERP/includes/MiscFunctions.php" style="display: inline">
<div style="display: inline">
<input type="hidden" name="view" value="log" />


<select name="pathrev" onchange="submit()">
<option value=""></option>

<optgroup label="Branches">


<option>rel2-9b</option>



<option>TAX_CHANGES</option>



<option>MAIN</option>


</optgroup>

<optgroup label="Non-branch tags">


<option>v3_10</option>



<option>v3-09</option>



<option>v3-08</option>



<option>v3-071</option>



<option>v3-06</option>



<option>v3-05</option>



<option>v3-04</option>



<option>v3-02</option>



<option>release_3-00</option>



<option>HEAD</option>


</optgroup>

</select>

<input type="submit" value="Set" />
</div>
</form>

</td>
</tr>
</table>
</div>
</div>
<div class="yui-b">
<div id="fad83">
<!-- DoubleClick Ad Tag -->
<script type="text/javascript">
//<![CDATA[
document.write('<script src="http://ad.doubleclick.net/adj/ostg.sourceforge/pg_viewvc_p88_shortrec;pg=viewvc;tile='+dfp_tile+';tpc=web-erp;ord='+dfp_ord+'?" type="text/javascript"><\/script>');
dfp_tile++;
//]]>
</script>
<!-- End DoubleClick Ad Tag -->
</div>
</div>
<br style="clear:both; margin-bottom: 1em; display: block;" />
 








<div>
<hr />

<a name="rev1.31"></a>
<a name="HEAD"></a>


Revision <strong>1.31</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.31&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.31">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.31">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.31&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Sep 15 17:45:46 2009 UTC</em> (9 days, 1 hour ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=HEAD"><strong>HEAD</strong></a>






<br />Changes since <strong>1.30: +16 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.30&amp;r2=1.31">previous 1.30</a>










<pre class="vc_log">Add functions to retrieve decimal places for stock items and cuurency codes
</pre>
</div>



<div>
<hr />

<a name="rev1.30"></a>


Revision <strong>1.30</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.30&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.30">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.30">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.30&amp;view=log">[select for diffs]</a>




<br />

<em>Wed Jul 15 01:02:51 2009 UTC</em> (2 months, 1 week ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.29: +11 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.29&amp;r2=1.30">previous 1.29</a>










<pre class="vc_log">typos Javier and wiki link to WorkOrders. Also added wikiLink function to MiscFunctions.php and ditched the includes/Wiki.php script altogether
</pre>
</div>



<div>
<hr />

<a name="rev1.29"></a>
<a name="v3_10"></a>


Revision <strong>1.29</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.29&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.29">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.29">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.29&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Sep  9 07:52:45 2008 UTC</em> (12 months, 2 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=v3_10"><strong>v3_10</strong></a>






<br />Changes since <strong>1.28: +1 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.28&amp;r2=1.29">previous 1.28</a>










<pre class="vc_log">Correct bug for when default currency is not in the ECB list
</pre>
</div>



<div>
<hr />

<a name="rev1.28"></a>


Revision <strong>1.28</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.28&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.28">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.28">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.28&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Aug 11 01:37:24 2008 UTC</em> (13 months, 2 weeks ago) by <em>emdeex</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.27: +2 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.27&amp;r2=1.28">previous 1.27</a>










<pre class="vc_log">Patch submitted by Harald Ringehahn.  When ECB does not deliver any rate for Euro.
</pre>
</div>



<div>
<hr />

<a name="rev1.27"></a>


Revision <strong>1.27</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.27&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.27">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.27">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.27&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Jul  7 14:59:06 2008 UTC</em> (14 months, 2 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.26: +12 -4 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.26&amp;r2=1.27">previous 1.26</a>










<pre class="vc_log">Correct divide by zero errors in currency function.
</pre>
</div>



<div>
<hr />

<a name="rev1.26"></a>
<a name="v3-09"></a>


Revision <strong>1.26</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.26&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.26">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.26">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.26&amp;view=log">[select for diffs]</a>




<br />

<em>Wed Apr  2 08:55:17 2008 UTC</em> (17 months, 3 weeks ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=v3-09"><strong>v3-09</strong></a>






<br />Changes since <strong>1.25: +1 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.25&amp;r2=1.26">previous 1.25</a>










<pre class="vc_log">fall back solution for currencies not quoted by ECB
</pre>
</div>



<div>
<hr />

<a name="rev1.25"></a>


Revision <strong>1.25</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.25&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.25">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.25">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.25&amp;view=log">[select for diffs]</a>




<br />

<em>Wed Apr  2 08:20:35 2008 UTC</em> (17 months, 3 weeks ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.24: +15 -4 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.24&amp;r2=1.25">previous 1.24</a>










<pre class="vc_log">fall back solution for currencies not quoted by ECB
</pre>
</div>



<div>
<hr />

<a name="rev1.24"></a>


Revision <strong>1.24</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.24&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.24">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.24">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.24&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Mar 20 23:25:45 2008 UTC</em> (18 months ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.23: +6 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.23&amp;r2=1.24">previous 1.23</a>










<pre class="vc_log">Add function to replace slashed carriage returns
</pre>
</div>



<div>
<hr />

<a name="rev1.23"></a>


Revision <strong>1.23</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.23&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.23">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.23">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.23&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Mar 15 22:51:32 2008 UTC</em> (18 months, 1 week ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.22: +2 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.22&amp;r2=1.23">previous 1.22</a>










<pre class="vc_log">Change log and fix to currency lookup
</pre>
</div>



<div>
<hr />

<a name="rev1.22"></a>


Revision <strong>1.22</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.22&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.22">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.22">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.22&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Mar  1 02:24:30 2008 UTC</em> (18 months, 3 weeks ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.21: +46 -38 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.21&amp;r2=1.22">previous 1.21</a>










<pre class="vc_log">automatic updates to exchange rates from ECB
</pre>
</div>



<div>
<hr />

<a name="rev1.21"></a>


Revision <strong>1.21</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.21&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.21">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.21">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.21&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Feb 28 09:30:19 2008 UTC</em> (18 months, 3 weeks ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.20: +14 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.20&amp;r2=1.21">previous 1.20</a>










<pre class="vc_log">auto-lookup of currency rates from ECB
</pre>
</div>



<div>
<hr />

<a name="rev1.20"></a>


Revision <strong>1.20</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.20&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.20">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.20">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.20&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Feb 28 08:39:08 2008 UTC</em> (18 months, 3 weeks ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.19: +40 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.19&amp;r2=1.20">previous 1.19</a>










<pre class="vc_log">Supplier searches
</pre>
</div>



<div>
<hr />

<a name="rev1.19"></a>


Revision <strong>1.19</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.19&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.19">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.19">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.19&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Feb  4 22:09:31 2008 UTC</em> (19 months, 2 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.18: +5 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.18&amp;r2=1.19">previous 1.18</a>










<pre class="vc_log">Format prnMsg strings
</pre>
</div>



<div>
<hr />

<a name="rev1.18"></a>


Revision <strong>1.18</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.18&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.18">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.18">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.18&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Nov 13 07:59:54 2007 UTC</em> (22 months, 1 week ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.17: +9 -34 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.17&amp;r2=1.18">previous 1.17</a>










<pre class="vc_log">modified audit trail work
</pre>
</div>



<div>
<hr />

<a name="rev1.17"></a>


Revision <strong>1.17</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.17&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.17">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.17">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.17&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Nov 12 21:42:15 2007 UTC</em> (22 months, 1 week ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.16: +25 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.16&amp;r2=1.17">previous 1.16</a>










<pre class="vc_log">Insert all database changes to an audit trail, and format them in an output script
</pre>
</div>



<div>
<hr />

<a name="rev1.16"></a>
<a name="v3-08"></a>
<a name="v3-071"></a>
<a name="v3-06"></a>


Revision <strong>1.16</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.16&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.16">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.16">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.16&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Jul 14 22:29:53 2007 UTC</em> (2 years, 2 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=v3-06"><strong>v3-06</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=v3-071"><strong>v3-071</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=v3-08"><strong>v3-08</strong></a>






<br />Changes since <strong>1.15: +19 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.15&amp;r2=1.16">previous 1.15</a>










<pre class="vc_log">various
</pre>
</div>



<div>
<hr />

<a name="rev1.15"></a>
<a name="v3-05"></a>


Revision <strong>1.15</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.15&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.15">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.15">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.15&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Nov 18 02:48:10 2006 UTC</em> (2 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=v3-05"><strong>v3-05</strong></a>






<br />Changes since <strong>1.14: +0 -131 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.14&amp;r2=1.15">previous 1.14</a>










<pre class="vc_log">*** empty log message ***
</pre>
</div>



<div>
<hr />

<a name="rev1.14"></a>


Revision <strong>1.14</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.14&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.14">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.14">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.14&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Aug  7 09:32:27 2006 UTC</em> (3 years, 1 month ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.13: +1 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.13&amp;r2=1.14">previous 1.13</a>










<pre class="vc_log">vtiger customer branches
</pre>
</div>



<div>
<hr />

<a name="rev1.13"></a>


Revision <strong>1.13</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.13&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.13">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.13">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.13&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Jul  6 10:34:17 2006 UTC</em> (3 years, 2 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.12: +132 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.12&amp;r2=1.13">previous 1.12</a>










<pre class="vc_log">Check printing code of Steves with some modifications - it does not work yet
</pre>
</div>



<div>
<hr />

<a name="rev1.12"></a>


Revision <strong>1.12</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.12&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.12">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.12">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.12&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Jun 27 16:57:54 2006 UTC</em> (3 years, 2 months ago) by <em>jessep</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.11: +6 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.11&amp;r2=1.12">previous 1.11</a>










<pre class="vc_log">see 27/6/06 ChangeLog Entries for full details
</pre>
</div>



<div>
<hr />

<a name="rev1.10.4.1"></a>

<a name="TAX_CHANGES"></a>

Revision <strong>1.10.4.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.10.4.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.10.4.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.10.4.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.10.4.1&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Mar 28 03:19:22 2005 UTC</em> (4 years, 5 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>







<br />Changes since <strong>1.10: +18 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.10&amp;r2=1.10.4.1">previous 1.10</a>






, to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.31&amp;r2=1.10.4.1">next main 1.31</a>







<pre class="vc_log">merged main trunk back into the tax changes
</pre>
</div>



<div>
<hr />

<a name="rev1.11"></a>
<a name="v3-02"></a>
<a name="v3-04"></a>
<a name="release_3-00"></a>


Revision <strong>1.11</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.11&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.11">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.11">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.11&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Mar 25 05:15:40 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=release_3-00"><strong>release_3-00</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=v3-02"><strong>v3-02</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=v3-04"><strong>v3-04</strong></a>






<br />Changes since <strong>1.10: +18 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.10&amp;r2=1.11">previous 1.10</a>










<pre class="vc_log">version 3
</pre>
</div>



<div>
<hr />

<a name="rev1.10"></a>


Revision <strong>1.10</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.10&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.10">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.10">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.10&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Dec  5 07:24:56 2004 UTC</em> (4 years, 9 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>




<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=rel2-9b"><strong>rel2-9b</strong></a>





<br />Changes since <strong>1.9: +5 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.9&amp;r2=1.10">previous 1.9</a>










<pre class="vc_log">messages tidy kitch - take 12
</pre>
</div>



<div>
<hr />

<a name="rev1.9"></a>


Revision <strong>1.9</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.9&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.9">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.9">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.9&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Nov 13 04:18:52 2004 UTC</em> (4 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.8: +1 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.8&amp;r2=1.9">previous 1.8</a>










<pre class="vc_log">gettexification
</pre>
</div>



<div>
<hr />

<a name="rev1.8"></a>


Revision <strong>1.8</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.8&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.8">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.8">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.8&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Nov  2 10:02:08 2004 UTC</em> (4 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.7: +1 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.7&amp;r2=1.8">previous 1.7</a>










<pre class="vc_log">gettextification
</pre>
</div>



<div>
<hr />

<a name="rev1.7"></a>


Revision <strong>1.7</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.7&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.7">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.7">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.7&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Sep 26 09:28:43 2004 UTC</em> (4 years, 11 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.6: +1 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.6&amp;r2=1.7">previous 1.6</a>










<pre class="vc_log">Stock Transfer Serial
</pre>
</div>



<div>
<hr />

<a name="rev1.6"></a>


Revision <strong>1.6</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.6&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.6">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.6">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.6&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Sep 19 04:35:26 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.5: +11 -11 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.5&amp;r2=1.6">previous 1.5</a>










<pre class="vc_log">Serial Stuff
</pre>
</div>



<div>
<hr />

<a name="rev1.5"></a>


Revision <strong>1.5</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.5&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.5">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.5">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.5&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Sep 12 03:38:43 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.4: +5 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.4&amp;r2=1.5">previous 1.4</a>










<pre class="vc_log">GLPosting bug fix
</pre>
</div>



<div>
<hr />

<a name="rev1.4"></a>


Revision <strong>1.4</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.4&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.4">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.4">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.4&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Jul 11 09:32:56 2004 UTC</em> (5 years, 2 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.3: +5 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.3&amp;r2=1.4">previous 1.3</a>










<pre class="vc_log">Serial stuff
</pre>
</div>



<div>
<hr />

<a name="rev1.3"></a>


Revision <strong>1.3</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.3&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.3">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.3">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.3&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Jul  5 10:28:54 2004 UTC</em> (5 years, 2 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.2: +1 -53 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.2&amp;r2=1.3">previous 1.2</a>










<pre class="vc_log">More serial stuff
</pre>
</div>



<div>
<hr />

<a name="rev1.2"></a>


Revision <strong>1.2</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.2&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.2">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.2">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.2&amp;view=log">[select for diffs]</a>




<br />

<em>Wed May 19 10:42:59 2004 UTC</em> (5 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.1: +1 -13 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.1&amp;r2=1.2">previous 1.1</a>










<pre class="vc_log">Order items cart class fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.1"></a>


Revision <strong>1.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?revision=1.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/MiscFunctions.php?revision=1.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?annotate=1.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?r1=1.1&amp;view=log">[select for diffs]</a>




<br />

<em>Wed May  5 10:12:44 2004 UTC</em> (5 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/MiscFunctions.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>
















<pre class="vc_log">Jesse Serialised stuff
</pre>
</div>

 



 <hr />
<p><a name="diff"></a>
This form allows you to request diffs between any two revisions of this file.
For each of the two "sides" of the diff,

select a symbolic revision name using the selection box, or choose
'Use Text Field' and enter a numeric revision.

</p>
<form method="get" action="/viewvc/web-erp/webERP/includes/MiscFunctions.php" id="diff_select">
<table cellpadding="2" cellspacing="0" class="auto">
<tr>
<td>&nbsp;</td>
<td>
<input type="hidden" name="view" value="diff" />
Diffs between

<select name="r1">
<option value="text" selected="selected">Use Text Field</option>

<option value="1.29:v3_10">v3_10</option>

<option value="1.26:v3-09">v3-09</option>

<option value="1.16:v3-08">v3-08</option>

<option value="1.16:v3-071">v3-071</option>

<option value="1.16:v3-06">v3-06</option>

<option value="1.15:v3-05">v3-05</option>

<option value="1.11:v3-04">v3-04</option>

<option value="1.11:v3-02">v3-02</option>

<option value="1.11:release_3-00">release_3-00</option>

<option value="1.10:rel2-9b">rel2-9b</option>

<option value="1.10.4.1:TAX_CHANGES">TAX_CHANGES</option>

<option value="1.31:MAIN">MAIN</option>

<option value="1.31:HEAD">HEAD</option>

</select>
<input type="text" size="12" name="tr1"
value="1.31"
onchange="document.getElementById('diff_select').r1.selectedIndex=0" />

and

<select name="r2">
<option value="text" selected="selected">Use Text Field</option>

<option value="1.29:v3_10">v3_10</option>

<option value="1.26:v3-09">v3-09</option>

<option value="1.16:v3-08">v3-08</option>

<option value="1.16:v3-071">v3-071</option>

<option value="1.16:v3-06">v3-06</option>

<option value="1.15:v3-05">v3-05</option>

<option value="1.11:v3-04">v3-04</option>

<option value="1.11:v3-02">v3-02</option>

<option value="1.11:release_3-00">release_3-00</option>

<option value="1.10:rel2-9b">rel2-9b</option>

<option value="1.10.4.1:TAX_CHANGES">TAX_CHANGES</option>

<option value="1.31:MAIN">MAIN</option>

<option value="1.31:HEAD">HEAD</option>

</select>
<input type="text" size="12" name="tr2"
value="1.1"
onchange="document.getElementById('diff_select').r2.selectedIndex=0" />

</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>
Type of Diff should be a
<select name="diff_format" onchange="submit()">
<option value="h" selected="selected">Colored Diff</option>
<option value="l" >Long Colored Diff</option>
<option value="u" >Unidiff</option>
<option value="c" >Context Diff</option>
<option value="s" >Side by Side</option>
</select>
<input type="submit" value=" Get Diffs " />
</td>
</tr>
</table>
</form>


<form method="get" action="/viewvc/web-erp/webERP/includes/MiscFunctions.php">
<div>
<hr />
<a name="logsort"></a>
<input type="hidden" name="view" value="log" />
Sort log by:
<select name="logsort" onchange="submit()">
<option value="cvs" >Not sorted</option>
<option value="date" selected="selected">Commit date</option>
<option value="rev" >Revision</option>
</select>
<input type="submit" value=" Sort " />
</div>
</form>


<hr />
<div class="yui-g">
<div class="yui-u first">
Powered by <a href="http://viewvc.tigris.org/">ViewVC 1.0.5</a>
</div>
<div class="yui-u" style="text-align: right">
<strong><a href="/viewvc-static/help_log.html">ViewVC Help</a></strong>
</div>
</div>
</div>
<div id="ft">
<div class="yui-g">
<div class="yui-u first">
Copyright &copy; 1999-2009 <a href="http://sourceforge.com" title="Network which provides and promotes Open Source software downloads, development, discussion and news.">SourceForge, Inc.</a> All rights reserved.
<br />
<span id="icons">
<span>follow us on</span><a class="twitter" href="http://twitter.com/sourceforge">Twitter</a>
<a class="feed" href="/export/rss2_projnews.php?group_id=141424&amp;rss_fulltext=1">RSS</a>
<a class="linkedin" href="http://www.linkedin.com/companies/sourceforge-inc.">LinkedIn</a>
</span>
</div>
<div class="yui-u">
<div class="yui-gb">
<div class="yui-u first" style="width: 23%;">
<a href="http://sourceforge.net/sitestatus">Site&nbsp;Status</a><br />
<a href="http://sourceforge.net/support/getsupport.php">Support</a><br />
<a href="http://p.sf.net/sourceforge/terms">Legal</a><br />
</div>
<div class="yui-u" style="width: 38%;">
<a href="http://p.sf.net/sourceforge/rssfeeds">Syndication&nbsp;Feeds&nbsp;/&nbsp;RSS</a><br />
<a href="http://sourceforge.net/community/">Community</a><br />
<a href="http://sourceforge.net/services/buy/index.php">Marketplace</a><br />
</div>
<div class="yui-u" style="width: 33%;">
<a href="http://p.sf.net/sourceforge/about">About&nbsp;SourceForge.net</a><br />
<a href="http://sourceforge.com">About&nbsp;SourceForge,&nbsp;Inc.</a><br />
<a href="http://web.sourceforge.com/media_kit.php">Advertising</a><br />
</div>
</div>
</div>
</div>
</div>
</div>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-32013-37");
pageTracker._setDomainName(".sourceforge.net");
pageTracker._trackPageview();
</script>
</body>
</html>


