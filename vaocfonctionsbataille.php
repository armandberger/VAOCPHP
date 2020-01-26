<script language="php">
/* * * Ensemble des fonctions d'affichage utilis�es par toutes les pages VAOC ** */
//Constantes
define("SANS_PROPRIETAIRE", 6);
define("NB_COLS_UNITES", 10);
define("NB_COLS_QG", 7);
define("ORDRE_MOUVEMENT", 1);
define("ORDRE_COMBAT", 2);
define("ORDRE_RETRAITE", 3);
define("ORDRE_MESSAGER", 5);
define("ORDRE_PATROUILLE", 4);
define("ORDRE_ENGAGEMENT", 14); //uniquement dans vaocfonctionsbataille
define("ORDRE_ETABLIRDEPOT",15);
define("ORDRE_RETRAIT",16);//uniquement dans vaocfonctionsbataille

define("NB_MESSAGES_MAX", 5);

//fonctions pour l'affichage
//Horizontal
// | 0 | 1 | 2 |
// | 3 | 4 | 5 |
// Vertical
// | 3 | 0 |
// | 4 | 1 |
// | 5 | 2 |
function AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $ID_PION_ROLE, $ID_LEADER, $I_TOUR, $I_ZONE, $ID_VICTOIRE)
{
//echo "ID_VICTOIRE=".$ID_VICTOIRE;
//echo "ID_NATION_JOUEUR".$ID_NATION_JOUEUR;
//echo "ID_NATION".$ID_NATION;

    $i_engagement = 1;
    //si le joueur donne un ordre d'engagement a ce tour, on doit l'afficher
    if ($ID_NATION_JOUEUR == $ID_NATION)
    {
        $requete = "SELECT tab_vaoc_ordre.I_ENGAGEMENT ";
        $requete.="FROM tab_vaoc_ordre ";
        $requete.="WHERE tab_vaoc_ordre.ID_PARTIE=" . $ID_PARTIE;
        $requete.=" AND I_TOUR=" . $I_TOUR;
        $requete.=" AND I_TYPE=" . ORDRE_ENGAGEMENT;
        $requete.=" AND ID_BATAILLE=" . $ID_BATAILLE;
        $requete.=" AND I_ZONE_BATAILLE=" . $I_ZONE;
        //$requete.=" AND tab_vaoc_ordre.ID_PION = " . $ID_PION_ROLE;
        //echo $requete."<br/>";
        $res_ordres_engagement = mysql_query($requete, $db);
        if ((false == empty($res_ordres_engagement) && mysql_num_rows($res_ordres_engagement) > 0))
        {
            $row = mysql_fetch_object($res_ordres_engagement);
            $i_engagement = $row->I_ENGAGEMENT;
            //echo "i_engagement=".$i_engagement;
        }
    }

    $requete = "SELECT I_ENGAGEMENT_" . $I_ZONE . " AS I_ENGAGEMENT";
    $requete.=" FROM tab_vaoc_bataille ";
    $requete.="WHERE ID_PARTIE=" . $ID_PARTIE . " AND ID_BATAILLE=" . $ID_BATAILLE;
    //echo $requete."=REQUETE<br/>";
    $res = mysql_query($requete, $db);
    $row = mysql_fetch_object($res);
    $i_engagement_minimum = $row->I_ENGAGEMENT;
    //echo "i_engagement_minimum=".$i_engagement_minimum;

    //echo "id_leader=".$ID_LEADER." id_pion_role=".$ID_PION_ROLE;
    $id_chaine = "id_engagement" . $I_ZONE;
    printf("<select id=\"%s\" name=\"%s\" onchange=\"javascript:callChangementEngagement();\" size=1", $id_chaine, $id_chaine);
    if ($ID_VICTOIRE >= 0 || $ID_LEADER <> $ID_PION_ROLE)
    {
        echo " disabled ";
    }
    echo "/>";

    if ($i_engagement_minimum < 2)
    {
        echo "<option";
        if (1 == $i_engagement)
        {
            echo " selected=\"selected\"";
        }
        echo" value=\"1\">Engagement r&eacute;duit</option>";
    }

    if ($i_engagement_minimum < 3)
    {
        echo "<option";
        if (2 == $i_engagement)
        {
            echo " selected=\"selected\"";
        }
        echo" value=\"2\">Engagement standard</option>";
    }

    echo "<option";
    if (3 == $i_engagement)
    {
        echo " selected=\"selected\"";
    }
    echo" value=\"3\">Engagement maximum</option>";

    echo "</select>";
}

function AfficherBatailleHorizontale($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE)
{
    $requete = "SELECT S_TERRAIN0, S_TERRAIN1, S_TERRAIN2, S_TERRAIN3, S_TERRAIN4, S_TERRAIN5";
    $requete.=", S_COULEURTERRAIN0, S_COULEURTERRAIN1, S_COULEURTERRAIN2, S_COULEURTERRAIN3, S_COULEURTERRAIN4, S_COULEURTERRAIN5";
    $requete.=", S_OBSTACLE0, S_OBSTACLE1, S_OBSTACLE2, S_COULEUROBSTACLE0, S_COULEUROBSTACLE1, S_COULEUROBSTACLE2";
    $requete.=", ID_NATION_012, ID_NATION_345, ID_LEADER_012, ID_LEADER_345";
    $requete.=", S_COMBAT_0, S_COMBAT_1, S_COMBAT_2, S_COMBAT_3, S_COMBAT_4, S_COMBAT_5";    
    $requete.=" FROM tab_vaoc_bataille ";
    $requete.="WHERE ID_PARTIE=" . $ID_PARTIE . " AND ID_BATAILLE=" . $ID_BATAILLE;
    //echo $requete."<br/>";
    $res = mysql_query($requete, $db);
    $row = mysql_fetch_object($res);
    //echo "<div>AfficherBatailleHorizontale:ID_NATION_JOUEUR=".$ID_NATION_JOUEUR."</div>";
    //echo "<div>AfficherBatailleHorizontale:ID_VICTOIRE=".$ID_VICTOIRE."</div>";

    echo "<h2>R&eacute;serves</h2>";
    AfficherUnitesEnReserve($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE);
    echo "<h2 style='text-align: center;'>";
    AfficherCommandantEnChef($db, $ID_PARTIE, $ID_BATAILLE, $row->ID_LEADER_012, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE);
    echo "</h2>";

    echo "<table summary=\"bataille horizontale\" width=\"100%\" class=\"bataille\">";
    echo "<tr class='bataille' >";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN0 . "; ' title='" . $row->S_TERRAIN0 . "'>";
    echo "<br/>";
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $row->ID_LEADER_012, $I_TOUR, 0, $ID_VICTOIRE);
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, 0, $ID_VICTOIRE);
    echo "<span class='bordurecombat'>".$row->S_COMBAT_0."</span>";
    echo "</td>";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN1 . "; ' title='" . $row->S_TERRAIN1 . "'>";
    echo "<br/>";
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $row->ID_LEADER_012, $I_TOUR, 1, $ID_VICTOIRE);
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, 1, $ID_VICTOIRE);
    echo "<span class='bordurecombat'>".$row->S_COMBAT_1."</span>";
    echo "</td>";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN2 . "; ' title='" . $row->S_TERRAIN2 . "'>";
    echo "<br/>";
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $row->ID_LEADER_012, $I_TOUR, 2, $ID_VICTOIRE);
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, 2, $ID_VICTOIRE);
    echo "<span class='bordurecombat'>".$row->S_COMBAT_2."</span>";
    echo "</td>";
    echo "</tr>";
    echo "<tr class='bataille' >";
    echo "<td class='bataille' style='width:100px; background-color: " . $row->S_COULEUROBSTACLE0 . "; ' title='" . $row->S_OBSTACLE0 . "'>";
    echo "&nbsp;</td>";
    echo "<td class='bataille' style='width:100px; background-color: " . $row->S_COULEUROBSTACLE1 . "; ' title='" . $row->S_OBSTACLE1 . "'>";
    echo "&nbsp;</td>";
    echo "<td class='bataille' style='width:100px; background-color: " . $row->S_COULEUROBSTACLE2 . "; ' title='" . $row->S_OBSTACLE2 . "'>";
    echo "&nbsp;</td>";
    echo "</tr>";
    echo "<tr class='bataille' >";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN3 . "; vertical-align:top; ' title='" . $row->S_TERRAIN3 . "'>";
    echo "<span class='bordurecombat'>".$row->S_COMBAT_3."</span>";
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, 3, $ID_VICTOIRE);
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $row->ID_LEADER_345, $I_TOUR, 3, $ID_VICTOIRE);
    echo "<br/>&nbsp";
    echo "</td>";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN4 . "; vertical-align:top; ' title='" . $row->S_TERRAIN4 . "'>";
    echo "<span class='bordurecombat'>".$row->S_COMBAT_4."</span>";
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, 4, $ID_VICTOIRE);
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $row->ID_LEADER_345, $I_TOUR, 4, $ID_VICTOIRE);
    echo "<br/>&nbsp";
    echo "</td>";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN5 . "; vertical-align:top; ' title='" . $row->S_TERRAIN5 . "'>";
    echo "<span class='bordurecombat'>".$row->S_COMBAT_5."</span>";
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, 5, $ID_VICTOIRE);
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $row->ID_LEADER_345, $I_TOUR, 5, $ID_VICTOIRE);
    echo "<br/>&nbsp";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
    echo "<h2 style='text-align: center;'>";
    AfficherCommandantEnChef($db, $ID_PARTIE, $ID_BATAILLE, $row->ID_LEADER_345, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE);
    echo "</h2>";
    echo "<h2>R&eacute;serves</h2>";
    AfficherUnitesEnReserve($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE);
}

