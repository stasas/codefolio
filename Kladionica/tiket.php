<?php

define('IN_WEBBET',true);

$webbet_root_path = './';
include($webbet_root_path . 'common.php');

session_start();
session_register("webbet_session","parovi","igre");

$authorized=0;
$message="";

$authorized = authUser();

if(isset($action))
{
	if($action=="add")
	{
		if(!(isset($HTTP_SESSION_VARS['parovi']))) 
		{
			$HTTP_SESSION_VARS['parovi']=Array();
			$HTTP_SESSION_VARS['igre']=Array();
		}
		
		$brpar=count($HTTP_SESSION_VARS['parovi']);
		$novi_ok=true;
		
		for($i=0;$i<$brpar;$i++)
		{
			if($sifra==$parovi[$i]) $novi_ok=false;
		}
		
		if($novi_ok)
		{
			$HTTP_SESSION_VARS['parovi'][$brpar]=$sifra;
			$parovi[$brpar]=$sifra;
			$HTTP_SESSION_VARS['igre'][$brpar]=$igra;
			$igre[$brpar]=$igra;
		}
	}
	if($action=="remove")
	{
		$parovi[$sifra]=$parovi[count($parovi)-1];
		$igre[$sifra]=$igre[count($igre)-1];
		unset($parovi[count($parovi)-1]);
		unset($igre[count($igre)-1]);
	}
	if($action=="Ponisti")
	{
		$i=count($parovi)-1;
		while($i>=0)
		{
			unset($parovi[$i]);
			unset($igre[$i]);
			$i--;
		}
		unset($HTTP_SESSION_VARS['parovi']);
		unset($HTTP_SESSION_VARS['igre']);
	}
	if($action=="postavi")
	{
		// slanje tiketa
		
		$postavi_msg='';
		
		//provera minimalnog/maksimalnog broja parova
		$postavi_msg = (count($parovi)>0 && count($parovi)<21) ? $postavi_msg.='' : $postavi_msg.='Izabrali ste nula ili previse parova<br>Minimum je 1 par, a maximum 20 parova<br>';
		
		//provera minimalnog/maksimalnog uloga
		$postavi_msg = ($tiket[uplata]>=50 && $tiket[uplata]<300000) ? $postavi_msg.='' : $postavi_msg.='Uplata mora biti veca od 50 poena, maksimalni dobitak ne veci od 300000 poena<br>';
		
		//provera kredita (sa racuna i iz aktinog kola)
		$sql="SELECT poeni FROM " . IGRACI_TABLE . " WHERE id='$webbet_session[userid]'"; 
		$result=$db->sql_query($sql);
		if ($result)
		{
			$kredit=$db->sql_fetchrow($result);
		}
		else
		{
			$kredit[poeni]=0;
		}

		$sql="SELECT SUM(dobitak) as akt_poeni FROM ".TIKETI_TABLE." WHERE tIgracID='$webbet_session[userid]' AND dobitni=1"; 
		$result=$db->sql_query($sql);
		if ($result)
		{
			$akt_kredit=$db->sql_fetchrow($result);
		}
		else
		{
			$akt_kredit[akt_poeni]=0;
		}
		
		$kredit[poeni]=$kredit[poeni]+$akt_kredit[akt_poeni]+1500;
		
		$postavi_msg = ($tiket[uplata]>0 && $tiket[uplata]<=$kredit[poeni]) ? $postavi_msg.='' : $postavi_msg.='Nemate dovoljno kredita za uplatu<br>';

		if($postavi_msg=='')
		{
			// aktivno kolo - $aktivnokolo[id]
			$sql="SELECT id FROM ".KOLA_TABLE." WHERE zavrseno=0 ORDER BY id DESC LIMIT 1";
			$result=$db->sql_query($sql);
			$aktivnokolo=$db->sql_fetchrow($result);
			
			// postavljanje tiketa
			$sql="INSERT INTO ".TIKETI_TABLE." (tKoloID,tIgracID,uplata_vreme,uplata_iznos,vidljiv) VALUES ('$aktivnokolo[id]','$webbet_session[userid]',now(),'$tiket[uplata]','$tiket[vidljiv]')";
			$result=$db->sql_query($sql);
			
			$sql="SELECT LAST_INSERT_ID()";
			$result=$db->sql_query($sql);
			if($result) $tiket_id=$db->sql_fetchrow($result);
			
			// smanjivanje kredita
			$sql="UPDATE ".IGRACI_TABLE." SET poeni=poeni-'$tiket[uplata]' WHERE id='$webbet_session[userid]'";
			$result=$db->sql_query($sql);
			
			// postavljanje igara na tiketu
			$uk_kvota=1;
			$i=0;
			while($i<count($parovi))
			{
				//provera para
				$sql="SELECT id,tKoloID,$igre[$i] FROM ".PAROVI_TABLE." WHERE subdate(datum, INTERVAL 5 MINUTE)>NOW() AND odlozeno!=1 AND primi_uplate=1 AND id='$parovi[$i]' AND tKoloID='$aktivnokolo[id]'";
				$result=$db->sql_query($sql);
				if($result) $tiket_ok=$db->sql_fetchrow($result);
				
				if($tiket_ok)
				{
					$uk_kvota=$uk_kvota*$tiket_ok[$igre[$i]];
					$sql="INSERT INTO ".TIKET_IGRA_TABLE." (tTiketID,tParID,tKoloID,tIgraID) VALUES ('$tiket_id[0]','$parovi[$i]','$tiket_ok[tKoloID]','$igre[$i]')";
					$result=$db->sql_query($sql);
					
				}
				$i++;
			}
			// postavljanje dobitka
			$uk_dobitak=$tiket[uplata]*$uk_kvota;
			if($uk_dobitak>=300000)
			{
				$uk_dobitak=300000;
			}
			$sql="UPDATE ".TIKETI_TABLE." SET dobitak='$uk_dobitak' WHERE id='$tiket_id[0]'";
			$result=$db->sql_query($sql);
			
			if($tiket[vidljiv] && $tiket[komentar]!='')
			{
				$komentar=strip_tags($tiket[komentar]);
				$sql="INSERT INTO ".TIKET_KOMENTAR_TABLE." (tTiketID,komentar) VALUES ('$tiket_id[0]','$komentar')";
				$result=$db->sql_query($sql);
			}

			// ciscenje
			$i=count($parovi)-1;
			while($i>=0)
			{
				unset($parovi[$i]);
				$i--;
			}
			
			$postavi_msg.="<b>Postavljanje tiketa uspesno!</b><br>";
				
		}
		
	}
}

