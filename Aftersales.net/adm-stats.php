<?php
include("common.php");
include("includes/dblib.inc.php");
include("includes/funclib.inc.php");
$redirect_url=urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
$userinfo_row = checkAdmUser();
?>
<html>
<head>
<title><? echo(HEADERBAR_TEXT);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link href="styles.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#e4e4e4" alink="#ff6600" leftmargin="0" topmargin="0">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td><img src="img/dot.gif" width="1" height="75"></td>
        </tr>
        <tr> 
          <td> 
            <!--glavni meni begin-->
            <?php
		include( "nav.inc.php" );
	    ?>
            <!--glavni meni end-->
          </td>
        </tr>
        <tr> 
          <td><img src="img/header.jpg" width="1024" height="202"></td>
        </tr>
        <tr> 
          <td height="100%"> 
            <!--section begin -->
            <table width="100%" height="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF">
              <tr> 
                <td rowspan="2" bgcolor="#f0f0f0" class="tekst_header"><img src="img/dot.gif" width="5" height="1"></td>
                <td nowrap bgcolor="#f0f0f0" class="tekst_header">Korisnički meni</td>
                <td rowspan="2" bgcolor="#f0f0f0" class="tekst_header"><img src="img/dot.gif" width="5" height="1"></td>
                <td class="tekst_header">Statistike akcija</td>
              </tr>
              <tr> 
                <td align="right" valign="top" nowrap bgcolor="#f0f0f0" class="tekst_header"> 
                  <!--user meni begin-->
                  <?php
  		include( "usernav.inc.php" );
	    ?>
                  <!--user meni end-->
                </td>
                <td width="100%" height="100%" align="center" valign="top" class="tekst_header"> 
                  <!--glavni deo begin-->
                  <br> 
