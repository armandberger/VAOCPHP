<script language="php">
/* * * Ensemble des fonctions d'affichage utilisees par toutes les pages VAOC ** */
//Constantes
define("SANS_PROPRIETAIRE", 6);
define("NB_COLS_UNITES", 10);
define("NB_COLS_QG", 5);
define("NB_MESSAGES_MAX", 5);
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

function ligneDrop($i_ligne)
{
    return "<div id=\"drop".$i_ligne."\" class=\"dropper col-xs-12 col-centered\" ondrop=\"drop(event)\" ondragover=\"allowDrop(event)\" ondragenter=\"entrerDrop(event)\" ondragleave=\"quitterDrop(event)\">&nbsp;</div>";
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
    $ligne = "<div id=\"drag".$i_ligne."\" id_pion=\"".$id_pion."\" class=\"draggable col-xs-12 col-centered\" draggable=\"true\" ondragstart=\"drag(event)\">";
    $ligne.= "<image src=\"images/".$image."\"/>&nbsp;<small>".substr($nom,0,40)."</small></div>";
    return $ligne;
}

function DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE)
{
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
    return $row_dernier_message->DATE_DEPART;
}

function ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, $NOM_MODELE_IMAGE) 
{
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

    return $row_modele_pion->S_IMAGE;
}

function AfficherDemandeEtablirDepot($FL_DEMMARAGE, $ID_PION)
{
    echo "<input alt=\"ordre d'etablir un depot\" id=\"id_etablir_ordre\" name=\"id_etablir_ordre\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_etablir');\" ";
    if (0 === $FL_DEMMARAGE)
    {
        //ordres interdits ou aucune unite renforcable
        echo " src=\"images/btnEtablir_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnEtablir_on2.png\"";
    }
    echo "/>";
    echo "\r\n";
}

function AfficherDemandeRenforcer($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE, $B_DEPOT, $ID_DEPOT_SOURCE, $ID_MODELE_PION)
{
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

    echo "<input alt=\"ordre de renfort d'une unite\" id=\"id_renforcer_ordre\" name=\"id_renforcer_ordre\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_renforcer');\" ";
    if (0 == $FL_DEMMARAGE || 0 == mysql_num_rows($res_renforcer_unite))
    {
        //ordres interdits ou aucune unit� renforcable
        echo " src=\"images/btnRenforcer_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnRenforcer_on2.png\"";
    }
    echo "/>";

    if (0 == mysql_num_rows($res_renforcer_unite))
    {
        echo "Aucune unit&eacute; sous votre commandement ne peut &ecirc;tre renforc&eacute;e";
    }
    else
    {
        $id_chaine = "id_renforcer_unite_" . $ID_PION;
        printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
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
    echo "\r\n";
}

function AfficherDemandeGenererConvoi($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, $C_NIVEAU_DEPOT)
{
    echo "<input alt=\"generer un nouveau convoi\" id=\"id_generer_nouveau_convoi\" name=\"id_generer_nouveau_convoi\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_generer_convoi');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnGenerer_off2.png\" disabled >";
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
                echo " src=\"images/btnGenerer_off2.png\" disabled >";
                echo " pas de nouvelle g&eacute;n&eacute;ration avant " . $delai . " heure";
                if ($delai > 1)
                {
                    echo "s";
                }
            }
            else
            {
                echo " src=\"images/btnGenerer_on2.png\">";
            }
        }
        else
        {
            echo " src=\"images/btnGenerer_on2.png\">";
        }
    }
    echo "\r\n";
}

function AfficherDemandeReduireDepot($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    echo "<input alt=\"reduire un depot\" id=\"reduire un depot\" name=\"reduire un depot\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_reduire_depot');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnReduire_off2.png\" disabled >";
    }
    else
    {
        //on ne peut donner cet ordre que s'il n'a pas déjà été donné
        $requete_dernier_ordre = "SELECT I_TOUR FROM tab_vaoc_ordre WHERE ID_PION = " . $ID_PION . " AND ID_PARTIE=" . $ID_PARTIE . " AND I_TYPE=" . ORDRE_REDUIRE_DEPOT;
        $requete_dernier_ordre.=" ORDER BY I_TOUR DESC";
        //echo $requete_dernier_ordre;
        $res_dernier_ordre = mysql_query($requete_dernier_ordre, $db);
        if (mysql_num_rows($res_dernier_ordre) > 0)
        {
            //48 heures, en me disant qu'il faut maximum 24 heures pour que l'ordre revienne et que le joueur sache, effectivement que le dépôt est passé au niveau B
            $delai = 48 - ($I_TOUR - $row_dernier_ordre->I_TOUR);
            if ($delai > 0)
            {
                echo " src=\"images/btnReduire_off2.png\" disabled >";
            }
            else 
            {
                echo " src=\"images/btnReduire_on2.png\">";
            }           
        }
        else
        {
            echo " src=\"images/btnReduire_on2.png\">";
        }
    }
    echo "\r\n";
}

function AfficherDemandeLigneDeRavitaillement($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    echo "<input alt=\"cr&eacute;er une ligne de ravitaillement\" id=\"ligne_de_ravitaillement\" name=\"ligne_de_ravitaillement\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_ligne_ravitaillement');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnLigneDeRavitaillement_off2.png\" disabled >";
    }
    else
    {
        echo " src=\"images/btnLigneDeRavitaillement_on2.png\">";
    }
    //choix d'une distance
    echo "<div class=\"input-group\">";
    $id_chaine = "id_distance_mouvement_" . $ID_PION;
    AfficherDistance($db, $FL_DEMMARAGE, $id_chaine);

    //choix d'une direction
    $id_chaine = "id_direction_mouvement_" . $ID_PION;
    AfficherDirection($db, $FL_DEMMARAGE, $id_chaine);
    echo "</div>";

    //choix d'une destination
    echo "<div class=\"input-group\">";
	echo "<div class=\"input-group-addon\">&nbsp;de&nbsp;</div>";
        $id_chaine = "id_destination_mouvement_" . $ID_PION;
        AfficherDestination($db, $FL_DEMMARAGE, $id_chaine, $ID_PARTIE);
    echo "</div>";

    echo "\r\n";
}