// | 3 | 0 |
// | 4 | 1 |
// | 5 | 2 |
function AfficherBatailleVerticale($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE)
{
    $requete = "SELECT S_TERRAIN0, S_TERRAIN1, S_TERRAIN2, S_TERRAIN3, S_TERRAIN4, S_TERRAIN5";
    $requete.=", S_COULEURTERRAIN0, S_COULEURTERRAIN1, S_COULEURTERRAIN2, S_COULEURTERRAIN3, S_COULEURTERRAIN4, S_COULEURTERRAIN5";
    $requete.=", S_OBSTACLE0, S_OBSTACLE1, S_OBSTACLE2, S_COULEUROBSTACLE0, S_COULEUROBSTACLE1, S_COULEUROBSTACLE2";
    $requete.=", ID_NATION_012, ID_NATION_345, ID_LEADER_012, ID_LEADER_345";
    $requete.=", S_COMBAT_0, S_COMBAT_1, S_COMBAT_2, S_COMBAT_3, S_COMBAT_4, S_COMBAT_5";    
    $requete.=" FROM tab_vaoc_bataille ";
    $requete.="WHERE ID_PARTIE=" . $ID_PARTIE . " AND ID_BATAILLE=" . $ID_BATAILLE;
    //echo $requete."<br/>";
    $res = mysql_query($requete, $db);
    $row = mysql_fetch_object($res);
    //echo "<div><b>AfficherBatailleVerticale:ID_NATION_JOUEUR=".$ID_NATION_JOUEUR."</b></div>";
    //echo "<div>AfficherBatailleVerticale:ID_VICTOIRE=".$ID_VICTOIRE."</div>";
    
    echo "<table summary=\"bataille verticale\" width=\"100%\" class=\"bataille\">";
    echo "<tr class='bataille' >";
    echo "<td  class='bataille' rowspan='3'>";
    echo "<h2 style='text-align: center;'>";
    AfficherCommandantEnChef($db, $ID_PARTIE, $ID_BATAILLE, $row->ID_LEADER_345, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE);
    echo "</h2>";
    echo "<h2>R&eacute;serves</h2>";
    AfficherUnitesEnReserve($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE);
    echo "</td>";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN3 . "; vertical-align: bottom;' title='" . $row->S_TERRAIN3 . "'><br/>";
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $row->ID_LEADER_345, $I_TOUR, 3, $ID_VICTOIRE);
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, 3, $ID_VICTOIRE);
    echo "<span class='bordurecombat'>".$row->S_COMBAT_3."</span>";
    echo "</td>";
    echo "<td class='bataille' style='width:100px; background-color: " . $row->S_COULEUROBSTACLE0 . ";' title='" . $row->S_OBSTACLE0 . "'>";
    echo "<img src=\"images/transparent.png\" width=20 height=1>"; //pour indiquer une taille minimale
    echo "</td>";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN0 . "; vertical-align: bottom;' title='" . $row->S_TERRAIN0 . "'><br/>";
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $row->ID_LEADER_012, $I_TOUR, 0, $ID_VICTOIRE);
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, 0, $ID_VICTOIRE);
    echo "<span class='bordurecombat'>".$row->S_COMBAT_0."</span>";
    echo "</td>";
    echo "<td class='bataille' rowspan='3'>";
    echo "<h2 style='text-align: center;'>";
    AfficherCommandantEnChef($db, $ID_PARTIE, $ID_BATAILLE, $row->ID_LEADER_012, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE);
    echo "</h2>";
    echo "<h2>R&eacute;serves</h2>";
    AfficherUnitesEnReserve($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE);
    echo "</td>";
    echo "</tr>";
    echo "<tr class='bataille' >";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN4 . "; vertical-align: bottom;' title='" . $row->S_TERRAIN4 . "'><br/>";
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $row->ID_LEADER_345, $I_TOUR, 4, $ID_VICTOIRE);
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, 4, $ID_VICTOIRE);
    echo "<span class='bordurecombat'>".$row->S_COMBAT_4."</span>";
    echo "</td>";
    echo "<td class='bataille' style='width:100px; background-color: " . $row->S_COULEUROBSTACLE1 . "; ' title='" . $row->S_OBSTACLE1 . "'>";
    echo "</td>";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN1 . "; vertical-align: bottom;' title='" . $row->S_TERRAIN1 . "'><br/>";
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $row->ID_LEADER_012, $I_TOUR, 1, $ID_VICTOIRE);
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, 1, $ID_VICTOIRE);
    echo "<span class='bordurecombat'>".$row->S_COMBAT_1."</span>";
    echo "</td>";
    echo "</tr>";
    echo "<tr class='bataille' >";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN5 . "; vertical-align: bottom;' title='" . $row->S_TERRAIN5 . "'><br/>";
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $row->ID_LEADER_345, $I_TOUR, 5, $ID_VICTOIRE);
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_345, $ID_PION_ROLE, $I_TOUR, 5, $ID_VICTOIRE);
    echo "<span class='bordurecombat'>".$row->S_COMBAT_5."</span>";
    echo "</td>";
    echo "<td class='bataille' style='width:100px; background-color: " . $row->S_COULEUROBSTACLE2 . ";' title='" . $row->S_OBSTACLE2 . "'>";
    echo "</td>";
    echo "<td class='bataille' style='background-color: " . $row->S_COULEURTERRAIN2 . "; vertical-align: bottom;' title='" . $row->S_TERRAIN2 . "'><br/>";
    AfficherEngagement($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $row->ID_LEADER_012, $I_TOUR, 2, $ID_VICTOIRE);
    AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $row->ID_NATION_012, $ID_PION_ROLE, $I_TOUR, 2, $ID_VICTOIRE);
    echo "<span class='bordurecombat'>".$row->S_COMBAT_2."</span>";
    echo "</td>";
    echo "</tr>";
    echo "</table>";
}

