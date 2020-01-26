<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
  <title>VAOC : Cinematique</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
  <meta name="Description" content=""/>
  <meta name="Keywords" content=""/>
  <meta name="Identifier-URL" content="http://vaoc.free.fr/vaoc/vaoccinematiquectoire.php"/>
  <meta name="revisit-after" content="31"/>
  <meta name="Copyright" content="copyright armand BERGER"/>
	<link rel="stylesheet" type="text/css" href="vaoc.css"/>
    <style type="text/css"> 
	body
	{
		color : black;
		background-color:#434223; 
		background-image:url(images/fondcinema.png);
	}
</style> 
<?php
	//phpinfo();
	require("vaocbase.php");//include obligatoire pour l'execution
	require("vaocfonctions.php");//include obligatoire pour l'executoion

	//converti toutes les variables REQUEST en variables du meme nom
	extract($_REQUEST,EXTR_OVERWRITE);

	//connection � la base
	$db = @db_connect();

	//fixe le fran�ais comme langue pour les dates
	$requete="SET lc_time_names = 'fr_FR'";
	mysql_query($requete,$db);

	//recherche de l'heure de d�but de partie
	$requete="SELECT I_HEURE_INITIALE";
	$requete.=" FROM tab_vaoc_partie, tab_vaoc_jeu";
	$requete.=" WHERE tab_vaoc_partie.ID_PARTIE=".$id_partie;
	$requete.=" AND tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
	//echo $requete;
	$res_heure_initiale = mysql_query($requete,$db);
	$row_heure_initiale = mysql_fetch_object($res_heure_initiale);
	$heureInitiale = $row_heure_initiale->I_HEURE_INITIALE;
?>
<script type="text/javascript">
function callRetourQG()
{
    document.getElementById("principal").action="vaocqg.php";
    document.getElementById("principal").target="_self";
    document.getElementById("principal").submit();
}

function callVictoire()
{
 	formPrincipale=document.getElementById("principal");
 	formPrincipale.action="vaocvictoire.php";
 	formPrincipale.target="_self";
 	formPrincipale.submit();
}

function callBataille(id)
{	
  	document.getElementById("id_bataille").value=id;
    document.getElementById("principal").action="vaocbataille.php";
    document.getElementById("principal").target="_blank";
    document.getElementById("principal").submit();
}

function onOff(id, on) {
    var elem, vis;
    if (document.getElementById) // this is the way the standards work    
        elem = document.getElementById(id);
    else if (document.all) // this is the way old msie versions work      
        elem = document.all[id];
    else if (document.layers) // this is the way nn4 works    
        elem = document.layers[id];

    vis = elem.style;  // if the style.display value is blank we try to figure it out here  
    if (on == 'O')
        vis.display = 'block';
    else
        vis.display = 'none';
}

function GetVendorPrefix(arrayOfPrefixes) {
    var tmp = document.createElement("div");
    var result = "";
    result = null;
    var i = 0;
    while (i < arrayOfPrefixes.length && result == null) {
        if (typeof tmp.style[arrayOfPrefixes[i]] != 'undefined') { result = arrayOfPrefixes[i]; }
        ++i;
    }
    return result;
}

var transformPrefix = GetVendorPrefix(["transform", "msTransform", "MozTransform", "WebkitTransform", "OTransform"]);
var transitionPrefix = GetVendorPrefix(["transition", "msTransition", "MozTransition", "WebkitTransition", "OTransition"]);
var animationPrefix = GetVendorPrefix(["animation", "msAnimation", "MozAnimation", "WebkitAnimation", "OAnimation"]);
var gridPrefix = GetVendorPrefix(["gridRow", "msGridRow", "MozGridRow", "WebkitGridRow", "OGridRow"]);
var hyphensPrefix = GetVendorPrefix(["hyphens", "msHyphens", "MozHyphens", "WebkitHyphens", "OHyphens"]);
var columnPrefix = GetVendorPrefix(["columnCount", "msColumnCount", "MozColumnCount", "WebkitColumnCount", "OColumnCount"]);

