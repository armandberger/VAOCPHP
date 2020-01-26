<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
    <title>VAOC : Ecran g&eacute;n&eacute;ral de connexion</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="Description" content="VAOC"/>
    <meta name="Keywords" content="VAOC, vol de l'aigle"/>
    <meta name="Identifier-URL" content="http://vaoc.free.fr/vaoc/index.php"/>
    <meta name="revisit-after" content="31"/>
    <meta name="Copyright" content="copyright armand BERGER"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
    <link rel="icon" type="image/png" href="images/favicon.png" sizes="32x32"/>
    <link rel="icon" type="image/png" href="images/favicon48.png" sizes="48x48"/>
    <link rel="icon" type="image/png" href="images/favicon96.png" sizes="96x96"/>
    <link rel="icon" type="image/png" href="images/favicon144.png" sizes="144x144"/>
    <!-- Genere a partir de https://realfavicongenerator.net -->
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="manifest" href="images/manifest.json">
    <link rel="mask-icon" href="images/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="images/favicon.ico">
    <meta name="msapplication-config" content="images/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">

    <!-- Bootstrap -->
    <link href="css/bootstrap.css" rel="stylesheet" />
    <style type="text/css">
        body {
            background-color: #434223 !important;
            color: white;
            text-align: center;
            }

        /* 1275 = largeur de l'image de fond */
        @media(min-width:1275px) 
        {
        body {
            background:url(images/fondindex.jpg) no-repeat center center;background-size: 100%;background-position:50% 50%;
            }
        }
        
        /* centered columns styles */
        .row-centered {
            text-align:center;
        }
        .col-centered {
            display:inline-block;
            float:none;
            /* reset the text-align */
            text-align:center;
            /* inline-block space fix */
            margin-right:-4px;
        }
        th.index {color:white; border-style: solid ; border-width: 0px 0px 1px 0px ; }
        a.index {color:white;}
    </style>
    <?php
    require("vaocbase.php");//include obligatoire pour l'executoion
    require("vaocfonctions.php");//include obligatoire pour l'execution

    //converti toutes les variables REQUEST en variables du meme nom
    extract($_REQUEST,EXTR_OVERWRITE);

    //connection a la base
    $db = @db_connect();
    mysql_query("SET NAMES 'utf8'"); 

    //pratique pour le debug
    /*
          echo "liste des valeurs transmises dans le post<br/>";
    while (list($name, $value) = each($HTTP_POST_VARS)) {echo "$name = $value<br>\n";}
          echo "liste des valeurs transmises dans le request<br/>";
    while (list($name, $value) = each($_REQUEST)) {echo "$name = $value<br>\n";}
          */
    ?>
    <script type="text/javascript">
        window.onload = setInterval(AfficherTempsTour,1000);//raffraichit la valeur toutes les secondes

        //mois 0-11, jour 1-31, heure 0-23
        function duree(annee, mois, jour, heure)
        {
            var jourcourant= new Date();
            var jourmiseajour= new Date(annee,mois,jour,heure,0,0,0);
            var delai=jourmiseajour.getTime()-jourcourant.getTime();//renvoie la difference en millisecondes
            var signe="";
            if (delai<0)
            {
                signe="-";
                delai=-delai;
            }
            var j=Math.floor(delai/(1000*60*60*24));
            var h=Math.floor((delai-j*1000*60*60*24)/(1000*60*60));
            var m=Math.floor((delai-j*1000*60*60*24-h*1000*60*60)/(1000*60));
            var s=Math.floor((delai-j*1000*60*60*24-h*1000*60*60-m*1000*60)/1000);
            //return delai+"/"+1000*60*60*24+"/"+signe+j+"j "+h+"h "+m+"m "+s+"s";
            return signe+j+"j "+h+"h "+m+"m "+s+"s";
        }
        
        <?php
            //recherche des parties en cours
            $requete="SELECT ID_PARTIE, DT_PROCHAINTOUR ";			
            $requete.=" FROM tab_vaoc_partie";
            $requete.=" WHERE ID_VICTOIRE<0 AND FL_DEMARRAGE=1";

            //echo $requete;
            $res_partie_encours = mysql_query($requete,$db);
            while($row = mysql_fetch_object($res_partie_encours))
            {
                echo "function AfficherTempsTour".$row->ID_PARTIE."(){document.getElementById('prochaintour".$row->ID_PARTIE."').innerHTML = ";
                /* date_parse ne marche qu'à partir de la version 5 de php */
                $dateProchainTour=date_parse($row->DT_PROCHAINTOUR);
                echo "duree(".$dateProchainTour["year"].",".($dateProchainTour["month"]-1).",".$dateProchainTour["day"].",".$dateProchainTour["hour"].");}";
                //echo "duree(2017,11,4,19);}";
            }
        ?>
            
        function AfficherTempsTour()
        {
            <?php
                //recherche des parties en cours
                $requete="SELECT ID_PARTIE, DT_PROCHAINTOUR ";			
                $requete.=" FROM tab_vaoc_partie";
                $requete.=" WHERE ID_VICTOIRE<0 AND FL_DEMARRAGE=1";

                //echo $requete;
                $res_partie_encours = mysql_query($requete,$db);
                while($row = mysql_fetch_object($res_partie_encours))
                {
                    echo "AfficherTempsTour".$row->ID_PARTIE."();";
                }
            ?>
        }

	function callConnexion()
	{
		document.getElementById("id_action").value="connexion";
	 	formPrincipale=document.getElementById("principal");
	 	formPrincipale.submit();
	}

	function callDeconnexion()
	{
		document.getElementById("id_action").value="deconnexion";
	 	formPrincipale=document.getElementById("principal");
	 	formPrincipale.submit();
	}

	function callModifierConnexion()
	{
	 	formPrincipale=document.getElementById("principal");
	 	formPrincipale.action="vaocmodifierutilisateur.php";
	 	formPrincipale.target="_self";
	 	formPrincipale.submit();
 	}
		
	function callQG() 
	{
	 	formPrincipale=document.getElementById("principal");
	 	formPrincipale.action="vaocqg.php";
	 	formPrincipale.target="_self";
	 	formPrincipale.submit();
 	}

	function callVictoire(idpartie)
	{
		//alert(idpartie);
		document.getElementById("id_partie").value=idpartie;
	 	formPrincipale=document.getElementById("principal");
	 	formPrincipale.action="vaocvictoire.php";
	 	formPrincipale.target="_self";
	 	formPrincipale.submit();
 	}

	</script>