function AfficherUnitesEnReserve($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE)
{
    if ($ID_VICTOIRE >= 0)
    {
        return AfficherUnitesEnReserveVictoire($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION);
    }
    $requete = "SELECT tab_vaoc_bataille_pions.ID_PION, tab_vaoc_bataille_pions.B_EN_DEFENSE ";
    $requete.=" FROM tab_vaoc_bataille_pions, tab_vaoc_pion ";
    $requete.="WHERE tab_vaoc_bataille_pions.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE=" . $ID_BATAILLE;
    $requete.=" AND tab_vaoc_bataille_pions.ID_NATION=" . $ID_NATION;
    //$requete.=" AND tab_vaoc_bataille_pions.B_ENGAGEE=0";
    $requete.=" AND tab_vaoc_bataille_pions.ID_PION=tab_vaoc_pion.ID_PION";
    $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
    $requete.=" AND tab_vaoc_pion.I_ZONE_BATAILLE=-1";
    $requete.=" AND tab_vaoc_pion.B_DETRUIT=0";
    //echo $requete."<br/>";
    $res_pions = mysql_query($requete, $db);

    //les unit�s mises en bataille � ce tour et qui ne sont pas celles du joueur doivent �tre affich�es en r�serve
    //unit�s mises en bataille � ce tour
    /* -> vu qu'elles ne sont pas engag�es, de toute fa�on, on ne les voit pas engag�es, donc le code qui suit ne sert � rien, et, en plus
      il affiche les unit�s mis au front � ce tour, en double !
      if ($ID_NATION_JOUEUR<>$ID_NATION)
      {
      $requete="SELECT tab_vaoc_ordre.ID_PION, tab_vaoc_bataille_pions.B_EN_DEFENSE ";
      $requete.="FROM tab_vaoc_ordre, tab_vaoc_bataille_pions ";
      $requete.="WHERE tab_vaoc_ordre.ID_PARTIE=".$ID_PARTIE;
      $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE=".$ID_PARTIE;
      $requete.=" AND I_TOUR=".$I_TOUR;
      $requete.=" AND I_TYPE=".ORDRE_COMBAT;
      $requete.=" AND tab_vaoc_bataille_pions.ID_NATION=".$ID_NATION;
      //$requete.=" AND I_ZONE_BATAILLE=".$I_ZONE;
      $requete.=" AND tab_vaoc_ordre.ID_PION = tab_vaoc_bataille_pions.ID_PION";
      echo $requete."<br/>";
      $res_ordres_combat = mysql_query($requete,$db);
      }
     */

    //Les unit� qui ont �t� mises en retrait � ce tour doivent �tre affich�es en r�serve
    if ($ID_NATION_JOUEUR === $ID_NATION)
    {
        $requete="SELECT tab_vaoc_ordre.ID_PION, tab_vaoc_bataille_pions.B_EN_DEFENSE ";
        $requete.="FROM tab_vaoc_ordre, tab_vaoc_bataille_pions ";
        $requete.="WHERE tab_vaoc_ordre.ID_PARTIE=".$ID_PARTIE;
        $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE=".$ID_PARTIE;
        $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE=" . $ID_BATAILLE;
        $requete.=" AND I_TOUR=".$I_TOUR;
        $requete.=" AND I_TYPE=".ORDRE_RETRAIT;
        $requete.=" AND tab_vaoc_bataille_pions.ID_NATION=".$ID_NATION;
        $requete.=" AND tab_vaoc_ordre.ID_PION = tab_vaoc_bataille_pions.ID_PION";
        $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE= tab_vaoc_ordre.ID_BATAILLE";
        //echo $requete."<br/>";
        $res_ordres_retrait = mysql_query($requete,$db);
    }
      
    echo "<table summary='reserve' id='reserve' cellpadding='10px' cellspacing='10px' style='border-style: none; border-width: 0px;'>";
    echo "<tr>";
    if (0 === mysql_num_rows($res_pions) && (true == empty($res_ordres_retrait) || 0 == mysql_num_rows($res_ordres_retrait)))
    {
        echo "<td><div class='unite'>Aucune unit&eacute; en r&eacute;serve</div></td>";
    }
    else
    {
        while ($row = mysql_fetch_object($res_pions))
        {
            //on v�rifie si un ordre de combat n'a pas �t� donn� dans ce tour
            $requete = "SELECT ID_PION FROM tab_vaoc_ordre WHERE ID_PARTIE=" . $ID_PARTIE . " AND I_TOUR=" . $I_TOUR . " AND I_TYPE=" . ORDRE_COMBAT . " AND ID_PION=" . $row->ID_PION;
            //echo $requete."<br/>";
            $res_ordre_combat = mysql_query($requete, $db);

            if ((0 == mysql_num_rows($res_ordre_combat) && $ID_NATION_JOUEUR == $ID_NATION) ||
                    ($ID_NATION_JOUEUR <> $ID_NATION))
            {
                echo "<td>";
                AfficherUnite($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $row->ID_PION, $ID_PION_ROLE, $I_TOUR, $row->B_EN_DEFENSE, 0);
                echo "</td>";
            }
        }
        /* voir note au-dessus*/
        if ($ID_NATION_JOUEUR === $ID_NATION)
        {
            while($row = mysql_fetch_object($res_ordres_retrait))
            {
                echo "<td>";
                AfficherUnite($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $row->ID_PION, $ID_PION_ROLE, $I_TOUR, $row->B_EN_DEFENSE, 1);
                echo "</td>";
            }
        }
    }
    echo "</tr>";
    echo "</table>";
}

