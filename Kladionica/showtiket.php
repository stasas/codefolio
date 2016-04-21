<?php

define('IN_WEBBET',true);

$webbet_root_path = './';
include($webbet_root_path . 'common.php');

// Zapocinje novu/nastavlja staru sesiju
session_start();
session_register("webbet_session");

// Proverava autorizaciju
$authorized = authUser();

if(isset( $ponisti ) && $authorized)
{
	$sql="SELECT id,uplata_iznos,tKoloID FROM ".TIKETI_TABLE." WHERE id='$ponisti' AND dobitni=0 AND tIgracID='$webbet_session[userid]'";
	$result=$db->sql_query($sql);
	$ponisti_row=$db->sql_fetchrow($result);
	
	if($ponisti_row)
	{
		$sql="SELECT p.id FROM ".TIKET_IGRA_TABLE." t,".PAROVI_TABLE." p WHERE tTiketID='$ponisti_row[id]' AND t.tParID=p.id AND p.tKoloID=t.tKoloID AND ( subdate(p.datum, INTERVAL 5 MINUTE)<NOW() OR p.odlozeno=1 OR t.odlozeno=1) AND p.primi_uplate=1";
		$result=$db->sql_query($sql);
		$ponisti_ok=$db->sql_fetchrowset($result);
		if(!$ponisti_ok) 
		{
			$sql="DELETE FROM ".TIKET_IGRA_TABLE." WHERE tTiketID='$ponisti_row[id]'";
			$result=$db->sql_query($sql);
			$sql="DELETE FROM ".TIKETI_TABLE." WHERE id='$ponisti_row[id]'";
			$result=$db->sql_query($sql);
			$sql="UPDATE ".IGRACI_TABLE." SET poeni=poeni+'$ponisti_row[uplata_iznos]' WHERE id='$webbet_session[userid]'";
			$result=$db->sql_query($sql);
			$sql="DELETE FROM ".TIKET_KOMENTAR_TABLE." WHERE tTiketID='$ponisti_row[id]'";
			$result=$db->sql_query($sql);
		}
	}
}
if(isset( $kopiraj ) && $authorized)
{
	$sql="SELECT id,uplata_iznos,tKoloID FROM ".TIKETI_TABLE." WHERE id='$kopiraj' AND dobitni=0 AND tIgracID='$webbet_session[userid]'";
	$result=$db->sql_query($sql);
	$ponisti_row=$db->sql_fetchrow($result);
	
	if($ponisti_row)
	{
		$sql="SELECT t.tParID,t.tIgraID,p.id FROM ".TIKET_IGRA_TABLE." t,".PAROVI_TABLE." p WHERE tTiketID='$ponisti_row[id]' AND t.tParID=p.id AND p.tKoloID=t.tKoloID AND subdate(p.datum, INTERVAL 5 MINUTE)>NOW() AND p.odlozeno=0 AND t.odlozeno=0 AND p.primi_uplate=1";
		$result=$db->sql_query($sql);
		$ponisti_ok=$db->sql_fetchrowset($result);
		
		if($ponisti_ok) 
		{
		  
		  //brisanje trenutnog tiketa
		  $i=count($parovi)-1;
		  while($i>=0)
		  {
		  	unset($parovi[$i]);
			unset($igre[$i]);
			$i--;
		  }
		  unset($HTTP_SESSION_VARS['parovi']);
		  unset($HTTP_SESSION_VARS['igre']);
		  
		  //da li postoji nesto
		  if(!(isset($HTTP_SESSION_VARS['parovi'])))
		  {
			$HTTP_SESSION_VARS['parovi']=Array();
			$HTTP_SESSION_VARS['igre']=Array();
		  }
		  //postavljanje kopiranog tiketa
		  foreach( $ponisti_ok AS $novipar )
		  {
		  	$brpar=count($HTTP_SESSION_VARS['parovi']);
		  	$novi_ok=true;
		  	
		  	for($i=0;$i<$brpar;$i++)
		  	{
		  		if($novipar[tParID]==$parovi[$i]) $novi_ok=false;
		  	}
		  	if($novi_ok)
		  	{
		  		$HTTP_SESSION_VARS['parovi'][$brpar]=$novipar[tParID];
		  		$parovi[$brpar]=$novipar[tParID];
		  		$HTTP_SESSION_VARS['igre'][$brpar]=$novipar[tIgraID];
		  		$igre[$brpar]=$novipar[tIgraID];
		  	}
		  }
		  $act_poruka="Tiket je kopiran u novi tiket<br><a href=\"novi.php\" target=\"mainFrame\">Pregled novog tiketa</a>";
  
		}
	}
}
if(isset( $izmeni ) && $authorized)
{
	$sql="SELECT id,uplata_iznos,tKoloID FROM ".TIKETI_TABLE." WHERE id='$izmeni' AND dobitni=0 AND tIgracID='$webbet_session[userid]'";
	$result=$db->sql_query($sql);
	$ponisti_row=$db->sql_fetchrow($result);
	
	if($ponisti_row)
	{
		$sql="SELECT t.tParID,t.tIgraID,p.id FROM ".TIKET_IGRA_TABLE." t,".PAROVI_TABLE." p WHERE tTiketID='$ponisti_row[id]' AND t.tParID=p.id AND p.tKoloID=t.tKoloID AND subdate(p.datum, INTERVAL 5 MINUTE)>NOW() AND p.odlozeno=0 AND t.odlozeno=0 AND p.primi_uplate=1";
		$result=$db->sql_query($sql);
		$ponisti_ok=$db->sql_fetchrowset($result);

		if($ponisti_ok) 
		{
		  
		  //brisanje trenutnog tiketa
		  $i=count($parovi)-1;
		  while($i>=0)
		  {
		  	unset($parovi[$i]);
			unset($igre[$i]);
			$i--;
		  }
		  unset($HTTP_SESSION_VARS['parovi']);
		  unset($HTTP_SESSION_VARS['igre']);
		  
		  //da li postoji nesto
		  if(!(isset($HTTP_SESSION_VARS['parovi'])))
		  {
			$HTTP_SESSION_VARS['parovi']=Array();
			$HTTP_SESSION_VARS['igre']=Array();
		  }
		  //postavljanje kopiranog tiketa
		  foreach( $ponisti_ok AS $novipar )
		  {
		  	$brpar=count($HTTP_SESSION_VARS['parovi']);
		  	$novi_ok=true;
		  	
		  	for($i=0;$i<$brpar;$i++)
		  	{
		  		if($novipar[tParID]==$parovi[$i]) $novi_ok=false;
		  	}
		  	if($novi_ok)
		  	{
		  		$HTTP_SESSION_VARS['parovi'][$brpar]=$novipar[tParID];
		  		$parovi[$brpar]=$novipar[tParID];
		  		$HTTP_SESSION_VARS['igre'][$brpar]=$novipar[tIgraID];
		  		$igre[$brpar]=$novipar[tIgraID];
		  	}
		  }
		  
		  //brisanje postavljenog tiketa
		  $sql="DELETE FROM ".TIKET_IGRA_TABLE." WHERE tTiketID='$ponisti_row[id]'";
		  $result=$db->sql_query($sql);
		  $sql="DELETE FROM ".TIKETI_TABLE." WHERE id='$ponisti_row[id]'";
		  $result=$db->sql_query($sql);
		  $sql="UPDATE ".IGRACI_TABLE." SET poeni=poeni+'$ponisti_row[uplata_iznos]' WHERE id='$webbet_session[userid]'";
		  $result=$db->sql_query($sql);
		  $sql="DELETE FROM ".TIKET_KOMENTAR_TABLE." WHERE tTiketID='$ponisti_row[id]'";
		  $result=$db->sql_query($sql);
		  
		  $act_poruka="Tiket je izmenjen u novi tiket<br><a href=\"novi.php\" target=\"mainFrame\">Pregled novog tiketa</a>";
  
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
if($authorized)
{
      echo '<!-- MAIN CELL BEGIN-->
      <table width="80%" align="center" border="0" cellspacing="0" cellpadding="0">
        <tr>
        <td><img src="img/dot.gif" height=20></td>
        </tr>
        <tr>
          <td align=center class="velikaslova">
            <!-- MAIN CONTENT BEGIN-->';
             
             $sql="SELECT * FROM ".TIKETI_TABLE." WHERE tIgracID='$webbet_session[userid]' AND id='$tiketid'";
             $result=$db->sql_query($sql);
             $tiketi_row=$db->sql_fetchrowset($result);
             // debug - echo "$sql";
             
             if(isset($kopiraj) || isset($izmeni))
             {
             	echo "$act_poruka";
             }
             
             if($tiketi_row)
             {
             	foreach($tiketi_row as $row)
             	{
             		$datum[dan]=substr($row['uplata_vreme'], 8, 2);
             		$datum[mesec]=substr($row['uplata_vreme'], 5, 2);
             		$datum[godina]=substr($row['uplata_vreme'], 0, 4);
             		
             		if($row[dobitni] && !$row[ceka])
             		{
             			$tik_status='Dobitni';
             			$tik_boja='#527bb9';
             		}
             		if(!$row[dobitni] && $row[ceka])
             		{
             			$tik_status='Ceka';
             			$tik_boja='#527bb9';
             		}
             		if(!$row[dobitni] && !$row[ceka])
             		{
             			$tik_status='Pao';
             			$tik_boja='#999999';
             		}
             		
             		
             		
             		$ponisti='&nbsp';
             		$izmeni='&nbsp';
             		$kopiraj='&nbsp';

             		$sql="SELECT p.id FROM ".TIKET_IGRA_TABLE." t,".PAROVI_TABLE." p WHERE tTiketID='$row[id]' AND t.tParID=p.id AND p.tKoloID=t.tKoloID AND ( subdate(p.datum, INTERVAL 5 MINUTE)<NOW() OR p.odlozeno=1 OR t.odlozeno=1) AND p.primi_uplate=1";
			$result=$db->sql_query($sql);
			$ponisti_ok=$db->sql_fetchrowset($result);
			if(!$ponisti_ok)
			{
				$ponisti="<a href=\"showtiket.php?ponisti={$row[id]}\" target=\"bottomFrame\"><img src=\"img/tik_ponisti.gif\" border=0 alt=\"Ponistavanje tiketa\"></a>";
				$izmeni="<a href=\"showtiket.php?izmeni={$row[id]}\" target=\"bottomFrame\"><img src=\"img/tik_izmeni.gif\" border=0 alt=\"Izmena tiketa\"></a>";
				$kopiraj="<a href=\"showtiket.php?kopiraj={$row[id]}\" target=\"bottomFrame\"><img src=\"img/tik_kopiraj.gif\" border=0 alt=\"Kopiranje tiketa\"></a>";

			}
             		
             		if($row[dobitni])
             		{
             			$ponisti='&nbsp';
             			$izmeni='&nbsp';
             			$kopiraj='&nbsp';
             		}
             		
             		echo "<table width=\"100%\" border=1 bgcolor=\"#FFFFFF\" bordercolor=\"#333399\" cellspacing=1 cellpadding=1>
             		<tr bordercolor=\"$tik_boja\">
             		<td colspan=8 bgcolor=\"$tik_boja\">
             		<!-- TIKET INFO BEGIN-->
             		<table width=\"100%\" border=0 cellspacing=0 cellpadding=0>
             		 <tr>             		 
             		 <td class=\"velikaslova_bela\"><b>Kolo:</b> {$row['tKoloID']}</td>
             		 <td class=\"velikaslova_bela\"><b>Datum:</b> $datum[dan].$datum[mesec].$datum[godina]</td>
             		 <td class=\"velikaslova_bela\"><b>Broj:</b> {$row['id']}</td>
             		 <td class=\"velikaslova_bela\"><b>Uplata:</b> {$row['uplata_iznos']}</td>
             		 <td class=\"velikaslova_bela\"><b>Dobitak:</b> {$row['dobitak']}</td>
             		 <td class=\"velikaslova_bela\"><b>Status:</b> $tik_status</td>
             		 <td align=right>$ponisti $izmeni $kopiraj</td>
             		 </tr>
             		</table>
             		<!-- TIKET INFO END-->
             		</td>
             		</tr>
             		<!-- TIKET IGRE BEGIN-->
             		<tr bordercolor=\"$tik_boja\">
             		<td align=center nowrap bgcolor=\"$tik_boja\" class=\"velikaslova_bela\"><b>Sifra</b></td>
             		<td align=center nowrap bgcolor=\"$tik_boja\" class=\"velikaslova_bela\"><b>Datum</b></td>
             		<td align=center nowrap bgcolor=\"$tik_boja\" class=\"velikaslova_bela\" width=\"100%\"><b>Par</b></td>
             		<td align=center nowrap bgcolor=\"$tik_boja\" class=\"velikaslova_bela\"><b>FT</b></td>
             		<td align=center nowrap bgcolor=\"$tik_boja\" class=\"velikaslova_bela\"><b>HT</b></td>
             		<td align=center nowrap bgcolor=\"$tik_boja\" class=\"velikaslova_bela\"><b>Igra</b></td>
             		<td align=center nowrap bgcolor=\"$tik_boja\" class=\"velikaslova_bela\"><b>Kvota</b></td>
             		<td align=center nowrap bgcolor=\"$tik_boja\" class=\"velikaslova_bela\"><b>Doslo</b></td>
             		</tr>";

             		$sql="SELECT i.oznaka as oznakaigre,tParID,tIgraID,doslo,".TIKET_IGRA_TABLE.".odlozeno AS tiodlozeno,".PAROVI_TABLE.".* FROM ".TIKET_IGRA_TABLE.",".PAROVI_TABLE.",".IGRE_TABLE." i WHERE tTiketID='$row[id]' AND ".PAROVI_TABLE.".id=tParID AND ".PAROVI_TABLE.".tKoloID=".TIKET_IGRA_TABLE.".tKoloID AND i.id=".TIKET_IGRA_TABLE.".tIgraID";
             		$result=$db->sql_query($sql);
             		$tiketigre_row=$db->sql_fetchrowset($result);
             		
             		foreach($tiketigre_row as $row2)
             		{
             			$doslo='&nbsp;';
             			if($row2['doslo']=='1')
             			{
             				$doslo='<img src="img/btn_yes.gif">';
             			}
             			elseif($row2['doslo']=='0')
             			{
             				$doslo='<img src="img/btn_no.gif">';
             			}
             			$dan=substr($row2['datum'], 8, 2);
             			$mesec=substr($row2['datum'], 5, 2);
             			$sat=substr($row2['datum'], 11, 5);
             			
             			$mecodlozen='';
             			
             			if($row2[tiodlozeno])
             			{
             				$mecodlozen='<font color="#ff0000">] - ODLOZENO</font>';
             			}
             			
             			echo "<tr bordercolor=\"#E9E9F4\">
             			<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">{$row2['tParID']}</td>
             			<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">$dan.$mesec $sat</td>
             			<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\" width=\"100%\">{$row2['domacin']} - {$row2['gost']} $mecodlozen</td>
             			<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">{$row2['rez_dom']}:{$row2['rez_gost']}</td>
             			<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">{$row2['pol_dom']}:{$row2['pol_gost']}</td>
             			<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">{$row2['oznakaigre']}</td>
             			<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">{$row2[$row2['tIgraID']]}</td>
             			<td align=center nowrap bgcolor=\"#E9E9F4\" class=\"srednjaslova\">$doslo</td>
             			</tr>";
             		}
             		
             		echo '<!-- TIKET IGRE END-->
             		</table><img src="img/dot.gif" height=10>';
             	}
             }
             else
             {
             	echo '<table width="100%" border=0 cellspacing=0 cellpadding=0>
             	<tr><td align=center class="srednjaslova">&nbsp;</td></tr></table>';
             }

            echo '<!-- MAIN CONTENT END-->
          </td>
        </tr>
      </table> 
      <!-- MAIN CELL END-->';
}
?>

</body>
</html>