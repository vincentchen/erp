<?php

/* $Revision: 1.13 $ */

	echo '<BR><BR>';
	echo '</TD>';
	echo '</TR>';
	echo '</TABLE>';

	echo '</TD>';
/*Do the borders */
	echo '<TD BGCOLOR="#555555" COLSPAN="3"></TD></TR>';
	echo '<TR><TD BGCOLOR="#555555" WIDTH="10"></TD></TR>';
	echo '<TR BGCOLOR="#555555"><TD COLSPAN=3><FONT SIZE="1" COLOR="#555555">Wont see this cos same colour as background</FONT></TD></TR>';
	
	echo '</TABLE>';

	echo '<FONT SIZE=2>' . date('l ' . $_SESSION['DefaultDateFormat'] . ' | g:i A') . '</FONT>';

	echo '<TABLE ALIGN="center" ID="footer">';
	
	echo '<TR>';
	echo '<TD ALIGN="center">';
	echo '<A HREF="http://www.weberp.org" TARGET="_blank"><IMG SRC="css/webERPsm.gif" BORDER="0" ALT="" TITLE="webERP ' . _('Copyright') . ' &copy; Logic Works Ltd - 2005"></A>';
	echo '<BR>' . _('Version') . ' - ' . $Version;
	echo '</TD></TR>';

	echo '<TR><TD ALIGN="center" CLASS="footer">webERP ' . _('Copyright') . ' &copy; Logic Works Ltd - 2005</TD></TR>';

	echo '</TABLE>';
	echo '</BODY>';
	echo '</HTML>';

?>