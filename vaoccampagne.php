<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
    <title>VAOC : Campagne</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="Description" content=""/>
    <meta name="Keywords" content="VAOC, Campagne, Scenario"/>
    <meta name="Identifier-URL" content="http://vaoc.free.fr/vaoc/vaoccampagne.php"/>
    <meta name="revisit-after" content="31"/>
    <meta name="Copyright" content="copyright armand BERGER"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
    <link rel="icon" type="image/png" href="/images/favicon.png" />
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" type="text/css" href="css/vaoc2.css"/>
    <link href="css/bootstrap.css" rel="stylesheet"/>
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
</head>
<body>
<?php
	//header("Content-Type:text/html; charset=iso-8859-1");
	require("vaocbase.php");//include obligatoire pour l'execution
	require("vaocfonctions.php");//include obligatoire pour l'executoion

  //pratique pour le debug
	/*
	echo "liste des valeurs transmises dans le post<br/>";
  while (list($name, $value) = each($HTTP_POST_VARS)) {echo "$name = $value<br>\n";}
	echo "liste des valeurs transmises dans le request<br/>";
  while (list($name, $value) = each($_REQUEST)) {echo "$name = $value<br>\n";}
	*/
	//converti toutes les variables REQUEST en variables du meme nom
	extract($_REQUEST,EXTR_OVERWRITE);

	//connection a la base
	$db = @db_connect();
        //mysql_set_charset("utf-8", $db); // version php 5.2.3+ et ça marche pas !!!

	//fixe le francais comme langue pour les dates
	$requete="SET lc_time_names = 'fr_FR'";
	mysql_query($requete,$db);
        mysql_query("SET NAMES 'utf8'");

	if(FALSE==empty($id_login))
	{
		$message="";
  		//on verifie si le login existe
		$res_login = mysql_query("SELECT S_LOGIN, ID_UTILISATEUR FROM tab_utilisateurs WHERE S_LOGIN='".trim($id_login)."'",$db);
		if (mysql_num_rows($res_login)<=0)
  		{
       		$message=$message."Le nom d'utilisateur est inconnu.<br/>";
  		}
		else
		{
	  		$login=mysql_fetch_object($res_login);
	
			$requete="SELECT tab_vaoc_role.ID_ROLE, tab_vaoc_role.ID_PARTIE, tab_vaoc_role.S_NOM AS NOM_ROLE,";
			$requete.=" tab_vaoc_partie.S_NOM AS NOM_PARTIE, DATE_FORMAT(tab_vaoc_partie.DT_TOUR ,'d/%m/%Y %H:%i') AS DATE_PARTIE";
			$requete.=" FROM tab_vaoc_role, tab_vaoc_partie";
			$requete.=" WHERE (tab_vaoc_role.ID_PARTIE=tab_vaoc_partie.ID_PARTIE) AND ID_UTILISATEUR=".$login->ID_UTILISATEUR;
			$requete.=" ORDER BY NOM_ROLE";
			//echo $requete;
			$res_role = mysql_query($requete,$db);
	  		//on vérifie si l'utilisateur a des parties en cours
			if (mysql_num_rows($res_role)<=0)
			{
	        	$message.=$requete."Vous n'&ecirc;tes inscrit &agrave; aucune partie.<br/>";
	      	}
		}
	}
	else
	{
		$message="Erreur, vous devez d'abord vous connectez pour acc&eacute;der &agrave; la page";
	}
		
	if (strlen($message)>0)
	{
		die("<body> <div class=\"alerte\">".$message."</div>");
	}
  
	//recherche du role courant et des elements generiques du jeu et de la partie 
	$requete="SELECT tab_vaoc_role.ID_NATION, tab_vaoc_jeu.ID_JEU, S_REPERTOIRE, ";
	$requete.=" tab_vaoc_jeu.I_LEVER_DU_SOLEIL, tab_vaoc_jeu.I_COUCHER_DU_SOLEIL, ";
	$requete.=" tab_vaoc_jeu.S_NOM AS NOM_JEU, tab_vaoc_jeu.S_IMAGE, ";
	$requete.=" DATE_FORMAT(DATE_ADD(tab_vaoc_jeu.DT_INITIALE, INTERVAL tab_vaoc_jeu.I_HEURE_INITIALE HOUR),'%d/%m/%Y %H:%i') AS DATE_DEBUT, ";
	$requete.=" DATE_FORMAT(DATE_ADD(tab_vaoc_jeu.DT_INITIALE, INTERVAL tab_vaoc_jeu.I_NOMBRE_TOURS HOUR),'%d/%m/%Y %H:%i') AS DATE_FIN ";	
	$requete.=" FROM tab_vaoc_role, tab_vaoc_partie, tab_vaoc_jeu";
	$requete.=" WHERE (tab_vaoc_role.ID_PARTIE=tab_vaoc_partie.ID_PARTIE)";
	$requete.=" AND (tab_vaoc_jeu.ID_JEU=tab_vaoc_partie.ID_JEU)";
  	$requete.=" AND tab_vaoc_role.ID_ROLE=".$liste_roles%10000;
  	$requete.=" AND tab_vaoc_role.ID_PARTIE=".($liste_roles-$liste_roles%10000)/10000;
    //echo $requete;
	$res_role_partie = mysql_query($requete,$db);
	//echo "nb resultats=".mysql_num_rows($res_role_partie);
	$row_role = mysql_fetch_object($res_role_partie);
	$id_jeu = $row_role->ID_JEU;
	$id_nation = $row_role->ID_NATION;	
	$repertoire = $row_role->S_REPERTOIRE;