function GEBTN$$(e, p) { return p.getElementsByTagName(e) }
var $positionFilm = 0;
var $longueurFilm = 0;
var $heureInitiale = 0;
var $fonctionAvanceFilm = null;
var $vitesseFilm = 3000;

    function initialisationFilm(premiereHeure) {
        //liste des images
        var boiteImages = cinema.getElementsByTagName('ul')[0];
        var listeImages = GEBTN$$('li', boiteImages);
        $longueurFilm = listeImages.length;
        $heureInitiale = premiereHeure;

        boiteImages.style.left = 0;
        boiteImages.style.height = listeImages[0].offsetHeight+ 'px'; // this.largeur + 'px';
        boiteImages.style.overflow = 'hidden';

        //on duplique la premiere image en tete de liste, toutes les images suivent
        boiteImages.insertBefore(listeImages[0].cloneNode(true), listeImages[0]);

        //liste des sous-titres
        var boiteImagesSousTitre = cinemaSousTitre.getElementsByTagName('ul')[0];
        var listeImagesSousTitre = GEBTN$$('li', boiteImagesSousTitre);
        //calcul de la hauteur de texte necessaire pour afficher tous les messages
        var iMaxHeight=0;
        var zLgTexte = document.getElementById("zoneLgTexte");
        for (var i=0; i<listeImagesSousTitre.length; i++)
        {
	        zLgTexte.innerHTML = listeImagesSousTitre[i].innerHTML;
			var iHeight = zLgTexte.clientHeight + 1;
			//var iWidth = zLgTexte.clientWidth + 1;
			//alert ("iHeight="+iHeight+" iWidth="+iWidth);
			if (iHeight>iMaxHeight) {iMaxHeight=iHeight;}
        }
                
        boiteImagesSousTitre.style.left = 0;
        boiteImagesSousTitre.style.height = iMaxHeight+'px';
        //alert (boiteImagesSousTitre.style.height);
        boiteImagesSousTitre.style.overflow = 'hidden';
        //on cache le paragraphe de calcul, il n'est plus necessaire
        //zLgTexte.innerHTML="coucou";
        zLgTexte.style.display = "none";
                        
        //on duplique la premi�re image en t�te de liste, toutes les images suivent
        boiteImagesSousTitre.insertBefore(listeImagesSousTitre[0].cloneNode(true), listeImagesSousTitre[0]);

        positionFilm($positionFilm);
        demarrerFilm();
    }

    function avanceFilm() {
        var position = $positionFilm >= $longueurFilm -1 ? 0 : $positionFilm + 1;
        positionFilm(position);
    }

    function reculeFilm() {
        var position = $positionFilm <= 0 ? $longueurFilm - 1 : $positionFilm - 1;
        positionFilm(position);
    }

    function demarrerFilm() {
        if (null != $fonctionAvanceFilm) {
            avanceFilm();//pour �viter que cela n'avance d'un au demarrage
        }
        $fonctionAvanceFilm = setTimeout("demarrerFilm()", $vitesseFilm);	        
    }

    function ralentirFilm() {
        $vitesseFilm = $vitesseFilm + 1000;
    }

    function accelererFilm() {
        $vitesseFilm = ($vitesseFilm>1000) ? $vitesseFilm - 1000 : 1000;
    }

    function positionFilm(position) {
        var boiteImages = cinema.getElementsByTagName('ul')[0];
        var listeImages = GEBTN$$('li', boiteImages);

        var boiteImagesSousTitre = cinemaSousTitre.getElementsByTagName('ul')[0];
        var listeImagesSousTitre = GEBTN$$('li', boiteImagesSousTitre);
        
        //on supprime la pr�c�dente image et le titre
        boiteImages.removeChild(listeImages[0]);
        boiteImagesSousTitre.removeChild(listeImagesSousTitre[0]);

        //on duplique la nouvelle en t�te de liste
        boiteImages.insertBefore(listeImages[position].cloneNode(true), listeImages[0]);
        boiteImagesSousTitre.insertBefore(listeImagesSousTitre[position].cloneNode(true), listeImagesSousTitre[0]);
        //boiteImages.replaceChild(listeImages[position], listeImages[0]);

        //mise � jour du jour et de la date correspondante
        var jour = Math.floor((position + $heureInitiale) / 24);
        var heure = (position + $heureInitiale) - jour * 24;
        //champDate.innerHTML = "pos=" + position + " hi=" + $heureInitiale + " j=" + jour + " h=" + heure + " lglisteImages=" + listeImages.length;

        var i;
        var listeJour = GEBTN$$('td', tablePaginationJour);
        //var listeHeure = GEBTN$$('li', pisteHeure);
        //mise � jour du jour
        for (i = 0; i < listeJour.length; i++) {
            if (i == jour) {
                listeJour[i].className = 'cinematiqueSelection';
            }
            else {
                listeJour[i].className = 'cinematique';
            }
        }

        //mise � jour de l'heure
        /*
        for (i = 0; i < listeHeure.length; i++) {
            if (i == heure) {
                listeHeure[i].innerHTML = '<span class="paginationHeureSelection"></span>';
            }
            else {
                listeHeure[i].innerHTML = '<span class="paginationHeureS"></span>';
            }
        }
        */
        //alert("heureCourante =" + heureCourante + "heure=" + heure);
        if (heure>=12) {
            onOff("fondHorloge12", "N");
            onOff("fondHorloge24", "O");
        }
        else {
            onOff("fondHorloge12", "O");
            onOff("fondHorloge24", "N");
        }
        var angleHeure = (360 / 12) * heure;
        var elemHeure = document.getElementById("heureHorloge");
        elemHeure.style[transformPrefix] = 'rotate(' + angleHeure + 'deg)';

        //mise a jour de la date
        //champDate.innerHTML = listeJour[jour].title + " : " + listeHeure[heure].title; //listeDates[position].innerHTML;

        //mise a jour de la position courant sur la pistes des jours/heures
        $positionFilm = position;
    }

    function positionFilmJour(positionJour) {
        clearTimeout($fonctionAvanceFilm);
        var position = positionJour * 24 - $heureInitiale;
        //alert("posJour=" + positionJour + " pos=" + position + " lgFilm=" + $longueurFilm);
        if (position < 0) { position = 0; }
        positionFilm(position);
    }

    function positionFilmHeure(positionHeure) {
        clearTimeout($fonctionAvanceFilm);
        var jour = Math.floor(($positionFilm + $heureInitiale) / 24);
        var heure = ($positionFilm + $heureInitiale) - jour * 24;
        var position = positionHeure + (jour * 24) - $heureInitiale;
        //alert("positionHeure =" + positionHeure + " heure=" + heure + " heure % 12 =" + heure % 12);
        if (positionHeure == heure % 12) {
            //si on clic sur l'heure d�j� s�l�ctionn�e, on avance ou on recule de 12 heures le cadran
            //position = (heure >= 12) ? position = position - 12 : position = position + 12;
            if (heure < 12) position = position + 12;
        }
        if (position < 0) { position = 0; }
        if (position >= $longueurFilm) { position = $longueurFilm - 1; }
        //alert("positionFilm=" + $positionFilm + " posHeure=" + positionHeure + " jour=" + jour + " pos=" + position + " lgFilm=" + $longueurFilm);
        positionFilm(position);
    }

    function auChargement() {
        <?php 
        echo "initialisationFilm($heureInitiale);";
        ?>
    }

    window.onload = auChargement;