function AfficherUnitesEngagesEnBataille($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $ID_PION_ROLE, $I_TOUR, $I_ZONE, $ID_VICTOIRE)
{
    if ($ID_VICTOIRE >= 0)
    {
        return AfficherUnitesEngagesEnBatailleVictoire($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $I_ZONE);
    }
    //echo "ID_PION_ROLE=".$ID_PION_ROLE;
    //Unit�s d�j� en bataille
    $requete = "SELECT tab_vaoc_pion.S_NOM, tab_vaoc_bataille_pions.ID_PION, tab_vaoc_bataille_pions.ID_NATION, tab_vaoc_bataille_pions.B_ENGAGEE, tab_vaoc_bataille_pions.B_EN_DEFENSE ";
    $requete.=" FROM tab_vaoc_bataille_pions, tab_vaoc_pion ";
    $requete.="WHERE tab_vaoc_bataille_pions.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE=" . $ID_BATAILLE;
    $requete.=" AND tab_vaoc_pion.I_ZONE_BATAILLE=" . $I_ZONE;
    //$requete.=" AND tab_vaoc_bataille_pions.I_ZONE_BATAILLE_ENGAGEMENT=" . $I_ZONE;
    //en  prenant la zone de bataille du pion, quand le chef de l'unit� engag� n'est pas pr�sent
    //on ne voit pas les unit�s sur le terrain, probl�me quand l'autre chef, lui est pr�sent
    //et ne voit pas ses adversaires !
    //$requete.=" AND tab_vaoc_pion.I_ZONE_BATAILLE=".$I_ZONE;
    $requete.=" AND tab_vaoc_bataille_pions.B_ENGAGEE=1";
    $requete.=" AND tab_vaoc_bataille_pions.ID_PION=tab_vaoc_pion.ID_PION";
    $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
    //echo $requete."<br/>";
    $res_pions = mysql_query($requete, $db);

    //unit�s mises en bataille � ce tour
    $nb_unites_retrait = 0;
    if ($ID_NATION_JOUEUR == $ID_NATION)
    {
        $requete = "SELECT tab_vaoc_ordre.ID_PION, tab_vaoc_bataille_pions.B_EN_DEFENSE ";
        $requete.="FROM tab_vaoc_ordre, tab_vaoc_bataille_pions ";
        $requete.="WHERE tab_vaoc_ordre.ID_PARTIE=" . $ID_PARTIE;
        $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE=" . $ID_PARTIE;
        $requete.=" AND I_TOUR=" . $I_TOUR;
        $requete.=" AND I_TYPE=" . ORDRE_COMBAT;
        $requete.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $ID_BATAILLE;
        $requete.=" AND I_ZONE_BATAILLE=" . $I_ZONE;
        $requete.=" AND tab_vaoc_ordre.ID_PION = tab_vaoc_bataille_pions.ID_PION";
        $requete.=" AND tab_vaoc_ordre.ID_BATAILLE = tab_vaoc_bataille_pions.ID_BATAILLE";
        //echo $requete."<br/>";
        $res_ordres_combat = mysql_query($requete, $db);

        //il faut retirer les unit�s mises en retrait � ce tour
        $requete="SELECT tab_vaoc_ordre.ID_PION ";
        $requete.="FROM tab_vaoc_ordre, tab_vaoc_bataille_pions ";
        $requete.="WHERE tab_vaoc_ordre.ID_PARTIE=".$ID_PARTIE;
        $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE=".$ID_PARTIE;
        $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE=" . $ID_BATAILLE;
        $requete.=" AND I_TOUR=".$I_TOUR;
        $requete.=" AND I_TYPE=".ORDRE_RETRAIT;
        $requete.=" AND tab_vaoc_bataille_pions.I_ZONE_BATAILLE_ENGAGEMENT=" . $I_ZONE;
        $requete.=" AND tab_vaoc_bataille_pions.ID_NATION=".$ID_NATION;
        $requete.=" AND tab_vaoc_ordre.ID_PION = tab_vaoc_bataille_pions.ID_PION";
        $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE= tab_vaoc_ordre.ID_BATAILLE";
        //echo $requete."<br/>";
        $res_ordres_retrait = mysql_query($requete,$db);
        $nb_unites_retrait = mysql_num_rows($res_ordres_retrait);
    }

    echo "<table summary='zone" . $I_ZONE . "' id='Table" . $I_ZONE . "' class='enbataille'>";
    echo "<tr>";
    if ($nb_unites_retrait == mysql_num_rows($res_pions) && (true == empty($res_ordres_combat) || 0 == mysql_num_rows($res_ordres_combat)))
    {
        echo "<td><div class='unite'>Aucune unit&eacute;</div></td>";
    }
    else
    {
        while ($row = mysql_fetch_object($res_pions))
        {
            $b_afficheUnite=true;
            if ($ID_NATION_JOUEUR == $ID_NATION)
            {
                //si l'unit� a donn� un ordre de retrait a ce tour, il ne fait pas l'afficher
                $requete="SELECT tab_vaoc_ordre.ID_PION";
                $requete.=" FROM tab_vaoc_ordre";
                $requete.=" WHERE tab_vaoc_ordre.ID_PARTIE=".$ID_PARTIE;
                $requete.=" AND I_TOUR=".$I_TOUR;
                $requete.=" AND I_TYPE=".ORDRE_RETRAIT;
                $requete.=" AND ID_PION=". $row->ID_PION;
                //echo $requete."<br/>";
                $res_ordres_retrait = mysql_query($requete,$db);
                if (mysql_num_rows($res_ordres_retrait)>0)
                {
                    $b_afficheUnite=false;
                }
            }
            if ($b_afficheUnite)
            {
                echo "<td>";
                AfficherUnite($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $row->ID_PION, $ID_PION_ROLE, $I_TOUR, $row->B_EN_DEFENSE, 1);
                echo "</td>";
            }
        }
        if ($ID_NATION_JOUEUR == $ID_NATION)
        {
            while ($row = mysql_fetch_object($res_ordres_combat))
            {
                echo "<td>";
                AfficherUnite($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $row->ID_PION, $ID_PION_ROLE, $I_TOUR, $row->B_EN_DEFENSE, 1);
                echo "</td>";
            }
        }
    }
    echo "</tr>";
    echo "</table>\r\n";
}

function CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, $I_ZONE_BATAILLE, $B_QG)
{
    //recherche des unites presentes sur le terrain car cela conditionne les ordres possibles pour l'unite
    //pour avoir le droit de mettre une unite sur les cotes,
    //il faut qu'il y ait au moins une unite combattantes au centre
    //il ne doit egalement pas y avoir plus de 4 unites par cote
    //pour les leaders, il doit pas y en avoir plus d'un par zone

    //Unites deja en bataille
    $requete = "SELECT tab_vaoc_bataille_pions.ID_PION ";
    $requete.=" FROM tab_vaoc_bataille_pions, tab_vaoc_pion ";
    $requete.=" WHERE tab_vaoc_bataille_pions.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE=" . $ID_BATAILLE;
    $requete.=" AND tab_vaoc_bataille_pions.B_ENGAGEE=1";
    $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
    $requete.=" AND tab_vaoc_bataille_pions.ID_PION=tab_vaoc_pion.ID_PION";
    $requete.=" AND (tab_vaoc_pion.I_MORAL_REEL>0 OR tab_vaoc_pion.B_QG>0)";    
    $requete.=" AND tab_vaoc_pion.I_ZONE_BATAILLE=". $I_ZONE_BATAILLE;
    $requete.=" AND tab_vaoc_pion.B_QG=".$B_QG; //uniquement les unit�s combattantes
    //echo $requete."<br/>";
    $res_pions = mysql_query($requete, $db);
    $nb_unites = mysql_num_rows($res_pions);
    while ($row_unite = mysql_fetch_object($res_pions))
    {
        //si l'unite a donne un ordre de retrait a ce tour, il ne faut pas la compter
        $requete="SELECT tab_vaoc_ordre.ID_PION";
        $requete.=" FROM tab_vaoc_ordre";
        $requete.=" WHERE tab_vaoc_ordre.ID_PARTIE=".$ID_PARTIE;
        $requete.=" AND I_TOUR=".$I_TOUR;
        $requete.=" AND I_TYPE=".ORDRE_RETRAIT;
        $requete.=" AND ID_PION=". $row_unite->ID_PION;
        //echo $requete."<br/>";
        $res_ordres_retrait = mysql_query($requete,$db);
        if (mysql_num_rows($res_ordres_retrait)>0)
        {
            $nb_unites--;
        }
    }

    $requeteordre = "SELECT tab_vaoc_ordre.ID_PION ";
    $requeteordre.="FROM tab_vaoc_ordre, tab_vaoc_pion ";
    $requeteordre.="WHERE tab_vaoc_ordre.ID_PARTIE=" . $ID_PARTIE;
    $requeteordre.=" AND tab_vaoc_ordre.I_TOUR=" . $I_TOUR;
    $requeteordre.=" AND tab_vaoc_ordre.I_TYPE=" . ORDRE_COMBAT;
    $requeteordre.=" AND tab_vaoc_ordre.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
    $requeteordre.=" AND tab_vaoc_ordre.ID_PION=tab_vaoc_pion.ID_PION";
    $requeteordre.=" AND tab_vaoc_ordre.ID_BATAILLE=" . $ID_BATAILLE;
    $requeteordre.=" AND (tab_vaoc_pion.I_MORAL_REEL>0 OR tab_vaoc_pion.B_QG>0)";
    $requeteordre.=" AND tab_vaoc_ordre.I_ZONE_BATAILLE=". $I_ZONE_BATAILLE;
    $requeteordre.=" AND tab_vaoc_pion.B_QG=".$B_QG; //uniquement les unites combattantes
    //echo $requeteordre."<br/>";
    $res_pions_ordre = mysql_query($requeteordre, $db);
    $nb_unites+=mysql_num_rows($res_pions_ordre);
    return $nb_unites;
}

