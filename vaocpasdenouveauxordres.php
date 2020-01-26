<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//FR" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<title>VAOC : PAS DE NOUVEAUX ORDRES</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="Description" content="VAOC - Pas de nouveaux ordres"/>
	<meta name="Keywords" content="VAOC, ordres"/>
	<meta name="Identifier-URL" content="http://vaoc.free.fr/vaoc/vaocpasdenouveauxordres.php"/>
	<meta name="revisit-after" content="31"/>
	<meta name="Copyright" content="copyright armand BERGER"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
        <link rel="icon" type="image/png" href="/images/favicon.png" />
        <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
        <link href="css/vaoc2.css" rel="stylesheet">
        <link href="css/bootstrap.css" rel="stylesheet">
    <style type="text/css"> 
	body
	{
		color : white;
		background-color:#434223; 
		background-image:url(images/fondqg.png);
	}
    </style> 
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.js"></script>
    <!-- https://github.com/steveathon/bootstrap-wysiwyg -->
    <script src="js/bootstrap-wysiwyg.min.js"></script>
    <!-- https://github.com/jeresig/jquery.hotkeys -->
    <script src="js/jquery.hotkeys.js"></script>
    <script type="text/javascript">
	function callPage(nouvellePage)
	{
	 	formPrincipale=document.getElementById("principal");
	 	formPrincipale.action=nouvellePage;
	 	formPrincipale.target="_blank";
	 	formPrincipale.submit();
	}

	function toggleLayer( whichLayer )
	{  
		var elem, vis;  
		if (document.getElementById ) // this is the way the standards work    
			elem = document.getElementById( whichLayer );  
		else if (document.all ) // this is the way old msie versions work      
			elem = document.all[whichLayer];  
		else if (document.layers ) // this is the way nn4 works    
			elem = document.layers[whichLayer];  

		vis = elem.style;  // if the style.display value is blank we try to figure it out here  
		if (vis.display==''&&elem.offsetWidth!=undefined&&elem.offsetHeight!=undefined)    
			vis.display = (elem.offsetWidth!=0&&elem.offsetHeight!=0)?'block':'none';  
		vis.display = (vis.display==''||vis.display=='block')?'none':'block';
	}

	function onOff( id , on)
	{  
		var elem, vis;  
		if (document.getElementById ) // this is the way the standards work    
			elem = document.getElementById( id );  
		else if (document.all ) // this is the way old msie versions work      
			elem = document.all[id];  
		else if (document.layers ) // this is the way nn4 works    
			elem = document.layers[id];  

		vis = elem.style;  // if the style.display value is blank we try to figure it out here  
		if (on=='O')    
			vis.display = 'block';
		else  
			vis.display = 'none';
	}

	function callQuitter()
	{
	 	formPrincipale=document.getElementById("principal");
	 	formPrincipale.action="index.php";
	 	formPrincipale.target="_self";
	 	formPrincipale.submit();
	}
		
