<?php
require("vaocbase.php"); //include obligatoire pour l'execution
require("vaocfonctions_s.php"); //include obligatoire pour l'execution

extract($_REQUEST, EXTR_OVERWRITE);
//pratique pour le debug
// pour appeler la page independemment
// id_login:'Bessieres', id_pion_role:40, id_partie:8, ordre_tri_liste:'', pageNum_recus:1, nombre_messages_pages :20,tri_liste:DT_ARRIVEE
//http://localhost/vaoc/vaocqg_messages_s.php?id_login=Bessieres&id_pion_role=220&id_partie=8&tri_liste=DT_ARRIVEE&ordre_tri_liste=DESC&pageNum_recus=1&nombre_messages_pages=100
//while (list($name, $value) = each($_REQUEST)) {echo "$name = $value<br>\n";}
/*
//echo "{id_login:'".$id_login."', id_pion_role:".$id_pion_role.", id_partie:".$id_partie.",tri_liste:'DT_DEPART', ordre_tri_liste:'".$ordre_tri_liste."', pageNum_recus:".$pageNum_recus.", nombre_messages_pages :".$nombre_messages_pages."}";
*/

//connection a la base
$db = @db_connect();
//mysql_set_charset("utf-8", $db); -> pas valide dans la version php free
//fixe le francais comme langue pour les dates
$requete = "SET lc_time_names = 'fr_FR'";
mysql_query($requete, $db);
mysql_query("SET NAMES 'utf8'");

Debug_Logger::getInstance()->timeStart();
Debug_Logger::getInstance()->timeAddPoint('avant le tableau des messages');

if (TRUE == isset($nombre_messages_pages) && TRUE == is_numeric($nombre_messages_pages))
{
    if (FALSE == empty($id_login))
    {
        if( $nombre_messages_pages <0)
        {
            //appel depuis la page maitre, on recherche la valeur en base
            $res_nombre_messages_pages = mysql_query("SELECT I_NB_MESSAGES FROM tab_utilisateurs WHERE S_LOGIN='" . trim($id_login) . "'", $db);
            $row_nombre_messages_pages = mysql_fetch_object($res_nombre_messages_pages);
            $nombre_messages_pages = $row_nombre_messages_pages->I_NB_MESSAGES;
        }
        else
        {
        //la valeur du nombre de messages a afficher par defaut vient de changer, on met a jour la base s'il s'agit d'un utilisateur identifie
            $requete = "UPDATE tab_utilisateurs SET I_NB_MESSAGES=" . $nombre_messages_pages . " WHERE S_LOGIN='" . trim($id_login) . "'";
            //echo $requete;
            $res_nb_messages_update = mysql_query($requete, $db);
        }
    }
}
else
{
    $nombre_messages_pages=5; //valeur par defaut
}

//chaine pour rechargement de la page
/*
if ($ordre_tri_liste=="") {$nouveau_ordre_tri_liste="DESC"; } else {$nouveau_ordre_tri_liste="";}
if (TRUE == isset($_REQUEST["liste_emetteurs"]) && TRUE == is_numeric($_REQUEST["liste_emetteurs"]))
{
    $paramsBase="{liste_emetteurs:".$liste_emetteurs.", id_login:'".$id_login."', id_pion_role:".$id_pion_role.", id_partie:".$id_partie.", ordre_tri_liste:'".$nouveau_ordre_tri_liste."', pageNum_recus:".$pageNum_recus.", nombre_messages_pages :".$nombre_messages_pages;
}
else
{
    $paramsBase="{id_login:'".$id_login."', id_pion_role:".$id_pion_role.", id_partie:".$id_partie.", ordre_tri_liste:'".$nouveau_ordre_tri_liste."', pageNum_recus:".$pageNum_recus.", nombre_messages_pages :".$nombre_messages_pages;    
}
    $paramsChargement=$paramsBase.",tri_liste:'".$tri_liste."'";
 */
//echo "<div>".$paramsChargement."</div>";
//if (FALSE == isset($liste_emetteurs)) {$liste_emetteurs="";}
$paramsBase="{id_login:'".$id_login."', id_pion_role:".$id_pion_role.", id_partie:".$id_partie;