function AfficherUnite($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $ID_PION, $ID_PION_ROLE, $I_TOUR, $B_EN_DEFENSE, $B_ENGAGEE)
{
    $requete = "SELECT tab_vaoc_pion.S_NOM, I_INFANTERIE_REEL, I_CAVALERIE_REEL, I_ARTILLERIE_REEL, I_MORAL_REEL, I_MORAL_MAX, I_FATIGUE_REEL";
    $requete.= " ,I_EXPERIENCE, I_TACTIQUE, I_STRATEGIQUE, B_QG, C_NIVEAU_HIERARCHIQUE, I_NIVEAU_FORTIFICATION, I_ZONE_BATAILLE ";
    $requete.= " ,I_MATERIEL, I_RAVITAILLEMENT ";  	
    $requete.=" FROM tab_vaoc_pion ";
    $requete.="WHERE tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_pion.ID_PION=" . $ID_PION;
    //echo $requete."<br/>";
    //echo "ID_NATION_JOUEUR=".$ID_NATION_JOUEUR." ID_NATION=".$ID_NATION."<br/>";

    $res_pion = mysql_query($requete, $db);
    $rowunite = mysql_fetch_object($res_pion);

    //recherche des leaders sur le combat
    $requete = "SELECT ID_LEADER_012, ID_LEADER_345, I_TOUR_DEBUT";
    $requete.=" FROM tab_vaoc_bataille ";
    $requete.="WHERE ID_PARTIE=" . $ID_PARTIE . " AND ID_BATAILLE=" . $ID_BATAILLE;
    //echo $requete."<br/>";
    $res_leader = mysql_query($requete, $db);
    $rowleader = mysql_fetch_object($res_leader);
    $iTourDebutBataille = $rowleader->I_TOUR_DEBUT;
    $b_reserve_artillerie = $rowunite->I_INFANTERIE_REEL==0 && $rowunite->I_CAVALERIE_REEL==0 && $rowunite->I_ARTILLERIE_REEL>0;

    //echo "ID_PION_ROLE=".$ID_PION_ROLE." ID_LEADER_012=".$rowleader->ID_LEADER_012." ID_LEADER_345=".$rowleader->ID_LEADER_345."<br/>";

    if ($rowunite->I_MORAL_REEL <= 0 && 0 == $rowunite->B_QG && false==$b_reserve_artillerie)
    {
        //unite en fuite
        //les reserves d'artillerie ne doivent pas etre marquée en fuite d'ou le test (I_INFANTERIE_REEL>0 || I_CAVALERIE_REEL>0)
        echo "<div class='unitefuite'";
    }
    else
    {
        if (0 == $rowunite->I_NIVEAU_FORTIFICATION)
        {
            if (0 == $B_EN_DEFENSE)
            {
                echo "<div class='unite'";
            }
            else
            {
                echo "<div class='unitedefense'";
            }
        }
        else
        {
            if (1 == $rowunite->I_NIVEAU_FORTIFICATION)
            {
                echo "<div class='uniteFortifcation1'";
            }
            else
            {
                echo "<div class='uniteFortifcationMax'";
            }
        }
    }
    
    if (0 == $rowunite->B_QG)
    {
        //c'est une unite
        if ($ID_NATION_JOUEUR == $ID_NATION)
        {
            echo " id='pion" . $ID_PION . "' onmouseover='simulerTitle(" . $ID_PION . "," . $rowunite->I_INFANTERIE_REEL . "," . $rowunite->I_CAVALERIE_REEL . "," . $rowunite->I_ARTILLERIE_REEL . "," . $rowunite->I_MORAL_REEL . "," . $rowunite->I_MORAL_MAX . "," . $rowunite->I_FATIGUE_REEL . "," . $rowunite->I_EXPERIENCE . ",". $rowunite->I_MATERIEL .",". $rowunite->I_RAVITAILLEMENT. ")' onmouseout='hideTitle()'>";
        }
        else
        {
            echo " id='pion" . $ID_PION . "' onmouseover='simulerTitle(" . $ID_PION . "," . $rowunite->I_INFANTERIE_REEL . "," . $rowunite->I_CAVALERIE_REEL . "," . $rowunite->I_ARTILLERIE_REEL . ",-1,-1," . $rowunite->I_FATIGUE_REEL . ")' onmouseout='hideTitle()'>";
        }
        echo $rowunite->S_NOM . "<br/>";

        if (($ID_NATION_JOUEUR <> $ID_NATION) ||
                (($rowleader->ID_LEADER_012 <> $ID_PION_ROLE) && ($rowleader->ID_LEADER_345 <> $ID_PION_ROLE)) ||
                ($rowunite->I_MORAL_REEL <= 0) || ($rowunite->I_MATERIEL <= 0) || ($rowunite->I_RAVITAILLEMENT <=0))
        {
            if ($rowunite->I_MORAL_REEL <= 0  && false==$b_reserve_artillerie)
            {
                echo "FUITE";
            }
            else
            {
                if (($rowunite->I_MATERIEL <= 0 || $rowunite->I_RAVITAILLEMENT <=0) 
                    && ($ID_NATION_JOUEUR == $ID_NATION))
                {
                    echo "INAPTE";
                }
            }
            echo "</div>\r\n";
            return; //pas d'ordre, soit elle n'est pas de sa nation soit on ne dirige pas le combat soit ne dispose d'aucun ravitaillement ou matériel
        }
    }
    else
    {
        //c'est un QG
        if ($ID_NATION_JOUEUR == $ID_NATION)
        {
            echo " id='pion" . $ID_PION . "' onmouseover='simulerTitleQG(" . $ID_PION . "," . $rowunite->I_TACTIQUE . "," . $rowunite->I_STRATEGIQUE . ",\"" . $rowunite->C_NIVEAU_HIERARCHIQUE . "\")' onmouseout='hideTitle()'>";
        }
        else
        {
            echo ">";
        }
        echo $rowunite->S_NOM . "<br/>";
        if (($ID_NATION_JOUEUR <> $ID_NATION) ||
                (($rowleader->ID_LEADER_012 <> $ID_PION_ROLE) && ($rowleader->ID_LEADER_345 <> $ID_PION_ROLE)))
        {
            echo "</div>\r\n";
            return; //pas d'ordre, soit elle n'est pas de sa nation soit on ne dirige pas le combat
        }
    }

    //recherche des unites presentes sur le terrain car cela conditionne les ordres possibles pour l'unite
    //pour avoir le droit de mettre une unite sur les cotes,
    //il faut qu'il y ait au moins une unite combattantes au centre
    //il ne doit egalement pas y avoir plus de 4 unites par cote
    //pour les leaders, il doit pas y en avoir plus d'un par zone
    //recherche de la nation de l'unite
    $requete = "SELECT tab_vaoc_modele_pion.ID_NATION ";
    $requete.=" FROM tab_vaoc_modele_pion, tab_vaoc_pion";
    $requete.=" WHERE tab_vaoc_modele_pion.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_modele_pion.ID_MODELE_PION=tab_vaoc_pion.ID_MODELE_PION";
    $requete.=" AND tab_vaoc_modele_pion.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
    $requete.=" AND tab_vaoc_pion.ID_PION=" . $ID_PION;
    //echo $requete."<br/>";
    $res_nation_pion = mysql_query($requete, $db);
    $row_nation_pion = mysql_fetch_object($res_nation_pion);

    //recherche des nations engagees dans la bataille
    $requete = "SELECT ID_NATION_012";
    $requete.=" FROM tab_vaoc_bataille ";
    $requete.="WHERE ID_PARTIE=" . $ID_PARTIE . " AND ID_BATAILLE=" . $ID_BATAILLE;
    $res_bataille = mysql_query($requete, $db);
    $row_bataille = mysql_fetch_object($res_bataille);
    //echo $requete."<br/>";
    //
    //on compte les unites presentes dans chaque zone
    if ($row_nation_pion->ID_NATION == $row_bataille->ID_NATION_012)
    {
        $nb_unites_gauche=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 2, 0);
        $nb_unites_gauche_QG=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 2, 1);

        $nb_unites_droite=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 0, 0);
        $nb_unites_droite_QG=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 0, 1);

        $nb_unites_centre=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 1, 0);
        $nb_unites_centre_QG=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 1, 1);
    }
    else
    {
        $nb_unites_gauche=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 3, 0);
        $nb_unites_gauche_QG=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 3, 1);

        $nb_unites_droite=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 5, 0);
        $nb_unites_droite_QG=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 5, 1);

        $nb_unites_centre=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 4, 0);
        $nb_unites_centre_QG=CompteUniteSurZone($db, $ID_PARTIE, $I_TOUR, $ID_BATAILLE, 4, 1);
    }
                    
    //echo "nb_unites_gauche=".$nb_unites_gauche." nb_unites_centre=".$nb_unites_centre." nb_unites_droite=".$nb_unites_droite."<br/>";
    //echo "nb_unites_gauche_QG=".$nb_unites_gauche_QG." nb_unites_centre_QG=".$nb_unites_centre_QG." nb_unites_droite_QG=".$nb_unites_droite_QG."<br/>";
    //affichage des ordres associes a l'unite
    //on recherche si un ordre a deja ete affecte a ce tour
    $requeteOrdre = "SELECT I_TYPE, I_ZONE_BATAILLE FROM tab_vaoc_ordre WHERE ID_PION = " . $ID_PION . " AND ID_PARTIE=" . $ID_PARTIE . " AND ID_BATAILLE=" . $ID_BATAILLE;
    $requeteOrdre.= " AND I_TYPE<>".ORDRE_ENGAGEMENT;
    if (($iTourDebutBataille - $I_TOUR) % 2 == 0)
    {
        $requeteOrdre.=" AND (I_TOUR=" . $I_TOUR . " OR I_TOUR=" . ($I_TOUR - 1) . ") ORDER BY I_TOUR DESC";
    }
    else
    {
        $requeteOrdre.=" AND I_TOUR=" . $I_TOUR;
    }

    //echo $requeteOrdre."<br/>";
    $res_ordre = mysql_query($requeteOrdre, $db);

    if (0 == mysql_num_rows($res_ordre))
    {
        if (1 == $B_ENGAGEE)
        {
            //une unite engagee ne peut faire qu'une retraite avec toutes les autres unites
            //-> en fait, c'est pas vrai, elle peut aussi revenir en reserve pour etre reengager aux tours suivants
                    //on ne peut annuler son positionnement que s'il n'y a pas de chef sur la zone et que l'on ait pas la derniere unite
                    //ou que qu'il n'y pas d'unite sur les cotes et que l'on est pas la derniere unite au centre
                    //echo "zone bataille=".$row->I_ZONE_BATAILLE;
                    
            if (    $rowunite->B_QG == 0 &&
                    ((($rowunite->I_ZONE_BATAILLE == 0 || $rowunite->I_ZONE_BATAILLE == 5) && $nb_unites_droite == 1 && $nb_unites_droite_QG == 1) ||
                    (($rowunite->I_ZONE_BATAILLE == 1 || $rowunite->I_ZONE_BATAILLE == 4) && $nb_unites_centre == 1 && ($nb_unites_centre_QG == 1 || $nb_unites_droite >= 1 || $nb_unites_gauche >= 1)) ||
                    (($rowunite->I_ZONE_BATAILLE == 2 || $rowunite->I_ZONE_BATAILLE == 3) && $nb_unites_gauche == 1 && $nb_unites_gauche_QG == 1)
                    ))
            {
                echo "<br/>";
            }
            else
            {
                echo "<input alt=\"retait\" id=\"retait" . $ID_PION . "\" name=\"retrait" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnRetrait.png\" onclick=\"javascript:callRetrait(" . $ID_PION . ",'G');\" />";
                echo "</div>\r\n";
            }
            return;
        }

        //echo "nb_unites_gauche=".$nb_unites_gauche." nb_unites_centre=".$nb_unites_centre." nb_unites_droite=".$nb_unites_droite;
        //echo "I_INFANTERIE_REEL=".$rowunite->I_INFANTERIE_REEL." I_CAVALERIE_REEL=".$rowunite->I_CAVALERIE_REEL." I_ARTILLERIE_REEL=".$rowunite->I_ARTILLERIE_REEL;
        if ($rowunite->B_QG > 0)
        {
            //QG
            if ($nb_unites_gauche > 0 && $nb_unites_centre > 0 && 0 == $nb_unites_gauche_QG)
            {
                echo "<input alt=\"gauche\" id=\"gauche" . $ID_PION . "\" name=\"gauche" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnGauche.png\" onclick=\"javascript:callCombatPion(" . $ID_PION . ",'G');\" />";
            }
            if (0 < $nb_unites_centre && 0 == $nb_unites_centre_QG)
            {
                echo "<input alt=\"centre\" id=\"centre" . $ID_PION . "\" name=\"centre" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnCentre.png\" onclick=\"javascript:callCombatPion(" . $ID_PION . ",'C');\" />";
            }
            if ($nb_unites_droite > 0 && $nb_unites_centre > 0 && 0 == $nb_unites_droite_QG)
            {
                echo "<input alt=\"droite\" id=\"droite" . $ID_PION . "\" name=\"droite" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnDroite.png\" onclick=\"javascript:callCombatPion(" . $ID_PION . ",'D');\" />";
            }
        }
        else
        {
            if (true==$b_reserve_artillerie)
            {
                //echo "artillerie pure";
                // artillerie pure, ne peut pas s'engager dans une zone sans autre unite de combat
                if ($nb_unites_gauche < 4 && $nb_unites_gauche > 0)
                {
                    echo "<input alt=\"gauche\" id=\"gauche" . $ID_PION . "\" name=\"gauche" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\"  src=\"images/btnGauche.png\" onclick=\"javascript:callCombatPion(" . $ID_PION . ",'G');\" />";
                }
                if ($nb_unites_centre < 4  && $nb_unites_centre > 0)
                {
                    echo "<input alt=\"centre\" id=\"centre" . $ID_PION . "\" name=\"centre" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnCentre.png\" onclick=\"javascript:callCombatPion(" . $ID_PION . ",'C');\" />";
                }
                if ($nb_unites_droite < 4 && $nb_unites_droite > 0)
                {
                    echo "<input alt=\"droite\" id=\"droite" . $ID_PION . "\" name=\"droite" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnDroite.png\" onclick=\"javascript:callCombatPion(" . $ID_PION . ",'D');\" />";
                }
            }
            else
            {
                if ($nb_unites_gauche < 4 && $nb_unites_centre > 0)
                {
                    echo "<input alt=\"gauche\" id=\"gauche" . $ID_PION . "\" name=\"gauche" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\"  src=\"images/btnGauche.png\" onclick=\"javascript:callCombatPion(" . $ID_PION . ",'G');\" />";
                }
                if ($nb_unites_centre < 4)
                {
                    echo "<input alt=\"centre\" id=\"centre" . $ID_PION . "\" name=\"centre" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnCentre.png\" onclick=\"javascript:callCombatPion(" . $ID_PION . ",'C');\" />";
                }
                if ($nb_unites_droite < 4 && $nb_unites_centre > 0)
                {
                    echo "<input alt=\"droite\" id=\"droite" . $ID_PION . "\" name=\"droite" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnDroite.png\" onclick=\"javascript:callCombatPion(" . $ID_PION . ",'D');\" />";
                }
            }
        }
    }
    else
    {
        $bOrdreCombat = false;
        //$row = mysql_fetch_object($res_ordre);
        while (($row = mysql_fetch_object($res_ordre)) && !$bOrdreCombat)
        {
            switch ($row->I_TYPE)
            {
                case ORDRE_RETRAITE:
                    echo " RETRAITE ";
                    $bOrdreCombat = true;
                    break;
                case ORDRE_COMBAT:
                    //on ne peut annuler son positionnement que s'il n'y a pas de chef sur la zone ou d'artillerie pure et que l'on ait pas la derniere unite
                    //ou que qu'il n'y pas d'unite sur les cotes et que l'on est pas la derniere unite au centre
                    //echo "zone bataille=".$row->I_ZONE_BATAILLE;
                    
                    if (    $rowunite->B_QG == 0 &&
                            ((($row->I_ZONE_BATAILLE == 0 || $row->I_ZONE_BATAILLE == 5) && $nb_unites_droite == 1 && $nb_unites_droite_QG == 1) ||
                            (($row->I_ZONE_BATAILLE == 1 || $row->I_ZONE_BATAILLE == 4) && $nb_unites_centre == 1 && ($nb_unites_centre_QG == 1 || $nb_unites_droite >= 1 || $nb_unites_gauche >= 1)) ||
                            (($row->I_ZONE_BATAILLE == 2 || $row->I_ZONE_BATAILLE == 3) && $nb_unites_gauche == 1 && $nb_unites_gauche_QG == 1)
                            ))
                    {
                        echo "<br/>";
                    }
                    else
                    {
                        echo "<input alt=\"annuler\" id=\"annuler" . $ID_PION . "\" name=\"annuler" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnAnnuler.png\" onclick=\"javascript:callAnnulerPion(" . $ID_PION . ");\" />";
                    }
                    $bOrdreCombat = true;
                    break;
                case ORDRE_RETRAIT:
                    echo "<input alt=\"annuler\" id=\"annuler" . $ID_PION . "\" name=\"annuler" . $ID_PION . "\" class=\"bataille\" type=\"image\" value=\"submit\" src=\"images/btnAnnuler.png\" onclick=\"javascript:callAnnulerPion(" . $ID_PION . ");\" />";
                    break;
                default:
                    //Cela peut �tre un QG qui envoie un message
                    echo " ordre incoh&eacute;rent:".$row->I_TYPE;
                    break;
            }
        }
    }
    echo "</div>\r\n";
}