function AfficherDemandeTransfert($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    echo "<input alt=\"ordre de transfert d'une unit&eacute;\" id=\"id_transfert_ordre\" name=\"id_transfert_ordre\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_transfert');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnTransferer_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnTransferer_on2.png\"";
    }
    echo "/>";

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

    printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
    if (0 == $FL_DEMMARAGE)
    {
        echo " disabled ";
    }
    echo ">";
    //on ajoute une option vide pour éviter que l'utilisateur n'envoie l'ordre par megarde
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

    printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
    if (0 == $FL_DEMMARAGE)
    {
        echo " disabled ";
    }
    echo ">";
    //on ajoute une option vide pour éviter que l'utilisateur n'envoie l'ordre par megarde
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
    echo "\r\n";
}

function AfficherDemandeArret($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    echo "<input alt=\"ordre d'arret sur position\" id=\"id_arret_unite\" name=\"id_arret_unite\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_arret');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnArret_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnArret_on2.png\"";
    }
    echo "/>";
    echo "\r\n";
}

function AfficherDemandeReparerPont($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    echo "<input alt=\"ordre de reparer un pont\" id=\"id_reparer_pont_unite\" name=\"id_reparer_pont_unite\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_reparer_pont');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnReparerPont_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnReparerPont_on2.png\"";
    }
    echo "/>\r\n";
}

function AfficherDemandeEndommagerPont($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    echo "<input alt=\"ordre d'endommager un pont\" id=\"id_endommager_pont_unite\" name=\"id_endommager_pont_unite\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_endommager_pont');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnEndommagerPont_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnEndommagerPont_on2.png\"";
    }
    echo "/>\r\n";
}

function AfficherDemandeConstruirePont($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    echo "<input alt=\"ordre de construction de pont\" id=\"id_construire_pont_unite\" name=\"id_construire_pont_unite\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_construire_pont_unite');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnConstruirePont_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnConstruirePont_on2.png\"";
    }
    echo "/>\r\n";
}

function AfficherDemandeConstruireFortifcation($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    echo "<input alt=\"ordre de construction de fortification\" id=\"id_construire_fortification\" name=\"id_construire_fortification\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_construire_fortification_unite');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnSefortifier_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnSefortifier_on2.png\"";
    }
    echo "/>\r\n";
}

function AfficherDemandeRavitaillementDirect($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    echo "<input alt=\"ordre de ravitaillement direct\" id=\"id_ravitaillement_direct\" name=\"id_ravitaillement_direct\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_ravitaillement_direct_unite');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnRavitaillementDirect_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnRavitaillementDirect_on2.png\"";
    }
    echo "/>\r\n";
}

