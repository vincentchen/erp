<?php


/********************************************/
/** STANDARD MESSAGE HANDLING & FORMATTING **/
/********************************************/

function prnMsg($Msg,$Type='info', $Prefix=''){

	echo '<P>' . getMsg($Msg, $Type, $Prefix) . '</P>';

}//prnMsg

function getMsg($Msg,$Type='info',$Prefix=''){
	$Colour='';
	switch($Type){
		case 'error':
			$Class = 'error';
			$Prefix = $Prefix ? $Prefix : _('ERROR') . ' ' ._('Message Report');
			break;
		case 'warn':
			$Class = 'warn';
			$Prefix = $Prefix ? $Prefix : _('WARNING') . ' ' . _('Message Report');
			break;
		case 'success':
			$Class = 'success';
			$Prefix = $Prefix ? $Prefix : _('SUCCESS') . ' ' . _('Report');
			break;
		case 'info':
		default:
			$Prefix = $Prefix ? $Prefix : _('INFORMATION') . ' ' ._('Message');
			$Class = 'info';
	}
	return '<DIV class="'.$Class.'"><P><B>' . $Prefix . '</B> : ' .$Msg . '<P></DIV>';
}//getMsg

function IsEmailAddress($TestEmailAddress){

/*thanks to Gavin Sharp for this regular expression to test validity of email addresses */

	if (function_exists("preg_match")){
		if(preg_match("/^(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}$/", $TestEmailAddress)){
			return true;
		} else {
			return false;
		}
	} else {
		if (strlen($TestEmailAddress)>5 AND strstr($TestEmailAddress,'@')>2 AND (strstr($TestEmailAddress,'.co')>3 OR strstr($TestEmailAddress,'.org')>3 OR strstr($TestEmailAddress,'.net')>3 OR strstr($TestEmailAddress,'.edu')>3 OR strstr($TestEmailAddress,'.biz')>3)){
			return true;
		} else {
			return false;
		}
	}
}


Function ContainsIllegalCharacters ($CheckVariable) {

	if (strstr($CheckVariable,"'")
		OR strstr($CheckVariable,'+')
		OR strstr($CheckVariable,"\"")
		OR strstr($CheckVariable,'&')
		OR strstr($CheckVariable,"\\")
		OR strstr($CheckVariable,'"')){

		return true;
	} else {
		return false;
	}
}


function pre_var_dump(&$var){
	echo "<div align=left><pre>";
	var_dump($var);
	echo "</pre></div>";
}

?>