function AfficherCommandantEnChef($db, $ID_PARTIE, $ID_BATAILLE, $ID_LEADER, $ID_NATION_JOUEUR, $ID_NATION, $ID_PION_ROLE, $I_TOUR, $ID_VICTOIRE)
{
    //echo "leader=".$ID_LEADER;
    echo "Commandant en chef : ";
    if ($ID_LEADER >= 0)
    {
        $requete = "SELECT S_NOM";
        $requete.=" FROM tab_vaoc_pion ";
        $requete.="WHERE ID_PARTIE=" . $ID_PARTIE . " AND ID_PION=" . $ID_LEADER;
        //echo $requete."<br/>";
        $res_leader = mysql_query($requete, $db);
        $row_leader = mysql_fetch_object($res_leader);
        echo $row_leader->S_NOM;
        
        //echo "ID_VICTOIRE=".$ID_VICTOIRE."<br/>";
        //echo "ID_NATION_JOUEUR=".$ID_NATION_JOUEUR."<br/>";
        //echo "ID_NATION".$ID_NATION."<br/>";
        //echo "ID_PION_ROLE=".$ID_PION_ROLE."<br/>";
        //echo "ID_LEADER=".$ID_LEADER."<br/>";
        if (($ID_VICTOIRE < 0) && ($ID_NATION_JOUEUR == $ID_NATION) && ($ID_PION_ROLE == $ID_LEADER))
        {
            //si le general en chef a un ordre de retraite, c'est qu'une retraite generale a ete ordonnee a ce tour, sinon, il y a bien un engagement
            $requete = "SELECT ID_ORDRE, I_TOUR";
            $requete.=" FROM tab_vaoc_ordre ";
            $requete.="WHERE ID_PARTIE=" . $ID_PARTIE . " AND ID_PION=" . $ID_LEADER;
            $requete.=" AND I_TOUR=".$I_TOUR;//mis en commentaire un moment,je ne sais pas pourquoi, mais si on ne le met pas, si le chef général est engagé au combat, il ne peut plus faire retraite générale.
            $requete.=" AND I_TYPE=" . ORDRE_RETRAITE . " AND ID_BATAILLE=" . $ID_BATAILLE; //on ne peut toujours donner qu'un seul ordre de retraite par bataille
            //echo $requete."<br/>";
            $res_ordre_retraite = mysql_query($requete, $db);
            if (0 == mysql_num_rows($res_ordre_retraite))
            {
                echo "<br/><input alt=\"retraite generale\" id='retraiteGenerale" . $ID_LEADER . "' name='retraiteGenerale" . $ID_LEADER . "' class='bataille' type='image' value='submit' src=\"images/btnRetraiteGenerale.png\" onclick='javascript:callRetraiteGenerale(" . $ID_LEADER . ",1);' />";
            }
            else
            {
                $row_retraite = mysql_fetch_object($res_ordre_retraite);
                //on ne peut annuler un ordre de retraite que s'il est donne dans le meme tour
                if ($I_TOUR == $row_retraite->I_TOUR)
                {
                    echo "<br/><input alt=\"annuler retraite generale\" id='annulerRetraiteGenerale" . $ID_LEADER . "' name='annulerRetraiteGenerale" . $ID_LEADER . "' class='bataille' type='image' value='submit' src=\"images/btnAnnulerRetraite.png\" onclick='javascript:callRetraiteGenerale(" . $ID_LEADER . ",0);' />";
                }
                else
                {
                    //ben... rien !
                }
            }
        }
    }
    else
    {
        echo "aucun";
    }
}

