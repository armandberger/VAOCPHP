<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
    <head>
	<title>Compte pour VAOC</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<meta name="Description" content="VAOC, modification de comptes"/>
	<meta name="Keywords" content="VAOC, connection, compte"/>
	<meta name="Identifier-URL" content="http://vaoc.free.fr/vaoc/vaocmodifierutilisateur.php"/>
	<meta name="revisit-after" content="31"/>
	<meta name="Copyright" content="copyright armand BERGER"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> 
        <link rel="icon" type="image/png" href="/images/favicon.png" />
        <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
        <link rel="stylesheet" type="text/css" href="vaoc.css"/>
        <link href="css/bootstrap.css" rel="stylesheet"/>
        <style type="text/css"> 
        body
        {
	background-color:#F0F0F0; 
	text-align: center; 
        }
        </style>	
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="js/jquery-3.1.1.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="js/bootstrap.js"></script>
<script type="text/javascript">
function counterUpdate(opt_countedTextBox, opt_countBody, opt_maxSize) 
{
    var countedTextBox = opt_countedTextBox ? opt_countedTextBox : "counttxt";
    var countBody = opt_countBody ? opt_countBody : "countBody";
    var maxSize = opt_maxSize ? opt_maxSize : 1024;

    var field = document.getElementById(countedTextBox);

    if (field && field.value.length >= maxSize) {
            field.value = field.value.substring(0, maxSize);
    }
    var txtField = document.getElementById(countBody);
            if (txtField) { 
            txtField.innerHTML = field.value.length;
}
}

function textCounter(field, countfield, maxlimit) 
{
    if (field.value.length > maxlimit) // if too long...trim it!
	{
  	  field.value = field.value.substring(0, maxlimit);
	}
  	else
	{ 
  	  // otherwise, update 'characters left' counter
  		countfield.value = maxlimit - field.value.length;
	}
}

function callQuitter() 
{
 	formPrincipale=document.getElementById("principal");
 	formPrincipale.action="index.php";
 	formPrincipale.target="_self";
 	formPrincipale.submit();
}

function callMaj()
{
	document.getElementById("id_action").value="maj";
 	formPrincipale=document.getElementById("principal");
 	formPrincipale.action="vaocmodifierutilisateur.php";
 	formPrincipale.target="_self";
 	formPrincipale.submit();
}
</script>
</head>
<?php
	require("vaocbase.php");//include obligatoire pour l'executoion
	require("vaocfonctions.php");//include obligatoire pour l'executoion

	//converti toutes les variables REQUEST en variables du meme nom
	extract($_REQUEST,EXTR_OVERWRITE);

	//connection � la base
	$db = @db_connect();
	//pratique pour le debug
	//while (list($name, $value) = each($HTTP_POST_VARS)) {echo "$name = $value<br>\n";}

        //        //mysql_set_charset("utf-8", $db); -> pas valide dans la version php free
        //fixe le francais comme langue pour les dates
        $requete = "SET lc_time_names = 'fr_FR'";
        mysql_query($requete, $db);
        mysql_query("SET NAMES 'utf8'");
	
	//s'agit d'un postback ?
	$message="";
	if(FALSE==empty($id_action))
	{
		//on valide la saisie
		if (strlen(trim($id_password))<6 )
		{
	    	$message=$message."Vous devez saisir un mot de passe d'au moins 6 lettres.<br/>";
		}
		if ($id_password!=$id_password2)
		{
	    	$message=$message."Vous devez re-saisir le meme mot de passe.<br/>";
		}
		if (""==$id_nom)
		{
	    	$message=$message."Vous devez saisir votre nom de famille.<br/>";
		}
		if (""==$id_prenom)
		{
	    	$message=$message."Vous devez saisir votre pr&eacute;nom.<br/>";
		}
		if (""==$id_courriel)
		{
	    	$message=$message."Vous devez saisir une adresse de messagerie.<br/>";
		}
		if (""==$id_question)
		{
	    	$message=$message."Vous devez saisir une question secr�te.<br/>";
		}
		if (""==$id_reponse)
		{
	    	$message=$message."Vous devez saisir une r�ponse � votre question secr�te.<br/>";
		}
		
		//s'il n'y a pas d'erreurs
		if (strlen($message)==0)
		{
		  	//sinon on met a jour les informations de l'utilisateur en base et on revient sur la page g�n�rale
			$requete="UPDATE tab_utilisateurs";
			$requete=$requete." SET S_MOTDEPASSE='".$id_password."'";			
			$requete=$requete.", S_NOM='".$id_nom."'";			
			$requete=$requete.", S_COURRIEL='".$id_courriel."'";			
			$requete=$requete.",S_PRENOM='".$id_prenom."'";			
			$requete=$requete.",S_QUESTION='".$id_question."'";			
			$requete=$requete.",S_REPONSE='".$id_reponse."'";			
			$requete=$requete." WHERE S_LOGIN='".$id_login."'";
			//echo $requete;			
			if (!mysql_query($requete))
			{
				die("Error: sur la requete ".$requete ."avec l'erreur". mysql_error());
			}

			//header("Location: index.html");
			//il faut faire un post vers la nouvelle de fa�on a transmettre l'ination de connexion
			/*
			echo "< method=\"post\" name=\"principal\" target=\"_self\" action=\"index.php\">\n";
		  echo "<input id='id_login' name='id_login' value='".trim($id_login)."' />";
			echo "</>\n";

			echo "<script language=\"JavaScript\" type=\"text/javascript\">";
		 	echo "Principale=document.getElementById(\"principal\");";
		 	echo "Principale.submit();";
			echo "</script>";
			*/
		}
	}