</script>
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

	if(empty($id_role) || empty($id_partie))
	{
		$message = "<body> <div class=\"alerte\">Vous ne devez arriver sur ce lieu qu'� partir d'un clic sur le lien d'un message.";
		$message.= "<input alt=\"quitter\" id='id_quitter' name='id_quitter' class=\"gq\" type='image' value='submit' src=\"images/btnQuitter.png\" onclick=\"javascript:location.href='index.php';\" />";
		$message.= "</div></body></html>";
		die($message);		
	}
	
	//recherche du role courant et des elements generiques du jeu et de la partie 
	$requete="SELECT tab_vaoc_role.S_NOM AS NOM_ROLE, tab_vaoc_role.ID_UTILISATEUR, ";
	$requete.=" tab_vaoc_partie.H_JOUR, tab_vaoc_partie.H_NUIT, ";
	$requete.=" tab_vaoc_jeu.S_NOM AS NOM_JEU, tab_vaoc_jeu.S_IMAGE, ";
	$requete.=" DATE_FORMAT(DATE_ADD(tab_vaoc_jeu.DT_INITIALE, INTERVAL tab_vaoc_jeu.I_NOMBRE_TOURS HOUR),'%W %e %M %Y %H:%i') AS DATE_FIN, S_METEO, ";	
	$requete.=" DATE_FORMAT(tab_vaoc_partie.DT_TOUR ,'%W %e %M %Y %H:%i') AS DATE_PARTIE, ";
	$requete.=" tab_vaoc_partie.FL_MISEAJOUR, tab_vaoc_partie.FL_DEMARRAGE, tab_vaoc_partie.ID_VICTOIRE,";
 	$requete.=" tab_vaoc_partie.I_TOUR, tab_vaoc_role.ID_NATION";
	$requete.=" FROM tab_vaoc_role, tab_vaoc_partie, tab_vaoc_jeu";
	$requete.=" WHERE (tab_vaoc_role.ID_PARTIE=tab_vaoc_partie.ID_PARTIE)";
	$requete.=" AND (tab_vaoc_jeu.ID_JEU=tab_vaoc_partie.ID_JEU)";
  	$requete.=" AND tab_vaoc_role.ID_ROLE=".$id_role;
  	$requete.=" AND tab_vaoc_role.ID_PARTIE=".$id_partie;

    //echo "role courant=".$requete;
	$res_role_partie = mysql_query($requete,$db);
	//echo "nb resultats=".mysql_num_rows($res_role_partie);
	$row_role = mysql_fetch_object($res_role_partie);
	
	$id_nation = $row_role->ID_NATION;
	$i_tour = $row_role->I_TOUR;
	$fl_demmarage = $row_role->FL_DEMARRAGE;
	$idVictoire = $row_role->ID_VICTOIRE;
	$id_utilisateur = $row_role->ID_UTILISATEUR;
	if ($idVictoire>=0) {$fl_demmarage = 0;}//la partie n'est plus active
	
	//si la mise a jour est en cours, on ne va pas plus loin
	if (1==$row_role->FL_MISEAJOUR)
	{
		echo "<form method=\"post\" id=\"principal\" action=\"".$_SERVER['PHP_SELF']."\">";
		echo "<h1>Un grognard passe actuellement le balai dans votre tente, merci d'y revenir un peu plus tard.</h1>";
		echo "<div style='text-align:center;'>";
		echo "<input alt=\"retour a� l'ecran g&eacute;n&eacute;ral\" id=\"id_quitter\" name=\"id_quitter\" type=\"image\" value=\"submit\" src=\"images/btnQuitter.png\" onclick=\"javascript:callQuitter();\" />";
		echo "</div>";
		echo "</form></body></html>";		
		die;
	}
		
	//Mise a jour d'ordres termines
	$requete="UPDATE tab_vaoc_role SET B_ORDRES_TERMINES=1 WHERE ";
	$requete.=" tab_vaoc_role.ID_ROLE=".$id_role;
  	$requete.=" AND tab_vaoc_role.ID_PARTIE=".$id_partie;
	//echo $requete;
	$res_b_ordres_termines_update = mysql_query($requete,$db);			

	//on met a jour la date de derniere connexion	
  	$requete="UPDATE tab_utilisateurs SET DT_DERNIERECONNEXION='".date("Y-m-d H:i:s")."' WHERE ID_UTILISATEUR='".$id_utilisateur."'";
	//echo $requete;
	$res_dt_connexion = mysql_query($requete,$db);
	?>
	
