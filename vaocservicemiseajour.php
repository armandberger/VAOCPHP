<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//FR" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>VAOC : SERVICE DE MISE A JOUR</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="Description" content="VAOC - Pas de nouveaux ordres"/>
	<meta name="Keywords" content="VAOC, ordres"/>
	<meta name="Identifier-URL" content="http://vaoc.free.fr/vaocservicemiseajour.php"/>
	<meta name="revisit-after" content="31"/>
	<meta name="Copyright" content="copyright armand BERGER"/>
</head>
<body>
<?php
    //header("Content-Type:text/html; charset=iso-8859-1");
    require("vaocbase.php");//include obligatoire pour l'execution
    require("vaocfonctions.php");//include obligatoire pour l'executoion

/*	if (!headers_sent())
	{
	 	 echo "!headers_sent()";
    header("Content-type: text/html; charset=ISO-8859-1");
	}*/
	
  //pratique pour le debug
	/**/
	//echo "liste des valeurs transmises dans le request<br/>";
  //while (list($name, $value) = each($_REQUEST)) {echo "$name = $value<br>\n";}
  //while (list($name, $value) = each($_POST)) {echo "$name = $value<br>\n";}
  //while (list($name, $value) = each($_GET)) {echo "$name = $value<br>\n";}
  //while (list($name, $value) = each($_SERVER)) {echo "$name = $value<br>\n";}
  /**/
    //converti toutes les variables REQUEST en variables du meme nom
    extract($_REQUEST,EXTR_OVERWRITE);

    //connection a la base
    $db = @db_connect();

    //fixe le francais comme langue pour les dates
    $requete="SET lc_time_names = 'fr_FR'";
    mysql_query($requete,$db);
    mysql_query("SET NAMES 'utf8'");

    if(empty($id_partie))
    {
        $message = "<body> <div class=\"alerte\">Vous ne devez arriver sur ce lieu qu'a partir d'une execution du programme lui-même.";
        $message.= "<input alt=\"quitter\" id='id_quitter' name='id_quitter' class=\"gq\" type='image' value='submit' src=\"images/btnQuitter.png\" onclick=\"javascript:location.href='index.php';\" />";
        $message.= "</div></body></html>";
        die($message);		
    }

    if(empty($date_prochaintour))
    {
        echo "<div style='text-align:center;'>";
        echo "date du prochain tour non renseignée";
        echo "</div>";
    }
    else
    {
        //UPDATE `tab_vaoc_partie` SET `DT_PROCHAINTOUR` = '2017-12-16 19:00:00' WHERE `tab_vaoc_partie`.`ID_PARTIE` = 8;
        //Mise a jour de la date de fin
        $requete="UPDATE tab_vaoc_partie SET DT_PROCHAINTOUR='".$date_prochaintour."' WHERE ";
        $requete.=" tab_vaoc_partie.ID_PARTIE=".$id_partie;
        echo $requete;
        $res_datefindutour = mysql_query($requete,$db);			
    }

    ?>

</body>
</html>