</script>
</head>
<body>
	<div  style="color:white";>
<?php
  //pratique pour le debug
	/*
	echo "liste des valeurs transmises dans le post<br/>";
  while (list($name, $value) = each($HTTP_POST_VARS)) {echo "$name = $value<br>\n";}
	echo "liste des valeurs transmises dans le request<br/>";
  while (list($name, $value) = each($_REQUEST)) {echo "$name = $value<br>\n";}
	*/

	//recherche du repertoire des images
  	$requete="SELECT S_REPERTOIRE, ID_VICTOIRE ";
  	$requete.="FROM tab_vaoc_partie ";
  	$requete.="WHERE ID_PARTIE=".$id_partie;
	//echo $requete."<br/>";
  	$res_repertoire = mysql_query($requete,$db);
    $row_repertoire = mysql_fetch_object($res_repertoire);
	$repertoire = $row_repertoire->S_REPERTOIRE;
	$idVictoire = $row_repertoire->ID_VICTOIRE;
	//echo "idVictoire=".$idVictoire."<br/>";
	?>
</div>

<form method="post" id="principal" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<div id="pageprincipale" class="cinema">
<?php 
//champ cach�s
echo "<input id='id_bataille' name='id_bataille' type='hidden' value='-1' />";//pour l'appel au detail d'une bataille
if(FALSE==empty($id_login))
{
	echo "<input id='id_login' name='id_login' type='hidden' value='".$id_login."' />";
	echo "<input id='id_nation' name='id_nation' type='hidden' value=\"".$id_nation."\" />";
	echo "<input id='liste_roles' name='liste_roles' type='hidden' value=\"".$liste_roles."\" />";//pour le retour QG
}
echo "<input id='id_partie' name='id_partie' type='hidden' value='".$id_partie."' />";

