



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
<title>SourceForge.net Repository - [web-erp] Log of /webERP/includes/session.inc</title>
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


session.inc
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
(<a href="/viewvc/web-erp/webERP/includes/session.inc?view=markup">view</a>)
(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?view=annotate">annotate</a>)
</td>
</tr>



<tr>
<td>Sticky Tag:</td>
<td><form method="get" action="/viewvc/web-erp/webERP/includes/session.inc" style="display: inline">
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

<a name="rev1.67"></a>
<a name="HEAD"></a>


Revision <strong>1.67</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.67&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.67">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.67">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.67&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Aug 23 04:40:25 2009 UTC</em> (4 weeks, 4 days ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=HEAD"><strong>HEAD</strong></a>






<br />Changes since <strong>1.66: +5 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.66&amp;r2=1.67">previous 1.66</a>










<pre class="vc_log">Salesman login functionality
</pre>
</div>



<div>
<hr />

<a name="rev1.66"></a>


Revision <strong>1.66</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.66&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.66">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.66">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.66&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Jul 16 23:03:29 2009 UTC</em> (2 months, 1 week ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.65: +8 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.65&amp;r2=1.66">previous 1.65</a>










<pre class="vc_log">Attempt to get the installer going with no config.php in distribution
</pre>
</div>



<div>
<hr />

<a name="rev1.65"></a>


Revision <strong>1.65</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.65&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.65">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.65">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.65&amp;view=log">[select for diffs]</a>




<br />

<em>Mon May 18 09:09:42 2009 UTC</em> (4 months, 1 week ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.64: +16 -17 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.64&amp;r2=1.65">previous 1.64</a>










<pre class="vc_log">rewrite of function to get the logo file without using scandir which requires PHP 5 - PHP 4.3 or above is still required
</pre>
</div>



<div>
<hr />

<a name="rev1.64"></a>


Revision <strong>1.64</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.64&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.64">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.64">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.64&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Apr 10 14:24:59 2009 UTC</em> (5 months, 2 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.63: +2 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.63&amp;r2=1.64">previous 1.63</a>










<pre class="vc_log">Ensure last visit field is always updated
</pre>
</div>



<div>
<hr />

<a name="rev1.63"></a>


Revision <strong>1.63</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.63&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.63">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.63">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.63&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Feb  8 09:05:31 2009 UTC</em> (7 months, 2 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.62: +9 -9 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.62&amp;r2=1.63">previous 1.62</a>










<pre class="vc_log">Logo search changes
</pre>
</div>



<div>
<hr />

<a name="rev1.62"></a>


Revision <strong>1.62</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.62&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.62">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.62">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.62&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Feb  6 11:57:28 2009 UTC</em> (7 months, 2 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.61: +15 -15 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.61&amp;r2=1.62">previous 1.61</a>










<pre class="vc_log">Work on xhtml compatability by Bogdan Stanciu
</pre>
</div>



<div>
<hr />

<a name="rev1.61"></a>
<a name="v3_10"></a>


Revision <strong>1.61</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.61&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.61">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.61">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.61&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Jan 17 12:54:35 2009 UTC</em> (8 months ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=v3_10"><strong>v3_10</strong></a>






<br />Changes since <strong>1.60: +45 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.60&amp;r2=1.61">previous 1.60</a>










<pre class="vc_log">Store the logo in a session variable, and search for the logo selecting a default if none present, as per Klaus Wulff.
</pre>
</div>



<div>
<hr />

<a name="rev1.60"></a>


Revision <strong>1.60</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.60&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.60">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.60">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.60&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Dec 26 14:27:51 2008 UTC</em> (8 months, 4 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.59: +17 -14 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.59&amp;r2=1.60">previous 1.59</a>










<pre class="vc_log">Correct session timeout problem
</pre>
</div>



<div>
<hr />

<a name="rev1.59"></a>
<a name="v3-09"></a>


Revision <strong>1.59</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.59&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.59">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.59">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.59&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Mar 15 23:00:28 2008 UTC</em> (18 months, 1 week ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=v3-09"><strong>v3-09</strong></a>






<br />Changes since <strong>1.58: +15 -9 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.58&amp;r2=1.59">previous 1.58</a>










<pre class="vc_log">session changed defualt theme to silverwolf
</pre>
</div>



<div>
<hr />

<a name="rev1.58"></a>


Revision <strong>1.58</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.58&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.58">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.58">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.58&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Mar 15 08:20:00 2008 UTC</em> (18 months, 1 week ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.57: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.57&amp;r2=1.58">previous 1.57</a>










<pre class="vc_log">Correct $theme to silverwolf
</pre>
</div>



<div>
<hr />

<a name="rev1.57"></a>


Revision <strong>1.57</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.57&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.57">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.57">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.57&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Mar  1 02:24:30 2008 UTC</em> (18 months, 3 weeks ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.56: +25 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.56&amp;r2=1.57">previous 1.56</a>










<pre class="vc_log">automatic updates to exchange rates from ECB
</pre>
</div>



<div>
<hr />

<a name="rev1.56"></a>


Revision <strong>1.56</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.56&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.56">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.56">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.56&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Feb 25 18:12:43 2008 UTC</em> (18 months, 4 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.55: +12 -4 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.55&amp;r2=1.56">previous 1.55</a>










<pre class="vc_log">Correct filtering for POST varaiables that are arrays.
</pre>
</div>



<div>
<hr />

<a name="rev1.55"></a>


Revision <strong>1.55</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.55&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.55">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.55">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.55&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Feb  9 08:43:41 2008 UTC</em> (19 months, 2 weeks ago) by <em>tim_schofield</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.54: +8 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.54&amp;r2=1.55">previous 1.54</a>










<pre class="vc_log">Bug fixes and code cleanup
</pre>
</div>



<div>
<hr />

<a name="rev1.54"></a>


Revision <strong>1.54</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.54&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.54">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.54">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.54&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Nov 13 07:59:54 2007 UTC</em> (22 months, 1 week ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.53: +10 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.53&amp;r2=1.54">previous 1.53</a>










<pre class="vc_log">modified audit trail work
</pre>
</div>



<div>
<hr />

<a name="rev1.53"></a>
<a name="v3-08"></a>


Revision <strong>1.53</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.53&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.53">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.53">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.53&amp;view=log">[select for diffs]</a>




<br />

<em>Wed Sep 12 10:01:13 2007 UTC</em> (2 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=v3-08"><strong>v3-08</strong></a>






<br />Changes since <strong>1.52: +9 -7 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.52&amp;r2=1.53">previous 1.52</a>










<pre class="vc_log">moved debug=1 in session.inc within the AllowAnyone!=true block
</pre>
</div>



<div>
<hr />

<a name="rev1.52"></a>
<a name="v3-071"></a>
<a name="v3-06"></a>


Revision <strong>1.52</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.52&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.52">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.52">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.52&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Jun 25 10:45:53 2007 UTC</em> (2 years, 3 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=v3-06"><strong>v3-06</strong></a>,

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=v3-071"><strong>v3-071</strong></a>






<br />Changes since <strong>1.51: +3 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.51&amp;r2=1.52">previous 1.51</a>










<pre class="vc_log">locstock in update fails
</pre>
</div>



<div>
<hr />

<a name="rev1.51"></a>


Revision <strong>1.51</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.51&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.51">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.51">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.51&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Jun  8 09:26:33 2007 UTC</em> (2 years, 3 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.50: +2 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.50&amp;r2=1.51">previous 1.50</a>










<pre class="vc_log">theme fix Tim Scofield
</pre>
</div>



<div>
<hr />

<a name="rev1.50"></a>


Revision <strong>1.50</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.50&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.50">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.50">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.50&amp;view=log">[select for diffs]</a>




<br />

<em>Sun May 20 09:26:59 2007 UTC</em> (2 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.49: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.49&amp;r2=1.50">previous 1.49</a>










<pre class="vc_log">silverwolf theme - Rob Wolf
</pre>
</div>



<div>
<hr />

<a name="rev1.49"></a>


Revision <strong>1.49</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.49&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.49">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.49">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.49&amp;view=log">[select for diffs]</a>




<br />

<em>Sun May 20 03:59:37 2007 UTC</em> (2 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.48: +34 -32 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.48&amp;r2=1.49">previous 1.48</a>










<pre class="vc_log">SelectProduct now shows summary data and purchasing info
</pre>
</div>



<div>
<hr />

<a name="rev1.48"></a>
<a name="v3-05"></a>


Revision <strong>1.48</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.48&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.48">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.48">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.48&amp;view=log">[select for diffs]</a>




<br />

<em>Wed Nov 22 19:14:31 2006 UTC</em> (2 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=v3-05"><strong>v3-05</strong></a>






<br />Changes since <strong>1.47: +4 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.47&amp;r2=1.48">previous 1.47</a>










<pre class="vc_log">pg work
</pre>
</div>



<div>
<hr />

<a name="rev1.47"></a>


Revision <strong>1.47</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.47&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.47">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.47">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.47&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Aug 31 10:07:06 2006 UTC</em> (3 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.46: +3 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.46&amp;r2=1.47">previous 1.46</a>










<pre class="vc_log">Transfers bug fixes auto_increment in sql scripts
</pre>
</div>



<div>
<hr />

<a name="rev1.46"></a>


Revision <strong>1.46</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.46&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.46">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.46">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.46&amp;view=log">[select for diffs]</a>




<br />

<em>Wed Jul  5 09:58:54 2006 UTC</em> (3 years, 2 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.45: +1 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.45&amp;r2=1.46">previous 1.45</a>










<pre class="vc_log">specialOrder.php didnt allow multiple lines to be inserted into sales orders ... now fixed. SupplierInvoice.php did not refresh and read manually entered tax values when posting directly without updating first - now fixed
</pre>
</div>



<div>
<hr />

<a name="rev1.45"></a>


Revision <strong>1.45</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.45&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.45">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.45">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.45&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Jun 29 07:22:59 2006 UTC</em> (3 years, 2 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.44: +2 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.44&amp;r2=1.45">previous 1.44</a>










<pre class="vc_log">Steve changes
</pre>
</div>



<div>
<hr />

<a name="rev1.44"></a>


Revision <strong>1.44</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.44&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.44">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.44">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.44&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Jan 27 22:49:18 2006 UTC</em> (3 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.43: +10 -7 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.43&amp;r2=1.44">previous 1.43</a>










<pre class="vc_log">Decimal places fix confirmDispatch_Invoices.php - escape characters in SQL statements of GLAccounts.php - PDFOrdersInvoiced.php corrected for multiple sales order lines of the same item
</pre>
</div>



<div>
<hr />

<a name="rev1.43"></a>


Revision <strong>1.43</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.43&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.43">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.43">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.43&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Dec 18 09:04:52 2005 UTC</em> (3 years, 9 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.42: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.42&amp;r2=1.43">previous 1.42</a>










<pre class="vc_log">Dave Premo form builder add on to the report builder - DIFOT - Delivery In Full On Time report
</pre>
</div>



<div>
<hr />

<a name="rev1.42"></a>


Revision <strong>1.42</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.42&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.42">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.42">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.42&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Nov 21 09:12:28 2005 UTC</em> (3 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.41: +16 -16 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.41&amp;r2=1.42">previous 1.41</a>










<pre class="vc_log">Daves Report Writer Stuff
</pre>
</div>



<div>
<hr />

<a name="rev1.41"></a>
<a name="v3-04"></a>


Revision <strong>1.41</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.41&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.41">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.41">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.41&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Oct 24 00:58:18 2005 UTC</em> (3 years, 11 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=v3-04"><strong>v3-04</strong></a>






<br />Changes since <strong>1.40: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.40&amp;r2=1.41">previous 1.40</a>










<pre class="vc_log">Chinese Korean Japanese pdf support
</pre>
</div>



<div>
<hr />

<a name="rev1.40"></a>


Revision <strong>1.40</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.40&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.40">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.40">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.40&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Oct 17 09:22:37 2005 UTC</em> (3 years, 11 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.39: +2 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.39&amp;r2=1.40">previous 1.39</a>










<pre class="vc_log">Fix for nongettext translation of SelectOrderItems and CreditItems.php
</pre>
</div>



<div>
<hr />

<a name="rev1.39"></a>


Revision <strong>1.39</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.39&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.39">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.39">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.39&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Sep 22 10:14:08 2005 UTC</em> (4 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.38: +10 -7 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.38&amp;r2=1.39">previous 1.38</a>










<pre class="vc_log">confirm
</pre>
</div>



<div>
<hr />

<a name="rev1.38"></a>
<a name="v3-02"></a>


Revision <strong>1.38</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.38&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.38">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.38">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.38&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Jul 10 07:15:07 2005 UTC</em> (4 years, 2 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=v3-02"><strong>v3-02</strong></a>






<br />Changes since <strong>1.37: +5 -9 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.37&amp;r2=1.38">previous 1.37</a>










<pre class="vc_log">multicompany
</pre>
</div>



<div>
<hr />

<a name="rev1.37"></a>


Revision <strong>1.37</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.37&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.37">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.37">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.37&amp;view=log">[select for diffs]</a>




<br />

<em>Thu May 19 08:40:28 2005 UTC</em> (4 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.36: +5 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.36&amp;r2=1.37">previous 1.36</a>










<pre class="vc_log">minor stuff
</pre>
</div>



<div>
<hr />

<a name="rev1.36"></a>


Revision <strong>1.36</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.36&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.36">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.36">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.36&amp;view=log">[select for diffs]</a>




<br />

<em>Sat May  7 11:01:42 2005 UTC</em> (4 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.35: +7 -7 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.35&amp;r2=1.36">previous 1.35</a>










<pre class="vc_log">php-gettext Braian Gomez
</pre>
</div>



<div>
<hr />

<a name="rev1.35"></a>


Revision <strong>1.35</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.35&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.35">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.35">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.35&amp;view=log">[select for diffs]</a>




<br />

<em>Sun May  1 10:36:01 2005 UTC</em> (4 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.34: +0 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.34&amp;r2=1.35">previous 1.34</a>










<pre class="vc_log">Merge Tax Changes Plus Braians bug fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.34"></a>


Revision <strong>1.34</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.34&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.34">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.34">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.34&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Apr 29 09:34:40 2005 UTC</em> (4 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.33: +5 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.33&amp;r2=1.34">previous 1.33</a>










<pre class="vc_log">post 3.00
</pre>
</div>



<div>
<hr />

<a name="rev1.29.2.2"></a>

<a name="TAX_CHANGES"></a>

Revision <strong>1.29.2.2</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.29.2.2&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.29.2.2">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.29.2.2">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.29.2.2&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Apr 24 07:19:02 2005 UTC</em> (4 years, 5 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>







<br />Changes since <strong>1.29.2.1: +15 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.29.2.1&amp;r2=1.29.2.2">previous 1.29.2.1</a>





, to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.29&amp;r2=1.29.2.2">branch point 1.29</a>




, to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.67&amp;r2=1.29.2.2">next main 1.67</a>







<pre class="vc_log">brought up to v3.00
</pre>
</div>



<div>
<hr />

<a name="rev1.33"></a>
<a name="release_3-00"></a>


Revision <strong>1.33</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.33&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.33">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.33">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.33&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Apr 17 09:04:01 2005 UTC</em> (4 years, 5 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=release_3-00"><strong>release_3-00</strong></a>






<br />Changes since <strong>1.32: +20 -15 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.32&amp;r2=1.33">previous 1.32</a>










<pre class="vc_log">bug fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.29.2.1"></a>


Revision <strong>1.29.2.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.29.2.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.29.2.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.29.2.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.29.2.1&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Mar 28 03:19:22 2005 UTC</em> (4 years, 5 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>







<br />Changes since <strong>1.29: +188 -161 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.29&amp;r2=1.29.2.1">previous 1.29</a>










<pre class="vc_log">merged main trunk back into the tax changes
</pre>
</div>



<div>
<hr />

<a name="rev1.32"></a>


Revision <strong>1.32</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.32&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.32">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.32">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.32&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Mar 27 03:21:30 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.31: +16 -8 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.31&amp;r2=1.32">previous 1.31</a>










<pre class="vc_log">DB maintenance
</pre>
</div>



<div>
<hr />

<a name="rev1.31"></a>


Revision <strong>1.31</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.31&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.31">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.31">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.31&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Mar 25 23:47:30 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.30: +121 -110 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.30&amp;r2=1.31">previous 1.30</a>










<pre class="vc_log">bug fixed recurr orders
</pre>
</div>



<div>
<hr />

<a name="rev1.30"></a>


Revision <strong>1.30</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.30&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.30">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.30">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.30&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Mar 25 05:15:40 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.29: +89 -81 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.29&amp;r2=1.30">previous 1.29</a>










<pre class="vc_log">version 3
</pre>
</div>



<div>
<hr />

<a name="rev1.29"></a>


Revision <strong>1.29</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.29&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.29">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.29">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.29&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Feb 26 22:00:29 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>




<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=TAX_CHANGES"><strong>TAX_CHANGES</strong></a>





<br />Changes since <strong>1.28: +20 -18 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.28&amp;r2=1.29">previous 1.28</a>










<pre class="vc_log">tidy up login screen and footer
</pre>
</div>



<div>
<hr />

<a name="rev1.28"></a>


Revision <strong>1.28</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.28&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.28">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.28">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.28&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Feb 26 07:56:45 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.27: +5 -4 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.27&amp;r2=1.28">previous 1.27</a>










<pre class="vc_log">docs update
</pre>
</div>



<div>
<hr />

<a name="rev1.27"></a>


Revision <strong>1.27</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.27&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.27">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.27">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.27&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Feb 26 03:05:30 2005 UTC</em> (4 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.26: +15 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.26&amp;r2=1.27">previous 1.26</a>










<pre class="vc_log">Janusz FPDF wrapper
</pre>
</div>



<div>
<hr />

<a name="rev1.26"></a>


Revision <strong>1.26</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.26&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.26">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.26">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.26&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Feb 17 07:52:33 2005 UTC</em> (4 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.25: +3 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.25&amp;r2=1.26">previous 1.25</a>










<pre class="vc_log">Dicks bug fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.25"></a>


Revision <strong>1.25</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.25&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.25">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.25">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.25&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Feb 12 04:38:10 2005 UTC</em> (4 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.24: +60 -37 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.24&amp;r2=1.25">previous 1.24</a>










<pre class="vc_log">merged with webERP
</pre>
</div>



<div>
<hr />

<a name="rev1.24.2.1"></a>

<a name="rel2-9b"></a>

Revision <strong>1.24.2.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.24.2.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.24.2.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.24.2.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.24.2.1&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Feb 11 08:18:16 2005 UTC</em> (4 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=rel2-9b"><strong>rel2-9b</strong></a>







<br />Changes since <strong>1.24: +60 -37 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.24&amp;r2=1.24.2.1">previous 1.24</a>






, to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.67&amp;r2=1.24.2.1">next main 1.67</a>







<pre class="vc_log">update to webERP module
</pre>
</div>



<div>
<hr />

<a name="rev1.24"></a>


Revision <strong>1.24</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.24&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.24">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.24">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.24&amp;view=log">[select for diffs]</a>




<br />

<em>Wed Dec 29 01:17:25 2004 UTC</em> (4 years, 8 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>




<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=rel2-9b"><strong>rel2-9b</strong></a>





<br />Changes since <strong>1.23: +28 -20 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.23&amp;r2=1.24">previous 1.23</a>










<pre class="vc_log">po maint scripts
</pre>
</div>



<div>
<hr />

<a name="rev1.23"></a>


Revision <strong>1.23</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.23&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.23">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.23">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.23&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Dec 24 22:09:29 2004 UTC</em> (4 years, 9 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.22: +7 -11 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.22&amp;r2=1.23">previous 1.22</a>










<pre class="vc_log">login language
</pre>
</div>



<div>
<hr />

<a name="rev1.22"></a>


Revision <strong>1.22</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.22&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.22">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.22">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.22&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Dec 24 03:17:03 2004 UTC</em> (4 years, 9 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.21: +68 -65 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.21&amp;r2=1.22">previous 1.21</a>










<pre class="vc_log">pre release candidate
</pre>
</div>



<div>
<hr />

<a name="rev1.21"></a>


Revision <strong>1.21</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.21&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.21">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.21">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.21&amp;view=log">[select for diffs]</a>




<br />

<em>Tue Dec 21 09:49:24 2004 UTC</em> (4 years, 9 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.20: +3 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.20&amp;r2=1.21">previous 1.20</a>










<pre class="vc_log">bug fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.20"></a>


Revision <strong>1.20</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.20&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.20">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.20">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.20&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Dec 12 04:17:23 2004 UTC</em> (4 years, 9 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.19: +4 -1 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.19&amp;r2=1.20">previous 1.19</a>










<pre class="vc_log">Steves double check
</pre>
</div>



<div>
<hr />

<a name="rev1.19"></a>


Revision <strong>1.19</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.19&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.19">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.19">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.19&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Dec  5 07:24:57 2004 UTC</em> (4 years, 9 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.18: +74 -74 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.18&amp;r2=1.19">previous 1.18</a>










<pre class="vc_log">messages tidy kitch - take 12
</pre>
</div>



<div>
<hr />

<a name="rev1.18"></a>


Revision <strong>1.18</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.18&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.18">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.18">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.18&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Nov 22 08:09:22 2004 UTC</em> (4 years, 10 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.17: +5 -3 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.17&amp;r2=1.18">previous 1.17</a>










<pre class="vc_log">bug in GoodsReceived.php
</pre>
</div>



<div>
<hr />

<a name="rev1.17"></a>


Revision <strong>1.17</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.17&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.17">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.17">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.17&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Oct 22 07:56:12 2004 UTC</em> (4 years, 11 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.16: +3 -8 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.16&amp;r2=1.17">previous 1.16</a>










<pre class="vc_log">new bg and better login
</pre>
</div>



<div>
<hr />

<a name="rev1.16"></a>


Revision <strong>1.16</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.16&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.16">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.16">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.16&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Oct 21 09:20:00 2004 UTC</em> (4 years, 11 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.15: +12 -7 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.15&amp;r2=1.16">previous 1.15</a>










<pre class="vc_log">various bug fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.15"></a>


Revision <strong>1.15</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.15&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.15">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.15">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.15&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Oct 16 07:14:41 2004 UTC</em> (4 years, 11 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.14: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.14&amp;r2=1.15">previous 1.14</a>










<pre class="vc_log">gettextification
</pre>
</div>



<div>
<hr />

<a name="rev1.14"></a>


Revision <strong>1.14</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.14&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.14">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.14">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.14&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Sep 18 10:19:42 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.13: +3 -4 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.13&amp;r2=1.14">previous 1.13</a>










<pre class="vc_log">Tax report
</pre>
</div>



<div>
<hr />

<a name="rev1.13"></a>


Revision <strong>1.13</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.13&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.13">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.13">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.13&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Sep  5 05:35:08 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.12: +2 -26 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.12&amp;r2=1.13">previous 1.12</a>










<pre class="vc_log">Aged Debtors Suppliers gettextified
</pre>
</div>



<div>
<hr />

<a name="rev1.12"></a>


Revision <strong>1.12</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.12&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.12">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.12">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.12&amp;view=log">[select for diffs]</a>




<br />

<em>Sat Sep  4 05:57:51 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.11: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.11&amp;r2=1.12">previous 1.11</a>










<pre class="vc_log">Customers.php Victor
</pre>
</div>



<div>
<hr />

<a name="rev1.11"></a>


Revision <strong>1.11</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.11&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.11">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.11">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.11&amp;view=log">[select for diffs]</a>




<br />

<em>Fri Sep  3 07:22:26 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.10: +7 -5 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.10&amp;r2=1.11">previous 1.10</a>










<pre class="vc_log">Account Groups
</pre>
</div>



<div>
<hr />

<a name="rev1.10"></a>


Revision <strong>1.10</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.10&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.10">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.10">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.10&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Sep  2 09:52:02 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.9: +14 -6 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.9&amp;r2=1.10">previous 1.9</a>










<pre class="vc_log">Go to page bug fixed
</pre>
</div>



<div>
<hr />

<a name="rev1.9"></a>


Revision <strong>1.9</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.9&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.9">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.9">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.9&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Aug 29 07:17:45 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.8: +2 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.8&amp;r2=1.9">previous 1.8</a>










<pre class="vc_log">Delivery diffs
</pre>
</div>



<div>
<hr />

<a name="rev1.8"></a>


Revision <strong>1.8</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.8&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.8">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.8">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.8&amp;view=log">[select for diffs]</a>




<br />

<em>Sun Aug 29 01:29:58 2004 UTC</em> (5 years ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.7: +35 -4 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.7&amp;r2=1.8">previous 1.7</a>










<pre class="vc_log">Steves Paging
</pre>
</div>



<div>
<hr />

<a name="rev1.7"></a>


Revision <strong>1.7</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.7&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.7">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.7">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.7&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Aug 23 10:21:37 2004 UTC</em> (5 years, 1 month ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.6: +7 -6 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.6&amp;r2=1.7">previous 1.6</a>










<pre class="vc_log">PHP5 bug strrpos
</pre>
</div>



<div>
<hr />

<a name="rev1.6"></a>


Revision <strong>1.6</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.6&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.6">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.6">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.6&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Aug 23 01:05:25 2004 UTC</em> (5 years, 1 month ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.5: +30 -12 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.5&amp;r2=1.6">previous 1.5</a>










<pre class="vc_log">translation stuff
</pre>
</div>



<div>
<hr />

<a name="rev1.5"></a>


Revision <strong>1.5</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.5&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.5">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.5">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.5&amp;view=log">[select for diffs]</a>




<br />

<em>Thu Jul  8 10:31:31 2004 UTC</em> (5 years, 2 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.4: +9 -8 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.4&amp;r2=1.5">previous 1.4</a>










<pre class="vc_log">Help
</pre>
</div>



<div>
<hr />

<a name="rev1.4"></a>


Revision <strong>1.4</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.4&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.4">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.4">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.4&amp;view=log">[select for diffs]</a>




<br />

<em>Wed May 19 10:42:59 2004 UTC</em> (5 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.3: +1 -2 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.3&amp;r2=1.4">previous 1.3</a>










<pre class="vc_log">Order items cart class fixes
</pre>
</div>



<div>
<hr />

<a name="rev1.3"></a>


Revision <strong>1.3</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.3&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.3">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.3">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.3&amp;view=log">[select for diffs]</a>




<br />

<em>Wed May  5 10:12:44 2004 UTC</em> (5 years, 4 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.2: +1 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.2&amp;r2=1.3">previous 1.2</a>










<pre class="vc_log">Jesse Serialised stuff
</pre>
</div>



<div>
<hr />

<a name="rev1.2"></a>


Revision <strong>1.2</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.2&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.2">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.2">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.2&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Mar 15 07:53:16 2004 UTC</em> (5 years, 6 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>







<br />Changes since <strong>1.1: +1 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.1&amp;r2=1.2">previous 1.1</a>










<pre class="vc_log">Dicks Revisions
</pre>
</div>



<div>
<hr />

<a name="rev1.1.1.1"></a>
<a name="start"></a>

<a name="logicworks"></a>

Revision <strong>1.1.1.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.1.1.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.1.1.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.1.1.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.1.1.1&amp;view=log">[select for diffs]</a>




<em>(vendor branch)</em>

<br />

<em>Mon Feb 23 07:16:57 2004 UTC</em> (5 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=logicworks"><strong>logicworks</strong></a>



<br />CVS Tags:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=start"><strong>start</strong></a>






<br />Changes since <strong>1.1: +0 -0 lines</strong>







<br />Diff to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.1&amp;r2=1.1.1.1">previous 1.1</a>






, to <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.67&amp;r2=1.1.1.1">next main 1.67</a>







<pre class="vc_log">Initial import.
</pre>
</div>



<div>
<hr />

<a name="rev1.1"></a>


Revision <strong>1.1</strong> -

(<a href="/viewvc/web-erp/webERP/includes/session.inc?revision=1.1&amp;view=markup">view</a>)

(<a href="/viewvc/*checkout*/web-erp/webERP/includes/session.inc?revision=1.1">download</a>)

(<a href="/viewvc/web-erp/webERP/includes/session.inc?annotate=1.1">annotate</a>)



- <a href="/viewvc/web-erp/webERP/includes/session.inc?r1=1.1&amp;view=log">[select for diffs]</a>




<br />

<em>Mon Feb 23 07:16:57 2004 UTC</em> (5 years, 7 months ago) by <em>daintree</em>


<br />Branch:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=MAIN"><strong>MAIN</strong></a>




<br />Branch point for:

<a href="/viewvc/web-erp/webERP/includes/session.inc?view=log&amp;pathrev=logicworks"><strong>logicworks</strong></a>














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
<form method="get" action="/viewvc/web-erp/webERP/includes/session.inc" id="diff_select">
<table cellpadding="2" cellspacing="0" class="auto">
<tr>
<td>&nbsp;</td>
<td>
<input type="hidden" name="view" value="diff" />
Diffs between

<select name="r1">
<option value="text" selected="selected">Use Text Field</option>

<option value="1.61:v3_10">v3_10</option>

<option value="1.59:v3-09">v3-09</option>

<option value="1.53:v3-08">v3-08</option>

<option value="1.52:v3-071">v3-071</option>

<option value="1.52:v3-06">v3-06</option>

<option value="1.48:v3-05">v3-05</option>

<option value="1.41:v3-04">v3-04</option>

<option value="1.38:v3-02">v3-02</option>

<option value="1.1.1.1:start">start</option>

<option value="1.33:release_3-00">release_3-00</option>

<option value="1.24.2.1:rel2-9b">rel2-9b</option>

<option value="1.1.1.1:logicworks">logicworks</option>

<option value="1.29.2.2:TAX_CHANGES">TAX_CHANGES</option>

<option value="1.67:MAIN">MAIN</option>

<option value="1.67:HEAD">HEAD</option>

</select>
<input type="text" size="12" name="tr1"
value="1.67"
onchange="document.getElementById('diff_select').r1.selectedIndex=0" />

and

<select name="r2">
<option value="text" selected="selected">Use Text Field</option>

<option value="1.61:v3_10">v3_10</option>

<option value="1.59:v3-09">v3-09</option>

<option value="1.53:v3-08">v3-08</option>

<option value="1.52:v3-071">v3-071</option>

<option value="1.52:v3-06">v3-06</option>

<option value="1.48:v3-05">v3-05</option>

<option value="1.41:v3-04">v3-04</option>

<option value="1.38:v3-02">v3-02</option>

<option value="1.1.1.1:start">start</option>

<option value="1.33:release_3-00">release_3-00</option>

<option value="1.24.2.1:rel2-9b">rel2-9b</option>

<option value="1.1.1.1:logicworks">logicworks</option>

<option value="1.29.2.2:TAX_CHANGES">TAX_CHANGES</option>

<option value="1.67:MAIN">MAIN</option>

<option value="1.67:HEAD">HEAD</option>

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


<form method="get" action="/viewvc/web-erp/webERP/includes/session.inc">
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