</head>
<?php
    //s'agit d'une tentative de connexion ?
    if(FALSE==empty($id_action))
    {
        if ($id_action=="deconnexion")
        {
            $id_login="";
        }
        else
        {
            $message="";
            //on verifie si le login existe
            $res_login = mysql_query("SELECT S_LOGIN, S_MOTDEPASSE, ID_UTILISATEUR FROM tab_utilisateurs WHERE S_LOGIN='".trim($id_loginTest)."'",$db);
            if (mysql_num_rows($res_login)<=0)
            {
                $message=$message."Le nom d'utilisateur est inconnu.<br/>";
            }
            else
            {
                if (0==strlen($id_password))
                {
                    $message=$message."Vous devez saisir un mot de passe.<br/>";
                }
                else
                {
                    $login=mysql_fetch_object($res_login);
                    //on verifie si le mot de passe est le bon
                    if ($id_password!=$login->S_MOTDEPASSE)
                    {
                            //$message=$message."Le mot de passe est incorrect.".$id_password." / ".$login->S_MOTDEPASSE." / ".$login->S_MOTDEPASSE."<br/>";
                            $message=$message."Le mot de passe est incorrect.<br/>";
                    }
                }
            }

            if (strlen($message)==0)
            {
                //connexion reussie
                $id_login=$id_loginTest;
                //on met a jour la date de derniere connexion
                $requete="UPDATE tab_utilisateurs SET DT_DERNIERECONNEXION='".date("Y-m-d H:i:s")."' WHERE S_LOGIN='".trim($id_loginTest)."'";
                //echo $requete;
                $res_dt_connexion = mysql_query($requete,$db);
            }
            else
            {
                echo "<div class=\"alerte\">".$message."</div>";
            }
        }
    }