function AfficherDemandeOrdrePatrouiller($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION)
{
    //echo "FL_DEMMARAGE=".$FL_DEMMARAGE;
    echo "<input alt=\"ordre de patrouille\" id=\"id_mouvement_patrouille\" name=\"id_mouvement_patrouille\" type=\"image\" value=\"submit\" class=\"btn btn-default\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_patrouillera');\" ";
    if (0 == $FL_DEMMARAGE)
    {
        //ordres interdits
        echo " src=\"images/btnPatrouiller_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnPatrouiller_on2.png\"";
    }
    echo "/>";

    //choix d'une distance
    echo "<div class=\"input-group\">";
        $id_chaine = "id_distance_patrouille_" . $ID_PION;

        printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
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
        $id_direction = "id_direction_patrouille_" . $ID_PION;
        $id_chaine = "id_direction_patrouille_" . $ID_PION;
        printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
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

        /*
        $requete = "SELECT ID_NOM, S_NOM";
        $requete.=" FROM tab_vaoc_noms_carte";
        $requete.=" WHERE ID_PARTIE=" . $ID_PARTIE;
        $requete.=" ORDER BY S_NOM";
        //echo $requete;
        $res_noms = mysql_query($requete, $db);
        $id_liste = "id_destination_patrouille_" + $ID_PION;
        printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
        if (0 == $FL_DEMMARAGE)
        {
            echo " disabled ";
        }
        echo ">";
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
    echo "</div>";
    echo "\r\n";
}

function AfficherPatrouilles($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_PATROUILLES_DISPONIBLES, $I_PATROUILLES_MAX)
{
    //recherche des patrouilles deja en cours de mission
    //-> en fait, ne sert à rien car deja conserve dans les ordres transmis ? Sauf que l'on ne peut jamais savoir a quel ordre
    //correspond une patrouille qui revient. On ne peut donc qu'afficher les ordres donnes, au joueur de faire le tri !
    //Il faut quand meme afficher les patrouilles envoyees a ce tour ci pour permettre au joueur de pouvoir les supprimer
    //-> fait dans l'affichage des ordres
    //recherche le nombre d'ordres de patrouilles envoyés à ce tour
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
}

function DonnerDirection($db, $I_DIRECTION)
{
    $requete = "SELECT S_VALEUR";
    $requete.=" FROM tab_vaoc_parametre";
    $requete.=" WHERE S_TYPE='direction' AND I_VALEUR=" . $I_DIRECTION;
    $res_direction = mysql_query($requete, $db);
    $row = mysql_fetch_object($res_direction);
    return $row->S_VALEUR;
}

function DonnerNomCarte($db, $ID_PARTIE, $ID_NOM)
{
    $requete = "SELECT tab_vaoc_noms_carte.S_NOM";
    $requete.=" FROM tab_vaoc_noms_carte";
    $requete.=" WHERE tab_vaoc_noms_carte.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_noms_carte.ID_NOM=" . $ID_NOM;
    $res_nom = mysql_query($requete, $db);
    $row = mysql_fetch_object($res_nom);
    //echo $requete;
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
    $requete = "SELECT ID_ORDRE, ID_ORDRE_SUIVANT, I_TYPE, I_DISTANCE, I_DIRECTION, ID_NOM_LIEU, I_HEURE, I_DUREE, S_MESSAGE, ";
    $requete.=" I_TOUR, ID_PION_DESTINATION";
    $requete.=" FROM tab_vaoc_ordre";
    $requete.=" WHERE ID_ORDRE=" . $ID_ORDRE . " AND tab_vaoc_ordre.ID_PARTIE=" . $ID_PARTIE;
    //echo $requete;
    $res_ordre = mysql_query($requete, $db);
    $row = mysql_fetch_object($res_ordre);

    if ($bOrdrePrimaire)
    {
        //$s_retour = "<div class=\"col-xs-12 col-sm-3 col-md-2\">";
        $s_retour = "<div class=\"col-xs-12 col-sm-1\">";
        //echo 'itour='.$I_TOUR." demmarage=".$FL_DEMMARAGE;
        //echo 'ritour='.$row->I_TOUR." type=".$row->I_TYPE;
        
        if ($I_TOUR == $row->I_TOUR && (1 == $FL_DEMMARAGE) && (ORDRE_MESSAGE_FORUM != $row->I_TYPE))
        // si on ajoute la suite, on crée un bug si fait un mouvement dérrière && (ORDRE_MESSAGE_FORUM != $row->I_TYPE))
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
            //$s_retour .= "<div class=\"col-xs-12 col-sm-5 col-md-7\">";
            $s_retour .= "<div class=\"col-xs-12 col-sm-7 col-md-8\">";
        }
        else
        {
            //$s_retour .= "<div class=\"col-xs-12 col-sm-9 col-md-10\">";
            $s_retour .= "<div class=\"col-xs-12 col-sm-11\">";
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
            //ordre de renforcement d'une unit�
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
            //ordre de génération d'un convoi automatique toute les 24 heures vers une destination
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
    return $row->ID_ORDRE_SUIVANT;
    echo "\r\n";
}

function ListeOrdresRecursifs($db, $ID_PARTIE, $ID_PION, $destinataire)
{
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
 
    return $rows;
}

function listeRemplacantsOrdreSQL($db, $ID_PARTIE, $ID_PION)
{
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
    return $chaineResultat;
}

function AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, $QG, &$I_PATROUILLES_DISPONIBLES, $pageNum, $destinataire)
{
    //note : tous les ordres ont une destination, sauf les ordres de combat et, cela tombe bien, on ne veut pas les afficher 
    //(en fait on voit quand meme les autres parce que je leur met une destination de valeur 0, ce qui est faux mais comme on ne l'affiche pas...)
    //sauf que pas de bol, on veut afficher les ordres de destruction de pont, de transfert, etc...
    //finalement, pourquoi ne pas afficher aussi les ordres de combat ? ->Parce que ce n'est pas forcement le proprietaire qui les donne et qu'il les verrait !
    //pour etre propre, il faudra clairement dire qu'elles ordres on ne veut pas afficher et pour les autres mettre les destinations a -1
    //echo "ID_PION=".$ID_PION."<BR/>";
    $requete = "SELECT ID_ORDRE, ID_ORDRE_SUIVANT, I_TYPE, I_DISTANCE, I_DIRECTION, ID_NOM_LIEU, I_HEURE, I_DUREE, S_MESSAGE, ";
    $requete.=" I_TOUR, ID_PION_DESTINATION, S_NOM";
    $requete.=" FROM tab_vaoc_ordre, tab_vaoc_pion";
    $requete.=" WHERE  tab_vaoc_ordre.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND (tab_vaoc_ordre.ID_PION=" . $ID_PION . listeRemplacantsOrdreSQL($db, $ID_PARTIE, $ID_PION).")";
    $requete.=" AND tab_vaoc_pion.ID_PION=tab_vaoc_ordre.ID_PION AND tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
    if ($destinataire>=0)
    {
        $requete.=" AND tab_vaoc_ordre.ID_PION_DESTINATION=" . $destinataire;
    }
    //on ne veut pas des ordres de combat ou de retraite
    $requete.=" AND tab_vaoc_ordre.I_TYPE<>" . ORDRE_COMBAT . " AND tab_vaoc_ordre.I_TYPE<>" . ORDRE_RETRAITE;
    $requete.=" AND tab_vaoc_ordre.I_TYPE<>" . ORDRE_RETRAIT . " AND tab_vaoc_ordre.I_TYPE<>" . ORDRE_ENGAGEMENT;
    //on ne veut que les ordres qui ne sont pas les suivants d'un autre
    $requete.=" AND tab_vaoc_ordre.ID_ORDRE not in";
    $requete.="(SELECT ID_ORDRE_SUIVANT from tab_vaoc_ordre WHERE ID_PION=" . $ID_PION . " AND tab_vaoc_ordre.ID_PARTIE=" . $ID_PARTIE . ")";
    $res_ordreNb = mysql_query($requete, $db);
    //echo $requete;
    $nb_messages_ordres = mysql_num_rows($res_ordreNb);
    $offset = ($pageNum - 1) * NB_MESSAGES_MAX;
    if ($offset < 0)
    {
        $offset = 0;
    }

    $requete.=" ORDER BY I_TOUR DESC, ID_ORDRE DESC LIMIT " . $offset . "," . NB_MESSAGES_MAX;
    //echo $requete;
    $res_ordre = mysql_query($requete, $db);
    if (false==$QG)
    {
        echo "<div class=\"row\">";
             echo "<div class=\"col-xs-12 col-sm-1\" align=\"left\">";
                 echo "<label class=\"control-label\">Depuis</label>";
             echo "</div>";
             echo "<div class=\"col-xs-12 col-sm-11\">";
                 echo "<label class=\"control-label\">Ordre</label>";
             echo "</div>";
         echo "</div>";
    }
    
    if (mysql_num_rows($res_ordre) <= 0)
    {
        echo "<div class=\"row\">";
        echo "<div class=\"col-xs-12 col-sm-4 col-md-3\">Aucun ordre transmis</div>";      
        echo "</div>\r\n";
    }

    while ($row = mysql_fetch_object($res_ordre))
    {
        //echo "ordre=".$row->ID_ORDRE;
        echo "<hr style=\"border-top: 1px solid white; width:100%\" />";
        echo "<div class=\"row\">";
        if ($QG)
        {
            echo "<div class=\"col-xs-12 col-sm-4 col-md-3\">";
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
        echo "<div class=\"col-xs-12\">";
        echo "<nav>";
        echo "<ul class=\"pagination\">";
        echo "<li>";
        echo "<a href=\"#\" aria-label=\"Premier\" onclick=\"javascript:callAllerALapage(" . $ID_PION . ",1);return false;\">";
        echo "<span aria-hidden=\"true\">&laquo;</span>";
        echo "</a>";
        echo "</li>";
        for ($page = 1; $page <= $maxPage; $page++)
        {
            if ($page==$pageNum)
            {
                echo "<li><span aria-hidden=\"true\"><strong>" . $page . "</span></strong></li>";
            }
            else
            {
                echo "<li><a href=\"#\" onclick=\"javascript:callAllerALapage(" . $ID_PION . "," . $page . ");return false;\">" . $page . "</a></li>";
            }
        }
        echo "<li>";
        echo "<a href=\"#\" aria-label=\"Dernier\" onclick=\"javascript:callAllerALapage(" . $ID_PION . "," . $maxPage . ");return false;\">";
        echo "<span aria-hidden=\"true\">&raquo;</span>";
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
}

function AfficherDistance($db, $FL_ACTIF, $id_chaine)
{
    printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
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
    echo "<div class=\"input-group-addon\">&nbsp;km&nbsp;</div>";
    echo "\r\n";
}

function AfficherDirection($db, $FL_ACTIF, $id_chaine)
{
    $requete = "SELECT ID_PARAMETRE, S_VALEUR, I_VALEUR";
    $requete.=" FROM tab_vaoc_parametre";
    $requete.=" WHERE S_TYPE='direction'";
    $requete.=" ORDER BY I_VALEUR";
    $res_direction = mysql_query($requete, $db);
    printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine, $id_chaine);
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
}

function AfficherDestination($db, $FL_ACTIF, $id_chaine, $id_partie)
{
    $requete = "SELECT ID_NOM, S_NOM";
    $requete.=" FROM tab_vaoc_noms_carte";
    $requete.=" WHERE ID_PARTIE=" . $id_partie;
    //$requete.=" AND B_PONT=0";
    $requete.=" ORDER BY S_NOM";
    //echo $requete;
    //$res_noms = mysql_query($requete, $db);
    //printf("<select class=\"selectpicker\"  id=\"%s\" name=\"%s\" size=1 onfocus='this.size=10;' onblur='this.size=1;' onchange='this.size=1; this.blur();'", $id_chaine, $id_chaine);
    // version liste déroulante
    /**/
    $valeur_defaut="";
    echo "<div class=\"dropdown\">";
    //echo "<button class=\"btn btn-default form-control dropdown-toggle\" type=\"button\"  id=\"".$id_chaine."\" name=\"".$id_chaine."\">Destination";
    echo "<button class=\"btn btn-default form-control dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" id=\"".$id_chaine."\" name=\"".$id_chaine."\"";
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
    

/**/
    
    //printf("<select class=\"form-control\"  id=\"%s\" name=\"%s\" ", $id_chaine, $id_chaine);
    /*
    printf("<select class=\"selectpicker\"  id=\"%s\" name=\"%s\" onclick=\"javascript:alert('test');return false;\"", $id_chaine, $id_chaine);
    if (0 == $FL_ACTIF)
    {
        echo " disabled ";
    }
    echo ">";
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
        echo "<option>Destination</option>";
    }
    echo "</select>";
    */
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
    $nom_bool = "b" . $id_chaine;
    $nom_ul = $id_chaine;
    echo "<!-- nombool=" . $nom_bool . "-->";
    global $chargement_destination_script;
    $chargement_destination_script .= "var " . $nom_bool . " = true; 
        $(\"#" . $id_chaine . "\").click(function () {
        if (" . $nom_bool . ") {
        $(\"#ul" . $nom_ul . "\").load(\"listenomscarte.php?partie=".$id_partie."\");"
        . $nom_bool . " = false;
        }});";
    /*
    $chargement_destination_script .= "var " . $nom_bool . " = true; 
        $(\"#" . $id_chaine . "\").click(function () {
        if (" . $nom_bool . ") {
            alert(\".$nom_ul.\");
        $(\"#ul" . $nom_ul . "\").html(\"<li><a data-target=\\\"#\\\">1</a></li><li><a data-target=\\\"#\\\">2</a></li><li><a data-target=\\\"#\\\">trois</a></li>\");"
        . $nom_bool . " = false;
        }});";
     * */
     
/*
    $chargement_destination_script .= "var " . $nom_bool . " = true; 
        $(\"#" . $id_chaine . "\").click(function () {
        if (" . $nom_bool . ") {
            alert(\".$nom_ul.\");
        $(\"#" . $nom_ul . "\").load(\"listenomscarte.php?partie=".$id_partie."\");"
        . $nom_bool . " = false;
        }});";
  */   
    //echo "<!-- chargement_destination_script=" . $chargement_destination_script . "-->";     
}

function AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, $SANS_DUREE)
{
    echo "<input class=\"btn btn-default\" alt=\"ordre de mouvement\" id=\"id_mouvement\" name=\"id_mouvement\" type=\"image\" value=\"submit\" onclick=\"javascript:callDonnerOrdreUnite(" . $ID_PION . ",'id_allera');\" ";

    if (0 == $FL_ACTIF)
    {
        //ordres interdits
        echo " src=\"images/btnAllerA_off2.png\" disabled ";
    }
    else
    {
        echo " src=\"images/btnAllerA_on2.png\"";
    }
    echo "/>";

    //choix d'une distance
    echo "<div class=\"input-group\">";
    $id_chaine = "id_distance_mouvement_" . $ID_PION;
    AfficherDistance($db, $FL_ACTIF, $id_chaine);

    //choix d'une direction
    $id_chaine = "id_direction_mouvement_" . $ID_PION;
    AfficherDirection($db, $FL_ACTIF, $id_chaine);
    echo "</div>";

    //choix d'une destination
    echo "<div class=\"input-group\">";
	echo "<div class=\"input-group-addon\">&nbsp;de&nbsp;</div>";
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
        echo "<div class=\"input-group\">";
            echo "<div class=\"input-group-addon\">&nbsp;A&nbsp;</div>";
            $requete = "SELECT ID_PARAMETRE, S_VALEUR, I_VALEUR";
            $requete.=" FROM tab_vaoc_parametre";
            $requete.=" WHERE S_TYPE='heure'";
            $requete.=" ORDER BY S_VALEUR";
            //echo $requete;
            $res_heure = mysql_query($requete, $db);
            printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine_heure, $id_chaine_heure);
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

        //choix de la duree
        echo "<div class=\"input-group\">";
            echo "<div class=\"input-group-addon\">&nbsp;durant&nbsp;</div>";
            printf("<select class=\"selectpicker\" id=\"%s\" name=\"%s\" size=1", $id_chaine_duree, $id_chaine_duree);
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
            echo "<div class=\"input-group-addon\">&nbsp;heures/jour.</div>";
        echo "</div>";
    }
    echo "\r\n";
}

