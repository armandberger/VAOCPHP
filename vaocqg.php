<?php session_start();//sinon les variables $_SESSION ne marchent pas
?>
<!DOCTYPE html>
<html>
    <head>
        <title>VAOC : QG</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="Description" content="VAOC Quartier Général"/>
        <meta name="Keywords" content="VAOC, QG"/>
        <meta name="Identifier-URL" content="http://vaoc.free.fr/vaoc/vaocqg.php"/>
        <meta name="revisit-after" content="31"/>
        <meta name="Copyright" content="copyright armand BERGER"/>
        <meta name="application-name" content="VAOC" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
        <link rel="icon" type="image/png" href="images/favicon.png" />
        <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
        <link href="css/vaoc2.css" rel="stylesheet">
        <link href="css/bootstrap.css" rel="stylesheet">
        <style type="text/css"> 
        body
        {
            color : white;
            background-color:#434223; 
            background-image:url(images/fondqg.png);
        }

        #editor {
                max-height: 250px;
                height: 250px;
                background-color: white;
                border-collapse: separate; 
                border: 1px solid rgb(204, 204, 204); 
                padding: 4px; 
                box-sizing: content-box; 
                -webkit-box-shadow: rgba(0, 0, 0, 0.0745098) 0px 1px 1px 0px inset; 
                box-shadow: rgba(0, 0, 0, 0.0745098) 0px 1px 1px 0px inset;
                border-top-right-radius: 3px; border-bottom-right-radius: 3px;
                border-bottom-left-radius: 3px; border-top-left-radius: 3px;
                overflow: scroll;
                outline: none;
                color:black;
            }
            div[data-role="editor-toolbar"] {
              -webkit-user-select: none;
              -moz-user-select: none;
              -ms-user-select: none;
              user-select: none;
            }

            .dropdown-menu a {cursor: pointer;}

        </style> 
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="js/jquery-3.1.1.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.js"></script>
        <!-- https://github.com/steveathon/bootstrap-wysiwyg -->
        <script src="js/bootstrap-wysiwyg.min.js"></script>
        <!-- https://github.com/jeresig/jquery.hotkeys -->
        <script src="js/jquery.hotkeys.js"></script>
        <!--
        <script src="bootstrap-wysiwyg-master/external/google-code-prettify/run_prettify.js"></script>
        -->

        <!-- http://silviomoreto.github.io/bootstrap-select/ -->
        <!-- pour selectpicket -->
        <link rel="stylesheet" href="css/bootstrap-select.css">
        <script src="js/bootstrap-select.js"></script>

        <!--type ahead pour les destinations mais non en fait  <script src="js/typeahead.bundle.js"></script> -->        
        <script src="js/vaoc.js"></script>        
    </head>
    <body>
        <?php
        //Pour tester la vitesse de chargement : https://developers.google.com/speed/pagespeed/
        //43 le 30/06/2019 pour http://vaoc.free.fr/vaocqg.php?id_login=Pils
        
        //header("Content-Type:text/html; charset=iso-8859-1");
        require("vaocbase.php"); //include obligatoire pour l'execution
        require("vaocfonctions.php"); //include obligatoire pour l'execution
        /* 	if (!headers_sent())
          {
          echo "!headers_sent()";
          header("Content-type: text/html; charset=ISO-8859-1");
          } */

        //pratique pour le debug
        /*
        echo "liste des valeurs transmises dans le request<br/>";
        while (list($name, $value) = each($_SESSION)) {echo "$name = $value<br>\n";}
        echo "--------------------------------------------------<br/>";        
        //while (list($name, $value) = each($_REQUEST)) {echo "$name = $value<br>\n";}
        //while (list($name, $value) = each($_POST)) {echo "$name = $value<br>\n";}
        //while (list($name, $value) = each($_GET)) {echo "$name = $value<br>\n";}
        //while (list($name, $value) = each($_SERVER)) {echo "$name = $value<br>\n";}
        /**/
        //IMPORTANT : pour voir les messages, penser à mettre en commentaire le callMoi() en fin de page
        //converti toutes les variables REQUEST en variables du meme nom
        extract($_REQUEST, EXTR_OVERWRITE);
        //extract($_POST, EXTR_OVERWRITE);

        //connection a la base
        $db = @db_connect();
        //mysql_set_charset("utf-8", $db); -> pas valide dans la version php free
        //fixe le francais comme langue pour les dates
        $requete = "SET lc_time_names = 'fr_FR'";
        mysql_query($requete, $db);
        mysql_query("SET NAMES 'utf8'");
        if (FALSE == empty($id_login))
        {
            $message = "";
            //on verifie si le login existe
            $res_login = mysql_query("SELECT S_LOGIN, ID_UTILISATEUR FROM tab_utilisateurs WHERE S_LOGIN='" . trim($id_login) . "'", $db);
            if (mysql_num_rows($res_login) <= 0)
            {
                $message = $message . "Le nom d'utilisateur est inconnu.<br/>";
            }
            else
            {
                $login = mysql_fetch_object($res_login);

                $requete = "SELECT tab_vaoc_role.ID_ROLE, tab_vaoc_role.ID_PARTIE, tab_vaoc_role.S_NOM AS NOM_ROLE,";
                $requete.=" tab_vaoc_partie.S_NOM AS NOM_PARTIE, DATE_FORMAT(tab_vaoc_partie.DT_TOUR ,'%W %e %M %Y %H:%i') AS DATE_PARTIE";
                $requete.=" FROM tab_vaoc_role, tab_vaoc_partie, tab_vaoc_pion";
                $requete.=" WHERE (tab_vaoc_role.ID_PARTIE=tab_vaoc_partie.ID_PARTIE) AND ID_UTILISATEUR=" . $login->ID_UTILISATEUR;
                $requete.=" AND (tab_vaoc_role.ID_PARTIE=tab_vaoc_pion.ID_PARTIE)";
                $requete.=" AND (tab_vaoc_role.ID_PION=tab_vaoc_pion.ID_PION)";
                $requete.=" ORDER BY NOM_ROLE";
                //echo "role:".$requete."<br/>";
                $res_role = mysql_query($requete, $db);
                //on verifie si l'utilisateur a des parties en cours
                if (mysql_num_rows($res_role) <= 0)
                {
                    $message.="Vous n'&ecirc;tes inscrit &agrave; aucune partie ou vous n'etes pas encore pr&eacute;sent sur les lieux.<br/>";
                }
            }
        }
        else
        {
            $message = "Erreur, vous devez d'abord vous connectez pour acc&eacute;der &agrave; la page";
        }

        if (strlen($message) > 0)
        {
            $message = "<body> <div class=\"alerte\">" . $message;
            $message.= "<input alt=\"quitter\" id='id_quitter' name='id_quitter' class=\"btn btn-default\" type='image' value='submit' src=\"images/btnQuitter2.png\" onclick=\"javascript:location.href='index.php';\" />";
            $message.= "</div></body></html>";
            die($message);
        }

        //recherche du role courant et des elements generiques du jeu et de la partie 
        $requete = "SELECT tab_vaoc_role.ID_ROLE, tab_vaoc_role.ID_PARTIE, tab_vaoc_role.S_NOM AS NOM_ROLE, tab_vaoc_role.ID_PION, tab_vaoc_role.B_ORDRES_TERMINES, ";
        $requete.=" tab_vaoc_partie.I_LARGEUR_CARTE_ZOOM, tab_vaoc_partie.I_HAUTEUR_CARTE_ZOOM,";
        $requete.=" tab_vaoc_role.S_COULEUR_FOND, tab_vaoc_role.S_COULEUR_TEXTE, tab_vaoc_partie.H_JOUR, tab_vaoc_partie.H_NUIT, ";
        $requete.=" tab_vaoc_jeu.S_NOM AS NOM_JEU, tab_vaoc_jeu.S_IMAGE, tab_vaoc_partie.S_NOM AS NOM_PARTIE, ";
        $requete.=" DATE_FORMAT(DATE_ADD(tab_vaoc_jeu.DT_INITIALE, INTERVAL tab_vaoc_jeu.I_NOMBRE_TOURS HOUR),'%W %e %M %Y %H:%i') AS DATE_FIN, S_METEO, ";
        $requete.=" DATE_FORMAT(tab_vaoc_partie.DT_TOUR ,'%W %e %M %Y %H:%i') AS DATE_PARTIE, ";
        $requete.=" tab_vaoc_partie.FL_MISEAJOUR, tab_vaoc_partie.FL_DEMARRAGE, tab_vaoc_partie.ID_VICTOIRE,";
        $requete.=" tab_vaoc_partie.I_TOUR, tab_vaoc_role.ID_NATION";
        $requete.=" FROM tab_vaoc_role, tab_vaoc_partie, tab_vaoc_jeu";
        $requete.=" WHERE (tab_vaoc_role.ID_PARTIE=tab_vaoc_partie.ID_PARTIE)";
        $requete.=" AND (tab_vaoc_jeu.ID_JEU=tab_vaoc_partie.ID_JEU)";
        if (true == empty($liste_roles))
        {
            //on vient d'arriver sur la page, on a donc en selection le role de la partie par defaut
            $requete.=" AND ID_UTILISATEUR=" . $login->ID_UTILISATEUR;
            $requete.=" ORDER BY NOM_ROLE";
        }
        else
        {
            $requete.=" AND tab_vaoc_role.ID_ROLE=" . $liste_roles % 10000;
            $requete.=" AND tab_vaoc_role.ID_PARTIE=" . ($liste_roles - $liste_roles % 10000) / 10000;
        }
        //echo "role courant=".$requete;
        $res_role_partie = mysql_query($requete, $db);
        //echo "nb resultats=".mysql_num_rows($res_role_partie);
        $row_role = mysql_fetch_object($res_role_partie);
        $id_role = $row_role->ID_ROLE; //comme cela la liste_roles est renseigne meme la premiere fois
        $id_pion_role = $row_role->ID_PION; //pion du role courant
        $id_partie = $row_role->ID_PARTIE;
        $id_nation = $row_role->ID_NATION;
        $i_tour = $row_role->I_TOUR;
        $fl_demmarage = $row_role->FL_DEMARRAGE;
        $largeur_zoom = $row_role->I_LARGEUR_CARTE_ZOOM;
        $hauteur_zoom = $row_role->I_HAUTEUR_CARTE_ZOOM;
        $idVictoire = $row_role->ID_VICTOIRE;
        $b_ordres_termines = $row_role->B_ORDRES_TERMINES;
        $nom_jeu = $row_role->NOM_JEU;
        if ($idVictoire >= 0)
        {
            $fl_demmarage = 0;
        }//la partie n'est plus active

        if (empty($pageNum_recus))
        {
            //lorsque l'on arrive sur la page, on est forcement sur la premiere page des messages
            $pageNum_recus = 0;
        }

        //si la mise a jour est en cours, on ne va pas plus loin
        if (1 == $row_role->FL_MISEAJOUR)
        {
            echo "<form method=\"post\" id=\"principal\" action=\"" . $_SERVER['PHP_SELF'] . "\">";
            echo "<h1>Un grognard passe actuellement le balai dans votre tente, merci d'y revenir un peu plus tard.</h1>";
            echo "<div style='text-align:center;'>";
            echo "<input class=\"btn btn-default\" alt=\"retour &agrave; l'ecran g&eacute;n&eacute;ral\" id=\"id_quitter\" name=\"id_quitter\" type=\"image\" value=\"submit\" src=\"images/btnQuitter2.png\" onclick=\"javascript:callQuitter();\" />";
            echo "</div>";
            echo "</form></body></html>";
            die;
        }

        //Execution des ordres
        //echo "cache_control = ".(filter_input(INPUT_SERVER, 'HTTP_CACHE_CONTROL') !== 'max-age=0');
        //la ligne suivante detecte si l'on a fait un refresh ou pas -> mais cela ne marche pas sur Chrome !
        //if (filter_input(INPUT_SERVER, 'HTTP_CACHE_CONTROL') !== 'max-age=0')
        //Pour un bug ? ce cretin de Chrome execute deux fois la page coté serveur, d'où la mise en place de la variable de SESSION action
        //echo "action=".$_SESSION['action']."</BR>";
        //echo "timestamp=".time()."</BR>";
        //ecrireLog("timestamp=".time());
        //ecrireLog("action=".$_SESSION['action']);
        //echo get_nom_navigateur($_SERVER['HTTP_USER_AGENT']);
        if (TRUE == isset($_SESSION['action']) && $_SESSION['action']=="OK"
                && (filter_input(INPUT_SERVER, 'HTTP_CACHE_CONTROL') !== 'max-age=0' 
                        || "Chrome"==get_nom_navigateur($_SERVER['HTTP_USER_AGENT'])))
        {
            $_SESSION['action']="";
            if (TRUE == isset($id_ordre_tri) && strlen($id_ordre_tri)>0)
            {
                $i_ordre = 0;
                $tri = explode(",", $id_ordre_tri);
                foreach ($tri as $otri)
                {
                    $requete = "UPDATE tab_vaoc_pion SET I_TRI=" . $i_ordre . " WHERE ";
                    $requete.=" tab_vaoc_pion.ID_PION=" . $otri;
                    $requete.=" AND tab_vaoc_pion.ID_PARTIE=" . ($liste_roles - $liste_roles % 10000) / 10000;
                    mysql_query($requete, $db);
                    //echo $requete;
                    $i_ordre++;
                }
            }

            //Mise a jour d'ordres termines
            if (TRUE == isset($id_ordres_termines) && TRUE == is_numeric($id_ordres_termines))
            {
                $requete = "UPDATE tab_vaoc_role SET B_ORDRES_TERMINES=" . $id_ordres_termines . " WHERE ";
                $requete.=" tab_vaoc_role.ID_ROLE=" . $liste_roles % 10000;
                $requete.=" AND tab_vaoc_role.ID_PARTIE=" . ($liste_roles - $liste_roles % 10000) / 10000;
                //echo $requete;
                $res_b_ordres_termines_update = mysql_query($requete, $db);
                $b_ordres_termines = $id_ordres_termines; //sinon l'affichage est faux 
            }

            if (TRUE == isset($id_supprimerallera) && TRUE == is_numeric($id_supprimerallera))
            {
                //suppression d'un ordre
                //s'il y a un ordre pr�cedent, il faut remettre l'ordre_suivant � -1
                $requete = "SELECT ID_ORDRE FROM tab_vaoc_ordre WHERE ID_PARTIE=" . $id_partie . " AND ID_ORDRE_SUIVANT=" . $id_supprimerallera;
                $res_ordre_precedent = mysql_query($requete, $db);
                if (mysql_num_rows($res_ordre_precedent) > 0)
                {
                    $row_ordre_precedent = mysql_fetch_object($res_ordre_precedent);
                    $requete = "UPDATE tab_vaoc_ordre SET ID_ORDRE_SUIVANT = -1";
                    $requete.=" WHERE tab_vaoc_ordre.ID_ORDRE=" . $row_ordre_precedent->ID_ORDRE;
                    $requete.=" AND ID_PARTIE=" . $id_partie;
                    mysql_query($requete, $db);
                }

                //s'il y a des ordres suivants, il faut �galement tous les supprimer
                $requete = "SELECT ID_ORDRE_SUIVANT FROM tab_vaoc_ordre WHERE ID_PARTIE=" . $id_partie . " AND ID_ORDRE=" . $id_supprimerallera;
                $res_ordre_suivant = mysql_query($requete, $db);
                if (mysql_num_rows($res_ordre_suivant) > 0)
                {
                    $row_ordre_suivant = mysql_fetch_object($res_ordre_suivant);
                    while ($row_ordre_suivant->ID_ORDRE_SUIVANT >= 0)
                    {
                        $id_ordre_suivant = $row_ordre_suivant->ID_ORDRE_SUIVANT;
                        //encore un ordre suivant ?
                        $requete = "SELECT ID_ORDRE_SUIVANT FROM tab_vaoc_ordre WHERE ID_PARTIE=" . $id_partie . " AND ID_ORDRE=" . $id_ordre_suivant;
                        $res_ordre_suivant = mysql_query($requete, $db);
                        $row_ordre_suivant = mysql_fetch_object($res_ordre_suivant);
                        //on supprimer l'ordre courant
                        $requete = "DELETE FROM tab_vaoc_ordre WHERE ID_ORDRE = " . $id_ordre_suivant . " AND ID_PARTIE=" . $id_partie;
                        mysql_query($requete, $db);
                    }
                }

                //on supprime (enfin) l'ordre d'origine
                $requete = "DELETE FROM tab_vaoc_ordre WHERE ID_ORDRE = " . $id_supprimerallera . " AND ID_PARTIE=" . $id_partie;
                //echo $requete;
                mysql_query($requete, $db);
                //unset($_POST["id_supprimerallera"]);
            }

            if (TRUE == isset($id_allera) && TRUE == is_numeric($id_allera))
            {
                if (TRUE == isset($_REQUEST["id_destination_mouvement_" . $id_allera]) && $_REQUEST["id_destination_mouvement_" . $id_allera]>0 )
                {
                    //on recherche l'id de la destination
                    $requete = "SELECT ID_NOM FROM tab_vaoc_noms_carte WHERE ID_PARTIE=" . $id_partie . " AND S_NOM ='";
                    if (get_magic_quotes_gpc())
                    {
                        // le cas sur Free alors que c'est une faille de securite...
                        //echo "get_magic_quotes_gpc() OFF";
                        $requete.= $_REQUEST["id_destination_mouvement_" . $id_allera] . "'";
                    }
                    else
                    {
                        //echo "get_magic_quotes_gpc() ON";
                        $requete.= mysql_real_escape_string($_REQUEST["id_destination_mouvement_" . $id_allera], $db) . "'";
                    }
                    //echo $requete;
                    //$res_destination = mysql_query($requete, $db);
                    //$row_destination = mysql_fetch_object($res_destination);

                    //on recherche le dernier ordre deja donne a ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                    $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_allera, $i_tour);

                    //insertion du nouvel ordre
                    $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                    $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                    $requete.=AjouterIDOrdre($db, $id_partie) . " , " . $id_allera . ", " . $id_allera . ", " . $id_partie . ", " . $i_tour . ", " . ORDRE_MOUVEMENT . ", '',";
                    $requete.=$_REQUEST['id_distance_mouvement_' . $id_allera] . ", ";
                    $requete.=$_REQUEST["id_direction_mouvement_" . $id_allera] . ", ";
                    //$requete.=$row_destination->ID_NOM . ", ";
                    $requete.=$_REQUEST["id_destination_mouvement_" . $id_allera] . ", ";
                    $requete.=$_REQUEST["id_heure_" . $id_allera] . ", ";
                    $requete.=$_REQUEST["id_duree_" . $id_allera];
                    $requete.=")";
                    //echo $requete;
                    mysql_query($requete, $db);

                    if ($id_ordre_precedent >= 0)
                    {
                        //il faut indiquer que l'ordre qui vient d'etre donne est le suivant du precedent
                        //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                        $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_allera, $i_tour);

                        //ensuite on met a jour l'ordre precedent
                        MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                    }
                }
                //unset($_POST["id_allera"]);
            }
            //echo "apres test II";

            if (TRUE == isset($id_patrouillera) && TRUE == is_numeric($id_patrouillera))
            {
                //ajout d'une patrouille
                if (TRUE == isset($_REQUEST["id_destination_patrouille_" . $id_patrouillera]) && $_REQUEST["id_destination_patrouille_" . $id_patrouillera]>0 )
                {
                    //on recherche l'id de la destination
                    $requete = "SELECT ID_NOM FROM tab_vaoc_noms_carte WHERE ID_PARTIE=" . $id_partie . " AND S_NOM ='";
                    if (get_magic_quotes_gpc())
                    {
                        // le cas sur Free alors que c'est une faille de securite...
                        //echo "get_magic_quotes_gpc() OFF";
                        $requete.= $_REQUEST["sel_id_destination_patrouille_" . $id_patrouillera] . "'";
                    }
                    else
                    {
                        //echo "get_magic_quotes_gpc() ON";
                        $requete.= mysql_real_escape_string($_REQUEST["sel_id_destination_patrouille_" . $id_patrouillera], $db) . "'";
                    }
                    //echo $requete;
                    //$res_destination = mysql_query($requete, $db);
                    //$row_destination = mysql_fetch_object($res_destination);

                    $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                    $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                    $requete.=AjouterIDOrdre($db, $id_partie) . " , '" . $id_patrouillera . "', NULL, '" . $id_partie . "', '" . $i_tour . "', '" . ORDRE_PATROUILLE . "', '',";
                    $requete.="'" . $_REQUEST['id_distance_patrouille_' . $id_patrouillera] . "', ";
                    $requete.="'" . $_REQUEST["id_direction_patrouille_" . $id_patrouillera] . "', ";
                    //$requete.="'" . $row_destination->ID_NOM . "', ";
                    $requete.=$_REQUEST["id_destination_patrouille_" . $id_patrouillera] . ", ";
                    $requete.="'0', '0'";
                    $requete.=")";
                    //echo $requete;
                    mysql_query($requete, $db);
                }
                //unset($_POST["id_patrouillera"]);
            }

            if (TRUE == isset($id_endommager_pont) && TRUE == is_numeric($id_endommager_pont))
            {
                //destruction du pont le plus proche
                //on recherche le dernier ordre d�j� donn� � ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_endommager_pont, $i_tour);
                //echo $requete;
                mysql_query($requete, $db);

                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . " , '" . $id_endommager_pont . "', NULL, '" . $id_partie . "', '" . $i_tour . "', '" . ORDRE_ENDOMMAGER_PONT . "', '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                if ($id_ordre_precedent >= 0)
                {
                    //il faut indiquer que l'ordre qui vient d'�tre donne est le suivant du precedent
                    //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                    $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_endommager_pont, $i_tour);

                    //ensuite on met � jour l'ordre pr�cedent
                    MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                }
                //unset($_POST["id_endommager_pont"]);
            }

            if (TRUE == isset($id_reparer_pont) && TRUE == is_numeric($id_reparer_pont))
            {
                //destruction du pont le plus proche
                //on recherche le dernier ordre d�j� donn� � ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_reparer_pont, $i_tour);
                //echo $requete;
                mysql_query($requete, $db);

                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . " , '" . $id_reparer_pont . "', NULL, '" . $id_partie . "', '" . $i_tour . "', '" . ORDRE_REPARER_PONT . "', '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                if ($id_ordre_precedent >= 0)
                {
                    //il faut indiquer que l'ordre qui vient d'�tre donne est le suivant du precedent
                    //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                    $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_reparer_pont, $i_tour);

                    //ensuite on met � jour l'ordre pr�cedent
                    MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                }
                //unset($_POST["id_reparer_pont"]);
            }

            if (TRUE == isset($id_construire_pont_unite) && TRUE == is_numeric($id_construire_pont_unite))
            {
                //destruction du pont le plus proche
                //on recherche le dernier ordre deja donne a ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_construire_pont_unite, $i_tour);

                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . " , '" . $id_construire_pont_unite . "', NULL, '" . $id_partie . "', '" . $i_tour . "', '" . ORDRE_CONSTRUIRE_PONT . "', '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                if ($id_ordre_precedent >= 0)
                {
                    //il faut indiquer que l'ordre qui vient d'�tre donne est le suivant du precedent
                    //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                    $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_construire_pont_unite, $i_tour);

                    //ensuite on met a jour l'ordre precedent
                    MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                }
                //unset($_POST["id_construire_pont_unite"]);
            }

            if (TRUE == isset($id_construire_fortification_unite) && TRUE == is_numeric($id_construire_fortification_unite))
            {
                //destruction du pont le plus proche
                //on recherche le dernier ordre deja donne a ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_construire_fortification_unite, $i_tour);

                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . " , '" . $id_construire_fortification_unite . "', NULL, '" . $id_partie . "', '" . $i_tour . "', '" . ORDRE_SE_FORTIFIER . "', '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                if ($id_ordre_precedent >= 0)
                {
                    //il faut indiquer que l'ordre qui vient d'�tre donne est le suivant du precedent
                    //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                    $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_construire_fortification_unite, $i_tour);

                    //ensuite on met a jour l'ordre precedent
                    MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                }
                //unset($_POST["id_construire_fortification_unite"]);
            }

            if (TRUE == isset($id_ravitaillement_direct_unite) && TRUE == is_numeric($id_ravitaillement_direct_unite))
            {
                //destruction du pont le plus proche
                //on recherche le dernier ordre deja donne a ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_ravitaillement_direct_unite, $i_tour);

                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . " , '" . $id_ravitaillement_direct_unite . "', NULL, '" . $id_partie . "', '" . $i_tour . "', '" . ORDRE_RAVITAILLEMENT_DIRECT . "', '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                if ($id_ordre_precedent >= 0)
                {
                    //il faut indiquer que l'ordre qui vient d'etre donne est le suivant du precedent
                    //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                    $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_ravitaillement_direct_unite, $i_tour);

                    //ensuite on met a jour l'ordre precedent
                    MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                }
                //unset($_POST["id_ravitaillement_direct_unite"]);
            }

            if (TRUE == isset($id_arret) && TRUE == is_numeric($id_arret))
            {
                //un ordre de mouvement vient d'etre envoye, il faut mettre a� jour la base de donnees
                //si l'on veut s'arr�ter, ce n'est pas un ordre qui peut arriver en suivant d'un autre, on supprimer donc tous les ordres donn�s � ce tour
                //si un ordre de mouvement a deja� ete donne pour ce tour, il faut le supprimer
                // --> mais, d'une part, c'est casse-pied � g�rer car il faut le g�rer s�parement si un ordre diff�rent est donn� apr�s un ordre arr�t
                // car il faut supprimer l'ordre arr�t dans ce cas
                // de plus, cela peut-�tre surprenant pour un joueur de voir tous ses ordres pr�c�dents supprim�s donc on fait comme pour les autres
                /*
                  $requete="DELETE FROM tab_vaoc_ordre WHERE ID_PION = ".$id_arret." AND ID_PARTIE=".$id_partie." AND I_TOUR=".$i_tour." AND (";
                  $requete.="I_TYPE=".ORDRE_MOUVEMENT;
                  $requete.=" OR I_TYPE=".ORDRE_ENDOMMAGER_PONT;
                  $requete.=" OR I_TYPE=".ORDRE_REPARER_PONT;
                  $requete.=" OR I_TYPE=".ORDRE_CONSTRUIRE_PONT;
                  $requete.=" OR I_TYPE=".ORDRE_ARRET;
                  $requete.=")";

                  //echo $requete;
                  mysql_query($requete,$db);
                 */

                //destruction du pont le plus proche
                //on recherche le dernier ordre deja donne a ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_arret, $i_tour);

                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . " , " . $id_arret . ", " . $id_arret . ", " . $id_partie . ", " . $i_tour . ", " . ORDRE_ARRET . ", '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                if ($id_ordre_precedent >= 0)
                {
                    //il faut indiquer que l'ordre qui vient d'etre donne est le suivant du precedent
                    //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                    $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_arret, $i_tour);

                    //ensuite on met a jour l'ordre precedent
                    MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                }
                //unset($_POST["id_arret"]);
            }

            if (TRUE == isset($id_transfert) && TRUE == is_numeric($id_transfert))
            {
                //transfert d'une unite vers un autre leader
                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PION_CIBLE`, `ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . ", " . $id_transfert . ", " . $id_transfert_qg . ", " . $id_transfert_unite;
                $requete.=", " . $id_partie . ", " . $i_tour . ", " . ORDRE_TRANSFERT . ", '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);
                //unset($_POST["id_transfert"]);
            }

            if (TRUE == isset($id_generer_convoi) && TRUE == is_numeric($id_generer_convoi))
            {
                //cr�ation d'un convoi a partir d'un depot
                //on recherche le dernier ordre deja donne a ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_generer_convoi, $i_tour);
                //echo $requete;
                mysql_query($requete, $db);

                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PION_CIBLE`, `ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . ", " . $id_generer_convoi . ", " . $id_generer_convoi . ", -1";
                $requete.=", " . $id_partie . ", " . $i_tour . ", " . ORDRE_GENERER_CONVOI . ", '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                if ($id_ordre_precedent >= 0)
                {
                    //il faut indiquer que l'ordre qui vient d'etre donne est le suivant du precedent
                    //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                    $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_generer_convoi, $i_tour);

                    //ensuite on met a jour l'ordre precedent
                    MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                }
                //unset($_POST["id_generer_convoi"]);
            }

            if (TRUE == isset($id_ligne_ravitaillement) && TRUE == is_numeric($id_ligne_ravitaillement))
            {
                //creation d'une ligne de ravitaillement a partir d'un depot
                /*
                //on recherche le dernier ordre deja donne a ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_ligne_ravitaillement, $i_tour);
                //echo $requete;
                mysql_query($requete, $db);
                */
                if (TRUE == isset($_REQUEST["id_destination_mouvement_" . $id_ligne_ravitaillement]) && $_REQUEST["id_destination_mouvement_" . $id_ligne_ravitaillement]>0 )
                {
                    //on recherche l'id de la destination
                    $requete = "SELECT ID_NOM FROM tab_vaoc_noms_carte WHERE ID_PARTIE=" . $id_partie . " AND S_NOM ='";
                    if (get_magic_quotes_gpc())
                    {
                        // le cas sur Free alors que c'est une faille de securite...
                        //echo "get_magic_quotes_gpc() OFF";
                        $requete.= $_REQUEST["sel_id_destination_mouvement_" . $id_ligne_ravitaillement] . "'";
                    }
                    else
                    {
                        //echo "get_magic_quotes_gpc() ON";
                        $requete.= mysql_real_escape_string($_REQUEST["sel_id_destination_mouvement_" . $id_ligne_ravitaillement], $db) . "'";
                    }
                    //echo $requete;
                    //$res_destination = mysql_query($requete, $db);
                    //$row_destination = mysql_fetch_object($res_destination);

                    $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PION_CIBLE`, `ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                    $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                    $requete.=AjouterIDOrdre($db, $id_partie) . ", " . $id_ligne_ravitaillement . ", " . $id_ligne_ravitaillement . ", -1";
                    $requete.=", " . $id_partie . ", " . $i_tour . ", " . ORDRE_LIGNE_RAVITAILLEMENT . ", '',";
                    $requete.="'" . $_REQUEST['id_distance_mouvement_' . $id_ligne_ravitaillement] . "', ";
                    $requete.="'" . $_REQUEST["id_direction_mouvement_" . $id_ligne_ravitaillement] . "', ";
                    //$requete.="'" . $row_destination->ID_NOM . "', ";
                    $requete.=$_REQUEST["id_direction_mouvement_" . $id_ligne_ravitaillement] . ", ";
                    $requete.="'0', '0'";
                    $requete.=")";
                    //echo $requete;
                    mysql_query($requete, $db);
                    /*
                    if ($id_ordre_precedent >= 0)
                    {
                        //il faut indiquer que l'ordre qui vient d'�tre donne est le suivant du precedent
                        //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                        $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_ligne_ravitaillement, $i_tour);

                        //ensuite on met a jour l'ordre precedent
                        MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                    }
                     */
                }
                //unset($_POST["id_ligne_ravitaillement"]);
            }

            if (TRUE == isset($id_reduire_depot) && TRUE == is_numeric($id_reduire_depot))
            {
                //réduction d'un depot en creant un convoi, toujours un ordre unique jamais en suivant
                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PION_CIBLE`, `ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . ", " . $id_reduire_depot . ", " . $id_reduire_depot . ", -1";
                $requete.=", " . $id_partie . ", " . $i_tour . ", " . ORDRE_REDUIRE_DEPOT . ", '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                //unset($_POST["id_reduire_depot"]);
            }

            if (TRUE == isset($id_renforcer) && TRUE == is_numeric($id_renforcer))
            {
                //renfort d'une unite par une autre unite ou d'un convoi par un autre convoi
                //on recherche le dernier ordre deja donne a ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_renforcer, $i_tour);
                //echo $requete;
                mysql_query($requete, $db);

                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PION_CIBLE`, `ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                //$requete.=AjouterIDOrdre($db, $id_partie) . ", " . $id_transfert . ", " . $id_transfert_qg . ", " . $id_transfert_unite;
                $requete.=AjouterIDOrdre($db, $id_partie) . ", " . $id_renforcer . ", " . $id_renforcer . ", " . $_REQUEST['id_renforcer_unite_' . $id_renforcer];
                $requete.=", " . $id_partie . ", " . $i_tour . ", " . ORDRE_RENFORCER . ", '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                if ($id_ordre_precedent >= 0)
                {
                    //il faut indiquer que l'ordre qui vient d'�tre donne est le suivant du precedent
                    //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                    $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_renforcer, $i_tour);

                    //ensuite on met � jour l'ordre pr�cedent
                    MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                }
                //unset($_POST["$id_renforcer"]);
            }

            if (TRUE == isset($id_etablir) && TRUE == is_numeric($id_etablir))
            {
                //creation d'un depot a partir d'un convoi
                //on recherche le dernier ordre deja donne a ce tour, hors combat ou retraite, s'il exite, cela devient un ordre_suivant
                $id_ordre_precedent = RechercheDernierOrdreSuivable($db, $id_partie, $id_etablir, $i_tour);
                //echo $requete;
                //echo "id_ordre_precedent=".$id_ordre_precedent;
                mysql_query($requete, $db);

                //insertion du nouvel ordre
                $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PION_CIBLE`, `ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                $requete.=AjouterIDOrdre($db, $id_partie) . ", " . $id_etablir . ", " . $id_etablir . ", -1";
                $requete.=", " . $id_partie . ", " . $i_tour . ", " . ORDRE_ETABLIRDEPOT . ", '',";
                $requete.="'-1', '-1', '-1', '-1', '-1'";
                $requete.=")";
                //echo $requete;
                mysql_query($requete, $db);

                if ($id_ordre_precedent >= 0)
                {
                    //il faut indiquer que l'ordre qui vient d'etre donne est le suivant du precedent
                    //on commence par rechercher l'id_ordre du l'ordre que l'on vient d'inserer
                    $id_nouvel_ordre = RechercheDernierOrdreSuivable($db, $id_partie, $id_etablir, $i_tour);
                    //echo "id_nouvel_ordre=".$id_nouvel_ordre;
                    //echo "id_ordre_precedent=".$id_ordre_precedent;
                    //ensuite on met a jour l'ordre precedent
                    MiseAjourOrdreSuivant($db, $id_partie, $id_ordre_precedent, $id_nouvel_ordre);
                }
                //unset($_POST["$id_etablir"]);
            }

            if (TRUE == isset($id_changementNom) && TRUE == is_numeric($id_changementNom))
            {
                //on fait une mise à jour directe du nom dans la table des pions
                //echo "id_changementNom=".$id_changementNom;
                //echo "nouveau nom=".$_REQUEST['nom_pion' . $id_changementNom];
                //echo "nouveau nom II=".$requete;
                $requete="UPDATE tab_vaoc_pion SET S_NOM='";
                if (get_magic_quotes_gpc())
                {
                    // le cas sur Free alors que c'est une faille de securite...
                    //echo "get_magic_quotes_gpc() OFF";
                    $requete.= $_REQUEST['id_message'];
                }
                else
                {
                    //echo "get_magic_quotes_gpc() ON";
                    $requete.= mysql_real_escape_string($_REQUEST['id_message'], $db);
                }
                $requete.="' WHERE ID_PARTIE=" . $id_partie . " AND ID_PION=".$id_changementNom;
                //echo $requete."<br/>";
                mysql_query($requete, $db);
                //unset($_POST["$id_changementNom"]);
            }

            if (TRUE == isset($id_envoyermessagea) && TRUE == is_numeric($id_envoyermessagea)) //&& FALSE ==isset($_SESSION['postback']))
            {
                //envoie d'un messager vers un autre joueur -> passer par envoyer ordre passer la phase de forum
                //insertion du nouvel ordre
                //echo "envoyer message =".$fl_demmarage;
                //recherche du dernier numero de message pour la partie
                //echo "postback 1=". $_SESSION['postback'];
                $requete="SELECT MAX_ID_MESSAGE FROM tab_vaoc_partie WHERE ID_PARTIE=" . $id_partie;
                //echo $requete."<br/>";
                $res_max_message = mysql_query($requete, $db);
                //echo "num=".mysql_num_rows($res_max_message);
                $row_max_message = mysql_fetch_object($res_max_message);
                //echo "row_max_message->MAX_ID_MESSAGE = ".$row_max_message->MAX_ID_MESSAGE."<br/>";
                $id_message = $row_max_message->MAX_ID_MESSAGE+1;

                // on ajoute +1 pour le prochain message
                $requete="UPDATE tab_vaoc_partie SET MAX_ID_MESSAGE=".$id_message." WHERE ID_PARTIE=" . $id_partie;
                //echo $requete."<br/>";
                mysql_query($requete, $db);

                //recherche de la date courante
                $requete = "SELECT DT_TOUR FROM tab_vaoc_partie WHERE ID_PARTIE=" . $id_partie;
                $res_tour = mysql_query($requete, $db);
                $row_tour = mysql_fetch_object($res_tour);
                $dt_message = $row_tour->DT_TOUR;

                if (0 == $fl_demmarage)
                {
                    //on se trouve en mode "forum", on poste directement les messages dans la table associee
                    $requete = "SELECT tab_vaoc_pion.ID_PION";
                    $requete.=" FROM tab_vaoc_pion, tab_vaoc_role WHERE ";
                    $requete.=" tab_vaoc_pion.ID_PARTIE = tab_vaoc_role.ID_PARTIE";
                    $requete.=" AND tab_vaoc_pion.ID_PION = tab_vaoc_role.ID_PION";
                    $requete.=" AND tab_vaoc_pion.ID_PARTIE = " . $id_partie;
                    $requete.=" AND tab_vaoc_role.ID_NATION = " . $id_nation;
                    //echo $requete;
                    $res_destinataires = mysql_query($requete, $db);
                    $i = 1;
                    while ($row_destinataire = mysql_fetch_object($res_destinataires))
                    {
                        //echo "request_id_message=".$_REQUEST['id_message'];
                        //echo "id_message=".$id_message."<BR/>";
                        $requete = "INSERT INTO tab_vaoc_message(`ID_MESSAGE`,`ID_PARTIE` ,`ID_EMETTEUR` ,`ID_PION_PROPRIETAIRE`  ,`DT_DEPART` ,`DT_ARRIVEE` ,`S_MESSAGE` ) VALUES (";
                        $requete.=$id_message + $i . ", " . $id_partie . ", " . $id_pion_role . ", " . $row_destinataire->ID_PION . ", ";
                        $requete.="DATE_ADD('" . $dt_message . "', INTERVAL " . $id_message . " SECOND), ";
                        $requete.="DATE_ADD('" . $dt_message . "', INTERVAL " . $id_message . " SECOND), '";
                        //$requete.=str_replace("\\","",str_replace( "'" , "''" ,$_REQUEST['id_message']))."'"; // -> ca marche, mais c'est pas la norme
                        //$requete.= addslashes($_REQUEST['id_message']) . "'";
                        //echo "id_message=".$_REQUEST['id_message'];
                        if (get_magic_quotes_gpc())
                        {
                            // le cas sur Free alors que c'est une faille de securite...
                            //echo "get_magic_quotes_gpc() OFF";
                            $requete.= $_REQUEST['id_message'] . "'";
                        }
                        else
                        {
                            //echo "get_magic_quotes_gpc() ON";
                            $requete.= mysql_real_escape_string($_REQUEST['id_message'], $db) . "'";
                        }

                        $requete.=")";
                        //echo $requete."<BR/>";
                        mysql_query($requete, $db);
                        $i++;
                    }
                    // on ajoute le nouveau numero du message max pour le prochain message
                    $requete="UPDATE tab_vaoc_partie SET MAX_ID_MESSAGE=".($id_message + $i)." WHERE ID_PARTIE=" . $id_partie;
                    mysql_query($requete, $db);
                }
                else
                {
                    $requete = "SELECT ID_PION1 ";
                    $requete.=" FROM tab_vaoc_forum";
                    $requete.=" WHERE ";
                    $requete.=" ID_PION1=" . $id_envoyermessagea . " AND ID_PION2=" . $id_destinataire_message;
                    $requete.=" AND ID_PARTIE=" . $id_partie;
                    //echo $requete."<BR/>";
                    $res_forum = mysql_query($requete, $db);
                    //envoi d'un message direct ?
                    if (mysql_num_rows($res_forum) >0)
                    { 
                        //on se trouve en mode "forum", on poste directement le message dans la table associee
                        //un message direct vers le destinataire
                        //echo "request_id_message=".$_REQUEST['id_message'];
                        //echo "id_message=".$id_message."<BR/>";
                            //on recherche l'id de la destination
                        /*
                            $requete = "SELECT ID_NOM FROM tab_vaoc_noms_carte WHERE ID_PARTIE=" . $id_partie . " AND S_NOM ='";
                            if (get_magic_quotes_gpc())
                            {
                                // le cas sur Free alors que c'est une faille de securite...
                                //echo "get_magic_quotes_gpc() OFF";
                                $requete.= $_REQUEST["sel_id_destination_messager_" . $id_pion_role] . "'";
                            }
                            else
                            {
                                //echo "get_magic_quotes_gpc() ON";
                                $requete.= mysql_real_escape_string($_REQUEST["sel_id_destination_messager_" . $id_pion_role], $db) . "'";
                            }
                            echo $requete;
                            $res_destination = mysql_query($requete, $db);
                            $row_destination = mysql_fetch_object($res_destination);
                         */

                            $requete = "INSERT INTO tab_vaoc_message(`ID_MESSAGE`,`ID_PARTIE` ,`ID_EMETTEUR` ,`ID_PION_PROPRIETAIRE`  ,`DT_DEPART` ,`DT_ARRIVEE` ,`S_MESSAGE` ) VALUES (";
                            $requete.=$id_message . ", " . $id_partie . ", " . $id_envoyermessagea . ", " . $id_destinataire_message . ", ";
                            $requete.="DATE_ADD('" .$dt_message . "', INTERVAL 1 SECOND), ";
                            $requete.="DATE_ADD('" . $dt_message . "', INTERVAL 1 SECOND), '";
                            if (get_magic_quotes_gpc())
                            {
                                // le cas sur Free alors que c'est une faille de securite...
                                //echo "get_magic_quotes_gpc() OFF";
                                $requete.= $_REQUEST['id_message'] . "'";
                            }
                            else
                            {
                                //echo "get_magic_quotes_gpc() ON";
                                $requete.= mysql_real_escape_string($_REQUEST['id_message'], $db) . "'";
                            }

                            $requete.=")";
                            //echo $requete."<BR/>";
                            mysql_query($requete, $db);

                            //Envoi d'un message via un messager a cheval -> mais il faut que ce soit direct d'où l'ordre spécifique
                            $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                            $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                            $requete.=AjouterIDOrdre($db, $id_partie) . " , " . $id_envoyermessagea . ", " . $id_destinataire_message . ", " . $id_partie . ", " . $i_tour . ", '" . ORDRE_MESSAGE_FORUM . "', '";
                            //$requete.=str_replace( "'" , "''" ,$_REQUEST['id_message'])."', "; -> inutile une fois mis sur le web, hum, hum...
                            //$requete.=addslashes($_REQUEST['id_message']) . "', ";
                            if (get_magic_quotes_gpc())
                            {
                                // le cas sur Free alors que c'est une faille de securite...
                                //echo "get_magic_quotes_gpc() OFF";
                                $requete.= $_REQUEST['id_message'] . "', ";
                            }
                            else
                            {
                                //echo "get_magic_quotes_gpc() ON";
                                $requete.=mysql_real_escape_string($_REQUEST['id_message'], $db) . "', ";
                            }

                            $requete.=$_REQUEST["id_distance_messager_" . $id_pion_role] . ", ";
                            $requete.=$_REQUEST["id_direction_messager_" . $id_pion_role] . ", ";
                            $requete.="-1 , ";
                            $requete.="'0', '0'";
                            $requete.=")";
                            //echo $requete;
                            mysql_query($requete, $db);

                            //envoi d'un courriel en direct pour prévenir le joueur
                            ini_set('sendmail_from','vaoc@free.fr');  // the INI lines are to force the From Address to be used !
                            if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
                                $eol="\r\n";
                            } elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
                                $eol="\r";
                            } else {
                                $eol="\n";
                            }  

                            $requete = "SELECT S_COURRIEL, tab_vaoc_pion.S_NOM ";
                            $requete.=" FROM tab_utilisateurs, tab_vaoc_role, tab_vaoc_pion";
                            $requete.=" WHERE tab_utilisateurs.ID_UTILISATEUR=tab_vaoc_role.ID_UTILISATEUR";
                            $requete.=" AND tab_vaoc_pion.ID_PION=tab_vaoc_role.ID_PION";
                            $requete.=" AND tab_vaoc_role.ID_PION=" . $id_envoyermessagea;
                            $requete.=" AND tab_vaoc_role.ID_PARTIE=" . $id_partie;
                            $requete.=" AND tab_vaoc_pion.ID_PARTIE=tab_vaoc_role.ID_PARTIE";
                            $res_emetteur = mysql_query($requete, $db);
                            $row_emetteur = mysql_fetch_object($res_emetteur);
                            //echo "<p>".$requete."</p>";

                            $requete = "SELECT S_COURRIEL, tab_vaoc_pion.S_NOM ";
                            $requete.=" FROM tab_utilisateurs, tab_vaoc_role, tab_vaoc_pion";
                            $requete.=" WHERE tab_utilisateurs.ID_UTILISATEUR=tab_vaoc_role.ID_UTILISATEUR";
                            $requete.=" AND tab_vaoc_pion.ID_PION=tab_vaoc_role.ID_PION";
                            $requete.=" AND tab_vaoc_pion.ID_PARTIE=tab_vaoc_role.ID_PARTIE"; 
                            $requete.=" AND tab_utilisateurs.ID_UTILISATEUR=tab_vaoc_role.ID_UTILISATEUR ";                    
                            $requete.=" AND tab_vaoc_role.ID_PION=" . $id_destinataire_message;
                            $requete.=" AND tab_vaoc_role.ID_PARTIE=" . $id_partie;
                            $res_destinataire = mysql_query($requete, $db);
                            $row_destinataire = mysql_fetch_object($res_destinataire);

                            $headers  = 'MIME-Version: 1.0' .$eol;
                            //$headers .= 'Content-type: text/html; charset=iso-8859-1'.$eol; envoie visiblement de mauvais caractères
                            $headers .= 'Content-type: text/html; charset=UTF-8'.$eol;                    
                            $headers .= 'From: VAOC <vaoc@free.fr>'.$eol;
                            $headers .= 'Reply-To: VAOC <vaoc@free.fr>'.$eol;                    
                            //$to = "hoel@free.fr";
                            $to = $row_destinataire->S_COURRIEL;
                            $subject = "VAOC - ". $row_destinataire->S_NOM." : Message de ". $row_emetteur->S_NOM;
                            $msg = "<html><body>".$_REQUEST['id_message']."<br>Note : Ce courriel est envoyé par un joueur en mode direct. Vous pouvez y répondre par le site web et non en répondant à ce courriel.</body></html>";
                            //echo $subject;
                            if (false == mail($to, $subject, $msg, $headers)) 
                            {
                              echo("<p>Erreur sur l'envoi du courriel…</p>");
                            }
                    }
                    else
                    {
                        //Envoi d'un message via un messager a cheval
                        if (TRUE == isset($_REQUEST["id_destination_messager_" . $id_pion_role]) && $_REQUEST["id_destination_messager_" . $id_pion_role]>0 )
                        {
                            //on recherche l'id de la destination
                            /* $requete = "SELECT ID_NOM FROM tab_vaoc_noms_carte WHERE ID_PARTIE=" . $id_partie . " AND S_NOM ='";
                            if (get_magic_quotes_gpc())
                            {
                                // le cas sur Free alors que c'est une faille de securite...
                                //echo "get_magic_quotes_gpc() OFF";
                                $requete.= $_REQUEST["id_destination_messager_" . $id_pion_role] . "'";
                            }
                            else
                            {
                                //echo "get_magic_quotes_gpc() ON";
                                $requete.= mysql_real_escape_string($_REQUEST["id_destination_messager_" . $id_pion_role], $db) . "'";
                            }
                            echo $requete;
                            $res_destination = mysql_query($requete, $db);
                            $row_destination = mysql_fetch_object($res_destination);
                               */
                            $requete = "INSERT INTO tab_vaoc_ordre(`ID_ORDRE` ,`ID_PION` ,`ID_PION_DESTINATION` ,`ID_PARTIE` ,`I_TOUR` ,`I_TYPE` ,`S_MESSAGE` ,";
                            $requete.="`I_DISTANCE` ,`I_DIRECTION` ,`ID_NOM_LIEU` ,`I_HEURE` ,`I_DUREE`) VALUES (";
                            $requete.=AjouterIDOrdre($db, $id_partie) . " , " . $id_envoyermessagea . ", " . $id_destinataire_message . ", " . $id_partie . ", " . $i_tour . ", '" . ORDRE_MESSAGER . "', '";
                            //$requete.=str_replace( "'" , "''" ,$_REQUEST['id_message'])."', "; -> inutile une fois mis sur le web, hum, hum...
                            //$requete.=addslashes($_REQUEST['id_message']) . "', ";
                            if (get_magic_quotes_gpc())
                            {
                                // le cas sur Free alors que c'est une faille de securite...
                                //echo "get_magic_quotes_gpc() OFF";
                                $requete.= $_REQUEST['id_message'] . "', ";
                            }
                            else
                            {
                                //echo "get_magic_quotes_gpc() ON";
                                $requete.=mysql_real_escape_string($_REQUEST['id_message'], $db) . "', ";
                            }

                            $requete.=$_REQUEST["id_distance_messager_" . $id_pion_role] . ", ";
                            $requete.=$_REQUEST["id_direction_messager_" . $id_pion_role] . ", ";
                            if (get_magic_quotes_gpc())
                            {
                                // le cas sur Free alors que c'est une faille de securite...
                                //echo "get_magic_quotes_gpc() OFF";
                                $requete.= $_REQUEST["id_destination_messager_" . $id_pion_role];
                            }
                            else
                            {
                                //echo "get_magic_quotes_gpc() ON";
                                $requete.= mysql_real_escape_string($_REQUEST["id_destination_messager_" . $id_pion_role], $db);
                            }
                            //$requete.=$row_destination->ID_NOM . ", ";
                            $requete.=",'0', '0'";
                            $requete.=")";
                            //echo $requete;
                        }
                        mysql_query($requete, $db);
                    }
                    //$_POST["id_envoyermessagea"]=0; //-> marche pas !
                    //unset($_REQUEST["id_envoyermessagea"]);
                    //unset($_POST["id_envoyermessagea"]);
                    //$_SESSION['postback']="NON";
                    //echo "postback 2=". $_SESSION['postback'];
                    //echo "<script type=\"text/javascript\">alert('session set');</script>";
                    //http_post_data("vaocqg.php");
                    //echo "callMoi";
                    //echo "<script type=\"text/javascript\">callMoi();</script>";
                    //echo "_REQUEST[id_envoyermessagea]=".$_REQUEST["id_envoyermessagea"]."<br/>";
                }
            }
            else
            {
                //die('postback'.$_SESSION['postback']);
                /*
                echo "<script type=\"text/javascript\">alert('session other');</script>";
                if(isset($_SESSION['postback']))
                {
                    echo "<script type=\"text/javascript\">alert('session set');</script>";
                    unset($_SESSION['postback']);
                }
                 * */
            }

            //echo "<script type=\"text/javascript\">alert('session set');</script>";
            //unset($action);
            //echo "<script type=\"text/javascript\">document.getElementById(\"action\").value = \"\";</script>";
        }//raffraichi
        
        if (TRUE == isset($nombre_messages_pages) && TRUE == is_numeric($nombre_messages_pages))
        {
            //la valeur du nombre de messages � afficher par defaut vient de changer, on met a jour la base s'il s'agit d'un utilisateur identifie
            if (FALSE == empty($id_login))
            {
                $requete = "UPDATE tab_utilisateurs SET I_NB_MESSAGES=" . $nombre_messages_pages . " WHERE S_LOGIN='" . trim($id_login) . "'";
                //echo $requete;
                $res_nb_messages_update = mysql_query($requete, $db);
            }
        }
        else
        {
            //sinon on recherche la valeur en base, pour un joueur authentifie
            $nombre_messages_pages = 0;
            if (FALSE == empty($id_login))
            {
                $res_nombre_messages_pages = mysql_query("SELECT I_NB_MESSAGES FROM tab_utilisateurs WHERE S_LOGIN='" . trim($id_login) . "'", $db);
                $row_nombre_messages_pages = mysql_fetch_object($res_nombre_messages_pages);
                $nombre_messages_pages = $row_nombre_messages_pages->I_NB_MESSAGES;
            }

            //s'il n'y en a pas, on prend une valeur moyenne
            if ($nombre_messages_pages <= 0)
            {
                $nombre_messages_pages = $listeNombreMessages[count($listeNombreMessages) / 2];
            }
        }        //A voir plutot avec un style complet
        //echo "<body style='background-color:".$row_role->S_COULEUR_FOND."; color:".$row_role->S_COULEUR_TEXTE.";' >";
        //echo "<form method=\"post\" name=\"principal\" target=\"_self\" action=\"{$_SERVER['PHP_SELF']}\">\n";
        ?>
        <div class="container">
        <form method="post" id="principal" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="form-inline" role="form">
            <?php
            if (empty($tri_liste))
            {
                //lorsque l'on arrive sur la page, on est forcement sur la premiere page des messages
                $tri_liste = "DT_ARRIVEE";
                $ordre_tri_liste = "DESC";
            }

            //champs caches
            echo "<input id='action' name='action' type='hidden' />";
            echo "<input id='id_changementNom' name='id_changementNom' type='hidden' />";
            echo "<input id='id_ravitaillement_direct_unite' name='id_ravitaillement_direct_unite' type='hidden' />";
            echo "<input id='id_reduire_depot' name='id_reduire_depot' type='hidden' />";
            echo "<input id='id_ligne_ravitaillement' name='id_ligne_ravitaillement' type='hidden' />";
            echo "<input id='id_message' name='id_message' type='hidden' />";
            echo "<input id='id_etablir' name='id_etablir' type='hidden' />";
            echo "<input id='id_renforcer' name='id_renforcer' type='hidden' />";
            echo "<input id='id_generer_convoi' name='id_generer_convoi' type='hidden' />";
            echo "<input id='id_transfert' name='id_transfert' type='hidden' />";
            echo "<input id='id_construire_pont_unite' name='id_construire_pont_unite' type='hidden' />";
            echo "<input id='id_construire_fortification_unite' name='id_construire_fortification_unite' type='hidden' />";
            echo "<input id='id_arret' name='id_arret' type='hidden' />";
            echo "<input id='id_endommager_pont' name='id_endommager_pont' type='hidden' />";
            echo "<input id='id_reparer_pont' name='id_reparer_pont' type='hidden' />";
            echo "<input id='id_allera' name='id_allera' type='hidden' />";
            echo "<input id='id_supprimerallera' name='id_supprimerallera' type='hidden' />";
            echo "<input id='id_patrouillera' name='id_patrouillera' type='hidden' />";
            echo "<input id='id_envoyermessagea' name='id_envoyermessagea' type='hidden' />";
            echo "<input id='id_login' name='id_login' type='hidden' value='" . $id_login . "' />";
            echo "<input id='id_partie' name='id_partie' type='hidden' value='" . $id_partie . "' />";
            echo "<input id='id_x' name='id_x' type='hidden' value='0' />";
            echo "<input id='id_y' name='id_y' type='hidden' value='0' />";
            echo "<input id='id_nation' name='id_nation' type='hidden' value=\"" . $id_nation . "\" />";
            echo "<input id='id_role' name='id_role' type='hidden' value=\"" . $id_role . "\" />";
            echo "<input id='id_ordres_termines' name='id_ordres_termines' type='hidden' />";
            echo "<input id='tri_liste' name='tri_liste' type='hidden' value='" . $tri_liste . "' />";
            echo "<input id='ordre_tri_liste' name='ordre_tri_liste' type='hidden' value='" . $ordre_tri_liste . "' />";
            //echo "<input id=\"id_ordre_tri\" name=\"id_ordre_tri\" type=\"hidden\" value=\"\"/>";
            echo "<input id=\"id_ordre_tri\" name=\"id_ordre_tri\" type=\"hidden\" />";
        
            //affichage du pion du joueur et de ses caracteristiques
            $requete = "SELECT ID_PION, ID_MODELE_PION, S_NOM, C_NIVEAU_HIERARCHIQUE, I_TACTIQUE, I_STRATEGIQUE, ID_BATAILLE, S_POSITION, S_ORDRE_COURANT ";
            $requete.="FROM tab_vaoc_pion ";
            $requete.="WHERE tab_vaoc_pion.ID_PION=" . $id_pion_role . " AND tab_vaoc_pion.ID_PARTIE=" . $id_partie;
            //echo $requete;
            $res_chef = mysql_query($requete, $db);
            $row_pion_chef = mysql_fetch_object($res_chef);
            ?>

            <!-- informations generales sur le jeu/la partie, bref, la campagne ! -->
            <div class="row row-centered hidden-md hidden-lg">
                <div class="col-sm-6 col-centered">
                    <h1><?php echo $nom_jeu ?></h1>
                </div>
                <div class="col-sm-6 col-centered">
                    <h2><?php echo $row_role->NOM_PARTIE ?></h2>
                </div>
            </div>
            <div class="row row-centered  hidden-xs hidden-sm">
                <div class="col-xs-12 col-centered">
                    <a href="nojs.htm" onclick="javascript:callPage('vaoccampagne.php');return false;">
                        <img alt="campagne" id="bandeau" 
                            <?php 
                                if ($idVictoire < 0) 
                                {
                                    echo "src=\"images/$row_role->S_IMAGE\"";
                                }
                                else
                                {
                                    if ($idVictoire == $id_nation)
                                    {
                                        echo "src=\"images/victoire.png\"";
                                    }
                                    else
                                    {
                                        if ($idVictoire == 2)
                                        {
                                            echo "src=\"images/egalite.png\"";
                                        }
                                        else
                                        {
                                            echo "src=\"images/defaite.png\"";
                                        }
                                    }
                                }                                
                            ?> />
                    </a>
                </div>

                <div class="col-xs-12 col-centered" style='position:relative; top:-60px; height: 0'>
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
                        //s'il y a une bataille en cours, on met un gros bouton d'accès rapide
                        if ($row_pion_chef->ID_BATAILLE>=0)
                        {
                            echo "<input alt=\"bataille\" id='id_afficher_bataille' name='id_afficher_bataille' class=\"btn btn-danger\" type='image' value='submit' src=\"images/btnBataille2.png\" onclick=\"javascript:callBataille();\" />";
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="row row-centered">
                <div class="col-xs-12 col-centered">
                    <div class="input-group">
                        <h3>
                            <?php
                            if ($idVictoire <0)
                            {
                                //echo "fl_demmarage=".$fl_demmarage;
                                if (0 == $fl_demmarage)
                                {
                                    echo "<div>Vous êtes actuellement dans la réunion préparatoire du haut commandement.<br/>Vos positions sont provisoires et "
                                    . "vous ne pouvez pas envoyer d'ordres mis à part des messages à tous les autres officiers.</div>";
                                }
                                else 
                                {
                                    echo "<input type=\"checkbox\" id=\"ordres_termines\" name=\"ordres_termines\" onclick=\"javascript:callOrdresTermines(". $b_ordres_termines .");\"";
                                    if ($b_ordres_termines == 1)
                                    {
                                        echo " checked ";
                                    }
                                    echo "> J'ai termin&eacute; de donner mes ordres&nbsp;";
                                }
                            }
                            ?>
                        </h3>
                    </div>
                    <?php
                    if ($idVictoire >= 0) //la partie est terminee
                    {
                        echo "<h4>Nous sommes le " . $row_role->DATE_PARTIE . " et cette campagne est termin&eacute;e.</h4>";
                    }
                    else
                    {
                        //affichage des heures et coucher du soleil
                        echo "<h4>Nous sommes le " . $row_role->DATE_PARTIE . " et cette campagne est pr&eacute;vue jusqu'au " . $row_role->DATE_FIN . "</h4>";
                        echo "<h4>Actuellement, le temps est " . $row_role->S_METEO . ". Il fait jour de " . $row_role->H_JOUR . "h00 &agrave; " . $row_role->H_NUIT . "h00.</h4>";
                    }
                    ?>
                </div>
            </div>

            <?php
            if (empty($_REQUEST['pageNum_' . $row_pion_chef->ID_PION]))
            {
                $numPage = 0;
            }
            else
            {
                $numPage = $_REQUEST['pageNum_' . $row_pion_chef->ID_PION];
            }
            $id_bataille = $row_pion_chef->ID_BATAILLE; //pour transmettre en cas d'appel a une carte de bataille
            echo "<input id='id_bataille' name='id_bataille' type='hidden' value=\"" . $id_bataille . "\" />";

            if (FALSE == isset($liste_recepteur))
            {
                $liste_recepteur=-1;
            }
            AfficherQG($db, $fl_demmarage, $login->ID_UTILISATEUR, $id_partie, $id_role, -1, 
                    $row_pion_chef->ID_PION, $row_pion_chef->S_NOM, $row_pion_chef->S_ORDRE_COURANT, 
                    $row_pion_chef->C_NIVEAU_HIERARCHIQUE, $row_pion_chef->ID_MODELE_PION, 
                    $row_pion_chef->I_TACTIQUE, $row_pion_chef->I_STRATEGIQUE, $row_pion_chef->S_POSITION, 
                    $row_pion_chef->ID_BATAILLE, $i_tour, $liste_recepteur, $numPage);
            ?>
            <div class="row">
                <div class="col-xs-12">
                    <h3>Messagers</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">			
                    <?php
                    //liste de tous les autres leaders de son camp joues par d'autres joueurs
                    $requete = "SELECT tab_vaoc_role.ID_PION, tab_vaoc_role.S_NOM, tab_vaoc_pion.S_NOM as S_NOMPION ";
                    $requete.=" FROM tab_vaoc_role, tab_vaoc_pion ";
                    $requete.=" WHERE ID_ROLE <>" . $id_role . " AND tab_vaoc_role.ID_NATION =" . $id_nation . " AND tab_vaoc_role.ID_PARTIE=" . $id_partie;
                    $requete.=" AND tab_vaoc_role.ID_PION=tab_vaoc_pion.ID_PION"; //pour ne pas pouvoir ecrire qu'aux roles ayant des pions en jeu
                    $requete.=" AND tab_vaoc_role.ID_PARTIE=tab_vaoc_pion.ID_PARTIE"; //pour ne pas pouvoir écrire qu'aux rôles ayant des pions en jeu
                    $requete.=" ORDER BY S_NOMPION";
                    //echo $requete;
                    $res_allies = mysql_query($requete, $db);
                    if (0 == mysql_num_rows($res_allies))
                    {
                            echo "<b>Vous n'avez aucun alli&eacute; &agrave; qui envoyer un message.</b>";
                    }
                    else
                    if ($idVictoire >= 0)
                    {
                            echo "<h3>Campagne terminée.</h3>";
                    }
                    else
                    {
                    ?>
                    <div class="btn-toolbar" data-role="editor-toolbar" data-target="#editor" style="background-color:white;">
                    <div class="btn-group">
                        <a class="btn dropdown-toggle" data-toggle="dropdown" title="Font Size">
                            <img alt='taille' src='images/text-26.png' />
                            &nbsp;<b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a data-edit="fontSize 5"><font size="5">Grand</font></a></li>
                            <li><a data-edit="fontSize 3"><font size="3">Normal</font></a></li>
                            <li><a data-edit="fontSize 1"><font size="1">Petit</font></a></li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <a class="btn" data-edit="bold" title="Gras (Ctrl/Cmd+B)">
                            <img alt='gras' src='images/bold-26.png' />
                        </a>
                        <a class="btn" data-edit="italic" title="Italique (Ctrl/Cmd+I)">
                            <img alt='italic' src='images/italic-26.png' />
                        </a>
                        <a class="btn" data-edit="strikethrough" title="Barr&eacute;">
                            <img alt='barre' src='images/textbarre-26.png' />
                        </a>
                        <a class="btn" data-edit="underline" title="soulign&eacute; (Ctrl/Cmd+U)">
                            <img alt='souligne' src='images/textsouligne-26.png' />
                        </a>
                    </div>
                    <div class="btn-group">
                        <a class="btn" data-edit="insertunorderedlist" title="liste">
                            <img alt='liste' src='images/list-26.png' />
                        </a>
                        <a class="btn" data-edit="insertorderedlist" title="Liste Numérique">
                            <img alt='listenumerique' src='images/listnum-26.png' />
                        </a>
                        <a class="btn" data-edit="outdent" title="Reduire tabulation (Shift+Tab)">
                            <img alt='tabulationmoins' src='images/tabmoins-26.png' />
                        </a>
                        <a class="btn" data-edit="indent" title="Tabulation">
                            <img alt='tabulation' src='images/tabplus-26.png' />
                        </a>
                    </div>
                    <div class="btn-group">
                        <a class="btn" data-edit="justifyleft" title="Aligner à Gauche (Ctrl/Cmd+L)">
                            <img alt='gauche' src='images/left-26.png' />
                        </a>
                        <a class="btn" data-edit="justifycenter" title="Centrer (Ctrl/Cmd+E)">
                            <img alt='centrer' src='images/center-26.png' />
                        </a>
                        <a class="btn" data-edit="justifyright" title="Aligner à Droite (Ctrl/Cmd+R)">
                            <img alt='droite' src='images/right-26.png' />
                        </a>
                        <a class="btn" data-edit="justifyfull" title="Justifer (Ctrl/Cmd+J)">
                            <img alt='justify' src='images/justify-26.png' />
                        </a>
                    </div>
                    <div class="btn-group">
                        <a class="btn" data-edit="undo" title="Annuler (Ctrl/Cmd+Z)">
                            <img alt='annuler' src='images/undo-26.png' />
                        </a>
                        <a class="btn" data-edit="redo" title="Refaire (Ctrl/Cmd+Y)">
                            <img alt='refaire' src='images/redo-26.png' />
                        </a>
                    </div>
                        <!--
                    <img alt='carteHistorique' src='images/delete-26.png' onclick="javascript:alert(document.getElementById('editor').innerHTML);" />
                        -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">			
                    <!-- Je ne comprends pas pourquoi il faut mettre -5px, mais sinon, cela n est pas aligne... -->
                    <div id="editor" style="margin-left:-5px;"></div>
                </div>
                <div class="col-xs-12">
                    <b><span id="cptMessage">0</span></b> caract&egrave;res (Maximum : 5000 Caract&egrave;res)
                </div>
                <div class="col-xs-12">
                    <?php
                    $id_chaine = "id_destinataire_message";
                    echo "<input alt=\"envoyer le message\" id=\"id_envoie_message\" name=\"id_envoie_message\" "
                    . "type= \"image\" src=\"images/btnEnvoyerLeMessage_on2.png\" value=\"submit\" class=\"btn btn-default\" "
                            . "onclick=\"javascript:callDonnerOrdreUnite(" . $row_pion_chef->ID_PION . ",'id_envoyermessagea');\">";
                    echo "<div class=\"input-group\">";
                            echo "<div class=\"input-group-addon\">&nbsp;&agrave;&nbsp;&nbsp;</div>";
                            echo "<select class=\"selectpicker\" id=\"id_destinataire_message\" name=\"id_destinataire_message\" size=1>";
                            if (0 == $fl_demmarage)
                            {
                                    echo "<option selected=\"selected\" value=\"tous\">tous</option>";
                            }
                            else
                            {
                                while ($row_allies = mysql_fetch_object($res_allies))
                                {
                                    echo "<option";
                                    if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine] == $row_allies->ID_PION)
                                    {
                                        echo " selected=\"selected\"";
                                    }

                                    $requete = "SELECT ID_PION1 ";
                                    $requete.=" FROM tab_vaoc_forum";
                                    $requete.=" WHERE ";
                                    $requete.=" ID_PION1=" . $id_pion_role . " AND ID_PION2=" . $row_allies->ID_PION;
                                    $requete.=" AND ID_PARTIE=" . $id_partie;
                                    $res_forum = mysql_query($requete, $db);
                                    if (0 == mysql_num_rows($res_forum))
                                    {
                                        printf(" value=\"%u\">%s</option>", $row_allies->ID_PION, $row_allies->S_NOMPION);
                                    }
                                    else
                                    {
                                        printf(" value=\"%u\">%s(direct)</option>", $row_allies->ID_PION, $row_allies->S_NOMPION);
                                    }
                                    //echo $requete;

                                    //printf(" value=\"%u\">%s</option>", $row_allies->ID_PION, $row_allies->S_NOMPION);
                                }
                            }
                            echo "</select>";
                    echo "</div>";
                    echo "<div class=\"input-group\">";
                        echo "<div class=\"input-group-addon\">&nbsp;situ&eacute; &agrave;&nbsp;</div>";
                        $id_chaine = "id_distance_messager_" . $id_pion_role;
                        AfficherDistance($db, $fl_demmarage, $id_chaine);
                        //choix d'une direction
                        $id_chaine = "id_direction_messager_" . $id_pion_role;
                        AfficherDirection($db, $fl_demmarage, $id_chaine);
                    echo "</div>";
                    echo "<div class=\"input-group\">";
                            echo "<div class=\"input-group-addon\">&nbsp;de&nbsp;</div>";
                            //choix d'une destination
                            $id_chaine = "id_destination_messager_" . $id_pion_role;
                            AfficherDestination($db, $fl_demmarage, $id_chaine, $id_partie);
                    echo "</div>";
                }
                ?>	
                </div>
            </div>
                    
            <div class="row">
                <div class="col-md-12 hidden-xs hidden-sm" >
                    <h3 id="troupes">Troupes
                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#id_fenetre_reorganise">
                            <img src="images/btnTrier2.png" />
                        </button>
                    </h3>
                </div>
            </div>
            <?php
            $i_tri=0;
            $ligne_tri=ligneDrop($i_tri++);
            $requete = "SELECT ID_PION, ID_MODELE_PION, S_NOM, B_DETRUIT, C_NIVEAU_HIERARCHIQUE, I_STRATEGIQUE, I_TACTIQUE, ";
            $requete.=" I_INFANTERIE, I_INFANTERIE_INITIALE, I_CAVALERIE, I_CAVALERIE_INITIALE ,I_ARTILLERIE, I_ARTILLERIE_INITIALE ,I_FATIGUE, I_MORAL, I_MORAL_MAX, ";
            $requete.=" I_MATERIEL, I_RAVITAILLEMENT, ";
            $requete.=" I_EXPERIENCE, B_DEPOT, B_PONTONNIER, ID_BATAILLE, I_RETRAITE, S_POSITION, I_PATROUILLES_DISPONIBLES, ";
            $requete.=" I_PATROUILLES_MAX, C_NIVEAU_DEPOT, I_SOLDATS_RAVITAILLES, ID_DEPOT_SOURCE, B_CONVOI, B_RENFORT, B_BLESSES, B_PRISONNIERS,";
            $requete.=" B_CAVALERIE_DE_LIGNE, B_CAVALERIE_LOURDE, B_GARDE, B_VIEILLE_GARDE, B_QG, I_NIVEAU_FORTIFICATION, S_ORDRE_COURANT ";
            $requete.=" FROM tab_vaoc_pion";
            $requete.=" WHERE "; //C_NIVEAU_HIERARCHIQUE='Z' "; // indique un leader sinon ->un leader peut �tre un pion d�di�, il faut juste que ce ne soit pas un joueur
            $requete.=" tab_vaoc_pion.ID_PION_PROPRIETAIRE=" . $id_pion_role . " AND tab_vaoc_pion.ID_PION<>" . $id_pion_role;
            $requete.=" AND tab_vaoc_pion.ID_PARTIE=" . $id_partie;
            $requete.=" AND tab_vaoc_pion.ID_PION not in (select tab_vaoc_role.ID_PION FROM tab_vaoc_role where tab_vaoc_role.ID_PARTIE=" . $id_partie . ")";
            $requete.=" ORDER BY I_TRI ASC";
            //$requete.=" ORDER BY b_QG DESC, I_INFANTERIE_INITIALE DESC, I_CAVALERIE_INITIALE DESC,I_ARTILLERIE_INITIALE DESC, B_DEPOT, S_NOM ASC";
            //echo $requete;
            $res_pions = mysql_query($requete, $db);
            if (0 == mysql_num_rows($res_pions))
            {
                echo "<div class=\"row\">";
                echo "<div class=\"col-md-12 hidden-xs hidden-sm\" >";
                echo "<h3>Vous ne commandez actuellement aucune unit&eacute;</h3>";
                echo "</div>";
                echo "</div>";
                echo "<div style='text-align:center;'></div>";
            }
            else
            {
                while ($row_pion = mysql_fetch_object($res_pions))
                {
                    if (empty($_REQUEST['pageNum_' . $row_pion->ID_PION]))
                    {
                        //lorsque l'on arrive sur la page, on est forcement sur la premiere page des messages
                        $numPage = 0;
                    }
                    else
                    {
                        $numPage = $_REQUEST['pageNum_' . $row_pion->ID_PION];
                    }
                    if ($row_pion->B_QG > 0)
                    {
                        AfficherQG($db, $fl_demmarage, $login->ID_UTILISATEUR, $id_partie, -1, 
                                $id_pion_role, $row_pion->ID_PION, $row_pion->S_NOM, $row_pion->S_ORDRE_COURANT,
                                $row_pion->C_NIVEAU_HIERARCHIQUE, $row_pion->ID_MODELE_PION, 
                                $row_pion->I_TACTIQUE, $row_pion->I_STRATEGIQUE, $row_pion->S_POSITION, 
                                $row_pion->ID_BATAILLE, $i_tour, 
                                -1, $numPage);
                        echo "<hr style=\"border-top: 5px solid white;\" />";
                        $ligne_tri .=ligneDrag($i_tri,$row_pion->ID_PION, $row_pion->S_NOM, "rang-26.png");
                        $ligne_tri .=ligneDrop($i_tri++);
                    }
                    else
                    {
                        if (1 == $row_pion->B_DEPOT)
                        {
                            AfficherDepot($db, $fl_demmarage, $id_partie, $id_pion_role, $row_pion->ID_PION, 
                                            $row_pion->S_NOM, $row_pion->S_ORDRE_COURANT, $row_pion->C_NIVEAU_DEPOT, 
                                            $row_pion->I_SOLDATS_RAVITAILLES, $row_pion->ID_MODELE_PION, $row_pion->B_DETRUIT, 
                                            $row_pion->S_POSITION, $i_tour, $numPage);
                            echo "<hr style=\"border-top: 5px solid white;\" />";
                            $ligne_tri .=ligneDrag($i_tri,$row_pion->ID_PION, $row_pion->S_NOM, "ravitaillement-26.png");
                            $ligne_tri .=ligneDrop($i_tri++);
                        }
                        else
                        {
                            if (1 == $row_pion->B_PONTONNIER)
                            {
                                AfficherPontonnier($db, $fl_demmarage, $id_partie, $id_pion_role, $row_pion->ID_PION, 
                                                    $row_pion->S_NOM, $row_pion->S_ORDRE_COURANT, $row_pion->ID_MODELE_PION, 
                                                    $row_pion->B_DETRUIT, $row_pion->S_POSITION, $i_tour, $numPage);
                                echo "<hr style=\"border-top: 5px solid white;\" />";
                                $ligne_tri .=ligneDrag($i_tri,$row_pion->ID_PION, $row_pion->S_NOM, "materiel-26.png");
                                $ligne_tri .=ligneDrop($i_tri++);
                            }
                            else
                            {
                                if (1 == $row_pion->B_CONVOI) // && $row_pion->C_NIVEAU_DEPOT == 'D')
                                {
                                    if (0 == $row_pion->B_DETRUIT)
                                    {
                                        AfficherConvoiDepot($db, $fl_demmarage, $id_partie, $id_pion_role, $row_pion->ID_PION, 
                                                            $row_pion->S_NOM, $row_pion->S_ORDRE_COURANT, $row_pion->ID_MODELE_PION, 
                                                            $row_pion->B_RENFORT, $row_pion->C_NIVEAU_DEPOT, $row_pion->ID_DEPOT_SOURCE, 
                                                            $row_pion->B_DETRUIT, $row_pion->S_POSITION, $i_tour, $numPage);
                                        echo "<hr style=\"border-top: 5px solid white;\" />";
                                        $ligne_tri .=ligneDrag($i_tri,$row_pion->ID_PION, $row_pion->S_NOM, "ravitaillement-26.png");
                                        $ligne_tri .=ligneDrop($i_tri++);
                                    }
                                }
                                else
                                {
                                    //echo "B_BLESSES=".$row_pion->B_BLESSES;
                                    if ((1 == $row_pion->B_CONVOI && $row_pion->C_NIVEAU_DEPOT <> 'D') || 1==$row_pion->B_BLESSES || 1==$row_pion->B_PRISONNIERS || 1 == $row_pion->B_RENFORT)
                                    {
                                        //echo "ploug";
                                        if (0 == $row_pion->B_DETRUIT)
                                        {
                                            //Dans le cas des blesses et des prisonniers, on ne les affiche pas s'ils sont detruits
                                            AfficherConvoi($db, $fl_demmarage, $id_partie, $id_pion_role, $row_pion->ID_PION, 
                                                                            $row_pion->S_NOM, $row_pion->S_ORDRE_COURANT, $row_pion->ID_MODELE_PION, 
                                                                            $row_pion->B_RENFORT, $row_pion->B_BLESSES,  $row_pion->B_PRISONNIERS, 
                                                                            $row_pion->I_INFANTERIE,
                                                                            $row_pion->I_CAVALERIE,
                                                                            $row_pion->I_ARTILLERIE, $row_pion->I_EXPERIENCE, 
                                                                            $row_pion->S_POSITION, $row_pion->ID_BATAILLE, 
                                                                            $i_tour, $numPage);
                                            echo "<hr style=\"border-top: 5px solid white;\" />";
                                            $ligne_tri .=ligneDrag($i_tri,$row_pion->ID_PION, $row_pion->S_NOM, "ravitaillement-26.png");
                                            $ligne_tri .=ligneDrop($i_tri++);
                                        }
                                    }
                                    else
                                    {
                                        if ($row_pion->I_INFANTERIE_INITIALE>0 || $row_pion->I_CAVALERIE_INITIALE>0)
                                        {
                                            AfficherDivision($db, $fl_demmarage, $id_partie, $id_pion_role, $row_pion->ID_PION, 
                                                                $row_pion->S_NOM, $row_pion->S_ORDRE_COURANT, $row_pion->ID_MODELE_PION, 
                                                                $row_pion->B_DETRUIT, $row_pion->I_MORAL, $row_pion->I_MORAL_MAX, 
                                                                $row_pion->I_FATIGUE, $row_pion->I_INFANTERIE, $row_pion->I_INFANTERIE_INITIALE, 
                                                                $row_pion->I_CAVALERIE, $row_pion->I_CAVALERIE_INITIALE, $row_pion->I_ARTILLERIE, 
                                                                $row_pion->I_ARTILLERIE_INITIALE, $row_pion->I_EXPERIENCE, $row_pion->B_CAVALERIE_DE_LIGNE, 
                                                                $row_pion->B_CAVALERIE_LOURDE, $row_pion->B_GARDE, $row_pion->B_VIEILLE_GARDE, 
                                                                $row_pion->I_MATERIEL, $row_pion->I_RAVITAILLEMENT, $row_pion->S_POSITION, 
                                                                $row_pion->ID_BATAILLE, $i_tour, $row_pion->I_PATROUILLES_DISPONIBLES, 
                                                                $row_pion->I_PATROUILLES_MAX, $row_pion->I_NIVEAU_FORTIFICATION, $numPage);
                                            echo "<hr style=\"border-top: 5px solid white;\" />";
                                            if ($row_pion->I_CAVALERIE_INITIALE < $row_pion->I_INFANTERIE_INITIALE)
                                            {
                                                $ligne_tri .=ligneDrag($i_tri,$row_pion->ID_PION, $row_pion->S_NOM, "infanterie-26.png");
                                            }
                                            else
                                            {
                                                $ligne_tri .=ligneDrag($i_tri,$row_pion->ID_PION, $row_pion->S_NOM, "cavalerie-26.png");
                                            }
                                            $ligne_tri .=ligneDrop($i_tri++);
                                        }
                                        else
                                        {
                                            AfficherArtillerie($db, $fl_demmarage, $id_partie, $id_pion_role, $row_pion->ID_PION, 
                                                            $row_pion->S_NOM, $row_pion->S_ORDRE_COURANT, $row_pion->ID_MODELE_PION, 
                                                            $row_pion->B_DETRUIT, $row_pion->I_FATIGUE, $row_pion->I_EXPERIENCE, $row_pion->I_ARTILLERIE, 
                                                            $row_pion->I_ARTILLERIE_INITIALE, $row_pion->S_POSITION, $row_pion->ID_BATAILLE, $i_tour, $numPage);
                                            echo "<hr style=\"border-top: 5px solid white;\" />";
                                            $ligne_tri .=ligneDrag($i_tri,$row_pion->ID_PION, $row_pion->S_NOM, "artillerie-26.png");
                                            $ligne_tri .=ligneDrop($i_tri++);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            ?>
            <div class="row" id="tableau_messages">
                <div class="col-xs-12">
                    <h3>Messages re&ccedil;us</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-2 col-md-1">
                    <a href='nojs.htm' onclick="javascript: callTri('DT_DEPART'); return false;"><label class="control-label">Envoy&eacute;</label></a>
                </div>
                <div class="col-xs-12 col-sm-2 col-md-1">
                    <a href='nojs.htm' onclick="javascript: callTri('DT_ARRIVEE'); return false;"><label class="control-label">Re&ccedil;u</label></a>
                </div>
                <!--Liste des ordres envoyés dans une "table" triable gérée avec des divs -->
                <div class="col-xs-12 col-sm-2 col-md-2">
                    <a href='nojs.htm' onclick="javascript: callTri('S_NOM'); return false;"><label class="control-label">Par</label></a>
                    <select class="selectpicker" id="liste_emetteurs" name="liste_emetteurs" size=1 onchange="javascript: changeEmetteur(); return false;">
                        <?php
                        //affichage de tous les emetteurs de message
                        $requete = "SELECT DISTINCT S_NOM, tab_vaoc_pion.ID_PION ";
                        $requete.=" FROM tab_vaoc_message, tab_vaoc_pion ";
                        $requete.=" WHERE tab_vaoc_message.ID_PION_PROPRIETAIRE=" . $id_pion_role;
                        $requete.=" AND tab_vaoc_message.ID_PARTIE=" . $id_partie;
                        $requete.=" AND tab_vaoc_message.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
                        $requete.=" AND tab_vaoc_message.ID_EMETTEUR=tab_vaoc_pion.ID_PION";
                        $requete.=" ORDER BY S_NOM";
                        $res_emetteur = mysql_query($requete, $db);                        
                        echo "<option value=\"\">Tous</option>";
                        $critereEmetteur ="";
                        if (TRUE == isset($_REQUEST["liste_emetteurs"]) && TRUE == is_numeric($_REQUEST["liste_emetteurs"]))
                        {
                            //echo "liste emetteur pas faux";
                            $critereEmetteur = $_REQUEST["liste_emetteurs"];
                        }
                        while ($row = mysql_fetch_object($res_emetteur))
                        {
                            echo "<option";
                            if ($critereEmetteur == $row->ID_PION)
                            {
                                echo " selected=\"selected\"";
                            }
                            printf(" value=\"%s\">%s</option>", $row->ID_PION, $row->S_NOM);
                        }
                        ?>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-2 col-md-2">
                    <a href='nojs.htm' onclick="javascript:callTri('S_ORIGINE');return false;"><label class="control-label">Origine</label></a>
                </div>
                <div class="col-xs-12 col-sm-4 col-md-6">
                    <label class="control-label">Message</label>
                </div>
            </div>
            <?php
            $requete = "SELECT DATE_FORMAT(DT_DEPART,'%W %e %M %Y %H:%i') AS DATE_DEPART, DATE_FORMAT(DT_ARRIVEE,'%W %e %M %Y %H:%i') AS DATE_ARRIVEE, S_NOM, S_ORIGINE, S_MESSAGE ";
            $requete.=" FROM tab_vaoc_message, tab_vaoc_pion ";
            $requete.=" WHERE tab_vaoc_message.ID_PION_PROPRIETAIRE=" . $id_pion_role;
            $requete.=" AND tab_vaoc_message.ID_PARTIE=" . $id_partie;
            $requete.=" AND tab_vaoc_message.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
            $requete.=" AND tab_vaoc_message.ID_EMETTEUR=tab_vaoc_pion.ID_PION";
            //chaine vide = valeur de "tous"
            if ($critereEmetteur <>"")
            {
                $requete.=" AND tab_vaoc_pion.ID_PION='".$critereEmetteur."'";
            }
            $res_messages = mysql_query($requete, $db);
            $nb_messages_recus = mysql_num_rows($res_messages);
            //echo $requete;
            //echo "nb_messages_recus=".$nb_messages_recus;
            $offset_recus = ($pageNum_recus - 1) * $nombre_messages_pages;
            if ($offset_recus < 0)
            {
                    $offset_recus = 0;
            }

            //$requete.=" ORDER BY DT_ARRIVEE DESC, ID_MESSAGE DESC LIMIT ".$offset_recus.",".$nombre_messages_pages;
            $requete.=" ORDER BY " . $tri_liste . " " . $ordre_tri_liste . ", ID_MESSAGE DESC, ID_MESSAGE LIMIT " . $offset_recus . "," . $nombre_messages_pages;
            //echo $requete;
            $res_messages = mysql_query($requete, $db);
            if (0 == mysql_num_rows($res_messages))
            {
                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">Vous n'avez re&ccedil;us aucun message</div>";
                echo "</div>";
            }
            else
            {
                while ($row_message = mysql_fetch_object($res_messages))
                {
                    echo "<hr style=\"width: 100%; border-top: 1px solid white; margin-top:0px; margin-bottom:0px;\" />";
                    echo "<div class=\"row\">";
                    echo "<div class=\"col-xs-12 col-sm-2 col-md-1\">" . $row_message->DATE_DEPART . "</div>";// à l'origine col-xs-12 col-sm-2 col-md-2
                    echo "<div class=\"col-xs-12 col-sm-2 col-md-1\">" . $row_message->DATE_ARRIVEE . "</div>";// à l'origine col-xs-12 col-sm-2 col-md-2
                    echo "<div class=\"col-xs-12 col-sm-2 col-md-2\">" . $row_message->S_NOM . "</div>";// à l'origine: col-xs-12 col-sm-4 col-md-3
                    echo "<div class=\"col-xs-12 col-sm-2 col-md-2\">" . $row_message->S_ORIGINE . "</div>";// à l'origine: col-xs-12 col-sm-3 col-md-6
                    echo "<div class=\"col-xs-12 col-sm-4 col-md-6\">" . $row_message->S_MESSAGE . "</div>";// à l'origine: col-xs-12 col-sm-3 col-md-6
                    echo "</div>";
                }

                $maxPage_recus = ceil($nb_messages_recus / $nombre_messages_pages);

                if ($maxPage_recus > 1)
                {
                    echo "<div class=\"row\">";
                    echo "<div class=\"col-xs-12\">";
                    echo "<nav>";
                    echo "<ul class=\"pagination\">";
                    echo "<li>";
                    echo "<a href=\"#\" aria-label=\"Premier\" onclick=\"javascript:callAllerALapage(-1,1);return false;\">";
                    echo "<span aria-hidden=\"true\">&laquo;</span>";
                    echo "</a>";
                    echo "</li>";
                    for ($page = 1; $page <= $maxPage_recus; $page++)
                    {
                        echo "<li>";
                        if ($page == $pageNum_recus)
                        {
                            echo "<span aria-hidden=\"true\"><strong>". $page ."</strong></span>";
                        }
                        else
                        {
                            echo "<a href=\"#\" onclick=\"javascript:callAllerALapage(-1,". $page .");return false;\">";
                            echo "<span aria-hidden=\"true\">". $page . "</span>";
                            echo "</a>";
                        }
                        echo "</li>";
                    }
                    echo "<li>";
                    echo "<a href=\"#\" aria-label=\"Premier\" onclick=\"javascript:callAllerALapage(-1,". $maxPage_recus .");return false;\">";
                    echo "<span aria-hidden=\"true\">&laquo;</span>";
                    echo "</a>";
                    echo "</li>";
                    echo "</ul></nav></div></div>";
                }
                
                //choix du nombre de messages par page
                echo "<select class=\"selectpicker\" id=\"nombre_messages_pages\" name=\"nombre_messages_pages\" size=1 onchange=\"javascript:callNombreMessagesPage();\">";
                foreach ($listeNombreMessages as $listeNombre)
                {
                    echo "<option";
                    if ($listeNombre == $nombre_messages_pages)
                    {
                            echo " selected=\"selected\"";
                    }
                    printf(" value=\"%u\">%s</option>", $listeNombre, $listeNombre);
                }
                echo "</select>";
            }
            echo "<input id=\"pageNum_recus\" name=\"pageNum_recus\" type=\"hidden\" value=\"0\" />";
            ?>
            
            <div class="row row-centered">
                <div class="col-xs-12 col-centered">
                    <input name="id_campagne" class="btn btn-info"
                           id="id_campagne" onclick="javascript: callPage('vaoccampagne.php'); return false;"
                           type="image" alt="campagne" src="images/btnCampagne2.png" />

                    <input name="id_aide" class="btn btn-info"
                           id="id_aide" onclick="javascript: window.open('aide.html'); return false;"
                           type="image" alt="connexion" src="images/btnAide2.png" />

                    <input name="id_quitter" class="btn btn-default"
                           id="id_quitter" onclick="javascript: callQuitter();"
                           type="image" alt="retour a l'ecran general" src="images/btnQuitter2.png" value="submit" />                
                </div>
             </div>
            <!-- Modal content pour la reorganisation -->
            <div class="modal fade" id="id_fenetre_reorganise" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content reorganisation-modal">
                    <div class="modal-header">
                        <h2 class="modal-title" id="id_fenetre_reorganise_titre" style="color:black;">R&eacute;organisation                            
                        </h2>
                    </div>
                    <div class="modal-body">
                      <?php echo $ligne_tri;  ?>
                    </div>
                    <div class="modal-footer">
                        <input name="id_validerReorg" class="btn btn-default" data-dismiss="modal" id="id_validerReorg" onclick="javascript:Trier(); return false;"
                               data-dismiss="modal" type="image" alt="valider" src="images/btnTrier2.png" value="submit"/>&nbsp;
                        <input name="id_annulerReorg" class="btn btn-default" data-dismiss="modal"
                                id="id_annulerReorg" type="image" alt="valider" src="images/btnQuitter2.png" value="submit"/>
                    </div>
                </div>
            </div>
        </div>	
        </form>
        </div>
        <!-- Scripts pour le chargement differe -->
            <?php
            global $chargement_destination_script;//indique que l'on reprend la variable globale de vaocfonctions.php
            //permet de faire un chargement sur la clic de la liste des destination, la page se charge donc beaucoup plus vite
            echo "<script type=\"text/javascript\">$(document).ready(function () {" . $chargement_destination_script . "});</script>";
        /*
            $("#Select1").click(function () {
                if (charge) {
                    $("#Select1").html("<option id=\"1\">1</option><option id=\"2\">2</option><option id=\"3\">trois</option>");
                    charge = false;
          */
            /* Ne sert plus à rien avec le test sur refresh et la nouvelle variable e SESSION action
            if ((TRUE == isset($id_patrouillera) && TRUE == is_numeric($id_patrouillera)) ||
			(TRUE == isset($id_endommager_pont) && TRUE == is_numeric($id_endommager_pont)) ||
			(TRUE == isset($id_envoyermessagea) && TRUE == is_numeric($id_envoyermessagea)))
            {
		//echo "callMoi";
		//--> Mince mais ca sert a quoi deja ce truc ! A Eviter le postback
		//echo "<script type=\"text/javascript\">callMoi();</script>";
            }
            */
	?>	    
    </body>
</html>