?>
<body>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-3.1.1.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.js"></script>
    <div class="container">
        <div class="row row-centered">
            <div class="col-sm-4 hidden-xs col-centered">
                <img alt="drapeau_napoleon" src="images/drapeau_napoleon_mini.jpg" />
            </div>
            <div class="col-xs-12 col-sm-4 col-centered">
                <h1 style="text-align:center;">V A O C</h1>
            </div>
            <div class="col-sm-4 hidden-xs col-centered">
                <img alt="drapeau_napoleon" src="images/drapeau_napoleon_mini.jpg" />
            </div>
        </div>
        <div class="row row-centered hidden-xs">
            <div class="col-xs-12 col-centered">
                <h2>Vol de l'aigle Assisté par Ordinateur et par Correspondance</h2>
            </div>
        </div>
        <div class="row row-centered hidden-xs">
            <div class="col-xs-12 col-centered">
                <h3>Camp de rassemblement</h3>
            </div>
        </div>
        <div class="row row-centered hidden-xs">
            <div class="col-xs-12 col-centered">
                <h4>Parties en cours</h4>
            </div>
        </div>
        <div class="row row-centered">
            <div class="col-xs-2 col-sm-2 col-md-1 col-centered"><b>Nom</b></div>
            <div class="col-md-1 hidden-xs hidden-sm col-centered"><b>Scénario</b></div>
            <div class="col-md-1 hidden-xs hidden-sm col-centered"><b>Début historique</b></div>
            <div class="col-md-1 hidden-xs hidden-sm col-centered"><b>Fin historique</b></div>
            <div class="col-xs-2 col-sm-2 col-md-1 col-centered"><b>Date</b></div>
            <div class="col-xs-1 col-sm-1 col-md-1 col-centered"><b>Tour</b></div>
            <div class="col-md-1 hidden-xs hidden-sm col-centered"><b>Début réel</b></div>
            <div class="col-xs-2 col-sm-2 col-md-1 col-centered"><b>Mise à jour</b></div>
            <div class="col-xs-2 col-sm-2 col-md-1 col-centered"><b>Prochain tour dans</b></div>
            <div class="col-xs-2 col-sm-2 col-md-3 col-centered"><b>En attente de</b></div>
            <hr style="border-top: 3px solid white; width:100%" />
        </div>
        <div class="row row-centered">
        <?php
            //fixe le francais comme langue pour les dates
            $requete="SET lc_time_names = 'fr_FR'";
            mysql_query($requete,$db);

            //affichage des parties en cours
            $requete="SELECT tab_vaoc_jeu.S_NOM AS NOM_JEU, tab_vaoc_partie.S_NOM AS NOM_SCENARIO, DATE_FORMAT(DT_TOUR,'%W %e %M %Y %H:%i') AS DATE_TOUR, ";			
            $requete.="DATE_FORMAT(DT_INITIALE,'%W %e %M %Y %H:%i') AS DATE_INITIALE, I_TOUR, DATE_FORMAT(DT_CREATION,'%W %e %M %Y %T') AS DATE_CREATION, ";
            $requete.="DATE_FORMAT(DATE_ADD(tab_vaoc_jeu.DT_INITIALE, INTERVAL tab_vaoc_jeu.I_NOMBRE_TOURS HOUR),'%W %e %M %Y %H:%i') AS DATE_FIN, ";
            $requete.="DATE_FORMAT(DT_MISEAJOUR,'%W %e %M %Y %T') AS DATE_MISEAJOUR, ID_VICTOIRE, ID_PARTIE, DT_PROCHAINTOUR, FL_DEMARRAGE";			
            //$requete="SELECT tab_vaoc_jeu.S_NOM AS NOM_JEU, tab_vaoc_partie.S_NOM AS NOM_SCENARIO, DT_INITIALE AS DATE_TOUR, I_TOUR, DT_CREATION, DT_MISEAJOUR";			
            $requete.=" FROM tab_vaoc_partie, tab_vaoc_jeu";
            $requete.=" WHERE tab_vaoc_partie.ID_JEU=tab_vaoc_jeu.ID_JEU";
            $requete.=" ORDER BY DT_MISEAJOUR DESC";

            //echo $requete;
            $res_partie = mysql_query($requete,$db);
            while($row = mysql_fetch_object($res_partie))
            {
                echo "<div class=\"row row-centered\">";
                echo "<div class=\"col-xs-2 col-sm-2 col-md-1 col-centered\">";
                if ($row->ID_VICTOIRE>=0)
                {
                    echo "<a id=\"Bilan\" href=\"\" onclick=\"javascript:callVictoire(".$row->ID_PARTIE.");return false;\" class=\"index\" >";
                    echo $row->NOM_SCENARIO;
                    echo "</a>";
                }
                else
                {
                    echo $row->NOM_SCENARIO;
                }
                echo "</div>";
                echo "<div class=\"col-md-1 hidden-xs hidden-sm col-centered\">".$row->NOM_JEU."</div>";
                echo "<div class=\"col-md-1 hidden-xs hidden-sm col-centered\">".$row->DATE_INITIALE."</div>";
                echo "<div class=\"col-md-1 hidden-xs hidden-sm col-centered\">".$row->DATE_FIN."</div>";
                echo "<div class=\"col-xs-2 col-sm-2 col-md-1 col-centered\">".$row->DATE_TOUR."</div>";
                echo "<div class=\"col-xs-1 col-sm-1 col-md-1 col-centered\">".$row->I_TOUR."</div>";
                echo "<div class=\"col-md-1 hidden-xs hidden-sm col-centered\">".$row->DATE_CREATION."</div>";
                echo "<div class=\"col-xs-2 col-sm-2 col-md-1 col-centered\">".$row->DATE_MISEAJOUR."</div>";
                //durée restante avant le prochain tour
                echo "<div class=\"col-xs-2 col-sm-2 col-md-1 col-centered\"";
                if ($row->ID_VICTOIRE<0 && $row->FL_DEMARRAGE=1)
                {
                    echo " id='prochaintour".$row->ID_PARTIE;
                }
                echo "'></div>";

                //joueurs n'ayant pas indiques qu'ils ont donnes leurs ordres pour le tour
                echo "<div class=\"col-xs-2 col-sm-2 col-md-3 col-centered\">";
                $listeattente="";
                if ($row->ID_VICTOIRE>=0)
                {
                    $listeattente="&nbsp;";
                }
                else
                {
                    $requete="SELECT tab_vaoc_role.S_NOM";			
                    $requete.=" FROM tab_vaoc_role";
                    $requete.=" WHERE B_ORDRES_TERMINES=0 AND tab_vaoc_role.ID_PARTIE=".$row->ID_PARTIE;
                    $requete.=" ORDER BY S_NOM";
                    $res_attente = mysql_query($requete,$db);
                    while($row_attente = mysql_fetch_object($res_attente))
                    {
                        if ($listeattente<>"") {$listeattente.=", ";}
                        $listeattente.= $row_attente->S_NOM;
                    }
                    if ($listeattente=="")
                    {
                        $listeattente="Tous les joueurs ont rendu leurs ordres";				
                    }
                }
                echo $listeattente;
                echo "</div>";
                echo "<hr style=\"border-top: 1px solid white; width:100%\"/>";
                echo "</div>";
            }
        ?>
        </div>

        <form id="principal" method="post" class="form-inline" role="form">
            <input name="id_action" id="id_action" type="hidden" />
            <input name="id_partie" id="id_partie" type="hidden" />
                <?php
                if(FALSE==empty($id_login) && ""!=$id_login)
                {
                    //l'utilisateur est connecte
                    echo "<input id='id_login' name='id_login' type='hidden' value='".$id_login."' />";
                    echo "<div class=\"row row-centered\">";
                        echo "<div class=\"col-xs-12 col-sm-3 col-md-3 \">";
                        echo "<div class=\"form-group \" >";
                            echo "<h3>".$id_login."</h3>";
                        echo "</div>";
                        echo "</div>";
                    //echo "<div class=\"form-group\"  style=\"margin-left:20px;\">";
                        echo "<div class=\"col-xs-12 col-sm-3 col-md-3 \">";
                        echo "<div class=\"form-group \" >";
                            echo "<button alt='deconnexion au service' id='id_deconnexion' name='id_deconnexion'  type='submit' class=\"btn btn-default\" onclick=\"javascript:callDeconnexion();\">";
                            echo "<img src=\"images/btnDeconnexion2.png\">";
                            echo "</button>";
                    //echo "<input alt='deconnexion au service' id='id_deconnexion' name='id_deconnexion'  type='image' value='submit' class=\"btn btn-default\" src=\"images/btnDeconnexion2.png\" onclick=\"javascript:callDeconnexion();\"/>";
                        echo "</div>";
                        echo "</div>";
                    //echo "<div class=\"form-group\" style=\"margin-left:20px;\">";
                        echo "<div class=\"col-xs-12 col-sm-3 col-md-3 \">";
                        echo "<div class=\"form-group \" >";
                            echo "<input alt=\"modification d'un compte\" id='id_connexion_modification' name='id_connexion_modification'  type='image' value='submit' class=\"btn btn-default\" src=\"images/btnCompte2.png\" onclick=\"javascript:callModifierConnexion();\"/>";
                        echo "</div>";
                        echo "</div>";
                    //echo "<div class=\"form-group\" style=\"margin-left:20px;\">";
                        echo "<div class=\"col-xs-12 col-sm-3 col-md-3 \">";
                        echo "<div class=\"form-group \" >";
                            echo "<input alt=\"Acces à mon QG\" id='id_connexion_mon_jeu' name='id_connexion_mon_jeu'  type='image' value='submit' class=\"btn btn-default\" src=\"images/AccesQG2.png\" onclick=\"javascript:callQG();\"/>";
                        echo "</div>";
                        echo "</div>";
                    echo "</div>";
                }
                else
                {
                    //l'utilisateur n'est pas encore connecté
                    echo "<div class=\"row row-centered\">";
                        echo "<div class=\"col-xs-12 \"><h2>Donnez le mot de passe à la sentinelle pour rejoindre votre QG.</h2></div>";
                    echo "</div>";

                    echo "<div class=\"row row-centered\">";
                        echo "<div class=\"col-xs-12 col-sm-5 col-md-4 \">";
                            echo "<div class=\"form-group\">";
                                echo "<label for=\"id_loginTest\" class=\"control-label \">Nom&nbsp;</label>";		
                                echo "<input name=\"id_loginTest\" class=\"form-control\" id=\"id_loginTest\" size=\"30\" maxlength=\"30\" placeholder=\"nom\""; 
                                if(FALSE==empty($id_loginTest))
                                {
                                    echo "value='".$id_loginTest."'";
                                }
                                echo "/>";
                            echo "</div>";
                        echo "</div>";

                        /*echo "<div class=\"form-group \"   style=\"margin-left:28px;\">";*/
                        echo "<div class=\"col-xs-12 col-sm-7 col-md-5 \">";
                            echo "<div class=\"form-group \">";
                                echo "<label for=\"id_password\" class=\"control-label \">Mot de passe&nbsp;</label>";
                                echo "<div class=\"input-group\">";
                                    echo "<div class=\"input-group-addon\">*</div>";
                                    echo "<input name=\"id_password\" class=\"form-control\" id=\"id_password\" type=\"password\" size=\"30\" maxlength=\"30\" placeholder=\"mot de passe\"/>";
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";

                        /*echo "<div class=\"form-group \" style=\"margin-left:28px;\">";*/
                        echo "<div class=\"col-xs-12 col-sm-12 col-md-3 \">";
                            echo "<div class=\"form-group \" >";
                                echo "<input name=\"id_connexion\" class=\"btn btn-default \" ";
                                echo "id=\"id_connexion\" onclick=\"javascript:callConnexion();\"";
                                echo "type=\"image\" alt=\"connexion\" src=\"images/btnConnexion2.png\" value=\"submit\" />";
                            echo "</div>";
                        echo "</div>";
                    echo "</div>";
                }
                ?>
            <div class="row row-centered" style="padding-top:8px;">
                <div class="col-xs-12 col-centered">
                    <input name="id_aide" class="btn btn-info"
                        id="id_aide" onclick="javascript:window.open('aide.html'); return false;"
                        type="image" alt="aide" src="images/btnAide2.png" />
                </div>
            </div>
        </form>
    </div>
</body>
</html>
