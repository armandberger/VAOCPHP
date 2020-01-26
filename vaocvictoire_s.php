<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>VAOC : Victoire</title>
        <!-- attention sans la ligne meta qui suit, bootstrap ne fonctionne pas -->
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="Description" content="VAOC, victoire, bilan final"/>
        <meta name="Keywords" content="VAOC, bilan"/>
        <meta name="Identifier-URL" content="http://vaoc.free.fr/vaoc/vaocvictoire.php"/>
        <meta name="revisit-after" content="31"/>
        <meta name="Copyright" content="copyright armand BERGER"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">         
        <link rel="icon" type="image/png" href="/images/favicon.png" />
        <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
        <link href="css/bootstrap4.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="css/vaoc2.css"/>
        <style type="text/css"> 
            body{color: black; font-family:Georgia, Arial, Helvetica, sans-serif; font-size: 14px; text-align: left; background-color:white;}
            h1 {font-size: 30px;text-align: center;}
            h2 {font-size: 20px;text-align: center;}
            h3 {font-size: 16px;text-align: center;}
            a {font-size: 12px;text-align: left; border: none; outline:none; color:black;}
            a:hover {font-weight: bold;}
            img {border: none; outline:none;}

            div.resultat {border: medium solid black}
            div.resultatentete {text-align: center; font-weight: bold;}
            div.total{font-weight: bold; text-align: right; border-top: thin solid black;}
            div.numerique{text-align: right; }

            div.fond {background-image: url(images/fondmarbre.png);}
        </style>
        <script src="js/jquery-3.4.1.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap4.min.js"></script>
        <script src="js/vaoc_s.js"></script>
<script type="text/javascript">
    $(document).ready(fonctionPrete);

    //Mise en place d'une meme hauteur de lignes
    function fonctionPrete() {

        //console.log("ready!");
        var liste0 = $(".victoire0");
        var liste1 = $(".victoire1");
        //console.log("liste0.length = " + liste0.length);
        //console.log("liste1.length = " + liste1.length);
        for (i=0; i< liste0.length; i++)
        {
            //console.log(liste0.eq(i).height());
            //console.log(i + "- avant 0:" + liste0.eq(i).height() + " 1:" + liste1.eq(i).height());
            var maximumHauteur=0;
            if (liste0.eq(i).height() > liste1.eq(i).height())
            {
                maximumHauteur=liste0.eq(i).height();
                //liste1.eq(i).height(liste0.eq(i).height());
                //console.log(liste0.eq(i).height());
            }
            else
            {
                maximumHauteur=liste1.eq(i).height();
                //liste0.eq(i).height(liste1.eq(i).height());
                //console.log(liste1.eq(i).height());
            }
            liste0.eq(i).height(maximumHauteur);
            liste1.eq(i).height(maximumHauteur);
            //console.log(i + "-  apres 0:"+liste0.eq(i).height()+" 1:"+liste1.eq(i).height());
        }
    }
      
function ajusterLongueurTableau()
{
    //alert("ajusterLongueurTableau");
    var divHeight0, divHeight1;
    var obj0 = document.getElementById('joueur0');
    var obj1 = document.getElementById('joueur1');

    if(obj0.offsetHeight)          {divHeight0=obj0.offsetHeight;}
    else if(obj0.style.pixelHeight){divHeight0=obj0.style.pixelHeight;}

    if(obj1.offsetHeight)          {divHeight1=obj1.offsetHeight;}
    else if(obj1.style.pixelHeight){divHeight1=obj1.style.pixelHeight;}

    alert(divHeight0);
    alert(divHeight1);
    if (divHeight0>divHeight1)
    {
        //obj1.height = divHeight0;
        if(obj1.offsetHeight)          {obj1.offsetHeight=divHeight0;}
        else if(obj1.style.pixelHeight){obj1.style.pixelHeight=divHeight0;}
        alert(obj1.offsetHeight);
        alert(obj1.obj1.style.pixelHeight);
    }
    else
    {
        //obj0.style.display='block';
        obj0.style.height = divHeight1;
        /*
        if(obj0.offsetHeight)          {alert("offsetHeight");obj0.offsetHeight=divHeight1;}
        else if(obj0.style.pixelHeight){alert("pixelHeight");obj0.style.pixelHeight=divHeight1;}
        alert(obj0.offsetHeight);
        alert(obj0.style.pixelHeight);*/
        alert(obj0.style.display);
    }
}
</script>
</head>
<body>
    <div class="container">
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

    //connection a la base
    $db = @db_connect();

    //fixe le francais comme langue pour les dates
    $requete="SET lc_time_names = 'fr_FR'";
    mysql_query($requete,$db);
    $requete="SET NAMES 'utf8'";//à ne plus utiliser en PHP7, inutile, pour une raison inconnue sur les autres pages
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
?>