function AfficherArtillerie($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $ID_MODELE_PION, $B_DETRUIT, $I_FATIGUE, $I_EXPERIENCE, $I_ARTILLERIE, $I_ARTILLERIE_INITIALE, $S_POSITION, $ID_BATAILLE, $I_TOUR, $pageNum)
{
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">\r\n";
	echo "<div class=\"col-md-3\">";
            echo "<div align=\"left\" class=\"hidden-xs hidden-sm\">";
		//Grand écran, on peut afficher une image
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

            echo "<div align=\"left\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE). "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";
        echo "</div>";
        echo "<div class=\"col-xs-12 col-md-9\">";
            echo "<div class=\"row\">";
		echo "<div class=\"col-xs-12\">";
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
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE)."</div>";;
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". $S_POSITION. "</div>";;

            echo "<div class=\"row\">";
		echo "<div class=\"col-xs-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Artillerie\" title=\"Artillerie\" src=\"images/artillerie-26.png\" />";
		echo "&nbsp;<span class=\"label label-default\">". round($I_ARTILLERIE * (100 - $I_FATIGUE) / 100) . "/" . $I_ARTILLERIE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-xs-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Fatigue\" title=\"Fatigue\" src='images/fatigue-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">". $I_FATIGUE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-xs-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Exp&eacute;rience\" title=\"Exp&eacute;rience\" src='images/experience-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">". $I_EXPERIENCE ."</span>";
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
                echo "<div class=\"col-xs-12\">";
                AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";

                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
                AfficherDemandeEndommagerPont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                echo "</div>";
                echo "</div>";
            }
	echo "</div>";
    echo "</div>";
}

function AfficherDivision($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $ID_MODELE_PION, $B_DETRUIT, $I_MORAL, $I_MORAL_MAX, $I_FATIGUE, $I_INFANTERIE, $I_INFANTERIE_INITIALE, $I_CAVALERIE, $I_CAVALERIE_INITIALE, $I_ARTILLERIE, $I_ARTILLERIE_INITIALE, $I_EXPERIENCE, $B_CAVALERIE_DE_LIGNE, $B_CAVALERIE_LOURDE, $B_GARDE, $B_VIEILLE_GARDE, $I_MATERIEL, $I_RAVITAILLEMENT, $S_POSITION, $ID_BATAILLE, $I_TOUR, $I_PATROUILLES_DISPONIBLES, $I_PATROUILLES_MAX, $I_NIVEAU_FORTIFICATION, $pageNum)
{
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
                echo "<div align=\"left\" class=\"hidden-xs hidden-sm uniteFortifcationMax\" title=\"unite tres fortifiee\">";
                $infobulle="unite tres fortifiee";
            }
            else
            {
                echo "<div align=\"left\" class=\"hidden-xs hidden-sm\">";
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

            echo "<div align=\"left\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE). "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";		
	echo "</div>";
	
	echo "<div class=\"col-xs-12 col-md-9\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
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
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE)."</div>";;
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". $S_POSITION. "</div>";;

            echo "<div class=\"row\">";
		echo "<div class=\"col-xs-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Infanterie\" title=\"Infanterie\" src='images/infanterie-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">". round($I_INFANTERIE * (100 - $I_FATIGUE) / 100) . "/" . $I_INFANTERIE ."</span>";
		echo "</h4>";
                echo "</div>";

		echo "<div class=\"col-xs-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Cavalerie\" title=\"Cavalerie\" src='images/cavalerie-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">". round($I_CAVALERIE * (100 - $I_FATIGUE) / 100) . "/" . $I_CAVALERIE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-xs-12 col-sm-4 col-md-2\">";
		echo "<h4>";
		echo "<img alt=\"Artillerie\" title=\"Artillerie\" src='images/artillerie-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">". round($I_ARTILLERIE * (100 - $I_FATIGUE) / 100) . "/" . $I_ARTILLERIE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-xs-12 col-sm-4 col-md-2\">";
		echo "<h4>";
		echo "<img alt=\"Fatigue\" title=\"Fatigue\" src='images/fatigue-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">". $I_FATIGUE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-xs-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Exp&eacute;rience\" title=\"Exp&eacute;rience\" src='images/experience-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">". $I_EXPERIENCE ."</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-xs-12 col-sm-4\">";
		echo "<h4>";
		echo "<img alt=\"Mat&eacute;riel\" title=\"Mat&eacute;riel\" src='images/materiel-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">". $I_MATERIEL . "</span>";
		echo "</h4>";
		echo "</div>";
		
		echo "<div class=\"col-xs-12 col-sm-4 col-md-2\">";
		echo "<h4>";
		echo "<img alt=\"Ravitaillement\" title=\"Ravitaillement\" src='images/ravitaillement-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">". $I_RAVITAILLEMENT ."</span>";
		echo "</h4>";
		echo "</div>";

		echo "<div class=\"col-xs-12 col-sm-4 col-md-2\">";
		echo "<h4>";
		echo "<img alt=\"Moral\" title=\"Moral\" src='images/moral-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">".  $I_MORAL . "/" . $I_MORAL_MAX ."</span>";
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
                echo "<div class=\"col-xs-12\">\r\n";
                AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";

                echo "<div class=\"row\">\r\n";
                echo "<div class=\"col-xs-12\">\r\n";
                AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";

                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
                AfficherPatrouilles($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, $I_PATROUILLES_DISPONIBLES, $I_PATROUILLES_MAX);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";

                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
                AfficherDemandeConstruireFortifcation($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                AfficherDemandeEndommagerPont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                AfficherDemandeRavitaillementDirect($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                echo "</div>";
                echo "</div>";
            }
        echo "</div>";//echo "<div class=\"col-xs-12 col-md-9\">";
    echo "</div>";
}

