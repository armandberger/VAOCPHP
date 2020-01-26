<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
  <head>
  <title>VAOC : Historique</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
  <meta name="Description" content=""/>
  <meta name="Keywords" content=""/>
  <meta name="Identifier-URL" content="http://waoc.free.fr/vaoc/vaocvictoire.php"/>
  <meta name="revisit-after" content="31"/>
  <meta name="Copyright" content="copyright armand BERGER"/>
  <link rel="stylesheet" type="text/css" href="vaoc.css"/>
  <style type="text/css"> 
body
{
    margin : 0 auto; 
    padding : 0; 
	background-color:white; 
	color:black; 
	background-image:url(images/fondhistorique.jpg);
}
</style>
<script type="text/javascript">
function callRetourQG()
{
    document.getElementById("principal").action="vaocqg.php";
    document.getElementById("principal").target="_self";
    document.getElementById("principal").submit();
}

function callAllerALapage(i_page)
{
  	//alert(document.getElementById("pageNum_recus").value);
  	document.getElementById("pageNum_recus").value=i_page;
  	//alert(document.getElementById("pageNum_recus").value);
 	//document.principal.action="vaochistorique.php"; -> ne fonctionne pas !
 	//document.principal.target="_self";
 	//document.principal.submit();
    document.getElementById("principal").action="vaochistorique.php";
    document.getElementById("principal").target="_self";
    document.getElementById("principal").submit();
}

function callBataille(id)
{	
  	document.getElementById("id_bataille").value=id;
    document.getElementById("principal").action="vaocbataille.php";
    document.getElementById("principal").target="_blank";
    document.getElementById("principal").submit();
}

function callVictoire()
{
 	formPrincipale=document.getElementById("principal");
 	formPrincipale.action="vaocvictoire.php";
 	formPrincipale.target="_self";
 	formPrincipale.submit();
}

function callQuitter()
{
 	formPrincipale=document.getElementById("principal");
 	formPrincipale.action="index.php";
 	formPrincipale.target="_self";
 	formPrincipale.submit();
}

function callTri(tri)
{
	//alert(tri);
	//alert(document.getElementById("tri_listeHistorique").value);
	if (document.getElementById("tri_listeHistorique").value==tri)
	{
		if (document.getElementById("ordre_tri_liste").value=="")
		{
			document.getElementById("ordre_tri_liste").value="DESC";
		}
		else
		{
			document.getElementById("ordre_tri_liste").value="";
		}
	}
	else
	{
		document.getElementById("tri_listeHistorique").value=tri;
		document.getElementById("ordre_tri_liste").value="";
	}
	//alert(document.getElementById("tri_listeHistorique").value);
	
	document.getElementById("principal").action="vaochistorique.php";
	document.getElementById("principal").target="_self";
	document.getElementById("principal").submit();
}
</script>
</head>
<body>
	<div  style="color:white";>
<?php
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

	//connection � la base
	$db = @db_connect();

	//fixe le fran�ais comme langue pour les dates
	$requete="SET lc_time_names = 'fr_FR'";
	mysql_query($requete,$db);

	//recherche du repertoire des images
  	$requete="SELECT S_REPERTOIRE, ID_VICTOIRE, I_TOUR ";
  	$requete.="FROM tab_vaoc_partie ";
  	$requete.="WHERE ID_PARTIE=".$id_partie;
	//echo $requete."<br/>";
  	$res_repertoire = mysql_query($requete,$db);
    $row_repertoire = mysql_fetch_object($res_repertoire);
	$repertoire = $row_repertoire->S_REPERTOIRE."_".$row_repertoire->I_TOUR;
	$idVictoire = $row_repertoire->ID_VICTOIRE;
	//echo "idVictoire=".$idVictoire."<br/>";

	if (TRUE==isset($nombre_messages_pages) && TRUE==is_numeric($nombre_messages_pages))
	{
		//la valeur du nombre de messages � afficher par defaut vient de changer, on met a jour la base s'il s'agit d'un utilisateur identifie
		if(FALSE==empty($id_login))
		{
				$requete="UPDATE tab_utilisateurs SET I_NB_MESSAGES_HISTORIQUE=".$nombre_messages_pages." WHERE S_LOGIN='".trim($id_login)."'";
				//echo $requete;
				$res_nb_messages_update = mysql_query($requete,$db);			
		}
	}
	else
	{
		//sinon on recherche la valeur en base, pour un joueur authentifie
		$nombre_messages_pages =0;
		if(FALSE==empty($id_login))
		{
			$res_nombre_messages_pages = mysql_query("SELECT I_NB_MESSAGES_HISTORIQUE FROM tab_utilisateurs WHERE S_LOGIN='".trim($id_login)."'",$db);
			$row_nombre_messages_pages=mysql_fetch_object($res_nombre_messages_pages);
			$nombre_messages_pages=$row_nombre_messages_pages->I_NB_MESSAGES_HISTORIQUE;
		}
		//s'il n'y en a pas, on prend une valeur moyenne
		if ($nombre_messages_pages <=0)
		{
			$nombre_messages_pages = $listeNombreMessages[count($listeNombreMessages)/2];
		} 
	}
	//echo "nombre_messages_pages=".$nombre_messages_pages;
	?>