<form method="post" id="principal" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<?php 
    //champ caches
    if(FALSE==empty($id_login))
    {
	echo "<input id='id_login' name='id_login' type='hidden' value='".$id_login."' />";
    }
    if(FALSE==empty($id_nation))
    {
	echo "<input id='id_nation' name='id_nation' type='hidden' value=\"".$id_nation."\" />";
    }
    if(FALSE==empty($id_role))
    {
	echo "<input id='id_role' name='id_role' type='hidden' value='".$id_role."' />";//pour l'appel au detail d'une bataille
    }
    if(FALSE==empty($liste_roles))
    {
	echo "<input id='liste_roles' name='liste_roles' type='hidden' value=\"".$liste_roles."\" />";//pour le retour QG
    }
    if(FALSE==empty($id_partie))
    {
	echo "<input id='id_partie' name='id_partie' type='hidden' value='".$id_partie."' />";
    }
    echo "<input id='id_cinematique_pion' name='id_cinematique_pion' type='hidden' value='' />";

if ($idVictoire<0)
{
    echo "<h1>VAOC : La partie en cours n'est pas termin&eacute;e</h1>";
    echo "</form></div></body></html>";
    return;//on s'arrete la !
}
else
{
    if(FALSE==empty($id_login) && FALSE==empty($id_nation))
    {
        echo "<div class=\"row d-none d-md-flex justify-content-center\"><div class=\"col-md-12\">";
        if (3==$idVictoire)
        {
            echo "<img alt='Egalite' id='bandeau'' src='images/egalite.png' />";		
        }
        else
        {
            if ($id_nation==$idVictoire)
            {
                echo "<img alt='Victoire' id='bandeau'' src='images/victoire.png' />";		
            }
            else
            {
                echo "<img alt='Defaite' id='bandeau'' src='images/defaite.png' />";		
            }			
        }
        echo "</div></div>";
    }
}
?>
    <div class="row">&nbsp;</div>
        <div class="rowjustify-content-center fond resultat">
            <div class="col-12">
            <?php
            //titres et contexte 
            $requete="SELECT tab_vaoc_partie.S_NOM AS NOM_PARTIE, tab_vaoc_jeu.S_NOM AS NOM_JEU, DATE_FORMAT(DT_INITIALE,'%W %e %M %Y %H:%i') AS DATE_DEBUT, DATE_FORMAT(DT_TOUR,'%W %e %M %Y %H:%i') AS DATE_FIN";
            $requete.=" FROM tab_vaoc_partie, tab_vaoc_jeu";
            $requete.=" WHERE tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
            $requete.=" AND tab_vaoc_partie.ID_PARTIE=".$id_partie;
            //echo $requete;
            $res_partie = mysql_query($requete,$db);
            $row_partie = mysql_fetch_object($res_partie);
            echo "<h1>".$row_partie->NOM_JEU." - ".$row_partie->NOM_PARTIE."</h1>";
            ?>
            </div>
            <div class="col-12 d-none d-xl-block">
                <?php
                echo "<h3>".$row_partie->DATE_DEBUT."</h3>";
                ?>
            </div>
            <div class="col-12 d-none d-xl-block">
                <?php
                echo "<h3>".$row_partie->DATE_FIN."</h3>";
                ?>
            </div>
            <div class="col-sm-12 d-none d-sm-block">
            <?php
            echo "<h2>".$row_partie->DATE_DEBUT." - ".$row_partie->DATE_FIN."</h2>";
            ?>
            </div>
        </div>
    <div class="row">&nbsp;</div>
    
    <div class="row justify-content-center fond">
