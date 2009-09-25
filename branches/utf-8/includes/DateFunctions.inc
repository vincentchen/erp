



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
<title>SourceForge.net Repository - [web-erp] Log of /webERP/includes/DateFunctions.inc</title>
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


DateFunctions.inc
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
(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=markup">view</a>)
(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=annotate">annotate</a>)
</td>
</tr>



<tr>
<td>Sticky Tag:</td>
<td><form method="get" action="/viewvc/web-erp/webERP/includes/DateFunctions.inc" style="display: inline">
<div style="display: inline">
<input type="hidden" name="view" value="log" />


<select name="pathrev" onchange="submit()">
<option value=""></option>

<optgroup label="Branches">


<option>rel2-9b</option>



<option>logicworks</option>



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



<option>start</option>



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

<a name="rev1.36"></a>
<a name="HEAD"></a>


Revision <strong>1.36</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.36&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.36">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.36">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.36&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Sep 15 17:42:08 2009 UTC</em> (9 days, 1 hour ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=HEAD"><strong>HEAD</strong></a>






<br />Changes since <strong>1.35: +17 -16 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.35&amp;r2=1.36">previous 1.35</a>










<pre class="vc_log">Various bug fixes and change to GetPeriod() function to allow periods to be created before the Prohibit date
</pre>
</div>



<div>
<hr />

<a name="rev1.35"></a>


Revision <strong>1.35</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.35&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.35">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.35">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.35&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Aug 13 10:35:47 2009 UTC</em> (6 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.34: +92 -79 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.34&amp;r2=1.35">previous 1.34</a>










<pre class="vc_log">Update for all supported date formats
</pre>
</div>



<div>
<hr />

<a name="rev1.34"></a>


Revision <strong>1.34</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.34&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.34">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.34">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.34&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Aug 10 08:45:10 2009 UTC</em> (6 weeks, 3 days ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.33: +8 -9 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.33&amp;r2=1.34">previous 1.33</a>










<pre class="vc_log">Fixed error in GetPeriod() where the period was being incorrectly found
</pre>
</div>



<div>
<hr />

<a name="rev1.33"></a>


Revision <strong>1.33</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.33&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.33">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.33">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.33&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Jul 24 12:05:04 2009 UTC</em> (2 months ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.32: +73 -20 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.32&amp;r2=1.33">previous 1.32</a>










<pre class="vc_log">Update for German date format
</pre>
</div>



<div>
<hr />

<a name="rev1.32"></a>


Revision <strong>1.32</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.32&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.32">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.32">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.32&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Feb 20 08:02:29 2009 UTC</em> (7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.31: +769 -761 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.31&amp;r2=1.32">previous 1.31</a>










<pre class="vc_log">Fixed creation of thousands of periods and chartdetails. GetPeriod function called before GLPostings was included. Now if no periods exist GetPeriod inserts the first 2
</pre>
</div>



<div>
<hr />

<a name="rev1.31"></a>
<a name="v3_10"></a>


Revision <strong>1.31</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.31&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.31">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.31">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.31&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Sep 29 11:15:41 2008 UTC</em> (11 months, 3 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=v3_10"><strong>v3_10</strong></a>






<br />Changes since <strong>1.30: +10 -7 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.30&amp;r2=1.31">previous 1.30</a>










<pre class="vc_log">Bug fix in GetPeriod()
</pre>
</div>



<div>
<hr />

<a name="rev1.30"></a>


Revision <strong>1.30</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.30&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.30">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.30">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.30&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Sep 29 08:45:39 2008 UTC</em> (11 months, 3 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.29: +120 -157 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.29&amp;r2=1.30">previous 1.29</a>










<pre class="vc_log">Re-write of GetPeriod() function to eliminate chartdetails errors
</pre>
</div>



<div>
<hr />

<a name="rev1.29"></a>


Revision <strong>1.29</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.29&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.29">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.29">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.29&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Sep 22 08:09:46 2008 UTC</em> (12 months ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.28: +6 -6 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.28&amp;r2=1.29">previous 1.28</a>










<pre class="vc_log">Remove check on prohibit journal before
</pre>
</div>



<div>
<hr />

<a name="rev1.28"></a>
<a name="v3-09"></a>


Revision <strong>1.28</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.28&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.28">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.28">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.28&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Feb 16 02:21:28 2008 UTC</em> (19 months, 1 week ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=v3-09"><strong>v3-09</strong></a>






<br />Changes since <strong>1.27: +4 -4 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.27&amp;r2=1.28">previous 1.27</a>










<pre class="vc_log">Multi currency bank accounts
</pre>
</div>



<div>
<hr />

<a name="rev1.27"></a>


Revision <strong>1.27</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.27&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.27">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.27">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.27&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Feb  9 19:19:54 2008 UTC</em> (19 months, 2 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.26: +5 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.26&amp;r2=1.27">previous 1.26</a>










<pre class="vc_log">Change to isdate for null dates sent
</pre>
</div>



<div>
<hr />

<a name="rev1.26"></a>


Revision <strong>1.26</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.26&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.26">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.26">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.26&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Jan 12 10:05:11 2008 UTC</em> (20 months, 1 week ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.25: +3 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.25&amp;r2=1.26">previous 1.25</a>










<pre class="vc_log">Bug fix in DateDiff function
</pre>
</div>



<div>
<hr />

<a name="rev1.25"></a>


Revision <strong>1.25</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.25&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.25">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.25">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.25&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Nov 13 07:59:54 2007 UTC</em> (22 months, 1 week ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.24: +2 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.24&amp;r2=1.25">previous 1.24</a>










<pre class="vc_log">modified audit trail work
</pre>
</div>



<div>
<hr />

<a name="rev1.24"></a>
<a name="v3-08"></a>


Revision <strong>1.24</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.24&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.24">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.24">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.24&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Nov  2 18:59:02 2007 UTC</em> (22 months, 3 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=v3-08"><strong>v3-08</strong></a>






<br />Changes since <strong>1.23: +19 -6 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.23&amp;r2=1.24">previous 1.23</a>










<pre class="vc_log">Update to allow entry of GL budgets for current financial year and next financial year
</pre>
</div>



<div>
<hr />

<a name="rev1.23"></a>


Revision <strong>1.23</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.23&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.23">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.23">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.23&amp;view=log">[select for diffs]</a>




<br />

<em>Wed Oct 24 08:18:00 2007 UTC</em> (23 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.22: +6 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.22&amp;r2=1.23">previous 1.22</a>










<pre class="vc_log">Set default for SESSION ProhibitPostingsBefore
</pre>
</div>



<div>
<hr />

<a name="rev1.22"></a>


Revision <strong>1.22</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.22&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.22">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.22">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.22&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Sep 20 08:36:26 2007 UTC</em> (2 years ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.21: +774 -690 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.21&amp;r2=1.22">previous 1.21</a>










<pre class="vc_log">Change date functions to allow Y/m/d format, and variuous bug fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.21"></a>
<a name="v3-071"></a>
<a name="v3-06"></a>


Revision <strong>1.21</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.21&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.21">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.21">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.21&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Jun 19 01:38:18 2007 UTC</em> (2 years, 3 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=v3-06"><strong>v3-06</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=v3-071"><strong>v3-071</strong></a>






<br />Changes since <strong>1.20: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.20&amp;r2=1.21">previous 1.20</a>










<pre class="vc_log">Renier fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.20"></a>
<a name="v3-05"></a>


Revision <strong>1.20</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.20&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.20">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.20">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.20&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Sep 11 09:54:40 2006 UTC</em> (3 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=v3-05"><strong>v3-05</strong></a>






<br />Changes since <strong>1.19: +26 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.19&amp;r2=1.20">previous 1.19</a>










<pre class="vc_log">New system parameter ProhibitPostingsBefore stops postings to periods prior to this date.
</pre>
</div>



<div>
<hr />

<a name="rev1.19"></a>


Revision <strong>1.19</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.19&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.19">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.19">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.19&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Jun 27 16:57:54 2006 UTC</em> (3 years, 2 months ago) by <em>jessep</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.18: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.18&amp;r2=1.19">previous 1.18</a>










<pre class="vc_log">see 27/6/06 ChangeLog Entries for full details
</pre>
</div>



<div>
<hr />

<a name="rev1.18"></a>


Revision <strong>1.18</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.18&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.18">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.18">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.18&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Dec 18 09:04:52 2005 UTC</em> (3 years, 9 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.17: +16 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.17&amp;r2=1.18">previous 1.17</a>










<pre class="vc_log">Dave Premo form builder add on to the report builder - DIFOT - Delivery In Full On Time report
</pre>
</div>



<div>
<hr />

<a name="rev1.17"></a>


Revision <strong>1.17</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.17&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.17">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.17">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.17&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Nov 21 07:09:43 2005 UTC</em> (3 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.16: +0 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.16&amp;r2=1.17">previous 1.16</a>










<pre class="vc_log">No change from 3.04
</pre>
</div>



<div>
<hr />

<a name="rev1.16"></a>
<a name="v3-04"></a>


Revision <strong>1.16</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.16&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.16">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.16">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.16&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Nov 15 08:07:01 2005 UTC</em> (3 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=v3-04"><strong>v3-04</strong></a>






<br />Changes since <strong>1.15: +7 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.15&amp;r2=1.16">previous 1.15</a>










<pre class="vc_log">final before 3.04
</pre>
</div>



<div>
<hr />

<a name="rev1.15"></a>


Revision <strong>1.15</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.15&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.15">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.15">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.15&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Nov  6 09:43:23 2005 UTC</em> (3 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.14: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.14&amp;r2=1.15">previous 1.14</a>










<pre class="vc_log">Briain - added LC_NUMERIC in LanguageSetup.php, DateFunctions.inc MonthAndYearFromSQLDate fixed for locale using strftime - fixed CustWhereAlloc - string Type not in gettext function
</pre>
</div>



<div>
<hr />

<a name="rev1.14"></a>


Revision <strong>1.14</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.14&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.14">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.14">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.14&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Oct  2 09:28:25 2005 UTC</em> (3 years, 11 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.13: +19 -9 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.13&amp;r2=1.14">previous 1.13</a>










<pre class="vc_log">shipment stuff
</pre>
</div>



<div>
<hr />

<a name="rev1.13"></a>
<a name="v3-02"></a>


Revision <strong>1.13</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.13&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.13">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.13">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.13&amp;view=log">[select for diffs]</a>




<br />

<em>Sun May  1 10:36:01 2005 UTC</em> (4 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=v3-02"><strong>v3-02</strong></a>






<br />Changes since <strong>1.12: +3 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.12&amp;r2=1.13">previous 1.12</a>










<pre class="vc_log">Merge Tax Changes Plus Braians bug fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.10.2.3"></a>

<a name="TAX_CHANGES"></a>

Revision <strong>1.10.2.3</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.10.2.3&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.10.2.3">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.10.2.3">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10.2.3&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Apr 24 07:19:02 2005 UTC</em> (4 years, 5 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>







<br />Changes since <strong>1.10.2.2: +1 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10.2.2&amp;r2=1.10.2.3">previous 1.10.2.2</a>





, to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10&amp;r2=1.10.2.3">branch point 1.10</a>




, to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.36&amp;r2=1.10.2.3">next main 1.36</a>







<pre class="vc_log">brought up to v3.00
</pre>
</div>



<div>
<hr />

<a name="rev1.10.2.2"></a>


Revision <strong>1.10.2.2</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.10.2.2&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.10.2.2">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.10.2.2">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10.2.2&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Mar 28 03:19:22 2005 UTC</em> (4 years, 5 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>







<br />Changes since <strong>1.10.2.1: +6 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10.2.1&amp;r2=1.10.2.2">previous 1.10.2.1</a>





, to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10&amp;r2=1.10.2.2">branch point 1.10</a>








<pre class="vc_log">merged main trunk back into the tax changes
</pre>
</div>



<div>
<hr />

<a name="rev1.12"></a>
<a name="release_3-00"></a>


Revision <strong>1.12</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.12&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.12">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.12">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.12&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Mar 25 05:15:40 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=release_3-00"><strong>release_3-00</strong></a>






<br />Changes since <strong>1.11: +5 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.11&amp;r2=1.12">previous 1.11</a>










<pre class="vc_log">version 3
</pre>
</div>



<div>
<hr />

<a name="rev1.10.2.1"></a>


Revision <strong>1.10.2.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.10.2.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.10.2.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.10.2.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10.2.1&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Mar  5 09:16:42 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>







<br />Changes since <strong>1.10: +6 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10&amp;r2=1.10.2.1">previous 1.10</a>










<pre class="vc_log">Tax Authorities
</pre>
</div>



<div>
<hr />

<a name="rev1.11"></a>


Revision <strong>1.11</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.11&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.11">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.11">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.11&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Mar  5 06:36:28 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.10: +6 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10&amp;r2=1.11">previous 1.10</a>










<pre class="vc_log">Sales Categories Stocks GetStockImage.php Danies stuff
</pre>
</div>



<div>
<hr />

<a name="rev1.10"></a>


Revision <strong>1.10</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.10&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.10">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.10">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.10&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Feb 12 04:38:10 2005 UTC</em> (4 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>




<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>





<br />Changes since <strong>1.9: +170 -147 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.9&amp;r2=1.10">previous 1.9</a>










<pre class="vc_log">merged with webERP
</pre>
</div>



<div>
<hr />

<a name="rev1.9.2.1"></a>

<a name="rel2-9b"></a>

Revision <strong>1.9.2.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.9.2.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.9.2.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.9.2.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.9.2.1&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Feb 11 08:18:09 2005 UTC</em> (4 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=rel2-9b"><strong>rel2-9b</strong></a>







<br />Changes since <strong>1.9: +170 -147 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.9&amp;r2=1.9.2.1">previous 1.9</a>






, to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.36&amp;r2=1.9.2.1">next main 1.36</a>







<pre class="vc_log">update to webERP module
</pre>
</div>



<div>
<hr />

<a name="rev1.9"></a>


Revision <strong>1.9</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.9&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.9">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.9">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.9&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Nov  7 10:08:50 2004 UTC</em> (4 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>




<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=rel2-9b"><strong>rel2-9b</strong></a>





<br />Changes since <strong>1.8: +7 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.8&amp;r2=1.9">previous 1.8</a>










<pre class="vc_log">GL Posting changes
</pre>
</div>



<div>
<hr />

<a name="rev1.8"></a>


Revision <strong>1.8</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.8&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.8">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.8">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.8&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Oct 14 09:13:03 2004 UTC</em> (4 years, 11 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.7: +16 -33 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.7&amp;r2=1.8">previous 1.7</a>










<pre class="vc_log">gettextification
</pre>
</div>



<div>
<hr />

<a name="rev1.7"></a>


Revision <strong>1.7</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.7&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.7">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.7">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.7&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Oct 10 04:58:31 2004 UTC</em> (4 years, 11 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.6: +4 -4 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.6&amp;r2=1.7">previous 1.6</a>










<pre class="vc_log">gettextification
</pre>
</div>



<div>
<hr />

<a name="rev1.6"></a>


Revision <strong>1.6</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.6&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.6">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.6">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.6&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Aug 29 07:17:45 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.5: +13 -9 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.5&amp;r2=1.6">previous 1.5</a>










<pre class="vc_log">Delivery diffs
</pre>
</div>



<div>
<hr />

<a name="rev1.5"></a>


Revision <strong>1.5</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.5&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.5">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.5">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.5&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Aug  1 10:16:38 2004 UTC</em> (5 years, 1 month ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.4: +11 -9 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.4&amp;r2=1.5">previous 1.4</a>










<pre class="vc_log">Period End Balances Scripts
</pre>
</div>



<div>
<hr />

<a name="rev1.4"></a>


Revision <strong>1.4</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.4&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.4">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.4">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.4&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Mar 15 07:53:15 2004 UTC</em> (5 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.3: +1 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.3&amp;r2=1.4">previous 1.3</a>










<pre class="vc_log">Dicks Revisions
</pre>
</div>



<div>
<hr />

<a name="rev1.3"></a>


Revision <strong>1.3</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.3&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.3">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.3">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.3&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Mar 14 09:01:59 2004 UTC</em> (5 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.2: +7 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.2&amp;r2=1.3">previous 1.2</a>










<pre class="vc_log">EDIProcessOrders battle continues
</pre>
</div>



<div>
<hr />

<a name="rev1.2"></a>


Revision <strong>1.2</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.2&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.2">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.2">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.2&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Mar 14 03:34:11 2004 UTC</em> (5 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.1: +41 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.1&amp;r2=1.2">previous 1.1</a>










<pre class="vc_log">Add GLAccounts.pgp bug fix
</pre>
</div>



<div>
<hr />

<a name="rev1.1.1.1"></a>
<a name="start"></a>

<a name="logicworks"></a>

Revision <strong>1.1.1.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.1.1.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.1.1.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.1.1.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.1.1.1&amp;view=log">[select for diffs]</a>




<em>(vendor branch)</em>

<br />

<em>Mon Feb 23 07:16:58 2004 UTC</em> (5 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=logicworks"><strong>logicworks</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=start"><strong>start</strong></a>






<br />Changes since <strong>1.1: +0 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.1&amp;r2=1.1.1.1">previous 1.1</a>






, to <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.36&amp;r2=1.1.1.1">next main 1.36</a>







<pre class="vc_log">Initial import.
</pre>
</div>



<div>
<hr />

<a name="rev1.1"></a>


Revision <strong>1.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?revision=1.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DateFunctions.inc?revision=1.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?annotate=1.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?r1=1.1&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Feb 23 07:16:58 2004 UTC</em> (5 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>




<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/DateFunctions.inc?view=log&amp;pathrev=logicworks"><strong>logicworks</strong></a>














<pre class="vc_log">Initial revision
</pre>
</div>

 



 <hr />
<p><a name="diff"></a>
This form allows you to request diffs between any two revisions of this file.
For each of the two "sides" of the diff,

select a symbolic revision name using the selection box, or choose
'Use Text Field' and enter a numeric revision.

</p>
<form method="get" action="/viewvc/web-erp/webERP/includes/DateFunctions.inc" id="diff_select">
<table cellpadding="2" cellspacing="0" class="auto">
<tr>
<td>&nbsp;</td>
<td>
<input type="hidden" name="view" value="diff" />
Diffs between

<select name="r1">
<option value="text" selected="selected">Use Text Field</option>

<option value="1.31:v3_10">v3_10</option>

<option value="1.28:v3-09">v3-09</option>

<option value="1.24:v3-08">v3-08</option>

<option value="1.21:v3-071">v3-071</option>

<option value="1.21:v3-06">v3-06</option>

<option value="1.20:v3-05">v3-05</option>

<option value="1.16:v3-04">v3-04</option>

<option value="1.13:v3-02">v3-02</option>

<option value="1.1.1.1:start">start</option>

<option value="1.12:release_3-00">release_3-00</option>

<option value="1.9.2.1:rel2-9b">rel2-9b</option>

<option value="1.1.1.1:logicworks">logicworks</option>

<option value="1.10.2.3:TAX_CHANGES">TAX_CHANGES</option>

<option value="1.36:MAIN">MAIN</option>

<option value="1.36:HEAD">HEAD</option>

</select>
<input type="text" size="12" name="tr1"
value="1.36"
onchange="document.getElementById('diff_select').r1.selectedIndex=0" />

and

<select name="r2">
<option value="text" selected="selected">Use Text Field</option>

<option value="1.31:v3_10">v3_10</option>

<option value="1.28:v3-09">v3-09</option>

<option value="1.24:v3-08">v3-08</option>

<option value="1.21:v3-071">v3-071</option>

<option value="1.21:v3-06">v3-06</option>

<option value="1.20:v3-05">v3-05</option>

<option value="1.16:v3-04">v3-04</option>

<option value="1.13:v3-02">v3-02</option>

<option value="1.1.1.1:start">start</option>

<option value="1.12:release_3-00">release_3-00</option>

<option value="1.9.2.1:rel2-9b">rel2-9b</option>

<option value="1.1.1.1:logicworks">logicworks</option>

<option value="1.10.2.3:TAX_CHANGES">TAX_CHANGES</option>

<option value="1.36:MAIN">MAIN</option>

<option value="1.36:HEAD">HEAD</option>

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


<form method="get" action="/viewvc/web-erp/webERP/includes/DateFunctions.inc">
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