function AfficherUniteVictoire($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $ID_PION, $B_EN_DEFENSE, $B_ENGAGEE)
{
    $requete = "SELECT tab_vaoc_pion.S_NOM, tab_vaoc_pion.I_MORAL_MAX, tab_vaoc_pion.I_EXPERIENCE, tab_vaoc_pion.B_QG, tab_vaoc_pion.I_STRATEGIQUE, tab_vaoc_pion.I_TACTIQUE, tab_vaoc_pion.C_NIVEAU_HIERARCHIQUE,";
    $requete.=" tab_vaoc_bataille_pions.I_INFANTERIE_DEBUT, tab_vaoc_bataille_pions.I_INFANTERIE_FIN, tab_vaoc_bataille_pions.I_CAVALERIE_DEBUT, tab_vaoc_bataille_pions.I_CAVALERIE_FIN,";
    $requete.=" tab_vaoc_bataille_pions.I_ARTILLERIE_DEBUT, tab_vaoc_bataille_pions.I_ARTILLERIE_FIN, tab_vaoc_bataille_pions.I_MORAL_DEBUT, tab_vaoc_bataille_pions.I_MORAL_FIN,";
    $requete.=" tab_vaoc_bataille_pions.I_FATIGUE_DEBUT, tab_vaoc_bataille_pions.I_FATIGUE_FIN, tab_vaoc_bataille_pions.B_RETRAITE, tab_vaoc_bataille_pions.B_ENGAGEMENT";
    $requete.=" FROM tab_vaoc_pion, tab_vaoc_bataille_pions ";
    $requete.="WHERE tab_vaoc_pion.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_pion.ID_PION=" . $ID_PION;
    $requete.=" AND tab_vaoc_pion.ID_PARTIE=tab_vaoc_bataille_pions.ID_PARTIE";
    $requete.=" AND tab_vaoc_pion.ID_PION=tab_vaoc_bataille_pions.ID_PION";
    //echo $requete."<br/>";

    $res_pion = mysql_query($requete, $db);
    $rowunite = mysql_fetch_object($res_pion);

    if ($rowunite->I_MORAL_FIN <= 0 && 0 == $rowunite->B_QG)
    {
        //unit� en fuite
        echo "<div class='unitefuite'";
    }
    else
    {
        if (0 == $B_EN_DEFENSE)
        {
            echo "<div class='unite'";
        }
        else
        {
            echo "<div class='unitedefense'";
        }
    }

    if (0 == $rowunite->B_QG)
    {
        //c'est une unit�
        echo " id='pion" . $ID_PION . "' onmouseover='simulerTitleVictoire(";
        echo $ID_PION . "," . $rowunite->I_INFANTERIE_DEBUT . "," . $rowunite->I_INFANTERIE_FIN . "," . $rowunite->I_CAVALERIE_DEBUT . "," . $rowunite->I_CAVALERIE_FIN . ",";
        echo $rowunite->I_ARTILLERIE_DEBUT . "," . $rowunite->I_ARTILLERIE_FIN . "," . $rowunite->I_MORAL_DEBUT . "," . $rowunite->I_MORAL_FIN . ",";
        echo $rowunite->I_MORAL_MAX . "," . $rowunite->I_FATIGUE_DEBUT . "," . $rowunite->I_FATIGUE_FIN . "," . $rowunite->I_EXPERIENCE . ")'";
        echo " onmouseout='hideTitle()'>";
        echo $rowunite->S_NOM . "<br/>";

        if ($rowunite->I_MORAL_FIN <= 0)
        {
            echo "FUITE";
        }
        else
        {
            if ($rowunite->B_RETRAITE == 1)
            {
                echo "RETRAITE";
            }
        }
    }
    else
    {
        //c'est un QG
        echo " id='pion" . $ID_PION . "' onmouseover='simulerTitleQG(" . $ID_PION . "," . $rowunite->I_TACTIQUE . "," . $rowunite->I_STRATEGIQUE . ",\"" . $rowunite->C_NIVEAU_HIERARCHIQUE . "\")' onmouseout='hideTitle()'>";
        echo $rowunite->S_NOM . "<br/>";
    }

    echo "</div>\r\n";
}

