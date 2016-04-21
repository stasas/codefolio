<?php

define('IN_WEBBET',true);

$webbet_root_path = '../';
include($webbet_root_path . 'common.php');

// Zapocinje novu/nastavlja staru sesiju
session_start();
session_register("webbet_adm_session");

$authorized=0;
$redirect_url=urlencode($PHP_SELF.'?'.$HTTP_SERVER_VARS['QUERY_STRING']);

// Proverava autorizaciju
$authorized = authAdmUser();
?>


<html>
<head>
<title>&lt;- [Bet Expert] - Administracija - &gt;</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">

<link href="../styles.css" rel="stylesheet" type="text/css">
</head>

<body bgcolor="#E9E9F4" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td>
      <!--HEADER BEGIN-->
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td background="../img/top2bg.gif"><img src="../img/dot.gif" width="1" height="24"></td>
          <td background="../img/top2bg.gif"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="velikaslova_bela">
              <tr> 
                <td align="center"><b>A D M I N I S T R A C I J A</b></td>
              </tr>
            </table></td>
          <td background="../img/top2bg.gif"><img src="../img/dot.gif" width="1" height="24"></td>
        </tr>
      </table>
      <!--HEADER  END-->
    </td>
  </tr>
  <tr>
    <td height="100%" valign="top"><table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
        <tr> 
          <td valign="bottom" background="../img/left-back.gif"><img src="../img/dot.gif" width="46" height="1"></td>
          <td width="100%" align="center" class="srednjaslova"> 
            <!-- MAIN CELL BEGIN-->
