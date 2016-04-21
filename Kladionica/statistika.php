<?php

define('IN_WEBBET',true);

$webbet_root_path = './';
include($webbet_root_path . 'common.php');

// Zapocinje novu/nastavlja staru sesiju
session_start();
session_register("webbet_session");

$authorized=0;
$message="";

// Proverava autorizaciju
$authorized = authUser();

?>
<html>
<head>
<title>[Bet Expert] -&gt; Kladjenje</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-2">
<link href="styles.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor="#E9E9F4" link="6666CC" vlink="6666CC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?php

if(!$authorized)
{
?>
<!-- MAIN CELL BEGIN-->
<table width="100%" height="100%" border=0  cellspacing=0 cellpadding=0>
<tr>
<td align=center>
<table width="50%" border=0 cellspacing=0 cellpadding=0>
<tr>
<td class="srednjaslova" align=center>&nbsp;<font color="#FF0000">
<?php echo("$message");?>
</font><br><br>
</td>
</tr>
<!-- INFOBEGIN-->
<tr>
<td class="srednjaslova">
<?php 
 include($webbet_root_path . 'kladtekst.inc');
?><br><br><br>
</td>
</tr>
<!-- INFOEND-->
<tr>
<td>
<!-- LOGIN BEGIN-->
<?php echo authForm(); ?>
<!-- LOGIN END-->
</td>
</tr>
<tr>
<td><img src="img/dot.gif" height=20></td>
</tr>
<tr>
<td class="srednjaslova">
<center><b>Kladjenje</b></center>
<br><br>
Virtuelno kladjenje pruza mogucnost kladjenja u realnom vremenu na stvarne meceve po realnim kvotama iz ponude kladionice.
Uslov za kladjenje je registracija koja je besplatna i po kojoj se dobijaju pocetni poeni koji se koriste kao ulog pri kladjenju.
Najuspesniji (oni koji na svom racunu imaju najveci broja poena) na kraju svakog kladionicarskog kola i na kraju meseca dobijaju vredne nagrade.
Moguce je odigrati sve igre koje su uobicajene u kladionicama sa razlikom sto ne postoje uslovi igre vec je moguce proizvoljno kombinovati igre.
Igracu je omoguceno da pregleda svoje dobitne tikete i tikete koji jos cekaju na zavrsetak nekog meca, da vidi svoju satistiku od trenutka otvaranja naloga i preleda tikete vodecih na listi. 
</td>
</tr>
</table>
</td>
</tr>
</table>
<!-- MAIN CELL END-->
<?php
// nastavak IF(!AUTORIZACIJA)
}
else
{
	echo '<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>      
    <td align="center" width="100%" valign="top">
      <!-- MAIN CELL BEGIN-->
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td valign="top" background="img/small-menu.gif">
            <!-- mali MENI TABELA begin-->
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td align="center"><img src="img/small-menu-div.gif" width="19" height="24"><a target="mainFrame" href="kladjenje.php?show=statistika"><img src="img/kmeni-st.gif" alt="Statistika" border="0"></a><img src="img/small-menu-div.gif" width="19" height="24"><a target="mainFrame" href="kladjenje.php?show=dobitni"><img src="img/kmeni-dt.gif" alt="Dobitni tiketi" border="0"></a><img src="img/small-menu-div.gif" width="19" height="24"><a target="mainFrame" href="kladjenje.php?show=aktivni"><img src="img/kmeni-at.gif" alt="Aktivni tiketi" border="0"></a><img src="img/small-menu-div.gif" width="19" height="24"><a target="mainFrame" href="kladjenje.php?show=vidljivi"><img src="img/kmeni-vt.gif" alt="Vidljivi tiketi" border="0"></a><img src="img/small-menu-div.gif" width="19" height="24"><a href="novi.php" target="mainFrame"><img src="img/kmeni-nt.gif" alt="Novi tiket" border="0"></a><img src="img/small-menu-div.gif" width="19" height="24"><a target="mainFrame" href="kladjenje.php?show=podaci"><img src="img/kmeni-lp.gif" alt="Licni podaci" border="0"></a><img src="img/small-menu-div.gif" width="19" height="24"></td>
              </tr>
            </table>
            <!-- mali MENI TABELA end-->
          </td>
        </tr>
        <tr>
        <td><img src="img/dot.gif" height=20></td>
        </tr>
        <tr>
          <td align=center>
            <!-- MAIN CONTENT BEGIN-->
            <table width="80%" border=0 bgcolor="#FFFFFF" bordercolor="#333399" cellspacing=1 cellpadding=3>
        <tr bgcolor="#333399"> 
          <td align=center nowrap bgcolor="#527bb9" class="velikaslova" height=20><font color="#FFFFFF"><b>Statistika vaseg kladjenja</b></font></td>
        </tr>';

        $sql="SELECT id FROM ".KOLA_TABLE." WHERE zavrseno=0 ORDER BY id DESC LIMIT 1";
	$result=$db->sql_query($sql);
	$aktivnokolo=$db->sql_fetchrow($result);
        
        $sql="SELECT * FROM ".MESEC_TABLE." LIMIT 1";
	$result=$db->sql_query($sql);
	$aktivnimesec=$db->sql_fetchrow($result);
        
        $sql="SELECT poeni FROM ".IGRACI_TABLE." WHERE id='$webbet_session[userid]'";
        $result=$db->sql_query($sql);
        $stats_row=$db->sql_fetchrow($result);
        $sql3="SELECT count(id) as broj FROM ".TIKETI_TABLE." WHERE dobitni=0 AND tIgracID='$webbet_session[userid]'";
        $result=$db->sql_query($sql3);
        $stats_row3=$db->sql_fetchrow($result);
        
        // osvojeno u aktivnom kolu
        $sql_poeniak="SELECT (SUM(td.dobitak)-SUM(tu.uplata_iznos)) AS osvojeno FROM ".TIKETI_TABLE." tu LEFT JOIN ".TIKETI_TABLE." td ON tu.id=td.id AND tu.dobitni=1 WHERE tu.tIgracID='$webbet_session[userid]' AND tu.tKoloID='$aktivnokolo[id]'";
        $result=$db->sql_query($sql_poeniak);
        $poeniak_row=$db->sql_fetchrow($result);
        // osvojeno u aktivnom mesecu
        $sql_poeniam="SELECT (SUM(td.dobitak)-SUM(tu.uplata_iznos)) AS osvojeno FROM ".TIKETI_TABLE." tu LEFT JOIN ".TIKETI_TABLE." td ON tu.id=td.id AND tu.dobitni=1 WHERE tu.tIgracID='$webbet_session[userid]' AND '$aktivnimesec[datumod]' <= tu.uplata_vreme AND tu.uplata_vreme <= '$aktivnimesec[datumdo]'";
        $result=$db->sql_query($sql_poeniam);
        $poeniam_row=$db->sql_fetchrow($result);
        // dobitak u svim kolima
        $sql_ukdobitak="SELECT count(dobitni) AS broj, SUM(dobitak) AS osvojeno FROM ".TIKETI_TABLE." t WHERE t.dobitni=1 AND t.tIgracID='$webbet_session[userid]'";
        $result=$db->sql_query($sql_ukdobitak);
        $ukdobitak_row=$db->sql_fetchrow($result);

        $statistika[ukupnopoena]=$stats_row['poeni']+$ukdobitak_row[osvojeno];
        $statistika[aktivnokolo]=$poeniak_row[osvojeno];
        $statistika[aktivnimesec]=$poeniam_row[osvojeno];
        $statistika[dobitnihtiketa]=$ukdobitak_row[broj];
        $statistika[aktivnihtiketa]=$stats_row3['broj'];
        echo "<tr>
        <td class=\"srednjaslova\" bgcolor=\"#E9E9F4\">Ukupno poena: $statistika[ukupnopoena]</td>
        <tr>
        <td class=\"srednjaslova\" bgcolor=\"#E9E9F4\">Osvojeno u aktivnom kolu: $statistika[aktivnokolo]</td>
        </tr>
        <tr>
        <td class=\"srednjaslova\" bgcolor=\"#E9E9F4\">Osvojeno u aktivnom mesecu: $statistika[aktivnimesec]</td>
        </tr>
        <tr>
        <td class=\"srednjaslova\" bgcolor=\"#E9E9F4\">Dobitnih tiketa: $statistika[dobitnihtiketa]</td>
        </tr>
        <tr>
        <td class=\"srednjaslova\" bgcolor=\"#E9E9F4\">Aktivnih tiketa: $statistika[aktivnihtiketa]</td>
        </tr>";
 
	echo '<!-- INFOBEGIN--><tr><td class="srednjaslova"><b>NAPOMENA</b>:<br>';
	include($webbet_root_path . 'kladtekst.inc');
	echo '<!-- INFOEND--></td></tr>
	<!-- INFO2BEGIN--><tr><td class="srednjaslova"><font color="#990000"><b>VAZNO</b></font>:<br>';
	include($webbet_root_path . 'kladtekst2.inc');
	echo '<!-- INFO2END--></td></tr>';
      echo '</table>
            <!-- MAIN CONTENT END-->
          </td>
        </tr>
      </table> 
      <!-- MAIN CELL END-->
    </td>
  </tr>
</table>';
}
?>

</body>
</html>