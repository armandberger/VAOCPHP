<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
        <title>VAOC : Carte</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="Description" content="VAOC, carte de campagne"/>
        <meta name="Keywords" content="VAOC, carte"/>
        <meta name="Identifier-URL" content="http://vaoc.free.fr/vaoc/vaoccarte.php"/>
        <meta name="revisit-after" content="31"/>
        <meta name="Copyright" content="copyright armand BERGER"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
        <link rel="icon" type="image/png" href="/images/favicon.png" />
        <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="css/vaoc2.css"/>
        <link href="css/bootstrap.css" rel="stylesheet"/>
        <style type="text/css"> 
        body
        {
            margin : 0 auto; 
            padding : 0; 
                background-color:white; 
                color:black; 
                background-image:url(images/fondcarte.jpg);
        }

        #inforecherche {
          position:absolute;
          display:none;
          padding:5px;
          margin:5px;
          border:1px solid black;
          background-color:lightgreen;
          color: black; 
          z-index:9999;
        }
        </style> 
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="js/jquery-3.1.1.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.js"></script>
        <!-- http://silviomoreto.github.io/bootstrap-select/ -->
        <link rel="stylesheet" href="css/bootstrap-select.css">
        <script src="js/bootstrap-select.js"></script>
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
        mysql_query("SET NAMES 'utf8'", $db);

	//recherche du repertoire des images
  	$requete="SELECT S_REPERTOIRE, D_MULT_ZOOM_X, D_MULT_ZOOM_Y, I_NB_CARTE_X, I_NB_CARTE_Y, I_LARGEUR_CARTE_ZOOM, I_HAUTEUR_CARTE_ZOOM, I_ECHELLE ";
  	$requete.="FROM tab_vaoc_partie ";
  	$requete.="WHERE ID_PARTIE=".$id_partie;
	//echo $requete."<br/>";
  	$res_repertoire = mysql_query($requete,$db);
        $row_repertoire = mysql_fetch_object($res_repertoire);
	$repertoire = $row_repertoire->S_REPERTOIRE."_carte";
	$modX = $row_repertoire->D_MULT_ZOOM_X;
	$modY = $row_repertoire->D_MULT_ZOOM_Y;
	$nbCarteX = $row_repertoire->I_NB_CARTE_X;
	$nbCarteY = $row_repertoire->I_NB_CARTE_Y;
	$largeur_zoom = $row_repertoire->I_LARGEUR_CARTE_ZOOM; 
	$hauteur_zoom = $row_repertoire->I_HAUTEUR_CARTE_ZOOM;
	$echelle =  $row_repertoire->I_ECHELLE;//nombre de pixels pour faire un kilometre
	//echo "Repertoire=".$repertoire."<br/>";
	//echo "modX=".$modX."<br/>";
	//echo "modY=".$modY."<br/>";
	//echo "nbCarteX=".$nbCarteX."<br/>";
	//echo "nbCarteY=".$nbCarteY."<br/>";
	
	if(FALSE==empty($id_destination))
	{
            //Demande de visualiser une ville
            $requete="SELECT S_NOM, I_X, I_Y";
            $requete.=" FROM tab_vaoc_noms_carte";
            $requete.=" WHERE ID_PARTIE=".$id_partie;
            $requete.=" AND ID_NOM =".$id_destination;
            //echo $requete;
            $res_trouver = mysql_query($requete,$db);
            $row_trouver = mysql_fetch_object($res_trouver);		
            $id_x=min(floor($row_trouver->I_X/$largeur_zoom),$nbCarteX-2);
            $id_y=min(floor($row_trouver->I_Y/$hauteur_zoom),$nbCarteY-2);
            //echo "I_X=".$row_trouver->I_X." modX=".$modX."largeur=".$largeur_zoom;
            //echo "id_x=".$id_x." id_y=".$id_y;
	}
	?>
	<script  type="text/javascript">
    showingTitle = false;
    mousex = 0;
    mousey = 0;
    ecartVisualisation = 20;
    xt=0;
    yt=0;

    //affichage de l'infobulle du texte recherche
    function affichageInfobulleRecherche()
    {
        idRecherche = document.getElementById("id_destination").value;
        //alert(idRecherche);
        if (""==idRecherche)
        {
            //alert("test KO");
            document.getElementById("inforecherche").style.display = "none";
        }
        else
        {
            //alert("test OK");
            xCarte=parseInt(document.getElementById("id_x").value);
            yCarte=parseInt(document.getElementById("id_y").value);
            posiHG = getPosition("imagehautgauche");
            //posiBD = getPosition("imagebasdroite");
            //alert("xCarte="+xCarte);
            <?php
            if(FALSE==empty($id_destination))
            {
                $requete="SELECT S_NOM,I_X,I_Y ";
                $requete.=" FROM tab_vaoc_noms_carte ";
                $requete.=" WHERE ID_PARTIE=".$id_partie;
                $requete.=" AND ID_NOM=".$id_destination;
                //echo "requete=".$requete.";";
                $res_recherche = mysql_query($requete,$db);
                $row = mysql_fetch_object($res_recherche);

                //calcul du numero de carte
                //echo "largeur_zoom=".$largeur_zoom;
                //echo "row->I_X=".$row->I_X;
                $numeroDeCarteX=floor($row->I_X/$largeur_zoom);
                $numeroDeCarteY=floor($row->I_Y/$hauteur_zoom);
                //calcul de la position relative
                //echo "alert(\"numeroDeCarteX=".$numeroDeCarteX."\");";
                //$posX=$row->I_X -$numeroDeCarteX*$largeur_zoom;
                //$posY=$row->I_Y -$numeroDeCarteY*$hauteur_zoom;

                //il y a quatre positions possibles correspondants aux quatres positions
                echo "if (";
                echo " (xCarte==".$numeroDeCarteX." && yCarte==".$numeroDeCarteY.")";
                echo " || ";
                echo " (xCarte==".($numeroDeCarteX-1)." && yCarte==".$numeroDeCarteY.")";
                echo " || ";
                echo " (xCarte==".$numeroDeCarteX." && yCarte==".($numeroDeCarteY-1).")";
                echo " || ";
                echo " (xCarte==".($numeroDeCarteX-1)." && yCarte==".($numeroDeCarteY-1).")";
                echo ")\r\n";
                echo "{\r\n";
                //echo "alert(\"test OK\");";
                echo "posX=".$row->I_X."-xCarte*".$largeur_zoom.";\r\n";
                echo "posY=".$row->I_Y."-yCarte*".$hauteur_zoom.";\r\n";
                echo "document.getElementById(\"inforecherche\").style.left = (posX + posiHG[0]) +\"px\";\r\n";
                echo "document.getElementById(\"inforecherche\").style.top = (posY + posiHG[1]) +\"px\";\r\n";
                echo "document.getElementById(\"inforecherche\").innerHTML =\"".$row->S_NOM."\";\r\n";
                echo "document.getElementById(\"inforecherche\").style.display = \"inline\";\r\n";
                echo "}\r\n";
                echo "else\r\n";
                echo "{";
                echo "document.getElementById(\"inforecherche\").style.display = \"none\";";
                echo "}\r\n";
            }
            ?>
            //alert(document.getElementById("inforecherche").style.left);
            //alert(document.getElementById("inforecherche").style.top);
        }
    }
    
    function getMouseCoord(e) {
        //pour ie seulement... onmouseove ne lui renvoye pas l'evenement, contrairement au moteur gecko de FF/mozilla
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
//        if (e) {alert ("e ok");}
//        else {alert ("e KO");}
            
        if (e) {
            //mousex = e.clientX + document.body.scrollLeft;
            //mousey = e.clientY + document.body.scrollTop;
            xCarte=parseInt(document.getElementById("id_x").value);
            yCarte=parseInt(document.getElementById("id_y").value);
            posiHG = getPosition("imagehautgauche");
            posiBD = getPosition("imagebasdroite");
            //alert("mousex="+mousex+"mousey="+mousey+"posiHG[0]="+posiHG[1]+"posiHG[1]="+posiHG[1]);
            if ((mousex >= posiHG[0]) &&
                (mousex <= posiBD[0]+document.getElementById("imagehautgauche").width+document.getElementById("imagehautdroite").width) &&
                (mousey >= posiHG[1]) &&
                (mousey <= posiBD[1] +document.getElementById("imagehautgauche").height+document.getElementById("imagebasgauche").height)) 
            {
                //rechercher s'il existe un texte dans la position
                xt = mousex - posiHG[0];
                yt = mousey - posiHG[1];
                
                //alert("mousex="+mousex+"mousey="+mousey+"posiHG[0]="+posiHG[1]+"posiHG[1]="+posiHG[1]+"xt="+xt+"yt="+yt);
                //ajout de toutes les lignes de test de nom de ville
                <?php
                    $requete="SELECT S_NOM,I_X,I_Y ";
                    $requete.="FROM tab_vaoc_noms_carte ";
                    $requete.="WHERE ID_PARTIE=".$id_partie;
                    //echo "requete=".$requete.";";
                    $res_noms = mysql_query($requete,$db);
                    while($row = mysql_fetch_object($res_noms))
                    {
                        //calcul du numero de carte
                        $numeroDeCarteX=floor($row->I_X/$largeur_zoom);
                        $numeroDeCarteY=floor($row->I_Y/$hauteur_zoom);
                        //calcul de la position relative
                        $posX=$row->I_X -$numeroDeCarteX*$largeur_zoom;
                        $posY=$row->I_Y -$numeroDeCarteY*$hauteur_zoom;

                        //il y a quatre positions possibles correspondants aux quatres positions
                        echo "if (";
                        echo " (xt >".$posX."- ecartVisualisation && xt <".$posX."+ ecartVisualisation";
                        echo " && xCarte==".$numeroDeCarteX." && yCarte==".$numeroDeCarteY;
                        echo " && yt >".$posY." - ecartVisualisation && yt < ".$posY." + ecartVisualisation)";
                        echo " || ";
                        echo " (xt >".($posX+$largeur_zoom)."- ecartVisualisation && xt <".($posX+$largeur_zoom)."+ ecartVisualisation";
                        echo " && xCarte==".($numeroDeCarteX-1)." && yCarte==".$numeroDeCarteY;
                        echo " && yt >".$posY." - ecartVisualisation && yt < ".$posY." + ecartVisualisation)";
                        echo " || ";
                        echo " (xt >".$posX."- ecartVisualisation && xt <".$posX."+ ecartVisualisation";
                        echo " && xCarte==".$numeroDeCarteX." && yCarte==".($numeroDeCarteY-1);
                        echo " && yt >".($posY+$hauteur_zoom)." - ecartVisualisation && yt < ".($posY+$hauteur_zoom)." + ecartVisualisation)";
                        echo " || ";
                        echo " (xt >".($posX+$largeur_zoom)."- ecartVisualisation && xt <".($posX+$largeur_zoom)."+ ecartVisualisation";
                        echo " && xCarte==".($numeroDeCarteX-1)." && yCarte==".($numeroDeCarteY-1);
                        echo " && yt >".($posY+$hauteur_zoom)." - ecartVisualisation && yt < ".($posY+$hauteur_zoom)." + ecartVisualisation)";
                        echo ")";
                        echo "{";
                        echo "simulerTitle(\"infobulle\",\"".$row->S_NOM."\");";
                        echo "textePresent = true;";
                        echo "}";
                    }
                ?>
                /* affichage des coordonnees, uniquement pour le debug
                if (false == textePresent)
                {
                    //pour debug
                    //alert("simulerTitle"); 
                    simulerTitle("xt="+xt+" yt="+yt+" xCarte="+xCarte+" yCarte="+yCarte);
                    textePresent = true;
                }
                */
            }
            if (false == textePresent)
            {
                hideTitle("infobulle");
            }

            //simulerTitle("infobulle= " + posiHG[0]);
            //si un title est affich�, lancons son suivi de souris	            
            if (showingTitle) {
                updateTitlePos();
            }
        }
    }

    function updateTitlePos() {
    //alert("document.getElementById('infobulle').style.left="+document.getElementById('infobulle').style.left);
        document.getElementById("infobulle").style.left = mousex+"px";
        document.getElementById("infobulle").style.top = mousey+"px";
    }

    function simulerTitle(id, txt) {
        //on remplis avec le texte
        document.getElementById(id).innerHTML = txt;
        //alert(txt);

        //on place le div au bon endroit
        document.getElementById(id).style.left = 100;//mousex+"px";
        document.getElementById(id).style.top = 100;//mousey+"px";

        //on l'affiche en temps qu'element inline
        document.getElementById(id).style.display = "inline";
        //on previens le script qu'on est en train d'afficher un title
        showingTitle = true;
    }

    function hideTitle(id) {
        //on previens le script
        showingTitle = false;
        //qu'on masque le div
        document.getElementById(id).style.display = "none";                
    }
    
    /**
        * @author Patrick Poulain
        * @see http://petitchevalroux.net
        * @licence GPL
        */
    function getPosition(element) {
        var left = 0;
        var top = 0;
        /*On recupere l'element*/
        var e = document.getElementById(element);
        /*Tant que l'on a un element parent*/
        while (e.offsetParent != undefined && e.offsetParent != null) {
            /*On ajoute la position de l'element parent*/
            left += e.offsetLeft + (e.clientLeft != null ? e.clientLeft : 0);
            top += e.offsetTop + (e.clientTop != null ? e.clientTop : 0);
            e = e.offsetParent;
        }
        return new Array(left, top);
    }

   document.onmousemove = getMouseCoord;
   window.onload = affichageInfobulleRecherche;
   
   function callTrouver()
    {
   	document.getElementById("principal").action="vaoccarte.php";
   	document.getElementById("principal").target="_self";
   	document.getElementById("principal").submit();
    }

   function callCarteZoom() {
        //alert("callCarteZoom xt="+xt+", yt="+yt);

        <?php
        echo "modX=".$modX.";";
        echo "modY=".$modY.";";
        echo "largeur_zoom=".$largeur_zoom.";";
        echo "hauteur_zoom=".$hauteur_zoom.";";
        ?>

        id_x=parseInt(document.getElementById("id_x").value);
        id_y=parseInt(document.getElementById("id_y").value);
        //alert("callCarteZoom id_x="+id_x+", id_y="+id_y);
        document.getElementById("id_x").value = Math.floor((id_x*largeur_zoom+xt)*modX/largeur_zoom);
        document.getElementById("id_y").value = Math.floor((id_y*hauteur_zoom+yt)*modX/hauteur_zoom);
        //alert("callCarteZoom("+document.getElementById("id_x").value+","+document.getElementById("id_y").value+")");
        document.getElementById("principal").action = "vaoccartezoom.php";
        //alert("callCarteZoom apres action");
        document.getElementById("principal").target="_self";
        document.getElementById("principal").submit();
    }

    function callDeplacer(x, y, repertoire, maxx, maxy) {
        ligne = "x=" + x + " y=" + y;
        //alert(ligne);
        //alert(repertoire);
        if ((parseInt(document.getElementById("id_x").value) + x >= 0) && (parseInt(document.getElementById("id_x").value) + x < (maxx-1))){
            document.getElementById("id_x").value = parseInt(document.getElementById("id_x").value) + x;
            //alert("id_x=" + document.getElementById("id_x").value);
        }
        if ((parseInt(document.getElementById("id_y").value) + y >= 0) && (parseInt(document.getElementById("id_y").value) + y < (maxy-1))) {
            document.getElementById("id_y").value = parseInt(document.getElementById("id_y").value) + y;
            //alert("id_y=" + document.getElementById("id_y").value);
        }
        //alert(document.getElementById("imagehautgauche").src);
        ligne = repertoire +"/carte_" + document.getElementById("id_x").value + "_" + document.getElementById("id_y").value + ".png";

        //alert(ligne);
        document.getElementById("imagehautgauche").src = ligne;

        xx = parseInt(document.getElementById("id_x").value) + 1;
        ligne = repertoire + "/carte_" + xx + "_" + document.getElementById("id_y").value + ".png";
        //alert(ligne);
        document.getElementById("imagehautdroite").src = ligne;

        yy = parseInt(document.getElementById("id_y").value) + 1;
        ligne = repertoire+"/carte_" + document.getElementById("id_x").value + "_" + yy + ".png";
        //alert("3:"+ligne);
        document.getElementById("imagebasgauche").src = ligne;

        ligne = repertoire+"/carte_" + xx + "_" + yy + ".png";
        //alert("4:" + ligne);
        document.getElementById("imagebasdroite").src = ligne;
        //alert("fin");

        if (xx>1)
        {
            document.getElementById("imageFlecheGauche").style.visibility = "visible";
        }
        else
        {
            document.getElementById("imageFlecheGauche").style.visibility = "hidden";
        }
        //alert(xx+","+maxx);
        if (xx<maxx-1)
        {
            document.getElementById("imageFlecheDroite").style.visibility = "visible";
        }
        else
        {
            document.getElementById("imageFlecheDroite").style.visibility = "hidden";
        }
        if (yy>1)
        {
            document.getElementById("imageFlecheHaute").style.visibility = "visible";
        }
        else
        {
            document.getElementById("imageFlecheHaute").style.visibility = "hidden";
        }
        if (yy<maxy-1)
        {
            document.getElementById("imageFlecheBasse").style.visibility = "visible";
        }
        else
        {
            document.getElementById("imageFlecheBasse").style.visibility = "hidden";
        }
        affichageInfobulleRecherche();
    }
    </script>
