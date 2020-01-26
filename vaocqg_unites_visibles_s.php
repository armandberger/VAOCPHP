<?php
require("vaocbase.php"); //include obligatoire pour l'execution
require("vaocfonctions_s.php"); //include obligatoire pour l'execution

extract($_REQUEST, EXTR_OVERWRITE);
//pratique pour le debug
// pour appeler la page independemment
//http://localhost/vaoc/vaocqg_unites_visibles_s.php?id_role=14&id_partie=8&combattif_visible='true'
//while (list($name, $value) = each($_REQUEST)) {echo "$name = $value<br>\n";}

//connection a la base
$db = @db_connect();
//mysql_set_charset("utf-8", $db); -> pas valide dans la version php free
//fixe le francais comme langue pour les dates
$requete = "SET lc_time_names = 'fr_FR'";
mysql_query($requete, $db);
mysql_query("SET NAMES 'utf8'");

if (TRUE == isset($combattif_visible) && TRUE == isset($id_role) && TRUE == isset($id_partie))
{
    //Mise a jour de la base
    if ($combattif_visible=='true') {$b_combattif_visible=1;} else {$b_combattif_visible=0;};
    $requete = "UPDATE tab_vaoc_role SET B_COMBATTIVES_VISIBLES=" . $b_combattif_visible . " WHERE ";
    $requete.=" tab_vaoc_role.ID_ROLE=" . $id_role;
    $requete.=" AND tab_vaoc_role.ID_PARTIE=" . $id_partie;
    //echo $requete;
    $res_b_combattif_visible_update = mysql_query($requete, $db);
    
    if ($combattif_visible=='true')
    {
        $requete = "SELECT S_UNITES_COMBATTIVES_VISIBLES AS S_VISIBLE ";
    }
    else
    {
        $requete = "SELECT S_UNITES_VISIBLES AS S_VISIBLE ";        
    }
    $requete.="FROM tab_vaoc_role ";
    $requete.="WHERE tab_vaoc_role.ID_ROLE=" . $id_role . " AND tab_vaoc_role.ID_PARTIE=" . $id_partie;
    $res_unites_visibles = mysql_query($requete, $db);
    $row_unites_visibles = mysql_fetch_object($res_unites_visibles);
    //echo $requete;
    
    //echo "<input type=\"checkbox\" id=\"combattives_visibles\" name=\"combattives_visibles\"";
    //if ($combattif_visible=='true') {echo " checked";}
    //echo "><b> Unités seules</b><br/>";
    echo $row_unites_visibles->S_VISIBLE;
}
?>