if ($idVictoire<0)
{
	echo "<h1>VAOC : La partie en cours n'est pas termin�e</h1>";
	echo "</form></body></html>";
	return;//on s'arrete l� !
}

//titres et contexte 
$requete="SELECT tab_vaoc_partie.S_NOM AS NOM_PARTIE, tab_vaoc_jeu.S_NOM AS NOM_JEU, DT_INITIALE, DT_TOUR,";
$requete.=" DATE_FORMAT(DT_INITIALE,'%W %e %M %Y %H:%i') AS DATE_DEBUT, DATE_FORMAT(DT_TOUR,'%W %e %M %Y %H:%i') AS DATE_FIN,";
$requete.=" DATEDIFF(DT_TOUR, DT_INITIALE) AS DUREE_NB_JOURS";
$requete.=" FROM tab_vaoc_partie, tab_vaoc_jeu";
$requete.=" WHERE tab_vaoc_partie.ID_PARTIE=".$id_partie;
$requete.=" AND tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
//echo $requete;
$res_partie = mysql_query($requete,$db);
$row_partie = mysql_fetch_object($res_partie);
echo "<h1>".$row_partie->NOM_JEU." - ".$row_partie->NOM_PARTIE."</h1>";
echo "<h1>".$row_partie->DATE_DEBUT." - ".$row_partie->DATE_FIN."</h1>";

//titre de l'unite en cours
$requete="SELECT tab_vaoc_pion.S_NOM, tab_vaoc_pion.B_QG";
$requete.=" FROM tab_vaoc_pion";
$requete.=" WHERE tab_vaoc_pion.ID_PARTIE=".$id_partie;
$requete.=" AND tab_vaoc_pion.ID_PION=".$id_cinematique_pion;
$res_pions = mysql_query($requete,$db);
//echo $requete;
$row_pion = mysql_fetch_object($res_pions);
echo "<h1>Le film de la campagne pour ".$row_pion->S_NOM."</h1>";
?>
    <a id="ralentifilm" href="nojs.htm" class="buttonqg" onclick="javascript:ralentirFilm(); return false;">
    	<img alt='aide' id="ImgCinemaMoins" src='images/cinemamoins.png' />
    </a>
    <a id="bougefilm" href="nojs.htm" class="buttonqg" onclick="javascript:demarrerFilm(); return false;">
    	<img alt='aide' id="ImgCinema" src='images/cinema.png' />
    </a>
    <a id="accelerefilm" href="nojs.htm" class="buttonqg" onclick="javascript:accelererFilm(); return false;">
    	<img alt='aide' id="ImgCinemaPlus" src='images/cinemaplus.png' />
    </a>
<table summary="cadre general" id= "tableau_general" class="generalcinematique"  border="0">
<tr><td>
    <table id="tablePaginationJour" class="cinematique">
