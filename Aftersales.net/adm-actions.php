<?php
include("common.php");
include("includes/dblib.inc.php");
include("includes/funclib.inc.php");
$redirect_url=urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
$userinfo_row = checkAdmUser();

//::eksport deo
if($_GET['do']=="csveksport")
{
	header('Content-Type: application/octetstream');
	header('Content-Disposition: filename="akcije-export.csv"');

	$sep=";";
	$crlf="\r\n";
	$buffer="";
	
	$buffer="Sasija;SifraAkcije;SSL;ModelOpis;Dopis;TrajeOd;TrajeDo;DilerOdradio;BrZahteva;BrDilerZahteva;Aktivna;Odradjena$crlf";

	$sql="SELECT s.tVehChasNum,a.actionName,a.actionSSL,a.actionDesc,a.actionDopis,a.actionDateFrom,a.actionDateTo,s.tDilerOdr,s.tBrZahteva,s.tBrDilerZahteva,a.actionActive,s.tOdradjeno FROM tblaction a,tblactveh s WHERE s.tActionID=a.actionID";
  $result=$db->sql_query($sql);
  $res_row=$db->sql_fetchrowset($result);
  
  if($res_row)
  {
  	foreach($res_row as $row)
  	{
	  	$buffer.="$row[tVehChasNum]$sep$row[actionName]$sep$row[actionSSL]$sep$row[actionDesc]$sep$row[actionDopis]$sep$row[actionDateFrom]$sep$row[actionDateTo]$sep$row[tDilerOdr]$sep$row[tBrZahteva]$sep$row[tBrDilerZahteva]$sep$row[actionActive]$sep$row[tOdradjeno]$crlf";
	  }
	}
	else
	{
		$buffer.="Nema podataka.";
	}

	echo $buffer;
	exit;
}

