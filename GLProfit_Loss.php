<?php

/* $Revision: 1.2 $ */

/*Through deviousness and cunning, this system allows trial balances for any date range that recalcuates the p & l balances and shows the balance sheets as at the end of the period selected - so first off need to show the input of criteria screen while the user is selecting the criteria the system is posting any unposted transactions */

$PageSecurity = 8;

include ("includes/session.inc");
$title = _("Profit and Loss");
include("includes/header.inc");
include("includes/DateFunctions.inc");
include("includes/SQL_CommonFunctions.inc");


echo "<FORM METHOD='POST' ACTION=" . $_SERVER["PHP_SELF"] . "?" . SID . ">";

if ($_POST["FromPeriod"] > $_POST["ToPeriod"]){
	echo "<P>"._("The selected period from is actually after the period to! Please re-select the reporting period.");
	$_POST["SelectADifferentPeriod"]=_("Select A Different Period");
}

if ((! isset($_POST["FromPeriod"]) AND ! isset($_POST["ToPeriod"])) OR $_POST["SelectADifferentPeriod"]==_("Select A Different Period")){

	if (Date("m") > $YearEnd){
		/*Dates in SQL format */
		$DefaultFromDate = Date ("Y-m-d", Mktime(0,0,0,$YearEnd + 2,0,Date("Y")));
	} else {
		$DefaultFromDate = Date ("Y-m-d", Mktime(0,0,0,$YearEnd + 2,0,Date("Y")-1));
	}

/*Show a form to allow input of criteria for TB to show */
	echo "<CENTER><TABLE><TR><TD>"._("Select Period From").":</TD><TD><SELECT Name='FromPeriod'>";

	$sql = "SELECT PeriodNo, LastDate_In_Period FROM Periods";
	$Periods = DB_query($sql,$db);


	while ($myrow=DB_fetch_array($Periods,$db)){
		if(isset($_POST["FromPeriod"]) AND $_POST["FromPeriod"]!=""){
			if( $_POST["FromPeriod"]== $myrow["PeriodNo"]){
				echo "<OPTION SELECTED VALUE=" . $myrow["PeriodNo"] . ">" .MonthAndYearFromSQLDate($myrow["LastDate_In_Period"]);
			} else {
				echo "<OPTION VALUE=" . $myrow["PeriodNo"] . ">" . MonthAndYearFromSQLDate($myrow["LastDate_In_Period"]);
			}
		} else {
			if($myrow["LastDate_In_Period"]==$DefaultFromDate){
				echo "<OPTION SELECTED VALUE=" . $myrow["PeriodNo"] . ">" . MonthAndYearFromSQLDate($myrow["LastDate_In_Period"]);
			} else {
				echo "<OPTION VALUE=" . $myrow["PeriodNo"] . ">" . MonthAndYearFromSQLDate($myrow["LastDate_In_Period"]);
			}
		}
	}

	echo "</SELECT></TD></TR>";
	if (!isset($_POST["ToPeriod"]) OR $_POST["ToPeriod"]==""){
		$sql = "SELECT Max(PeriodNo) FROM Periods";
		$MaxPrd = DB_query($sql,$db);
		$MaxPrdrow = DB_fetch_row($MaxPrd);

		$DefaultToPeriod = (int) ($MaxPrdrow[0]-1);
	} else {
		$DefaultToPeriod = $_POST["ToPeriod"];
	}

	echo '<TR><TD>' . _('Select Period To:') . "</TD><TD><SELECT Name='ToPeriod'>";

	$RetResult = DB_data_seek($Periods,0);

	while ($myrow=DB_fetch_array($Periods,$db)){

		if($myrow["PeriodNo"]==$DefaultToPeriod){
			echo "<OPTION SELECTED VALUE=" . $myrow["PeriodNo"] . ">" . MonthAndYearFromSQLDate($myrow["LastDate_In_Period"]);
		} else {
			echo "<OPTION VALUE =" . $myrow["PeriodNo"] . ">" . MonthAndYearFromSQLDate($myrow["LastDate_In_Period"]);
		}
	}
	echo "</SELECT></TD></TR>";

	echo "<TR><TD>"._("Detail Or Summary").":</TD><TD><SELECT Name='Detail'>";
		echo "<OPTION SELECTED VALUE='Summary'>"._("Summary");
		echo "<OPTION SELECTED VALUE='Detailed'>"._("All Accounts");
	echo "</SELECT></TD></TR>";

	echo "</TABLE>";

	echo "<INPUT TYPE=SUBMIT Name='ShowPL' Value='"._("Show Statement of Profit and Loss")."'></CENTER>";

/*Now do the posting while the user is thinking about the period to select */

	include ("includes/GLPostings.inc");

} else {

	echo "<INPUT TYPE=HIDDEN NAME='FromPeriod' VALUE=" . $_POST["FromPeriod"] . "><INPUT TYPE=HIDDEN NAME='ToPeriod' VALUE=" . $_POST["ToPeriod"] . ">";

	$NumberOfMonths = $_POST["ToPeriod"] - $_POST["FromPeriod"] + 1;

	if ($NumberOfMonths >12){
		echo "<P>"._('A period up to 12 months in duration can be specified - the system automatically shows a comparative for the same period from the previous year - it cannot do this if a period of more than 12 months is specified. Please select an alternative period range');
		include("includes/footer.inc");
		exit;
	}

	$sql = "SELECT LastDate_in_Period FROM Periods WHERE PeriodNo=" . $_POST["ToPeriod"];
	$PrdResult = DB_query($sql, $db);
	$myrow = DB_fetch_row($PrdResult);
	$PeriodToDate = MonthAndYearFromSQLDate($myrow[0]);


	$SQL = "SELECT AccountGroups.SectionInAccounts, AccountGroups.GroupName,
			ChartDetails.AccountCode ,
			ChartMaster.AccountName,
			Sum(CASE WHEN ChartDetails.Period=" . $_POST['FromPeriod'] . " THEN ChartDetails.BFwd ELSE 0 END) AS FirstPrdBFwd,
			Sum(CASE WHEN ChartDetails.Period=" . $_POST['FromPeriod'] . " THEN ChartDetails.BFwdBudget ELSE 0 END) AS FirstPrdBudgetBFwd,
			Sum(CASE WHEN ChartDetails.Period=" . $_POST['ToPeriod'] . " THEN ChartDetails.BFwd + ChartDetails.Actual ELSE 0 END) AS LastPrdCFwd,
			Sum(CASE WHEN ChartDetails.Period=" . ($_POST['FromPeriod'] - 12) . " THEN ChartDetails.BFwd ELSE 0 END) AS LYFirstPrdBFwd,
			Sum(CASE WHEN ChartDetails.Period=" . ($_POST['ToPeriod']-12) . " THEN ChartDetails.BFwd + ChartDetails.Actual ELSE 0 END) AS LYLastPrdCFwd,
			Sum(CASE WHEN ChartDetails.Period=" . $_POST['ToPeriod'] . " THEN ChartDetails.BFwdBudget + ChartDetails.Budget ELSE 0 END) AS LastPrdBudgetCFwd
		FROM ChartMaster INNER JOIN AccountGroups
		ON ChartMaster.Group_ = AccountGroups.GroupName INNER JOIN ChartDetails
		ON ChartMaster.AccountCode= ChartDetails.AccountCode
		WHERE AccountGroups.PandL=1
		GROUP BY AccountGroups.GroupName,
			ChartDetails.AccountCode,
			ChartMaster.AccountName
		ORDER BY AccountGroups.SectionInAccounts, AccountGroups.SequenceInTB, ChartDetails.AccountCode";

	$AccountsResult = DB_query($SQL,$db,"<BR>"._("No general ledger accounts were returned by the SQL because"),_("The SQL that failed was:"));

	echo "<CENTER><FONT SIZE=4 COLOR=BLUE><B>" . _('Statement of Profit and Loss for the'). ' ' . $NumberOfMonths . ' ' . _("months to"). ' ' . $PeriodToDate . '</B></FONT><BR>';

	/*show a table of the accounts info returned by the SQL
	Account Code ,   Account Name , Month Actual, Month Budget, Period Actual, Period Budget */

	echo "<TABLE CELLPADDING=2>";

	if ($_POST['Detail']==_('Detailed')){
		$TableHeader = "<TR>
				<TD class='tableheader'>"._("Account")."</TD>
				<TD class='tableheader'>"._("Account Name")."</TD>
				<TD COLSPAN=2 class='tableheader'>"._("Period Actual")."</TD>
				<TD COLSPAN=2 class='tableheader'>"._("Period Budge")."t</TD>
				<TD COLSPAN=2 class='tableheader'>"._("Last Year")."</TD>
				</TR>";
	} else { /*summary */
		$TableHeader = "<TR>
				<TD COLSPAN=2 class='tableheader'></TD>
				<TD COLSPAN=2 class='tableheader'>"._("Period Actual")."</TD>
				<TD COLSPAN=2 class='tableheader'>"._("Period Budget")."</TD>
				<TD COLSPAN=2 class='tableheader'>"._("Last Year")."</TD>
				</TR>";
	}


	echo $TableHeader;
	$j = 1;
	$k=0; //row colour counter
	$Section="";
	$SectionPrdActual= 0;
	$SectionPrdLY 	 = 0;
	$SectionPrdBudget= 0;

	$ActGrp ="";
	$GrpPrdActual	= 0;
	$GrpPrdLY 	= 0;
	$GrpPrdBudget 	= 0;



	while ($myrow=DB_fetch_array($AccountsResult)) {

		if ($myrow["GroupName"]!= $ActGrp){

			if ($GrpActual+$GrpBudget+$GrpPrdActual+$GrpPrdBudget !=0){

				if ($_POST['Detail']=='Detailed'){
					echo "<TR>
						<TD COLSPAN=2></TD>
						<TD COLSPAN=6><HR></TD>
					</TR>";
					$ActGrpLable = $ActGrp . " total";
				} else {
					$ActGrpLable = $ActGrp;
				}

				if ($Section ==1){ /*Income */
					printf("<TR>
						<TD COLSPAN=2><FONT SIZE=2>%s "._("total")."</FONT></td>
						<TD></TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD></TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD></TD>
						<TD ALIGN=RIGHT>%s</TD>
						</TR>",
						$ActGrpLable,
						number_format(-$GrpPrdActual),
						number_format(-$GrpPrdBudget),
						number_format(-$GrpPrdLY));
				} else { /*Costs */
					printf("<TR>
						<TD COLSPAN=2><FONT SIZE=2>%s "._("total")."</FONT></td>
						<TD ALIGN=RIGHT>%s</TD>
						<TD></TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD></TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD></TD>
						</TR>",
						$ActGrpLable,
						number_format($GrpPrdActual),
						number_format($GrpPrdBudget),
						number_format($GrpPrdLY));
				}

			}
			$GrpPrdLY =0;
			$GrpPrdActual =0;
			$GrpPrdBudget =0;


			$j++;

		}

		if ($myrow["SectionInAccounts"]!= $Section){

			if ($SectionPrdLY+$SectionPrdActual+$SectionPrdBudget !=0){
				if ($Section==1) { /*Income*/

					echo "<TR>
						<TD COLSPAN=3></TD>
      						<TD><HR></TD>
						<TD></TD>
						<TD><HR></TD>
						<TD></TD>
						<TD><HR></TD>
					</TR>";

					printf("<TR>
					<TD COLSPAN=2><FONT SIZE=4>%s</FONT></td>
					<TD></TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD></TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD></TD>
					<TD ALIGN=RIGHT>%s</TD>
					</TR>",
					$Sections[$Section],
					number_format(-$SectionPrdActual),
					number_format(-$SectionPrdBudget),
					number_format(-$SectionPrdLY));
					$TotalIncome = -$SectionPrdActual;
					$TotalBudgetIncome = -$SectionPrdBudget;
					$TotalLYIncome = -$SectionPrdLY;
				} else {
					echo "<TR>
					<TD COLSPAN=2></TD>
      					<TD><HR></TD>
					<TD></TD>
					<TD><HR></TD>
					<TD></TD>
					<TD><HR></TD>
					</TR>";
					printf("<TR>
					<TD COLSPAN=2><FONT SIZE=4>%s</FONT></td>
					<TD></TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD></TD>
					<TD ALIGN=RIGHT>%s</TD>
					<TD></TD>
					<TD ALIGN=RIGHT>%s</TD>
					</TR>",
					$Sections[$Section],
					number_format($SectionPrdActual),
					number_format($SectionPrdBudget),
					number_format($SectionPrdLY));
				}
				if ($Section==2){ /*Cost of Sales - need sub total for Gross Profit*/
					echo "<TR>
						<TD COLSPAN=2></TD>
						<TD COLSPAN=6><HR></TD>
					</TR>";
					printf("<TR>
						<TD COLSPAN=2><FONT SIZE=4>"._("Gross Profit")."</FONT></td>
						<TD></TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD></TD>
						<TD ALIGN=RIGHT>%s</TD>
						<TD></TD>
						<TD ALIGN=RIGHT>%s</TD>
						</TR>",
					number_format($TotalIncome - $SectionPrdActual),
					number_format($TotalBudgetIncome - $SectionPrdBudget),
					number_format($TotalLYIncome - $SectionPrdLY));

					echo "<TR>
						<TD COLSPAN=2></TD>
						<TD COLSPAN=6><HR></TD>
					</TR>";
					printf("<TR>
						<TD COLSPAN=2><FONT SIZE=2><I>"._("Gross Profit Percent")."</I></FONT></td>
						<TD></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						<TD></TD>
						<TD ALIGN=RIGHT><I>%s</I></TD>
						</TR><TR><TD COLSPAN=6> </TD></TR>",
					number_format(100*($TotalIncome - $SectionPrdActual)/$TotalIncome,1) . "%",
					number_format(100*($TotalBudgetIncome - $SectionPrdBudget)/$TotalBudgetIncome,1) . "%",
					number_format(100*($TotalLYIncome - $SectionPrdLY)/$TotalLYIncome,1). "%");
					$j++;
				}
			}
			$SectionPrdLY =0;
			$SectionPrdActual =0;
			$SectionPrdBudget =0;

			$Section = $myrow["SectionInAccounts"];

			if ($_POST['Detail']=="Detailed"){
				printf("<TR>
					<td COLSPAN=6><FONT SIZE=4 COLOR=BLUE><B>%s</B></FONT></TD>
					</TR>",
					$Sections[$myrow["SectionInAccounts"]]);
			}
			$j++;

		}



		if ($myrow["GroupName"]!= $ActGrp){
			$ActGrp = $myrow["GroupName"];
			if ($_POST['Detail']=='Detailed'){
				printf("<TR>
					<td COLSPAN=6><FONT SIZE=2 COLOR=BLUE><B>%s</B></FONT></TD>
					</TR>",
					$myrow["GroupName"]);
			}
		}

		$AccountPeriodActual = $myrow["LastPrdCFwd"] - $myrow["FirstPrdBFwd"];
		$AccountPeriodLY = $myrow["LYLastPrdCFwd"] - $myrow["LYFirstPrdBFwd"];
		$AccountPeriodBudget = $myrow["LastPrdBudgetCFwd"] - $myrow["FirstPrdBudgetBFwd"];
		$PeriodProfitLoss += $AccountPeriodActual;
		$PeriodBudgetProfitLoss += $AccountPeriodBudget;
		$PeriodLYProfitLoss += $AccountPeriodLY;

		$GrpPrdLY +=$AccountPeriodLY;
		$GrpPrdActual +=$AccountPeriodActual;
		$GrpPrdBudget +=$AccountPeriodBudget;

		$SectionPrdLY +=$AccountPeriodLY;
		$SectionPrdActual +=$AccountPeriodActual;
		$SectionPrdBudget +=$AccountPeriodBudget;

		if ($_POST['Detail']==_('Detailed')){

			if ($k==1){
				echo "<tr bgcolor='#CCCCCC'>";
				$k=0;
			} else {
				echo "<tr bgcolor='#EEEEEE'>";
				$k++;
			}

			$ActEnquiryURL = "<A HREF='$rootpath/GLAccountInquiry.php?" . SID . "Period=" . $_POST["ToPeriod"] . "&Account=" . $myrow["AccountCode"] . "&Show=Yes'>" . $myrow["AccountCode"] . "<A>";

			if ($Section ==1){
				$PrintString = "<td>%s</td>
						<td>%s</td>
						<TD></TD>
						<td ALIGN=RIGHT>%s</td>
						<TD></TD>
						<td ALIGN=RIGHT>%s</td>
						<TD></TD>
						<td ALIGN=RIGHT>%s</td>
						</tr>";
			} else {
				$PrintString = "<td>%s</td>
						<td>%s</td>
						<td ALIGN=RIGHT>%s</td>
						<TD></TD>
						<td ALIGN=RIGHT>%s</td>
						<TD></TD>
						<td ALIGN=RIGHT>%s</td>
						<TD></TD>
						</tr>";
			}

			printf($PrintString,
				$ActEnquiryURL,
				$myrow["AccountName"],
				number_format($AccountPeriodActual),
				number_format($AccountPeriodBudget),
				number_format($AccountPeriodLY)
				);


			$j++;
			If ($j == 18){
				$j=1;
				echo $TableHeader;
			}
		}
	}
	//end of loop


	if ($GrpActual+$GrpBudget+$GrpPrdActual+$GrpPrdBudget !=0){

		if ($_POST['Detail']=='Detailed'){
			echo "<TR>
			<TD COLSPAN=2></TD>
			<TD COLSPAN=6><HR></TD>
			</TR>";
			$ActGrpLable = $ActGrp . " "._("total");
		} else {
			$ActGrpLable = $ActGrp;
		}

		if ($Section ==1){ /*Income */
			printf("<TR>
			<TD COLSPAN=2><FONT SIZE=2>%s "._("total")."</FONT></td>
			<TD></TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD></TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD></TD>
			<TD ALIGN=RIGHT>%s</TD>
			</TR>",
			$ActGrpLable,
			number_format(-$GrpPrdActual),
			number_format(-$GrpPrdBudget),
			number_format(-$GrpPrdLY));
		} else { /*Costs */
			printf("<TR>
				<TD COLSPAN=2><FONT SIZE=2>%s "._("total")."</FONT></td>
				<TD ALIGN=RIGHT>%s</TD>
				<TD></TD>
				<TD ALIGN=RIGHT>%s</TD>
				<TD></TD>
				<TD ALIGN=RIGHT>%s</TD>
				<TD></TD>
				</TR>",
				$ActGrpLable,
				number_format($GrpPrdActual),
				number_format($GrpPrdBudget),
				number_format($GrpPrdLY));
		}
	}

	if ($SectionPrdLY+$SectionPrdActual+$SectionPrdBudget !=0){

		if ($Section==1) { /*Income*/
			echo "<TR>
				<TD COLSPAN=2></TD>
				<TD></TD>
      				<TD><HR></TD>
				<TD></TD>
				<TD><HR></TD>
				<TD></TD>
				<TD><HR></TD>
				</TR>";
			printf("<TR>
			<TD COLSPAN=2><FONT SIZE=4>%s</FONT></td>
			<TD></TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD></TD>
			<TD ALIGN=RIGHT>%s</TD>
			<TD></TD>
			<TD ALIGN=RIGHT>%s</TD>
			</TR>",
			$Sections[$Section],
			number_format(-$SectionPrdActual),
			number_format(-$SectionPrdBudget),
			number_format(-$SectionPrdLY));
			$TotalIncome = -$SectionPrdActual;
			$TotalBudgetIncome = -$SectionPrdBudget;
			$TotalLYIncome = -$SectionPrdLY;
		} else {
			echo "<TR>
				<TD COLSPAN=2></TD>
      				<TD><HR></TD>
				<TD></TD>
				<TD><HR></TD>
				<TD></TD>
				<TD><HR></TD>
				</TR>";
			printf("<TR>
				<TD COLSPAN=2><FONT SIZE=4>%s</FONT></td>
				<TD></TD>
				<TD ALIGN=RIGHT>%s</TD>
				<TD></TD>
				<TD ALIGN=RIGHT>%s</TD>
				<TD></TD>
				<TD ALIGN=RIGHT>%s</TD>
				</TR>",
				$Sections[$Section],
				number_format($SectionPrdActual),
				number_format($SectionPrdBudget),
				number_format($SectionPrdLY));
		}
		if ($Section==2){ /*Cost of Sales - need sub total for Gross Profit*/
			echo "<TR>
				<TD COLSPAN=2></TD>
				<TD COLSPAN=6><HR></TD>
			</TR>";
			printf("<TR>
				<TD COLSPAN=2><FONT SIZE=4>"._("Gross Profit")."</FONT></td>
				<TD></TD>
				<TD ALIGN=RIGHT>%s</TD>
				<TD></TD>
				<TD ALIGN=RIGHT>%s</TD>
				<TD></TD>
				<TD ALIGN=RIGHT>%s</TD>
				</TR>",
			number_format($TotalIncome - $SectionPrdActual),
			number_format($TotalBudgetIncome - $SectionPrdBudget),
			number_format($TotalLYIncome - $SectionPrdLY));

			echo "<TR>
				<TD COLSPAN=2></TD>
				<TD COLSPAN=6><HR></TD>
			</TR>";
			printf("<TR>
				<TD COLSPAN=2><FONT SIZE=2><I>"._("Gross Profit Percent")."</I></FONT></td>
				<TD></TD>
				<TD ALIGN=RIGHT><I>%s</I></TD>
				<TD></TD>
				<TD ALIGN=RIGHT><I>%s</I></TD>
				<TD></TD>
				<TD ALIGN=RIGHT><I>%s</I></TD>
				</TR>",
			number_format(100*($TotalIncome - $SectionPrdActual)/$TotalIncome,1) . "%",
			number_format(100*($TotalBudgetIncome - $SectionPrdBudget)/$TotalBudgetIncome,1) . "%",
			number_format(100*($TotalLYIncome - $SectionPrdLY)/$TotalLYIncome,1). "%");
			$j++;
		}
	}

	if ($_POST['Detail']==_("Detailed")){
		printf("<TR>
			<td COLSPAN=6><FONT SIZE=4 COLOR=BLUE><B>%s</B></FONT></TD>
			</TR>",
			$Sections[$myrow["SectionInAccounts"]]);
	}



	echo "<TR>
		<TD COLSPAN=2></TD>
		<TD COLSPAN=6><HR></TD>
		</TR>";

	printf("<tr bgcolor='#ffffff'>
		<td COLSPAN=2><FONT SIZE=4 COLOR=BLUE><B>"._("Profit -Loss")."</B></FONT></td>
		<TD></TD>
		<td ALIGN=RIGHT>%s</td>
		<TD></TD>
		<td ALIGN=RIGHT>%s</td>
		<TD></TD>
		<td ALIGN=RIGHT>%s</td>
		</tr>",
		number_format(-$PeriodProfitLoss),
		number_format(-$PeriodBudgetProfitLoss),
		number_format(-$PeriodLYProfitLoss)
		);

	echo "<TR>
		<TD COLSPAN=2></TD>
		<TD COLSPAN=6><HR></TD>
		</TR>";

	echo "</TABLE>";
	echo "<INPUT TYPE=SUBMIT Name='SelectADifferentPeriod' Value='"._("Select A Different Period")."'></CENTER>";
}
echo "</form>";
include("includes/footer.inc");

?>