<?php

 $sql="SELECT DISTINCT dilerID,dilerName FROM tbldilers WHERE type='M' AND dilerID<>'55430' ORDER BY dilerName ASC";
 $result=$db->sql_query($sql);
 $diler_row=$db->sql_fetchrowset($result);
 
 echo "<table width=\"98%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
	<tr>
	<form name=\"showstats\" method=post action=\"adm-stats.php\">
	<td colspan=10 align=center>Statistika po dileru: <select name=\"showdiler\">
	<option value=\"svi\">Svi dileri</option>";
	$diler_selected='';
	foreach( $diler_row as $row )
	{
	  if( isset( $_POST['showdiler'] ) && $_POST['showdiler'] == $row['dilerID'] ) $diler_selected='selected';
	  echo "<option value=\"{$row["dilerID"]}\" $diler_selected>{$row["dilerID"]} - {$row["dilerName"]}</option>";
	  $diler_selected='';
	}
	echo "</select> Prikaži i neaktivne <input name=\"showneaktivne\" type=checkbox value=1><input name=submit type=submit value=Prikaži><br><br></td></form></tr>
	<tr bgcolor=\"#d1d1d1\">
	 <td align=right>&nbsp; <b>RB</b>&nbsp;</td>
	 <td>&nbsp; <b>Naziv akcije</b></td>
	 <td>&nbsp; <b>SSL</b></td>
	 <td>&nbsp; <b>Model/opis</b></td>
	 <td align=center colspan=2><b>Ukupno</b><br>[ 1 / % ]</td>
	 <td align=center colspan=2><b>Odrađeno</b><br>[ 1 / % ]</td>
	 <td align=center colspan=2><b>Preostalo</b><br>[ 1 / % ]</td>
	</tr>";

	$filter=' AND tblAction.actionActive=1 ';
	if ( isset( $_POST['showneaktivne'] ) && $_POST['showneaktivne'] == 1 )
	{
		$filter='';
	}

 // SQL za sve dilere
 $sql="SELECT DISTINCT tActionID,actionName,actionSSL,actionDesc,COUNT(tActionID) FROM tblActVeh,tblAction WHERE tblAction.actionID=tblActVeh.tActionID $filter GROUP BY tActionID ORDER BY actionDateFrom ASC";
 
 // + dodatak za filter prema akcija aktivna/nije
 
 if ( isset( $_POST['showdiler'] ) && $_POST['showdiler'] != 'svi' )
 {
	$dilerid=$_POST['showdiler'];
	$sql="SELECT DISTINCT tActionID,actionName,actionSSL,actionDesc,tDiler,COUNT(tActionID) FROM tblActVeh,tblAction WHERE tblAction.actionID=tblActVeh.tActionID AND tDilerOdr='$dilerid' $filter GROUP BY tActionID ORDER BY actionDateFrom ASC";
 }

 $result=$db->sql_query($sql);
 $stats_row=$db->sql_fetchrowset($result);

 if ( ! $stats_row )
 {
  echo "<tr><td colspan=10>U bazi nema podataka</td></tr>";
 }
 else
 {
    $all_ukupno=0;
    $all_odradjeno=0;
    $all_odradjenoproc=0;
    $all_preostalo=0;
    $all_preostaloproc=0;
    $cntr=0;
    foreach( $stats_row as $row )
    {
     	$cntr++;
     	$ukupno=$row["COUNT(tActionID)"];
     	
     	if($dilerid)
     	{
     		$sql="SELECT COUNT(*) as ukupno FROM tblActVeh WHERE tActionID='$row[tActionID]'";
     		$result=$db->sql_query($sql);
     		$ukupno_row=$db->sql_fetchrow($result);
     		$ukupno=$ukupno_row["ukupno"];
     	}
     	$akcija=$row["tActionID"];
     	$sql="SELECT COUNT(*) as done FROM tblActVeh WHERE tActionID='$row[tActionID]' AND tOdradjeno=1";
     	if($dilerid) $sql="SELECT COUNT(*) as done FROM tblActVeh WHERE tActionID='$row[tActionID]' AND tOdradjeno=1 AND tDilerOdr='$dilerid'";
     	$result=$db->sql_query($sql);
     	$statsdone_row=$db->sql_fetchrow($result);
     	$odradjeno=$statsdone_row[done];
     	
     	$odradjenoproc=round(($odradjeno/$ukupno)*100,2);
     	$preostalo=$ukupno-$odradjeno;
     	$preostaloproc=round(($preostalo/$ukupno)*100,2);
     	
     	if($dilerid)
     	{
     		$sql="SELECT COUNT(*) as done FROM tblActVeh WHERE tActionID='$row[tActionID]' AND tOdradjeno=1";
     		$result=$db->sql_query($sql);
     		$statstotdone_row=$db->sql_fetchrow($result);
     		$ukupno_odradjeno=$statstotdone_row[done];
				$preostalo=$ukupno-$ukupno_odradjeno;
				$preostaloproc=round(($preostalo/$ukupno)*100,2);
     	}
     	
     	
     	echo "<tr bgcolor=\"#e4e4e4\">
     		 <td align=right>$cntr&nbsp;</td>
     		 <td>&nbsp; <a class=\"linkz\" href=\"viewaction.php?action_id=$akcija\">{$row["actionName"]}</a></td>
     		 <td>&nbsp; {$row["actionSSL"]}</td>
     		 <td>&nbsp; {$row["actionDesc"]}</td>
     		 <td align=center>$ukupno</td>
     		 <td align=center>100</td>
     		 <td align=center>$odradjeno</td>
     		 <td align=center>$odradjenoproc</td>
     		 <td align=center>$preostalo</td>
     		 <td align=center>$preostaloproc</td>
     		</tr>";	
    	$all_ukupno=$all_ukupno+$ukupno;
    	$all_odradjeno=$all_odradjeno+$odradjeno;
    	$all_odradjenoproc=round(($all_odradjeno/$all_ukupno)*100,2);
    	$all_preostalo=$all_preostalo+$preostalo;
    	$all_preostaloproc=round(($all_preostalo/$all_ukupno)*100,2);
    }
    echo "<tr bgcolor=\"#d1d1d1\">
     	 <td colspan=4>&nbsp;<b>Ukupno</b></td>
     	 <td align=center>$all_ukupno</td>
     	 <td align=center>100</td>
     	 <td align=center>$all_odradjeno</td>
     	 <td align=center>$all_odradjenoproc</td>
     	 <td align=center>$all_preostalo</td>
     	 <td align=center>$all_preostaloproc</td>
     	</tr>";	
 }
 echo "<tr><td bgcolor=\"#d1d1d1\" colspan=10>&nbsp;</tr></table>";
?>
                  <!--glavni deo end-->
                </td>
              </tr>
            </table>
            <!--section end -->
          </td>
        </tr>
        <tr> 
          <td bgcolor="#d1d1d1"> 
            <!--mali meni begin-->
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class="tekst_meni">
              <tr> 
                <td><img src="img/dot.gif" width="10" height="1"></td>
                <td nowrap><a href="login.php?actionflag=logout" class="linkz">Log 
                  out</a> <img src="img/dot-w.gif" width="1" height="12" align="middle"> 
                  <a href="profile.php" class="linkz">Podešavanja</a></td>
                <td width="100%"><img src="img/dot.gif" width="1" height="1"></td>
                <td align="right" nowrap><? echo(FOOTERCOPY_TEXT);?></td>
                <td><img src="img/dot.gif" width="10" height="1"></td>
              </tr>
            </table>
            <!--mali meni end-->
          </td>
        </tr>
      </table></td>
    <td width="100%"><img src="img/dot.gif" width="1" height="1"></td>
  </tr>
</table>
</body>
</html>