</div>

<form method="post" id="principal" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<h1>HISTORIQUE DES EVENEMENTS</h1>
<?php 
if (empty($pageNum_recus))
{
	//lorsque l'on arrive sur la page, on est forcement sur la premi�re page des messages
	$pageNum_recus=0;
}	

if (empty($tri_listeHistorique))
{
	//lorsque l'on arrive sur la page, on est forcement sur la premiere page des messages
        //echo "tri_listeHistorique est vide";
	$tri_listeHistorique="I_TOUR";
	$ordre_tri_liste="";
}	

//champs caches
echo "<input id='id_partie' name='id_partie' type='hidden' value='".$id_partie."' />";
if(FALSE==empty($id_login))
{
	echo "<input id='id_login' name='id_login' type='hidden' value='".$id_login."' />";
	echo "<input id='id_nation' name='id_nation' type='hidden' value=\"".$id_nation."\" />";
	echo "<input id='liste_roles' name='liste_roles' type='hidden' value=\"".$liste_roles."\" />";//pour le retour QG
	echo "<input id='id_role' name='id_role' type='hidden' value='".$id_role."' />";//pour l'appel au detail d'une bataille
}
echo "<input id='id_bataille' name='id_bataille' type='hidden' value='-1' />";//pour l'appel au detail d'une bataille

echo "<input id='pageNum_recus' name='pageNum_recus' type='hidden' value='".$pageNum_recus."' />";
echo "<input id='tri_listeHistorique' name='tri_listeHistorique' type='hidden' value='".$tri_listeHistorique."' />";
echo "<input id='ordre_tri_liste' name='ordre_tri_liste' type='hidden' value='".$ordre_tri_liste."' />";

//affichage du titre
if ($idVictoire<0)
{
	echo "<h1>VAOC : La partie en cours n'est pas termin&eacute;e</h1>";
	echo "</form></body></html>";
	return;//on s'arrete la !
}
$requete="SELECT tab_vaoc_partie.S_NOM AS NOM_PARTIE, tab_vaoc_jeu.S_NOM AS NOM_JEU, DATE_FORMAT(DT_INITIALE,'%W %e %M %Y %H:%i') AS DATE_DEBUT, DATE_FORMAT(DT_TOUR,'%W %e %M %Y %H:%i') AS DATE_FIN";
$requete.=" FROM tab_vaoc_partie, tab_vaoc_jeu";
$requete.=" WHERE tab_vaoc_partie.ID_PARTIE=".$id_partie;
$requete.=" AND tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
$res_partie = mysql_query($requete,$db);
$row_partie = mysql_fetch_object($res_partie);
echo "<h1>".$row_partie->NOM_JEU." - ".$row_partie->NOM_PARTIE."</h1>";
echo "<h1>".$row_partie->DATE_DEBUT." - ".$row_partie->DATE_FIN."</h1>";