?>
<html>
<head>
<title>[Bet Expert] -&gt; Kladjenje</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
<link href="styles.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#E9E9F4" link="6666CC" vlink="6666CC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php
if($authorized && $action=="postavi")
{
	echo '<!-- MAIN CELL BEGIN-->
	<table width="100%" height="100%" border=0  cellspacing=0 cellpadding=0>
	<tr>
	<td align=center>
	<table width="50%" border=0 cellspacing=0 cellpadding=0>
	<tr>
	<td class="srednjaslova" align=center>&nbsp;<font color="#FF0000">';
	
	echo "$postavi_msg";
	
	echo '</font><br><br>
	<a class="linkz" href="tiket.php">Povratak na tiket...</a>
	</td>
	</tr>
	</table>
	</td>
	</tr>
	</table>
	<!-- MAIN CELL END-->';
}
if($authorized && $action!="postavi")
{
  echo '<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr><td align="center" valign="top">
  <!-- MAIN CELL BEGIN-->
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr><td>
  </td></tr>
  <tr>
  <td>
   <!-- TIKET BEGIN-->
   <table width="100%" border=1 bgcolor="#FFFFFF" bordercolor="#333399" cellspacing=1 cellpadding=1>
   <form name="tiket" method=post action="tiket.php?action=postavi">
   <tr bordercolor="#527bb9">
    <td align=center nowrap bgcolor="#527bb9" class="velikaslova_bela"><b>Sifra</b></td>
    <td align=center nowrap bgcolor="#527bb9" class="velikaslova_bela"><b>Datum</b></td>
    <td align=center nowrap bgcolor="#527bb9" class="velikaslova_bela" width="100%"><b>Par</b></td>
    <td align=center nowrap bgcolor="#527bb9" class="velikaslova_bela"><b>Igra</b></td>
    <td align=center nowrap bgcolor="#527bb9" class="velikaslova_bela"><b>Kvota</b></td>
    <td align=center nowrap bgcolor="#527bb9" class="velikaslova_bela"><b>Ponisti</b></td>
   </tr>';


   $kvota_uk=1;
   
   // aktivno kolo - $aktivnokolo[id]
   $sql="SELECT id FROM ".KOLA_TABLE." WHERE zavrseno=0 ORDER BY id DESC LIMIT 1";
   $result=$db->sql_query($sql);
   $aktivnokolo=$db->sql_fetchrow($result);
   
   for($i=0;$i<count($parovi);$i++)
   {
   	
   	$sql="SELECT p.id,p.datum,p.domacin,p.gost,p.$igre[$i],i.oznaka as oznakaigre FROM ".PAROVI_TABLE." p,".IGRE_TABLE." i WHERE p.id='$parovi[$i]' AND p.tKoloID='$aktivnokolo[id]' AND i.id='$igre[$i]'";
   	$result=$db->sql_query($sql);
   	if($result) $par_row=$db->sql_fetchrow($result);
   	
   	$dan=substr($par_row['datum'], 8, 2);
   	$mesec=substr($par_row['datum'], 5, 2);
   	$sat=substr($par_row['datum'], 11, 5);
   	
   	$kvota_uk=$kvota_uk*$par_row[$igre[$i]];
   	
   	echo "<tr bordercolor=\"#E9E9F4\">
   	<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">{$par_row['id']}</td>
   	<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">$dan.$mesec $sat</td>
   	<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">{$par_row['domacin']} - {$par_row['gost']}</td>
   	<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">{$par_row['oznakaigre']}</td>
   	<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">{$par_row[$igre[$i]]}</td>
   	<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\"><a href=\"tiket.php?action=remove&sifra=$i\"><img src=\"img/btn_no.gif\" border=0></a></td>
   	</tr>";
   }
   echo '<tr bordercolor="#527bb9">
    <td  bgcolor="#527bb9" class="velikaslova" colspan=7><font color="#FFFFFF"><b>Ukupna kvota: </b>'.round($kvota_uk,2).' <b>Uplata:</b> <input type=text name="tiket[uplata]" size=5 maxlength=5 value="50"> bod</font> <input name="tiket[vidljiv]" type=checkbox value=1><font color="#FFFFFF">Tiket vidljiv ostalim igracima</font><input type=submit name=tiket_postavi value="Posalji"><input type=submit name=action value="Ponisti"></td>
   </tr>
   <tr bordercolor="#527bb9">
    <td  bgcolor="#527bb9" class="velikaslova" colspan=7><font color="#FFFFFF"><b>Komentar: </b> (maksimum 255 karaktera)<br><textarea name="tiket[komentar]" cols=60 rows=4></textarea></td>
   </tr>
   </form>
   </table>
   <!-- TIKET END-->
  </td>
  </tr>
  </table>
  <!-- MAIN CELL END-->
  </td></tr></table>';
}
?>

</body>
</html>