function AfficherDepot($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $C_NIVEAU_DEPOT, $I_SOLDATS_RAVITAILLES, $ID_MODELE_PION, $B_DETRUIT, $S_POSITION, $I_TOUR, $pageNum)
{
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">";
        echo "<div class=\"col-md-3\">";
            echo "<div align=\"left\" class=\"hidden-xs hidden-sm\">";
                //Grand écran, on peut afficher une image
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

            echo "<div align=\"left\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE). "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";
        echo "</div>";
	
	echo "<div class=\"col-xs-12 col-md-9\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
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
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE)."</div>";;
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". $S_POSITION. "</div>";;

            echo "<div class=\"col-xs-12 col-sm-6\">";
                echo "<h4>";
                echo "<img alt=\"Niveau\" title=\"Niveau\" src='images/rang-26.png' />";
                echo "&nbsp;<span class=\"label label-default\">". $C_NIVEAU_DEPOT ."</span>";
                echo "</h4>";
            echo "</div>";
		
            echo "<div class=\"row\">";
		echo "<div class=\"col-xs-12 col-sm-6\">";
		echo "<h4>";
		echo "<img alt=\"Ravitaillement direct\" title=\"Ravitaillement direct\" src='images/ravitaillement-26.png' />";
		echo "&nbsp;<span class=\"label label-default\">";
		echo  $I_SOLDATS_RAVITAILLES;
		switch ($C_NIVEAU_DEPOT)
		{
			case 'A':
				echo "/50000</td>";
				break;
			case 'B':
				echo "/40000</td>";
				break;
			case 'C':
				echo "/30000</td>";
				break;
			default:
				echo "/20000</td>";
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
                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
		AfficherDemandeGenererConvoi($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, $C_NIVEAU_DEPOT);
                echo "</div>";
                echo "</div>";
                if ('A' == $C_NIVEAU_DEPOT)
                {
                    echo "<hr style=\"border-top: 1px solid white;\" />";
                    echo "<div class=\"row\">";
                    echo "<div class=\"col-xs-12\">";
                    AfficherDemandeLigneDeRavitaillement($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION);
                    AfficherDemandeReduireDepot($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION);
                    echo "</div>";
                    echo "</div>";
                }
            }
	echo "</div>";
    echo "</div>";
}

function nomFichierImage($str, $charset = 'utf-8')
{
    //echo $str;
    $str = htmlentities($str, ENT_NOQUOTES, $charset);
    //$str = htmlentities($str);
    //echo $str;
    $str = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str); // pour les ligatures e.g. '&oelig;'
    $str = preg_replace("#&[^;]+;#", '', $str); // supprime les autres caract�res
    $str = str_replace("'", "_", strtolower($str));

    return str_replace(' ', '_', strtolower($str));
  }

function AfficherQG($db, $FL_DEMMARAGE, $ID_UTILISATEUR, $ID_PARTIE, $ID_ROLE, 
                    $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $C_NIVEAU_HIERARCHIQUE, $ID_MODELE_PION, 
                    $I_TACTIQUE, $I_STRATEGIQUE, $S_POSITION, $ID_BATAILLE, $I_TOUR, 
                    $recepteur, $pageNum)
{
    //on recherche l'image associe au modele du general du joueur
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">\r\n";
        echo "<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-3\">\r\n";
        echo "<div class=\"row\"  style=\"vertical-align:middle\" >\r\n";
            echo "<div class=\"col-xs-12 col-sm-12 col-md-6 col-lg-12 hidden-xs hidden-sm\" align=\"center\">\r\n";
                echo "<img alt=\"". nomFichierImage($S_NOM) . ".jpg\" width=\"200\" src=\"images/" . nomFichierImage($S_NOM) . ".jpg\" />";
            echo "</div>";
            echo "<div class=\"col-xs-12 col-sm-12 col-md-6 col-lg-12\" align=\"center\">\r\n";
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
                echo "<hr class=\"col-lg-12 hidden-xs hidden-sm hidden-md\" style=\"border-top: 2px dashed white;\" />";
                echo "<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-12 hidden-xs\" align=\"left\">\r\n";
                $requete = "SELECT S_UNITES_VISIBLES ";
                $requete.="FROM tab_vaoc_role ";
                $requete.="WHERE tab_vaoc_role.ID_ROLE=" . $ID_ROLE . " AND tab_vaoc_role.ID_PARTIE=" . $ID_PARTIE;
                $res_unites_visibles = mysql_query($requete, $db);
                $row_unites_visibles = mysql_fetch_object($res_unites_visibles);
                echo $row_unites_visibles->S_UNITES_VISIBLES;
                echo "</div>\r\n";
            }
        echo "</div>\r\n";
        echo "</div>\r\n";
    //echo "</div>\r\n";

    echo "<div class=\"col-xs-12 col-sm-12 col-md-12 col-lg-9\">\r\n";
        echo "<div class=\"row\" style=\"vertical-align:middle\">\r\n";
            echo "<div class=\"col-xs-12 col-sm-12 col-md-6\">\r\n";
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
                    echo "<select id=\"liste_roles\" class=\"selectpicker\"  name=\"liste_roles\" onchange=\"javascript:callChangementRole();\" size=\"1\">";
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
                echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". $S_POSITION. "</div>";
                if ($ID_BATAILLE >= 0)
                {
                    echo "<div align=\"left\" class=\"hidden-md hidden-lg\"><a href=\"nojs.htm\" onclick=\"javascript:callBataille();return false;\">BATAILLE</a></div>";
                }
                echo "</div>";
            echo "<div class=\"col-xs-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Rang\" title=\"Rang\"src='images/rang-26.png' />&nbsp;<span class=\"label label-default\">". $C_NIVEAU_HIERARCHIQUE ."</span>";
                echo "</h4>";
            echo "</div>\r\n";
            echo "<div class=\"col-xs-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Tactique\" title=\"Tactique\" src='images/tactique-26.png' />&nbsp;<span class=\"label label-default\">";
                if ($I_TACTIQUE > 0)
                {
                    echo "+";
                }
                echo $I_TACTIQUE;
                echo "</span>";
                echo "</h4>";
            echo "</div>\r\n";
            echo "<div class=\"col-xs-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Strat&eacute;gique\" title=\"Strat&eacute;gique\" src='images/strategie-26.png' />&nbsp;<span class=\"label label-default\">" . $I_STRATEGIQUE . "</span>";
                echo "</h4>";
            echo "</div>\r\n";
        echo "</div>";
        //Liste des ordres envoyés dans une table triable gérée avec des divs
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
            echo "<div class=\"col-xs-12 col-sm-4 col-md-3\">\r\n";
            echo "<select class=\"selectpicker\" id=\"liste_recepteur\" name=\"liste_recepteur\" size=\"1\" onchange=\"javascript:changeRecepteur();return false;\">";
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
            echo "<div class=\"col-xs-12 col-sm-3 col-md-2\">";
            echo "<label class=\"control-label\">&nbsp;Depuis</label>";
            echo "</div>";
            echo "<div class=\"col-xs-12 col-sm-5 col-md-7\">";
            echo "<label class=\"control-label\">Ordre</label>";
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
            echo "<div class=\"col-xs-12\">";
            AfficherDemandeOrdreMouvement($db, $ORDRE_AUTORISE, $ID_PARTIE, $ID_PION, true);
            echo "</div>";
        echo "</div>";
        
        echo "<hr style=\"border-top: 1px solid white;\" />";
        echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
            AfficherDemandeArret($db, $ORDRE_AUTORISE, $ID_PARTIE, $ID_PION, true);
            echo "</div>";
        echo "</div>";
        //s'agit d'un general en chef ?
        $general_en_chef = IsGeneralEnChef($db, $ID_PARTIE, $ID_PION);
        if ($general_en_chef)
        {
            echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
            echo "<hr style=\"border-top: 1px solid white;\" />";
            AfficherDemandeTransfert($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION);
            echo "</div>";
            echo "</div>";
        }
    echo "</div>\r\n";
    echo "</div>\r\n";
}

function AfficherBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_ROLE, $ID_PION_PROPRIETAIRE, $ID_PION, $B_QG)
{
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
}

function AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $ID_ROLE)
{
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
        echo "<img alt='" . $nomcarte . "' style='display: block;' align='middle' src='" . $nomcarte . "'/>";
    }
    else
    {
        echo "<div id='historique_" . $ID_PION . "' style='display: block;' align='center' >";
        echo "<a href=\"nojs.htm\" onclick=\"javascript:callCarte();return false;\">";
        echo "<img alt='carteHistorique' src='" . $repertoire . "/carterole_" . $ID_ROLE . ".png' />";
        echo "</a>";
        echo "</div>";
        
        echo "<div id='topographie_" . $ID_PION . "' style='display: none;' align='center'>";
        echo "<a href=\"nojs.htm\" onclick=\"javascript:callCarte();return false;\">";
        echo "<img alt='carteTopographique' src='" . $repertoire . "/carterole_" . $ID_ROLE . "_topographie.png' />";
        echo "</a>";
        echo "</div>";
        
        echo "<div id='zoom_" . $ID_PION . "' style='display: none;' align='center'>";
        echo "<a href=\"nojs.htm\" onclick=\"javascript:callCarte();return false;\">";
        echo "<img alt='carteZoom' src='" . $repertoire . "/carterole_" . $ID_ROLE . "_zoom.png' />";
        echo "</a>";
        echo "</div>";

        echo "<div id='film_" . $ID_PION . "' style='display: none;' align='center'>";
        $srcFilmRole = $repertoire . "/filmrole_" . $ID_ROLE . "_zoom.gif";
        $idFilmRole = "film_role_".$ID_ROLE;
        echo "<a href=\"nojs.htm\" onclick=\"javascript:reloadFilm('" . $idFilmRole . "','".$srcFilmRole."');return false;\">";        
        echo "<img id='".$idFilmRole."' alt='film' src='" . $srcFilmRole . "' />";
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
}

//Renvoie le nouveau numero de l'ordre suivant, ajoute 1 a 
function AjouterIDOrdre($db, $ID_PARTIE)
{
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

    return $nouvelOrdre;
}

// ER = 'E' pour ordres envoyes, 'R' pour ordres recus
// Utilise pour la page de cinematique
  function TraduireOrdre($db, $ID_PARTIE, $I_TYPE, $ID_PION, $I_HEURE, $I_DUREE, $ID_NOM_LIEU, $I_DISTANCE, $I_DIRECTION, $ID_BATAILLE, $S_MESSAGE, $ER)
  {
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
    return $s_retour;
}

function AfficherPontonnier($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $ID_MODELE_PION, $B_DETRUIT, $S_POSITION, $I_TOUR, $pageNum)
{
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">";
	echo "<div class=\"col-md-3\">";
            echo "<div align=\"left\" class=\"hidden-xs hidden-sm\">";
		//Grand écran, on peut afficher une image
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
            echo "<div align=\"left\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE). "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";
	echo "</div>";
	echo "<div class=\"col-xs-12 col-md-9\">";
            echo "<div class=\"row\">";
            echo "<div class=\"col-xs-12\">";
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
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE)."</div>";;
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". $S_POSITION. "</div>";;

            //derniers ordres
            $I_PATROUILLES_DISPONIBLES = 0; //pour le passage par reference
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, false, $I_PATROUILLES_DISPONIBLES, $pageNum, -1);

            //ordres disponibles
            if (0 == $B_DETRUIT)
            {
                $FL_ACTIF = $FL_DEMMARAGE;

                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
                AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, true);
                AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";
                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
                AfficherDemandeConstruirePont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                AfficherDemandeReparerPont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                AfficherDemandeEndommagerPont($db, $FL_ACTIF, $ID_PARTIE, $ID_PION);
                echo "</div>";
                echo "</div>";
            }
	echo "</div>";
    echo "</div>";
}

function RechercheDernierOrdreSuivable($db, $ID_PARTIE, $ID_PION, $I_TOUR)
{
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
    return $id_dernier_ordre;
}

function MiseAjourOrdreSuivant($db, $ID_PARTIE, $ID_ORDRE, $ID_ORDRE_SUIVANT)
{
    $requete = "UPDATE tab_vaoc_ordre SET ID_ORDRE_SUIVANT = " . $ID_ORDRE_SUIVANT;
    $requete.=" WHERE tab_vaoc_ordre.ID_ORDRE=" . $ID_ORDRE;
    $requete.=" AND ID_PARTIE=" . $ID_PARTIE;
    mysql_query($requete, $db);
}