<div class="container">
<form method="post" id="principal" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="row row-centered hidden-xs hidden-sm">
        <div class="col-xs-12 col-centered">
        <?php 
	echo "<img alt=\"campagne\" id=\"bandeau\" src=\"images/".$row_role->S_IMAGE."\" />";
        ?>
        </div>
    </div>
    <div class="row row-centered">
        <div class="col-xs-12 col-centered  hidden-xs hidden-sm" style='position:relative; top:-60px; height: 0'>
        <?php
        if ($idVictoire >= 0) //la partie est terminee
        {
            echo "<input alt=\"victoire\" id=\"id_victoire\" name=\"id_victoire\" class=\"btn btn-info\" type=\"image\" value=\"submit\" src=\"images/btnBilan2.png\" onclick=\"javascript:callVictoire();\" />";
            echo "<input alt=\"historique\" id=\"id_historique\" name=\"id_historique\" class=\"btn btn-info\" type=\"image\" value=\"submit\" src=\"images/btnHistorique2.png\" onclick=\"javascript:callHistorique();\" />";
        }
        else
        {
            echo "<input alt=\"aide\" id='id_aide' name='id_aide' class=\"btn btn-info\" type='image' value='submit' src=\"images/btnAide2.png\" onclick=\"javascript: window.open('aide.html'); return false;\" />";
            echo "<input alt=\"retour a l'ecran general\" id='id_quitter' name='id_quitter' class=\"btn btn-default\" type='image' value='submit' src=\"images/btnQuitter2.png\" onclick=\"javascript:callQuitter();\" />";
        }
        ?>
        </div>
    </div>
    <div class="row row-centered">
        <div class="col-xs-12 col-centered">
        <?php
	if ($idVictoire>=0) //la partie est terminee
	{
		echo "<h3>Nous sommes le ".$row_role->DATE_PARTIE." et cette campagne est termin&eacute;e.</h3>";
	}
	else
	{
		//affichage des heures et coucher du soleil
		echo "<h3>Nous sommes le ".$row_role->DATE_PARTIE."</h3>";
		echo "<h3>Cette campagne est pr&eacute;vue jusqu'au ".$row_role->DATE_FIN."</h3>";
		echo "<h3>Actuellement, le temps est ".$row_role->S_METEO.". Il fait jour de ".$row_role->H_JOUR."h00 &agrave; ".$row_role->H_NUIT ."h00.</h3>";
		//echo "<a href=\"nojs.htm\" onclick=\"javascript:callPage('vaoccampagne.php');return false;\" >";	
		//echo "<img alt=\"campagne\" id=\"bandeau\" src=\"images/btnCampagne.png\" />";
		//echo "</a>";
		echo "</h3>";
		echo "<h2>".$row_role->NOM_ROLE.", je comprends que vous n'avez pas de nouveaux ordres &agrave; donner pour l'instant.</h2>";
	}
	?>
        </div>
    </div>
    <div class="row row-centered">
        <div class="col-xs-12 col-centered hidden-md hidden-lg">
        <?php
        if ($idVictoire >= 0) //la partie est terminee
        {
            echo "<input alt=\"victoire\" id=\"id_victoire\" name=\"id_victoire\" class=\"btn btn-info\" type=\"image\" value=\"submit\" src=\"images/btnBilan2.png\" onclick=\"javascript:callVictoire();\" />";
            echo "<input alt=\"historique\" id=\"id_historique\" name=\"id_historique\" class=\"btn btn-info\" type=\"image\" value=\"submit\" src=\"images/btnHistorique2.png\" onclick=\"javascript:callHistorique();\" />";
        }
        else
        {
            echo "<input alt=\"aide\" id='id_aide' name='id_aide' class=\"btn btn-info\" type='image' value='submit' src=\"images/btnAide2.png\" onclick=\"javascript: window.open('aide.html'); return false;\" />";
            echo "<input alt=\"retour a l'ecran general\" id='id_quitter' name='id_quitter' class=\"btn btn-default\" type='image' value='submit' src=\"images/btnQuitter2.png\" onclick=\"javascript:callQuitter();\" />";
        }
        ?>
        </div>
    </div>
</form>
</div>
</body>
</html>