<?php
   require("vaocbase.php"); //include obligatoire pour l'execution

    $db = @db_connect();
    //fixe le francais comme langue pour les dates et les caracteres accentues
    $requete = "SET lc_time_names = 'fr_FR'";
    mysql_query($requete, $db);
    mysql_query("SET NAMES 'utf8'");
    
    $partie = mysql_real_escape_string($_GET['partie']);
    $requete = "SELECT ID_NOM, S_NOM";
    $requete.=" FROM tab_vaoc_noms_carte";
    $requete.=" WHERE ID_PARTIE=" . $partie;
    $requete.=" ORDER BY S_NOM";
    //echo $requete;
    $res_noms = mysql_query($requete, $db);
    while ($row = mysql_fetch_object($res_noms))
    {
        printf("<option value=\"%u\">%s</option>", $row->ID_NOM, $row->S_NOM);
        //printf("<li><a data-target=\\\"#\\\">%s</a></li>",$row->S_NOM);
        //printf("<span class=\"filter-option pull-left\">%s</option>", $row->S_NOM);
    }
    /*
        printf("<option>%s</option>", "zero");
        printf("<option>%s</option>", "un");
        printf("<option>%s</option>", "deux");
        printf("<option>%s</option>", "trois");
*/
    
?>