?>

<body>
<div class="container">
    <div class="row  hidden-xs hidden-sm">
        <div class="col-xs-4 col-centered">
            <img alt="drapeau_napoleon" src="images/drapeau_napoleon_mini.jpg"  />            
        </div>
        <div class="col-xs-4 col-centered">
            <h1>  V A O C  </h1>
        </div>
        <div class="col-xs-4 col-centered">
            <img alt="drapeau_napoleon" src="images/drapeau_napoleon_mini.jpg" />
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-centered">
            <h2>Configuration du compte</h2>
        </div>
    </div>

<?php
    //pratique pour le debug
    /*
	echo "liste des valeurs transmises dans le post<br/>";
  	while (list($name, $value) = each($HTTP_POST_VARS)) {echo "$name = $value<br>\n";}
	echo "liste des valeurs transmises dans le request<br/>";
  	while (list($name, $value) = each($_REQUEST)) {echo "$name = $value<br>\n";}
    */
    //s'il y a des erreurs, on les affiche
    if (strlen($message)>0)
    {
      echo "<div class=\"alerte\">".$message."</div>";
    }
?>
<form id="principal" method="post" >
    <input id='id_action' name='id_action' type='hidden' />

<?php
	echo "<input id='id_login' name='id_login' type='hidden' value='".$id_login."' />";
	$requete="SELECT S_NOM, S_PRENOM, S_QUESTION, S_REPONSE, S_COURRIEL FROM tab_utilisateurs WHERE S_LOGIN='".$id_login."'";
	$res_login = mysql_query($requete,$db);
	$login=mysql_fetch_object($res_login);