</head>
    <body>
    <div style="color:white";>
    </div>
<div class="container">
<form method="post" id="principal" action="{$_SERVER['PHP_SELF']}">
    <?php 
    //champ caches
    echo "<input id='id_login' name='id_login' type='hidden' value='".$id_login."' />";
    echo "<input id='id_partie' name='id_partie' type='hidden' value='".$id_partie."' />";
    echo "<input id='id_x' name='id_x' type='hidden' value='".$id_x."' />";
    echo "<input id='id_y' name='id_y' type='hidden' value='".$id_y."' />";
    ?>

    <div class="row row-centered">
        <div class="col-xs-12 col-centered">
            <input alt="Rechercher" src='images/btnRechercher2.png' id="id_trouver" name="id_trouver" type="image" value="submit" class="btn btn-default" onclick="javascript:callTrouver();">
	<?php 
	//choix d'une destination
  	$requete="SELECT ID_NOM, S_NOM";
  	$requete.=" FROM tab_vaoc_noms_carte";
  	$requete.=" WHERE ID_PARTIE=".$id_partie;
  	$requete.=" ORDER BY S_NOM";
  	//echo $requete;
	$res_noms = mysql_query($requete,$db);
	$id_chaine="id_destination";
        printf("<select id=\"%s\" name=\"%s\" size=1 class=\"selectpicker\">",$id_chaine,$id_chaine);
        while($row = mysql_fetch_object($res_noms))
        {
            echo "<option";
            if (FALSE==empty($_REQUEST[$id_chaine]) && $_REQUEST[$id_chaine]==$row->ID_NOM)
            {
                    echo " selected=\"selected\"";
            }
            printf(" value=\"%u\">%s</option>",$row->ID_NOM,$row->S_NOM);
        }
        echo "</select>";
        ?>    
        </div>
    </div>
