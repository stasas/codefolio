<?php
include("common.php");
include("includes/dblib.inc.php");
include("includes/funclib.inc.php");
$redirect_url=urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
$userinfo_row = checkUser();

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
                <td class="tekst_header">Informacije o vozilu</td>
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
if ( $_POST['do']=="search-cha" )
{
  $chasisno=$_POST['chasisno'];
  
 	$sql="SELECT DISTINCT actionActive, actionName, actionDesc, actionDopis, av.tVehChasNum, av.tActionID, av.tOdradjeno FROM tblActVeh av LEFT JOIN tblAction ON actionID=av.tActionID WHERE av.tVehChasNum='$chasisno'";
  $result=$db->sql_query($sql);
  $rezultati_row=$db->sql_fetchrowset($result);
  
  echo "<table width=\"95%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <tr bgcolor=\"#d1d1d1\"><td colspan=7>&nbsp; <b>Servisne akcije</b></td></tr>
	<tr bgcolor=\"#e4e4e4\">
		<td align=right>&nbsp;<b>RB</b>&nbsp;</td>
		<td>&nbsp; <b>Šasija</b></td>
		<td align=center>&nbsp; <b>Odrađena</b></td>
		<td>&nbsp; <b>Akcija</b></td>
		<td nowrap>&nbsp; <b>Model/opis</b></td>
		<td align=center>&nbsp;<b>Dop</b></td>
		<td align=center>&nbsp;<b>Akt</b></td>
	</tr>";
  
  if ($rezultati_row)
  {
         $cntr=0;
         foreach( $rezultati_row as $row )
         {
          $cntr++;
          $aktivna="ne";
          if ( $row["actionActive"] ) $aktivna="da";
          $adopis="ne";
          if ( $row["actionDopis"] ) $adopis="da";
          $odradjena="ne";
          $chasis="<a class=\"linkz\" href=\"updvehicle.php?action_id={$row["tActionID"]}&vehicle_id={$row["tVehChasNum"]}\">{$row["tVehChasNum"]}</a>";
          if ( $row["tOdradjeno"] )
          {
          	$odradjena="da";
          	$chasis=$row["tVehChasNum"];
          }
          if ( !$row["actionActive"] )
          {
          	$chasis=$row["tVehChasNum"];
          }
          echo "<tr bgcolor=\"#e4e4e4\">
          <td align=right>$cntr</td>
          <td>&nbsp; $chasis</td>
          <td align=center>&nbsp; $odradjena</td>
          <td>&nbsp; {$row["actionName"]}</td>
          <td>&nbsp; {$row["actionDesc"]}</td>
          <td align=center>$adopis</td>
          <td align=center>$aktivna</td>
  	  </tr>";
  	 }
  }
	else
	{
  	 echo "<tr bgcolor=\"#e4e4e4\"><td colspan=7>&nbsp; -</td></tr>";
  }
  echo "<tr bgcolor=\"#d1d1d1\"><td colspan=8>&nbsp;</td></tr></table><br>";

  //:: PROBLEMATICNI SLUCAJEVI
  echo "<table width=\"95%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <tr bgcolor=\"#d1d1d1\"><td colspan=7>&nbsp; <b>Problematični slučajevi</b></td></tr>
	<tr bgcolor=\"#e4e4e4\">
		<td align=center nowrap>&nbsp;<b>RB</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>Ime i prezime/Firma</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>Broj šasije</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>Datum</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>Diler</b>&nbsp;</td>
		<td width=\"100%\">&nbsp; <b>Razlog</b> &nbsp;</td>
	</tr>";
  
  $sql="SELECT tVehChasNum,tVehCNF,date,name,reason,dilerName FROM tblwarsterms LEFT JOIN tbldilers ON dilerID=tdilerid WHERE tVehChasNum='$chasisno'";
  $result=$db->sql_query($sql);
  $rezultati_row=$db->sql_fetchrowset($result);
  
  if ($rezultati_row)
  {
   $cntr=0;
   foreach( $rezultati_row as $row )
   {
  	$cntr++;
  	$tekst=nl2br($row["reason"]); 
  	$datum=datetimeSQL2YU($row["date"]);
  	echo "<tr bgcolor=\"#e4e4e4\">
  	<td align=center valign=top nowrap>&nbsp;$cntr&nbsp;</td>
  	<td valign=top nowrap>&nbsp;{$row["name"]}&nbsp;</td>
  	<td valign=top nowrap>&nbsp;{$row["tVehCNF"]} {$row["tVehChasNum"]}&nbsp;</td>
  	<td valign=top nowrap>&nbsp;$datum&nbsp;</td>
  	<td valign=top nowrap>&nbsp;{$row["dilerName"]}&nbsp;</td>
  	<td>$tekst</td></tr>";
   }
 	}
  else
  {
  	echo "<tr><td colspan=6 nowrap bgcolor=\"#e4e4e4\">&nbsp; -</td></tr>";
  }
  echo "<tr bgcolor=\"#d1d1d1\"><td colspan=6>&nbsp;</td></tr></table><br>";
  
  //:: AAS
  echo "<table width=\"95%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <tr bgcolor=\"#d1d1d1\"><td colspan=3>&nbsp; <b>AAS</b></td></tr>
	<tr bgcolor=\"#e4e4e4\">
		<td align=center nowrap>&nbsp;<b>RB</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>Firma</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>Broj šasije</b>&nbsp;</td>
	</tr>";
  
  $sql="SELECT * FROM tblaas WHERE tVehChasNum='$chasisno'";
  $result=$db->sql_query($sql);
  $rezultati_row=$db->sql_fetchrowset($result);
  
  if ($rezultati_row)
  {
   $cntr=0;
   foreach( $rezultati_row as $row )
   {
  	$cntr++;
  	echo "<tr bgcolor=\"#e4e4e4\">
  	<td align=center valign=top nowrap>&nbsp;$cntr&nbsp;</td>
  	<td valign=top nowrap>&nbsp;{$row["firma"]}&nbsp;</td>
  	<td valign=top nowrap>&nbsp;{$row["tVehCNF"]} {$row["tVehChasNum"]}&nbsp;</td>
	</tr>";
   }
 	}
  else
  {
  	echo "<tr><td colspan=3 nowrap bgcolor=\"#e4e4e4\">&nbsp; -</td></tr>";
  }
  echo "<tr bgcolor=\"#d1d1d1\"><td colspan=3>&nbsp;</td></tr></table><br>";
  
  //SU:speccond
  echo "<table width=\"95%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <tr bgcolor=\"#d1d1d1\"><td>&nbsp; <b>SU Modul: Specijalne kondicije</b></td></tr>";
	
	$sql="SELECT tVehChasNum FROM tblspeccond, tblsrspeccond WHERE tcond_id=cond_id AND tVehChasNum='$chasisno'";
  $result=$db->sql_query($sql);
  $rezultati_row=$db->sql_fetchrowset($result);
	if ($rezultati_row)
  {
  	echo "<tr><td nowrap bgcolor=\"#e4e4e4\">&nbsp; Za vozilo postoje <a class=\"linkz\" href=\"speccond.php?do=search-cha&chasisno=$chasisno\">specijalne kondicije</a></td></tr>";
  }
	else
	{
  	echo "<tr><td nowrap bgcolor=\"#e4e4e4\">&nbsp; -</td></tr>";
  }
	echo "<tr bgcolor=\"#d1d1d1\"><td>&nbsp;</td></tr></table><br>";
	
	//SU:ISP
  echo "<table width=\"95%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <tr bgcolor=\"#d1d1d1\"><td>&nbsp; <b>SU Modul: ISP</b></td></tr>";
	
	$sql="SELECT tvehchasnum FROM tblispvozila v LEFT JOIN tblisp i ON id=id_isp WHERE tvehchasnum='$chasisno'";
  $result=$db->sql_query($sql);
  $rezultati_row=$db->sql_fetchrowset($result);
	if ($rezultati_row)
  {
  	echo "<tr><td nowrap bgcolor=\"#e4e4e4\">&nbsp; Za vozilo postoji <a class=\"linkz\" href=\"isp.php?do=search-cha&chasisno=$chasisno\">ISP</a></td></tr>";
  }
	else
	{
  	echo "<tr><td nowrap bgcolor=\"#e4e4e4\">&nbsp; -</td></tr>";
  }
	echo "<tr bgcolor=\"#d1d1d1\"><td>&nbsp;</td></tr></table><br>";
	
	//SU:UGOVORI
  echo "<table width=\"95%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <tr bgcolor=\"#d1d1d1\"><td>&nbsp; <b>SU Modul: Servisni ugovori</b></td></tr>";
	
	$sql="SELECT v.tvehchasnum FROM tblsugovor u,tblsuvozila v WHERE u.id=v.id_ugo AND v.tvehchasnum='$chasisno'";
  $result=$db->sql_query($sql);
  $rezultati_row=$db->sql_fetchrowset($result);
	if ($rezultati_row)
  {
  	echo "<tr><td nowrap bgcolor=\"#e4e4e4\">&nbsp; Za vozilo postoji <a class=\"linkz\" href=\"scontracts.php?do=search-cha&chasisno=$chasisno\">servisni ugovor</a></td></tr>";
  }
	else
	{
  	echo "<tr><td nowrap bgcolor=\"#e4e4e4\">&nbsp; -</td></tr>";
  }
	echo "<tr bgcolor=\"#d1d1d1\"><td>&nbsp;</td></tr></table><br>";
	
}
else
{
  
  echo "<br><table width=\"80%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst_crni\">
  	<tr>
  	<td>&nbsp;</td>
  	</tr>
  	<tr>
  	<td><b>Obavezno uneti kompletan broj šasije, bez WDB</b><br>Na primer: 2110261A226477</td>
  	</tr>
  	<tr>
  	<td>&nbsp;</td>
  	</tr>
  	<tr>
  	<td>Pretraga prema broju šasije</td>
  	</tr>
  	<tr>
  	<form name=\"searchbychasis\" method=post action=\"vehicle-info.php\">
  	<input type=hidden name=\"do\" value=\"search-cha\">
  	<td><b>Broj šasije</b>: <input size=20 name=\"chasisno\" type=text> <input name=submit type=submit value=\"Pretraži\"></td>
  	</form>
  	</tr>
  	</table>";
 }
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