function Rechargement($emetteurs, $tri, $page, $pageMax)
{
    global $paramsBase;
    global $tri_liste;
    global $liste_emetteurs;
    global $ordre_tri_liste;
    global $nombre_messages_pages;
    global $pageNum_recus;
    $paramsBase2 = $paramsBase;
    
    echo "$('#SidebarMessages').html(\"<img alt='attente' src='images/giphy.gif' />\");\r\n";
    echo "$('#SidebarMessages').load('vaocqg_messages_s.php',";
    if ($emetteurs <> "")
    {
        echo $paramsBase.", liste_emetteurs:\"\"+".$emetteurs."+\"\", tri_liste:'".$tri_liste."', ordre_tri_liste:'".$ordre_tri_liste."', pageNum_recus:".$pageNum_recus.", nombre_messages_pages :".$nombre_messages_pages."}";
    }
    else
    {
        if ($liste_emetteurs <> "")
        {
            $paramsBase2.=", liste_emetteurs:".$liste_emetteurs;
        }
        if ($tri <> "")
        {
            if ($tri == $tri_liste)
            {
                if ($ordre_tri_liste=="") {$nouveau_ordre_tri_liste="DESC"; } else {$nouveau_ordre_tri_liste="";}
            }
            else
            {
                $nouveau_ordre_tri_liste="DESC";
            }
            echo $paramsBase2.", tri_liste:'".$tri."', ordre_tri_liste:'".$nouveau_ordre_tri_liste."', pageNum_recus:".$pageNum_recus.", nombre_messages_pages :".$nombre_messages_pages."}";
        }
        if ($page <> "")
        {
            echo $paramsBase2.", tri_liste:'".$tri_liste."', ordre_tri_liste:'".$ordre_tri_liste."', pageNum_recus:\"\"+".$page."+\"\", nombre_messages_pages :".$nombre_messages_pages."}";
        }
        if ($pageMax <> "")
        {
            echo $paramsBase2.", tri_liste:'".$tri_liste."', ordre_tri_liste:'".$ordre_tri_liste."', nombre_messages_pages :\"\"+".$pageMax."+\"\", pageNum_recus:".$pageNum_recus."}";
        }
    }
    echo ");\r\n";
}
?>

<script type="text/javascript">
    function callAllerALapageM(i_page)
    {
        id = "pageNum_recus";//liste des messages en bas de page
        document.getElementById(id).value = i_page;
        <?php
            Rechargement("","","i_page","");
            //$chargement =  "$('#SidebarMessages').load('vaocqg_messages_s.php', ".$paramsChargement.", pageNum_recus:\"+i_page+\"});";
            //echo "alert(\"".$chargement."\");";
            //echo $chargement;
        ?>
    }
        
$(document).ready(function () {

    $('#fermermessages').on('click', function () {
        $('#SidebarMessages').css('width', '0%');
    });
       
    $('#triDT_DEPART').on('click', function () {
        <?php
            Rechargement("","DT_DEPART", "","");
            //echo "$('#SidebarMessages').load('vaocqg_messages_s.php', ".$paramsBase.",tri_liste:'DT_DEPART'});";
        ?>
    });

    $('#triDT_ARRIVEE').on('click', function () {
        <?php
            Rechargement("","DT_ARRIVEE", "","");
            //echo "$('#SidebarMessages').load('vaocqg_messages_s.php', ".$paramsBase.",tri_liste:'DT_ARRIVEE'});";
        ?>
    });

    $('#triS_NOM').on('click', function () {
        <?php
            Rechargement("","S_NOM", "","");
            //echo "$('#SidebarMessages').load('vaocqg_messages_s.php', ".$paramsBase.",tri_liste:'S_NOM'});";
        ?>
    });

    $('#triS_ORIGINE').on('click', function () {
        <?php
            Rechargement("","S_ORIGINE", "","");
            //echo "$('#SidebarMessages').load('vaocqg_messages_s.php', ".$paramsBase.",tri_liste:'S_ORIGINE'});";
        ?>
    });
    
    $('#liste_emetteurs').change('click', function () {
        <?php
            Rechargement("$(this).val()","", "","");
            //$paramsListe="{id_login:'".$id_login."', id_pion_role:".$id_pion_role.", id_partie:".$id_partie.", ordre_tri_liste:'".$ordre_tri_liste."', pageNum_recus:".$pageNum_recus;
            //$paramsListe.=", nombre_messages_pages :".$nombre_messages_pages.",tri_liste:".$tri_liste.", liste_emetteurs:+$(this).val()";
            //echo "$('#SidebarMessages').load('vaocqg_messages_s.php', ".$paramsListe."});";
        ?>
    });    

    $('#nombre_messages_pages').change('click', function () {
        <?php
            Rechargement("","","","$(this).val()");
            /*$paramsNb="{id_login:'".$id_login."', id_pion_role:".$id_pion_role.", id_partie:".$id_partie.", ordre_tri_liste:'".$ordre_tri_liste."', pageNum_recus:".$pageNum_recus;
            $paramsNb.=", nombre_messages_pages :+$(this).val(),tri_liste:".$tri_liste."";
            if (TRUE == isset($_REQUEST["liste_emetteurs"]) && TRUE == is_numeric($_REQUEST["liste_emetteurs"]))
            {
                $paramsNb.=", nombre_messages_pages :+$(this).val(),tri_liste:".$tri_liste.", liste_emetteurs:".$liste_emetteurs;
            }
            echo "$('#SidebarMessages').load('vaocqg_messages_s.php', ".$paramsNb."});";
             */
        ?>
    });    
});
</script>