<table summary="cadre general" class="carte">
<tr>
<td colspan="3" style="text-align: center">
<a id="A3" href="" onclick="javascript:callDeplacer(0,-1,'<?php echo $repertoire."',".$nbCarteX.",".$nbCarteY ?>);return false;">
<img alt='haut' id="imageFlecheHaute" src='images/uparrow-64.png' style="text-align:right; vertical-align:top;visibility:<?php if ($id_y>0) echo "visible"; else echo "hidden"; ?>"/>
</a>
</td>
 
</tr>
<tr>
<td style="vertical-align:middle">	
    <a id="flecheGauche" href="nojs.htm" onclick="javascript:callDeplacer(-1,0,'<?php echo $repertoire."',".$nbCarteX.",".$nbCarteY ?>);return false;">
    <img id="imageFlecheGauche" alt='gauche' src='images/leftarrow-64.png' 
    style='visibility:<?php if ($id_x>0) echo "visible"; else echo "hidden"; ?>'/></a>
</td>
<td>
    <table summary="carte" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td></td>
        <td>
        	0&nbsp;km&nbsp;<img id="echelle" alt='echelle' src='images/echelle.png' />&nbsp;<?php echo floor(100/$echelle) ?>&nbsp;km
       </td>
        <td>
        	0&nbsp;km&nbsp;<img id="echelle" alt='echelle' src='images/echelle.png' />&nbsp;<?php echo floor(100/$echelle) ?>&nbsp;km
       </td>
        <td></td>
    </tr>
    <tr>
        <td align="right">
        	0&nbsp;km&nbsp;<br/><img id="echelle" alt='echelle' src='images/echellevertical.png' />&nbsp;<br/><?php echo floor(100/$echelle) ?>&nbsp;km&nbsp;
       </td>
        <td>
            <a id="lienhg" href="nojs.htm" onclick="javascript:callCarteZoom(); return false;" style="border-style: none">
            <?php
            echo "<img alt='haut-gauche' id='imagehautgauche' src='".$repertoire."/carte_".$id_x."_".$id_y.".png' style='display:block;'/>";
            ?>
            </a>
        </td>
        <td>
            <a id="lienhd" href="nojs.htm" onclick="javascript:callCarteZoom(); return false;" style="border-style: none">
            <?php
            echo "<img alt='haut-droite' id='imagehautdroite' src='".$repertoire."/carte_".($id_x+1)."_".$id_y.".png' style='display:block;'/>";
            ?>
            </a>
        </td>
        <td>
        	&nbsp;0&nbsp;km<br/>&nbsp;<img id="echelle" alt='echelle' src='images/echellevertical.png' /><br/>&nbsp;<?php echo floor(100/$echelle) ?>&nbsp;km
       </td>
    </tr>
    <tr>
        <td align="right">
        	&nbsp;0&nbsp;km&nbsp;<br/><img id="echelle" alt='echelle' src='images/echellevertical.png' />&nbsp;<br/><?php echo floor(100/$echelle) ?>&nbsp;km&nbsp;
       </td>
        <td>
            <a id="lienbg" href="nojs.htm" onclick="javascript:callCarteZoom(); return false;">
            <?php
            echo "<img alt='bas-gauche' id='imagebasgauche' src='".$repertoire."/carte_".($id_x)."_".($id_y+1).".png' style='display:block;'/>";
            ?>
            </a>            
        </td>
        <td>
            <a id="lienbd" href="nojs.htm" onclick="javascript:callCarteZoom(); return false;" style="border-style: none">
            <?php
            echo "<img alt='bas-droite' id='imagebasdroite' src='".$repertoire."/carte_".($id_x+1)."_".($id_y+1).".png' style='display:block;'/>";
            ?>
            </a>
        </td>
        <td>
        	&nbsp;0&nbsp;km<br/>&nbsp;<img id="echelle" alt='echelle' src='images/echellevertical.png' /><br/>&nbsp;<?php echo floor(100/$echelle) ?>&nbsp;km
       </td>
    </tr>
    <tr>
        <td></td>
        <td>
        	0&nbsp;km&nbsp;<img id="echelle" alt='echelle' src='images/echelle.png' />&nbsp;<?php echo floor(100/$echelle) ?>&nbsp;km
       </td>
        <td>
        	0&nbsp;km&nbsp;<img id="echelle" alt='echelle' src='images/echelle.png' />&nbsp;<?php echo floor(100/$echelle) ?>&nbsp;km
       </td>
        <td></td>
    </tr>
    </table>