<?php
	//$dateDebut = strtotime($row_partie->DT_INITIALE);
	//$dateFin = new DateTime($row_partie->DT_TOUR);
	//$dateIntervalle = $dateFin - $dateDebut;
	//$dateIntervalle = $row_partie->DT_TOUR - $row_partie->DT_INITIALE;
	//echo "dateIntervalle=".$dateIntervalle;
	//$jours = ($dateIntervalle/(60*60*24))%365;
	$jours = $row_partie->DUREE_NB_JOURS;
	//echo "jours=".$jours;
	
	for ($i=0; $i<=$jours; $i++)
	{
		echo "<tr><td id=\"tablePaginationJour".$i."\""; 
		echo "onclick=\"positionFilmJour(".$i.")\" class=\"cinematiqueSelection\">";
		//$dateCourante->add(new DateInterval('P1D'));//ajout d'une journee -> inutilisable car fait tous les affichages en anglais
		$requete="SELECT DATE_FORMAT(DATE_ADD(DT_INITIALE, INTERVAL ".$i." DAY),'%W %e %M %Y') AS DATE_CINEMATIQUE";
		$requete.=" FROM tab_vaoc_partie, tab_vaoc_jeu";
		$requete.=" WHERE tab_vaoc_partie.ID_PARTIE=".$id_partie;
		$requete.=" AND tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
		//echo $requete;
		$res_date = mysql_query($requete,$db);
		$row_date = mysql_fetch_object($res_date);
		
		echo $row_date->DATE_CINEMATIQUE."</td></tr>";
	}
?>
    </table>
	</td>
    <td>
    <div id="horloge" style="width: 189px ; height: 189px; margin-left: auto ; margin-right: auto ;">
        <map id="horlogeMap" name="horlogeMap"> 
			<area shape="poly" onclick="javascript:positionFilmHeure(0);" title="12" alt="12" coords="95,95,70,0,120,0" /> 
			<area shape="poly" onclick="javascript:positionFilmHeure(1);" title="1"  alt="1"  coords="95,95,120,0,188,0" /> 
			<area shape="poly" onclick="javascript:positionFilmHeure(2);" title="2"  alt="2"  coords="95,95,188,0,188,70" /> 
			<area shape="poly" onclick="javascript:positionFilmHeure(3);" title="3"  alt="3"  coords="95,95,188,70,188,120" /> 
			<area shape="poly" onclick="javascript:positionFilmHeure(4);" title="4"  alt="4"  coords="95,95,188,120,188,188" /> 
			<area shape="poly" onclick="javascript:positionFilmHeure(5);" title="5"  alt="5"  coords="95,95,188,188,120,188" /> 
			<area shape="poly" onclick="javascript:positionFilmHeure(6);" title="6"  alt="6"  coords="95,95,120,188,70,188" /> 
			<area shape="poly" onclick="javascript:positionFilmHeure(7);" title="7" alt="7" coords="95,95,70,188,0,188" />
			<area shape="poly" onclick="javascript:positionFilmHeure(8);" title="8" alt="8" coords="95,95,0,188,0,120" />
			<area shape="poly" onclick="javascript:positionFilmHeure(9);" title="9" alt="9" coords="95,95,0,120,0,70" />
			<area shape="poly" onclick="javascript:positionFilmHeure(10);" title="10" alt="10" coords="95,95,0,70,0,0" />
			<area shape="poly" onclick="javascript:positionFilmHeure(11);" title="11" alt="11" coords="95,95,0,0,70,0" /> 
        </map> 
        <div id="fondHorloge"><img id="fondHorloge12" usemap="#horlogeMap" width="189px" height="189px" src='images/fondHorloge.png' alt="fond Horloge"/></div>
        <div id="fondHorloge2"><img id="fondHorloge24" usemap="#horlogeMap" width="189px" height="189px" src='images/fondHorloge2.png' alt="fond Horloge" style="display:none;"/></div>
        <div id="heureHorloge"><img usemap="#horlogeMap" src="images/aiguilleHeure.png" width="189px" height="189px" alt="petite aiguille"/></div>
        <div id="minuteHorloge"><img usemap="#horlogeMap" src="images/aiguilleMinute.png" width="189px" height="189px" alt="grande aiguille"/></div>
     </div>
    </td>