<?php
/*
echo "<script type=\"text/javascript\">";
echo "$(document).ready(function () {";
    echo "$('#fermermessages').on('click', function () {";
        echo "$('#SidebarMessages').css('width', '0%');";
    echo "});";
echo "});";
echo "</script>";
*/

function ImageTri($tri)
{
    if ($tri=="") {echo("&nbsp;<img alt='decroissant' src='images/Alphabetical_Sorting-26.png' />");} 
        else {echo("&nbsp;<img alt='croissant' src='images/Alphabetical_Sorting2-26.png' />");}            
}

echo "<div class=\"row\" id=\"tableau_messages\">";
    echo "<div class=\"col-12 text-center\">";
        echo "<h3>Messages re&ccedil;us&nbsp;<button class=\"btn btn-light bouton \" type=\"button\" alt=\"fermer les messages\" id=\"fermermessages\">Quitter</button></h3>";
        //echo "<button class=\"btn btn-light bouton \" type=\"button\" alt=\"fermer les messages\" id=\"fermermessages\">Quitter</button>";
    echo "</div>";
echo "</div>";
echo "<div class=\"row\">";
    echo "<div class=\"col-12 col-sm-2 col-md-1\">";
        //echo "<a href='nojs.htm' onclick=\"javascript: callTri('DT_DEPART'); return false;\"><label class=\"control-label\">Envoy&eacute;</label></a>";
        echo "<button class=\"btn btn-light bouton \" type=\"button\" id=\"triDT_DEPART\">Envoy&eacute";
        if ($tri_liste=="DT_DEPART") { ImageTri($ordre_tri_liste);}
        echo "</button>";
    echo "</div>";
    echo "<div class=\"col-12 col-sm-2 col-md-1\">";
        //echo "<a href='nojs.htm' onclick=\"javascript: callTri('DT_ARRIVEE'); return false;\"><label class=\"control-label\">Re&ccedil;u</label></a>";
        echo "<button class=\"btn btn-light bouton \" type=\"button\" id=\"triDT_ARRIVEE\">Re&ccedil;u";
        if ($tri_liste=="DT_ARRIVEE") { ImageTri($ordre_tri_liste);}
        echo "</button>";
    echo "</div>";
    // Liste des ordres envoyes dans une "table" triable geree avec des divs
    echo "<div class=\"col-12 col-sm-2 col-md-2\">";
        //echo "<a href='nojs.htm' onclick=\"javascript: callTri('S_NOM'); return false;\"><label class=\"control-label\">Par</label></a>";
        echo "<div class=\"btn-group\"><button class=\"btn btn-light bouton \" type=\"button\" id=\"triS_NOM\">Par";
        if ($tri_liste=="S_NOM") { ImageTri($ordre_tri_liste);}
        echo "</button>";
        //echo "<select class=\"custom-select\" id=\"liste_emetteurs\" name=\"liste_emetteurs\" size=1 onchange=\"javascript: changeEmetteur(); return false;\">";
        echo "<select class=\"custom-select\" id=\"liste_emetteurs\" name=\"liste_emetteurs\" size=1 \">";

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

        echo "</select>";
    echo "</div></div>";
    echo "<div class=\"col-12 col-sm-2 col-md-2\">";
        //echo "<a href='nojs.htm' onclick=\"javascript:callTri('S_ORIGINE');return false;\"><label class=\"control-label\">Origine</label></a>";
        echo "<button class=\"btn btn-light bouton \" type=\"button\" id=\"triS_ORIGINE\">Origine";
        if ($tri_liste=="S_ORIGINE") { ImageTri($ordre_tri_liste);}
        echo "</button>";
    echo "</div>";
    echo "<div class=\"col-12 col-sm-4 col-md-6\">";
        echo "<label class=\"control-label\"><strong>Message</strong></label>";
    echo "</div>";
