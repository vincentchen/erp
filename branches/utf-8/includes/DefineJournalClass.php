



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
<title>SourceForge.net Repository - [web-erp] Log of /webERP/includes/DefineJournalClass.php</title>
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


DefineJournalClass.php
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
(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=markup">view</a>)
(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DefineJournalClass.php">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=annotate">annotate</a>)
</td>
</tr>



<tr>
<td>Sticky Tag:</td>
<td><form method="get" action="/viewvc/web-erp/webERP/includes/DefineJournalClass.php" style="display: inline">
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

<a name="rev1.5"></a>
<a name="HEAD"></a>


Revision <strong>1.5</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?revision=1.5&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DefineJournalClass.php?revision=1.5">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?annotate=1.5">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.5&amp;view=log">[select for diffs]</a>




<br />

<em>Wed Sep 16 14:20:19 2009 UTC</em> (8 days, 5 hours ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=HEAD"><strong>HEAD</strong></a>






<br />Changes since <strong>1.4: +8 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.4&amp;r2=1.5">previous 1.4</a>










<pre class="vc_log">Changes required for depreciation journals
</pre>
</div>



<div>
<hr />

<a name="rev1.4"></a>
<a name="v3_10"></a>


Revision <strong>1.4</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?revision=1.4&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DefineJournalClass.php?revision=1.4">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?annotate=1.4">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.4&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Sep 26 10:45:18 2008 UTC</em> (11 months, 4 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=v3_10"><strong>v3_10</strong></a>






<br />Changes since <strong>1.3: +6 -4 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.3&amp;r2=1.4">previous 1.3</a>










<pre class="vc_log">Update to allow selection of a GL tag.
</pre>
</div>



<div>
<hr />

<a name="rev1.3"></a>
<a name="v3-09"></a>
<a name="v3-08"></a>
<a name="v3-02"></a>
<a name="v3-071"></a>
<a name="v3-06"></a>
<a name="v3-05"></a>
<a name="v3-04"></a>
<a name="release_3-00"></a>


Revision <strong>1.3</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?revision=1.3&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DefineJournalClass.php?revision=1.3">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?annotate=1.3">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.3&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Feb 12 04:38:10 2005 UTC</em> (4 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=release_3-00"><strong>release_3-00</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=v3-02"><strong>v3-02</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=v3-04"><strong>v3-04</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=v3-05"><strong>v3-05</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=v3-06"><strong>v3-06</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=v3-071"><strong>v3-071</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=v3-08"><strong>v3-08</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=v3-09"><strong>v3-09</strong></a>



<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>





<br />Changes since <strong>1.2: +1 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.2&amp;r2=1.3">previous 1.2</a>










<pre class="vc_log">merged with webERP
</pre>
</div>



<div>
<hr />

<a name="rev1.2.2.1"></a>

<a name="rel2-9b"></a>

Revision <strong>1.2.2.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?revision=1.2.2.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DefineJournalClass.php?revision=1.2.2.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?annotate=1.2.2.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.2.2.1&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Feb 11 08:18:09 2005 UTC</em> (4 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=rel2-9b"><strong>rel2-9b</strong></a>







<br />Changes since <strong>1.2: +1 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.2&amp;r2=1.2.2.1">previous 1.2</a>






, to <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.5&amp;r2=1.2.2.1">next main 1.5</a>







<pre class="vc_log">update to webERP module
</pre>
</div>



<div>
<hr />

<a name="rev1.2"></a>


Revision <strong>1.2</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?revision=1.2&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DefineJournalClass.php?revision=1.2">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?annotate=1.2">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.2&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Mar 15 07:53:16 2004 UTC</em> (5 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>




<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=rel2-9b"><strong>rel2-9b</strong></a>





<br />Changes since <strong>1.1: +63 -63 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.1&amp;r2=1.2">previous 1.1</a>










<pre class="vc_log">Dicks Revisions
</pre>
</div>



<div>
<hr />

<a name="rev1.1.1.1"></a>
<a name="start"></a>

<a name="logicworks"></a>

Revision <strong>1.1.1.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?revision=1.1.1.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DefineJournalClass.php?revision=1.1.1.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?annotate=1.1.1.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.1.1.1&amp;view=log">[select for diffs]</a>




<em>(vendor branch)</em>

<br />

<em>Mon Feb 23 07:17:13 2004 UTC</em> (5 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=logicworks"><strong>logicworks</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=start"><strong>start</strong></a>






<br />Changes since <strong>1.1: +0 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.1&amp;r2=1.1.1.1">previous 1.1</a>






, to <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.5&amp;r2=1.1.1.1">next main 1.5</a>







<pre class="vc_log">Initial import.
</pre>
</div>



<div>
<hr />

<a name="rev1.1"></a>


Revision <strong>1.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?revision=1.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/DefineJournalClass.php?revision=1.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?annotate=1.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?r1=1.1&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Feb 23 07:17:13 2004 UTC</em> (5 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>




<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/DefineJournalClass.php?view=log&amp;pathrev=logicworks"><strong>logicworks</strong></a>














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
<form method="get" action="/viewvc/web-erp/webERP/includes/DefineJournalClass.php" id="diff_select">
<table cellpadding="2" cellspacing="0" class="auto">
<tr>
<td>&nbsp;</td>
<td>
<input type="hidden" name="view" value="diff" />
Diffs between

<select name="r1">
<option value="text" selected="selected">Use Text Field</option>

<option value="1.4:v3_10">v3_10</option>

<option value="1.3:v3-09">v3-09</option>

<option value="1.3:v3-08">v3-08</option>

<option value="1.3:v3-071">v3-071</option>

<option value="1.3:v3-06">v3-06</option>

<option value="1.3:v3-05">v3-05</option>

<option value="1.3:v3-04">v3-04</option>

<option value="1.3:v3-02">v3-02</option>

<option value="1.1.1.1:start">start</option>

<option value="1.3:release_3-00">release_3-00</option>

<option value="1.2.2.1:rel2-9b">rel2-9b</option>

<option value="1.1.1.1:logicworks">logicworks</option>

<option value="1.3:TAX_CHANGES">TAX_CHANGES</option>

<option value="1.5:MAIN">MAIN</option>

<option value="1.5:HEAD">HEAD</option>

</select>
<input type="text" size="12" name="tr1"
value="1.5"
onchange="document.getElementById('diff_select').r1.selectedIndex=0" />

and

<select name="r2">
<option value="text" selected="selected">Use Text Field</option>

<option value="1.4:v3_10">v3_10</option>

<option value="1.3:v3-09">v3-09</option>

<option value="1.3:v3-08">v3-08</option>

<option value="1.3:v3-071">v3-071</option>

<option value="1.3:v3-06">v3-06</option>

<option value="1.3:v3-05">v3-05</option>

<option value="1.3:v3-04">v3-04</option>

<option value="1.3:v3-02">v3-02</option>

<option value="1.1.1.1:start">start</option>

<option value="1.3:release_3-00">release_3-00</option>

<option value="1.2.2.1:rel2-9b">rel2-9b</option>

<option value="1.1.1.1:logicworks">logicworks</option>

<option value="1.3:TAX_CHANGES">TAX_CHANGES</option>

<option value="1.5:MAIN">MAIN</option>

<option value="1.5:HEAD">HEAD</option>

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


<form method="get" action="/viewvc/web-erp/webERP/includes/DefineJournalClass.php">
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