<?php
        //liste des nations
	$requeteNation="SELECT tab_vaoc_nation.S_NOM, tab_vaoc_nation.ID_NATION";
	$requeteNation.=" FROM tab_vaoc_nation";
	$requeteNation.=" WHERE tab_vaoc_nation.ID_PARTIE=".$id_partie;
	$res_nation = mysql_query($requeteNation,$db);
	//recherche le nombre de lignes que va contenir chaque tableau, ceci afin d'aligner les totaux ensemble en bas (c'est classe pas vrai !)
	$total_lignes = 0;
	while($row_nation = mysql_fetch_object($res_nation))
	{
            $requeteCount="SELECT COUNT(ID_PION) as NB_LIGNES";
            $requeteCount.=" FROM tab_vaoc_pion, tab_vaoc_modele_pion";
            $requeteCount.=" WHERE tab_vaoc_pion.ID_PARTIE=".$id_partie;
            $requeteCount.=" AND tab_vaoc_modele_pion.ID_PARTIE=".$id_partie;
            $requeteCount.=" AND tab_vaoc_pion.ID_MODELE_PION=tab_vaoc_modele_pion.ID_MODELE_PION";
            $requeteCount.=" AND tab_vaoc_modele_pion.ID_NATION=".$row_nation->ID_NATION;
            $requeteCount.=" AND B_DEPOT=0 AND B_CONVOI=0 AND B_RENFORT=0 AND B_BLESSES=0 AND B_PRISONNIERS=0";
            $res_lignes = mysql_query($requeteCount,$db);
            $row_lignes = mysql_fetch_object($res_lignes);
            if ($total_lignes<$row_lignes->NB_LIGNES)
            {
                $total_lignes = $row_lignes->NB_LIGNES;
            }

            //Initialisation des tableaux pour le traitement suivant
            $totalInitial[$row_nation->ID_NATION]=0;
            $totalReel[$row_nation->ID_NATION]=0;
            $totalDetruit[$row_nation->ID_NATION]=0;
            $totalDemoralise[$row_nation->ID_NATION]=0;
            $totalVictoire[$row_nation->ID_NATION]=0;
            $autre_nation = $row_nation->ID_NATION;
        }

        $res_nation = mysql_query($requeteNation,$db);
	//recherche le nombre de lignes que va contenir chaque tableau, ceci afin d'aligner les totaux ensemble en bas (c'est classe pas vrai !)
        $premierID_NATION= -1;
        $deuxiemeID_NATION= -1;
	while($row_nation = mysql_fetch_object($res_nation))
	{
            if (-1==$premierID_NATION) {$premierID_NATION=$row_nation->ID_NATION;} else {$deuxiemeID_NATION=$row_nation->ID_NATION;}

            //je stocke tous les noms pour ensuite pouvoir compléter en invisible les noms les plus longs sur chaque ligne
            $requeteUnites="SELECT tab_vaoc_pion.S_NOM, ID_PION, ID_PION_PROPRIETAIRE, I_FATIGUE_REEL, I_MORAL_REEL, I_MORAL_MAX, S_POSITION, B_DETRUIT, B_FUITE_AU_COMBAT,";
            $requeteUnites.=" I_INFANTERIE_REEL, I_CAVALERIE_REEL, I_ARTILLERIE_REEL, I_INFANTERIE_INITIALE, I_CAVALERIE_INITIALE, I_ARTILLERIE_INITIALE";
            $requeteUnites.=" FROM tab_vaoc_pion, tab_vaoc_modele_pion";
            $requeteUnites.=" WHERE tab_vaoc_pion.ID_PARTIE=".$id_partie;
            $requeteUnites.=" AND tab_vaoc_modele_pion.ID_PARTIE=".$id_partie;
            $requeteUnites.=" AND tab_vaoc_pion.ID_MODELE_PION=tab_vaoc_modele_pion.ID_MODELE_PION";
            $requeteUnites.=" AND tab_vaoc_modele_pion.ID_NATION=".$row_nation->ID_NATION;
            $requeteUnites.=" ORDER BY ID_PION, S_NOM";//ID_PION_PROPRIETAIRE ->met tous les leaders en premier, c'est pas terrible		

            $res_pions = mysql_query($requeteUnites,$db);
            //echo $requete;
            $nb_lignes =0;
            while($row = mysql_fetch_object($res_pions))
	    {
                //recherche du nom du leader
                $requeteChef="SELECT tab_vaoc_pion.S_NOM, tab_vaoc_pion.ID_PION ";
                $requeteChef.=" FROM tab_vaoc_pion";
                $requeteChef.=" WHERE tab_vaoc_pion.ID_PARTIE=".$id_partie;
                $requeteChef.=" AND tab_vaoc_pion.ID_PION=".$row->ID_PION_PROPRIETAIRE;
                $res_chef = mysql_query($requeteChef,$db);
                $row_chef = mysql_fetch_object($res_chef);

                $Noms[$row_nation->ID_NATION][$nb_lignes]=$row->S_NOM;
                if (null==$row_chef)
                {
                    $Chefs[$row_nation->ID_NATION][$nb_lignes]="absent";
                }
                else
                {
                    $Chefs[$row_nation->ID_NATION][$nb_lignes]=$row_chef->S_NOM;
                }
                $nb_lignes++;
            }
            for ($i = $nb_lignes; $i < $total_lignes; $i++) 
            {
                $Noms[$row_nation->ID_NATION][$i]="";
                $Chefs[$row_nation->ID_NATION][$i]="";
            }
	}
	
	$res_nation = mysql_query($requeteNation,$db);
	while($row_nation = mysql_fetch_object($res_nation))
	{
            $nb_lignes=0;
            $bilan[$row_nation->ID_NATION]="<div class=\"col-12 col-lg-6 resultat col-lg-height\" id=\"joueur".$row_nation->ID_NATION."\">";
            $bilan[$row_nation->ID_NATION].="<div class=\"inside inside-full-height\"><div class=\"content\">";
            $bilan[$row_nation->ID_NATION].="<div class=\"row\"><div class=\"col-12 \"><h1>".$row_nation->S_NOM."</h1></div></div>";

            //recherche des objectifs realises ou non par la nation
            $requete="SELECT S_NOM, I_VICTOIRE, ID_NATION";
            $requete.=" FROM tab_vaoc_objectifs";
            $requete.=" WHERE tab_vaoc_objectifs.ID_PARTIE=".$id_partie;
            $res_objectifs = mysql_query($requete,$db);
            $num_objectifs = mysql_num_rows($res_objectifs);
            if ($num_objectifs>0)
            {
                $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION].
                        "<div class=\"row\"><div class=\"col-10 resultatentete\"><img alt=\"Objectifs\" title=\"Objectifs\" src='images/objectif_noir-26.png' /></div><div class=\"col-2 resultatentete\"><img alt=\"Pts de victoire\" title=\"Pts de victoire\" src='images/points_de_victoire_noir-26.png' /></div></div>";
                while($row_objectif = mysql_fetch_object($res_objectifs))
                {
                    $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"row\"><div class=\"col-10 text-center\">".$row_objectif->S_NOM."</div>";
                    if ($row_nation->ID_NATION == $row_objectif->ID_NATION)
                    {
                        $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"col-2 text-center\">".$row_objectif->I_VICTOIRE."</div></div>";
                        $totalVictoire[$row_nation->ID_NATION]= $totalVictoire[$row_nation->ID_NATION] + $row_objectif->I_VICTOIRE;  		
                    }
                    else
                    {
                        $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"col-2 text-center\">0</div></div>";
                    }
                }
            }
		
            $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION].
                "<div class=\"row\">
                    <div class=\"col-3 resultatentete\"><img alt=\"Nom\" title=\"Nom\" src='images/nom_noir-26.png' /></div>
                    <div class=\"col-3 resultatentete\"><img alt=\"Commandant\" title=\"Commandant\" src='images/rang_noir-26.png' /></div>
                    <div class=\"col-2 resultatentete\"><img alt=\"Effectif\" title=\"Effectif\" src='images/infanterie_noir-26.png' /></div>
                    <div class=\"col-2 resultatentete\"><img alt=\"Initial\" title=\"Initial\" src='images/initial_noir-26.png' /></div>
                    <div class=\"col-1 resultatentete\"><img alt=\"D&eacute;truite\" title=\"D&eacute;truite\" src='images/detruit_noir-26.png' /></div>
                    <div class=\"col-1 resultatentete\"><img alt=\"Fuite\" title=\"Fuite\" src='images/deroute_noir-26.png' /></div>
                </div>";
            //liste des unites
            $requeteUnites="SELECT tab_vaoc_pion.S_NOM, ID_PION, ID_PION_PROPRIETAIRE, I_FATIGUE_REEL, I_MORAL_REEL, I_MORAL_MAX, S_POSITION, B_DETRUIT, B_FUITE_AU_COMBAT,";
            $requeteUnites.=" I_INFANTERIE_REEL, I_CAVALERIE_REEL, I_ARTILLERIE_REEL, I_INFANTERIE_INITIALE, I_CAVALERIE_INITIALE, I_ARTILLERIE_INITIALE";
            $requeteUnites.=" FROM tab_vaoc_pion, tab_vaoc_modele_pion";
            $requeteUnites.=" WHERE tab_vaoc_pion.ID_PARTIE=".$id_partie;
            $requeteUnites.=" AND tab_vaoc_modele_pion.ID_PARTIE=".$id_partie;
            $requeteUnites.=" AND tab_vaoc_pion.ID_MODELE_PION=tab_vaoc_modele_pion.ID_MODELE_PION";
            $requeteUnites.=" AND tab_vaoc_modele_pion.ID_NATION=".$row_nation->ID_NATION;
            $requeteUnites.=" AND B_DEPOT=0 AND B_CONVOI=0 AND B_RENFORT=0 AND B_BLESSES=0 AND B_PRISONNIERS=0";
            $requeteUnites.=" ORDER BY ID_PION, S_NOM";//ID_PION_PROPRIETAIRE ->met tous les leaders en premier, c'est pas terrible		

            $res_pions = mysql_query($requeteUnites,$db);
            //echo $requete;
            while($row = mysql_fetch_object($res_pions))
	    {
                $bilanUnite[$row_nation->ID_NATION][$nb_lignes]="";
	    	//recherche du nom du leader
                /*
                $requeteChef="SELECT tab_vaoc_pion.S_NOM, tab_vaoc_pion.ID_PION ";
                $requete.=" FROM tab_vaoc_pion";
                $requete.=" WHERE tab_vaoc_pion.ID_PARTIE=".$id_partie;
                $requete.=" AND tab_vaoc_pion.ID_PION=".$row->ID_PION_PROPRIETAIRE;
                $res_chef = mysql_query($requete,$db);
                $row_chef = mysql_fetch_object($res_chef);*/

                /*
                $idAutreNation=-1;
                if ($premierID_NATION == $row_nation->ID_NATION)
                {
                    $idAutreNation = $deuxiemeID_NATION;
                }
                else
                {
                    $idAutreNation = $premierID_NATION;
                }

                $longueur = strlen($Noms[$idAutreNation][$nb_lignes]) - strlen($Noms[$row_nation->ID_NATION][$nb_lignes]);
                if ($longueur>0)
                {
                    $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes].
                        "<div class=\"row\" style=\"border-color: blue; border-style: solid; border-width: 1px;\"><div class=\"col-3 \">".$Noms[$row_nation->ID_NATION][$nb_lignes].
                        "<span style=\"opacity: 0.0;\">".substr($Noms[$idAutreNation][$nb_lignes],strlen($Noms[$row_nation->ID_NATION][$nb_lignes]) - $longueur,$longueur)."</span></div>";
                }
                else
                {
                    $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes].
                        "<div class=\"row\" style=\"border-color: red; border-style: solid; border-width: 1px;\"><div class=\"col-3 \">".$Noms[$row_nation->ID_NATION][$nb_lignes].
                        "</div>";
                }
                */
                /*
                $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes].
                    "<div class=\"row victoire".$row_nation->ID_NATION."\" style=\"border-color: blue; border-style: solid; border-width: 0px;\">".
                    "<div class=\"col-3 \">".$Noms[$row_nation->ID_NATION][$nb_lignes].
                    "</div>";
                */
                $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes].
                    "<div class=\"row victoire".$row_nation->ID_NATION."\" >".
                    "<div class=\"col-3 \">".$Noms[$row_nation->ID_NATION][$nb_lignes].
                    "</div>";
                /*
                $longueur = strlen($Chefs[$idAutreNation][$nb_lignes]) - strlen($Chefs[$row_nation->ID_NATION][$nb_lignes]);
                if ($longueur>0)
                {
                    $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes].
                    "<div class=\"col-3 \">".$Chefs[$row_nation->ID_NATION][$nb_lignes].
                    "<span style=\"opacity: 0.0;\">".substr($Chefs[$idAutreNation][$nb_lignes],strlen($Chefs[$row_nation->ID_NATION][$nb_lignes]) - $longueur,$longueur)."</span></div>";
                }
                else
                {
                    $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes].
                    "<div class=\"col-3 \">".$Chefs[$row_nation->ID_NATION][$nb_lignes]."</div>";
                }
                 */
                    $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes].
                    "<div class=\"col-3 \">".$Chefs[$row_nation->ID_NATION][$nb_lignes]."</div>";

                if (0==$row->I_INFANTERIE_INITIALE+$row->I_CAVALERIE_INITIALE)
                {
                    //leader
                    $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes]."<div class=\"col-6\"><img alt=\"non\" src=\"images/transparent-26.png\" /></div>";
                }
                else
                {
                    $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes]."<div class=\"col-2 numerique\">".($row->I_INFANTERIE_REEL+$row->I_CAVALERIE_REEL)."</div>";
                    $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes]."<div class=\"col-2 numerique\">".($row->I_INFANTERIE_INITIALE+$row->I_CAVALERIE_INITIALE)."</div>";
                    if (1==$row->B_DETRUIT)
                    {
                        $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes]."<div class=\"col-1 \"><img alt=\"oui\" src=\"images/oui_noir-26.png\" /></div>";
                        $totalDetruit[$row_nation->ID_NATION]=$totalDetruit[$row_nation->ID_NATION]+1;
                    }
                    else
                    {
                        $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes]."<div class=\"col-1 \"><img alt=\"&nbsp;\" src=\"images/transparent-26.png\" /></div>";
                    }
                    if (1==$row->B_FUITE_AU_COMBAT)
                    {
                        $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes]."<div class=\"col-1 \"><img alt=\"oui\" src=\"images/oui_noir-26.png\" /></div>";
                        $totalDemoralise[$row_nation->ID_NATION]=$totalDemoralise[$row_nation->ID_NATION]+1;
                    }
                    else
                    {
                        $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes]."<div class=\"col-1 \"><img alt=\"&nbsp;\" src=\"images/transparent-26.png\" /></div>";
                    }
                }
	  		
                $bilanUnite[$row_nation->ID_NATION][$nb_lignes]=$bilanUnite[$row_nation->ID_NATION][$nb_lignes]."</div>";

                /*
                if ($premierID_NATION != $row_nation->ID_NATION && ""===$bilanUnite[$premierID_NATION][$nb_lignes])
                {
                    $bilanUnite[$premierID_NATION][$nb_lignes] = $bilanUnite[$row_nation->ID_NATION][$nb_lignes];
                }
*/
                $totalInitial[$row_nation->ID_NATION]=$totalInitial[$row_nation->ID_NATION]+$row->I_INFANTERIE_INITIALE+$row->I_CAVALERIE_INITIALE;
                $totalReel[$row_nation->ID_NATION]=$totalReel[$row_nation->ID_NATION]+$row->I_INFANTERIE_REEL+$row->I_CAVALERIE_REEL;
                $nb_lignes++;
	    }
            for ($i = $nb_lignes; $i < $total_lignes; $i++) 
            {
                /*
                if ($premierID_NATION == $row_nation->ID_NATION)
                {
                    $bilanUnite[$row_nation->ID_NATION][$i]="";
                }
                else 
                {
                    $bilanUnite[$row_nation->ID_NATION][$i]=$bilanUnite[$premierID_NATION][$i];
                }
                 */
                //ce qui suit ne marche pas si les lignes de l'autre nation font plusieurs lignes de haut.
                // $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"row\"><div class=\"col-12 \"><img alt=\"&nbsp;\" src=\"images/transparent-26.png\"/></div></div>";
                // c'est pourquoi il faut recopier les lignes de l'autre nation mais en invisible pour qu'elles fassent la même hauteur
                $bilanUnite[$row_nation->ID_NATION][$i]="<div class=\"row victoire".$row_nation->ID_NATION."\"><div class=\"col-12 \"><img alt=\"&nbsp;\" src=\"images/transparent-26.png\"/></div></div>";
            }
        }

        $res_nation = mysql_query($requeteNation,$db);
	while($row_nation = mysql_fetch_object($res_nation))
	{
            // on recopie toutes les lignes
            for ($i = 0; $i < $total_lignes; $i++) 
            {
                //ce qui suit ne marche pas si les lignes de l'autre nation font plusieurs lignes de haut.
                // $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"row\"><div class=\"col-12 \"><img alt=\"&nbsp;\" src=\"images/transparent-26.png\"/></div></div>";
                // c'est pourquoi il faut recopier les lignes de l'autre nation mais en invisible pour qu'elles fassent la même hauteur
                $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION].$bilanUnite[$row_nation->ID_NATION][$i];
            }

            $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"row total\">";
            $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"col-6\">Total</div>";
            $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"col-2\">".$totalReel[$row_nation->ID_NATION]."</div>";
            $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"col-2\">".$totalInitial[$row_nation->ID_NATION]."</div>";
            $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"col-1\">".$totalDetruit[$row_nation->ID_NATION]."</div>";
            $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"col-1\">".$totalDemoralise[$row_nation->ID_NATION]."</div>";
            $bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."</div>";
            $totalVictoire[$autre_nation]= $totalVictoire[$autre_nation] + $totalDemoralise[$row_nation->ID_NATION];  		
            $autre_nation = $row_nation->ID_NATION;
	}
	
	//on refait encore un tour pour afficher le total des points de victoire maintenant que l'on connait le nombre de corps demoralises
	$res_nation = mysql_query($requeteNation,$db);
	while($row_nation = mysql_fetch_object($res_nation))
	{
		$bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."<div class=\"row\">
                    <div class=\"col-12\"><h1>Points de victoire : ".$totalVictoire[$row_nation->ID_NATION]."</h1></div></div>";
		$bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."</div>";//pour div de début de nation
		$bilan[$row_nation->ID_NATION]=$bilan[$row_nation->ID_NATION]."</div></div>";//pour div de début de nation
	}
	
	//on affiche les tableaux
	foreach ($bilan as $elementbilan)
	{
		echo $elementbilan;
	}