?>
<table summary="cadre general" class="historique">
<?php 
	//recherche du pion role du joueur
	/*
	$requete="SELECT tab_vaoc_role.ID_PION ";
	$requete.=" FROM tab_vaoc_role";
	$requete.=" WHERE tab_vaoc_role.ID_ROLE=".$liste_roles%10000;
  	$requete.=" AND tab_vaoc_role.ID_PARTIE=".($liste_roles-$liste_roles%10000)/10000;

    //echo $requete;
	$res_role_partie = mysql_query($requete,$db);
	//echo "nb resultats=".mysql_num_rows($res_role_partie);
	$row_role = mysql_fetch_object($res_role_partie);
	$id_pion_role = $row_role->ID_PION;//pion du r�le courant
	*/

	//recherche du nom des nations
	$requete="SELECT tab_vaoc_nation.S_NOM, tab_vaoc_nation.ID_NATION";
	$requete.=" FROM tab_vaoc_nation";
	$requete.=" WHERE tab_vaoc_nation.ID_PARTIE=".$id_partie;
	$res_nation = mysql_query($requete,$db);
	while($row_nation = mysql_fetch_object($res_nation))
	{
		$nomNation[$row_nation->ID_NATION]=$row_nation->S_NOM;
	}
	
	//affichage des actions = evenements
	$requete="SELECT tab_vaoc_ordre.I_TYPE, tab_vaoc_ordre.ID_ORDRE, tab_vaoc_ordre.I_TOUR, tab_vaoc_ordre.ID_BATAILLE, tab_vaoc_ordre.I_DISTANCE, tab_vaoc_ordre.I_HEURE";	   
	$requete.=", tab_vaoc_ordre.I_DIRECTION, tab_vaoc_ordre.ID_NOM_LIEU, tab_vaoc_ordre.S_MESSAGE";
	$requete.=", tab_vaoc_ordre.I_DUREE, tab_vaoc_ordre.ID_PION_DESTINATION, PION1.S_NOM AS S_NOM_EMETTEUR, tab_vaoc_modele_pion.ID_NATION";
	$requete.=", DATE_FORMAT(ADDTIME(tab_vaoc_jeu.DT_INITIALE,MAKETIME(tab_vaoc_ordre.I_TOUR+I_HEURE_INITIALE,0,0)),'%W %e %M %Y %H:%i') AS DATE_DEMANDE";
	$requete.=" FROM tab_vaoc_ordre, tab_vaoc_pion AS PION1, tab_vaoc_jeu, tab_vaoc_partie, tab_vaoc_modele_pion";
	$requete.=" WHERE tab_vaoc_ordre.ID_PARTIE=".$id_partie;
	$requete.=" AND tab_vaoc_ordre.ID_PARTIE=PION1.ID_PARTIE";
	$requete.=" AND tab_vaoc_ordre.ID_PION=PION1.ID_PION";
	$requete.=" AND tab_vaoc_partie.ID_PARTIE=tab_vaoc_ordre.ID_PARTIE AND tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
	$requete.=" AND tab_vaoc_modele_pion.ID_PARTIE=tab_vaoc_ordre.ID_PARTIE";
	$requete.=" AND PION1.ID_MODELE_PION=tab_vaoc_modele_pion.ID_MODELE_PION";
	//echo $requete;
	
	$res_ordres = mysql_query($requete,$db);
	$nb_ordres_envoyes= mysql_num_rows($res_ordres);
	$offset_envoyes = ($pageNum_recus - 1) * $nombre_messages_pages;
	if ($offset_envoyes<0) {$offset_envoyes=0;}
	//$requete.=" ORDER BY DATE_DEMANDE LIMIT ".$offset_recus.",".$nombre_messages_pages;
	$requete.=" ORDER BY ".$tri_listeHistorique." ".$ordre_tri_liste." LIMIT ".$offset_envoyes.",".$nombre_messages_pages;
	//echo $requete;
	$res_ordres = mysql_query($requete,$db);
	echo "<tr>";
	echo "<th class='historique'><a href='nojs.htm' onclick=\"javascript:callTri('ID_NATION');return false;\">Nation</a></th>\r\n";
	echo "<th class='historique'><a href='nojs.htm' onclick=\"javascript:callTri('S_NOM_EMETTEUR');return false;\">Emetteur</a></th>\r\n";
	echo "<th class='historique'><a href='nojs.htm' onclick=\"javascript:callTri('I_TOUR');return false;\">Envoy&eacute;</a></th>\r\n";
	echo "<th class='historique'>Message</th>\r\n";//je ne peux pas trier par messager il est compose dans le script
	echo "</tr>\r\n";
	while($row = mysql_fetch_object($res_ordres))
	{
		echo "<tr>";
		echo "<td class='historique'>".$nomNation[$row->ID_NATION]."</td>";
		echo "<td class='historique'>".$row->S_NOM_EMETTEUR."</td>";
		echo "<td class='historique'>".$row->DATE_DEMANDE."</td>";
		echo "<td class='historique'>";
		
		$s_retour=TraduireOrdre($db, $id_partie, $row->I_TYPE, $row->ID_PION_DESTINATION, $row->I_HEURE, $row->I_DUREE, $row->ID_NOM_LIEU, 
								$row->I_DISTANCE, $row->I_DIRECTION, $row->ID_BATAILLE, $row->S_MESSAGE, 'E');
		
		echo $s_retour."</td>";
	    echo "</tr>\r\n";
	}

	//gestion des pages
	$maxPage_recus = ceil($nb_ordres_envoyes/$nombre_messages_pages);
	$nav  = '';

	if ($maxPage_recus>1)
	{
		for($page = 1; $page <= $maxPage_recus; $page++)
		{
   			if ($page == $pageNum_recus)
   			{
      			$nav .= " $page "; // no need to create a link to current page
   			}
   			else
   			{
      			$nav .= " <a href='nojs.htm' onclick=\"javascript:callAllerALapage(".$page.");return false;\">$page</a> ";
   			} 
		}
		if ($pageNum_recus > 1)
		{
   			$page  = $pageNum_recus - 1;
      		$prev = " <a href='nojs.htm' onclick=\"javascript:callAllerALapage(".$page.");return false;\">[Pr&eacute;c&eacute;dent]</a> ";
      		$first = " <a href='nojs.htm' onclick=\"javascript:callAllerALapage(1);return false;\">[Premi&egrave;re Page]</a> ";
		} 
		else
		{
   			$prev  = '&nbsp;'; // we're on page one, don't print previous link
   			$first = '&nbsp;'; // nor the first page link
		}

		if ($pageNum_recus < $maxPage_recus)
		{
   			$page = $pageNum_recus + 1;
      		$next = " <a href='nojs.htm' onclick=\"javascript:callAllerALapage(".$page.");return false;\">[Suivant]</a> ";
      		$last = " <a href='nojs.htm' onclick=\"javascript:callAllerALapage(".$maxPage_recus.");return false;\">[Derni&egrave;re Page]</a> ";
		}
		else
		{
   			$next = '&nbsp;'; // we're on the last page, don't print next link
   			$last = '&nbsp;'; // nor the last page link
		}

		//pagination
  		echo "<tr><td colspan='4'>$first . $prev . $nav . $next . $last";
	}
  	else
  	{
  		echo "<tr><td colspan='4'>";
  	}
   	echo "<select id=\"nombre_messages_pages\" name=\"nombre_messages_pages\" size=1 onchange=\"this.form.submit()\">";
   	foreach ( $listeNombreMessages as $listeNombre )
	{
		echo "<option";
		if ($listeNombre==$nombre_messages_pages)
		{
			echo " selected=\"selected\"";
		}
   		printf(" value=\"%u\">%s</option>",$listeNombre,$listeNombre);
	}
   	echo "</select>";
		
	echo "</td></tr>";
	?> 