?>
<html>
<head>
<title><? echo(HEADERBAR_TEXT);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link href="styles.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function setDateNow()
{
  var time=new Date();
  var godina=time.getYear();
  var mesec=time.getMonth()+1;
  var dan=time.getDate();
  var date_now="" + godina + '-' + mesec + '-' + dan;
  
  return(date_now)
}
//-->
</script>
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
                <td nowrap bgcolor="#f0f0f0" class="tekst_header">Korisnièki meni</td>
                <td rowspan="2" bgcolor="#f0f0f0" class="tekst_header"><img src="img/dot.gif" width="5" height="1"></td>
                <td class="tekst_header">Administracija servisnih akcija</td>
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
  
 if ( isset( $_POST['do'] ) && $_POST['do']=="update" )
 {
  $data=$_POST['updactsform'];
  $sql="UPDATE tblAction SET actionName='$data[name]',actionDesc='$data[desc]',actionDopis='$data[dopis]',actionDateFrom='$data[datefrom]', actionDateTo='$data[dateto]', actionSSL='$data[ssl]', actionActive='$data[active]' WHERE actionID='$data[actionid]'";
  $result=$db->sql_query($sql);
  if ( $result )
  {
   echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>Ažuriranje akcija</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>Uspešno<br><a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a><br><Br>
   	  </td>
   	 </tr>
   	 </table>";
  }
 }
 elseif ( $_POST['do']=="updatefile" && !empty($_FILES['actFile']['name']) )
 {
  $newActFile='download/' . $_POST[updactfileform][actionid] . '-' . $_FILES['actFile']['name'];
  move_uploaded_file($_FILES['actFile']['tmp_name'], $newActFile);
  $actFileLink=basename($newActFile);

  $data=$_POST[updactfileform];
  $sql="UPDATE tblAction SET actionFile='$actFileLink' WHERE actionID='$data[actionid]'";
  $result=$db->sql_query($sql);
  if ( $result )
  {
   echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>Ažuriranje uputstva akcije</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>Uspešno<br><a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a><br><Br>
   	  </td>
   	 </tr>
   	 </table>";
  }
 }
 elseif ( isset( $_POST['do'] ) && $_POST['do']=="updateactveh" )
 {
  $data=$_POST['actonvehform'];
  $sql="UPDATE tblActVeh SET tDopis1='$data[dopis1]',tDopis2='$data[dopis2]',tPogresnaAdr='$data[pogradr]', tNepoznatVlas='$data[nepvlas]',
  tNePrihvata='$data[neprih]',tOdradjeno='$data[odradjeno]',tDatum=CURDATE(),tBrZahteva='$data[brzahteva]', tStornirano='$data[stornirano]',
  tBrDilerZahteva='$data[brdzahteva]',tDiler='$data[odgdiler]', tDilerOdr='$data[dilerodr]' WHERE tActionID='$data[action]' AND tVehChasNum='$data[vehicle]'";
  $result=$db->sql_query($sql);
  if ( $result )
  {
   echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>Postavljanje izmena</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>Uspešno<br><a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a><br><Br>
   	  </td>
   	 </tr>
   	 </table>";
  }
 }
 elseif ( $_GET['do']=="editactveh" )
 {
 
 $action_id=$_GET['action_id'];
 $vehicle_id=$_GET['vehicle_id'];
 
 $action_row = getRow( "tblAction", "actionID", $action_id);
 $vehicle_row = getRow( "tblVehicle", "vehChasNum", $vehicle_id);
 
 $datum_od=explode("-", $action_row["actionDateFrom"]);
 $datum_do=explode("-", $action_row["actionDateTo"]);
 $adopis="<img src=\"img/btn_no.gif\" border=0 alt=\"Ne\">";
 if ( $action_row["actionDopis"] ) $adopis="<img src=\"img/btn_yes.gif\" border=0 alt=\"Da\">";
 $aktivna="<img src=\"img/btn_no.gif\" border=0 alt=\"Ne\">";
 if ( $action_row["actionActive"] ) $aktivna="<img src=\"img/btn_yes.gif\" border=0 alt=\"Da\">";
 $ezl=explode("-", $vehicle_row["vehEZL"]);
 
 echo "<table width=\"90%\" border=0 cellspacing=0 cellpadding=0 class=\"tekst\">
 	<tr>
 	 <td valign=top><b>Podaci o vozilu</b><br><br>
 	 <b>Broj šasije</b>: {$vehicle_row["vehChasNum"]}<br>
 	 <b>Komisioni broj</b>: {$vehicle_row["vehEngNum"]}<br>
 	 <b>Tip</b>: {$vehicle_row["vehType"]}<br>
 	 <b>EZL</b>: $ezl[2].$ezl[1].$ezl[0]<br>
 	 <b>Vlasnik</b>: {$vehicle_row["vehOwner"]}<br>
 	 <b>Adresa</b>: {$vehicle_row["vehOwnerAddress"]}<br>
 	 <b>Grad</b>: {$vehicle_row["vehOwnerAC"]} {$vehicle_row["vehOwnerCity"]}<br>
 	 <b>Telefon</b>: {$vehicle_row["vehOwnerTel"]}
 	 </td>
 	 <td valign=top><b>Podaci o akciji</b><br><br>
 	 <b>Akcija</b>: {$action_row["actionName"]}<br>
 	 <b>Traje od</b>: $datum_od[2].$datum_od[1].$datum_od[0] <b>Traje do</b>: $datum_do[2].$datum_do[1].$datum_do[0]<br>
 	 <b>Dopis</b>: $adopis<br>
 	 <b>Aktivna</b>: $aktivna<br>
 	 <b>SSL</b>: {$action_row["actionSSL"]}
 	 </td>
 	</tr>
 	</table><br><br>";

 $sql="SELECT * FROM tblActVeh WHERE tActionID='$action_id' AND tVehChasNum='$vehicle_id'";
 $result=$db->sql_query($sql);
 $actonveh_row=$db->sql_fetchrow($result);  

 $diler_row=getApprUsers();
 
 $dopis1="";
 if ( $actonveh_row["tDopis1"] ) $dopis1="checked";
 $dopis2="";
 if ( $actonveh_row["tDopis2"] ) $dopis2="checked";
 $dopis3="";
 if ( $actonveh_row["tDopis3"] ) $dopis3="checked";
 $pogresnaadr="";
 if ( $actonveh_row["tPogresnaAdr"] ) $pogresnaadr="checked";
 $nepoznatvlas="";
 if ( $actonveh_row["tNepoznatVlas"] ) $nepoznatvlas="checked";
 $neprihvata="";
 if ( $actonveh_row["tNePrihvata"] ) $neprihvata="checked";
 $odradjeno="";
 if ( $actonveh_row["tOdradjeno"] ) $odradjeno="checked";
 $stornirano="";
 if ( $actonveh_row["tStornirano"] ) $stornirano="checked";

 echo "<table width=\"90%\" border=0 cellspacing=0 cellpadding=0 class=\"tekst\">
 	<tr>
 	 <td>Postavi podatke o akciji za trenutno vozilo
 	 </td>
 	</tr>
 	<tr>
 	 <td><br><form name=\"actiononvehicle\" method=post action=\"adm-actions.php\">
	<input type=hidden name=\"do\" value=\"updateactveh\">
	<input type=hidden name=\"actonvehform[action]\" value=\"$action_id\">
	<input type=hidden name=\"actonvehform[dilerodr]\" value=\"$actonveh_row[tDilerOdr]\">
	<input type=hidden name=\"actonvehform[vehicle]\" value=\"$vehicle_id\">
	
	Odgovorni diler: <select name=\"actonvehform[odgdiler]\"><option value=\"\" $selected>Nema</option>";
	foreach( $diler_row as $row )
	{
	  $selected="";
	  if ( $row["dilerID"] == $actonveh_row["tDiler"] ) $selected="selected";
	  echo "<option value=\"{$row["dilerID"]}\" $selected>{$row["dilerID"]} - {$row["dilerName"]}</option>";
	}
	echo "</select><br>
	<input name=\"actonvehform[dopis1]\" type=checkbox $dopis1 value=1> Dopis 1 
	<input name=\"actonvehform[dopis2]\" type=checkbox $dopis2 value=1> Dopis 2<br>
	<input name=\"actonvehform[pogradr]\" type=checkbox $pogresnaadr value=1> Pogrešna adresa<br>
	<input name=\"actonvehform[nepvlas]\" type=checkbox $nepoznatvlas value=1> Nepoznat vlasnik<br>
	<input name=\"actonvehform[neprih]\" type=checkbox $neprihvata value=1> Neæe da prihvati<br>
	<input name=\"actonvehform[odradjeno]\" type=checkbox $odradjeno value=1> Odraðeno<br>
	<input name=\"actonvehform[stornirano]\" type=checkbox $stornirano value=1> Stornirano<br>
	Odradio diler: $actonveh_row[tDilerOdr]<br>
	Broj dilerskog zahteva: <input size=8 maxlength=6 name=\"actonvehform[brdzahteva]\" type=text value=\"{$actonveh_row["tBrDilerZahteva"]}\"><br>
	Broj zahteva: <input size=8 maxlength=6 name=\"actonvehform[brzahteva]\" type=text value=\"{$actonveh_row["tBrZahteva"]}\"><br>
	<input name=submit type=submit value=Postavi>
	</form> 	 
 	 </td>
 	</tr>
 	</table>";
}
elseif ( $_GET['do']=="edit" )
{
  $action_id=$_GET['action_id'];
  $action_row = getRow ( "tblAction", "actionID", $action_id );
  $datum_od=explode("-", $action_row["actionDateFrom"]);
  $datum_do=explode("-", $action_row["actionDateTo"]);
  $adopis="";
  if ( $action_row["actionDopis"] ) $adopis="checked";
  $aktivna="";
  if ( $action_row["actionActive"] ) $aktivna="checked";
  
  echo "<table width=\"80%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <form name=\"actionsupdate\" method=post action=\"adm-actions.php\">
  <input type=hidden name=\"do\" value=\"update\">
  <input type=hidden name=\"updactsform[actionid]\" value=\"$action_id\">
  <tr>
  <td colspan=2><b>ID akcije</b>: {$action_row["actionID"]}</td>
  </tr>
  <tr>
  <td width=\"20%\"><b>Šifra akcije</b>: </td><td><input size=30 name=\"updactsform[name]\" type=text value=\"{$action_row["actionName"]}\"></td>
  </tr>
  <tr>
  <td><b>Model/opis</b>: </td><td><input size=30 name=\"updactsform[desc]\" type=text value=\"{$action_row["actionDesc"]}\"></td>
  </tr>
  <tr>
  <td><b>Traje od</b>: </td><td><input size=10 name=\"updactsform[datefrom]\" type=text value=\"{$action_row["actionDateFrom"]}\"> <img onClick=\"document.actionsupdate.elements['updactsform[datefrom]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>format:GODINA-MESEC-DAN</i></td>
  </tr>
  <tr>
  <td><b>Traje do</b>: </td><td><input size=10 name=\"updactsform[dateto]\" type=text value=\"{$action_row["actionDateTo"]}\"> <img onClick=\"document.actionsupdate.elements['updactsform[dateto]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>format:GODINA-MESEC-DAN</i></td>
  </tr>
  <tr>
  <td><b>SSL</b>: </td><td><input size=10 name=\"updactsform[ssl]\" type=text value=\"{$action_row["actionSSL"]}\"></td>
  </tr>
  <tr>
  <td><b>Dopis?</b>: </td><td><input type=checkbox name=\"updactsform[dopis]\" value=1 $adopis></td>
  </tr>
  <tr>
  <td><b>Aktivna?</b>: </td><td><input type=checkbox name=\"updactsform[active]\" value=1 $aktivna></td>
  </tr>
  <tr><td colspan=2><input name=submit type=submit value=Postavi></td></tr></form>
  <form name=\"actionfileupdate\" method=post action=\"adm-actions.php\" enctype=\"multipart/form-data\">
  <input type=hidden name=\"do\" value=\"updatefile\">
  <input type=hidden name=\"updactfileform[actionid]\" value=\"$action_id\">
  <tr>
  <td colspan=2><br><b>Uputstvo za akciju</b>:<br><input type=\"file\" name=\"actFile\">
  <br><input name=submit type=submit value=\"Postavi fajl\"></td>
  </tr></form>
  </table>
  <br>
  <table width=\"95%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <tr>
   <td colspan=8 nowrap>&nbsp;Vozila na koja se primenjuje akcija:</td>
  </tr>
  <tr bgcolor=\"#e4e4e4\">
   <td colspan=8 nowrap>&nbsp;[ <a class=\"linkz\" href=\"adm-vehicles.php?do=new&action_id=$action_id\">Dodaj novo vozilo za ovu akciju</a> ] [ <a class=\"linkz\" href=\"adm-vehicles.php?do=linkveh&action_id=$action_id\">Dodaj postojeæe vozilo za ovu akciju</a> ]</td>
  </tr>
  <tr bgcolor=\"#d1d1d1\"> 
   <td>&nbsp;<b>Broj šasije</b></td>
   <td>&nbsp;<b>Tip</b></td>
   <td>&nbsp;<b>Vlasnik</b></td>
   <td>&nbsp;<b>Adresa</b></td>
   <td>&nbsp;<b>Odg diler</b></td>
   <td align=center><b>Uraðena</b></td>
   <td>&nbsp;</td>
   <td>&nbsp;</td>
  </tr>";

  $sql="SELECT vehChasNum, vehEngNum, vehType, vehEZL, vehOwner, vehOwnerAddress, vehOwnerAC,vehOwnerCity, tDiler, tOdradjeno FROM tblActVeh, tblVehicle WHERE tvehChasNum=tblVehicle.vehChasNum AND tActionID='$action_id'";
  $result=$db->sql_query($sql);
  $vehicle_row=$db->sql_fetchrowset($result);
  if ( ! $vehicle_row )
  {
   echo "<tr><td colspan=8 nowrap>Nema vozila koja odgovaraju ovoj akciji</td></tr>";
  }
  else
  {
     foreach( $vehicle_row as $row )
     {
      $ezl=explode("-", $row["vehEZL"]);
      $odradjeno="<img src=\"img/btn_no.gif\" border=0 alt=\"Ne\">";
      if ( $row["tOdradjeno"] ) $odradjeno="<img src=\"img/btn_yes.gif\" border=0 alt=\"Da\">";
      echo "<tr bgcolor=\"#e4e4e4\">
     	 <td>&nbsp;{$row["vehChasNum"]}</td>
     	 <td>&nbsp;{$row["vehType"]}</td>
     	 <td>&nbsp;{$row["vehOwner"]}</td>
     	 <td>&nbsp;{$row["vehOwnerAddress"]}, {$row["vehOwnerAC"]} {$row["vehOwnerCity"]}</td>
     	 <td>&nbsp;{$row["tDiler"]}</td>
     	 <td align=center>$odradjeno</td>
     	 <td align=center><a href=\"adm-actions.php?do=editactveh&action_id=$action_id&vehicle_id={$row["vehChasNum"]}\"><img src=\"img/btn_edit.gif\" border=0 alt=\"Promeni\"></a></td> 
     	 <td align=center><a href=\"adm-actions.php?do=deleteactveh&action_id=$action_id&vehicle_id={$row["vehChasNum"]}\"><img src=\"img/btn_delete.gif\" border=0 alt=\"Izbriši\"></a></td> 
     	</tr>";
     }
  }
  echo "<tr bgcolor=\"#d1d1d1\"><td colspan=8>&nbsp;</td></tr></table>";
}
elseif ( $_GET['do']=="pretragaod" || $_POST['do']=="pretragaod" )
{
  $sql="SELECT DISTINCT dilerID,dilerName FROM tbldilers WHERE type='M' AND dilerID<>'55430' ORDER BY dilerName ASC";
 	$result=$db->sql_query($sql);
 	$diler_row=$db->sql_fetchrowset($result);
  
  echo "<table width=\"90%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <form name=\"actionsupdate\" method=post action=\"adm-actions.php\">
  <input type=hidden name=\"do\" value=\"pretragaod\">
  <input type=hidden name=\"pret\" value=\"ok\">
  <tr>
  <td width=\"20%\"><b>Diler</b>: </td><td><select name=\"showdiler\">";
	$diler_selected='';
  foreach( $diler_row as $row )
	{
	  if( isset( $_POST['showdiler'] ) && $_POST['showdiler'] == $row['dilerID'] ) $diler_selected='selected';
	  echo "<option value=\"{$row["dilerID"]}\" $diler_selected>{$row["dilerID"]} - {$row["dilerName"]}</option>";
	  $diler_selected='';
	}
  echo "</select></td>
  </tr>
  <tr>
  <td><b>Datum od</b>: </td><td><input size=10 name=\"searchform[datefrom]\" type=text> <img onClick=\"document.actionsupdate.elements['searchform[datefrom]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>format:GODINA-MESEC-DAN</i></td>
  </tr>
  <tr>
  <td><b>Datum do</b>: </td><td><input size=10 name=\"searchform[dateto]\" type=text> <img onClick=\"document.actionsupdate.elements['searchform[dateto]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>format:GODINA-MESEC-DAN</i></td>
  </tr>
  <tr><td colspan=2><br><input name=submit type=submit value=Pretraga></td></tr>
  </form></table><br>";
  
  if ( isset( $_POST['pret'] ) && $_POST['pret']=="ok" )
  {
  	$data=$_POST['searchform'];
  	$data[diler]=$_POST['showdiler'];
  	
  	echo "<table width=\"90%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  	<tr bgcolor=\"#d1d1d1\">
		<td align=right>&nbsp;<b>RB</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>Broj šasije</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>Šifra akcije</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>SSL</b>&nbsp;</td>
		<td>&nbsp;<b>Model/opis</b>&nbsp;</td>
		<td nowrap>&nbsp;<b>Datum</b>&nbsp;</td>
		</tr>";
				
				$sql="SELECT s.tVehChasNum,s.tDiler,s.tDatum,a.actionName,a.actionSSL,a.actionDesc FROM tblactveh s,tblaction a WHERE s.tOdradjeno=1 AND actionID=tActionID AND tDilerOdr='$data[diler]'";
				if($data[datefrom]!="")
				{
					$sql.=" AND s.tDatum>='$data[datefrom]'";
				}
				if($data[dateto]!="")
				{
					$sql.=" AND s.tDatum<='$data[dateto]'";
				}
				$sql.=" ORDER BY s.tDatum";
				$result=$db->sql_query($sql);
				$aktivni_row=$db->sql_fetchrowset($result);
				
				if($aktivni_row)
				{
				   $cntr=0;
				   foreach( $aktivni_row as $rowak )
				   {
				      $cntr++;
				      $datum_od=explode("-", $rowak["tDatum"]);
				      echo "<tr bgcolor=\"#e4e4e4\">
			    	       <td align=right>$cntr</td>
			    	       <td>&nbsp;$rowak[tVehChasNum]</td>
			    	       <td nowrap>&nbsp;$rowak[actionName]</td>
			    	       <td>&nbsp;$rowak[actionSSL]</td>
			    	       <td>&nbsp;$rowak[actionDesc]</td>
			    	       <td>&nbsp;$datum_od[2].$datum_od[1].$datum_od[0]&nbsp;</td>
			    	      </tr>";
			    	   }
				}
				else
				{
				   echo "<tr><td bgcolor=\"#e4e4e4\" colspan=6>&nbsp; Nema podataka koji odgovaraju pretrazi.</tr>";
				}
				
				echo "<tr><td bgcolor=\"#d1d1d1\" colspan=6>&nbsp;</tr></table>";
     }
}
elseif ( $_GET['do']=="new" || $_POST['do']=="new" )
{
  if ( isset( $_POST['new'] ) && $_POST['new']=="ok" )
  {
   $data=$_POST['updactsform'];
   $sql="INSERT INTO tblAction VALUES (null, '$data[name]', '$data[desc]', '$data[datefrom]', '$data[dateto]', '$data[ssl]', '$data[active]', null, '$data[dopis]')";
   $result=$db->sql_query($sql);
   if ( $result )
   {
    	$sql="INSERT INTO tbllog VALUES (null,'$_SESSION[userID]',now(),'".EDIT_ACTION_NEW."','" . $_SERVER['REMOTE_ADDR'] . "')";
    	$result=$db->sql_query($sql);

    	echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>Postavljanje nove servisne akcije</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>Uspešno<br><a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a><br><Br>
   	  </td>
   	 </tr>
   	 </table><br>";
   }
  }
  echo "<table width=\"80%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <form name=\"actionsupdate\" method=post action=\"adm-actions.php\">
  <input type=hidden name=\"do\" value=\"new\">
  <input type=hidden name=\"new\" value=\"ok\">
  <tr>
  <td width=\"20%\"><b>Šifra akcije</b>: </td><td><input size=30 name=\"updactsform[name]\" type=text></td>
  </tr>
  <tr>
  <td><b>Model/opis</b>: </td><td><input size=30 name=\"updactsform[desc]\" type=text></td>
  </tr>
  <tr>
  <td><b>Traje od</b>: </td><td><input size=10 name=\"updactsform[datefrom]\" type=text> <img onClick=\"document.actionsupdate.elements['updactsform[datefrom]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>format:GODINA-MESEC-DAN</i></td>
  </tr>
  <tr>
  <td><b>Traje do</b>: </td><td><input size=10 name=\"updactsform[dateto]\" type=text> <img onClick=\"document.actionsupdate.elements['updactsform[dateto]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>format:GODINA-MESEC-DAN</i></td>
  </tr>
  <tr>
  <td><b>SSL</b>: </td><td><input size=10 name=\"updactsform[ssl]\" type=text></td>
  </tr>
  <tr>
  <td><b>Dopis?</b>: </td><td><input type=checkbox name=\"updactsform[dopis]\" value=1></td>
  </tr>  
  <tr>
  <td><b>Aktivna?</b>: </td><td><input type=checkbox name=\"updactsform[active]\" value=1></td>
  </tr>  
  <tr><td colspan=2><br><input name=submit type=submit value=Postavi></td></tr>
  </form></table>";
 }
 elseif ( $_POST['do']=="csvimportact" || $_GET['do']=="csvimportact" )
 {
  if ( $_POST['import']=="ok" )
  {
   $result = CSVImport( $_POST['csvtabela'], AddSlashes($_FILES['csvFile']['tmp_name']) );

   if ( $result )
   {
    echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>CSV import vozila</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>Uspešan<br><a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a><br><Br>
   	  </td>
   	 </tr>
   	 </table><br>";
   }
  }

  echo "<table width=\"80%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <form name=\"wtermsupdate\" method=post action=\"adm-actions.php\" enctype=\"multipart/form-data\">
  <input type=hidden name=\"do\" value=\"csvimportact\">
  <input type=hidden name=\"import\" value=\"ok\">
  <input type=hidden name=\"csvtabela\" value=\"tblAction\">
  <tr><td><b>Fajl</b>: <input type=\"file\" name=\"csvFile\">
  <br><br><input name=submit type=submit value=Postavi></td>
  </tr>
  </form>
  </table>";
 }
 elseif ( $_POST['do']=="csvimportvehact" || $_GET['do']=="csvimportvehact" )
 {
  if ( $_POST['import']=="ok" )
  {
   $result = CSVImport( $_POST['csvtabela'], AddSlashes($_FILES['csvFile']['tmp_name']) );
   if ( $result )
   {
    echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>CSV import vozila</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>Uspešan<br><a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a><br><Br>
   	  </td>
   	 </tr>
   	 </table><br>";
   }
  }

  echo "<table width=\"80%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <form name=\"wtermsupdate\" method=post action=\"adm-actions.php\" enctype=\"multipart/form-data\">
  <input type=hidden name=\"do\" value=\"csvimportvehact\">
  <input type=hidden name=\"import\" value=\"ok\">
  <input type=hidden name=\"csvtabela\" value=\"tblActVeh\">
  <tr><td><b>Fajl</b>: <input type=\"file\" name=\"csvFile\">
  <br><br><input name=submit type=submit value=Postavi></td>
  </tr>
  </form>
  </table>";
 }
 elseif ( $_POST['do']=="delete" || $_GET['do']=="delete" )
 {
  if ( isset( $_POST['confirm'] ) && $_POST['confirm']=="Da" )
  {

  $sql="SELECT actionID,actionFile FROM tblAction WHERE actionID='$_POST[action_id]'";
  $result=$db->sql_query($sql);
  $action_file=$db->sql_fetchrow($result);
  
  $sql="DELETE FROM tblAction WHERE actionID='$_POST[action_id]'";
  $result=$db->sql_query($sql);

  if($result)
  {
  	$sql="INSERT INTO tbllog VALUES (null,'$_SESSION[userID]',now(),'".DELETE_ACTION."','" . $_SERVER['REMOTE_ADDR'] . "')";
	$result=$db->sql_query($sql);

  	if($action_file) unlink("download/$action_file[actionFile]");
  	
  	$sql="DELETE FROM tblactveh WHERE tActionID='$_POST[action_id]'";
  	$result=$db->sql_query($sql);
  }
  if ( $result )
  {
   echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>Brisanje akcije</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>Uspešno<br><a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a><br><Br>
   	  </td>
   	 </tr>
   	 </table>";
  }
  }
  elseif ( !isset($_POST['confirm']))
  {
  	echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>Potvrdite brisanje podataka</b>
   	  </td>
   	 </tr>
   	 <tr><form name=\"confirm\" method=post action=\"adm-actions.php\">
   	 <input type=hidden name=\"do\" value=\"delete\">
   	 <input type=hidden name=\"action_id\" value=\"{$_GET['action_id']}\">
   	  <td align=center class=\"tekst\"><br>
   	  <input type=submit name=confirm value=Da>
   	  <input type=submit name=confirm value=Ne>
   	  <br><br>
   	  </td>
   	 </tr>
   	 </table>";
  }
  else
  {
  	echo "<a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a>";
  }
 }
 elseif ( $_GET['do']=="deleteactveh" || $_POST['do']=="deleteactveh" )
 {
  if ( isset( $_POST['confirm'] ) && $_POST['confirm']=="Da" )
  {
  	$sql="DELETE FROM tblActVeh WHERE tVehChasNum='$_POST[vehicle_id]' AND tActionID='$_POST[action_id]'";
  	$result=$db->sql_query($sql);
  	if ( $result )
  	{
   		$sql="INSERT INTO tbllog VALUES (null,'$_SESSION[userID]',now(),'".DELETE_ACTION_VEHICLE."','" . $_SERVER['REMOTE_ADDR'] . "')";
		$result=$db->sql_query($sql);

   		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 	<tr bgcolor=\"#527bb9\">
   	  	<td align=center class=\"tekst_beli\"><b>Brisanje vozila u akciji</b>
   	  	</td>
   	 	</tr>
   	 	<tr>
   	  	<td align=center class=\"tekst\"><br>Uspešno<br><a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a><br><Br>
   	  	</td>
   	 	</tr>
   	 	</table>";
  	}
  }
  elseif ( !isset($_POST['confirm']))
  {
  	echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>Potvrdite brisanje podataka</b>
   	  </td>
   	 </tr>
   	 <tr><form name=\"confirm\" method=post action=\"adm-actions.php\">
   	 <input type=hidden name=\"do\" value=\"deleteactveh\">
   	 <input type=hidden name=\"action_id\" value=\"{$_GET['action_id']}\">
   	 <input type=hidden name=\"vehicle_id\" value=\"{$_GET['vehicle_id']}\">
   	  <td align=center class=\"tekst\"><br>
   	  <input type=submit name=confirm value=Da>
   	  <input type=submit name=confirm value=Ne>
   	  <br><br>
   	  </td>
   	 </tr>
   	 </table>";
  }
  else
  {
  	echo "<a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju akcija</a>";
  }
 }
 elseif ( $_GET['do']=="edittext" )
 {
  $sql="SELECT * FROM tblobukauslovi WHERE id='akctekst'";
  $result=$db->sql_query($sql);
  $tekst_row=$db->sql_fetchrow($result);

  echo "<table width=\"90%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <form name=\"update\" method=post action=\"adm-actions.php\">
  <input type=hidden name=\"do\" value=\"updatetext\">
  <tr>
  <td><b>Tekst akcija</b>:<br><textarea name=\"updform[tekst]\" cols=80 rows=10>$tekst_row[tekst]</textarea>
  <br><br><input name=submit type=submit value=Postavi></td>
  </tr>
  </form>
  </table>";
 }
 elseif ( $_POST['do']=="updatetext" )
 {
  $updform=$_POST['updform'];
  $sql="UPDATE tblobukauslovi SET tekst='$updform[tekst]' WHERE id='akctekst'";
  $result=$db->sql_query($sql);
  if ( $result )
  {
   echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>Ažuriranje teksta servisnih akcija</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>Uspešno<br><a href=\"adm-actions.php\" class=\"linkz\">Nazad na administraciju servisnih akcija</a><br><Br>
   	  </td>
   	 </tr>
   	 </table>";
  }
 }
 else
 {
    $uslov='WHERE actionActive=1';
    if($_GET['do']=="showall") $uslov='WHERE actionActive<>1';
    
    $sql="SELECT * FROM tblAction $uslov ORDER BY actionDateFrom ASC";
    $result=$db->sql_query($sql);
    $actions_row=$db->sql_fetchrowset($result);

    echo "<table width=\"98%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
    	<tr><td bgcolor=\"#d1d1d1\" colspan=10>&nbsp;[ <a class=\"linkz\" href=\"adm-actions.php?do=new\">Postavi novu servisnu akciju</a> ] [ <a class=\"linkz\" href=\"adm-actions.php?do=showall\">Prikaži neaktivne</a> ] [ <a class=\"linkz\" href=\"adm-actions.php?do=pretragaod\">Pretraga akcija</a> ] [ <a class=\"linkz\" href=\"adm-actions.php?do=edittext\">Tekst akcija</a> ] <br> &nbsp;[ <a class=\"linkz\" href=\"adm-actions.php?do=csvimportact\">CSV import akcija</a> ] [ <a class=\"linkz\" href=\"adm-actions.php?do=csvimportvehact\">CSV import vozila u akcije</a> ] [ <a class=\"linkz\" href=\"adm-actions.php?do=csveksport\">CSV Eksport akcija</a> ]</tr>
    	<tr bgcolor=\"#e4e4e4\">
    	  <td align=right>&nbsp;<b>RB</b>&nbsp;</td>
    	  <td>&nbsp; <b>Šifra</b></td>
    	  <td>&nbsp; <b>SSL</b></td>
    	  <td>&nbsp; <b>Model/opis</b></td>
    	  <td align=center><b>Traje od</b></td>
    	  <td align=center><b>Traje do</b></td>
    	  <td align=center><b>Dop</b></td>
    	  <td align=center><b>Akt</b></td>
    	  <td><b>&nbsp;</b></td><td><b>&nbsp;</b></td>
    	</tr>";
    
    if ( ! $actions_row )
    {
     echo "<tr><td colspan=10>U bazi nema servisnih akcija</td></tr>";
    }
    
    $cntr=0;
    foreach( $actions_row as $row )
    {
     $cntr++;
     $datum_od=explode("-", $row["actionDateFrom"]);
     $datum_do=explode("-", $row["actionDateTo"]);
     $adopis="<img src=\"img/btn_no.gif\" border=0 alt=\"Ne\">";
     if ( $row["actionDopis"] ) $adopis="<img src=\"img/btn_yes.gif\" border=0 alt=\"Da\">";
     $aktivna="<img src=\"img/btn_no.gif\" border=0 alt=\"Ne\">";
     if ( $row["actionActive"] ) $aktivna="<img src=\"img/btn_yes.gif\" border=0 alt=\"Da\">";
     echo "<tr bgcolor=\"#e4e4e4\">
     	<td align=right>$cntr</td>
     	<td nowrap>&nbsp; {$row["actionName"]}</td>
     	<td>&nbsp; {$row["actionSSL"]}</td>
     	<td>&nbsp; {$row["actionDesc"]}</td>
     	<td align=center>$datum_od[2].$datum_od[1].$datum_od[0]</td>
     	<td align=center>$datum_do[2].$datum_do[1].$datum_do[0]</td>
     	<td align=center>$adopis</td>
     	<td align=center>$aktivna</td>
     	<td align=center><a class=\"linkz\" href=\"adm-actions.php?do=edit&action_id={$row["actionID"]}\"><img src=\"img/btn_edit.gif\" border=0 alt=\"Promeni\"></a></td>
     	<td align=center><a class=\"linkz\" href=\"adm-actions.php?do=delete&action_id={$row["actionID"]}\"><img src=\"img/btn_delete.gif\" border=0 alt=\"Izbriši\"></a></td>
     	</tr>";	
    }
    echo "<tr><td bgcolor=\"#d1d1d1\" colspan=10>&nbsp;</tr></table><br><a class=\"linkz\" href=\"adm-actions.php?do=showactive\">Prikaži neodraðene akcije</a><br><br>";
    
    if($_GET['do']=="showactive")
    {
			    	echo "<table width=\"90%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
			    	      <tr bgcolor=\"#d1d1d1\">
			    	       <td align=right>&nbsp;<b>RB</b>&nbsp;</td>
			    	       <td>&nbsp;<b>Broj šasije</b>&nbsp;</td>
			    	       <td>&nbsp;<b>Šifra akcije</b>&nbsp;</td>
			    	       <td>&nbsp;<b>Model/opis</b>&nbsp;</td>
			    	       <td align=center><b>Odgovorni diler</b></td>
			    	      </tr>";
				
				$sql="SELECT s.tVehChasNum,s.tDiler,a.actionName,a.actionDesc FROM tblactveh s,tblaction a WHERE s.tOdradjeno=0 OR ISNULL(s.tOdradjeno) AND actionID=tActionID ORDER BY s.tVehChasNum,a.actionName";
				$result=$db->sql_query($sql);
				$aktivni_row=$db->sql_fetchrowset($result);
				
				if($aktivni_row)
				{
				   $cntr=0;
				   foreach( $aktivni_row as $rowak )
				   {
				      $cntr++;
				      echo "<tr bgcolor=\"#e4e4e4\">
			    	       <td align=right>$cntr</td>
			    	       <td align=center>$rowak[tVehChasNum]</td>
			    	       <td align=center>$rowak[actionName]</td>
			    	       <td>&nbsp;$rowak[actionDesc]</td>
			    	       <td align=center>$rowak[tDiler]</td>
			    	      </tr>";
			    	   }
				}
				else
				{
				   echo "<tr><td bgcolor=\"#e4e4e4\" colspan=5>Nema neodraðenih akcija</tr>";
				}
				
				echo "<tr><td bgcolor=\"#d1d1d1\" colspan=5>&nbsp;</tr></table>";
     }
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