<?php
if( isset( $action ) && $action=="obrada" )
{
	$ST_br_obradjenih=0;
	$ST_br_dobitnih=0;
	$ST_br_palih=0;
	
	echo '<table border=1 bgcolor="#FFFFFF" bordercolor="#333399" cellspacing=1 cellpadding=1>
	<tr bordercolor="#527bb9">
	<td align=center nowrap bgcolor="#527bb9" class="velikaslova_bela" width="100%"><b>[ Administracija kola - rezultati obrade ]</b></td>
	</tr>
	<tr bordercolor="#E9E9F4">
	<td align=center colspan=6 bgcolor="#E9E9F4" class="srednjaslova"><br><b>Obrada u toku</b><br><br><b>[ ';

	$sql="SELECT * FROM ".TIKETI_TABLE." WHERE tKoloID='$koloid'";
	$result=$db->sql_query($sql);
	$tiketi_row=$db->sql_fetchrowset($result);
	
	foreach($tiketi_row as $row)
	{
		echo "*";
		
		$sql="SELECT t.tTiketID,t.tParID,t.tKoloID,t.tIgraID,t.odlozeno AS tiodlozeno,p.odigrano,p.odlozeno,p.rez_dom,p.rez_gost,p.pol_dom,p.pol_gost FROM ".TIKET_IGRA_TABLE." t,".PAROVI_TABLE." p WHERE p.id=t.tParID AND p.tKoloID=t.tKoloID AND t.tTiketID='$row[id]'";
		$result=$db->sql_query($sql);
		$tikigre_row=$db->sql_fetchrowset($result);

		$br_parova=0;
		$br_pogodjenih=0;
		$SKINISACEKANJA=true;
		$PARPAO=false;
		
		// pregled pojedinacnih parova na tiketu
		// logika igara
		foreach($tikigre_row as $row2)
		{

			$DOUPDATE=false;
			$doslo=0;
			$vecpovecao=0;
			
			switch($row2[tIgraID])
			{
				case 'igra_1':
				 if($row2[rez_dom]>$row2[rez_gost])
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_x':
				 if( ($row2[rez_dom]==$row2[rez_gost]) && ($row2[rez_dom]!='') )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_2':
				 if($row2[rez_dom]<$row2[rez_gost])
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;

				case 'igra_12':
				 if($row2[rez_dom]<>$row2[rez_gost])
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_1x':
				 if( ($row2[rez_dom]>=$row2[rez_gost]) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_x2':
				 if( ($row2[rez_dom]<=$row2[rez_gost]) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_p1':
				 if($row2[pol_dom]>$row2[pol_gost])
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_p2':
				 if($row2[pol_dom]<$row2[pol_gost])
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_px':
				 if( ($row2[pol_dom]==$row2[pol_gost]) && ($row2[pol_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_01':
				 if( ($row2[rez_dom]+$row2[rez_gost] <= 1) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_02':
				 if( ($row2[rez_dom]+$row2[rez_gost] <= 2) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_11p':
				 if( ($row2[pol_dom]+$row2[pol_gost] >= 1) && ($row2[pol_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_12p':
				 if( ($row2[pol_dom]+$row2[pol_gost] >= 2) && ($row2[pol_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_22p':
				 if( ( ($row2[rez_dom]-$row2[pol_dom])+($row2[rez_gost]-$row2[pol_gost]) >= 2) && ($row2[pol_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_23':
				 if( ($row2[rez_dom]+$row2[rez_gost] >= 2) && ($row2[rez_dom]+$row2[rez_gost] <= 3) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_3p':
				 if( ($row2[rez_dom]+$row2[rez_gost] >= 3) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_4p':
				 if( ($row2[rez_dom]+$row2[rez_gost] >= 4) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_46':
				 if( ($row2[rez_dom]+$row2[rez_gost] >= 4) && ($row2[rez_dom]+$row2[rez_gost] <= 6) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;	
				case 'igra_5p':
				 if( ($row2[rez_dom]+$row2[rez_gost] >= 5) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_7p':
				 if( ($row2[rez_dom]+$row2[rez_gost] >= 7) && ($row2[rez_dom]!=''))
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				
				case 'igra_1u1':
				 if( ($row2[rez_dom]>$row2[rez_gost]) && ($row2[pol_dom]>$row2[pol_gost]) )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_1u2':
				 if( ($row2[rez_dom]<$row2[rez_gost]) && ($row2[pol_dom]>$row2[pol_gost]) )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_1ux':
				 if( ($row2[rez_dom]==$row2[rez_gost]) && ($row2[pol_dom]>$row2[pol_gost]) && ($row2[rez_dom]!='') )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_2u1':
				 if( ($row2[rez_dom]>$row2[rez_gost]) && ($row2[pol_dom]<$row2[pol_gost]) )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_2u2':
				 if( ($row2[rez_dom]<$row2[rez_gost]) && ($row2[pol_dom]<$row2[pol_gost]) )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_2ux':
				 if( ($row2[rez_dom]==$row2[rez_gost]) && ($row2[pol_dom]<$row2[pol_gost]) && ($row2[rez_dom]!='') )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_xu1':
				 if( ($row2[rez_dom]>$row2[rez_gost]) && ($row2[pol_dom]==$row2[pol_gost]) && ($row2[pol_dom]!='') )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_xu2':
				 if( ($row2[rez_dom]<$row2[rez_gost]) && ($row2[pol_dom]==$row2[pol_gost]) && ($row2[pol_dom]!='') )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
				case 'igra_xux':
				 if( ($row2[rez_dom]==$row2[rez_gost]) && ($row2[pol_dom]==$row2[pol_gost]) && ($row2[pol_dom]!='') )
				 {
				 	$br_pogodjenih++;
				 	$doslo=1;
				 }
				 break;
			}
			$br_parova++;
			
			if( ($row2[rez_dom]!='') && ($row2[rez_gost]!='') )
			{
				$sql="UPDATE ".TIKET_IGRA_TABLE." SET ceka=0,doslo='$doslo' WHERE tTiketID='$row2[tTiketID]' AND tParID='$row2[tParID]'";
				$DOUPDATE=true;
				if(!$doslo)
				{
					$PARPAO=true;
				}
			}
			else
			{
				// nisu uneti rezultati, treba tiket da ostane na cekanju, tj da je SKINISACEKANJA=false
				$SKINISACEKANJA=false;
			}
			if($row2[odlozeno] || $row2[tiodlozeno])
			{
				if($doslo==0)
				{
					$br_pogodjenih++;
					$vecpovecao=1;
				}
			}
			if( $row[uplata_vreme] >= $row2[odigrano] && $row2[odigrano]!='' && $row2[tiodlozeno]!='1' )
			{
				// da se ne desi da se poveca br_pogodjenih dva puta
				if($doslo==0 AND $vecpovecao==0)
				{
					$br_pogodjenih++;
				}
				$sql="UPDATE ".TIKET_IGRA_TABLE." SET ceka=0,odlozeno=1 WHERE tTiketID='$row2[tTiketID]' AND tParID='$row2[tParID]'";
				$DOUPDATE=true;
				
				// smanjenje dobitka za kvota odlozenog*uplata
				$kvota_sql="SELECT $row2[tIgraID] FROM ".PAROVI_TABLE." WHERE id='$row2[tParID]' AND tKoloID='$row2[tKoloID]'";
				$result=$db->sql_query($kvota_sql);
				$kvota_row=$db->sql_fetchrow($result);

				$novidobitak=$row[dobitak]/$kvota_row[$row2[tIgraID]];

				$sql2="UPDATE ".TIKETI_TABLE." SET dobitak='$novidobitak' WHERE id='$row2[tTiketID]'";
				$result=$db->sql_query($sql2);
			}
			if($row2[odlozeno] && !$row2[tiodlozeno])
			{
				$sql="UPDATE ".TIKET_IGRA_TABLE." SET ceka=0,odlozeno=1 WHERE tTiketID='$row2[tTiketID]' AND tParID='$row2[tParID]'";
				$DOUPDATE=true;
				
				// smanjenje dobitka za kvota odlozenog*uplata
				$kvota_sql="SELECT $row2[tIgraID] FROM ".PAROVI_TABLE." WHERE id='$row2[tParID]'";
				$result=$db->sql_query($kvota_sql);
				$kvota_row=$db->sql_fetchrow($result);

				$novidobitak=$row[dobitak]/$kvota_row[$row2[tIgraID]];

				$sql2="UPDATE ".TIKETI_TABLE." SET dobitak='$novidobitak' WHERE id='$row2[tTiketID]'";
				$result=$db->sql_query($sql2);
			}
			
			if($DOUPDATE)
			{
				// ovde podesavanje tiket igre
				$result=$db->sql_query($sql);
			}
		}
		if($br_parova>0 && $br_parova==$br_pogodjenih)
		{
			$ST_br_dobitnih++;
			// skidanje tiketa sa cekanja i proglasavanje dobitnim
			$sql="UPDATE ".TIKETI_TABLE." SET dobitni=1,ceka=0 WHERE id='$row[id]'";
			$result=$db->sql_query($sql);
		}
		else
		{
			// ako nije dobitni a SKINISACEKANJA je true onda postavlja ceka=0
			if($SKINISACEKANJA || $PARPAO)
			{
				$sql="UPDATE ".TIKETI_TABLE." SET dobitni=0,ceka=0 WHERE id='$row[id]'";
				$result=$db->sql_query($sql);
				$ST_br_palih++;
			}
		}
		
		
		$ST_br_obradjenih++;
	}
	
	echo " ]</b><br><br>Broj obradjenih tiketa: $ST_br_obradjenih; Broj dobitnih tiketa: $ST_br_dobitnih; Broj palih tiketa: $ST_br_palih";
	echo '</td></tr><tr bordercolor="#527bb9">';
	echo "<td align=center nowrap bgcolor=\"#527bb9\" class=\"velikaslova\" colspan=6>
	<a class=\"velikaslova\" href=\"$PHP_SELF\">Nazad</a></td>";
	echo '</tr></table>';
}
elseif( isset( $action ) && $action=="zatvori" )
{
	
	// postavljanje kredita igracima (sa dobitnih tiketa iz izabranog kola)
	$sql="SELECT tIgracID,SUM(dobitak) AS pluspoeni FROM ".TIKETI_TABLE." WHERE dobitni=1 AND tKoloID ='$koloid' GROUP by tIgracID";
	$result=$db->sql_query($sql);
	$pluspoeni_row=$db->sql_fetchrowset($result);
	if($pluspoeni_row)
	{
		foreach($pluspoeni_row as $row)
		{
			$sql="UPDATE ".IGRACI_TABLE." SET poeni=poeni+'$row[pluspoeni]' WHERE id='$row[tIgracID]'";
			$result=$db->sql_query($sql);
		}
	}
	
	// brisanje tiketa,igara i parova za izabrano kolo
	$sql="DELETE FROM ".TIKETI_TABLE." WHERE tKoloID ='$koloid'";
	$result=$db->sql_query($sql);
	$sql="DELETE FROM ".TIKET_IGRA_TABLE." WHERE tKoloID ='$koloid'";
	$result=$db->sql_query($sql);
	$sql="DELETE FROM ".PAROVI_TABLE." WHERE tKoloID ='$koloid'";
	$result=$db->sql_query($sql);
	
	$sql="UPDATE ".KOLA_TABLE." SET zavrsena_obr='1' WHERE id='$koloid'";
	$result=$db->sql_query($sql);
	
	if( $result )
	{
		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\"><td align=center class=\"velikaslova_bela\"><b>Zatvaranje kola</b></td></tr>
		<tr><td align=center class=\"srednjaslova\"><br>Uspesno<br><a href=\"adm-kolo.php\" class=\"linkz\">Nazad na administraciju kola</a><br><br><br></td></tr>
		</table>";
	}
}
elseif( isset( $action ) && $action=="lock" )
{
	// zakljucavanje kola
	$sql="UPDATE ".KOLA_TABLE." SET zavrseno=1 WHERE id='$kolo'";
	$result=$db->sql_query($sql);
	
	if( $result )
	{
		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\"><td align=center class=\"velikaslova_bela\"><b>Zatvaranje kola: $kolo</b></td></tr>
		<tr><td align=center class=\"srednjaslova\"><br>Uspesno<br><a href=\"adm-kolo.php\" class=\"linkz\">Nazad na administraciju kola</a><br><br><br></td></tr>
		</table>";
	}
}
elseif( isset( $action ) && $action=="nagradi" )
{
	$sql="SELECT tIgracID, SUM(uplata_iznos) AS ulozeno FROM ".TIKETI_TABLE." WHERE tKoloID='$kolo' GROUP BY tIgracID ORDER BY ulozeno DESC";
	$result=$db->sql_query($sql);
	$uplata_row=$db->sql_fetchrowset($result);
	
	$brojucesnika=count($uplata_row);
	$cntr=0;
	$debugi='';
	
	foreach($uplata_row AS $row)
	{
	  $nagrada=$row[ulozeno];
	  if($row[ulozeno]>3000) $nagrada=3000;
	  
	  // nagradjivanje svakog igraca sa bonus x poena (x=ulozeno u kolu,ne vise od 3000)
	  $sql="UPDATE ".IGRACI_TABLE." SET poeni=poeni+'$nagrada' WHERE id='$row[tIgracID]'";
	  $result=$db->sql_query($sql);
	  
	  $debugi.=$sql.'<br>';
	  
	  if( $result ) $cntr++;
	}

		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\"><td align=center class=\"velikaslova_bela\"><b>Povecanje poena igraca</b></td></tr>
		<tr><td align=center class=\"srednjaslova\"><br>Broj ucesnika kola: $brojucesnika<br>Nagradjeno: $cntr<br>$debugi<br><a href=\"adm-kolo.php\" class=\"linkz\">Nazad na administraciju kola</a><br><br><br></td></tr>
		</table>";
}
elseif( isset( $action ) && $action=="novokolo" )
{
	// ubacivanje novog kola
	$sql="INSERT INTO ".KOLA_TABLE." (id,dat_pocetka,dat_zavrsetka) VALUES ('$nkform[koloid]','$nkform[pocetak]','$nkform[kraj]')";
	$result=$db->sql_query($sql);
	
	if( $result )
	{
		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\"><td align=center class=\"velikaslova_bela\"><b>Postavljanje kola: $nkform[koloid]</b></td></tr>
		<tr><td align=center class=\"srednjaslova\"><br>Uspesno<br><a href=\"adm-kolo.php\" class=\"linkz\">Nazad na administraciju kola</a><br><br><br></td></tr>
		</table>";
	}
}
else
{
	$sql="SELECT * FROM ".KOLA_TABLE." ORDER BY id DESC";
	$result=$db->sql_query($sql);
	$kola_row=$db->sql_fetchrowset($result);
	
	echo '<table width="50%" border=1 bgcolor="#FFFFFF" bordercolor="#333399" cellspacing=1 cellpadding=1>
            <tr bordercolor="#527bb9">
             <td align=center nowrap bgcolor="#527bb9" class="velikaslova_bela" width="100%"><b>[ Administracija kola ]</b></td>
            </tr>
            <tr bordercolor="#527bb9">
            <form name="novokolo" method=post action="adm-kolo.php">
            <input type=hidden name="action" value="novokolo">
             <td bgcolor="#527bb9" class="velikaslova_bela" width="100%">Novo kolo: <input size=3 name="nkform[koloid]" type=text> Pocetak: <input size=10 name="nkform[pocetak]" type=text value="0000-00-00"> Kraj: <input size=10 name="nkform[kraj]" type=text value="0000-00-00"><input name=submit type=submit value=Postavi><br><i>format datuma: GODINA/MESEC/DAN</i></td>
            </form>
            </tr>
            <tr bordercolor="#E9E9F4">
             <td align=center colspan=6 bgcolor="#E9E9F4">
             <table width="100%" border=0 cellspacing=1 cellpadding=0 class="srednjaslova">
             <tr>
             <td><b>ID</b></td>
             <td><b>Pocetak</b></td>
             <td><b>Kraj</b></td>
             <td align=center><b>Zavrseno</b></td>
             <td align=center><b>Nagradi</b></td>
             <td align=center>&nbsp;</td>
             <td align=center>&nbsp;</td>
             </tr>';
        
        foreach( $kola_row as $row )
        {
        	$dat_poc=explode("-", $row[dat_pocetka]);
        	$dat_zav=explode("-", $row[dat_zavrsetka]);
        	
        	$zav_s='<img src="../img/btn_no.gif">';
        	$zav_l='<a href="adm-kolo.php?action=lock&kolo='.$row[id].'"><img src="../img/lock.gif" border=0 alt="Zatvori kolo"></a>';
        	
        	if($row[zavrseno])
        	{
        		$zav_l='';
        		$zav_s='<img src="../img/btn_yes.gif">';
        	}

        	$zavob_n='&nbsp;';
        	$zavob_o='&nbsp;';
        	$zavob_s='&nbsp;';
        	if(!$row[zavrsena_obr])
        	{
        		$zavob_n="<a class=\"srednjaslova\" href=\"adm-kolo.php?action=nagradi&kolo=$row[id]\">+nagrada</a>";
        		$zavob_o="<a class=\"srednjaslova\" href=\"adm-kolo.php?action=obrada&koloid={$row[id]}\">obradi</a>";
        		$zavob_s="<a class=\"srednjaslova\" href=\"adm-kolo.php?action=zatvori&koloid={$row[id]}\">Zavrsi obradu</a>";
        	}
        	
        	echo "<tr>
        	<td>$row[id]</td>
        	<td>$dat_poc[2].$dat_poc[1].$dat_poc[0]</td>
        	<td>$dat_zav[2].$dat_zav[1].$dat_zav[0]</td>
        	<td align=center>$zav_s $zav_l</td>
        	<td align=center>$zavob_n</td>
        	<td align=center><b>$zavob_o</b></td>
        	<td align=center>$zavob_s</td>
        	</tr>";
        }
        echo '</table>
             </td>
            </tr>
            <tr bordercolor="#527bb9">
             <td align=center nowrap bgcolor="#527bb9" class="velikaslova" colspan=6><a class="velikaslova" href="index.php">Nazad</a></td>
            </tr>
            </table>';
	
}
?>
            <!-- MAIN CELL END-->
          </td>
          <td valign="bottom" background="../img/right-back.gif"><img src="../img/dot.gif" width="117" height="1"></td>
        </tr>
      </table> </td>
  </tr>
  <tr>
    <td>
      <!--FOOTER BEGIN-->
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td background="../img/top2bg.gif"><img src="../img/dot.gif" width="1" height="24"></td>
          <td background="../img/top2bg.gif"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="malaslova-bela">
              <tr> 
                <td align="center">&copy; copyright 2003,2004 Bet<strong>Expert</strong>. 
                  Sva prava zadržana.</td>
              </tr>
            </table></td>
          <td background="../img/top2bg.gif"><img src="../img/dot.gif" width="1" height="24"></td>
        </tr>
      </table>
      <!--FOOTER END-->
    </td>
  </tr>
</table>
</body>
</html>