?> 
    </div>
    <div class="row"><div class="col-12">&nbsp;</div></div>
    <div class="row ">
        <div class="col-12 justify-content-center text-center">
<?php 
	if(FALSE==empty($id_login))
	{
            //peut survenir pour un anonyme venant voir le bilan
            echo "<button name=\"retourQG\" class=\"btn btn-light bouton\" id=\"btnRetourQG\" onclick=\"javascript:callRetourQG();\"
            type=\"button\" alt=\"retour au QG\" value=\"submit\" />Retour au QG</button>";
	}
	else
	{
            //dans ce cas on peut revenir a l'accueil
            echo "<button name=\"Quitter\" class=\"btn btn-light bouton\" id=\"btnQuitter\" onclick=\"javascript:callQuitter();\"
            type=\"button\" alt=\"retour a l'ecran general\" value=\"submit\" />Quitter</button>";
	}
        
        //compte-rendu
        echo "<button name=\"Compte-rendu\" class=\"btn btn-light bouton\" id=\"btnCompteRendu\" onclick=\"javascript:window.open('".$row_repertoire->S_REPERTOIRE."_carte/compterendu.pdf');return false; \"
        type=\"button\" alt=\"compte-rendu\" value=\"submit\" />Compte-rendu</button>";
?>            
        </div>
    </div>
</form>	
</div>
<script type="text/javascript">
//on remet la taille de l'image, de la meme largeur que le tableau
//ajusterTailleDuBandeau();
//ajusterLongueurTableau();
</script>
</body>
</html>