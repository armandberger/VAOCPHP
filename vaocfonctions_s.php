<script language="php">
    require("performance_s.php"); //uniquement pour la mesure de performance

/* * * Ensemble des fonctions d'affichage utilisees par toutes les pages VAOC * * */
//Constantes
define("SANS_PROPRIETAIRE", 6);
define("NB_COLS_UNITES", 10);
define("NB_COLS_QG", 5);
define("NB_MESSAGES_MAX", 5);
define("NB_PAGINATION_MAX", 10);
define("NB_MESSAGES_HISTORIQUE_MAX", 20);

define("ORDRE_MOUVEMENT", 1);
define("ORDRE_COMBAT", 2);
define("ORDRE_RETRAITE", 3);
define("ORDRE_PATROUILLE", 4);
define("ORDRE_MESSAGER", 5);
define("ORDRE_ENDOMMAGER_PONT", 6);
define("ORDRE_REPARER_PONT", 7);
define("ORDRE_CONSTRUIRE_PONT", 8);
define("ORDRE_ARRET", 9);
define("ORDRE_TRANSFERT", 10);
define("ORDRE_GENERER_CONVOI", 11);
define("ORDRE_RENFORCER", 12);
define("ORDRE_SE_FORTIFIER", 13);
define("ORDRE_ENGAGEMENT", 14); //uniquement dans vaocfonctionsbataille
define("ORDRE_ETABLIRDEPOT", 15);
define("ORDRE_RETRAIT", 16); //uniquement dans vaocfonctionsbataille
define("ORDRE_MESSAGE_FORUM", 17);//uniquement pour les messages direct
define("ORDRE_LIGNE_RAVITAILLEMENT", 18);
define("ORDRE_REDUIRE_DEPOT", 19);
define("ORDRE_RAVITAILLEMENT_DIRECT", 20);

$listeNombreMessages = array();
$listeNombreMessages[] = "5";
$listeNombreMessages[] = "10";
$listeNombreMessages[] = "20";
$listeNombreMessages[] = "50";
$listeNombreMessages[] = "100";

$chargement_destination_script = "";

function get_nom_navigateur($user_agent)
{
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edge')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
   
    return 'Autre';
}

function ecrireLog($ligne)
{
    $log  = "User: ".$_SERVER['REMOTE_ADDR'].' - '.date("F j, Y, g:i a").PHP_EOL.
        "Log: ".$ligne.PHP_EOL.
        "-------------------------".PHP_EOL;
    //Save string to log, use FILE_APPEND to append.
    file_put_contents('./log_'.date("j.n.Y").'.log', $log, FILE_APPEND);  
}

function ligneDrop($i_ligne)
{
    return "<div id=\"drop".$i_ligne."\" class=\"dropper col-12 col-centered\" ondrop=\"drop(event)\" ondragover=\"allowDrop(event)\" ondragenter=\"entrerDrop(event)\" ondragleave=\"quitterDrop(event)\">&nbsp;</div>";
}

function Abbreviations($test, $nom)
{
    if (substr($nom,0,strlen($test)) == $test)
    {
        $nom = substr($nom,0, 4) . "." .substr($nom,strlen($test));
    }
    return $nom;
}
function ligneDrag($i_ligne, $id_pion, $nom, $image)
{
    //40 sans small
    //reduction des phrases
    $nom = Abbreviations("Convoi de ravitaillement", $nom);
    $nom = Abbreviations("Prisonniers", $nom);
    $nom = Abbreviations("Blessés", $nom);
    $ligne = "<div id=\"drag".$i_ligne."\" id_pion=\"".$id_pion."\" class=\"draggable col-12 col-centered\" draggable=\"true\" ondragstart=\"drag(event)\">";
    $ligne.= "<image src=\"images/".$image."\"/>&nbsp;<small>".substr($nom,0,40)."</small></div>";
    return $ligne;
}

function DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE)
{
    Debug_Logger::getInstance()->time_mesure_start('DateDernierMessage');
    //recherche de l'heure du dernier message recu
    $requete = "SELECT DATE_FORMAT(DT_DEPART,'%W %e %M %Y %H:%i') AS DATE_DEPART ";
    $requete.="FROM tab_vaoc_message ";
    $requete.="WHERE tab_vaoc_message.ID_EMETTEUR=" . $ID_PION . " AND tab_vaoc_message.ID_PARTIE=" . $ID_PARTIE . " ";
    $requete.="AND tab_vaoc_message.ID_PION_PROPRIETAIRE=" . $ID_PION_PROPRIETAIRE . " ";
    $requete.="ORDER BY DT_DEPART DESC";
    $res_dernier_message = mysql_query($requete, $db);
    $row_dernier_message = mysql_fetch_object($res_dernier_message);

    if (empty($row_dernier_message->DATE_DEPART))
    {
            return "Aucun message";
    }
    Debug_Logger::getInstance()->time_mesure_end('DateDernierMessage');
    //return "dimanche 23 mai 1813 09:36";
    return $row_dernier_message->DATE_DEPART;
}

function ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, $NOM_MODELE_IMAGE) 
{
    Debug_Logger::getInstance()->time_mesure_start('ImageModele');
    //on recherche l'image associe a l'unite
    $requete = "SELECT ". $NOM_MODELE_IMAGE." AS S_IMAGE ";
    $requete.="FROM tab_vaoc_nation, tab_vaoc_modele_pion ";
    $requete.="WHERE tab_vaoc_nation.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_nation.ID_PARTIE = tab_vaoc_modele_pion.ID_PARTIE";
    $requete.=" AND tab_vaoc_nation.ID_NATION = tab_vaoc_modele_pion.ID_NATION";
    $requete.=" AND tab_vaoc_modele_pion.ID_MODELE_PION = " . $ID_MODELE_PION;
    //echo $requete;
    $res_modele_pion = mysql_query($requete, $db);
    $row_modele_pion = mysql_fetch_object($res_modele_pion);

    Debug_Logger::getInstance()->time_mesure_end('ImageModele');
    return $row_modele_pion->S_IMAGE;
}

function AfficherDemandeEtablirDepot($FL_DEMMARAGE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeEtablirDepot');
    echo "<button alt=\"ordre d'etablir un depot\" id=\"id_etablir_ordre\" name=\"id_etablir_ordre\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_etablir');\" ";
    if (0 === $FL_DEMMARAGE)
    {
        //ordres interdits ou aucune unite renforcable
        echo " disabled ";
    }
    echo ">Etablir un d&eacute;p&ocirc;t</button>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeEtablirDepot');
}

function AfficherDemandeRenforcer($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE, $B_DEPOT, $ID_DEPOT_SOURCE, $ID_MODELE_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeRenforcer');
    //liste des unites que l'on peut renforcer sous son commandement, sinon, il faut transferer a un autre chef qui renforcera selon choix
    if (0 == $B_DEPOT)
    {
        //l'unite peut fusionner avec une autre unite de combat
        $requete = "SELECT pion1.ID_PION, pion1.S_NOM";
        $requete.=" FROM tab_vaoc_pion pion1";
        $requete.=" WHERE pion1.ID_PARTIE=" . $ID_PARTIE;
        $requete.=" AND pion1.B_DETRUIT=0 AND pion1.B_DEPOT=0 AND pion1.B_PONTONNIER=0 and pion1.B_RENFORT=0 and pion1.B_CONVOI=0 and pion1.B_PRISONNIERS=0";
        //$requete.=" AND (pion1.I_INFANTERIE>0 OR pion1.I_CAVALERIE>0 OR pion1.I_ARTILLERIE>0)";
        $requete.=" AND pion1.B_QG<=0";
        $requete.=" AND pion1.ID_PION_PROPRIETAIRE=" . $ID_PION_PROPRIETAIRE;
        $requete.=" ORDER BY S_NOM";
    }
    else
    {
        //L'unite peut fusionner avec un autre depot
        $requete = "SELECT pion1.ID_PION, pion1.S_NOM";
        $requete.=" FROM tab_vaoc_pion pion1, tab_vaoc_modele_pion modele1, tab_vaoc_modele_pion modele2";
        $requete.=" WHERE pion1.ID_PARTIE=" . $ID_PARTIE;
        $requete.=" AND pion1.B_DETRUIT=0 AND pion1.B_DEPOT=1 AND pion1.B_PONTONNIER=0 and pion1.B_RENFORT=0 and pion1.B_CONVOI=0 and pion1.C_NIVEAU_DEPOT<>'A'";
        $requete.=" AND modele1.ID_MODELE_PION=" . $ID_MODELE_PION;
        $requete.=" AND modele1.ID_NATION = modele2.ID_NATION";
        $requete.=" AND modele2.ID_MODELE_PION=pion1.ID_MODELE_PION";
        $requete.=" GROUP BY ID_PION, S_NOM";
        //$requete.=" AND pion1.ID_PION_PROPRIETAIRE=" . $ID_PION_PROPRIETAIRE; -> ne permet pas d'aider des depots de la meme coalition
        //$requete.=" AND pion1.ID_PION<>" . $ID_DEPOT_SOURCE; -> permet de corriger une erreur de generation comme ca
        $requete.=" ORDER BY S_NOM";
    }
    //echo $requete;
    $res_renforcer_unite = mysql_query($requete, $db);

    
    echo "<div class=\"form-inline\">";
    echo "<button alt=\"ordre de renfort d'une unite\" id=\"id_renforcer_ordre\" name=\"id_renforcer_ordre\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_renforcer');\" ";
    if (0 == $FL_DEMMARAGE || 0 == mysql_num_rows($res_renforcer_unite))
    {
        //ordres interdits ou aucune unit� renforcable
        echo " disabled ";
    }
    echo ">Renforcer</button>";

    if (0 == mysql_num_rows($res_renforcer_unite))
    {
        echo "Aucune unit&eacute; sous votre commandement ne peut &ecirc;tre renforc&eacute;e";
    }
    else
    {
        $id_chaine = "id_renforcer_unite_" . $ID_PION;
        printf("&nbsp;<select class=\"custom-select\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
        if (0 == $FL_DEMMARAGE)
        {
            echo " disabled ";
        }
        echo ">";
        while ($row = mysql_fetch_object($res_renforcer_unite))
        {
            echo "<option";
            if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine] == $row->ID_PION)
            {
                echo " selected=\"selected\"";
            }
            printf(" value=\"%u\">%s</option>", $row->ID_PION, $row->S_NOM);
        }
        echo "</select>";
    }
    echo "</div>";
    echo "\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeRenforcer');
}

function AfficherDemandeGenererConvoi($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, $C_NIVEAU_DEPOT)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeGenererConvoi');
    echo "<button alt=\"generer un nouveau convoi\" id=\"id_generer_nouveau_convoi\" name=\"id_generer_nouveau_convoi\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_generer_convoi');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " disabled >G&eacute;n&eacute;rer un convoi";
    }
    else
    {
        //on ne peut generer un convoir qu'une seule fois par 24 heures
        //on regarde depuis combien de temps le dernier convoi a ete genere
        $requete_dernier_ordre = "SELECT I_TOUR FROM tab_vaoc_ordre WHERE ID_PION = " . $ID_PION . " AND ID_PARTIE=" . $ID_PARTIE . " AND I_TYPE=" . ORDRE_GENERER_CONVOI;
        $requete_dernier_ordre.=" ORDER BY I_TOUR DESC";
        //echo $requete_dernier_ordre;
        $res_dernier_ordre = mysql_query($requete_dernier_ordre, $db);
        if (mysql_num_rows($res_dernier_ordre) > 0)
        {
            $row_dernier_ordre = mysql_fetch_object($res_dernier_ordre);
            if ($C_NIVEAU_DEPOT <> 'D')
            {
                $delai = 24 - ($I_TOUR - $row_dernier_ordre->I_TOUR);
            }
            else
            {
                $delai = 1 - ($I_TOUR - $row_dernier_ordre->I_TOUR); //simplement pas deux fois dans le meme tour pour unc convoi D
            }

            if ($delai > 0)
            {
                echo " disabled >";
                echo " pas de nouvelle g&eacute;n&eacute;ration avant " . $delai . " heure";
                if ($delai > 1)
                {
                    echo "s";
                }
            }
            else
            {
                echo ">G&eacute;n&eacute;rer un convoi";
            }
        }
        else
        {
            echo ">G&eacute;n&eacute;rer un convoi";
        }
    }
    echo "</button>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeGenererConvoi');
}

function AfficherDemandeReduireDepot($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeReduireDepot');
    //echo "<span>FL_DEMMARAGE=".$FL_DEMMARAGE."</span>";
    echo "<button alt=\"reduire un depot\" id=\"reduire un depot\" name=\"reduire un depot\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_reduire_depot');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " disabled";
    }
    else
    {
        //on ne peut donner cet ordre que s'il n'a pas dejà ete donne
        $requete_dernier_ordre = "SELECT I_TOUR FROM tab_vaoc_ordre WHERE ID_PION = " . $ID_PION . " AND ID_PARTIE=" . $ID_PARTIE . " AND I_TYPE=" . ORDRE_REDUIRE_DEPOT;
        $requete_dernier_ordre.=" ORDER BY I_TOUR DESC";
        //echo $requete_dernier_ordre;
        $res_dernier_ordre = mysql_query($requete_dernier_ordre, $db);
        if (mysql_num_rows($res_dernier_ordre) > 0)
        {
            //48 heures, en me disant qu'il faut maximum 24 heures pour que l'ordre revienne et que le joueur sache, effectivement que le depôt est passe au niveau B
            $delai = 48 - ($I_TOUR - $row_dernier_ordre->I_TOUR);
            if ($delai > 0)
            {
                echo " disabled";
            }
        }
    }
    echo ">R&eacute;duire un d&eacutep&ocirc;t</button>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeReduireDepot');
}

function AfficherDemandeLigneDeRavitaillement($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeLigneDeRavitaillement');
    echo "<div class=\"form-inline\">";
        echo "<button alt=\"cr&eacute;er une ligne de ravitaillement\" id=\"ligne_de_ravitaillement\" name=\"ligne_de_ravitaillement\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_ligne_ravitaillement');\" ";
        if (0 == $FL_DEMMARAGE)
        {
            //ordres interdits
            echo " disabled";
        }
        echo ">Ligne de ravitaillement</button>\r\n";
        //choix d'une distance
        echo "&nbsp;<div class=\"input-group\">";
            echo "<div class=\"input-group-prepend\">";
            $id_chaine = "id_distance_mouvement_" . $ID_PION;
            AfficherDistance($db, $FL_DEMMARAGE, $id_chaine);
            echo "</div>";

            echo "<span class=\"input-group-text\">&nbsp;km&nbsp;</span>";

            //choix d'une direction
            echo "<div class=\"input-group-append\">";
            $id_chaine = "id_direction_mouvement_" . $ID_PION;
            AfficherDirection($db, $FL_DEMMARAGE, $id_chaine);
            echo "</div>";
        echo "</div>";

        echo "&nbsp;<div class=\"input-group\">";
            echo "<div class=\"input-group-prepend\"><span class=\"input-group-text\">&nbsp;de&nbsp;</span></div>";
            $id_chaine = "id_destination_mouvement_" . $ID_PION;
            AfficherDestination($db, $FL_DEMMARAGE, $id_chaine, $ID_PARTIE);
        echo "</div>";
    echo "</div>\r\n";
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeLigneDeRavitaillement');
}

