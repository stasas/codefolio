<?php
include("common.php");
include("includes/dblib.inc.php");
include("includes/funclib.inc.php");
$redirect_url=urlencode($_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING']);
$userinfo_row = checkAdmUser();
?>
<html>
<head>
<title><? echo($lang['page_title']);?></title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
<link href="styles.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/JavaScript">
<!--
function setDateNow()
{
  var time=new Date();
  var godina=time.getFullYear();
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
          <td><img src="img/fastfurious.jpg" width="1024" height="160"></td>
        </tr>
        <tr> 
          <td height="100%"> 
            <!--section begin -->
            <table width="100%" height="100%" border="0" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF">
              <tr> 
                <td rowspan="2" bgcolor="#f0f0f0" class="tekst_header"><img src="img/dot.gif" width="5" height="1"></td>
                <td nowrap bgcolor="#f0f0f0" class="tekst_header"><? echo($lang['profile_usermenu']);?></td>
                <td rowspan="2" bgcolor="#f0f0f0" class="tekst_header"><img src="img/dot.gif" width="5" height="1"></td>
                <td class="tekst_header"><? echo($lang['menu_adm_training']);?></td>
              </tr>
              <tr> 
                <td align="right" valign="top" nowrap bgcolor="#f0f0f0" class="tekst_header"> 
                  <!--user meni begin-->
            <?php
  		include( "usernav.inc.php" );
	    ?>
            <!--user meni end--> </td>
                <td width="100%" height="100%" align="center" valign="top" class="tekst_header"> 
                  <!--glavni deo begin-->
                  <br><table width="98%" border=0 cellspacing=1 cellpadding=2 class="tekst">
                  <tr><td>
<?php

// adding new training
if ( $_GET['do']=="new" || $_POST['do']=="new" )
{
  $cat = ( isset($_POST['cat']) ) ? $_POST['cat'] : $_GET['cat'];
  if ( $_POST['new']=="ok" )
  {
	$newobukaform=$_POST['newobukaform'];
	$materijal = (empty($_FILES['obFile']['name'])) ? 0 : 1;
	$grupe=$newobukaform[grupa1].$newobukaform[grupa2].$newobukaform[grupa3];
	
	$sql="INSERT INTO obuka (id_cat,id_vrsta,naziv,kod,aktivna,program_p,program_t,program_l,program_e,grupa,opis,materijal) VALUES ($cat,'$newobukaform[id_vrsta]','$newobukaform[naziv]','$newobukaform[kod]',1,'$newobukaform[program_p]','$newobukaform[program_t]','$newobukaform[program_l]','$newobukaform[program_e]','$grupe','$newobukaform[opis]',$materijal)";
	$result=$db->sql_query($sql);
	
	if ( $result )
	{
		if ($materijal)
		{
			$sql="SELECT LAST_INSERT_ID()";
			$result=$db->sql_query($sql);
			$obuka_id=$db->sql_fetchrow($result);

			$newOBFile='download/obuka/mat_' . $obuka_id[0] .'.pdf';
			move_uploaded_file($_FILES['obFile']['tmp_name'], $newOBFile);
		}
	
		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\">
		<td align=center class=\"tekst_beli\"><b>{$lang['adm_training_new']}</b>
		</td>
		</tr>
		<tr>
		<td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a><br><Br>
		</td>
		</tr>
		</table><br>";
	}
  }
  
  $sql = "SELECT DISTINCT grupa AS value, concat(grupa, ' - ', SUBSTRING_INDEX(opis, '(',1)) AS descr FROM grupa_obuke";
  $result=$db->sql_query($sql);
  $grupe_row=$db->sql_fetchrowset($result);
  $grupe_select = gen_combo($grupe_row, '');
  
  $sql = "SELECT DISTINCT id AS value, opis AS descr FROM obuka_vrsta";
  $result=$db->sql_query($sql);
  $vrste_row=$db->sql_fetchrowset($result);
  $vrste_select = gen_combo($vrste_row, '');

  echo "<table width=\"100%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <form name=\"obukaupdate\" method=post action=\"adm-training.php\" enctype=\"multipart/form-data\">
  <input type=hidden name=\"do\" value=\"new\">
  <input type=hidden name=\"new\" value=\"ok\">
  <input type=hidden name=\"cat\" value=\"$cat\">
  <tr>
  <td width=\"25%\"><b>{$lang['adm_training_name']}</b>: </td><td><input size=30 name=\"newobukaform[naziv]\" type=text></td>
  </tr>
  <tr>
  <td><b>{$lang['adm_training_type']}</b>: </td><td><select name=\"newobukaform[id_vrsta]\">$vrste_select</select></td>
  </tr>
  <tr>
  <td><b>{$lang['adm_training_code']}</b>: </td><td><input size=30 name=\"newobukaform[kod]\" type=text></td>
  </tr>
  <tr>
  <td><b>{$lang['adm_training_program']}</b>: </td><td> <input type=checkbox name=\"newobukaform[program_p]\" value=1>{$lang['adm_training_pkw']} 
  <input type=checkbox name=\"newobukaform[program_t]\" value=1>{$lang['adm_training_trapo']} <input type=checkbox name=\"newobukaform[program_l]\" value=1>{$lang['adm_training_lkw']}
  <input type=checkbox name=\"newobukaform[program_e]\" value=1>{$lang['adm_training_omniplus']}</td>
  </tr>
  <tr>
    <td valign=top><b>{$lang['adm_training_group']}</b>: </td><td><select name=\"newobukaform[grupa1]\">$grupe_select</select><br>
	<select name=\"newobukaform[grupa2]\"><option value=\" \"></option>$grupe_select</select><br>
	<select name=\"newobukaform[grupa3]\"><option value=\" \"></option>$grupe_select</select><td>
  </tr>
  <tr>
  <td><b>{$lang['adm_training_material']}</b>: </td><td><input type=\"file\" name=\"obFile\"></td>
  </tr>
  <tr>
  <td valign=top><b>{$lang['adm_training_comment']}</b>: </td><td><textarea name=\"newobukaform[opis]\" cols=60 rows=10></textarea></td>
  </tr>
  <tr><td colspan=2><input name=submit type=submit value={$lang['general_submit']}></td></tr>
  </form></table>";
}
// adding new training term and signing up trainee
elseif ( $_POST['do']=="autoprijava" )
{
  $cat = ( isset($_POST['cat']) ) ? $_POST['cat'] : $_GET['cat'];
  $id_obuka = ( isset($_POST['id_obuka']) ) ? $_POST['id_obuka'] : $_GET['id_obuka'];

  $newterminform=$_POST['newterminform'];
	
	$sql="INSERT INTO obuka_termin (id_obuka,datum_od,datum_do,id_mesto,id_trener,kapacitet,aktivna) VALUES ($id_obuka,'$newterminform[datum_od]','$newterminform[datum_od]',4,23,0,0)";
	$result=$db->sql_query($sql);
	
	if ( $result)
	{
		$sql="SELECT LAST_INSERT_ID()";
		$result=$db->sql_query($sql);
		$termin_id=$db->sql_fetchrow($result);
		
		$sql="INSERT INTO termin_polaznik VALUES ('$newterminform[radnik]','$termin_id[0]',now())";
		$result=$db->sql_query($sql);
		
		if ( $result)
		{
			if($newterminform[rezultat]=="x" || $newterminform[rezultat]=="X")
			{
				$sql="INSERT INTO termin_test (id_radnik,id_termin,rezultat) VALUES ('$newterminform[radnik]','$termin_id[0]',NULL)";
			}
			else
			{
				$sql="INSERT INTO termin_test (id_radnik,id_termin,rezultat) VALUES ('$newterminform[radnik]','$termin_id[0]','$newterminform[rezultat]')";
			}
			$result=$db->sql_query($sql);
			
			if ( $result )
			{
				echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
				<tr bgcolor=\"#527bb9\">
				<td align=center class=\"tekst_beli\"><b>{$lang['adm_training_add_and_sign_up']}</b>
				</td>
				</tr>
				<tr>
				<td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?do=edit&id=$id_obuka&cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a><br><Br>
				</td>
				</tr>
				</table><br>";
			}
		}
	}
}
// adding new training term
elseif ( $_GET['do']=="newterm" || $_POST['do']=="newterm" )
{
  $cat = ( isset($_POST['cat']) ) ? $_POST['cat'] : $_GET['cat'];
  $id_obuka = ( isset($_POST['id_obuka']) ) ? $_POST['id_obuka'] : $_GET['id_obuka'];
  if ( $_POST['new']=="ok" )
  {
	$newterminform=$_POST['newterminform'];
	
	$sql="INSERT INTO obuka_termin (id_obuka,datum_od,datum_do,id_mesto,id_trener,kapacitet,aktivna) VALUES ($id_obuka,'$newterminform[datum_od]','$newterminform[datum_do]','$newterminform[id_mesto]','$newterminform[id_trener]','$newterminform[kapacitet]',1)";
	$result=$db->sql_query($sql);

	if ( $result )
	{	
		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\">
		<td align=center class=\"tekst_beli\"><b>{$lang['adm_training_term_new']}</b>
		</td>
		</tr>
		<tr>
		<td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?do=edit&id=$id_obuka&cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a><br><Br>
		</td>
		</tr>
		</table><br>";
	}
  }
  
  $sql = "SELECT DISTINCT id AS value, naziv AS descr FROM obuka_mesto";
  $result=$db->sql_query($sql);
  $mesto_row=$db->sql_fetchrowset($result);
  $mesto_select = gen_combo($mesto_row, '');
  
  $sql = "SELECT DISTINCT id AS value, imeprezime AS descr FROM trener";
  $result=$db->sql_query($sql);
  $trener_row=$db->sql_fetchrowset($result);
  $trener_select = gen_combo($trener_row, '');
  
  $sql="SELECT naziv FROM obuka WHERE id='$id_obuka'";
  $result=$db->sql_query($sql);
  $obuka_row=$db->sql_fetchrow($result);

  echo "<h1>$obuka_row[naziv]</h1><table width=\"100%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
  <form name=\"terminupdate\" method=post action=\"adm-training.php\" enctype=\"multipart/form-data\">
  <input type=hidden name=\"do\" value=\"newterm\">
  <input type=hidden name=\"new\" value=\"ok\">
  <input type=hidden name=\"cat\" value=\"$cat\">
  <input type=hidden name=\"id_obuka\" value=\"$id_obuka\">
  <tr>
  <td width=\"25%\"><b>{$lang['adm_training_date_from']}</b>: </td><td><input size=10 name=\"newterminform[datum_od]\" type=text> <img onClick=\"document.terminupdate.elements['newterminform[datum_od]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>{$lang['adm_training_date_format']}</i></td>
  </tr>
  <tr>
  <td><b>{$lang['adm_training_date_to']}</b>: </td><td><input size=10 name=\"newterminform[datum_do]\" type=text> <img onClick=\"document.terminupdate.elements['newterminform[datum_do]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>{$lang['adm_training_date_format']}</i></td>
  </tr>
  <tr>
    <td><b>{$lang['adm_training_location']}</b>: </td><td><select name=\"newterminform[id_mesto]\">$mesto_select</select></td>
  </tr>
  <tr>
    <td><b>{$lang['adm_training_trainer']}</b>: </td><td><select name=\"newterminform[id_trener]\">$trener_select</select></td>
  </tr>
  <tr>
  <td><b>{$lang['adm_training_capacity']}</b>: </td><td><input size=3 name=\"newterminform[kapacitet]\" type=text></td>
  </tr>
  <tr><td colspan=2><input name=submit type=submit value={$lang['general_submit']}></td></tr>
  </form></table>";
}
// editing training page text
elseif ( $_GET['do']=="editdesc" || $_POST['do']=="editdesc" )
{
  $cat = ( isset($_POST['cat']) ) ? $_POST['cat'] : $_GET['cat'];

  $sql="SELECT * FROM obuka_cat WHERE id='$cat'";
  $result=$db->sql_query($sql);
  $desc_row=$db->sql_fetchrow($result);
  
  if ( $_POST['update']=="ok" )
  {
	$upddescform=$_POST['upddescform'];
	
	$sql="UPDATE obuka_cat SET opis ='$upddescform[desc]' WHERE id='$cat'";
	$result=$db->sql_query($sql);

	if ( $result )
	{
		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\">
		<td align=center class=\"tekst_beli\"><b>{$lang['adm_training_text_update']}</b>
		</td>
		</tr>
		<tr>
		<td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a><br><Br>
		</td>
		</tr>
		</table><br>";
	}
  }
  elseif ( $_POST['update']=="file" )
  {
	$upddescform=$_POST['upddescform'];
	$newOBFile='download/plan/plan_' . $cat .'.pdf';
	move_uploaded_file($_FILES['obFile']['tmp_name'], $newOBFile);
	
	echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\">
		<td align=center class=\"tekst_beli\"><b>{$lang['adm_training_text_update']}</b>
		</td>
		</tr>
		<tr>
		<td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a><br><Br>
		</td>
		</tr>
		</table><br>";
  }
  else
  {
	echo "<form name=\"descupdate\" method=post action=\"adm-training.php\">
	<input type=hidden name=\"do\" value=\"editdesc\">
	<input type=hidden name=\"update\" value=\"ok\">
	<input type=hidden name=\"cat\" value=\"$cat\">
	<b>{$lang['adm_training_text']}</b>:<br><textarea name=\"upddescform[desc]\" cols=100 rows=10>$desc_row[opis]</textarea>
	<br><input name=submit type=submit value={$lang['general_submit']}>
	</form>
	<form name=\"descupdatefile\" method=post action=\"adm-training.php\" enctype=\"multipart/form-data\">
	<input type=hidden name=\"do\" value=\"editdesc\">
	<input type=hidden name=\"update\" value=\"file\">
	<input type=hidden name=\"cat\" value=\"$cat\">
	<b>{$lang['adm_training_plan']}</b>: <input type=\"file\" name=\"obFile\">
	<br><input name=submit type=submit value={$lang['general_submit']}>
	</form>";
  }
}
// edit training
elseif ( $_GET['do']=="edit" )
{
  $id=$_GET['id'];
  $cat=$_GET['cat'];
  $sql="SELECT * FROM obuka WHERE id='$id'";
  $result=$db->sql_query($sql);
  $obuka_row=$db->sql_fetchrow($result);
  
  if($obuka_row)
  {
    
	$sql = "SELECT DISTINCT grupa AS value, concat(grupa, ' - ', SUBSTRING_INDEX(opis, '(',1)) as descr FROM grupa_obuke";
	$result=$db->sql_query($sql);
 	$grupe_row=$db->sql_fetchrowset($result);
  	$grupe1_select = gen_combo($grupe_row, $obuka_row[grupa][0]);
	$grupe2_select = gen_combo($grupe_row, $obuka_row[grupa][1]);
	$grupe3_select = gen_combo($grupe_row, $obuka_row[grupa][2]);
	
	$sql = "SELECT DISTINCT id AS value, opis as descr FROM obuka_vrsta";
	$result=$db->sql_query($sql);
 	$vrste_row=$db->sql_fetchrowset($result);
  	$vrste_select = gen_combo($vrste_row, $obuka_row[id_vrsta]);
	
	$traje= ( $obuka_row[aktivna] ) ? 'checked' : '' ;
	
	$program_p= ( $obuka_row[program_p] ) ? 'checked' : '' ;
	$program_t= ( $obuka_row[program_t] ) ? 'checked' : '' ;
	$program_l= ( $obuka_row[program_l] ) ? 'checked' : '' ;
	$program_e= ( $obuka_row[program_e] ) ? 'checked' : '' ;

    echo "<table width=\"100%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
    <form name=\"obukaupdate\" method=post action=\"adm-training.php\" enctype=\"multipart/form-data\">
    <input type=hidden name=\"do\" value=\"update\">
	<input type=hidden name=\"cat\" value=\"$cat\">
    <input type=hidden name=\"newobukaform[id]\" value=\"$id\">
	<input type=hidden name=\"newobukaform[materijal]\" value=\"$obuka_row[materijal]\">
    <tr>
    <td width=\"20%\"><b>{$lang['adm_training_name']}</b>: </td><td><input size=50 name=\"newobukaform[naziv]\" type=text value=\"$obuka_row[naziv]\"></td>
    </tr>
	<tr>
    <td><b>{$lang['adm_training_type']}</b>: </td><td><select name=\"newobukaform[id_vrsta]\">$vrste_select</select></td>
    </tr>
	<tr>
    <td><b>{$lang['adm_training_code']}</b>: </td><td><input size=30 name=\"newobukaform[kod]\" type=text value=\"$obuka_row[kod]\"></td>
    </tr>
	<tr>
    <td><b>{$lang['adm_training_program']}</b>: </td><td><input name=\"newobukaform[program_p]\" type=checkbox $program_p value=1> {$lang['adm_training_pkw']}
	<input name=\"newobukaform[program_t]\" type=checkbox $program_t value=1> {$lang['adm_training_trapo']}
	<input name=\"newobukaform[program_l]\" type=checkbox $program_l value=1> {$lang['adm_training_lkw']}
	<input name=\"newobukaform[program_e]\" type=checkbox $program_e value=1> {$lang['adm_training_omniplus']}</td>
    </tr>
    <tr>
    <td valign=top><b>{$lang['adm_training_group']}</b>: </td><td><select name=\"newobukaform[grupa1]\">$grupe1_select</select><br>
	<select name=\"newobukaform[grupa2]\"><option value=\" \"></option>$grupe2_select</select><br>
	<select name=\"newobukaform[grupa3]\"><option value=\" \"></option>$grupe3_select</select></td>
    </tr>
    <tr>
    <td><b>{$lang['adm_training_material']}</b>: </td><td><input type=\"file\" name=\"obFile\"></td>
    </tr>
    <tr>
    <td valign=top><b>{$lang['adm_training_comment']}</b>: </td><td><textarea name=\"newobukaform[opis]\" cols=60 rows=10>$obuka_row[opis]</textarea></td>
    </tr>
    <tr><td colspan=2><br><input name=submit type=submit value={$lang['general_submit']}></td></tr>
    </form></table>";
    
    $sql="SELECT ot.id,ot.datum_od,ot.datum_do,ot.kapacitet,m.naziv AS mesto,t.imeprezime AS trener FROM obuka_termin ot LEFT JOIN obuka_mesto m ON m.id=ot.id_mesto LEFT JOIN trener t ON t.id=ot.id_trener WHERE ot.id_obuka='$id' ORDER BY ot.datum_od DESC";
    $result=$db->sql_query($sql);
    $termini_row=$db->sql_fetchrowset($result);
         
	 if( $obuka_row[id_vrsta] == 6 || $obuka_row[id_vrsta] == 7 )
	 {
		//automatski pravi termin, prijavljuje radnika i postavlja rezultat testa.		
		$sql = "SELECT DISTINCT r.id AS value, CONCAT(d.dilerName, ' - ', r.imeprezime) AS descr FROM radnik r LEFT JOIN tbldilers d ON d.dilerID=r.id_diler ORDER BY d.dilerID,r.imeprezime";
		$result=$db->sql_query($sql);
		$radnici_row=$db->sql_fetchrowset($result);
		$prijavi_select = gen_combo($radnici_row,'');
		
		echo "<br>
		<b>{$lang['adm_training_terms']}:</b><br>
		<table width=\"100%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst_crni\">
		<form name=\"formprijava\" method=post action=\"adm-training.php\">
		<tr bgcolor=\"#d1d1d1\"><td nowrap class=\"tekst\" colspan=6>";
		echo "&nbsp;{$lang['adm_training_term_signup_result']}: 
		<input size=10 name=\"newterminform[datum_od]\" type=text> <img onClick=\"document.formprijava.elements['newterminform[datum_od]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\">
		<select name=\"newterminform[radnik]\">$prijavi_select</select> 
		<input size=5 name=\"newterminform[rezultat]\" type=text>
		<input type=hidden name=\"do\" value=\"autoprijava\"><input type=hidden name=\"cat\" value=\"$cat\"><input type=hidden name=\"id_obuka\" value=\"$id\">
		<input name=submit type=submit value={$lang['general_submit']}></td></tr></form>";
	 }
	 else
	 {
		echo "<br>
		<b>{$lang['adm_training_terms']}:</b><br>
		<table width=\"100%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst_crni\">
		<tr bgcolor=\"#d1d1d1\"><td nowrap class=\"tekst\" colspan=6>";
		echo "&nbsp;[ <a class=\"linkz\" href=\"adm-training.php?do=newterm&id_obuka=$id&cat=$cat\">{$lang['adm_training_term_new']}</a> ]</td>";
		echo '</tr>';
	 }
	 echo "<tr bgcolor=\"#d1d1d1\">
     <td nowrap class=\"tekst_header\" align=center>{$lang['adm_training_date_from']}</td>
     <td nowrap class=\"tekst_header\" align=center>{$lang['adm_training_date_to']}</td>
	 <td nowrap class=\"tekst_header\" align=center>{$lang['adm_training_location']}</td>
	 <td nowrap class=\"tekst_header\" align=center>{$lang['adm_training_trainer']}</td>
     <td align=center>&nbsp;</td>
	 <td align=center>&nbsp;</td>
     </tr>";
     
     if(!$termini_row)
     {
     	echo "<tr bgcolor=\"#e4e4e4\"><td colspan=6>{$lang['adm_training_terms_empty']}</td></tr>";
     }
     else
     {
     	foreach($termini_row as $row)
     	{
     		$datum_od=datetimeSQL2YU($row[datum_od]);
			$datum_do=datetimeSQL2YU($row[datum_do]);
			$dmsg=urlencode($datum_od . ' - ' . $datum_do);

			echo "<tr class=\"lista_red\">
			<td align=center>$datum_od</td>
			<td align=center>$datum_do</td>
			<td align=center>$row[mesto]</td>
			<td align=center>$row[trener]</td>
			<td align=center><a class=\"linkz\" href=\"adm-training.php?do=editterm&id={$row[id]}&cat=$cat\"><img src=\"img/btn_edit.gif\" border=0 alt=\"Promeni\"></a></td>
			<td align=center><a class=\"linkz\" href=\"adm-training.php?do=deleteterm&id={$row[id]}&cat=$cat&dmsg=$dmsg\"><img src=\"img/btn_delete.gif\" border=0 alt=\"Izbriši\"></a></td>
			</tr>";
     	}
     }
     echo '<tr bgcolor="#d1d1d1"><td colspan=6>&nbsp;</td></tr></table>';
    
  }
  else
  {
  	echo "{$lang['adm_training_empty']}<br><br>
  	<a href=\"adm-training.php\" class=\"linkz\">{$lang['adm_training_back_to']}</a>";
  }
}
// edit training - submiting changes
elseif ( $_POST['do']=="update" )
{
  $newobukaform=$_POST['newobukaform'];
  $cat=$_POST['cat'];
  
  $grupe=$newobukaform[grupa1].$newobukaform[grupa2].$newobukaform[grupa3];

  if ( !empty($_FILES['obFile']['name']) )
  {
  	$newObukaFile='download/obuka/mat_' . $newobukaform[id] . '.pdf';
	move_uploaded_file($_FILES['obFile']['tmp_name'], $newObukaFile);
	$newobukaform[materijal]=1;
  }
  
  $sql="UPDATE obuka SET naziv='$newobukaform[naziv]',kod='$newobukaform[kod]',id_vrsta='$newobukaform[id_vrsta]',grupa='$grupe',program_p='$newobukaform[program_p]',program_t='$newobukaform[program_t]',program_l='$newobukaform[program_l]',program_e='$newobukaform[program_e]',opis='$newobukaform[opis]',materijal='$newobukaform[materijal]' WHERE id='$newobukaform[id]'";
  $result=$db->sql_query($sql);

  if ( $result )
  {
   echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>{$lang['adm_training_update']}</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?do=edit&id={$newobukaform[id]}&cat={$cat}\" class=\"linkz\">{$lang['adm_training_back_to']}</a><br><Br>
   	  </td>
   	 </tr>
   	 </table>";
  }
}
// signing up trainee for a term
elseif ( $_POST['do']=="prijaviradnika" )
{
	$id_termin=$_POST['id_termin'];
	$cat=$_POST['cat'];
	$id_radnik=$_POST['radnik'];
	$sql="INSERT INTO termin_polaznik VALUES ('$id_radnik','$id_termin',now())";
	$result=$db->sql_query($sql);

	if ( $result )
	{
		$sql="UPDATE obuka_termin SET kapacitet = kapacitet - 1 WHERE id='$id_termin' AND kapacitet > 0";
		$result=$db->sql_query($sql);
		
		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\">
		<td align=center class=\"tekst_beli\"><b>{$lang['adm_training_sign_up']}</b>
		</td>
		</tr>
		<tr>
		<td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?do=editterm&id=$id_termin&cat={$cat}\" class=\"linkz\">{$lang['adm_training_back_to_term']}</a><br><Br>
		</td>
		</tr>
		</table>";
	}
	
}
// edit term
elseif ( $_GET['do']=="editterm" )
{
  $id=$_GET['id'];
  $cat=$_GET['cat'];
  $sql="SELECT ot.*,o.naziv FROM obuka_termin ot LEFT JOIN obuka o ON o.id=ot.id_obuka WHERE ot.id='$id'";
  $result=$db->sql_query($sql);
  $termin_row=$db->sql_fetchrow($result);
  
  if($termin_row)
  {
	$sql = "SELECT DISTINCT id AS value, naziv AS descr FROM obuka_mesto";
	$result=$db->sql_query($sql);
	$mesto_row=$db->sql_fetchrowset($result);
	$mesto_select = gen_combo($mesto_row, $termin_row[id_mesto]);
  
	$sql = "SELECT DISTINCT id AS value, imeprezime AS descr FROM trener";
	$result=$db->sql_query($sql);
	$trener_row=$db->sql_fetchrowset($result);
	$trener_select = gen_combo($trener_row, $termin_row[id_trener]);
	
	$aktivna= ( $termin_row[aktivna] ) ? 'checked' : '' ;
	
    echo "<h1>{$lang['adm_training_term_details']}</h1>
	<h2>$termin_row[naziv]</h2>
	<table width=\"100%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst\">
    <form name=\"terminupdate\" method=post action=\"adm-training.php\">
    <input type=hidden name=\"do\" value=\"updateterm\">
	<input type=hidden name=\"cat\" value=\"$cat\">
    <input type=hidden name=\"newterminform[id]\" value=\"$id\">
	<input type=hidden name=\"newterminform[id_obuka]\" value=\"$termin_row[id_obuka]\">
    <tr>
    <td width=\"15%\"><b>{$lang['adm_training_date_from']}</b>: </td><td><input size=15 name=\"newterminform[datum_od]\" type=text value=\"$termin_row[datum_od]\"> <img onClick=\"document.terminupdate.elements['newterminform[datum_od]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>{$lang['adm_training_date_format']}</i></td>
    </tr>
	<tr>
    <td><b>{$lang['adm_training_date_to']}</b>: </td><td><input size=15 name=\"newterminform[datum_do]\" type=text value=\"$termin_row[datum_do]\"> <img onClick=\"document.terminupdate.elements['newterminform[datum_do]'].value=setDateNow()\"; src=\"img/btn_date.gif\" border=0 alt=\"Današnji datum\"> <i>{$lang['adm_training_date_format']}</i></td>
    </tr>
    <tr>
    <td><b>{$lang['adm_training_location']}</b>: </td><td><select name=\"newterminform[id_mesto]\">$mesto_select</select></td>
    </tr>
	<tr>
    <td><b>{$lang['adm_training_trainer']}</b>: </td><td><select name=\"newterminform[id_trener]\">$trener_select</select></td>
    </tr>
    <tr>
    <td><b>{$lang['adm_training_capacity']}</b>: </td><td><input size=3 name=\"newterminform[kapacitet]\" type=text value=\"$termin_row[kapacitet]\"></td>
    </tr>
	<tr>
	<td><b>{$lang['adm_training_term_active']}</b>: </td><td><input name=\"newterminform[aktivna]\" type=checkbox $aktivna value=1></td>
	</tr>
    <tr><td colspan=2><input name=submit type=submit value={$lang['general_submit']}></td></tr>
    </form></table>";
    
    $sql="SELECT r.imeprezime, r.id, tp.datum_prijave, r.id_diler, d.dilerName FROM termin_polaznik tp LEFT JOIN radnik r ON r.id=tp.id_radnik LEFT JOIN tbldilers d ON d.dilerID=r.id_diler WHERE tp.id_termin='$id' ORDER BY r.id_diler, r.imeprezime";
    $result=$db->sql_query($sql);
    $prijavljeni_row=$db->sql_fetchrowset($result);
	
	$sql = "SELECT DISTINCT r.id AS value, CONCAT(d.dilerName, ' - ', r.imeprezime) AS descr FROM radnik r LEFT JOIN tbldilers d ON d.dilerID=r.id_diler ORDER BY d.dilerID,r.imeprezime";
	$result=$db->sql_query($sql);
	$radnici_row=$db->sql_fetchrowset($result);
	$prijavi_select = gen_combo($radnici_row,'');
         
	 echo "<br>
     <b>{$lang['adm_training_signed_up']}:</b><br>
     <table width=\"100%\" border=0 cellspacing=1 cellpadding=0 class=\"tekst_crni\">
     <form name=\"formprijava\" method=post action=\"adm-training.php\">
	 <tr bgcolor=\"#d1d1d1\"><td nowrap class=\"tekst\" colspan=5>";
	 echo "&nbsp;{$lang['adm_training_sign_up_for']}: <select name=\"radnik\">$prijavi_select</select> <input name=submit type=submit value={$lang['general_submit']}>
	 <input type=hidden name=\"do\" value=\"prijaviradnika\"><input type=hidden name=\"cat\" value=\"$cat\"><input type=hidden name=\"id_termin\" value=\"$id\"></td>";
	 echo "</form></tr>
	 <tr bgcolor=\"#d1d1d1\">
     <td align=right nowrap class=\"tekst_header\">&nbsp;{$lang['adm_training_number']}&nbsp;</td>
	 <td nowrap class=\"tekst_header\">&nbsp;{$lang['adm_training_trainee_name']}</td>
     <td nowrap class=\"tekst_header\" align=center>{$lang['adm_training_trainee_workshop']}</td>
	 <td nowrap class=\"tekst_header\" align=center>{$lang['adm_training_signup_date']}</td>
	 <td align=center>&nbsp;</td>
     </tr>";
     
     if(!$prijavljeni_row)
     {
     	echo "<tr bgcolor=\"#e4e4e4\"><td colspan=5>{$lang['adm_training_signup_empty']}!</td></tr>";
     }
     else
     {
     	$cntr=0;
		foreach($prijavljeni_row as $row)
     	{
     		$cntr++;
			$datum_prijave=datetimeSQL2YU($row[datum_prijave]);
			$dmsg=urlencode($row[imeprezime]);

			echo "<tr class=\"lista_red\">
			<td align=right nowrap>&nbsp;$cntr&nbsp;</td>
			<td>&nbsp;<a class=\"linkz\" href=\"adm-trainee.php?do=edit&id={$row[id]}&dilerid={$row[id_diler]}\">$row[imeprezime]</a></td>
			<td align=center>$row[dilerName]</td>
			<td align=center>$datum_prijave</td>
			<td align=center><a class=\"linkz\" href=\"adm-training.php?do=deleteprij&id={$row[id]}&id_termin=$id&cat=$cat&dmsg=$dmsg\"><img src=\"img/btn_delete.gif\" border=0 alt=\"Izbriši\"></a></td>
			</tr>";
     	}
     }
     echo '<tr bgcolor="#d1d1d1"><td colspan=5>&nbsp;</td></tr></table>';
    
  }
  else
  {
  	echo "{$lang['adm_training_terms_empty']}<br><br>
  	<a href=\"adm-training.php?id=$id&cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a>";
  }
}
// edit term - submiting changes
elseif ( $_POST['do']=="updateterm" )
{
  $newterminform=$_POST['newterminform'];
  $cat=$_POST['cat'];

  $sql="UPDATE obuka_termin SET datum_od='$newterminform[datum_od]',datum_do='$newterminform[datum_do]',id_mesto='$newterminform[id_mesto]',id_trener='$newterminform[id_trener]',kapacitet='$newterminform[kapacitet]',aktivna='$newterminform[aktivna]' WHERE id='$newterminform[id]'";
  $result=$db->sql_query($sql);

  if ( $result )
  {
   echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>{$lang['adm_training_term_update']}</b>
   	  </td>
   	 </tr>
   	 <tr>
   	  <td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?do=edit&id={$newterminform[id_obuka]}&cat={$cat}\" class=\"linkz\">{$lang['adm_training_back_to']}</a><br><Br>
   	  </td>
   	 </tr>
   	 </table>";
  }
}
// deleting training
elseif ( $_GET['do']=="delete" || $_POST['do']=="delete" )
{
  $id=$_GET['id'];
  $dmsg=urldecode($_GET['dmsg']);
  $cat = ( isset($_POST['cat']) ) ? $_POST['cat'] : $_GET['cat'];
  if ( isset( $_POST['confirm'] ) && $_POST['confirm']==$lang['general_yes'] )
  {
  	$id=$_POST['id'];
  	$sql="DELETE FROM obuka WHERE id='$id'";
  	$result=$db->sql_query($sql);
  	
	// delete all terms for that training
  	if($result)
  	{
  		$sql="INSERT INTO tbllog VALUES (null,'$_SESSION[userID]',now(),'".DELETE_TECHTRAINING."','" . $_SERVER['REMOTE_ADDR'] . "')";
		$result=$db->sql_query($sql);
		
		$sql="SELECT id FROM obuka_termin WHERE id_obuka='$id'";
		$result=$db->sql_query($sql);
		$termini_row=$db->sql_fetchrowset($result);
  		foreach ( $termini_row as $row)
		{
			$sql="DELETE FROM obuka_termin WHERE id='$row[id]'";
			$result=$db->sql_query($sql);
		
			$sql="DELETE FROM termin_polaznik WHERE id_termin='$row[id]'";
			$result=$db->sql_query($sql);
		
			$sql="DELETE FROM termin_test WHERE id_termin='$row[id]'";
			$result=$db->sql_query($sql);
		}
  	}
  	if ( $result )
  	{
   		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 	<tr bgcolor=\"#527bb9\">
   	  	<td align=center class=\"tekst_beli\"><b>{$lang['adm_training_delete']}</b>
   	  	</td>
   	 	</tr>
   	 	<tr>
   	  	<td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a><br><Br>
   	  	</td>
   	 	</tr>
   	 	</table>";
  	}
  }
  elseif ( !isset($_POST['confirm']))
  {
  	echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>{$lang['general_confirm_delete']}</b>
   	  </td>
   	 </tr>
   	 <tr><form name=\"confirm\" method=post action=\"adm-training.php\">
   	 <input type=hidden name=\"do\" value=\"delete\">
   	 <input type=hidden name=\"id\" value=\"$id\">
	 <input type=hidden name=\"cat\" value=\"$cat\">
   	  <td align=center class=\"tekst\"><br>
	  {$lang['adm_training_confirm_delete_text']} '$dmsg'? <br><br>
   	  <input type=submit name=confirm value={$lang['general_yes']}>
   	  <input type=submit name=confirm value={$lang['general_ne']}>
   	  <br><br>
   	  </td>
   	 </tr>
   	 </table>";
  }
  else
  {
  	echo "<a href=\"adm-training.php?cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a>";
  }
}
// deleting term from training
elseif ( $_GET['do']=="deleteterm" || $_POST['do']=="deleteterm" )
{
  $id= ( isset($_POST['id']) ) ? $_POST['id'] : $_GET['id'];
  $cat = ( isset($_POST['cat']) ) ? $_POST['cat'] : $_GET['cat'];
  $dmsg=urldecode($_GET['dmsg']);
  
  $sql="SELECT id_obuka FROM obuka_termin WHERE id='$id'";
  $result=$db->sql_query($sql);
  $obuka_row=$db->sql_fetchrow($result);
  
  $id_obuka=$obuka_row[id_obuka];
  
  if ( isset( $_POST['confirm'] ) && $_POST['confirm']==$lang['general_yes'] )
  {
  	$id=$_POST['id'];
  	$sql="DELETE FROM obuka_termin WHERE id='$id'";
  	$result=$db->sql_query($sql);
  	
	// deleting trainees attached to term, and results
  	if($result)
  	{
  		$sql="INSERT INTO tbllog VALUES (null,'$_SESSION[userID]',now(),'".DELETE_TECHTRAINING."','" . $_SERVER['REMOTE_ADDR'] . "')";
		$result=$db->sql_query($sql);
  		
  		$sql="DELETE FROM termin_polaznik WHERE id_termin='$id'";
  		$result=$db->sql_query($sql);
		
		$sql="DELETE FROM termin_test WHERE id_termin='$id'";
  		$result=$db->sql_query($sql);
  	}
  	if ( $result )
  	{
   		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 	<tr bgcolor=\"#527bb9\">
   	  	<td align=center class=\"tekst_beli\"><b>{$lang['adm_training_term_delete']}</b>
   	  	</td>
   	 	</tr>
   	 	<tr>
   	  	<td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?do=edit&id=$id_obuka&cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a><br><Br>
   	  	</td>
   	 	</tr>
   	 	</table>";
  	}
  }
  elseif ( !isset($_POST['confirm']))
  {
  	echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>{$lang['general_confirm_delete']}</b>
   	  </td>
   	 </tr>
   	 <tr><form name=\"confirm\" method=post action=\"adm-training.php\">
   	 <input type=hidden name=\"do\" value=\"deleteterm\">
   	 <input type=hidden name=\"id\" value=\"$id\">
	 <input type=hidden name=\"cat\" value=\"$cat\">
   	  <td align=center class=\"tekst\"><br>
	  {$lang['adm_training_term_confirm_delete_text']} '$dmsg'? <br><br>
   	  <input type=submit name=confirm value={$lang['general_yes']}>
   	  <input type=submit name=confirm value={$lang['general_ne']}>
   	  <br><br>
   	  </td>
   	 </tr>
   	 </table>";
  }
  else
  {
  	echo "<a href=\"adm-training.php?do=edit&id=$id_obuka&cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to']}</a>";
  }
}
// delete trainee sign-ups
elseif ( $_GET['do']=="deleteprij" || $_POST['do']=="deleteprij" )
{
  $dmsg=urldecode($_GET['dmsg']);
  $id=$_GET['id']; 
  $cat = ( isset($_POST['cat']) ) ? $_POST['cat'] : $_GET['cat'];
  $id_termin = ( isset($_POST['id_termin']) ) ? $_POST['id_termin'] : $_GET['id_termin'];
  if ( isset( $_POST['confirm'] ) && $_POST['confirm']==$lang['general_yes'] )
  {
  	$id=$_POST['id'];
  	$sql="DELETE FROM termin_polaznik WHERE id_termin='$id_termin' AND id_radnik='$id' LIMIT 1";
  	$result=$db->sql_query($sql);
  	
  	if ( $result )
  	{
   		$sql="INSERT INTO tbllog VALUES (null,'$_SESSION[userID]',now(),'".DELETE_TECHTRAINING_PERSON."','" . $_SERVER['REMOTE_ADDR'] . "')";
		$result=$db->sql_query($sql);
   		
   		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 	<tr bgcolor=\"#527bb9\">
   	  	<td align=center class=\"tekst_beli\"><b>{$lang['adm_training_signedup_delete']}</b>
   	  	</td>
   	 	</tr>
   	 	<tr>
   	  	<td align=center class=\"tekst\"><br>{$lang['adm_training_success']}<br><a href=\"adm-training.php?do=editterm&id=$id_termin&cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to_term']}</a><br><Br>
   	  	</td>
   	 	</tr>
   	 	</table>";
  	}
  }
  elseif ( !isset($_POST['confirm']))
  {
  	echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
   	 <tr bgcolor=\"#527bb9\">
   	  <td align=center class=\"tekst_beli\"><b>{$lang['general_confirm_delete']}</b>
   	  </td>
   	 </tr>
   	 <tr><form name=\"confirm\" method=post action=\"adm-training.php\">
   	 <input type=hidden name=\"do\" value=\"deleteprij\">
   	 <input type=hidden name=\"id\" value=\"$id\">
	 <input type=hidden name=\"id_termin\" value=\"$id_termin\">
	 <input type=hidden name=\"cat\" value=\"$cat\">
   	  <td align=center class=\"tekst\"><br>
{$lang['adm_training_term_signedup_confirm_delete_text1']} '$dmsg' {$lang['adm_training_term_signedup_confirm_delete_text2']}? <br><br>
   	  <input type=submit name=confirm value={$lang['general_yes']}>
   	  <input type=submit name=confirm value={$lang['general_ne']}>
   	  <br><br>
   	  </td>
   	 </tr>
   	 </table>";
  }
  else
  {
  	echo "<a href=\"adm-training.php?do=editterm&id=$id_termin&cat=$cat\" class=\"linkz\">{$lang['adm_training_back_to_term']}</a>";
  }
}
else
{
  $cat=$_GET['cat'];
  
  $sql='SELECT DISTINCT LEFT(datum_od,4) AS value, LEFT(datum_od,4) AS descr FROM obuka_termin ORDER BY datum_od DESC';
  $result=$db->sql_query($sql);
  $godine_row=$db->sql_fetchrowset($result);
 	
  $godine_select = gen_combo($godine_row, $_POST['search_godina']);

  $sql='SELECT DISTINCT LEFT(datum_od,4) AS godina FROM obuka_termin ORDER BY datum_od DESC LIMIT 1';
  $result=$db->sql_query($sql);
  $godina_row=$db->sql_fetchrow($result);
  
  $sql = "SELECT DISTINCT id AS value, opis AS descr FROM obuka_vrsta";
  $result=$db->sql_query($sql);
  $vrste_row=$db->sql_fetchrowset($result);
  $vrste_select = gen_combo($vrste_row, $_POST['search_vrsta']);
  
  $sql = "SELECT DISTINCT grupa AS value, concat(grupa, ' - ', SUBSTRING_INDEX(opis, '(',1)) AS descr FROM grupa_obuke";
  $result=$db->sql_query($sql);
  $grupe_row=$db->sql_fetchrowset($result);
  $grupe_select = gen_combo($grupe_row, $_POST['search_grupa']);
  
  echo '<table width="100%" border=0 cellspacing=1 cellpadding=0 class="tekst_crni">';
  echo "<tr><td nowrap><form name=\"searchbygodina\" method=post action=\"adm-training.php?cat={$cat}\">
  <input type=hidden name=\"do\" value=\"search-year\">{$lang['adm_training_filter_period']}: ({$lang['adm_training_filter_term']} <input type=checkbox name=\"search_godina_termin\" value=1>) <select name=\"search_godina\">$godine_select<option value=\"sve\">{$lang['adm_training_filter_period_all']}</option></select> <input name=submit type=submit value={$lang['general_show']}></form></td>
  <td width=\"80%\" nowrap><form name=\"searchbynaziv\" method=post action=\"adm-training.php?cat={$cat}\">&nbsp; <input type=hidden name=\"do\" value=\"search-name\">{$lang['adm_training_filter_name']}: <input size=15 name=\"search_name\" type=text> <input name=submit type=submit value={$lang['general_show']}></form></td></tr>
  <tr><td nowrap><form name=\"searchbyvrsta\" method=post action=\"adm-training.php?cat={$cat}\"><input type=hidden name=\"do\" value=\"search-vrsta\">{$lang['adm_training_filter_type']}: <select name=\"search_vrsta\">$vrste_select</select> <input name=submit type=submit value={$lang['general_show']}></form></td>
  <td nowrap><form name=\"searchbygrupa\" method=post action=\"adm-training.php?cat={$cat}\">&nbsp; <input type=hidden name=\"do\" value=\"search-grupa\">{$lang['adm_training_filter_group']}: <select name=\"search_grupa\">$grupe_select</select> <input name=submit type=submit value={$lang['general_show']}></form></td></tr>";
  echo '</table>';
  echo '<table width="100%" border=0 cellspacing=1 cellpadding=0 class="tekst_crni">
  <tr bgcolor="#d1d1d1"><td nowrap class="tekst" colspan=8>';
  echo "&nbsp;[ <a class=\"linkz\" href=\"adm-training.php?do=new&cat=$cat\">{$lang['adm_training_new']}</a> ] [ <a class=\"linkz\" href=\"adm-training.php?do=editdesc&cat=$cat\">{$lang['adm_training_update_text']}</a> ]</td>";
  echo "</tr>
  <tr bgcolor=\"#d1d1d1\">
  <td align=right nowrap class=\"tekst_header\">&nbsp;{$lang['adm_training_number']}&nbsp;</td>
  <td nowrap class=\"tekst_header\">&nbsp;{$lang['adm_training_name']}</td>
  <td class=\"tekst_header\">&nbsp;{$lang['adm_training_code']}</td>
  <td class=\"tekst_header\">&nbsp;{$lang['adm_training_type']}</td>
  <td align=center class=\"tekst_header\">&nbsp;{$lang['adm_training_program']}</td>
  <td align=center class=\"tekst_header\">&nbsp;{$lang['adm_training_group']}</td>
  <td align=center>&nbsp;</td>
  <td align=center>&nbsp;</td>
 </tr>";

 if( $_POST['search_godina']=='sve' )
 {
	$sql="SELECT o.id,o.naziv,o.kod,ov.opis,o.program_p,o.program_t,o.program_l,o.program_e,o.grupa FROM obuka o LEFT JOIN obuka_termin ot ON ot.id_obuka=o.id LEFT JOIN obuka_vrsta ov ON ov.id=o.id_vrsta WHERE o.id_cat='$cat' GROUP BY o.id ORDER BY o.naziv ASC, ot.datum_od DESC";
 }
 elseif( $_POST['do']=='search-name' && strlen(trim($_POST['search_name'])) > 0 )
 {
	$deo_naziva=$_POST['search_name'];
	$sql="SELECT o.id,o.naziv,o.kod,ov.opis,o.program_p,o.program_t,o.program_l,o.program_e,o.grupa FROM obuka o LEFT JOIN obuka_termin ot ON ot.id_obuka=o.id LEFT JOIN obuka_vrsta ov ON ov.id=o.id_vrsta WHERE o.naziv LIKE '%$deo_naziva%' AND o.id_cat='$cat' GROUP BY o.id ORDER BY o.naziv ASC, ot.datum_od DESC";
 }
 elseif( $_POST['do']=='search-vrsta' )
 {
	$vrsta=$_POST['search_vrsta'];
	$sql="SELECT o.id,o.naziv,o.kod,ov.opis,o.program_p,o.program_t,o.program_l,o.program_e,o.grupa FROM obuka o LEFT JOIN obuka_termin ot ON ot.id_obuka=o.id LEFT JOIN obuka_vrsta ov ON ov.id=o.id_vrsta WHERE o.id_vrsta = '$vrsta' AND o.id_cat='$cat' GROUP BY o.id ORDER BY o.naziv ASC, ot.datum_od DESC";
 }
 elseif( $_POST['do']=='search-grupa' )
 {
	$grupa=$_POST['search_grupa'];
	$sql="SELECT o.id,o.naziv,o.kod,ov.opis,o.program_p,o.program_t,o.program_l,o.program_e,o.grupa FROM obuka o LEFT JOIN obuka_termin ot ON ot.id_obuka=o.id LEFT JOIN obuka_vrsta ov ON ov.id=o.id_vrsta WHERE INSTR(o.grupa,'$grupa') > 0 AND o.id_cat='$cat' GROUP BY o.id ORDER BY o.naziv ASC, ot.datum_od DESC";
 }
 else
 {
 		if( $_POST['do']=='search-year' )
 		{
 			$sr_datum='' . $_POST['search_godina'] . '-00-00';
			$sr_datum_do='' . $_POST['search_godina'] + 1 . '-00-00';
 			$sql="SELECT o.id,o.naziv,o.kod,ov.opis,o.program_p,o.program_t,o.program_l,o.program_e,o.grupa FROM obuka o LEFT JOIN obuka_termin ot ON ot.id_obuka=o.id LEFT JOIN obuka_vrsta ov ON ov.id=o.id_vrsta WHERE (ot.datum_od >= '$sr_datum' AND ot.datum_od <= '$sr_datum_do' OR ot.datum_od IS NULL) AND o.id_cat='$cat' GROUP BY o.id ORDER BY o.naziv ASC, ot.datum_od DESC";
 		}
 		else
 		{
 			$sql="SELECT o.id,o.naziv,o.kod,ov.opis,o.program_p,o.program_t,o.program_l,o.program_e,o.grupa FROM obuka o LEFT JOIN obuka_termin ot ON ot.id_obuka=o.id LEFT JOIN obuka_vrsta ov ON ov.id=o.id_vrsta WHERE (ot.datum_od >= '$godina_row[godina]' OR ot.datum_od IS NULL) AND o.id_cat='$cat' GROUP BY o.id ORDER BY o.naziv ASC, ot.datum_od DESC";
 		}
 }
 $result=$db->sql_query($sql);
 $obuke_row=$db->sql_fetchrowset($result);

 if(!$obuke_row)
 {
	 echo "<tr bgcolor=\"#e4e4e4\"><td colspan=8 nowrap class=\"tekst_crni\">&nbsp; {$lang['adm_training_none']}</td></tr>";
 }
 else
 {
     $cntr=0;
	 foreach($obuke_row as $row)
     {
    	$program = '';
		$program = ($row[program_p]) ? 'PKW ' : '';
		$program = ($row[program_t]) ? $program . 'TRAPO ' : $program . '';
		$program = ($row[program_l]) ? $program . 'LKW ' : $program . '';
		$program = ($row[program_e]) ? $program . 'OMNIplus ' : $program . '';
		
		$dmsg=urlencode($row[naziv]);
		
		$cntr++;
		 echo "<tr class=\"lista_red\">
		 <td align=right nowrap>&nbsp;$cntr&nbsp;</td>
    	 <td nowrap>&nbsp;<a class=\"linkz\" href=\"adm-training.php?do=edit&id={$row[id]}&cat=$cat\">$row[naziv]</a></td>
		 <td>&nbsp;$row[kod]</td>
		 <td>&nbsp;$row[opis]</td>
    	 <td align=center>$program</td>
    	 <td align=center>$row[grupa]</td>
    	 <td align=center><a class=\"linkz\" href=\"adm-training.php?do=edit&id={$row[id]}&cat=$cat\"><img src=\"img/btn_edit.gif\" border=0 alt=\"Promeni\"></a></td>
    	 <td align=center><a class=\"linkz\" href=\"adm-training.php?do=delete&id={$row[id]}&cat=$cat&dmsg=$dmsg\"><img src=\"img/btn_delete.gif\" border=0 alt=\"Izbriši\"></a></td>
    	 </tr>";
		 
		 if( $_POST['do']=='search-year' && $_POST['search_godina_termin'] )
		 {
			$sql="SELECT ot.id,concat(date_format(ot.datum_od,'%d.%m.%Y'),' - ', date_format(ot.datum_do,'%d.%m.%Y')) AS termin,ot.kapacitet,m.naziv AS mesto,t.imeprezime AS trener FROM obuka_termin ot LEFT JOIN obuka_mesto m ON m.id=ot.id_mesto LEFT JOIN trener t ON t.id=ot.id_trener WHERE (ot.datum_od >= '$sr_datum' AND ot.datum_od <= '$sr_datum_do' OR ot.datum_od IS NULL) AND ot.id_obuka='$row[id]' ORDER BY ot.datum_od DESC";
			$result=$db->sql_query($sql);
			$termini_row=$db->sql_fetchrowset($result);
			foreach($termini_row as $t_row)
			{
				echo "<tr class=\"lista_red\">
				<td>&nbsp;</td>
				<td colspan=7>&nbsp;<a class=\"linkz\" href=\"adm-training.php?do=editterm&id={$t_row[id]}&cat=$cat\">$t_row[termin]</a></td>
				</tr>";
			}
		 }
     }
 }
 echo '<tr bgcolor="#d1d1d1"><td colspan=8>&nbsp;</td></tr></table>';
}
?>
                  </td></tr></table>
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
                <td nowrap><a href="login.php?actionflag=logout" class="linkz"><? echo($lang['profile_logout']);?></a> <img src="img/dot-w.gif" width="1" height="12" align="middle"> 
                  <a href="profile.php" class="linkz"><? echo($lang['profile_settings']);?></a></td>
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