?>

    <div class="row">
        <div class="hidden-xs hidden-sm col-md-3">
            <img alt="garde" src="images/fondcompte.png" height="500px"/>
        </div>
        <div class="col-xs-12 col-md-9">
            <div class="row">
                <div class="col-xs-12 col-sm-6">Nom d'utilisateur</div>
                <div class="col-xs-12 col-sm-6">*&nbsp;
                <?php
                    echo "<input maxlength='30' size='30' id='id_login' name='id_login' disabled=\"disabled\"";
                    if(FALSE==empty($id_login))
                    {
                      echo "value='".trim($id_login)."' />";
                    }
                    else
                    {
                      echo "value='ERREUR !'  />";
                    }
                ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">Mot de passe</div>
                <div class="col-xs-12 col-sm-6">*&nbsp;
                    <?php
                    echo "<input maxlength='30'  size='30' id='id_password' name='id_password'  type='password'";
                    if(FALSE==empty($id_password))
                    {
                      echo "value='".trim($id_password)."' />";
                    }
                    else
                    {
                      echo " />";
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">Ressaisie du mot de passe</div>
                <div class="col-xs-12 col-sm-6">*&nbsp;
                <?php
                    echo "<input maxlength='30' size='30' id='id_password2' name='id_password2' type='password'";
                    if(FALSE==empty($id_password2))
                    {
                        echo "value='".trim($id_password2)."' />";
                    }
                    else
                    {
                        echo " />";
                    }
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">Adresse de messagerie</div>
                <div class="col-xs-12 col-sm-6">*&nbsp;
                <?php
                    echo "<input maxlength='100'  size='50' id='id_courriel' name='id_courriel'";
                    if(FALSE==empty($login))
                    {
                      echo "value='".$login->S_COURRIEL."' />";
                    }
                    else
                    {
                      echo "value='ERREUR !'/>";
                    }
                ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">Nom</div>
                <div class="col-xs-12 col-sm-6">*&nbsp;
                <?php
                    echo "<input maxlength='50' size='50' id='id_nom' name='id_nom'";
                    if(FALSE!=$login)
                    {
                      echo "value='".$login->S_NOM."' />";
                    }
                    else
                    {
                      echo "value='ERREUR !'/>";
                    }
                ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">Pr&eacute;nom</div>
                <div class="col-xs-12 col-sm-6">*&nbsp;
                <?php
                    echo "<input maxlength='50' size='50' id='id_prenom' name='id_prenom'";
                    if(FALSE!=$login)
                    {
                        echo "value='".$login->S_PRENOM."' />";
                    }
                    else
                    {
                        echo "value='ERREUR !'/>";
                    }
                ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">Questions secr&egrave;te (cette question vous sera pos&eacute;e si vous perdez votre mot de passe)
                <?php
                    echo "<span id=\"LgQuestion\">";
                    if(FALSE==empty($LgQuestion))
                    {
                      echo $LgQuestion;
                    }
                    else
                    {
                      echo strlen($login->S_QUESTION);
                    }
                    echo "</span> caract&egrave;res (Maximum : 500 Caract&egrave;res)";
                ?>
                </div>
                <div class="col-xs-12 col-sm-6">*&nbsp;
                <?php
                    echo "<textarea rows=\"5\" id=\"id_question\" name=\"id_question\" cols=\"50\"  onkeydown=\"counterUpdate('id_question','LgQuestion',500);\" onkeyup=\"counterUpdate('id_question','LgQuestion',500);\">";
                    if(FALSE!=$login)
                    {
                      echo $login->S_QUESTION;
                    }
                    echo "</textarea>";
                ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-sm-6">R&eacute;ponse &agrave; la question secr&ecirc;te
                <?php
                    echo "<span id=\"LgReponse\">";
                    if(FALSE==empty($LgReponse))
                    {
                      echo "value='".$LgReponse."'";
                    }
                    else
                    {
                      echo strlen($login->S_REPONSE);
                    }
                    echo "</span> caract&egrave;res (Maximum : 500 Caract&egrave;res)";
                ?>
                </div>
                <div class="col-xs-12 col-sm-6">*&nbsp;
                <?php
                    echo "<textarea rows=\"5\" id=\"id_reponse\" name=\"id_reponse\" cols=\"50\"  onkeydown=\"counterUpdate('id_reponse','LgReponse',500);\" onkeyup=\"counterUpdate('id_reponse','LgReponse',500);\">";
                    if(FALSE!=$login)
                    {
                      echo $login->S_REPONSE;
                    }
                    echo "</textarea>";
                ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-centered">
              Les champs marqu&eacute;s d'une &eacute;toile sont obligatoires (oui, tous !).
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 col-centered">
            <input name="id_maj" class="btn btn-default"
               id="id_maj" onclick="javascript:callMaj();"
               type="image" alt="mise a jour" src="images/btnMiseAJour2.png" value="submit" />                
            <input name="id_quitter" class="btn btn-default"
               id="id_quitter" onclick="javascript:callQuitter();"
               type="image" alt="quitter" src="images/btnQuitter2.png" value="submit" />     
        </div>
    </div>
</form>
</div>
 <?//php phpinfo(); ?>
</body>
</html> 