function AfficherDemandeTransfert($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeTransfert');
    echo "<div class=\"form-inline\">";
    echo "<button alt=\"ordre de transfert d'une unit&eacute;\" id=\"id_transfert_ordre\" name=\"id_transfert_ordre\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_transfert');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " disabled ";
    }
    echo ">Transfert</button>\r\n";
    //recherche de la nation du QG
    $requete = "SELECT ID_NATION";
    $requete.=" FROM tab_vaoc_modele_pion, tab_vaoc_pion ";
    $requete.=" WHERE tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_pion.ID_PARTIE=tab_vaoc_modele_pion.ID_PARTIE";
    $requete.=" AND tab_vaoc_modele_pion.ID_MODELE_PION=tab_vaoc_pion.ID_MODELE_PION";
    $requete.=" AND tab_vaoc_pion.ID_PION=" . $ID_PION;
    //echo $requete;
    $res_nation_qg = mysql_query($requete, $db);
    $row_nation_qg = mysql_fetch_object($res_nation_qg);

    //choix d'une unite
    $requete = "SELECT pion1.ID_PION, CONCAT(pion2.S_NOM, \" - \", pion1.S_NOM) AS NOM_COMPLET";
    $requete.=" FROM tab_vaoc_pion pion1, tab_vaoc_modele_pion, tab_vaoc_pion pion2";
    $requete.=" WHERE pion1.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND pion1.B_DETRUIT=0";// AND pion1.B_PONTONNIER=0";
    $requete.=" AND (pion1.I_INFANTERIE>0 OR pion1.I_CAVALERIE>0 OR pion1.I_ARTILLERIE>0 OR pion1.B_PONTONNIER=1 OR pion1.B_DEPOT=1 OR pion1.B_CONVOI=1)";
    $requete.=" AND pion1.ID_PARTIE=tab_vaoc_modele_pion.ID_PARTIE";
    $requete.=" AND pion1.ID_MODELE_PION=tab_vaoc_modele_pion.ID_MODELE_PION";
    $requete.=" AND tab_vaoc_modele_pion.ID_NATION=" . $row_nation_qg->ID_NATION;
    $requete.=" AND pion1.ID_PARTIE=pion2.ID_PARTIE";
    $requete.=" AND pion1.ID_PION_PROPRIETAIRE=pion2.ID_PION";
    $requete.=" ORDER BY NOM_COMPLET";
    //echo $requete;
    $res_transfert_unite = mysql_query($requete, $db);

    //$id_chaine="id_transfert_unite_".$ID_PION;
    $id_chaine = "id_transfert_unite";

    printf("&nbsp;<select class=\"custom-select\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
    if (0 == $FL_DEMMARAGE)
    {
        echo " disabled ";
    }
    echo ">";
    //on ajoute une option vide pour eviter que l'utilisateur n'envoie l'ordre par megarde
    echo "<option value=\"\"></option>";
    while ($row = mysql_fetch_object($res_transfert_unite))
    {
        echo "<option";
        if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine] == $row->ID_PION)
        {
            echo " selected=\"selected\"";
        }
        printf(" value=\"%u\">%s</option>", $row->ID_PION, $row->NOM_COMPLET);
    }
    echo "</select>";
    echo "&nbsp;&agrave;&nbsp;";

    //choix d'un QG ou transferer l'unite
    $requete = "SELECT tab_vaoc_pion.ID_PION, tab_vaoc_pion.S_NOM";
    $requete.=" FROM tab_vaoc_pion";
    $requete.=" WHERE ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND B_DETRUIT=0 AND C_NIVEAU_HIERARCHIQUE<>'Z'";
    $requete.=" AND I_INFANTERIE=0 AND I_CAVALERIE=0 AND I_ARTILLERIE=0";
    $requete.=" AND ID_PION_PROPRIETAIRE=" . $ID_PION;
    $requete.=" ORDER BY S_NOM";
    //echo $requete;
    $res_transfert_qg = mysql_query($requete, $db);

    $id_chaine = "id_transfert_qg";

    printf("<select class=\"custom-select\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
    if (0 == $FL_DEMMARAGE)
    {
        echo " disabled ";
    }
    echo ">";
    //on ajoute une option vide pour eviter que l'utilisateur n'envoie l'ordre par megarde
    echo "<option value=\"\"></option>";
    while ($row = mysql_fetch_object($res_transfert_qg))
    {
        echo "<option";
        if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine] == $row->ID_PION)
        {
            echo " selected=\"selected\"";
        }
        printf(" value=\"%u\">%s</option>", $row->ID_PION, $row->S_NOM);
    }
    echo "</select>";
    echo "</div>";
    echo "\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeTransfert');
}

function AfficherDemandeArret($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeArret');
    echo "<button alt=\"ordre d'arret sur position\" id=\"id_arret_unite\" name=\"id_arret_unite\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_arret');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo "  disabled";
    }
    echo "/>Arr&ecirc;t</button>";
    echo "\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeArret');
}

function AfficherDemandeReparerPont($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeReparerPont');
    echo "<button alt=\"ordre de reparer un pont\" id=\"id_reparer_pont_unite\" name=\"id_reparer_pont_unite\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_reparer_pont');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " disabled";
    }
    echo ">R&eacute;parer un pont</button>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeReparerPont');
}

function AfficherDemandeEndommagerPont($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeEndommagerPont');
    echo "<button alt=\"ordre d'endommager un pont\" id=\"id_endommager_pont_unite\" name=\"id_endommager_pont_unite\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_endommager_pont');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo "disabled ";
    }
    echo ">Endommager un pont</button>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeEndommagerPont');
}

function AfficherDemandeConstruirePont($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeConstruirePont');
    echo "<button alt=\"ordre de construction de pont\" id=\"id_construire_pont_unite\" name=\"id_construire_pont_unite\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_construire_pont_unite');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " disabled";
    }
    echo ">Construire un pont</button>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeConstruirePont');
}

function AfficherDemandeConstruireFortifcation($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeConstruireFortifcation');
    echo "<button alt=\"ordre de construction de fortification\" id=\"id_construire_fortification\" name=\"id_construire_fortification\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_construire_fortification_unite');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " disabled";
    }
    echo ">Construire des fortifications</button>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeConstruireFortifcation');
}

function AfficherDemandeRavitaillementDirect($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeRavitaillementDirect');
    echo "<button alt=\"ordre de ravitaillement direct\" id=\"id_ravitaillement_direct\" name=\"id_ravitaillement_direct\" typebuttonimage\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_ravitaillement_direct_unite');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " disabled";
    }
    echo ">Ravitaillement direct</button>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeRavitaillementDirect');
}

function AfficherDemandeOrdrePatrouiller($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeOrdrePatrouiller');
    echo "<div class=\"form-inline\">";
    //echo "FL_DEMMARAGE=".$FL_DEMMARAGE;
    echo "<button alt=\"ordre de patrouille\" id=\"id_mouvement_patrouille\" name=\"id_mouvement_patrouille\" type=\"button\" value=\"submit\" class=\"btn btn-light bouton\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_patrouillera');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " disabled";
    }
    echo ">Patrouiller</button>";

    //choix d'une distance
    echo "<div class=\"input-group\">";
        $id_chaine = "id_distance_patrouille_" . $ID_PION;

        printf("<select class=\"custom-select\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
        if (0 == $FL_DEMMARAGE)
        {
            echo " disabled ";
        }
        echo ">";
        for ($i = 0; $i < 50; $i++)
        {
            echo "<option";
            if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine] == $i)
            {
                echo " selected=\"selected\"";
            }
            printf(" value=\"%u\">%s</option>", $i, $i);
        }
        echo "</select>";
        echo "<div class=\"input-group-addon\">&nbsp;km&nbsp;</div>";

        //choix d'une direction
        $requete = "SELECT ID_PARAMETRE, S_VALEUR, I_VALEUR";
        $requete.=" FROM tab_vaoc_parametre";
        $requete.=" WHERE S_TYPE='direction'";
        $requete.=" ORDER BY S_VALEUR";
        //echo $requete;
        $res_direction = mysql_query($requete, $db);
        //$id_direction = "id_direction_patrouille_" . $ID_PION;
        $id_chaine = "id_direction_patrouille_" . $ID_PION;
        printf("<select class=\"custom-select\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
        if (0 == $FL_DEMMARAGE)
        {
            echo " disabled ";
        }
        echo ">";
        while ($row = mysql_fetch_object($res_direction))
        {
            echo "<option";
            if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine] == $row->I_VALEUR)
            {
                echo " selected=\"selected\"";
            }
            printf(" value=\"%u\">%s</option>", $row->I_VALEUR, $row->S_VALEUR);
        }
        echo "</select>";
    echo "</div>";
    echo "<div class=\"input-group\">";
        echo "<div class=\"input-group-addon\">&nbsp;de&nbsp;</div>";

        //choix d'une destination
        $id_chaine = "id_destination_patrouille_" . $ID_PION;
        AfficherDestination($db, $FL_DEMMARAGE, $id_chaine, $ID_PARTIE);

    echo "</div>";
    echo "</div>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeOrdrePatrouiller');
}

function AfficherPatrouilles($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_PATROUILLES_DISPONIBLES, $I_PATROUILLES_MAX)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherPatrouilles');
    //recherche des patrouilles deja en cours de mission
    //-> en fait, ne sert à rien car deja conserve dans les ordres transmis ? Sauf que l'on ne peut jamais savoir a quel ordre
    //correspond une patrouille qui revient. On ne peut donc qu'afficher les ordres donnes, au joueur de faire le tri !
    //Il faut quand meme afficher les patrouilles envoyees a ce tour ci pour permettre au joueur de pouvoir les supprimer
    //-> fait dans l'affichage des ordres
    //recherche le nombre d'ordres de patrouilles envoyes à ce tour
    //echo "FL_DEMMARAGE=".$FL_DEMMARAGE;
    //envoie de nouvelles patrouilles  ?
    if (0 == $I_PATROUILLES_DISPONIBLES)
    {
        //echo "<tr><td colspan='".NB_COLS_UNITES."'>Aucune patrouille n'est disponible</td></tr>";
        //echo "I_PATROUILLES_DISPONIBLES=".$I_PATROUILLES_DISPONIBLES;
        echo "Aucune patrouille n'est disponible";
    }
    else
    {
        //echo "<tr><td colspan='".NB_COLS_UNITES."'>".$I_PATROUILLES_DISPONIBLES." patrouille";
        echo $I_PATROUILLES_DISPONIBLES . " patrouille";
        if ($I_PATROUILLES_DISPONIBLES > 1)
        {
            echo "s sont disponibles";
        }
        else
        {
            echo " est disponible";
        }
    }
    if ($I_PATROUILLES_MAX > 0)
    {
        echo " sur un total de " . $I_PATROUILLES_MAX . " patrouille";
        if ($I_PATROUILLES_MAX > 1)
        {
            echo "s";
        }
    }

    if ($I_PATROUILLES_DISPONIBLES > 0)
    {
        echo "\r\n";
        AfficherDemandeOrdrePatrouiller($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION);
    }
    echo "\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherPatrouilles');
}

function DonnerDirection($db, $I_DIRECTION)
{
    Debug_Logger::getInstance()->time_mesure_start('DonnerDirection');
    $requete = "SELECT S_VALEUR";
    $requete.=" FROM tab_vaoc_parametre";
    $requete.=" WHERE S_TYPE='direction' AND I_VALEUR=" . $I_DIRECTION;
    $res_direction = mysql_query($requete, $db);
    $row = mysql_fetch_object($res_direction);
    Debug_Logger::getInstance()->time_mesure_end('DonnerDirection');
    return $row->S_VALEUR;
}

function DonnerNomCarte($db, $ID_PARTIE, $ID_NOM)
{
    Debug_Logger::getInstance()->time_mesure_start('DonnerNomCarte');
    $requete = "SELECT tab_vaoc_noms_carte.S_NOM";
    $requete.=" FROM tab_vaoc_noms_carte";
    $requete.=" WHERE tab_vaoc_noms_carte.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_noms_carte.ID_NOM=" . $ID_NOM;
    $res_nom = mysql_query($requete, $db);
    $row = mysql_fetch_object($res_nom);
    //echo $requete;
    Debug_Logger::getInstance()->time_mesure_end('DonnerNomCarte');
    return $row->S_NOM;
}

function AfficherIlyAOrdre($DUREE, $bOrdrePrimaire)
{
    $s_retour = "";
    if ($bOrdrePrimaire)
    {
        $s_retour = " il y a " . $DUREE . " heure";
        if ($DUREE > 1)
        {
            $s_retour = $s_retour . "s";
        }
    }
    return $s_retour;
}

function DecrireOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, $QG, $ID_ORDRE, $bOrdrePrimaire, &$I_PATROUILLES_DISPONIBLES)
{
    Debug_Logger::getInstance()->time_mesure_start('DecrireOrdre');
    Debug_Logger::getInstance()->time_mesure_start('DecrireOrdre : requete 1');
    $requete = "SELECT ID_ORDRE, ID_ORDRE_SUIVANT, I_TYPE, I_DISTANCE, I_DIRECTION, ID_NOM_LIEU, I_HEURE, I_DUREE, S_MESSAGE, ";
    $requete.=" I_TOUR, ID_PION_DESTINATION";
    $requete.=" FROM tab_vaoc_ordre";
    $requete.=" WHERE ID_ORDRE=" . $ID_ORDRE . " AND tab_vaoc_ordre.ID_PARTIE=" . $ID_PARTIE;
    echo $requete;
    $res_ordre = mysql_query($requete, $db);
    $row = mysql_fetch_object($res_ordre);
    Debug_Logger::getInstance()->time_mesure_end('DecrireOrdre : requete 1');

    if ($bOrdrePrimaire)
    {
        //$s_retour = "<div class=\"col-12 col-sm-3 col-md-2\">";
        $s_retour = "<div class=\"col-12 col-sm-1\">";
        //echo 'itour='.$I_TOUR." demmarage=".$FL_DEMMARAGE;
        //echo 'ritour='.$row->I_TOUR." type=".$row->I_TYPE;
        
        if ($I_TOUR == $row->I_TOUR && (1 == $FL_DEMMARAGE) && (ORDRE_MESSAGE_FORUM != $row->I_TYPE))
        // si on ajoute la suite, on cree un bug si fait un mouvement derrière && (ORDRE_MESSAGE_FORUM != $row->I_TYPE))
        // sauf qu'en le remettant je ne reproduis pas le bug, donc, on va voir...
        {
            $s_retour .= "<input alt=\"supprimer\" id=\"id_supprimer_ordre\" name=\"id_supprimer_ordre\" type=\"image\" value=\"submit\" class=\"btn btn-danger\" onclick=\"javascript:callSupprimerOrdre(" . $ID_PION . "," . $row->ID_ORDRE . ");\" src=\"images/btnSupprimer2.png\" />";
        }
        else
        {
            $s_retour .= $I_TOUR - $row->I_TOUR." h";
        }
        $s_retour .= "</div>";
        if ($QG)
        {
            //$s_retour .= "<div class=\"col-12 col-sm-5 col-md-7\">";
            $s_retour .= "<div class=\"col-12 col-sm-7 col-md-8\">";
        }
        else
        {
            //$s_retour .= "<div class=\"col-12 col-sm-9 col-md-10\">";
            $s_retour .= "<div class=\"col-12 col-sm-11\">";
        }
    }
    else
    {
        if ($I_TOUR == $row->I_TOUR && (1 == $FL_DEMMARAGE))
        {
            $s_retour = "<input alt=\"supprimer\" id=\"id_supprimer_ordre\" name=\"id_supprimer_ordre\" type=\"image\" value=\"submit\" class=\"btn btn-danger\" onclick=\"javascript:callSupprimerOrdre(" . $ID_PION . "," . $row->ID_ORDRE . ");\" src=\"images/btnSupprimer2.png\" />";
        }
        else
        {
            $s_retour = "";
        }
    }
    switch ($row->I_TYPE)
    {
        case ORDRE_SE_FORTIFIER:
            $s_retour.= "Construire une fortification";
            break;

        case ORDRE_CONSTRUIRE_PONT:
            $s_retour.= "Construire un pont";
            break;

        case ORDRE_REPARER_PONT:
            $s_retour.= "R&eacute;parer un pont";
            break;

        case ORDRE_ENDOMMAGER_PONT:
            $s_retour.= "Endommager un pont ";
            break;

        case ORDRE_ARRET:
            $s_retour.= "Arr&ecirc;t ";
            break;

        case ORDRE_MOUVEMENT:
            //ordre de mouvement
            $s_retour.= "Ordre d'aller &agrave; ";
            if ($row->I_DISTANCE > 0)
            {
                $s_retour.= $row->I_DISTANCE . " km " . DonnerDirection($db, $row->I_DIRECTION) . " de ";
            }
            if ($QG)
            {
                $s_retour.= DonnerNomCarte($db, $ID_PARTIE, $row->ID_NOM_LIEU);
            }
            else
            {
                $s_retour.= DonnerNomCarte($db, $ID_PARTIE, $row->ID_NOM_LIEU) . " &agrave; " . $row->I_HEURE . "h00 durant " . $row->I_DUREE . " h/jour";
            }
            break;
        case ORDRE_COMBAT:
            //ordre d'engagement au combat
            $s_retour.=  "ORDRE_COMBAT non format&eacute;";
            break;
        case ORDRE_RETRAITE:
            //ordre de retraite au combat
            $s_retour.=  "ORDRE_RETRAITE non format&eacute;";
            break;
        case ORDRE_MESSAGER:
            //ordre d'envoie de message
            //recherche du libelle de la direction$
            if ($row->I_DIRECTION >= 0)
            {
                $valeurDirection = DonnerDirection($db, $row->I_DIRECTION);
            }
            else
            {
                $valeurDirection = "non d&eacute;finie";
            }

            //recherche du destinataire du message
            $nomDestinataire = "inconnu";
            if (TRUE == isset($row->ID_PION_DESTINATION) && TRUE == is_numeric($row->ID_PION_DESTINATION))
            //if (false==empty($row->ID_PION_DESTINATION))
            {
                $requete = "SELECT S_NOM ";
                $requete.="FROM tab_vaoc_pion ";
                $requete.="WHERE ID_PION=" . $row->ID_PION_DESTINATION . " AND ID_PARTIE=" . $ID_PARTIE;
                //echo $requete;
                $res_destinataire = mysql_query($requete, $db);
                if (mysql_num_rows($res_destinataire) > 0)
                {
                    $rowDestinataire = mysql_fetch_object($res_destinataire);
                    $nomDestinataire = $rowDestinataire->S_NOM;
                }
            }

            $s_retour.= "Ordre de transmettre le message suivant &agrave; " . $nomDestinataire;
            $s_retour.= " localis&eacute; &agrave; ";
            if ($row->I_DISTANCE > 0)
            {
                $s_retour.= $row->I_DISTANCE . " km " . $valeurDirection . " de ";
            }
            $s_retour.= DonnerNomCarte($db, $ID_PARTIE, $row->ID_NOM_LIEU) . "<br/>" . " \"" . stripslashes($row->S_MESSAGE) . "\"";
            break;
        case ORDRE_PATROUILLE:
            if ($I_TOUR == $row->I_TOUR && (1 == $FL_DEMMARAGE))
            {
                $I_PATROUILLES_DISPONIBLES--; //une patrouille de moins que l'on peut envoyer
                //echo "I_PATROUILLES_DISPONIBLES=".$I_PATROUILLES_DISPONIBLES;
            }
            $s_retour.= "Envoie d'une patrouille ";
            if (0 == $row->I_DISTANCE)
            {
                $s_retour.= " aller observer &agrave; " . DonnerNomCarte($db, $ID_PARTIE, $row->ID_NOM_LIEU);
            }
            else
            {
                $s_retour.= " aller observer &agrave; " . $row->I_DISTANCE . " km " . DonnerDirection($db, $row->I_DIRECTION) . " de " . DonnerNomCarte($db, $ID_PARTIE, $row->ID_NOM_LIEU);
            }
            break;
        case ORDRE_TRANSFERT:
            //recherche de l'unite transferee et vers qui
            $requete = "SELECT tab_vaoc_pion.S_NOM";
            $requete.=" FROM tab_vaoc_pion, tab_vaoc_ordre";
            $requete.=" WHERE tab_vaoc_pion.ID_PION=tab_vaoc_ordre.ID_PION_DESTINATION AND tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
            $requete.=" AND tab_vaoc_pion.ID_PARTIE=tab_vaoc_ordre.ID_PARTIE AND tab_vaoc_ordre.ID_ORDRE=" . $ID_ORDRE;
            //echo $requete;
            $res_destinataire = mysql_query($requete, $db);
            $row_destinataire = mysql_fetch_object($res_destinataire);

            $requete = "SELECT tab_vaoc_pion.S_NOM";
            $requete.=" FROM tab_vaoc_pion, tab_vaoc_ordre";
            $requete.=" WHERE tab_vaoc_pion.ID_PION=tab_vaoc_ordre.ID_PION_CIBLE AND tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
            $requete.=" AND tab_vaoc_pion.ID_PARTIE=tab_vaoc_ordre.ID_PARTIE AND tab_vaoc_ordre.ID_ORDRE=" . $ID_ORDRE;
            //echo $requete;
            $res_cible = mysql_query($requete, $db);
            $row_cible = mysql_fetch_object($res_cible);

			$s_retour.= "Transfert de la " . $row_cible->S_NOM . " au " . $row_destinataire->S_NOM;
            break;
        case ORDRE_GENERER_CONVOI:
            $s_retour.= "G&eacute;n&eacute;rer un convoi ";
            break;
        case ORDRE_RENFORCER:
            //ordre de renforcement d'une unite
            $requete = "SELECT tab_vaoc_pion.S_NOM";
            $requete.=" FROM tab_vaoc_pion, tab_vaoc_ordre";
            $requete.=" WHERE tab_vaoc_pion.ID_PION=tab_vaoc_ordre.ID_PION_CIBLE AND tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
            $requete.=" AND tab_vaoc_pion.ID_PARTIE=tab_vaoc_ordre.ID_PARTIE AND tab_vaoc_ordre.ID_ORDRE=" . $ID_ORDRE;
            //echo $requete;
            $res_destinataire = mysql_query($requete, $db);
            $row_destinataire = mysql_fetch_object($res_destinataire);

			$s_retour.= "Renforcer&nbsp;".$row_destinataire->S_NOM;
            break;
        case ORDRE_ENGAGEMENT:
            //ordre d'engagement au combat
            $s_retour.=  "ORDRE_ENGAGEMENT non format&eacute;";
            break;
        case ORDRE_RETRAIT:
            //ordre de retrait au combat
            $s_retour.=  "ORDRE_RETRAIT non format&eacute;";
            break;
        case ORDRE_ETABLIRDEPOT:
            //ordre d'etablissement d'un depot a partir d'un convoi
            $s_retour.= "&eacute;tablir un d&eacute;p&ocirc;t ";
            break;
        case ORDRE_MESSAGE_FORUM:
            //ordre d'envoie de message direct
            //recherche du destinataire du message
            $nomDestinataire = "inconnu";
            if (TRUE == isset($row->ID_PION_DESTINATION) && TRUE == is_numeric($row->ID_PION_DESTINATION))
            //if (false==empty($row->ID_PION_DESTINATION))
            {
                $requete = "SELECT S_NOM ";
                $requete.="FROM tab_vaoc_role ";
                $requete.="WHERE ID_PION=" . $row->ID_PION_DESTINATION . " AND ID_PARTIE=" . $ID_PARTIE;
                //echo $requete;
                $res_destinataire = mysql_query($requete, $db);
                if (mysql_num_rows($res_destinataire) > 0)
                {
                    $rowDestinataire = mysql_fetch_object($res_destinataire);
                    $nomDestinataire = $rowDestinataire->S_NOM;
                }
            }

            $s_retour.= "Vous dites la phrase suivante &agrave; " . $nomDestinataire;
            $s_retour.= "<br/>" . " \"" . stripslashes($row->S_MESSAGE) . "\"";
            break;
        case ORDRE_LIGNE_RAVITAILLEMENT:
            //ordre de generation d'un convoi automatique toute les 24 heures vers une destination
            $s_retour.= "Ordre d'envoyer un convoi tous les jours &agrave; ";
            if ($row->I_DISTANCE > 0)
            {
                $s_retour.= $row->I_DISTANCE . " km " . DonnerDirection($db, $row->I_DIRECTION) . " de ";
            }
            $s_retour.= DonnerNomCarte($db, $ID_PARTIE, $row->ID_NOM_LIEU);
            break;
        case ORDRE_REDUIRE_DEPOT:
            $s_retour.= "R&eacute;duire le dep&ocirc;t ";
            break;
        case ORDRE_RAVITAILLEMENT_DIRECT:
            $s_retour.= "Se ravitailler directement sur le dep&ocirc;t ";
            break;
        default:
            $s_retour .= "phrase non format&eacute;e : DecrireOrdre";
            break;
        }
        /*
	if ($bOrdrePrimaire)
	{
		$s_retour .= "</div>";
	}
         */
    echo $s_retour;
    echo "\r\n";
    Debug_Logger::getInstance()->time_mesure_end('DecrireOrdre');
    return $row->ID_ORDRE_SUIVANT;
}

function ListeOrdresRecursifs($db, $ID_PARTIE, $ID_PION, $destinataire)
{
    Debug_Logger::getInstance()->time_mesure_start('ListeOrdresRecursifs');
    $rows = array();
    $i = 0;
    $requete = "SELECT ID_ORDRE, ID_ORDRE_SUIVANT, I_TYPE, I_DISTANCE, I_DIRECTION, ID_NOM_LIEU, I_HEURE, I_DUREE, S_MESSAGE, ";
    $requete.=" I_TOUR, ID_PION_DESTINATION, S_NOM";
    $requete.=" FROM tab_vaoc_ordre, tab_vaoc_pion";
    $requete.=" WHERE tab_vaoc_ordre.ID_PION=" . $ID_PION . " AND tab_vaoc_ordre.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_pion.ID_PION=tab_vaoc_ordre.ID_PION AND tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
    if ($destinataire>=0)
    {
        $requete.=" AND tab_vaoc_ordre.ID_PION_DESTINATION=" . $destinataire;
    }

    //$requete .= ($id_pere == null) ? ' is null' : ' = '.$id_pere;
    $res_ordre = mysql_query($requete, $db);
    while ($row = $res_ordre->fetchRow(DB_FETCHMODE_ASSOC)) 
    {
        $rows[$i++] = $row;
    }

    $requeteRemplace = "SELECT ID_REMPLACE ";
    $requeteRemplace.=" FROM tab_vaoc_pion";
    $requeteRemplace.=" WHERE tab_vaoc_pion.ID_PION=" . $ID_PION . " AND tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
    $res_remplace = mysql_query($requeteRemplace, $db);
    $row_remplace = $res_remplace->fetchRow();
    if ($row_remplace->ID_REMPLACE>=0)
    {
        $rows[$i]['remplace'] = ListeOrdresRecursifs($db, $ID_PARTIE, $row_remplace->ID_REMPLACE, $destinataire);
    }
    $res_ordre->free();
 
    Debug_Logger::getInstance()->time_mesure_end('ListeOrdresRecursifs');
    return $rows;
}

function listeRemplacantsOrdreSQL($db, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('listeRemplacantsOrdreSQL');
    $chaineResultat="";
    $requeteRemplace = "SELECT ID_PION_REMPLACE ";
    $requeteRemplace.=" FROM tab_vaoc_pion";
    $requeteRemplace.=" WHERE tab_vaoc_pion.ID_PION=" . $ID_PION . " AND tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
    //echo $requeteRemplace;
    $res_remplace = mysql_query($requeteRemplace, $db);
    $row_remplace = mysql_fetch_object($res_remplace);
    if ($row_remplace->ID_PION_REMPLACE>=0)
    {
        $chaineResultat = " OR tab_vaoc_ordre.ID_PION=".$row_remplace->ID_PION_REMPLACE.listeRemplacantsOrdreSQL($db, $ID_PARTIE, $row_remplace->ID_PION_REMPLACE);
    }
    Debug_Logger::getInstance()->time_mesure_end('listeRemplacantsOrdreSQL');
    return $chaineResultat;
}

function AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, $QG, &$I_PATROUILLES_DISPONIBLES, $pageNum, $destinataire)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherOrdre');
    //note : tous les ordres ont une destination, sauf les ordres de combat et, cela tombe bien, on ne veut pas les afficher 
    //(en fait on voit quand meme les autres parce que je leur met une destination de valeur 0, ce qui est faux mais comme on ne l'affiche pas...)
    //sauf que pas de bol, on veut afficher les ordres de destruction de pont, de transfert, etc...
    //finalement, pourquoi ne pas afficher aussi les ordres de combat ? ->Parce que ce n'est pas forcement le proprietaire qui les donne et qu'il les verrait !
    //pour etre propre, il faudra clairement dire qu'elles ordres on ne veut pas afficher et pour les autres mettre les destinations a -1
    //echo "ID_PION=".$ID_PION."<BR/>";
    Debug_Logger::getInstance()->time_mesure_start('AfficherOrdre : requete 1');
    $requete = "SELECT o.ID_ORDRE, o.ID_ORDRE_SUIVANT, o.I_TYPE, o.I_DISTANCE, o.I_DIRECTION, o.ID_NOM_LIEU, o.I_HEURE, o.I_DUREE, o.S_MESSAGE, ";
    $requete.=" o.I_TOUR, o.ID_PION_DESTINATION, p.S_NOM";
    //$requete.=" o.ID_ORDRE_SUIVANT, o.I_TYPE, o.I_DISTANCE, o.I_DIRECTION, o.ID_NOM_LIEU, o.ID_PION_DESTINATION";
    $requete.=" FROM tab_vaoc_ordre o, tab_vaoc_pion p";
    $requete.=" WHERE o.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND (o.ID_PION=" . $ID_PION . listeRemplacantsOrdreSQL($db, $ID_PARTIE, $ID_PION).")";
    $requete.=" AND p.ID_PION=o.ID_PION AND p.ID_PARTIE=o.ID_PARTIE";
    if ($destinataire>=0)
    {
        $requete.=" AND o.ID_PION_DESTINATION=" . $destinataire;
    }

    //on ne veut pas des ordres de combat ou de retraite
    $requete.=" AND o.I_TYPE<>" . ORDRE_COMBAT . " AND o.I_TYPE<>" . ORDRE_RETRAITE;
    $requete.=" AND o.I_TYPE<>" . ORDRE_RETRAIT . " AND o.I_TYPE<>" . ORDRE_ENGAGEMENT;
    //on ne veut que les ordres qui ne sont pas les suivants d'un autre
    $requete.=" AND o.ID_ORDRE not in";
    $requete.="(SELECT ID_ORDRE_SUIVANT from tab_vaoc_ordre WHERE ID_PION=" . $ID_PION . " AND tab_vaoc_ordre.ID_PARTIE=" . $ID_PARTIE . ")";
    $res_ordreNb = mysql_query($requete, $db);
    //echo $requete;
    $nb_messages_ordres = mysql_num_rows($res_ordreNb);
    //echo "<span>pageNum=".$pageNum."</span>";
    $offset = ($pageNum - 1) * NB_MESSAGES_MAX;
    if ($offset < 0)
    {
        $offset = 0;
    }

    $requete.=" ORDER BY o.I_TOUR DESC, o.ID_ORDRE DESC LIMIT " . $offset . "," . NB_MESSAGES_MAX;
    echo $requete;
    $res_ordre = mysql_query($requete, $db);
    Debug_Logger::getInstance()->time_mesure_end('AfficherOrdre : requete 1');

    if (false==$QG)
    {
        echo "<div class=\"row\">";
             echo "<div class=\"col-12 col-sm-1\" align=\"left\">";
                 echo "<badge class=\"control-badge\">Depuis</badge>";
             echo "</div>";
             echo "<div class=\"col-12 col-sm-11\">";
                 echo "<badge class=\"control-badge\">Ordre</badge>";
             echo "</div>";
         echo "</div>";
    }
    
    if (mysql_num_rows($res_ordre) <= 0)
    {
        echo "<div class=\"row\">";
        echo "<div class=\"col-12 col-sm-4 col-md-3\">Aucun ordre transmis</div>";      
        echo "</div>\r\n";
    }

    while ($row = mysql_fetch_object($res_ordre))
    {
        //echo "ordre=".$row->ID_ORDRE;
        echo "<hr style=\"border-top: 1px solid white; width:100%\" />";
        echo "<div class=\"row\">";
        if ($QG)
        {
            echo "<div class=\"col-12 col-sm-4 col-md-3\">";
            //recherche du nom du destinataire
            $requete = "SELECT S_NOM";
            $requete.=" FROM tab_vaoc_pion";
            $requete.=" WHERE ID_PARTIE=" . $ID_PARTIE;
            $requete.=" AND ID_PION=" . $row->ID_PION_DESTINATION;
            //echo $requete;
            $res_nom = mysql_query($requete, $db);
            $row_nom = mysql_fetch_object($res_nom);
            echo $row_nom->S_NOM;
            echo "</div>";
        }

        DecrireOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, $QG, $row->ID_ORDRE, true, $I_PATROUILLES_DISPONIBLE);
        if ($row->I_TYPE == ORDRE_PATROUILLE && $I_TOUR == $row->I_TOUR && (1 == $FL_DEMMARAGE))
        {
            $I_PATROUILLES_DISPONIBLES--; //une patrouille de moins que l'on peut envoyer
        }
        $id_ordre_suivant = $row->ID_ORDRE_SUIVANT;
        while ($id_ordre_suivant >= 0)
        {
                echo " et puis "; //.$id_ordre_suivant;
                $id_ordre_suivant = DecrireOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, $QG, $id_ordre_suivant, false, $I_PATROUILLES_DISPONIBLE);
                //echo "fin et puis ".$id_ordre_suivant;
        }
        echo "</div>";//le <div> est cree dans DecrireOrdre
        echo "</div>";
    }//while

    //gestion du multi page 
    $maxPage = ceil($nb_messages_ordres / NB_MESSAGES_MAX);

    if ($maxPage > 1)
    {
        echo "<div class=\"row\">";
        echo "<div class=\"col-12\">";
        echo "<nav>";
        echo "<ul class=\"pagination\">";
        echo "<li class=\"page-item\">";
            echo "<a class=\"page-link\" href=\"#\" aria-badge=\"Premier\" onclick=\"javascript:callAllerALapage(" . $ID_PION .",". max(1,$pageNum-NB_PAGINATION_MAX) .");return false;\">";
                echo "<span aria-hidden=\"true\">&laquo;</span>";
                echo "<span class=\"sr-only\">Precedent</span>";
            echo "</a>";
        echo "</li>";
        $fin_pagination = $pageNum + min(NB_PAGINATION_MAX,$maxPage);
        for ($page = $pageNum; $page < $fin_pagination; $page++)
        {
            if ($page==$pageNum)
            {
                echo "<li class=\"page-item active\"><span class=\"page-link\"><strong>" . $page . "</strong><span class=\"sr-only\" aria-hidden=\"true\">(courant)</span></span></li>";
            }
            else
            {
                echo "<li class=\"page-item\"><a class=\"page-link\" href=\"#\" onclick=\"javascript:callAllerALapage(" . $ID_PION . "," . $page . ");return false;\">" . $page . "</a></li>";
            }
        }
        echo "<li class=\"page-item\">";
            echo "<a class=\"page-link\" href=\"#\" aria-badge=\"Dernier\" onclick=\"javascript:callAllerALapage(" . $ID_PION . "," . min($maxPage,$pageNum+NB_PAGINATION_MAX) . ");return false;\">";
            echo "<span aria-hidden=\"true\">&raquo;</span>";
            echo "<span class=\"sr-only\">Suivant</span>";
            echo "</a>";
        echo "</li>";
        echo "</ul>";
        echo "</nav>";
        echo "</div>";
        echo "</div>";
        echo "<hr style=\"border-top: 2px dashed white; margin-top:0px; \" />";

        $id = "pageNum_" . $ID_PION;
        echo "<input id='$id' name='$id' type='hidden' value='0' />";
    }
    echo "\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherOrdre');
}

function AfficherDistance($db, $FL_ACTIF, $id_chaine)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDistance');
    printf("<select class=\"custom-select\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
    if (0 == $FL_ACTIF)
    {
        echo " disabled ";
    }
    echo ">";
    for ($i = 0; $i < 50; $i++)
    {
        echo "<option";
        if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine] == $i)
        {
            echo " selected=\"selected\"";
        }
        printf(" value=\"%u\">%s</option>", $i, $i);
    }
    echo "</select>";
    echo "\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDistance');
}

function AfficherDirection($db, $FL_ACTIF, $id_chaine)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDirection');
    $requete = "SELECT ID_PARAMETRE, S_VALEUR, I_VALEUR";
    $requete.=" FROM tab_vaoc_parametre";
    $requete.=" WHERE S_TYPE='direction'";
    $requete.=" ORDER BY I_VALEUR";
    $res_direction = mysql_query($requete, $db);
    printf("<select class=\"custom-select\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
    if (0 == $FL_ACTIF)
    {
        echo " disabled ";
    }
    echo ">";
    while ($row = mysql_fetch_object($res_direction))
    {
        echo "<option";
        if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine] == $row->I_VALEUR)
        {
            echo " selected=\"selected\"";
        }
        printf(" value=\"%u\">%s</option>", $row->I_VALEUR, $row->S_VALEUR);
    }
    echo "</select>";
    echo "\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDirection');
}

function AfficherDestination($db, $FL_ACTIF, $id_chaine, $id_partie)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDestination');
    $requete = "SELECT ID_NOM, S_NOM";
    $requete.=" FROM tab_vaoc_noms_carte";
    $requete.=" WHERE ID_PARTIE=" . $id_partie;
    //$requete.=" AND B_PONT=0";
    $requete.=" ORDER BY S_NOM";
    //echo $requete;
    //$res_noms = mysql_query($requete, $db);
    //printf("<select class=\"selectpicker\"  id=\"%s\" name=\"%s\" size=1 onfocus='this.size=10;' onblur='this.size=1;' onchange='this.size=1; this.blur();'", $id_chaine, $id_chaine);
    // version liste deroulante
    /*
    $valeur_defaut="";
    echo "<div class=\"dropdown\">";
    //echo "<button class=\"btn btn-light bouton form-control dropdown-toggle\" type=\"button\"  id=\"".$id_chaine."\" name=\"".$id_chaine."\">Destination";
    echo "<button class=\"btn btn-light bouton form-control dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"".$id_chaine."\" name=\"".$id_chaine."\"";
    if (0 == $FL_ACTIF)
    {
        echo " disabled ";
    }
    echo ">";
    if (isset($_REQUEST["sel_" . $id_chaine]) && TRUE==$_REQUEST["sel_" . $id_chaine])
    {
        $valeur_defaut = $_REQUEST["sel_" . $id_chaine];
        echo $valeur_defaut;
    }
    else
    {
        echo "Destination";
    }
    echo "<span class=\"caret\"></span></button>";
    echo "<ul class=\"dropdown-menu scrollable-menu\" id=\"ul".$id_chaine."\" name=\"ul".$id_chaine."\">";
    //echo "<li><a href=\"#\">HTML</a></li>";
    //echo "<li><a href=\"#\">CSS</a></li>";
    echo "</ul></div>";
    printf("<input type=\"hidden\" id=\"sel_%s\" name=\"sel_%s\" value=\"%s\" />", $id_chaine, $id_chaine, $valeur_defaut);
    */

/**/
    printf("<select class=\"custom-select\"  id=\"%s\" name=\"%s\" ", $id_chaine, $id_chaine);
    if (0 == $FL_ACTIF)
    {
        echo " disabled ";
    }
    echo ">";
    if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine]>0)
    {
        $requete = "SELECT ID_NOM, S_NOM";
        $requete.=" FROM tab_vaoc_noms_carte";
        $requete.=" WHERE ID_PARTIE=" . $id_partie . " AND ID_NOM=" . $_REQUEST[$id_chaine];
        //echo $requete;
        $res_nom = mysql_query($requete, $db);
        $row = mysql_fetch_object($res_nom);
        printf("<option value=\"%u\">%s</option>", $row->ID_NOM, $row->S_NOM);
    }
    else
    {
        echo "<option value=\"-1\">Choisir destination...</option>";
    }
    echo "</select>";
                       
    /**************************** Version qui marche sur le site actuel
    printf("<select class=\"form-control\"  id=\"%s\" name=\"%s\" ", $id_chaine, $id_chaine);
    //printf("<select class=\"selectpicker\"  id=\"%s\" name=\"%s\" onclick=\"javascript:alert('test');return false;\"", $id_chaine, $id_chaine);
    if (0 == $FL_ACTIF)
    {
        echo " disabled ";
    }
    echo ">";
    if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine]>0)
    {
        $requete = "SELECT ID_NOM, S_NOM";
        $requete.=" FROM tab_vaoc_noms_carte";
        $requete.=" WHERE ID_PARTIE=" . $id_partie . " AND ID_NOM=" . $_REQUEST[$id_chaine];
        //echo $requete;
        $res_nom = mysql_query($requete, $db);
        $row = mysql_fetch_object($res_nom);
        printf("<option value=\"%u\">%s</option>", $row->ID_NOM, $row->S_NOM);
    }
    else
    {
        echo "<option value=\"-1\">Choisir destination...</option>";
    }
    echo "</select>";
     ************************************/
    
    //Version intellisense --> cela ne va pas, je ne peux pas autoriser la saisie et limiter cette "saisie" au contenu d'une liste
    //echo "<input type=\"text\" class=\"states_search form-control\" autocomplete=\"off\" spellcheck=\"false\" placeholder=\"destination\" id=\"ul".$id_chaine."\" name=\"ul".$id_chaine."\" >";
    /*
    if (0 == $FL_ACTIF)
    {
        echo " disabled ";
    }
    //echo ">";
    if (FALSE == empty($_REQUEST[$id_chaine])) 
    {        
        $requete = "SELECT ID_NOM, S_NOM";
        $requete.=" FROM tab_vaoc_noms_carte";
        $requete.=" WHERE ID_PARTIE=" . $id_partie . " AND ID_NOM=" . $_REQUEST[$id_chaine];
        //echo $requete;
        $res_nom = mysql_query($requete, $db);
        $row = mysql_fetch_object($res_nom);
        printf("<option value=\"%u\">%s</option>", $row->ID_NOM, $row->S_NOM);
    }
    else
    {
        echo "<option>Choisir une destination</option>";;
    }
     */
    /*
    while ($row = mysql_fetch_object($res_noms))
    {
        echo "<option";
        if (FALSE == empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine] == $row->ID_NOM)
        {
            echo " selected=\"selected\"";
        }
        printf(" value=\"%u\">%s</option>", $row->ID_NOM, $row->S_NOM);
    }
    echo "</select>";
    */ 
    echo "\r\n";
    /*
    $nom_bool = "b" . $id_chaine;
    $nom_ul = $id_chaine;
    echo "<!-- nombool=" . $nom_bool . "-->";
     */
    global $chargement_destination_script;    
    $chargement_destination_script .= "$('#". $id_chaine ."').click($.fn.chargeNomsCarte);"; 
    /*
    $chargement_destination_script .= "var " . $nom_bool . " = true; 
        $(\"#" . $id_chaine . "\").click(function () {
        if (" . $nom_bool . ") {
        $(\"#ul" . $nom_ul . "\").load(\"listenomscarte.php?partie=".$id_partie."\");"
        . $nom_bool . " = false;
        }});";
    */
    //echo "<!-- chargement_destination_script=" . $chargement_destination_script . "-->";     
    Debug_Logger::getInstance()->time_mesure_end('AfficherDestination');
}

function AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, $SANS_DUREE)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeOrdreMouvement');
    echo "<div class=\"form-inline\">";
    echo "<button class=\"btn btn-light bouton\" alt=\"ordre de mouvement\" id=\"id_mouvement\" name=\"id_mouvement\" type=\"button\" value=\"submit\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_allera');\" ";

    if (0 == $FL_ACTIF)
    {
        //ordres interdits
        echo " disabled ";
    }
    echo ">Aller &agrave;</button>";

    //choix d'une distance
    echo "&nbsp;<div class=\"input-group\">";
        echo "<div class=\"input-group-prepend\">";
        $id_chaine = "id_distance_mouvement_" . $ID_PION;
        AfficherDistance($db, $FL_ACTIF, $id_chaine);
        echo "</div>";

        echo "<span class=\"input-group-text\">&nbsp;km&nbsp;</span>";

        //choix d'une direction
        echo "<div class=\"input-group-append\">";
        $id_chaine = "id_direction_mouvement_" . $ID_PION;
        AfficherDirection($db, $FL_ACTIF, $id_chaine);
        echo "</div>";
    echo "</div>";

    //choix d'une destination
    echo "&nbsp;<div class=\"input-group\">";
	echo "<div class=\"input-group-prepend\"><span class=\"input-group-text\">&nbsp;de&nbsp;</span></div>";
        $id_chaine = "id_destination_mouvement_" . $ID_PION;
        AfficherDestination($db, $FL_ACTIF, $id_chaine, $ID_PARTIE);
    echo "</div>";

    $id_chaine_heure = "id_heure_" . $ID_PION;
    $id_chaine_duree = "id_duree_" . $ID_PION;
    if ($SANS_DUREE)
    {
        printf("<input id=\"%s\" name=\"%s\" type='hidden' value=\"0\" />", $id_chaine_heure, $id_chaine_heure);
        printf("<input id=\"%s\" name=\"%s\" type='hidden' value=\"24\" />", $id_chaine_duree, $id_chaine_duree);
    }
    else
    {
        //choix de l'heure de depart
        Debug_Logger::getInstance()->time_mesure_start('AfficherDemandeOrdreMouvement : choix heure');
        echo "&nbsp;<div class=\"input-group\">";
            echo "<div class=\"input-group-prepend\"><span class=\"input-group-text\">&nbsp;A&nbsp;</span></div>";
            $requete = "SELECT ID_PARAMETRE, S_VALEUR, I_VALEUR";
            $requete.=" FROM tab_vaoc_parametre";
            $requete.=" WHERE S_TYPE='heure'";
            $requete.=" ORDER BY S_VALEUR";
            //echo $requete;
            $res_heure = mysql_query($requete, $db);
            printf("<select class=\"custom-select\" id=\"%s\" name=\"%s\" size=1", $id_chaine_heure, $id_chaine_heure);
            if (0 == $FL_ACTIF)
            {
                echo " disabled ";
            }
            echo ">";
            while ($row = mysql_fetch_object($res_heure))
            {
                echo "<option";
                if (FALSE == empty($_REQUEST[$id_chaine_heure]) && $_REQUEST[$id_chaine_heure] == $row->I_VALEUR)
                {
                    echo " selected=\"selected\"";
                }
                printf(" value=\"%u\">%s</option>", $row->I_VALEUR, $row->S_VALEUR);
            }
            echo "</select>";
        echo "</div>";
        Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeOrdreMouvement : choix heure');
        //choix de la duree
        echo "&nbsp;<div class=\"input-group\">";
            echo "<div class=\"input-group-append\"><span class=\"input-group-text\">&nbsp;durant&nbsp;</span></div>";
            printf("<select class=\"custom-select\" id=\"%s\" name=\"%s\" size=1", $id_chaine_duree, $id_chaine_duree);
            if (0 == $FL_ACTIF)
            {
                echo " disabled ";
            }
            echo ">";
            //pas plus de 20 heures de marche par jour d'apres les regles
            for ($i = 1; $i < 21; $i++)
            {
                echo "<option";
                if (FALSE == empty($_REQUEST[$id_chaine_duree]) && $_REQUEST[$id_chaine_duree] == $i)
                {
                    echo " selected=\"selected\"";
                }
                printf(" value=\"%u\">%s</option>", $i, $i);
            }
            echo "</select>";
            echo "<div class=\"input-group-prepend\"><span class=\"input-group-text\">&nbsp;heures/jour.</span></div>";
        echo "</div>";
    }
    echo "</div>";
    echo "\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDemandeOrdreMouvement');
}

function AfficherArtillerie($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $ID_MODELE_PION, $B_DETRUIT, $I_FATIGUE, $I_EXPERIENCE, $I_ARTILLERIE, $I_ARTILLERIE_INITIALE, $S_POSITION, $ID_BATAILLE, $I_TOUR, $DATE_DERNIER_MESSAGE, $pageNum)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherArtillerie');
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">\r\n";
	echo "<div class=\"col-md-3\">";
            echo "<div align=\"left\" class=\"d-none d-md-block\">";
		//Grand ecran, on peut afficher une image
		if (0 == $B_DETRUIT)
		{
			echo "<img alt='unite' title=\"unite\" width=\"200\"  src='images/" . ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, "S_IMAGE_ARTILLERIE") . "'/>";
		}
		else
		{
			echo "<img alt='unite detruite' title='unite detruite' width=\"200\"  src='images/rip.jpg'/>";
		}
            echo "</div>";
            if (0 == $B_DETRUIT)
            {
                echo "<div align=\"left\">";
                if ($ID_BATAILLE >= 0)
                {
                        AfficherBataille($db, $ID_PARTIE, $ID_BATAILLE, -1, $ID_PION_PROPRIETAIRE, $ID_PION, 1);
                }
                else
                {
                        AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, -1);
                }
                echo "</div>";
            }

            echo "<div align=\"left\">". $DATE_DERNIER_MESSAGE. "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";
        echo "</div>";
        echo "<div class=\"col-12 col-md-9\">";
            echo "<div class=\"row\">";
		echo "<div class=\"col-12\">";
                if (0 == $B_DETRUIT)
                {
                    echo "<h2>" . $S_NOM . "</h2>";
                    echo "<h5>".$S_ORDRE_COURANT."</h5>";
                }
                else
                {
                    echo "<h2><strike>" . $S_NOM . "</strike></h2>";                    
                }
		echo "</div>";
            echo "</div>";
            echo "<div align=\"left\" class=\"d-md-none\">". $DATE_DERNIER_MESSAGE ."</div>";;
            echo "<div align=\"left\" class=\"d-md-none\">". $S_POSITION. "</div>";;

            echo "<div class=\"row\">";
		echo "<div class=\"col-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Artillerie\" title=\"Artillerie\" src=\"images/artillerie-26.png\" />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". round($I_ARTILLERIE * (100 - $I_FATIGUE) / 100) . "/" . $I_ARTILLERIE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Fatigue\" title=\"Fatigue\" src='images/fatigue-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". $I_FATIGUE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Exp&eacute;rience\" title=\"Exp&eacute;rience\" src='images/experience-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". $I_EXPERIENCE ."</span>";
		echo "</h4>";
		echo "</div>";
		
            echo "</div>";
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, false, $I_PATROUILLES_DISPONIBLES, $pageNum, -1);
	
            //ordres disponibles
            if (0 == $B_DETRUIT)
            {
                if ($ID_BATAILLE >= 0)
                {
                        $FL_ACTIF = 0;
                }
                else
                {
                        $FL_ACTIF = $FL_DEMMARAGE;
                }

                echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";

                echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                AfficherDemandeEndommagerPont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                echo "</div>";
                echo "</div>";
            }
	echo "</div>";
    echo "</div>";
    Debug_Logger::getInstance()->time_mesure_end('AfficherArtillerie');
}

function AfficherDivision($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $ID_MODELE_PION, $B_DETRUIT, $I_MORAL, $I_MORAL_MAX, $I_FATIGUE, $I_INFANTERIE, $I_INFANTERIE_INITIALE, $I_CAVALERIE, $I_CAVALERIE_INITIALE, $I_ARTILLERIE, $I_ARTILLERIE_INITIALE, $I_EXPERIENCE, $B_CAVALERIE_DE_LIGNE, $B_CAVALERIE_LOURDE, $B_GARDE, $B_VIEILLE_GARDE, $I_MATERIEL, $I_RAVITAILLEMENT, $S_POSITION, $ID_BATAILLE, $I_TOUR, $I_PATROUILLES_DISPONIBLES, $I_PATROUILLES_MAX, $I_NIVEAU_FORTIFICATION, $DATE_DERNIER_MESSAGE, $pageNum)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDivision');
    //echo "AfficherDivision I_PATROUILLES_DISPONIBLES=".$I_PATROUILLES_DISPONIBLES;
    if (0 == $B_DETRUIT)
    {
        //echo "division";uniteFortifcation1
        if ($I_CAVALERIE > 0 && $I_INFANTERIE == 0)
        {
            $NOM_IMAGE = "S_IMAGE_CAVALERIE";
        }
        else
        {
            $NOM_IMAGE = "S_IMAGE_INFANTERIE";
        }
    }
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">";
	echo "<div class=\"col-md-3\">";
            //echo "I_NIVEAU_FORTIFICATION=".$I_NIVEAU_FORTIFICATION;
            if (2 == $I_NIVEAU_FORTIFICATION && 0 == $B_DETRUIT)
            {
                echo "<div align=\"left\" class=\"d-none d-md-block uniteFortifcationMax\" title=\"unite tres fortifiee\">";
                $infobulle="unite tres fortifiee";
            }
            else
            {
                echo "<div align=\"left\" class=\"d-none d-md-block\">";
                $infobulle="unite non fortifiee";
            }
            if (0 == $B_DETRUIT)
            {
                if (1 == $I_NIVEAU_FORTIFICATION)
                {
                    echo "<img alt='unite' title=\"unite fortifiee\" width=\"200\"  class=\"uniteFortifcation1\" src='images/" . ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, $NOM_IMAGE) . "'/>";
                }
                else
                {
                    echo "<img alt='unite' title=\"" . $infobulle . "\" width=\"200\"  src='images/" . ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, $NOM_IMAGE) . "'/>";
                }
            }
            else
            {
                echo "<img alt='unite detruite' title='unite detruite' width=\"200\"  src='images/rip.jpg'/>";
            }
            echo "</div>";
            if (0 == $B_DETRUIT)
            {
                echo "<div align=\"left\">";
                if ($ID_BATAILLE >= 0)
                {
                    AfficherBataille($db, $ID_PARTIE, $ID_BATAILLE, -1, $ID_PION_PROPRIETAIRE, $ID_PION, 1);
                }
                else
                {
                    AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, -1);
                }
                echo "</div>";
            }

            //echo "<div align=\"left\">". $DATE_DERNIER_MESSAGE." : ". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE) . "</div>";
            echo "<div align=\"left\">". $DATE_DERNIER_MESSAGE . "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";		
	echo "</div>";
	
	echo "<div class=\"col-12 col-md-9\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                if (0 == $B_DETRUIT)
                {
                    echo "<h2>" . $S_NOM . "</h2>";
                    echo "<h5>".$S_ORDRE_COURANT."</h5>";
                }
                else
                {
                    echo "<h2><strike>" . $S_NOM . "</strike></h2>";
                }
                echo "</div>";
            echo "</div>";
            echo "<div align=\"left\" class=\"d-md-none\">". $DATE_DERNIER_MESSAGE."</div>";;
            echo "<div align=\"left\" class=\"d-md-none\">". $S_POSITION. "</div>";;

            echo "<div class=\"row\">";
		echo "<div class=\"col-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Infanterie\" title=\"Infanterie\" src='images/infanterie-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". round($I_INFANTERIE * (100 - $I_FATIGUE) / 100) . "/" . $I_INFANTERIE ."</span>";
		echo "</h4>";
                echo "</div>";

		echo "<div class=\"col-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Cavalerie\" title=\"Cavalerie\" src='images/cavalerie-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". round($I_CAVALERIE * (100 - $I_FATIGUE) / 100) . "/" . $I_CAVALERIE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-12 col-sm-4 col-md-2\">";
		echo "<h4>";
		echo "<img alt=\"Artillerie\" title=\"Artillerie\" src='images/artillerie-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". round($I_ARTILLERIE * (100 - $I_FATIGUE) / 100) . "/" . $I_ARTILLERIE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-12 col-sm-4 col-md-2\">";
		echo "<h4>";
		echo "<img alt=\"Fatigue\" title=\"Fatigue\" src='images/fatigue-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". $I_FATIGUE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Exp&eacute;rience\" title=\"Exp&eacute;rience\" src='images/experience-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". $I_EXPERIENCE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Mat&eacute;riel\" title=\"Mat&eacute;riel\" src='images/materiel-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". $I_MATERIEL . "</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-12 col-sm-4 col-md-2\">";
		echo "<h4>";
		echo "<img alt=\"Ravitaillement\" title=\"Ravitaillement\" src='images/ravitaillement-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">". $I_RAVITAILLEMENT ."</span>";
		echo "</h4>";
		echo "</div>";

		echo "<div class=\"col-12 col-sm-4 col-md-2\">";
		echo "<h4>";
		echo "<img alt=\"Moral\" title=\"Moral\" src='images/moral-26.png' />";
		echo "&nbsp;<span class=\"badge badge-secondary\">".  $I_MORAL . "/" . $I_MORAL_MAX ."</span>";
		echo "</h4>";
		echo "</div>";
            echo "</div>";
            //derniers ordres
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, false, $I_PATROUILLES_DISPONIBLES, $pageNum, -1);

            //ordres disponibles
            //echo "B_DETRUIT=".$B_DETRUIT;
            if (0 == $B_DETRUIT)
            {
                if ($ID_BATAILLE >= 0)
                {
                    $FL_ACTIF = 0;
                }
                else
                {
                    $FL_ACTIF = $FL_DEMMARAGE;
                }

                echo "<div class=\"row\">\r\n";
                echo "<div class=\"col-12\">\r\n";
                AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";

                echo "<div class=\"row\">\r\n";
                echo "<div class=\"col-12\">\r\n";
                AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";

                echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                AfficherPatrouilles($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, $I_PATROUILLES_DISPONIBLES, $I_PATROUILLES_MAX);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";

                echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                AfficherDemandeConstruireFortifcation($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                AfficherDemandeEndommagerPont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                AfficherDemandeRavitaillementDirect($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                echo "</div>";
                echo "</div>";
            }
        echo "</div>";//echo "<div class=\"col-12 col-md-9\">";
    echo "</div>";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDivision');
}

function AfficherDepot($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $C_NIVEAU_DEPOT, $I_SOLDATS_RAVITAILLES, $ID_MODELE_PION, $B_DETRUIT, $S_POSITION, $I_TOUR, $DATE_DERNIER_MESSAGE, $pageNum)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherDepot');
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">";
        echo "<div class=\"col-md-3\">";
            echo "<div align=\"left\" class=\"d-none d-md-block\">";
                //Grand ecran, on peut afficher une image
                if (0 == $B_DETRUIT)
                {
                    echo "<img alt='unite' title=\"unite\" width=\"200\"  src='images/" . ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, "S_IMAGE_DEPOT") . "'/>";
                }
                else
                {
                    echo "<img alt='unite detruite' title='unite detruite' width=\"200\"  src='images/rip.jpg'/>";
                }
            echo "</div>";
            if (0 == $B_DETRUIT)
            {
                echo "<div align=\"left\">";
                AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, -1);
                echo "</div>";
            }

            echo "<div align=\"left\">". $DATE_DERNIER_MESSAGE. "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";
        echo "</div>";
	
	echo "<div class=\"col-12 col-md-9\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                if (0 == $B_DETRUIT)
                {
                    echo "<h2>" . $S_NOM . "</h2>";
                    echo "<h5>".$S_ORDRE_COURANT."</h5>";
                }
                else
                {
                    echo "<h2><strike>" . $S_NOM . "</strike></h2>";
                }
                echo "</div>";
            echo "</div>";
            echo "<div align=\"left\" class=\"d-md-none\">". $DATE_DERNIER_MESSAGE."</div>";;
            echo "<div align=\"left\" class=\"d-md-none\">". $S_POSITION. "</div>";;

            echo "<div class=\"row\">";
                echo "<div class=\"col-12 col-sm-6\">";
                    echo "<h4>";
                    echo "<img alt=\"Niveau\" title=\"Niveau\" src='images/rang-26.png' />";
                    echo "&nbsp;<span class=\"badge badge-secondary\">". $C_NIVEAU_DEPOT ."</span>";
                    echo "</h4>";
                echo "</div>";
		
                echo "<div class=\"col-12 col-sm-6\">";
                    echo "<h4>";
                    echo "<img alt=\"Ravitaillement direct\" title=\"Ravitaillement direct\" src='images/ravitaillement-26.png' />";
                    echo "&nbsp;<span class=\"badge badge-secondary\">";
                    echo  $I_SOLDATS_RAVITAILLES;
                    switch ($C_NIVEAU_DEPOT)
                    {
                            case 'A':
                                    echo "/50000";
                                    break;
                            case 'B':
                                    echo "/40000";
                                    break;
                            case 'C':
                                    echo "/30000";
                                    break;
                            default:
                                    echo "/20000";
                                    break;
                    }
                    echo "</span>";
                    echo "</h4>";
                echo "</div>";
            echo "</div>";
		
            $I_PATROUILLES_DISPONIBLES = 0;
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, false, $I_PATROUILLES_DISPONIBLES, $pageNum, -1);

            //ordres disponibles
            if (0 == $B_DETRUIT)
            {
                echo "<div class=\"row\">\r\n";
                    echo "<div class=\"col-12\">\r\n";
                    AfficherDemandeGenererConvoi($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, $C_NIVEAU_DEPOT);
                    echo "</div>";
                echo "</div>";
                if ('A' == $C_NIVEAU_DEPOT)
                {
                    echo "<hr style=\"border-top: 1px solid white;\" />";
                    echo "<div class=\"row\">";
                        echo "<div class=\"col-12\">";
                        AfficherDemandeLigneDeRavitaillement($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION);
                        echo "&nbsp;";
                        AfficherDemandeReduireDepot($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION);
                        echo "</div>";
                    echo "</div>";
                }
            }
	echo "</div>";
    echo "</div>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherDepot');
}