</tr>
</table>
	<div id="cinema">
		<ul class="boiteImage">
<?php
		$requete="SELECT I_NOMBRE_TOURS, S_REPERTOIRE, DT_INITIALE";
		$requete.=" FROM tab_vaoc_partie, tab_vaoc_jeu";
		$requete.=" WHERE tab_vaoc_partie.ID_PARTIE=".$id_partie;
		$requete.=" AND tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
		//echo $requete;
		$res_nb_tours = mysql_query($requete,$db);
		$row_nb_tours = mysql_fetch_object($res_nb_tours);
		$nb_tours = $row_nb_tours->I_NOMBRE_TOURS;
		//echo "nb_tours=".$nb_tours;
		$barrive=0;//indique si l'unite est deja arrivee sur le champ de bataille, c'est a dire, une image a deja ete presente
		//affichage de toutes les images de vision de l'unite, tour apres tour
		for ($i=0; $i<$nb_tours; $i++)
		{
                    if (1==$row_pion->B_QG)
                    {
			$nomFichier = $row_nb_tours->S_REPERTOIRE."_".$i."/cartepion_".$id_cinematique_pion.".png";                        
                    }
                    else 
                    {
                        //le nom du fichier est suffixé par le propriétaire, mais celui-ci peut changer en cours de partie (reaffaction, blessure, etc.)
                        //echo "glob sur ".$row_nb_tours->S_REPERTOIRE."_".$i."/cartepion_".$id_cinematique_pion."*.png<br/>";
                        $listesFichiers = glob("./".$row_nb_tours->S_REPERTOIRE."_".$i."/cartepion_".$id_cinematique_pion."*.png");
                        //echo "count=".count($listesFichiers);
                        if ($listesFichiers == FALSE)
                        {
                            //echo "glob renvoie false";
                            //print_r(error_get_last());//a partir de php 5 seulement
                        }
                        //print_r(error_get_last());
                        //echo 'Current PHP version: ' . phpversion();
                        if (count($listesFichiers)>0)
                        {
                            //echo "un";
                            //print_r($listesFichiers);
                            //echo "gettype=".gettype($listesFichiers);
                            /*
                            foreach ($listesFichiers as $fiche)
                            {
                                echo "fiche=".$fiche;
                            }
                             * */
                            $nomFichier = $listesFichiers[0];                             
                        }
                        else
                        {
                            $nomFichier = "fichier manquant";
                        }
                        //echo "nomfichier=".$nomFichier;
                    }
                    
                    
                        //echo "nomfichier=".$nomFichier;
			if (file_exists($nomFichier)) 
			{
				echo "<li><img src='".$nomFichier."' ";
				$barrive=1;
			} else {
				if ($barrive==0)
				{
					echo "<li><img src='images/en_renfort.png' ";
				}
				else
				{
					echo "<li><img src='images/cimetiere.png' ";
				}
			}
			$requete="SELECT DATE_FORMAT(DATE_ADD(DT_INITIALE, INTERVAL ".($i+$heureInitiale)." HOUR),'%W %e %M %Y %H:%i') AS DATE_CINEMATIQUE";
			$requete.=" FROM tab_vaoc_partie, tab_vaoc_jeu";
			$requete.=" WHERE tab_vaoc_partie.ID_PARTIE=".$id_partie;
			$requete.=" AND tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
			//echo $requete;
			$res_date = mysql_query($requete,$db);
			$row_date = mysql_fetch_object($res_date);
			
			echo "title='".$row_date->DATE_CINEMATIQUE."' alt='cartepion_".$id_cinematique_pion.".png' style='width:200px; height:200px'/></li>\r\n";
		}
		
		?>
		<!-- 
			<li><img src='images/cartepion_blanche.png' title='base' alt='cartepion_blanche.png' /></li>
			<li><img src='Autriche1809_Testcomplet_3/cartepion_10.png' title='12 Novembre 1808 minuit' alt='cartepion_10.png' /></li>
			<li><img src='Autriche1809_Testcomplet_3/cartepion_101.png' title='12 Novembre 1808 01h00' alt='cartepion_101.png' /></li>
			 -->
		</ul>
	</div>
	<div id="cinemaSousTitre">
		<ul class="boiteImage">
		<?php 
		//affichage de tous les ordre/messages recus ou envoyes, a chaque tour
		for ($i=0; $i<$nb_tours; $i++)
		{
			//Ordres/messages recus par l'unite
			$requete="SELECT tab_vaoc_ordre.I_TYPE, tab_vaoc_ordre.ID_ORDRE, tab_vaoc_ordre.I_TOUR, tab_vaoc_ordre.ID_BATAILLE, tab_vaoc_ordre.I_DISTANCE, tab_vaoc_ordre.I_HEURE";	   
			$requete.=", tab_vaoc_ordre.I_DIRECTION, tab_vaoc_ordre.ID_NOM_LIEU, tab_vaoc_ordre.S_MESSAGE, tab_vaoc_ordre.ID_PION";
			$requete.=", tab_vaoc_ordre.I_DUREE, tab_vaoc_ordre.ID_PION_DESTINATION, PION1.S_NOM AS S_NOM_EMETTEUR, tab_vaoc_modele_pion.ID_NATION";
			$requete.=", DATE_FORMAT(ADDTIME(tab_vaoc_jeu.DT_INITIALE,MAKETIME(tab_vaoc_ordre.I_TOUR+I_HEURE_INITIALE,0,0)),'%W %e %M %Y %H:%i') AS DATE_DEMANDE";
			$requete.=" FROM tab_vaoc_ordre, tab_vaoc_pion AS PION1, tab_vaoc_jeu, tab_vaoc_partie, tab_vaoc_modele_pion";
			$requete.=" WHERE tab_vaoc_ordre.ID_PARTIE=".$id_partie;
			$requete.=" AND tab_vaoc_ordre.ID_PARTIE=PION1.ID_PARTIE";
			$requete.=" AND tab_vaoc_ordre.ID_PION_DESTINATION=PION1.ID_PION";
			$requete.=" AND tab_vaoc_ordre.ID_PION_DESTINATION=".$id_cinematique_pion;
			$requete.=" AND tab_vaoc_ordre.I_TOUR=".$i;
			$requete.=" AND tab_vaoc_partie.ID_PARTIE=tab_vaoc_ordre.ID_PARTIE AND tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
			$requete.=" AND tab_vaoc_modele_pion.ID_PARTIE=tab_vaoc_ordre.ID_PARTIE";
			$requete.=" AND PION1.ID_MODELE_PION=tab_vaoc_modele_pion.ID_MODELE_PION";
			$requete.=" AND I_TYPE<>".ORDRE_ENGAGEMENT;
			$requete.=" AND I_TYPE<>".ORDRE_RETRAIT;
			$requete.=" AND I_TYPE<>".ORDRE_MESSAGE_FORUM;
			//echo $requete;
			$res_ordres_recus = mysql_query($requete,$db);
			$nb_ordres_recus= mysql_num_rows($res_ordres_recus);

			//Ordres/messages envoyes par l'unite
			$requete="SELECT tab_vaoc_ordre.I_TYPE, tab_vaoc_ordre.ID_ORDRE, tab_vaoc_ordre.I_TOUR, tab_vaoc_ordre.ID_BATAILLE, tab_vaoc_ordre.I_DISTANCE, tab_vaoc_ordre.I_HEURE";	   
			$requete.=", tab_vaoc_ordre.I_DIRECTION, tab_vaoc_ordre.ID_NOM_LIEU, tab_vaoc_ordre.S_MESSAGE, tab_vaoc_ordre.ID_PION";
			$requete.=", tab_vaoc_ordre.I_DUREE, tab_vaoc_ordre.ID_PION_DESTINATION, PION1.S_NOM AS S_NOM_EMETTEUR, tab_vaoc_modele_pion.ID_NATION";
			$requete.=", DATE_FORMAT(ADDTIME(tab_vaoc_jeu.DT_INITIALE,MAKETIME(tab_vaoc_ordre.I_TOUR+I_HEURE_INITIALE,0,0)),'%W %e %M %Y %H:%i') AS DATE_DEMANDE";
			$requete.=" FROM tab_vaoc_ordre, tab_vaoc_pion AS PION1, tab_vaoc_jeu, tab_vaoc_partie, tab_vaoc_modele_pion";
			$requete.=" WHERE tab_vaoc_ordre.ID_PARTIE=".$id_partie;
			$requete.=" AND tab_vaoc_ordre.ID_PARTIE=PION1.ID_PARTIE";
			$requete.=" AND tab_vaoc_ordre.ID_PION=PION1.ID_PION";
			$requete.=" AND tab_vaoc_ordre.ID_PION=".$id_cinematique_pion;
			$requete.=" AND tab_vaoc_ordre.I_TOUR=".$i;
			$requete.=" AND tab_vaoc_partie.ID_PARTIE=tab_vaoc_ordre.ID_PARTIE AND tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
			$requete.=" AND tab_vaoc_modele_pion.ID_PARTIE=tab_vaoc_ordre.ID_PARTIE";
			$requete.=" AND PION1.ID_MODELE_PION=tab_vaoc_modele_pion.ID_MODELE_PION";
			//echo $requete;
			$res_ordres_envoyes = mysql_query($requete,$db);
			$nb_ordres_envoyes= mysql_num_rows($res_ordres_envoyes);
			
			if ($nb_ordres_recus>0 || $nb_ordres_envoyes>0)
			{
				$nb_lignes = 0;
				$s_retour="";
				while($row = mysql_fetch_object($res_ordres_recus))
				{					
					$s_retour.=TraduireOrdre($db, $id_partie, $row->I_TYPE, $row->ID_PION, $row->I_HEURE, $row->I_DUREE, $row->ID_NOM_LIEU, 
								$row->I_DISTANCE, $row->I_DIRECTION, $row->ID_BATAILLE, $row->S_MESSAGE, 'R');
				    //$s_retour.= ":R".$row->ID_ORDRE."<br/>";
				    if ($nb_lignes>0) {$s_retour.= "<br/>";}
				    $nb_lignes++;
				}
				while($row = mysql_fetch_object($res_ordres_envoyes))
				{					
					$s_retour.=TraduireOrdre($db, $id_partie, $row->I_TYPE, $row->ID_PION_DESTINATION, $row->I_HEURE, $row->I_DUREE, $row->ID_NOM_LIEU, 
								$row->I_DISTANCE, $row->I_DIRECTION, $row->ID_BATAILLE, $row->S_MESSAGE, 'E');
				    //$s_retour.= ":E".$row->ID_ORDRE."<br/>";
				    if ($nb_lignes>0) {$s_retour.= "<br/>";}
				    $nb_lignes++;
				}
				echo "<li>".$s_retour."</li>";	
				//echo "<li>".$i.":".$nb_ordres_recus."</li>";	
			}
			else
			{
				echo "<li>&nbsp;</li>";				
			}
		}		
		?>
		</ul>
	</div>
</div>
<div style='text-align:center;'>
<?php 
	if(FALSE==empty($id_login))
	{
		//peut survenir pour un anonyme venant voir le bilan
		echo "<a id=\"retourQG\" href=\"\" onclick=\"javascript:callRetourQG();return false;\">";
		echo "<img alt='retour au QG' id=\"btnRetourQG\" src='images/btnRetourQG.png' style=\"text-align:right; vertical-align:top;\"/>";
		echo "</a>";
	}
?>
<a id="retourBilan" href="" onclick="javascript:callVictoire();return false;">
	<img alt='retour au Bilan' id="btnRetourBilan" src='images/btnBilan.png' style="text-align:right; vertical-align:top;"/>
</a>
</div>
<p id="zoneLgTexte" style ="position: absolute; height: auto; width: auto;"></p>
</form>	
</body>
</html>