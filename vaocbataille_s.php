<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>VAOC : Bataille</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="Description" content=""/>
        <meta name="Keywords" content=""/>
        <meta name="Identifier-URL" content="http://waoc.free.fr/vaoc/vaocbataille.php"/>
        <meta name="revisit-after" content="31"/>
        <meta name="Copyright" content="copyright armand BERGER"/>
        <link rel="icon" type="image/png" href="/images/favicon.png" />
        <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">         
        <link href="css/bootstrap4.min.css" rel="stylesheet" />
        <link rel="stylesheet" type="text/css" href="css/vaoc2.css"/>
        <style type="text/css"> 
            body
            {
                margin : 0 auto; padding : 0;
                background-color:#D3E399; 
                background-image:url(images/fondbataille.png);
            }
        </style> 
    </head>
    <?php
    require("vaocbase.php"); //include obligatoire pour l'execution
    require("vaocfonctionsbataille_s.php"); //include obligatoire pour l'execution
    //pratique pour le debug
    /*
      echo "liste des valeurs transmises dans le post<br/>";
      while (list($name, $value) = each($HTTP_POST_VARS)) {echo "$name = $value<br>\n";}
     */
      //echo "liste des valeurs transmises dans le request<br/>";
      //while (list($name, $value) = each($_REQUEST)) {echo "$name = $value<br>\n";}
    //converti toutes les variables REQUEST en variables du meme nom
    extract($_REQUEST, EXTR_OVERWRITE);

    //connection a la base
    $db = @db_connect();

    //fixe le francais comme langue pour les dates
    $requete = "SET lc_time_names = 'fr_FR'";
    mysql_query($requete, $db);
    mysql_query("SET NAMES 'utf8'", $db);

    $paramsChargement="{id_login:'".$id_login."', id_role:".$id_role.", id_partie:".$id_partie.", id_nation:".$id_nation.", id_bataille:".$id_bataille.",tri_liste:'";    
    ?>
    <body>
        <div>
            <?php
            //recherche d'information generique
            //recherche du role courant 
            if (FALSE == empty($id_login))
            {
                $requete = "SELECT tab_vaoc_role.ID_ROLE, tab_vaoc_role.ID_PARTIE, tab_vaoc_role.S_NOM AS NOM_ROLE, tab_vaoc_role.ID_PION, ";
                $requete.=" tab_vaoc_role.S_COULEUR_FOND, tab_vaoc_role.S_COULEUR_TEXTE, tab_vaoc_jeu.I_LEVER_DU_SOLEIL , tab_vaoc_jeu.I_COUCHER_DU_SOLEIL , ";
                $requete.=" tab_vaoc_partie.FL_MISEAJOUR, tab_vaoc_partie.FL_DEMARRAGE, tab_vaoc_partie.S_REPERTOIRE,";
                $requete.=" tab_vaoc_partie.I_TOUR, tab_vaoc_role.ID_NATION";
                $requete.=" FROM tab_vaoc_role, tab_vaoc_partie, tab_vaoc_jeu";
                $requete.=" WHERE (tab_vaoc_role.ID_PARTIE=tab_vaoc_partie.ID_PARTIE)";
                $requete.=" AND (tab_vaoc_jeu.ID_JEU=tab_vaoc_partie.ID_JEU)";
                $requete.=" AND tab_vaoc_role.ID_ROLE=" . $id_role;
                $requete.=" AND tab_vaoc_role.ID_PARTIE=" . $id_partie;
                //echo $requete;
                $res_role_partie = mysql_query($requete, $db);
                //echo "nb resultats=".mysql_num_rows($res_role_partie);
                $row_role = mysql_fetch_object($res_role_partie);
                $id_pion_role = $row_role->ID_PION; //pion du r�le courant
                //echo "id_pion_role=".$id_pion_role;
                $id_nation = $row_role->ID_NATION;
                $i_tour = $row_role->I_TOUR;
                //echo "i_tour=".$i_tour;
                $fl_demmarage = $row_role->FL_DEMARRAGE;
                $repertoire = $row_role->S_REPERTOIRE . "_" . $i_tour;
            }
            else
            {
                //connection anonyme sur compte rendu
                $requete = "SELECT tab_vaoc_partie.FL_DEMARRAGE, tab_vaoc_partie.S_REPERTOIRE,";
                $requete.=" tab_vaoc_partie.I_TOUR";
                $requete.=" FROM tab_vaoc_partie";
                $requete.=" WHERE tab_vaoc_partie.ID_PARTIE=" . $id_partie;
                //echo $requete;
                $res_role_partie = mysql_query($requete, $db);
                //echo "nb resultats=".mysql_num_rows($res_role_partie);
                $row_role = mysql_fetch_object($res_role_partie);

                $i_tour = $row_role->I_TOUR;
                $fl_demmarage = $row_role->FL_DEMARRAGE;
                $repertoire = $row_role->S_REPERTOIRE . "_" . $i_tour;

                $id_nation = -1;
                $id_pion_role = -1;
            }

            //changement dans l'ordre d'engagement
            for ($i = 0; $i < 6; $i++)
            {
                $id_chaine = "id_engagement" . $i;
                if (TRUE == isset($_REQUEST[$id_chaine]) && TRUE == is_numeric($_REQUEST[$id_chaine]))
                {
                    //si on a deja un ordre d'engagement sur cette zone pour ce tour, il faut le modifier
                    $requete = "SELECT tab_vaoc_ordre.I_ENGAGEMENT ";
                    $requete.=" FROM tab_vaoc_ordre";
                    $requete.=" WHERE tab_vaoc_ordre.ID_PARTIE=" . $id_partie;
                    $requete.=" AND I_TOUR=" . $i_tour;
                    $requete.=" AND I_TYPE=" . ORDRE_ENGAGEMENT;
                    $requete.=" AND ID_BATAILLE=" . $id_bataille;
                    $requete.=" AND I_ZONE_BATAILLE=" . $i;
                    //echo $requete."<br/>";
                    $res_ordres_engagement = mysql_query($requete, $db);
                    if ((false == empty($res_ordres_engagement) && mysql_num_rows($res_ordres_engagement) > 0))
                    {
                        $requete = "UPDATE tab_vaoc_ordre SET I_ENGAGEMENT = " . $_REQUEST[$id_chaine];
                        $requete.=" WHERE tab_vaoc_ordre.ID_PARTIE=" . $id_partie;
                        $requete.=" AND I_TOUR=" . $i_tour;
                        $requete.=" AND I_TYPE=" . ORDRE_ENGAGEMENT;
                        $requete.=" AND ID_BATAILLE=" . $id_bataille;
                        $requete.=" AND I_ZONE_BATAILLE=" . $i;
                        //echo $requete."<br/>";
                        mysql_query($requete, $db);
                    }
                    else
                    {
                        //$requete="DELETE FROM tab_vaoc_ordre WHERE ID_BATAILLE = ".$id_bataille." AND I_ZONE_BATAILLE=".$i." AND I_TYPE=".ORDRE_ENGAGEMENT." AND ID_PARTIE=".$id_partie." AND I_TOUR=".$i_tour;
                        //echo $requete."<br/>";
                        //mysql_query($requete,$db);
                        //On insere le nouvel ordre d'engagement
                        $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                        $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`,`ID_BATAILLE`,`I_ZONE_BATAILLE`,`I_ENGAGEMENT`) VALUES (";
                        $requete.=AjouterIDOrdre($db, $id_partie) . " , " . $id_pion_role . ", " . $id_pion_role . ", " . $id_partie . ", " . $i_tour . ", " . ORDRE_ENGAGEMENT . ", ''";                        
                        //$requete.="NULL , ".$row_retraite->ID_PION.", ".$row_retraite->ID_PION.", ".$id_partie.", ".$i_tour.", ".ORDRE_RETRAITE.", ''";;
                        $requete.=", -1, -1, -1, -1, -1," . $id_bataille . ", " . $i . ", " . $_REQUEST[$id_chaine];
                        $requete.=")";
                        //echo $requete."<br/>";
                        mysql_query($requete, $db);
                    }
                }
            }

            //if (FALSE == empty($id_retraiter) && $id_retraiter >= 0)
            if (TRUE == isset($id_retraiter) && TRUE == is_numeric($id_retraiter))
            {
                //Retraite Generale
                //il faut rechercher tous les pions pr�sents sur cette bataille de la meme nation
                //et leur donner un ordre de retraite
                $requete = "SELECT TAB1.ID_PION, TAB1.I_ZONE_BATAILLE_ENGAGEMENT ";
                $requete.=" FROM tab_vaoc_bataille_pions TAB1, tab_vaoc_bataille_pions TAB2";
                $requete.=" WHERE TAB1.ID_PARTIE=" . $id_partie . " AND TAB1.ID_BATAILLE=" . $id_bataille;
                $requete.=" AND TAB2.ID_PARTIE=" . $id_partie . " AND TAB2.ID_BATAILLE=" . $id_bataille;
                $requete.=" AND TAB1.ID_NATION=TAB2.ID_NATION AND TAB2.ID_PION=" . $id_retraiter;
                //echo $requete."<br/>";
                $res_retraite = mysql_query($requete, $db);
                while ($row_retraite = mysql_fetch_object($res_retraite))
                {
                    //si un ordre de mouvement a deja ete donn� pour ce tour, il faut le supprimer
                    $requete = "DELETE FROM tab_vaoc_ordre WHERE ID_PION = " . $row_retraite->ID_PION . " AND ID_PARTIE=" . $id_partie . " AND I_TOUR=" . $i_tour;
                    $requete.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $id_bataille;
                    //echo $requete."<br/>";
                    mysql_query($requete, $db);
                    //insertion du nouvel ordre, retraite
                    $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                    $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`,`ID_BATAILLE`,`I_ZONE_BATAILLE`) VALUES (";
                    $requete.=AjouterIDOrdre($db, $id_partie) . " , " . $row_retraite->ID_PION . ", " . $row_retraite->ID_PION . ", " . $id_partie . ", " . $i_tour . ", " . ORDRE_RETRAITE . ", ''";
                    //$requete.="NULL , ".$row_retraite->ID_PION.", ".$row_retraite->ID_PION.", ".$id_partie.", ".$i_tour.", ".ORDRE_RETRAITE.", ''";;
                    $requete.=", -1, -1, -1, -1, -1," . $id_bataille . ", ".$row_retraite->I_ZONE_BATAILLE_ENGAGEMENT;
                    $requete.=")";
                    //echo $requete."<br/>";
                    mysql_query($requete, $db);
                }
            }

            //if (FALSE == empty($id_annuler_retraiter) && $id_annuler_retraiter >= 0)
            if (TRUE == isset($id_annuler_retraiter) && TRUE == is_numeric($id_annuler_retraiter))
            {
                //il faut rechercher tous les pions presents sur cette bataille de la meme nation
                //et annuler les ordres
                $requete = "SELECT TAB1.ID_PION ";
                $requete.=" FROM tab_vaoc_bataille_pions TAB1, tab_vaoc_bataille_pions TAB2";
                $requete.=" WHERE TAB1.ID_PARTIE=" . $id_partie . " AND TAB1.ID_BATAILLE=" . $id_bataille;
                $requete.=" AND TAB2.ID_PARTIE=" . $id_partie . " AND TAB2.ID_BATAILLE=" . $id_bataille;
                $requete.=" AND TAB1.ID_NATION=TAB2.ID_NATION AND TAB2.ID_PION=" . $id_annuler_retraiter;
                //echo $requete."<br/>";
                $res_annule_retraite = mysql_query($requete, $db);
                while ($row_annule_retraite = mysql_fetch_object($res_annule_retraite))
                {
                    //si un ordre de mouvement a d�j� �t� donn� pour ce tour, il faut le supprimer
                    $requete = "DELETE FROM tab_vaoc_ordre WHERE ID_PION = " . $row_annule_retraite->ID_PION . " AND ID_PARTIE=" . $id_partie . " AND I_TOUR=" . $i_tour;
                    $requete.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $id_bataille;
                    //echo $requete."<br/>";
                    mysql_query($requete, $db);
                }
            }

            //if (FALSE == empty($id_annuler))
            if (TRUE == isset($id_annuler) && TRUE == is_numeric($id_annuler))
            {
                $requete = "SELECT I_ZONE_BATAILLE, ID_BATAILLE FROM tab_vaoc_ordre WHERE ID_PION = " . $id_annuler . " AND ID_PARTIE=" . $id_partie . " AND I_TOUR=" . $i_tour;
                //echo $requete."<br/>";
                $res = mysql_query($requete, $db);

                //on peut n'avoir aucune ligne de retour, si l'utilisateur fait F5 apres avoir deja annuler l'ordre
                if (mysql_num_rows($res) > 0)
                {
                    $row = mysql_fetch_object($res);

                    //annule l'ordre donne a ce tour
                    $requete = "DELETE FROM tab_vaoc_ordre WHERE ID_PION = " . $id_annuler . " AND ID_PARTIE=" . $id_partie . " AND I_TOUR=" . $i_tour;
                    $requete.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $id_bataille;
                    //echo $requete."<br/>";
                    mysql_query($requete, $db);

                    //s'il ne reste qu'un QG/leader dans la zone, il doit egalement partir		
                    //Unites deja en bataille
                    $requete = "SELECT tab_vaoc_bataille_pions.ID_PION, tab_vaoc_pion.I_STRATEGIQUE ";
                    $requete.=" FROM tab_vaoc_bataille_pions, tab_vaoc_pion ";
                    $requete.=" WHERE tab_vaoc_bataille_pions.ID_PARTIE=" . $id_partie;
                    $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE=" . $row->ID_BATAILLE;
                    $requete.=" AND tab_vaoc_bataille_pions.B_ENGAGEE=1";
                    $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
                    $requete.=" AND tab_vaoc_bataille_pions.ID_PION=tab_vaoc_pion.ID_PION";
                    $requete.=" AND tab_vaoc_pion.I_ZONE_BATAILLE=" . $row->I_ZONE_BATAILLE;
                    $requete.=" AND tab_vaoc_pion.B_QG=0"; //uniquement les unites combattantes
                    //echo $requete."<br/>";
                    $res_pionsB = mysql_query($requete, $db);
                    $nb_unitesB = mysql_num_rows($res_pionsB);

                    //unites mises en bataille a ce tour
                    $requete = "SELECT tab_vaoc_ordre.ID_PION, tab_vaoc_pion.B_QG ";
                    $requete.="FROM tab_vaoc_ordre, tab_vaoc_pion ";
                    $requete.="WHERE tab_vaoc_ordre.ID_PARTIE=" . $id_partie;
                    $requete.=" AND tab_vaoc_ordre.I_TOUR=" . $i_tour;
                    $requete.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $row->ID_BATAILLE;
                    $requete.=" AND tab_vaoc_ordre.I_TYPE=" . ORDRE_COMBAT;
                    $requete.=" AND tab_vaoc_ordre.ID_PION=tab_vaoc_pion.ID_PION";
                    $requete.=" AND tab_vaoc_ordre.I_ZONE_BATAILLE=" . $row->I_ZONE_BATAILLE;
                    //echo $requete."<br/>";
                    $res_pionsT = mysql_query($requete, $db);
                    $nb_unitesT = mysql_num_rows($res_pionsT);

                    //echo "nb_unitesB=".$nb_unitesB." nb_unitesT=".$nb_unitesT."<br/>";
                    if (($nb_unitesB + $nb_unitesT) == 1)
                    {
                        //s'il y a plus d'une unite, l'une d'elle est forcement combattante
                        //s'il n'y en a qu'une et que c'est un leader, il doit partir
                        //il vient obligatoirement d'etre place a ce tour, sinon, l'unite combattante est parti sur RETRAITE avec le leader			
                        //s'il s'agissait de la zone centrale toutes les unites arrivees dans les zones voisines a ce tour doivent partir
                        if ($row->I_ZONE_BATAILLE == 1 || $row->I_ZONE_BATAILLE == 4)
                        {
                            $requete = "DELETE FROM tab_vaoc_ordre WHERE ID_PARTIE=" . $id_partie . " AND I_TOUR=" . $i_tour;
                            $requete.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $row->ID_BATAILLE;
                            if ($row->I_ZONE_BATAILLE == 1)
                            {
                                $requete.=" AND (tab_vaoc_ordre.I_ZONE_BATAILLE=0 OR ab_vaoc_ordre.I_ZONE_BATAILLE=1 OR ab_vaoc_ordre.I_ZONE_BATAILLE=2)";
                            }
                            else
                            {
                                $requete.=" AND (tab_vaoc_ordre.I_ZONE_BATAILLE=3 OR ab_vaoc_ordre.I_ZONE_BATAILLE=4 OR ab_vaoc_ordre.I_ZONE_BATAILLE=5)";
                            }
                        }
                        else
                        {
                            if ($nb_unitesT>0)
                            {
                                $row_QG = mysql_fetch_object($res_pionsT);
                                if (1==$row_QG->B_QG)
                                {
                                    $requete = "DELETE FROM tab_vaoc_ordre WHERE ID_PION = " . $row_QG->ID_PION . " AND ID_PARTIE=" . $id_partie . " AND I_TOUR=" . $i_tour;
                                    $requete.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $id_bataille;
                                }
                            }
                        }
                        //echo $requete."<br/>";
                        mysql_query($requete, $db);
                    }
                }
            }

            //if (FALSE == empty($id_combat))
            if (TRUE == isset($id_combat) && TRUE == is_numeric($id_combat))
            {
                //annule l'ordre precedent en cas de F5
                $requete = "DELETE FROM tab_vaoc_ordre WHERE ID_PION = " . $id_combat . " AND ID_PARTIE=" . $id_partie . " AND I_TOUR=" . $i_tour;
                $requete.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $id_bataille;
                //echo $requete."<br/>";
                mysql_query($requete, $db);
                //calcul $i_zone_bataille suivant c_zone, l'axe de la bataille et la position de l'unit�
                $requete = "SELECT ID_NATION_012";
                $requete.=" FROM tab_vaoc_bataille ";
                $requete.="WHERE ID_PARTIE=" . $id_partie . " AND ID_BATAILLE=" . $id_bataille;
                //echo $requete."<br/>";
                $res = mysql_query($requete, $db);
                $row = mysql_fetch_object($res);
                //echo "c_zone=".$c_zone.", id_nation=".$id_nation." ,row->ID_NATION_012=".$row->ID_NATION_012."<br/>";
                switch ($c_zone)
                {
                    case "G":
                        if ($id_nation == $row->ID_NATION_012)
                        {
                            $i_zone_bataille = 2;
                        }
                        else
                        {
                            $i_zone_bataille = 3;
                        }
                        break;
                    case "C":
                        if ($id_nation == $row->ID_NATION_012)
                        {
                            $i_zone_bataille = 1;
                        }
                        else
                        {
                            $i_zone_bataille = 4;
                        }
                        break;
                    case "D":
                        if ($id_nation == $row->ID_NATION_012)
                        {
                            $i_zone_bataille = 0;
                        }
                        else
                        {
                            $i_zone_bataille = 5;
                        }
                        break;
                    default:
                        $i_zone_bataille = -1;
                        break;
                }

                //insertion de l'ordre de combat dans la zone demand�e		
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`,`ID_BATAILLE`,`I_ZONE_BATAILLE`) VALUES (";
                //$requete.="NULL , ".$id_combat.", ".$id_combat.", ".$id_partie.", ".$i_tour.", ".ORDRE_COMBAT.", ''";;
                $requete.=AjouterIDOrdre($db, $id_partie) . " , " . $id_combat . ", " . $id_combat . ", " . $id_partie . ", " . $i_tour . ", " . ORDRE_COMBAT . ", ''";
                ;
                $requete.=", -1, -1, -1, -1, -1," . $id_bataille . ", " . $i_zone_bataille;
                $requete.=")";
                //echo $requete."<br/>";
                mysql_query($requete, $db);
            }

            //if (FALSE == empty($id_retrait))
            if (TRUE == isset($id_retrait) && TRUE == is_numeric($id_retrait))
            {
                //annule l'ordre precedent en cas de F5
                $requete = "DELETE FROM tab_vaoc_ordre WHERE ID_PION = " . $id_combat . " AND ID_PARTIE=" . $id_partie . " AND I_TOUR=" . $i_tour;
                $requete.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $id_bataille;
                //echo $requete."<br/>";
/*                mysql_query($requete, $db);
                $requete = "SELECT ID_NATION_012";
                $requete.=" FROM tab_vaoc_bataille ";
                $requete.="WHERE ID_PARTIE=" . $id_partie . " AND ID_BATAILLE=" . $id_bataille;
                //echo $requete."<br/>";
                $res = mysql_query($requete, $db);
                $row = mysql_fetch_object($res);*/

                //insertion de l'ordre de combat dans la zone demand�e		
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`,`ID_BATAILLE`,`I_ZONE_BATAILLE`) VALUES (";
                //$requete.="NULL , ".$id_combat.", ".$id_combat.", ".$id_partie.", ".$i_tour.", ".ORDRE_COMBAT.", ''";;
                $requete.=AjouterIDOrdre($db, $id_partie) . " , " . $id_retrait . ", " . $id_retrait . ", " . $id_partie . ", " . $i_tour . ", " . ORDRE_RETRAIT . ", ''";                
                $requete.=", -1, -1, -1, -1, -1," . $id_bataille . ", -1";
                $requete.=")";
                //echo $requete."<br/>";
                mysql_query($requete, $db);
            }
            ?>
        </div>
        <script type="text/javascript">
            //Fonctions d'affichage des infosbulles sur les unit�s
            showingTitle = false;
            mousex = 0;
            mousey = 0;
            function getMouseCoord(e) {
                //pour ie seulement... onmouseove ne lui renvoye pas l'�venement, contrairement au moteur gecko de FF/mozilla
                if (!e) {
                    e = window.event;
                }

                textePresent = false;
                //on peut ici avec le moteur gecko utiliser directement e.pageX et e.pageY, mais ie encore...
                if (e.pageX || e.pageY)
                {
                    mousex = e.pageX;
                    mousey = e.pageY;
                }
                else if (e.clientX || e.clientY)
                {
                    mousex = e.clientX + document.body.scrollLeft
                            + document.documentElement.scrollLeft;
                    mousey = e.clientY + document.body.scrollTop
                            + document.documentElement.scrollTop;
                }
                if (e) {
                    //simulerTitle("infobulle= " + posiHG[0]);
                    //si un title est affich�, lancons son suivi de souris	            
                    if (showingTitle) {
                        updateTitlePos();
                    }
                }
            }
            function updateTitlePos() {
                document.getElementById("infobulle").style.left = mousex + "px";
                ;
                document.getElementById("infobulle").style.top = mousey + "px";
                ;
            }

            function simulerTitle(idPion, iInfanterie, iCavalerie, iArtillerie,
                    iMoral, iMoralMax, iFatigue, iExperience, iMateriel, iRavitaillement) {
                txt = "";//idPion;
                if (iInfanterie > 0) {
                    txt = txt + " Infanterie:" + Math.round(iInfanterie - iInfanterie * iFatigue / 100);
                }
                ;
                if (iCavalerie > 0) {
                    txt = txt + " Cavalerie:" + Math.round(iCavalerie - iCavalerie * iFatigue / 100);
                }
                ;
                if (iArtillerie > 0) {
                    txt = txt + " Artillerie:" + Math.round(iArtillerie - iArtillerie * iFatigue / 100);
                }
                ;
                if (iMoralMax > 0)
                {
                    //unite appartenant au joueur
                    if ((iInfanterie > 0 || iCavalerie > 0))
                    {
                        txt = txt + "<br/> moral/max:" + iMoral + "/" + iMoralMax
                                + " Fatigue:" + iFatigue
                                + " Exp&eacute;rience: " + iExperience 
                                + "<br/>" + " Mat&eacute;riel:" + iMateriel 
                                + " Ravitaillement:" + iRavitaillement;
                    }
                    else
                    {
                        //artillerie pure
                        txt = txt + "<br/> Fatigue:" + iFatigue
                                + " Exp&eacute;rience: " + iExperience 
                    }
                }
                txt = txt + "<br/>";
                afficherInfoBulle(txt);
            }

            function simulerTitleVictoire(idPion, iInfanterieAvant,
                    iInfanterieApres, iCavalerieAvant, iCavalerieApres,
                    iArtillerieAvant, iArtillerieApres, iMoralAvant,
                    iMoralApres, iMoralMax, iFatigueAvant, iFatigueApres,
                    iExperience) {
                txt = "Avant";
                if (iInfanterieAvant > 0 || iCavalerieAvant > 0 || iArtillerieAvant > 0) {
                    txt = txt + "<br/>"
                }
                ;
                if (iInfanterieAvant > 0) {
                    txt = txt + " Infanterie:" + Math.round(iInfanterieAvant - iInfanterieAvant * iFatigueAvant / 100);
                }
                ;
                if (iCavalerieAvant > 0) {
                    txt = txt + " Cavalerie:" + Math.round(iCavalerieAvant - iCavalerieAvant * iFatigueAvant / 100);
                }
                ;
                if (iArtillerieAvant > 0) {
                    txt = txt + " Artillerie:" + Math.round(iArtillerieAvant - iArtillerieAvant * iFatigueAvant / 100);
                }
                ;

                if (iInfanterieAvant > 0) {
                    txt = txt + " Infanterie:" + Math.round(iInfanterieAvant - iInfanterieAvant * iFatigueAvant / 100);
                }
                ;
                if (iCavalerieAvant > 0) {
                    txt = txt + " Cavalerie:" + Math.round(iCavalerieAvant - iCavalerieAvant * iFatigueAvant / 100);
                }
                ;
                if (iArtillerieAvant > 0) {
                    txt = txt + " Artillerie:" + Math.round(iArtillerieAvant - iArtillerieAvant * iFatigueAvant / 100);
                }
                ;
                txt = txt + "<br/> moral/max:" + iMoralAvant + "/" + iMoralMax +
                        " Fatigue:" + iFatigueAvant +
                        " Exp&eacute;rience: " + iExperience + "<br/>";

                txt = txt + "<br/>";
                txt = txt + "Apres";
                if (iInfanterieApres > 0 || iCavalerieApres > 0 || iArtillerieApres > 0) {
                    txt = txt + "<br/>";
                }
                ;
                if (iInfanterieApres > 0) {
                    txt = txt + " Infanterie:" + Math.round(iInfanterieApres - iInfanterieApres * iFatigueApres / 100);
                }
                ;
                if (iCavalerieApres > 0) {
                    txt = txt + " Cavalerie:" + Math.round(iCavalerieApres - iCavalerieApres * iFatigueApres / 100);
                }
                ;
                if (iArtillerieApres > 0) {
                    txt = txt + " Artillerie:" + Math.round(iArtillerieApres - iArtillerieApres * iFatigueApres / 100);
                }
                ;
                txt = txt + "<br/> moral/max:" + iMoralApres + "/" + iMoralMax +
                        " Fatigue:" + iFatigueApres +
                        " Exp&eacute;rience: " + iExperience + "<br/>";

                afficherInfoBulle(txt);
            }

            function simulerTitleQG(idPion, iTactique, iStrategie,
                    cHierarchie)
            {
                txt = " Tactique:" + iTactique +
                        " Strat&eacute;gie:" + iStrategie +
                        " Niveau hierarchique: " + cHierarchie + "<br/>";
                afficherInfoBulle(txt);
            }

            function afficherInfoBulle(txt)
            {
                //on remplis avec le texte
                document.getElementById("infobulle").innerHTML = txt;

                //on place le div au bon endroit
                document.getElementById("infobulle").style.left = mousex;
                document.getElementById("infobulle").style.top = mousey;

                //on l'affiche en temps qu'�l�ment inline
                document.getElementById("infobulle").style.display = "inline";
                //on pr�viens le script qu'on est en train d'afficher un title
                showingTitle = true;
            }

            function hideTitle() {
                //on previens le script
                showingTitle = false;
                //qu'on masque le div
                document.getElementById("infobulle").style.display = "none";
            }

            document.onmousemove = getMouseCoord;

            function callRetourQG()
            {
                formPrincipale = document.getElementById("principal");
                formPrincipale.action = "vaocqg_s.php";
                formPrincipale.target = "_self";
                formPrincipale.submit();
            }

            function callRetraiteGenerale(idPion, active) {
                //alert(idPion);
                if (1 === active)
                {
                    document.getElementById("id_retraiter").value = idPion;
                }
                else
                {
                    document.getElementById("id_annuler_retraiter").value = idPion;
                }
                formPrincipale = document.getElementById("principal");
                formPrincipale.action = "vaocbataille_s.php";
                formPrincipale.target = "_self";
                formPrincipale.submit();
            }

            function callAnnulerPion(idPion) {
                //alert(idPion);
                document.getElementById("id_annuler").value = idPion;
                formPrincipale = document.getElementById("principal");
                formPrincipale.action = "vaocbataille_s.php";
                formPrincipale.target = "_self";
                formPrincipale.submit();
            }

            function callCombatPion(idPion, cZone) {
                alert(idPion+cZone);
                document.getElementById("id_combat").value = idPion;
                document.getElementById("c_zone").value = cZone;
                formPrincipale = document.getElementById("principal");
                formPrincipale.action = "vaocbataille_s.php";
                formPrincipale.target = "_self";
                formPrincipale.submit();
            }

            function callChangementEngagement()
            {
                //alert("callChangementEngagement");
                formPrincipale = document.getElementById("principal");
                formPrincipale.action = "vaocbataille_s.php";
                formPrincipale.target = "_self";
                formPrincipale.submit();
            }

            function callRetrait(idPion) {
                //alert(idPion);
                document.getElementById("id_retrait").value = idPion;
                formPrincipale = document.getElementById("principal");
                formPrincipale.action = "vaocbataille_s.php";
                formPrincipale.target = "_self";
                formPrincipale.submit();
            }

        </script>
        <div class="container-fluid">
        <form method="post" id="principal" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <?php
            //champ caches
            if (empty($tri_liste))
            {
                //lorsque l'on arrive sur la page, on est forcement sur la premi�re page des messages
                $tri_liste = "DT_ARRIVEE";
                $ordre_tri_liste = "DESC";
            }

            echo "<input id='id_partie' name='id_partie' type='hidden' value='" . $id_partie . "' />";
            if (FALSE == empty($id_login))
            {
                echo "<input id='id_login' name='id_login' type='hidden' value='" . $id_login . "' />";
                echo "<input id='id_role' name='id_role' type='hidden' value='" . $id_role . "' />";
                echo "<input id='id_nation' name='id_nation' type='hidden' value='" . $id_nation . "' />";
                echo "<input id='id_bataille' name='id_bataille' type='hidden' value='" . $id_bataille . "' />";
                echo "<input id='id_retraiter' name='id_retraiter' type='hidden' />";
                echo "<input id='id_annuler_retraiter' name='id_annuler_retraiter' type='hidden' />";
                echo "<input id='id_annuler' name='id_annuler' type='hidden' />";
                echo "<input id='id_retrait' name='id_retrait' type='hidden' />";
            }
            echo "<input id='id_combat' name='id_combat' type='hidden' />";
            echo "<input id='c_zone' name='c_zone' type='hidden' />";
            echo "<input id='tri_liste' name='tri_liste' type='hidden' value='" . $tri_liste . "' />";
            echo "<input id='ordre_tri_liste' name='ordre_tri_liste' type='hidden' value='" . $ordre_tri_liste . "' />";

            $requete = "SELECT ID_BATAILLE, S_NOM, DT_BATAILLE_DEBUT, DATE_FORMAT(DT_BATAILLE_DEBUT ,'%e %M %Y %H:%i') AS DATE_DEBUT, DT_BATAILLE_FIN, DATE_FORMAT(DT_BATAILLE_FIN ,'%e %M %Y %H:%i') AS DATE_FIN, C_ORIENTATION";
            $requete.=", S_TERRAIN0, S_TERRAIN1, S_TERRAIN2, S_TERRAIN3, S_TERRAIN4, S_TERRAIN5";
            $requete.=", S_COULEURTERRAIN0, S_COULEURTERRAIN1, S_COULEURTERRAIN2, S_COULEURTERRAIN3, S_COULEURTERRAIN4, S_COULEURTERRAIN5";
            $requete.=", S_OBSTACLE0, S_OBSTACLE1, S_OBSTACLE2, S_COULEUROBSTACLE0, S_COULEUROBSTACLE1, S_COULEUROBSTACLE2, ID_LEADER_012, ID_LEADER_345, ID_NATION_012, ID_NATION_345";
            $requete.=" FROM tab_vaoc_bataille ";
            $requete.="WHERE ID_PARTIE=" . $id_partie . " AND ID_BATAILLE=" . $id_bataille;
            //echo $requete."<br/>";
            $res_bataille = mysql_query($requete, $db);
            $row_bataille = mysql_fetch_object($res_bataille);

            //recherche du repertoire des images
            $requete = "SELECT S_REPERTOIRE, ID_VICTOIRE ";
            $requete.="FROM tab_vaoc_partie ";
            $requete.="WHERE ID_PARTIE=" . $id_partie;
            //echo $requete."<br/>";
            $res_partie = mysql_query($requete, $db);
            $row_partie = mysql_fetch_object($res_partie);

            echo "<table border='0' width='100%'><tr><td style=\"white-space:nowrap;  font-size: xx-large;\">";
            echo "<table><tr><td>";
            AfficherCommandantEnChef($db, $id_partie, $row_bataille->ID_BATAILLE, $row_bataille->ID_LEADER_012, $id_nation, $row_bataille->ID_NATION_012, $id_pion_role, $i_tour, $row_partie->ID_VICTOIRE);
            echo "</td><td>";
            echo "<strong> VS </strong>";
            echo "</td><td>";
            AfficherCommandantEnChef($db, $id_partie, $row_bataille->ID_BATAILLE, $row_bataille->ID_LEADER_345, $id_nation, $row_bataille->ID_NATION_345, $id_pion_role, $i_tour, $row_partie->ID_VICTOIRE);
            echo "</td></tr></table>";
            //echo "</div>";
            //echo "<img id=\"rose_des_vents\" alt=\"rose des vents\" src=\"images/rosedesventsN.png\"  height=\"240px\" width=\"240px\" />";
            echo "</td><td class=\"text-center\">";
            echo "<h1><strong>" . $row_bataille->S_NOM . "</strong></h1>";
            if (empty($row_bataille->DATE_FIN) || 0 == $row_bataille->DATE_FIN)
            {
                echo "<h2>d&eacute;but&eacute;e le " . $row_bataille->DATE_DEBUT . "</h2>";
            }
            else
            {
                echo "<h2>du " . $row_bataille->DATE_DEBUT . " au " . $row_bataille->DATE_FIN . "</h2>";
            }
            echo "</td><td align='right'>";
            echo "<img alt='image_champ_de_bataille' src='" . $repertoire . "/bataille_" . $id_bataille . ".png' height='240px' width='240px'/>";
            echo "</td></tr></table>";

            if ($row_bataille->C_ORIENTATION == "V")
            {
                AfficherBatailleVerticale($db, $id_partie, $id_bataille, $id_nation, $id_pion_role, $i_tour, $row_partie->ID_VICTOIRE);
            }
            else
            {
                AfficherBatailleHorizontale($db, $id_partie, $id_bataille, $id_nation, $id_pion_role, $i_tour, $row_partie->ID_VICTOIRE);
            }
            
function ImageTri($tri)
{
    if ($tri=="") {echo("&nbsp;<img alt='decroissant' src='images/Alphabetical_Sorting-26.png' />");} 
        else {echo("&nbsp;<img alt='croissant' src='images/Alphabetical_Sorting2-26.png' />");}            
}

            if ($row_partie->ID_VICTOIRE < 0)
            {
                //on n'affiche les messages que si la partie n'est pas finie
                
                //echo "<h2 style=\"text-align: center;\" id=\"tableau_messages\">Messages</h2>";
                echo "<div class=\"row\" id=\"tableau_messages\" style=\"margin-top:10px;\">";
                    echo "<div class=\"col-12 col-sm-2 col-md-2\">";
                        //echo "<a href='nojs.htm' onclick=\"javascript: callTri('DT_DEPART'); return false;\"><label class=\"control-label\">Envoy&eacute;</label></a>";
                        echo "<button class=\"btn btn-light bouton \" type=\"button\" id=\"triDT_DEPART\">Envoy&eacute";
                        if ($tri_liste=="DT_DEPART") { ImageTri($ordre_tri_liste);}
                        echo "</button>";
                    echo "</div>";
                    echo "<div class=\"col-12 col-sm-2 col-md-2\">";
                        //echo "<a href='nojs.htm' onclick=\"javascript: callTri('DT_ARRIVEE'); return false;\"><label class=\"control-label\">Re&ccedil;u</label></a>";
                        echo "<button class=\"btn btn-light bouton \" type=\"button\" id=\"triDT_ARRIVEE\">Re&ccedil;u";
                        if ($tri_liste=="DT_ARRIVEE") { ImageTri($ordre_tri_liste);}
                        echo "</button>";
                    echo "</div>";
                    // Liste des ordres envoyes dans une "table" triable geree avec des divs
                    echo "<div class=\"col-12 col-sm-2 col-md-2\">";
                        //echo "<a href='nojs.htm' onclick=\"javascript: callTri('S_NOM'); return false;\"><label class=\"control-label\">Par</label></a>";
                        echo "<button class=\"btn btn-light bouton \" type=\"button\" id=\"triS_NOM\">Par";
                        if ($tri_liste=="S_NOM") { ImageTri($ordre_tri_liste);}
                        echo "</button>";
                    echo "</div>";
                    echo "<div class=\"col-12 col-sm-6 col-md-6 text-center\">";
                        echo "<label class=\"control-label bouton\"><strong>Messages</strong></label>";
                    echo "</div>";
                echo "</div>";
                if (empty($pageNum_recus))
                {
                    //lorsque l'on arrive sur la page, on est forcement sur la premiere page des messages
                    $pageNum_recus = 0;
                }

                $requete = "SELECT DATE_FORMAT(DT_DEPART,'%W %e %M %Y %H:%i') AS DATE_DEPART, DATE_FORMAT(DT_ARRIVEE,'%W %e %M %Y %H:%i') AS DATE_ARRIVEE, S_NOM, S_MESSAGE ";
                $requete.=" FROM tab_vaoc_message, tab_vaoc_pion, tab_vaoc_bataille_pions ";
                $requete.=" WHERE tab_vaoc_message.ID_PION_PROPRIETAIRE=" . $id_pion_role;
                $requete.=" AND tab_vaoc_message.ID_PARTIE=" . $id_partie;
                $requete.=" AND tab_vaoc_message.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
                $requete.=" AND tab_vaoc_message.ID_EMETTEUR=tab_vaoc_pion.ID_PION";
                $requete.=" AND DT_DEPART>='" . $row_bataille->DT_BATAILLE_DEBUT."'";
                $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE = tab_vaoc_message.ID_PARTIE";
                $requete.=" AND tab_vaoc_bataille_pions.ID_PION = tab_vaoc_message.ID_EMETTEUR";
                $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE=".$id_bataille;
                //echo $requete;
                $res_messages = mysql_query($requete, $db);
                $nb_messages_recus = mysql_num_rows($res_messages);
                $offset_recus = ($pageNum_recus - 1) * NB_MESSAGES_MAX;
                if ($offset_recus < 0)
                {
                    $offset_recus = 0;
                }

                $requete.=" ORDER BY ". $tri_liste . " " . $ordre_tri_liste ." ,ID_MESSAGE DESC LIMIT " . $offset_recus . "," . NB_MESSAGES_MAX;
                //echo $requete;
                //echo "nb_messages_recus=".$nb_messages_recus;
                
                $res_messages = mysql_query($requete, $db);
                if (0 == $nb_messages_recus)
                {
                    echo "<div class=\"row\">";
                    echo "<div class=\"col-12\">Vous n'avez re&ccedil;us aucun message</div>";
                    echo "</div>";
                    //echo "<tr><td colspan='4'>Vous n'avez re&ccedil;us aucun message</td></tr>";
                }
                else
                {
                    echo "<div class=\"row\" style=\"background-color:white;\">";
                    while ($row_message = mysql_fetch_object($res_messages))
                    {
                        echo "<hr style=\"width: 100%; border-top: 1px solid black; margin-top:0px; margin-bottom:0px;\" />";
                        echo "<div class=\"col-12 col-sm-2 col-md-2\" style=\"margin:0px;\">" . $row_message->DATE_DEPART . "</div>";// Ã  l'origine col-12 col-sm-2 col-md-2
                        echo "<div class=\"col-12 col-sm-2 col-md-2\">" . $row_message->DATE_ARRIVEE . "</div>";// Ã  l'origine col-12 col-sm-2 col-md-2
                        echo "<div class=\"col-12 col-sm-2 col-md-2\">" . $row_message->S_NOM . "</div>";// Ã  l'origine: col-12 col-sm-4 col-md-3
                        echo "<div class=\"col-12 col-sm-6 col-md-6\">" . $row_message->S_MESSAGE . "</div>";// Ã  l'origine: col-12 col-sm-3 col-md-6
                        /*
                        echo "<tr>";
                        echo "<td>" . $row_message->DATE_DEPART . "</td><td>" . $row_message->DATE_ARRIVEE . "</td>";
                        echo "<td>" . $row_message->S_NOM . "</td><td>" . $row_message->S_MESSAGE . "</td>";
                        echo "</tr>";
                         * */
                    }
                    echo "</div>";

                    $maxPage_recus = ceil($nb_messages_recus / NB_MESSAGES_MAX);
                    //echo "maxPage_recus=".$maxPage_recus;

                    if ($maxPage_recus > 1)
                    {
                        echo "<div class=\"row\">";
                        echo "<div class=\"col-12\">";
                        echo "<nav>";
                        echo "<ul class=\"pagination\">";
                        echo "<li class=\"page-item\">";
                            echo "<a  class=\"page-link\" href=\"#\" aria-label=\"Precedent\" onclick=\"javascript:callAllerALapageB(".max(1,$pageNum_recus-NB_PAGINATION_MAX).");return false;\">";
                            echo "<span aria-hidden=\"true\">&laquo;</span>";
                            echo "<span class=\"sr-only\">Precedent</span>";
                            echo "</a>";
                        echo "</li>";
                        $debut_pagination_Num = max(1, min ($pageNum_recus,  $pageNum_recus - NB_PAGINATION_MAX/2));
                        $fin_pagination_Num = $debut_pagination_Num + min(NB_PAGINATION_MAX/2,$maxPage_recus - $debut_pagination_Num);
                        //echo "debut_pagination_Num=".$debut_pagination_Num;
                        //echo "fin_pagination_Num=".$fin_pagination_Num;
                        for ($page = $debut_pagination_Num; $page <= $fin_pagination_Num; $page++)
                        {
                            if ($page == $pageNum_recus)
                            {
                                echo "<li class=\"page-item ative\">";
                                echo "<span class=\"page-link\"><strong>". $page ."</strong><span class=\"sr-only\" aria-hidden=\"true\"></span></span>";
                            }
                            else
                            {
                                echo "<li class=\"page-item\">";
                                echo "<a class=\"page-link\" href=\"#\" onclick=\"javascript:callAllerALapageB(". $page .");return false;\">";
                                echo "<span aria-hidden=\"true\">". $page . "</span>";
                                echo "</a>";
                            }
                            echo "</li>";
                        }
                        echo "<li class=\"page-item\">";
                            echo "<a class=\"page-link\"  href=\"#\" aria-label=\"Suivant\" onclick=\"javascript:callAllerALapageB(". min($maxPage_recus , $pageNum_recus + NB_PAGINATION_MAX) .");return false;\">";
                            echo "<span aria-hidden=\"true\">&raquo;</span>";
                            echo "<span class=\"sr-only\">Suivant</span>";
                            echo "</a>";
                        echo "</li>";
                        echo "</ul></nav></div></div>";
                    }
                }
            }
            ?>

            <div style='text-align:center;'>
                <br/>
                <button name="id_aide" class="btn btn-info bouton" 
                       id="id_aide" onclick="javascript: window.open('aide_s.php'); return false;"
                       type="button" alt="connexion" >Aide</button>

                <button name="id_quitter" class="btn btn-light bouton"
                       id="id_quitter" onclick="javascript: window.close();"
                       type="button" alt="retour a l'ecran general" value="submit" >Quitter</button>
            </div>
            <div id="infobulle"></div>
            <input id="pageNum_recus" name="pageNum_recus" type="hidden" />
        </form>	
        </div>

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="js/jquery-3.4.1.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap4.min.js"></script>
        <!-- pour le lazy loading des images  -->
         <script src="js/jquery.lazy.min.js"></script>
        <!--type ahead pour les destinations mais non en fait  <script src="js/typeahead.bundle.js"></script> -->        
        <script src="js/vaoc_s.js" type="application/javascript"></script>
        
        <script type="text/javascript">       
        $(document).ready(function () {
            $('#triDT_DEPART').on('click', function () {
                callTriB('DT_DEPART');
                <?php
                    //echo "alert(\"$.post('vaocbataille_s.php', ".$paramsChargement."DT_DEPART'});\");";
                    //echo "$.post('vaocbataille_s.php', ".$paramsChargement."DT_DEPART'});";
                ?>
            });

            $('#triDT_ARRIVEE').on('click', function () {
                callTriB('DT_ARRIVEE');
            });

            $('#triS_NOM').on('click', function () {
                callTriB('S_NOM');
            });
        });
        </script>
    </body>
</html>