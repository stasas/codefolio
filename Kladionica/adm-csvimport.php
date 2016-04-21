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
if( isset( $action ) && $action=="csvimport" )
{

	// brise podatke iz tabele
	if($koloid!='' && $table==PAROVI_TABLE)
	{
		$deleterecords = "DELETE FROM $table WHERE tKoloID='$koloid'";
        	$db->sql_query($deleterecords);
	}
	else
	{
        	$deleterecords = "DELETE FROM $table";
        	$db->sql_query($deleterecords);
        }

        // brojaci za uspele i neuspele redove
        $pass = 0; 
        $fail = 0; 

        // fajl koji se unosi
        $filecontents = file ("$csvfile"); 
         
        // svaki red fajla ce biti unesen u tabelu
        for($i=0; $i<sizeof($filecontents); $i++)
        {
            $insertrecord = "INSERT IGNORE INTO $table VALUES ($filecontents[$i])"; 
            $result=$db->sql_query($insertrecord);
            if(!$result)
            {
                echo "Ne mogu da unesem: $insertrecord<br>";
                $fail += 1;
            } 
            else 
            { 
                $pass += 1;
            } 
        } 
	// prikazuje broj ubacenih redova
	$message .= "Tabela $table: Uneto = $pass  nije uneto = $fail<br>";

		echo "<table width=\"50%\" border=1 bordercolor=\"#527bb9\" cellspacing=0 cellpadding=0>
		<tr bgcolor=\"#527bb9\"><td align=center class=\"velikaslova_bela\"><b>CSV import</b></td></tr>
		<tr><td align=center class=\"srednjaslova\"><br>$message<br><a href=\"adm-csvimport.php\" class=\"linkz\">Nazad na CSV import</a><br><br><br></td></tr>
		</table>";
}
else
{
	echo '<table border=1 bgcolor="#FFFFFF" bordercolor="#333399" cellspacing=1 cellpadding=1>
            <tr bordercolor="#527bb9">
             <td align=center nowrap bgcolor="#527bb9" class="velikaslova_bela" width="100%"><b>[ Administracija CSV importa ]</b></td>
            </tr>
            <tr bordercolor="#E9E9F4">
             <form name="csvimport" method=post action="adm-csvimport.php" enctype="multipart/form-data">
             <input type=hidden name="action" value="csvimport">
             <td align=center colspan=6 bgcolor="#E9E9F4" class="srednjaslova">
             <b>Tabela</b>:
             <select name="table">
             <option value="none">Izaberite tabelu</option>
             <option value="'.PAROVI_TABLE.'">Parovi</option>
             <option value="'.KOLA_TABLE.'">Kola</option>
             <option value="'.KLUBOVI_TABLE.'">Klubovi</option>
             <option value="'.TAKMICENJA_TABLE.'">Takmicenja</option>
             </select> <b>Kolo</b>:* <input size=3 name="koloid" type=text>
             <b>Fajl</b>: <input type="file" name="csvfile"> <input name=submit type=submit value=Postavi>
             <br><i>* Kolo popuniti samo ukoliko se salje tabela parovi</i>
             </td>
             </form>
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