<tr><td colspan="5" style="text-align: center"><br/>
<a id="id_aide" href="nojs.htm" class="buttonhistorique" onclick="javascript:window.open('aide.html'); return false;">
	<img alt='aide' id="id_aideImage" src='images/btnAide.png' />	
</a>
	<?php 
	if(FALSE==empty($id_login))
	{
		//peut survenir pour un anonyme venant voir le bilan
		echo "<a id=\"retourQG\" class=\"buttonhistorique\" href=\"\" onclick=\"javascript:callRetourQG();return false;\">";
		echo "<img alt='retour au QG' id=\"btnRetourQG\" src='images/btnRetourQG.png' style=\"text-align:right; vertical-align:top;\"/>";
		echo "</a>";
	}
	else
	{
		//dans ce cas on peut revenir � l'accueil
		echo "<a id=\"retourQG\" class=\"buttonhistorique\" href=\"\" onclick=\"javascript:callQuitter();return false;\">";
		echo "<img alt='retour a l'ecran general' id=\"id_quitter\" src='images/btnQuitter.png' style=\"text-align:right; vertical-align:top;\"/>";
		echo "</a>";
	}
?>
<a id="retourBilan" href="" class="buttonhistorique" onclick="javascript:callVictoire();return false;">
	<img alt='retour au Bilan' id="btnRetourBilan" src='images/btnBilan.png' style="text-align:right; vertical-align:top;"/>
</a>
</td></tr>
</table>
<div id="infobulle"></div>
</form>	
</body>
</html>