</td>

<td  style="vertical-align:middle">
    <a id="A1" href="nojs.htm" onclick="javascript:callDeplacer(1,0,'<?php echo $repertoire."',".$nbCarteX.",".$nbCarteY ?>);return false;">
    <img alt='droite' id="imageFlecheDroite" src='images/rightarrow-64.png' style='visibility:<?php if ($id_x<$nbCarteX-2) echo "visible"; else echo "hidden"; ?>'/>
    </a>
</td>
</tr>
<tr>
    <td colspan="3" style="text-align: center">
            <a id="A2" href="nojs.htm" onclick="javascript:callDeplacer(0,1,'<?php echo $repertoire."',".$nbCarteX.",".$nbCarteY ?>);return false;">
            <img alt='bas' id="imageFlecheBasse" src='images/downarrow-64.png' style='visibility:<?php if ($id_y<$nbCarteY-2) echo "visible"; else echo "hidden"; ?>'/>
            </a>
    </td>
</tr>
</table>
    <div class="row row-centered">
        <div class="col-xs-12 col-centered">
        <?php 
        $requete="SELECT S_CARTE ";
        $requete.=" FROM tab_vaoc_campagne, tab_vaoc_partie";
        $requete.=" WHERE tab_vaoc_partie.ID_JEU=tab_vaoc_campagne.ID_JEU";
        $requete.=" AND ID_PARTIE=".$id_partie;
        //echo $requete;
        $res_campagne = mysql_query($requete,$db);
        $row_campagne = mysql_fetch_object($res_campagne);
        echo "<input alt=\"carte source\" id='id_chargement_carte' name='id_chargement_carte' class=\"btn btn-info\" type='image' value='submit' src=\"images/btnCarte2.png\" onclick=\"javascript:window.open('".$repertoire."/".$row_campagne->S_CARTE."'); return false;\" />";
        echo "&nbsp;<input alt=\"aide\" id='id_aide' name='id_aide' class=\"btn btn-info\" type='image' value='submit' src=\"images/btnAide2.png\" onclick=\"javascript: window.open('aide.html'); return false;\" />";
        echo "&nbsp;<input alt=\"quitter\" id='id_quitter' name='id_quitter' class=\"btn btn-default\" type='image' value='submit' src=\"images/btnQuitter2.png\" onclick=\"javascript:window.close();\" />";
        ?>
        </div>
    </div>
<div id="infobulle"></div>
<div id="inforecherche"></div>
</form>
</div>
</body>
</html>