function AfficherUnitesEnReserveVictoire($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION)
{
    $requete = "SELECT tab_vaoc_bataille_pions.ID_PION, tab_vaoc_bataille_pions.B_EN_DEFENSE ";
    $requete.=" FROM tab_vaoc_bataille_pions ";
    $requete.="WHERE tab_vaoc_bataille_pions.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE=" . $ID_BATAILLE;
    $requete.=" AND tab_vaoc_bataille_pions.ID_NATION=" . $ID_NATION;
    $requete.=" AND tab_vaoc_bataille_pions.B_ENGAGEMENT=0";
    //echo $requete."<br/>";
    $res_pions = mysql_query($requete, $db);

    echo "<table summary='reserve' id='reserve' cellpadding='10px' cellspacing='10px' style='border-style: none; border-width: 0px;'>";
    echo "<tr>";
    if (0 == mysql_num_rows($res_pions))
    {
        echo "<td><div class='unite'>Aucune unit&eacute; en r&eacute;serve</div></td>";
    }
    else
    {
        while ($row = mysql_fetch_object($res_pions))
        {
            echo "<td>";
            AfficherUniteVictoire($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $row->ID_PION, $row->B_EN_DEFENSE, 0);
            echo "</td>";
        }
    }
    echo "</tr>";
    echo "</table>";
}

function AfficherUnitesEngagesEnBatailleVictoire($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $I_ZONE)
{
    //echo "ID_PION_ROLE=".$ID_PION_ROLE;
    //Unit�s d�j� en bataille
    $requete = "SELECT tab_vaoc_pion.S_NOM, tab_vaoc_bataille_pions.ID_PION, tab_vaoc_bataille_pions.ID_NATION, tab_vaoc_bataille_pions.B_ENGAGEE, tab_vaoc_bataille_pions.B_EN_DEFENSE ";
    $requete.=" FROM tab_vaoc_bataille_pions, tab_vaoc_pion ";
    $requete.="WHERE tab_vaoc_bataille_pions.ID_PARTIE=" . $ID_PARTIE;
    $requete.=" AND tab_vaoc_bataille_pions.ID_BATAILLE=" . $ID_BATAILLE;
    $requete.=" AND tab_vaoc_bataille_pions.I_ZONE_BATAILLE_ENGAGEMENT=" . $I_ZONE;
    $requete.=" AND tab_vaoc_bataille_pions.B_ENGAGEMENT=1";
    $requete.=" AND tab_vaoc_bataille_pions.ID_PION=tab_vaoc_pion.ID_PION";
    $requete.=" AND tab_vaoc_bataille_pions.ID_PARTIE=tab_vaoc_pion.ID_PARTIE";
    //echo $requete."<br/>";
    $res_pions = mysql_query($requete, $db);

    echo "<table summary='zone" . $I_ZONE . "' id='Table" . $I_ZONE . "' cellpadding='10px' cellspacing='10px' style='border-style: none; border-width: 0px;'>";
    echo "<tr>";
    if (0 == mysql_num_rows($res_pions))
    {
        echo "<td><div class='unite'>Aucune unit&eacute;</div></td>";
    }
    else
    {
        while ($row = mysql_fetch_object($res_pions))
        {
            echo "<td>";
            AfficherUniteVictoire($db, $ID_PARTIE, $ID_BATAILLE, $ID_NATION_JOUEUR, $ID_NATION, $row->ID_PION, $row->B_EN_DEFENSE, 1);
            echo "</td>";
        }
    }
    echo "</tr>";
    echo "</table>\r\n";
}

//Renvoie le nouveau numero de l'ordre suivant, ajoute 1 a MAX_ID_ORDRE
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
    $requete2 = "UPDATE tab_vaoc_partie ";
    $requete2.=" SET `MAX_ID_ORDRE` = '" . $nouvelOrdre . "'";
    $requete2.="WHERE ID_PARTIE=" . $ID_PARTIE;
    mysql_query($requete2, $db);
//echo $requete;

    return $nouvelOrdre;
}
</script>