function IsGeneralEnChef($db, $ID_PARTIE, $ID_PION)
{
    $requete = "SELECT ID_PION_PROPRIETAIRE";
    $requete.=" FROM tab_vaoc_pion";
    $requete.=" WHERE tab_vaoc_pion.ID_PION=" . $ID_PION;
    $requete.=" AND ID_PARTIE=" . $ID_PARTIE;
    $res_general_en_chef = mysql_query($requete, $db);
    $row_general_en_chef = mysql_fetch_object($res_general_en_chef);
    return $row_general_en_chef->ID_PION_PROPRIETAIRE == $ID_PION;
}

function AfficherConvoi($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $ID_MODELE_PION, $B_RENFORT, $B_BLESSES, $B_PRISONNIERS, $I_INFANTERIE, $I_CAVALERIE, $I_ARTILLERIE, $I_EXPERIENCE, $S_POSITION, $ID_BATAILLE, $I_TOUR, $numPage)
{
    //on recherche l'image associe a l'unite
    //image differente suivant qu'il s'agit d'un convoi de prisonniers, de blesses ou de renfort
    $nom_invariant = "";
    if ($B_BLESSES == 1)
    {
        $NOM_IMAGE = "S_IMAGE_CONVOI_BLESSES";
        $nom_invariant = "Blessés ";
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
            echo "<div align=\"left\" class=\"hidden-xs hidden-sm\">\r\n";
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

            echo "<div align=\"left\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE). "</div>";
            echo "<div align=\"left\">". $S_POSITION. "</div>";
	echo "</div>";

	echo "<div class=\"col-xs-12 col-md-9\">\r\n";
            echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
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
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE)."</div>\r\n";
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". $S_POSITION. "</div>\r\n";;

            echo "<div class=\"row\">\r\n";
                echo "<div class=\"col-xs-12 col-sm-4\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Infanterie\" title=\"Infanterie\" src='images/infanterie-26.png' />";
                echo "&nbsp;<span class=\"label label-default\">". $I_INFANTERIE ."</span>";
                echo "</h4>";
                echo "</div>\r\n";

                echo "<div class=\"col-xs-12 col-sm-4\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Cavalerie\" title=\"Cavalerie\" src='images/cavalerie-26.png' />";
                echo "&nbsp;<span class=\"label label-default\">". $I_CAVALERIE ."</span>";
                echo "</h4>";
                echo "</div>\r\n";

                echo "<div class=\"col-xs-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Artillerie\" title=\"Artillerie\" src='images/artillerie-26.png' />";
                echo "&nbsp;<span class=\"label label-default\">". $I_ARTILLERIE ."</span>";
                echo "</h4>";
                echo "</div>\r\n";

                echo "<div class=\"col-xs-12 col-sm-4 col-md-2\">\r\n";
                echo "<h4>";
                echo "<img alt=\"Exp&eacute;rience\" title=\"Exp&eacute;rience\" src='images/experience-26.png' />";
                echo "&nbsp;<span class=\"label label-default\">". $I_EXPERIENCE ."</span>";
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
            echo "<div class=\"col-xs-12\">\r\n";
            AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, ($B_BLESSES == 1) || ($B_PRISONNIERS == 1));
            AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
            echo "</div>";
            echo "</div>";

            if ($B_RENFORT == 1)
            {
                echo "<hr style=\"border-top: 1px solid white;\" />";
                echo "<div class=\"row\">\r\n";
                echo "<div class=\"col-xs-12\">\r\n";
                AfficherDemandeRenforcer($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE, 0, -1, $ID_MODELE_PION);
                echo "</div>";
                echo "</div>";
            }
	echo "</div>\r\n";
    echo "</div>\r\n";
}

function AfficherConvoiDepot($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, $S_NOM, $S_ORDRE_COURANT, $ID_MODELE_PION, $B_RENFORT, $C_NIVEAU_DEPOT, $ID_DEPOT_SOURCE, $B_DETRUIT, $S_POSITION, $I_TOUR, $pageNum)
{
    if (1 == $B_DETRUIT)
    {
        return;    
    }
    echo "<div class=\"row\" id=\"tableau_pion" . $ID_PION . "\">\r\n";
    echo "<div class=\"col-md-3\">";
        echo "<div align=\"left\" class=\"hidden-xs hidden-sm\">";
        //Grand écran, on peut afficher une image
        echo "<img alt='unite' title=\"unite\" width=\"200\"  src='images/" . ImageModele($db, $ID_PARTIE, $ID_MODELE_PION, "S_IMAGE_CONVOI_DEPOT") . "'/>";
        echo "</div>";
        echo "<div align=\"left\">";
        //Cela n'a pas de sens d'afficher un engagement en bataille, de toute facon, il ne pourra rien faire a cause
        //de la proximite des ennemis alors pourquoi ne tenterait-il pas de s'enfuir pendant ce temps la ?
        AfficherCarte($db, $ID_PARTIE, $ID_PION_PROPRIETAIRE, $ID_PION, -1);
        echo "</div>";
        echo "<div align=\"left\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE). "</div>";
        echo "<div align=\"left\">". $S_POSITION. "</div>";
    echo "</div>";
    echo "<div class=\"col-xs-12 col-md-9\">";
            echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
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
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". DateDernierMessage($db, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE)."</div>";;
            echo "<div align=\"left\" class=\"hidden-md hidden-lg\">". $S_POSITION. "</div>";;

            //derniers ordres
            $I_PATROUILLES_DISPONIBLES = 0; //pour le passage par reference
            echo AfficherOrdre($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $I_TOUR, false, $I_PATROUILLES_DISPONIBLES, $pageNum, -1);

            //ordres disponibles
                $FL_ACTIF = $FL_DEMMARAGE;

                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
                    AfficherDemandeOrdreMouvement($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, true);
                    AfficherDemandeArret($db, $FL_ACTIF, $ID_PARTIE, $ID_PION, false);
                echo "</div>";
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white;\" />";
                echo "<div class=\"row\">";
                echo "<div class=\"col-xs-12\">";
                if ($B_RENFORT == 1 || $C_NIVEAU_DEPOT == 'D')
                {
                        AfficherDemandeRenforcer($db, $FL_DEMMARAGE, $ID_PARTIE, $ID_PION, $ID_PION_PROPRIETAIRE, 1, $ID_DEPOT_SOURCE, $ID_MODELE_PION);
                        echo "</div>";
                        echo "</div>";
                        echo "<hr style=\"border-top: 1px solid white;\" />";
                        echo "<div class=\"row\">";
                        echo "<div class=\"col-xs-12\">";
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
}
</script>