function nomFichierImage($str, $charset = 'utf-8')
{
    Debug_Logger::getInstance()->time_mesure_start('nomFichierImage');
    //echo $str;
    $str = htmlentities($str, ENT_NOQUOTES, $charset);
    //$str = htmlentities($str);
    //echo $str;
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace("#&[^;]+;#", '', $str); // supprime les autres caract�res
    $str = str_replace("'", "_", strtolower($str));

    Debug_Logger::getInstance()->time_mesure_end('nomFichierImage');
    return str_replace(' ', '_', strtolower($str));
  }

function AfficherQG($db, $FL_DEMMARAGE, $ID_UTILISATEUR, $ID_PARTIE, $ID_ROLE, 
                    $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $C_NIVEAU_HIERARCHIQUE, $ID_MODELE_PION, 
                    $I_TACTIQUE, $I_STRATEGIQUE, $S_POSITION, $ID_BATAILLE, $I_TOUR, 
                    $recepteur, $pageNum)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherQG');
    //on recherche l'image associe au modele du general du joueur
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">\r\n";
        echo "<div class=\"col-12 col-sm-12 col-md-12 col-lg-3\">\r\n";
        echo "<div class=\"row\"  style=\"vertical-align:middle\" >\r\n";
            echo "<div class=\"col-12 col-sm-12 col-md-6 col-lg-12 d-none d-md-block\" align=\"center\">\r\n";
                echo "<img alt=\"". nomFichierImage($S_NOM) . ".jpg\" width=\"200\" src=\"images/" . nomFichierImage($S_NOM) . ".jpg\" />";
            echo "</div>";
            echo "<div class=\"col-12 col-sm-12 col-md-6 col-lg-12\" align=\"center\">\r\n";
            if ($ID_BATAILLE >= 0)
            {
                if ($ID_ROLE >= 0)
                {
                    AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $ID_ROLE);
                    echo "<br/>";
                    AfficherBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_ROLE, $ID_PION_PROPRIETAIRE, $ID_PION, 1);
                }
                else
                {
                    AfficherBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_ROLE, $ID_PION_PROPRIETAIRE, $ID_PION, 1);
                }
            }
            else
            {
                AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $ID_ROLE);
            }
            echo "<div align='center'>". $S_POSITION ."</div>";
            echo "</div>";
        //echo "</div>";

            if ($ID_ROLE >= 0)
            {
                $requete = "SELECT B_COMBATTIVES_VISIBLES, S_UNITES_VISIBLES, S_UNITES_COMBATTIVES_VISIBLES ";
                $requete.="FROM tab_vaoc_role ";
                $requete.="WHERE tab_vaoc_role.ID_ROLE=" . $ID_ROLE . " AND tab_vaoc_role.ID_PARTIE=" . $ID_PARTIE;
                $res_unites_visibles = mysql_query($requete, $db);
                $row_unites_visibles = mysql_fetch_object($res_unites_visibles);

                //echo "<hr class=\"col-lg-12 d-none d-lg-block\" style=\"border-top: 2px dashed white; width:90%\" />";
                echo "<hr style=\"border-top: 2px dashed white; width:100%\" />";
                echo "<div class=\"col-12 col-sm-12 col-md-12 col-lg-12 d-none d-sm-block\" align=\"left\">\r\n";
                //on click géré via jquery
                echo "<input type=\"checkbox\" id=\"combattives_visibles\" name=\"combattives_visibles\"";
                if ($row_unites_visibles->B_COMBATTIVES_VISIBLES == 1)
                {
                    echo " checked ";
                }
                echo "><b> Unités seules</b><br/>";

                echo "<div id=\"div_unites_visibles\" class=\"col-12 col-sm-12 col-md-12 col-lg-12 d-none d-sm-block\" align=\"left\">\r\n";
                if ($row_unites_visibles->B_COMBATTIVES_VISIBLES == 1)
                {
                    echo $row_unites_visibles->S_UNITES_COMBATTIVES_VISIBLES;
                }
                else
                {
                    echo $row_unites_visibles->S_UNITES_VISIBLES;
                }
                echo "</div>\r\n";
                echo "</div>\r\n";
            }
        echo "</div>\r\n";
        echo "</div>\r\n";
    //echo "</div>\r\n";

    echo "<div class=\"col-12 col-sm-12 col-md-12 col-lg-9\">\r\n";
        echo "<div class=\"row\" style=\"vertical-align:middle\">\r\n";
            echo "<div class=\"col-12 col-sm-12 col-md-6\">\r\n";
            echo "<h2>";
            if ($ID_ROLE >= 0)
            {
                $requete = "SELECT tab_vaoc_role.ID_ROLE, tab_vaoc_role.ID_PARTIE, tab_vaoc_role.S_NOM AS NOM_ROLE,";
                $requete.=" tab_vaoc_partie.S_NOM AS NOM_PARTIE, DATE_FORMAT(tab_vaoc_partie.DT_TOUR ,'%e/%c/%Y %H:%i') AS DATE_PARTIE";
                $requete.=" FROM tab_vaoc_role, tab_vaoc_partie";
                $requete.=" WHERE (tab_vaoc_role.ID_PARTIE=tab_vaoc_partie.ID_PARTIE) AND tab_vaoc_role.ID_UTILISATEUR=" . $ID_UTILISATEUR;
                //$requete.=" AND (tab_vaoc_role.ID_PARTIE=tab_vaoc_pion.ID_PARTIE)";
                //s$requete.=" AND (tab_vaoc_role.ID_PION=tab_vaoc_pion.ID_PION)";
                $requete.=" ORDER BY NOM_ROLE";
                //echo $requete;
                $res_role = mysql_query($requete, $db);
                if (1 == mysql_num_rows($res_role))
                {
                    //Une seule partie en cours
                    echo $S_NOM;
                    //mais il faut quand meme sauvegarder la valeur de liste_roles pour les appels aux autres ecrans
                    printf("<input id='liste_roles' name='liste_roles' type='hidden' value=\"%u\" />", $ID_PARTIE * 10000 + $ID_ROLE);
                }
                else
                {
                    //liste deroulante avec le choix courant et les autres choix possibles
                    echo "<select id=\"liste_roles\" class=\"custom-select\"  name=\"liste_roles\" onchange=\"javascript:callChangementRole();\" size=\"1\">";
                    while ($row = mysql_fetch_object($res_role))
                    {
                        echo "<option";
                        if (FALSE == empty($ID_ROLE)
                                && $ID_ROLE == $row->ID_ROLE && FALSE == empty($ID_PARTIE) && $ID_PARTIE == $row->ID_PARTIE)
                        {
                                echo " selected=\"selected\"";
                        }
                        //printf (" value=\"%u\">%s (%s)</option>",$row->ID_ROLE,$row->NOM_ROLE,$row->NOM_PARTIE);
                        printf(" value=\"%u\">%s (%s %s)</option>", $row->ID_PARTIE * 10000 + $row->ID_ROLE, $row->NOM_ROLE, $row->NOM_PARTIE, $row->DATE_PARTIE);
                    }
                    echo "</select>";
                }
            }
            else
            {
                echo $S_NOM;
            }

                echo "</h2>";
                echo "<h5>".$S_ORDRE_COURANT."</h5>";
                echo "<div align=\"left\" class=\"d-md-none\">". $S_POSITION. "</div>";
                if ($ID_BATAILLE >= 0)
                {
                    echo "<div align=\"left\" class=\"d-md-none\"><a href=\"nojs.htm\" onclick=\"javascript:callBataille();return false;\">BATAILLE</a></div>";
                }
                echo "</div>";
            echo "<div class=\"col-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Rang\" title=\"Rang\"src='images/rang-26.png' />&nbsp;<span class=\"badge badge-secondary\">". $C_NIVEAU_HIERARCHIQUE ."</span>";
                echo "</h4>";
            echo "</div>\r\n";
            echo "<div class=\"col-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Tactique\" title=\"Tactique\" src='images/tactique-26.png' />&nbsp;<span class=\"badge badge-secondary\">";
                if ($I_TACTIQUE > 0)
                {
                    echo "+";
                }
                echo $I_TACTIQUE;
                echo "</span>";
                echo "</h4>";
            echo "</div>\r\n";
            echo "<div class=\"col-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Strat&eacute;gique\" title=\"Strat&eacute;gique\" src='images/strategie-26.png' />&nbsp;<span class=\"badge badge-secondary\">" . $I_STRATEGIQUE . "</span>";
                echo "</h4>";
            echo "</div>\r\n";
        echo "</div>";
        //Liste des ordres envoyes dans une table triable geree avec des divs
        //echo "recepteur=".$recepteur;
        if ($ID_ROLE >= 0)
        {
            //recherche la nation du joueur
            $requete = "SELECT ID_NATION ";
            $requete.="FROM tab_vaoc_modele_pion ";
            $requete.="WHERE tab_vaoc_modele_pion.ID_PARTIE=" . $ID_PARTIE;
            $requete.=" AND tab_vaoc_modele_pion.ID_MODELE_PION = " . $ID_MODELE_PION;
            //echo $requete;
            $res_modele_pion = mysql_query($requete, $db);
            $row_modele_pion = mysql_fetch_object($res_modele_pion);
    
            echo "<div class=\"row\">\r\n";
            echo "<div class=\"col-12 col-sm-4 col-md-3\">\r\n";
            echo "<select class=\"custom-select\" id=\"liste_recepteur\" name=\"liste_recepteur\" size=\"1\" onchange=\"javascript:changeRecepteur();return false;\">";
            //if (TRUE == empty($recepteur) || (FALSE == empty($recepteur) && $recepteur <0))
            if ($recepteur <0)
            {
                echo "<option value=\"-1\"  selected=\"selected\">Tous</option>";
            }
            else
            {
                echo "<option value=\"-1\">Tous</option>";
            }

            $requete = "SELECT tab_vaoc_role.ID_ROLE,  tab_vaoc_role.S_NOM AS NOM_ROLE, ID_PION";
            $requete.=" FROM tab_vaoc_role, tab_vaoc_partie";
            $requete.=" WHERE (tab_vaoc_role.ID_PARTIE=tab_vaoc_partie.ID_PARTIE) AND tab_vaoc_partie.ID_PARTIE=" . $ID_PARTIE;
            $requete.=" AND (tab_vaoc_role.ID_ROLE<>". $ID_ROLE . ")";
            $requete.=" AND tab_vaoc_role.ID_NATION=" . $row_modele_pion->ID_NATION;
            $requete.=" ORDER BY NOM_ROLE";
            //echo $requete;
            $res_recepteur = mysql_query($requete, $db);
            while ($row = mysql_fetch_object($res_recepteur))
            {
                echo "<option";
                if ($recepteur == $row->ID_PION)
                {
                        echo " selected=\"selected\"";
                }
                printf(" value=\"%u\">%s</option>", $row->ID_PION, $row->NOM_ROLE);
            }
            echo "</select>";
            echo "</div>";
            echo "<div class=\"col-12 col-sm-3 col-md-2\">";
            echo "<badge class=\"control-badge\">&nbsp;Depuis</badge>";
            echo "</div>";
            echo "<div class=\"col-12 col-sm-5 col-md-7\">";
            echo "<badge class=\"control-badge\">Ordre</badge>";
            echo "</div>";
            echo "</div>";
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, true, $I_PATROUILLES_DISPONIBLES, $pageNum, $recepteur);
        }
        else
        {
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, false, $I_PATROUILLES_DISPONIBLES, $pageNum, -1);          
        }

        //Liste des ordres que le QG peut donner
        $ORDRE_AUTORISE=$FL_DEMMARAGE;
        if ($ID_BATAILLE >= 0)
        {
            $ORDRE_AUTORISE=0;
        }
        //echo "ID_BATAILLE=".$ID_BATAILLE;
        echo "<div class=\"row\">";
            echo "<div class=\"col-12\">";
            AfficherDemandeOrdreMouvement($db, $ORDRE_AUTORISE, $ID_PARTIE, $ID_PION, true);
            echo "</div>";
        echo "</div>";
        
        echo "<hr style=\"border-top: 1px solid white;\" />";
        echo "<div class=\"row\">";
            echo "<div class=\"col-12\">";
            AfficherDemandeArret($db, $ORDRE_AUTORISE, $ID_PARTIE, $ID_PION, true);
            echo "</div>";
        echo "</div>";
        //s'agit d'un general en chef ?
        $general_en_chef = IsGeneralEnChef($db, $ID_PARTIE, $ID_PION);
        if ($general_en_chef)
        {
            echo "<div class=\"row\">";
            echo "<div class=\"col-12\">";
            echo "<hr style=\"border-top: 1px solid white;\" />";
            AfficherDemandeTransfert($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION);
            echo "</div>";
            echo "</div>";
        }
    echo "</div>\r\n";
    echo "</div>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherQG');
}

function AfficherBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_ROLE, $ID_PION_PROPRIETAIRE, $ID_PION, $B_QG)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherBataille');
    if ($ID_BATAILLE < 0)
    {
        return "<div align='center'>aucune</div>";
    }

    //recherche du repertoire des images
    $requete = "SELECT S_REPERTOIRE, I_TOUR ";
    $requete.="FROM tab_vaoc_partie ";
    $requete.="WHERE ID_PARTIE=" . $ID_PARTIE;
    $res_repertoire = mysql_query($requete, $db);
    $row_repertoire = mysql_fetch_object($res_repertoire);
    $repertoire = $row_repertoire->S_REPERTOIRE . "_" . $row_repertoire->I_TOUR;

    /* On va afficher les images de bataille de la meme taille que les images de carte standard */
    if ($B_QG == 0)
    {
        //unite standard
        $nomcarte = $repertoire . "/cartepion_" . $ID_PION . "_" . $ID_PION_PROPRIETAIRE . ".png";
        list($largeurImage, $hauteurImage) = getimagesize($nomcarte);
        echo "<img alt='carteBataille' src='" . $repertoire . "/bataille_" . $ID_BATAILLE . ".png' width=" . $largeurImage . " height=" . $hauteurImage . "/>";
    }
    else
    {
        if ($ID_ROLE >= 0)
        {
            list($largeurImage, $hauteurImage) = getimagesize($repertoire . "/carterole_" . $ID_ROLE . ".png");
            echo "<div id='historique_" . $ID_BATAILLE . "' style='display: block;'  align='center' >";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:callBataille();return false;\">";
            echo "<img alt='carteBataille' src='" . $repertoire . "/bataille_" . $ID_BATAILLE . ".png' width=" . $largeurImage . " height=" . $hauteurImage . "/>";
            echo "</a>";
            echo "</div>";
            echo "<div id='topographie_" . $ID_BATAILLE . "' style='display: none;'  align='center' >";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:callBataille();return false;\">";
            echo "<img alt='carteTopographique' src='" . $repertoire . "/bataille_" . $ID_BATAILLE . "_topographique.png' width=" . $largeurImage . " height=" . $hauteurImage . "/>";
            echo "</a>";
            echo "</div>";
            echo "<br/>";
            echo "<div id='commandesCartesBataille' align='center' >";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:onOffBataille(" . $ID_BATAILLE . ",'O','N');return false;\">";
            echo "<img alt='carteBatailleHistorique' src='images/historique.png' />&nbsp;";
            echo "</a>";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:onOffBataille(" . $ID_BATAILLE . ",'N','O');return false;\" >";
            echo "<img alt='carteBatailleTopographique' src='images/topographique.png' />&nbsp;";
            echo "</a>";
            echo "</div>";
        }
        else
        {
            //cas d'un leader ne pouvant servir qu'en appui tactique
            $nomcarte = $repertoire . "/cartepion_" . $ID_PION . "_" . $ID_PION_PROPRIETAIRE . ".png";
            list($largeurImage, $hauteurImage) = getimagesize($nomcarte);
            echo "<img alt='carteBataille' src='" . $repertoire . "/bataille_" . $ID_BATAILLE . ".png' width=" . $largeurImage . " height=" . $hauteurImage . "/>";
        }
    }
    Debug_Logger::getInstance()->time_mesure_end('AfficherBataille');
}

function AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $ID_ROLE)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherCarte');
    //recherche du repertoire des images
    $requete = "SELECT S_REPERTOIRE, I_TOUR ";
    $requete.="FROM tab_vaoc_partie ";
    $requete.="WHERE ID_PARTIE=" . $ID_PARTIE;
    //echo $requete;
    $res_repertoire = mysql_query($requete, $db);
    $row_repertoire = mysql_fetch_object($res_repertoire);
    $repertoire = $row_repertoire->S_REPERTOIRE . "_" . $row_repertoire->I_TOUR;

    if ($ID_ROLE < 0)
    {
        //unite standard
        //Il faut faire une distinction par proprietaire dans le cas d'un transfert ou deux joueurs peuvent voir une m�me unit� en m�me temps
        $nomcarte = $repertoire . "/cartepion_" . $ID_PION . "_" . $ID_PION_PROPRIETAIRE . ".png";
        echo "<img alt='" . $nomcarte . "' style='display: block;' align='middle' class='lazy' src='images/transparent.png' data-src='" . $nomcarte . "'/>";
    }
    else
    {
        echo "<div id='historique_" . $ID_PION . "' style='display: block;' align='center' >";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:callCarte();return false;\">";
            echo "<img class='lazy' alt='carteHistorique' src='images/transparent.png' data-src='" . $repertoire . "/carterole_" . $ID_ROLE . ".png' />";
            echo "</a>";
        echo "</div>";
        
        echo "<div id='topographie_" . $ID_PION . "' style='display: none;' align='center'>";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:callCarte();return false;\">";
            echo "<img class='lazy' alt='carteTopographique' src='images/transparent.png' data-src='" . $repertoire . "/carterole_" . $ID_ROLE . "_topographie.png' />";
            echo "</a>";
        echo "</div>";
        
        echo "<div id='zoom_" . $ID_PION . "' style='display: none;' align='center'>";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:callCarte();return false;\">";
            echo "<img alt='carteZoom'  class='lazy' src='images/transparent.png' data-src='" . $repertoire . "/carterole_" . $ID_ROLE . "_zoom.png' />";
            echo "</a>";
        echo "</div>";

        echo "<div id='film_" . $ID_PION . "' style='display: none;' align='center'>";
            $srcFilmRole = $repertoire . "/filmrole_" . $ID_ROLE . "_zoom.gif";
            $idFilmRole = "film_role_".$ID_ROLE;
            echo "<a href=\"nojs.htm\" onclick=\"javascript:reloadFilm('" . $idFilmRole . "','".$srcFilmRole."');return false;\">";        
            echo "<img class='lazy' id='".$idFilmRole."' alt='film' data-src='" . $srcFilmRole . "' />";
            //echo "<img class='lazy' alt='film' data-src='" . $srcFilmRole . "' />";
            echo "</a>";
        echo "</div>";
        echo "<br/>";
        
        echo "<div id='commandesCartes' align='center' >";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:onOffCarte(" . $ID_PION . ",'O','N','N','N');return false;\">";
            echo "<img alt='carteHistorique' src='images/historique.png' />&nbsp;";
            echo "</a>";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:onOffCarte(" . $ID_PION . ",'N','O','N','N');return false;\" >";
            echo "<img alt='carteTopographique' src='images/topographique.png' />&nbsp;";
            echo "</a>";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:onOffCarte(" . $ID_PION . ",'N','N','O','N');return false;\">";
            echo "<img alt='carteZoom' src='images/zoom.png' />&nbsp;";
            echo "</a>";
            echo "<a href=\"nojs.htm\" onclick=\"javascript:onOffCarte(" . $ID_PION . ",'N','N','N','O');return false;\">";
            echo "<img alt='carteFilm' src='images/filmrole.PNG' />";
            echo "</a>";
        echo "</div>";
    }
    Debug_Logger::getInstance()->time_mesure_end('AfficherCarte');
}

//Renvoie le nouveau numero de l'ordre suivant, ajoute 1 a 
function AjouterIDOrdre($db, $ID_PARTIE)
{
    Debug_Logger::getInstance()->time_mesure_start('AjouterIDOrdre');
    //recherche du prochain identifiant
    $requete = "SELECT MAX_ID_ORDRE ";
    $requete.="FROM tab_vaoc_partie ";
    $requete.="WHERE ID_PARTIE=" . $ID_PARTIE;
//echo $requete;
    $res_idordre = mysql_query($requete, $db);
    $row_idordre = mysql_fetch_object($res_idordre);
    $nouvelOrdre = $row_idordre->MAX_ID_ORDRE + 1;

    //mise � jour de l'identifiant max
    //UPDATE `vaoc`.`tab_vaoc_partie` SET `MAX_ID_ORDRE` = '836' WHERE `tab_vaoc_partie`.`ID_PARTIE` =3 LIMIT 1 ;
    $requete = "UPDATE tab_vaoc_partie ";
    $requete.=" SET `MAX_ID_ORDRE` = '" . $nouvelOrdre . "'";
    $requete.="WHERE ID_PARTIE=" . $ID_PARTIE;
    $res_idordre = mysql_query($requete, $db);
//echo $requete;

    Debug_Logger::getInstance()->time_mesure_end('AjouterIDOrdre');
    return $nouvelOrdre;
}

// ER = 'E' pour ordres envoyes, 'R' pour ordres recus
// Utilise pour la page de cinematique
function TraduireOrdre($db, $ID_PARTIE, $I_TYPE, $ID_PION, $I_HEURE, $I_DUREE, $ID_NOM_LIEU, $I_DISTANCE, $I_DIRECTION, $ID_BATAILLE, $S_MESSAGE, $ER)
{
    Debug_Logger::getInstance()->time_mesure_start('TraduireOrdre');
  //recherche du nom de lieu si besoin
  if (false==empty($ID_NOM_LIEU) && $ID_NOM_LIEU>=0)
  {
  $nomLieu = DonnerNomCarte($db, $ID_PARTIE, $ID_NOM_LIEU);
  }

  //recherche du nom de la bataille si besoin
  if (false==empty($ID_BATAILLE) && $ID_BATAILLE>=0)
  {
  $requete="SELECT S_NOM";
  $requete.=" FROM tab_vaoc_bataille";
  $requete.=" WHERE tab_vaoc_bataille.ID_PARTIE=".$ID_PARTIE;
  $requete.=" AND tab_vaoc_bataille.ID_BATAILLE=".$ID_BATAILLE;
  $res_bataille = mysql_query($requete,$db);
  $rowBataille = mysql_fetch_object($res_bataille);
  $nomBataille = $rowBataille->S_NOM;
  }

  //recherche de la direction si besoin
  if (false==empty($I_DIRECTION) && $I_DIRECTION>=0)
  {
  $nomDirection = DonnerDirection($db, $I_DIRECTION);
  }
  else
  {
  $nomDirection="";
  }

  switch ($I_TYPE)
  {
    case ORDRE_MOUVEMENT:
        //ordre de mouvement
        $s_retour= "Faire mouvement vers ".$I_DISTANCE." km ".$nomDirection." de ".$nomLieu;
        $s_retour.= " &agrave;&agrave; ".$I_HEURE."h00 durant ".$I_DUREE." h/jour";
        break;
    case ORDRE_COMBAT:
        //ordre d'engagement au combat
        $s_retour= "Engag&eacute; &agrave; <a href='nojs.htm' onclick=\"javascript:callBataille(".$ID_BATAILLE.");return false;\">".$nomBataille."</a>";
        break;
    case ORDRE_RETRAITE:
        //ordre de retraite au combat
        $s_retour= "Fait retraite &agrave; <a href='nojs.htm' onclick=\"javascript:callBataille(".$ID_BATAILLE.");return false;\">".$nomBataille."</a>";
        break;
    case ORDRE_MESSAGER:
        //ordre d'envoie de message
        //recherche du destinataire du message
        $nomDestinataire ="inconnu";
        if (TRUE==isset($ID_PION) && TRUE==is_numeric($ID_PION))
        {
            $requete="SELECT S_NOM ";
            $requete.="FROM tab_vaoc_role ";
            $requete.="WHERE ID_PION=".$ID_PION." AND ID_PARTIE=".$ID_PARTIE;
            //echo $requete;
            $res_destinataire = mysql_query($requete,$db);
            if (mysql_num_rows($res_destinataire) >0)
            {
                $rowDestinataire = mysql_fetch_object($res_destinataire);
                $nomDestinataire = $rowDestinataire->S_NOM;
            }
        }

        if ($ER=='E')
        {
            $s_retour= " Transmettre le message \"".$S_MESSAGE."\" &agrave; ".$nomDestinataire;
            if (""<>$nomDirection)
            {
                $s_retour.= " localis&eacute; &agrave; ".$I_DISTANCE." km ".$nomDirection." de ".$nomLieu;
            }
        }
        else
        {
            $s_retour= " Re&ccedil;oit le message \"".$S_MESSAGE."\" de ".$nomDestinataire;
        }
        break;
    case ORDRE_PATROUILLE:
        $s_retour= "Envoie d'une patrouille &agrave; ".$I_DISTANCE." km ".$nomDirection." de ".$nomLieu;
        break;
    case ORDRE_ENDOMMAGER_PONT:
        $s_retour= "Endommager le pont le plus proche";
        break;
    case ORDRE_ARRET:
        $s_retour= "Arr&ecirc;t sur place";
        break;
    case ORDRE_SE_FORTIFIER:
        $s_retour= "Construire des fortifications &agrave; ".$I_HEURE."h00";
        break;
    case ORDRE_CONSTRUIRE_PONT:
        $s_retour= "Construire un pont &agrave; ".$I_HEURE."h00";
        break;
    case ORDRE_REPARER_PONT:
        $s_retour= "R&eacute;parer le pont le plus proche";
        break;
    case ORDRE_REDUIRE_DEPOT:
        $s_retour= "R&eacute;duire le d&eacute;p&ocirc;t le plus proche";
        break;
    case ORDRE_RAVITAILLEMENT_DIRECT:
        $s_retour= "Se ravitailler en direct sur le d&eacute;p&ocirc;t le plus proche";
        break;
    default:
        $s_retour= "type d'ordre inconnu de type=".$I_TYPE;
          //echo "type d'ordre inconnu=".$I_TYPE;
        break;
    }
    Debug_Logger::getInstance()->time_mesure_end('TraduireOrdre');
    return $s_retour;
}