echo "</div>";

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
    echo "<div class=\"col-12\">Vous n'avez re&ccedil;us aucun message</div>";
    echo "</div>";
}
else
{
    while ($row_message = mysql_fetch_object($res_messages))
    {
        echo "<hr style=\"width: 100%; border-top: 1px solid white; margin-top:0px; margin-bottom:0px;\" />";
        echo "<div class=\"row\">";
        echo "<div class=\"col-12 col-sm-2 col-md-1\">" . $row_message->DATE_DEPART . "</div>";// Ã  l'origine col-12 col-sm-2 col-md-2
        echo "<div class=\"col-12 col-sm-2 col-md-1\">" . $row_message->DATE_ARRIVEE . "</div>";// Ã  l'origine col-12 col-sm-2 col-md-2
        echo "<div class=\"col-12 col-sm-2 col-md-2\">" . $row_message->S_NOM . "</div>";// Ã  l'origine: col-12 col-sm-4 col-md-3
        echo "<div class=\"col-12 col-sm-2 col-md-2\">" . $row_message->S_ORIGINE . "</div>";// Ã  l'origine: col-12 col-sm-3 col-md-6
        echo "<div class=\"col-12 col-sm-4 col-md-6\">" . $row_message->S_MESSAGE . "</div>";// Ã  l'origine: col-12 col-sm-3 col-md-6
        echo "</div>";
    }

    $maxPage_recus = ceil($nb_messages_recus / $nombre_messages_pages);

    if ($maxPage_recus > 1)
    {
        echo "<div class=\"row\">";
        echo "<div class=\"col-12\">";
        echo "<nav>";
        echo "<ul class=\"pagination\">";
        echo "<li class=\"page-item\">";
            echo "<a  class=\"page-link\" href=\"#\" aria-label=\"Precedent\" onclick=\"javascript:callAllerALapageM(".max(1,$pageNum_recus-NB_PAGINATION_MAX).");return false;\">";
            echo "<span aria-hidden=\"true\">&laquo;</span>";
            echo "<span class=\"sr-only\">Precedent</span>";
            echo "</a>";
        echo "</li>";
        $debut_pagination_Num = max(1, min ($pageNum_recus,  $pageNum_recus - NB_PAGINATION_MAX/2));
        $fin_pagination_Num = $debut_pagination_Num + min(NB_PAGINATION_MAX/2,$maxPage_recus - $debut_pagination_Num);
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
                echo "<a class=\"page-link\" href=\"#\" onclick=\"javascript:callAllerALapageM(". $page .");return false;\">";
                echo "<span aria-hidden=\"true\">". $page . "</span>";
                echo "</a>";
            }
            echo "</li>";
        }
        echo "<li class=\"page-item\">";
            echo "<a class=\"page-link\"  href=\"#\" aria-label=\"Suivant\" onclick=\"javascript:callAllerALapageM(". min($maxPage_recus , $pageNum_recus + NB_PAGINATION_MAX) .");return false;\">";
            echo "<span aria-hidden=\"true\">&raquo;</span>";
            echo "<span class=\"sr-only\">Suivant</span>";
            echo "</a>";
        echo "</li>";
        echo "</ul></nav></div></div>";
    }

    //choix du nombre de messages par page
    echo "<div class=\"row\"><div class=\"col-auto\">";
    //echo "<select class=\"custom-select\" id=\"nombre_messages_pages\" name=\"nombre_messages_pages\" size=1 onchange=\"javascript:callNombreMessagesPage();\">";
    echo "<select class=\"custom-select\" id=\"nombre_messages_pages\" name=\"nombre_messages_pages\" size=1 \">";
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
    echo "</div></div>";
}
echo "<input id=\"pageNum_recus\" name=\"pageNum_recus\" type=\"hidden\" value=\"0\" />";
Debug_Logger::getInstance()->timeAddPoint('apres le tableau des messages');
Debug_Logger::getInstance()->timeStop();
?>