?>
<form method="post" id="principal" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div class="container">
    <div class="row row-centered">
        <div class="col-xs-12 col-centered">
<?php 
	echo "<div style='text-align:center;'>";
	echo "<img alt=\"campagne\" id=\"bandeau\" src=\"images/".$row_role->S_IMAGE."\" />";
	echo "</div>";

	echo "<h3>La campagne est pr&eacute;vue entre le ".$row_role->DATE_DEBUT." et le ".$row_role->DATE_FIN ."</h3>";
	echo "<h3>En cette saison il fait jour de ".$row_role->I_LEVER_DU_SOLEIL."h00 &agrave; ".$row_role->I_COUCHER_DU_SOLEIL ."h00.</h3>";
	
 	echo "<h2>Dossier de campagne</h2>";
?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
<?php 
 	//recherche du descriptif
	$requete="SELECT S_TEXTE, S_CARTE ";
	$requete.=" FROM tab_vaoc_campagne";
	$requete.=" WHERE ID_JEU=".$id_jeu;
  	$requete.=" AND ID_NATION=".$id_nation;
    //echo $requete;
  	$res_campagne = mysql_query($requete,$db);
	$row_campagne = mysql_fetch_object($res_campagne);
	//affichage du descriptif
	echo $row_campagne->S_TEXTE; 
?>
        </div>
    </div>