function AfficherPontonnier($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, 
                            $ID_MODELE_PION, $B_DETRUIT, $S_POSITION, $I_TOUR, $DATE_DERNIER_MESSAGE, $pageNum)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherPontonnier');
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">";
	echo "<div class=\"col-md-3\">";
            echo "<div align=\"left\" class=\"d-none d-md-block\">";
		//Grand ecran, on peut afficher une image
		if (0 == $B_DETRUIT)
		{
                    echo "<img alt='unite' title=\"unite\" width=\"200\"  src='images/" . ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, "S_IMAGE_PONTONNIER") . "'/>";
		}
		else
		{
                    echo "<img alt='unite detruite' title='unite detruite' width=\"200\"  src='images/rip.jpg'/>";
		}
		echo "</div>";
		echo "<div align=\"left\">";
		//Cela n'a pas de sens d'afficher un engagement en bataille, de toute facon, il ne pourra rien faire a cause
		//de la proximite des ennemis alors pourquoi ne tenterait-il pas de s'enfuir pendant ce temps la ?
		AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, -1);
            echo "</div>";
            echo "<div align=\"left\">". $DATE_DERNIER_MESSAGE. "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";
	echo "</div>";
	echo "<div class=\"col-12 col-md-9\">";
            echo "<div class=\"row\">";
            echo "<div class=\"col-12\">";
            if (0 == $B_DETRUIT)
            {
                echo "<h2>" . $S_NOM . "</h2>";
                echo "<h5>".$S_ORDRE_COURANT."</h5>";
            }
            else
            {
                echo "<h2><strike>" . $S_NOM . "</strike></h2>";
            }
            echo "</div>";
            echo "</div>";
            echo "<div align=\"left\" class=\"d-md-none\">". $DATE_DERNIER_MESSAGE."</div>";;
            echo "<div align=\"left\" class=\"d-md-none\">". $S_POSITION. "</div>";;

            //derniers ordres
            $I_PATROUILLES_DISPONIBLES = 0; //pour le passage par reference
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, false, $I_PATROUILLES_DISPONIBLES, $pageNum, -1);

            //ordres disponibles
            if (0 == $B_DETRUIT)
            {
                $FL_ACTIF = $FL_DEMMARAGE;

                echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, true);
                AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";
                echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                AfficherDemandeConstruirePont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                AfficherDemandeReparerPont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                AfficherDemandeEndommagerPont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                echo "</div>";
                echo "</div>";
            }
	echo "</div>";
    echo "</div>";
    Debug_Logger::getInstance()->time_mesure_end('AfficherPontonnier');
}

function RechercheDernierOrdreSuivable($db, $ID_PARTIE, $ID_PION, $I_TOUR)
{
    Debug_Logger::getInstance()->time_mesure_start('RechercheDernierOrdreSuivable');
    $id_dernier_ordre = -1;
    //on recherche le dernier ordre deja donne a ce tour, et qui n'est pas independant (transfert, patrouille) et affichable (combat ou retraite), s'il exite, cela devient un ordre_suivant
    $requete_dernier_ordre = "SELECT ID_ORDRE FROM tab_vaoc_ordre WHERE ID_PION = " . $ID_PION . " AND ID_PARTIE=" . $ID_PARTIE . " AND I_TOUR=" . $I_TOUR;
    $requete_dernier_ordre.=" AND I_TYPE<>" . ORDRE_COMBAT;
    $requete_dernier_ordre.=" AND I_TYPE<>" . ORDRE_RETRAITE;
    $requete_dernier_ordre.=" AND I_TYPE<>" . ORDRE_TRANSFERT;
    $requete_dernier_ordre.=" AND I_TYPE<>" . ORDRE_MESSAGER;
    $requete_dernier_ordre.=" AND I_TYPE<>" . ORDRE_MESSAGE_FORUM;
    $requete_dernier_ordre.=" AND I_TYPE<>" . ORDRE_PATROUILLE;
    $requete_dernier_ordre.=" AND I_TYPE<>" . ORDRE_LIGNE_RAVITAILLEMENT;
    $requete_dernier_ordre.=" AND I_TYPE<>" . ORDRE_REDUIRE_DEPOT;
    $requete_dernier_ordre.=" ORDER BY ID_ORDRE DESC";
    //echo $requete_dernier_ordre;
    $res_dernier_ordre = mysql_query($requete_dernier_ordre, $db);
    if (mysql_num_rows($res_dernier_ordre) > 0)
    {
        $row_dernier_ordre = mysql_fetch_object($res_dernier_ordre);
        $id_dernier_ordre = $row_dernier_ordre->ID_ORDRE;
    }
    Debug_Logger::getInstance()->time_mesure_end('RechercheDernierOrdreSuivable');
    return $id_dernier_ordre;
}

function MiseAjourOrdreSuivant($db, $ID_PARTIE, $ID_ORDRE, $ID_ORDRE_SUIVANT)
{
    Debug_Logger::getInstance()->time_mesure_start('MiseAjourOrdreSuivant');
    $requete = "UPDATE tab_vaoc_ordre SET ID_ORDRE_SUIVANT = " . $ID_ORDRE_SUIVANT;
    $requete.=" WHERE tab_vaoc_ordre.ID_ORDRE=" . $ID_ORDRE;
    $requete.=" AND ID_PARTIE=" . $ID_PARTIE;
    mysql_query($requete, $db);
    Debug_Logger::getInstance()->time_mesure_end('MiseAjourOrdreSuivant');
}

function IsGeneralEnChef($db, $ID_PARTIE, $ID_PION)
{
    Debug_Logger::getInstance()->time_mesure_start('IsGeneralEnChef');
    $requete = "SELECT ID_PION_PROPRIETAIRE";
    $requete.=" FROM tab_vaoc_pion";
    $requete.=" WHERE tab_vaoc_pion.ID_PION=" . $ID_PION;
    $requete.=" AND ID_PARTIE=" . $ID_PARTIE;
    $res_general_en_chef = mysql_query($requete, $db);
    $row_general_en_chef = mysql_fetch_object($res_general_en_chef);
    Debug_Logger::getInstance()->time_mesure_end('IsGeneralEnChef');
    return $row_general_en_chef->ID_PION_PROPRIETAIRE == $ID_PION;
}

function AfficherConvoi($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $ID_MODELE_PION, $B_RENFORT, $B_BLESSES, $B_PRISONNIERS, $I_INFANTERIE, $I_CAVALERIE, $I_ARTILLERIE, $I_EXPERIENCE, $S_POSITION, $ID_BATAILLE, $I_TOUR, $DATE_DERNIER_MESSAGE, $numPage)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherConvoi');
   //on recherche l'image associe a l'unite
    //image differente suivant qu'il s'agit d'un convoi de prisonniers, de blesses ou de renfort
    $nom_invariant = "";
    if ($B_BLESSES == 1)
    {
        $NOM_IMAGE = "S_IMAGE_CONVOI_BLESSES";
        $nom_invariant = "Bless&eacute;s ";
    }
    else
    {
        if ($B_RENFORT == 1)
        {
            $NOM_IMAGE = "S_IMAGE_CONVOI_RENFORTS";
            $nom_invariant = "Renforts ";
        }
        else
        {
            $NOM_IMAGE = "S_IMAGE_CONVOI_PRISONNIERS";
            $nom_invariant = "Prisonniers ";
        }
    }

    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">\r\n";
	echo "<div class=\"col-md-3\">\r\n";
            echo "<div align=\"left\" class=\"d-none d-md-block\">\r\n";
            echo "<img alt='unite' title=\"unite\" width=\"200\"  src='images/" . ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, $NOM_IMAGE) . "'/>";
            echo "</div>";
            echo "<div align=\"left\">";
            if ($ID_BATAILLE >= 0)
            {
                AfficherBataille($db, $ID_PARTIE, $ID_BATAILLE, -1, $ID_PION_PROPRIETAIRE, $ID_PION, 1);
            }
            else
            {
                AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, -1);
            }
            echo "</div>";

            echo "<div align=\"left\">". $DATE_DERNIER_MESSAGE. "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";
	echo "</div>";

	echo "<div class=\"col-12 col-md-9\">\r\n";
            echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                $lg_saisie = strlen($S_NOM)-strlen($nom_invariant);
                echo "<h2>" .$nom_invariant;
                echo "<input type=\"text\" style=\"background-color:#434223;color:white;\" id=\"nom_pion" . $ID_PION."\" value=\"". substr($S_NOM, strlen($nom_invariant), $lg_saisie)."\" ";
                echo "size=\"".$lg_saisie."\"/>";
                echo "<input alt=\"envoyer le message\" id=\"id_change_nom".$ID_PION."\" name=\"id_change_nom".$ID_PION."\" "
                . "type= \"image\" src=\"images/valider-26.png\" value=\"submit\" "
                        . "onclick=\"javascript:callChangementNom(" . $ID_PION . ",'id_changementNom','". $nom_invariant ."');\">";
                echo "</h2><h5>".$S_ORDRE_COURANT."</h5>";
                echo "</div>";
            echo "</div>";
            echo "<div align=\"left\" class=\"d-md-none\">". $DATE_DERNIER_MESSAGE."</div>\r\n";
            echo "<div align=\"left\" class=\"d-md-none\">". $S_POSITION. "</div>\r\n";;

            echo "<div class=\"row\">\r\n";
                echo "<div class=\"col-12 col-sm-4\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Infanterie\" title=\"Infanterie\" src='images/infanterie-26.png' />";
                echo "&nbsp;<span class=\"badge badge-secondary\">". $I_INFANTERIE ."</span>";
                echo "</h4>";
                echo "</div>\r\n";

                echo "<div class=\"col-12 col-sm-4\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Cavalerie\" title=\"Cavalerie\" src='images/cavalerie-26.png' />";
                echo "&nbsp;<span class=\"badge badge-secondary\">". $I_CAVALERIE ."</span>";
                echo "</h4>";
                echo "</div>\r\n";

                echo "<div class=\"col-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Artillerie\" title=\"Artillerie\" src='images/artillerie-26.png' />";
                echo "&nbsp;<span class=\"badge badge-secondary\">". $I_ARTILLERIE ."</span>";
                echo "</h4>";
                echo "</div>\r\n";

                echo "<div class=\"col-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Exp&eacute;rience\" title=\"Exp&eacute;rience\" src='images/experience-26.png' />";
                echo "&nbsp;<span class=\"badge badge-secondary\">". $I_EXPERIENCE ."</span>";
                echo "</h4>";
                echo "</div>\r\n";
            echo "</div>\r\n";
            //derniers ordres
            $I_PATROUILLES_DISPONIBLES=0;
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, false, $I_PATROUILLES_DISPONIBLES, $numPage, -1);

            //ordres disponibles
            if ($ID_BATAILLE >= 0)
            {
                $FL_ACTIF = 0;
            }
            else
            {
                $FL_ACTIF = $FL_DEMMARAGE;
            }

            echo "<hr style=\"border-top: 2px dashed white;\" />\r\n";
            echo "<div class=\"row\">\r\n";
            echo "<div class=\"col-12\">\r\n";
            AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, ($B_BLESSES == 1) || ($B_PRISONNIERS == 1));
            AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
            echo "</div>";
            echo "</div>";

            if ($B_RENFORT == 1)
            {
                echo "<hr style=\"border-top: 1px solid white;\" />";
                echo "<div class=\"row\">\r\n";
                echo "<div class=\"col-12\">\r\n";
                AfficherDemandeRenforcer($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE, 0, -1, $ID_MODELE_PION);
                echo "</div>";
                echo "</div>";
            }
	echo "</div>\r\n";
    echo "</div>\r\n";
    Debug_Logger::getInstance()->time_mesure_end('AfficherConvoi');
}

function AfficherConvoiDepot($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $ID_MODELE_PION, $B_RENFORT, $C_NIVEAU_DEPOT, $ID_DEPOT_SOURCE, $B_DETRUIT, $S_POSITION, $I_TOUR, $DATE_DERNIER_MESSAGE, $pageNum)
{
    Debug_Logger::getInstance()->time_mesure_start('AfficherConvoiDepot');
    if (1 == $B_DETRUIT)
    {
        return;    
    }
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">\r\n";
    echo "<div class=\"col-md-3\">";
        echo "<div align=\"left\" class=\"d-none d-md-block\">";
        //Grand ecran, on peut afficher une image
        echo "<img alt='unite' title=\"unite\" width=\"200\"  src='images/" . ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, "S_IMAGE_CONVOI_DEPOT") . "'/>";
        echo "</div>";
        echo "<div align=\"left\">";
        //Cela n'a pas de sens d'afficher un engagement en bataille, de toute facon, il ne pourra rien faire a cause
        //de la proximite des ennemis alors pourquoi ne tenterait-il pas de s'enfuir pendant ce temps la ?
        AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, -1);
        echo "</div>";
        echo "<div align=\"left\">". $DATE_DERNIER_MESSAGE. "</div>";
        echo "<div align=\"left\">". $S_POSITION. "</div>";
    echo "</div>";
    echo "<div class=\"col-12 col-md-9\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                $nom_invariant = "Convoi de ravitaillement ";
                $lg_saisie = strlen($S_NOM)-strlen($nom_invariant);
                echo "<h2>" .$nom_invariant;
                echo "<input type=\"text\" style=\"background-color:#434223;color:white;\" id=\"nom_pion" . $ID_PION."\" value=\"". substr($S_NOM, strlen($nom_invariant), $lg_saisie)."\" ";
                echo "size=\"".$lg_saisie."\"/>";
                echo "<input alt=\"envoyer le message\" id=\"id_change_nom".$ID_PION."\" name=\"id_change_nom".$ID_PION."\" "
                . "type= \"image\" src=\"images/valider-26.png\" value=\"submit\" "
                        . "onclick=\"javascript:callChangementNom(" . $ID_PION . ",'id_changementNom','". $nom_invariant ."');\">";
                echo "<h5>".$S_ORDRE_COURANT."</h5>";
                echo "</div>";
            echo "</div>";
            echo "<div align=\"left\" class=\"d-md-none\">". $DATE_DERNIER_MESSAGE."</div>";;
            echo "<div align=\"left\" class=\"d-md-none\">". $S_POSITION. "</div>";;

            //derniers ordres
            $I_PATROUILLES_DISPONIBLES = 0; //pour le passage par reference
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, false, $I_PATROUILLES_DISPONIBLES, $pageNum, -1);

            //ordres disponibles
                $FL_ACTIF = $FL_DEMMARAGE;

                echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                    AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, true);
                    AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";
                echo "<div class=\"row\">";
                echo "<div class=\"col-12\">";
                if ($B_RENFORT == 1 || $C_NIVEAU_DEPOT == 'D')
                {
                        AfficherDemandeRenforcer($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE, 1, $ID_DEPOT_SOURCE, $ID_MODELE_PION);
                        echo "</div>";
                        echo "</div>";
                        echo "<hr style=\"border-top: 1px solid white;\" />";
                        echo "<div class=\"row\">";
                        echo "<div class=\"col-12\">";
                        AfficherDemandeEtablirDepot($FL_DEMMARAGE, $ID_PION);
                }
                else
                {
                        AfficherDemandeRenforcer($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE, 0, -1, $ID_MODELE_PION);
                }
                echo "</div>";
                echo "</div>";
    echo "</div>";
    echo "</div>";
    Debug_Logger::getInstance()->time_mesure_end('AfficherConvoiDepot');
}
</script>