<!-- 	
	<h3>Contexte</h3>
	<p class="campagne">Profitant du fait que la plupart des troupes combattent en Espagne, ce traitre d'Autrichien, soutenu par le non moins infâme britannique a décidé de se mettre en campagne
	pour s'emparer de l'Allemagne.<br/> 
	Il vous faut donc, dans l'urgence, mettre sur pied une armée et faire passer à l'Autriche le goût des coups fourrés. Cela ne sera guère facile car vos meilleures unités, comme la Garde, ne sont pas disponibles.
	</p>
	<h3>L'armée : composition et déploiement initial</h3>	
	<p class="campagne">Elle est décomposée en cinq corps mais tous ne sont pas encore disponibles. 
	Sa disposition est strictement laissée au choix de l'Etat-major autrichien 
	(durant la réunion d'état-major initiale, les troupes seront positionnées autour de Passau mais elles seront repositionnées, là où l'indiquera Charles en début de campagne). 
	Elle peut se situer n'importe où au sud de l'Isar et au sud de la ligne Deggendorf-Passau. 
	</p>
	<p class="campagne">Les corps disponibles sont les suivants :<br/>
	Oudinot, 2 divisions de recrues, quelques cuirassiers, 21000 hommes, encore en dehors de la carte. Oudinot arrive le 17 avril à Pfaffenhausen.<br/>
	Davout, la fleur de cette nouvelle armée, 4 divisions d'infanterie plus une de réserve et une de cuirassiers, 53000 hommes à Ratisbonne et ses environs.<br/>
	Masséna, très hétérogène, 4 divisions, 37000 hommes. Masséna arrive le 18 avril à Pfaffenhausen.<br/>
	Lefebvre, composé uniquement de Bavarois en 3 divisions, 23000 hommes dispoées à Munich, Freisig et Moosburg.<br/>
	Vandame, que des Wurtembourgeois, 12000, plus 5000 cuirassiers de Nansouty, arrive le 18 avril à Ingolstat.<br/>
	L'Empereur est encore à l'ouest, arrive à Ingolstat le 19 avril.</p>	
	<h3>Informations sur l'ennemi</h3>	
	<p class="campagne">Vos rapports d'espions indiquent que l'Archiduc Charles a envahi la Bavière par Passau et Braunau à la tête de nombreux corps d'armée, 
	et les derniers rapports font état de pénétrations de troupes par les monts de Bohème, au nord-est de Ratisbonne. </p>
	<h3>Ravitaillement</h3>	
	<p class="campagne">Vous disposez de centre de ravitaillement à Pfaffenhausen, Dachau et Ingolstat. Il est donc improbable que vos troupes se retrouvent isolées. Attention quand même à Ratisbonne !</p>
	<h3>Objectifs de la campagne</h3>
	<p class="campagne">Vous devez en imposer à ces Autrichiens belliqueux en leur montrant, une fois de plus, que vous êtes le maître de l'Europe.<br/>
	  Pour cela il faudra battre son armée et tenir la région. Vous devez donc démoralisez plus de corps qu'il ne vous en démoralise.</p>	
	<p class="campagne">Par ailleurs, vous devez tenir également les lieux stratégiques suivants (un point est équivalent à un corps démoralisé) :<br/>
	La forteresse de Ratisbonne (Sud: 1 point, Nord: 2 points)<br/>
	Munich : 2 points.</p>
 -->	
    <div class="row row-centered">
        <div class="col-xs-12 col-centered">
	<h2>Carte g&eacute;n&eacute;rale de la campagne</h2>
	<p class="campagne">La carte g&eacute;n&eacute;rale de la campagne est disponible en appuyant sur ce bouton :
	<?php 
                echo "<input alt=\"carte source\" id='id_chargement_carte' name='id_chargement_carte' class=\"btn btn-info\" type='image' value='submit' src=\"images/btnCarte2.png\" onclick=\"javascript:window.open('".$repertoire."_carte/".$row_campagne->S_CARTE."'); return false;\" />";
	?>
	</p>
        </div>
    </div>
    <div class="row row-centered">
        <div class="col-xs-12 col-centered">
	<h2>Fatigue quotidienne des unit&eacute;s suivant leur temps de d&eacute;placement sur une journ&eacute;e</h2>
	<table  class="campagne"><tr><th rowspan="2" class="campagne">&nbsp;</th><th colspan="20" class="campagne">Dur&eacute;e en heures</th></tr>
	<tr>
		<th class="campagnedroite">1</th><th class="campagnedroite">2</th><th class="campagnedroite">3</th><th class="campagnedroite">4</th><th class="campagnedroite">5</th><th class="campagnedroite">6</th><th class="campagnedroite">7</th><th class="campagnedroite">8</th><th class="campagnedroite">9</th><th class="campagnedroite">10</th>
		<th class="campagnedroite">11</th><th class="campagnedroite">12</th><th class="campagnedroite">13</th><th class="campagnedroite">14</th><th class="campagnedroite">15</th><th class="campagnedroite">16</th><th class="campagnedroite">17</th><th class="campagnedroite">18</th><th class="campagnedroite">19</th><th class="campagnedroite">20</th>
	</tr>
	<tr>
		<td class="campagnegras">Infanterie</td><td class="campagnedroite">0</td><td class="campagnedroite">0</td><td class="campagnedroite">0</td><td class="campagnedroite">0</td><td class="campagnedroite">1</td><td class="campagnedroite">1</td><td class="campagnedroite">2</td><td class="campagnedroite">2</td><td class="campagnedroite">3</td><td class="campagnedroite">4</td>
		<td class="campagnedroite">5</td><td class="campagnedroite">6</td><td class="campagnedroite">8</td><td class="campagnedroite">10</td><td class="campagnedroite">12</td><td class="campagnedroite">14</td><td class="campagnedroite">16</td><td class="campagnedroite">18</td><td class="campagnedroite">20</td><td class="campagnedroite">22</td>
	</tr>
	<tr>
		<td class="campagnegras">Cavalerie</td><td class="campagnedroite">1</td><td class="campagnedroite">1</td><td class="campagnedroite">1</td><td class="campagnedroite">1</td><td class="campagnedroite">1</td><td class="campagnedroite">2</td><td class="campagnedroite">2</td><td class="campagnedroite">3</td><td class="campagnedroite">4</td><td class="campagnedroite">5</td>
		<td class="campagnedroite">6</td><td class="campagnedroite">8</td><td class="campagnedroite">10</td><td class="campagnedroite">12</td><td class="campagnedroite">14</td><td class="campagnedroite">16</td><td class="campagnedroite">18</td><td class="campagnedroite">20</td><td class="campagnedroite">22</td><td class="campagnedroite">24</td>
	</tr>
	</table>
	<p  class="commentaire">Nombre auquel il faut ajouter un point de fatigue par heure de marche de nuit et un point de fatigue par heure de combat.</p>	
	<?php 	
	echo "<h2>M&eacute;t&eacute;o pr&eacute;vue</h2>";
 	echo "<table class=\"campagne\"><tr><th class=\"campagne\">M&eacute;t&eacute;o</th><th class=\"campagne\">Probabilit&eacute;</th></tr>";
	$requete="SELECT I_CHANCE, S_NOM ";
	$requete.=" FROM tab_vaoc_meteo";
	$requete.=" WHERE ID_JEU=".$id_jeu;
  	$res_meteo = mysql_query($requete,$db);
  	while($row_meteo = mysql_fetch_object($res_meteo))
  	{
  		echo "<tr>";
  		echo "<td class=\"campagne\">".$row_meteo->S_NOM."</td>";
  		echo "<td class=\"campagnedroite\">".$row_meteo->I_CHANCE."</td>";
  		echo "</tr>";
  	}
  	echo "</table>";  	
	
 	echo "<h2>Vitesse de d&eacute;placement des unit&eacute;s</h2>";
 	echo "<table class=\"campagne\">";
 	echo "<tr><th class=\"campagne\" rowspan='2'>Nation</th><th class=\"campagne\" rowspan='2'>Unit&eacute;</th><th class=\"campagne\" rowspan='2'>Terrain</th>";
 	echo "<th class=\"campagne\" rowspan='2'>Temps</th><th class=\"campagne\" colspan='3'>Vitesse (en km/h)</th></tr>";
 	echo "<tr><th class=\"campagne\">Infanterie</th><th class=\"campagne\">Cavalerie</th><th class=\"campagne\">Artillerie</th></tr>";
 	$requete="SELECT S_NATION ";
	$requete.=" FROM tab_vaoc_modele_mouvement";
	$requete.=" WHERE ID_JEU=".$id_jeu;
	$requete.=" GROUP BY S_NATION";
	$requete.=" ORDER BY S_NATION";
	//echo $requete;
	$res_modele_mouvement_nation = mysql_query($requete,$db);
  	while($row_modele_mouvement_nation = mysql_fetch_object($res_modele_mouvement_nation))
  	{
 		$requete="SELECT COUNT(*) AS NB ";
		$requete.=" FROM tab_vaoc_modele_mouvement";
		$requete.=" WHERE ID_JEU=".$id_jeu;
		$requete.=" AND S_NATION='".$row_modele_mouvement_nation->S_NATION."'";
		//echo $requete;
		$res_nb = mysql_query($requete,$db);
		$row_nb = mysql_fetch_object($res_nb);
		echo "<tr><td class=\"campagnegras\" rowspan='".$row_nb->NB."'><p  class=\"campagnevertical\">".$row_modele_mouvement_nation->S_NATION."</p></td>";
		
		//liste des modeles
 		$requete="SELECT S_MODELE ";
		$requete.=" FROM tab_vaoc_modele_mouvement";
		$requete.=" WHERE ID_JEU=".$id_jeu;
		$requete.=" AND S_NATION='".$row_modele_mouvement_nation->S_NATION."'";
		$requete.=" GROUP BY S_MODELE";
		$requete.=" ORDER BY S_MODELE";
		//echo $requete;
		$res_modele_mouvement_modele = mysql_query($requete,$db);
		$premier_modele = true;
	  	while($row_modele_mouvement_modele = mysql_fetch_object($res_modele_mouvement_modele))
	  	{
	 		$requete="SELECT COUNT(*) AS NB ";
			$requete.=" FROM tab_vaoc_modele_mouvement";
			$requete.=" WHERE ID_JEU=".$id_jeu;
			$requete.=" AND S_NATION='".$row_modele_mouvement_nation->S_NATION."'";
			$requete.=" AND S_MODELE='".$row_modele_mouvement_modele->S_MODELE."'";
			//echo $requete;
			$res_nb = mysql_query($requete,$db);
			$row_nb = mysql_fetch_object($res_nb);
			if ($premier_modele){$premier_modele = false;}else {echo "<tr>";}
			echo "<td  class=\"campagnegras\" rowspan='".$row_nb->NB."'><p  class=\"campagnevertical\">".$row_modele_mouvement_modele->S_MODELE."</p></td>";
			
			//liste des terrains
	 		$requete="SELECT S_TERRAIN ";
			$requete.=" FROM tab_vaoc_modele_mouvement";
			$requete.=" WHERE ID_JEU=".$id_jeu;
			$requete.=" AND S_NATION='".$row_modele_mouvement_nation->S_NATION."'";
			$requete.=" AND S_MODELE='".$row_modele_mouvement_modele->S_MODELE."'";
			$requete.=" GROUP BY S_TERRAIN";
			$requete.=" ORDER BY S_TERRAIN";
			//echo $requete;			
			$res_modele_mouvement_terrain = mysql_query($requete,$db);
			$premier_terrain = true;
		  	while($row_modele_mouvement_terrain = mysql_fetch_object($res_modele_mouvement_terrain))
		  	{
		 		$requete="SELECT COUNT(*) AS NB ";
				$requete.=" FROM tab_vaoc_modele_mouvement";
				$requete.=" WHERE ID_JEU=".$id_jeu;
				$requete.=" AND S_NATION='".$row_modele_mouvement_nation->S_NATION."'";
				$requete.=" AND S_MODELE='".$row_modele_mouvement_modele->S_MODELE."'";
				$requete.=" AND S_TERRAIN='".$row_modele_mouvement_terrain->S_TERRAIN."'";
				//echo $requete;				
				$res_nb = mysql_query($requete,$db);
				$row_nb = mysql_fetch_object($res_nb);
				if ($premier_terrain){$premier_terrain = false;}else {echo "<tr>";}
				echo "<td class=\"campagne\" rowspan='".$row_nb->NB."'>".$row_modele_mouvement_terrain->S_TERRAIN."</td>";
				
				//liste des météos
		 		$requete="SELECT S_METEO, I_VITESSE_INFANTERIE, I_VITESSE_CAVALERIE, I_VITESSE_ARTILLERIE ";
				$requete.=" FROM tab_vaoc_modele_mouvement";
				$requete.=" WHERE ID_JEU=".$id_jeu;
				$requete.=" AND S_NATION='".$row_modele_mouvement_nation->S_NATION."'";
				$requete.=" AND S_MODELE='".$row_modele_mouvement_modele->S_MODELE."'";
				$requete.=" AND S_TERRAIN='".$row_modele_mouvement_terrain->S_TERRAIN."'";
				$requete.=" ORDER BY S_METEO";
				//echo $requete;				
				$res_modele_mouvement_meteo = mysql_query($requete,$db);
				$premier_meteo = true;
		  		while($row_modele_mouvement_meteo = mysql_fetch_object($res_modele_mouvement_meteo))
		  		{
					if ($premier_meteo){$premier_meteo = false;}else {echo "<tr>";}
		  			echo "<td  class=\"campagne\">".$row_modele_mouvement_meteo->S_METEO."</td>";
			  		echo "<td class=\"campagnecentre\">".str_replace(".",",",$row_modele_mouvement_meteo->I_VITESSE_INFANTERIE)."</td>";
			  		echo "<td class=\"campagnecentre\">".str_replace(".",",",$row_modele_mouvement_meteo->I_VITESSE_CAVALERIE)."</td>";
			  		echo "<td class=\"campagnecentre\">".str_replace(".",",",$row_modele_mouvement_meteo->I_VITESSE_ARTILLERIE)."</td>";
			  		echo "</tr>";		  			
		  		}
		  	}
	  	}
  	}
  	echo "</table>";  	
	?>
   
        <input alt="aide" id='id_aide' name='id_aide' class="btn btn-info" type='image' value='submit' src="images/btnAide2.png" onclick="javascript: window.open('aide.html'); return false;" />
        &nbsp;<input alt="quitter" id='id_quitter' name='id_quitter' class="btn btn-default" type='image' value='submit' src="images/btnQuitter2.png" onclick="javascript:window.close();" />
        </div>
    </div>
</form>
</div>
</body>
</html>