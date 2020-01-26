<?php
	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>";
	/*echo "<?xml version=\"1.0\" encoding=\"UTF-16\"?>"; */ 
	require("vaocbase.php");//include obligatoire pour l'executoion
	require("vaocfonctions.php");//include obligatoire pour l'executoion
	require("vaocservicefonctions.php");//include obligatoire pour l'executoion

  //converti toutes les variables REQUEST en variables du meme nom
  extract($_REQUEST,EXTR_OVERWRITE);

  //connection à la base
  $db = @db_connect();

  //pratique pour le debug
  //while (list($name, $value) = each($HTTP_POST_VARS)) {echo "$name = $value<br>\n";}

	//y 'a-t-il une opération demandées
  $message="";
  if(TRUE==empty($op))
	{
    $message=$message."<ERREUR>";
    $message=$message."Les opérations possibles (op=) sont: version, utilisateurs, jeux, miseajour, creerjeu, creerpartie, creercarte, nomcarteajouter, nomcartevider";
    $message=$message."</ERREUR>";
	}
	else
	{
   		if("version"==$op)
			{
    	  $message=$message."<VERSION>1.0</VERSION>";
			}

   		if("utilisateurs"==$op)
			{
			  utilisateurs($db,$message);
			}

   		if("jeux"==$op)
			{
			  jeux($db,$message);
			}

   		if("miseajour"==$op)
			{
        if(TRUE==empty($idpartie) || TRUE==empty($encours))
      	{
          $message=$message."<ERREUR>Les paramètres sont: idpartie, encours(0 ou 1)</ERREUR>";
      	}
				else
				{
				  miseAjour($db,$idpartie,$encours,&$message);
				}
			}

   		if("creerjeu"==$op)
			{
        if(TRUE==empty($nom) || TRUE==empty($idcarte) || TRUE==empty($nbtours) || TRUE==empty($nbphases) || TRUE==empty($dateinitiale))
      	{
          $message=$message."<ERREUR>Les paramètres sont: nom, idcarte, nbtours, nbphases, dateinitiale</ERREUR>";
      	}
				else
				{
				  creerJeu($db,$nom,$idcarte,$nbtours,$nbphases,$dateinitiale,&$message);
				}
			}

   		if("creerpartie"==$op)
			{
        if(TRUE==empty($nom) || TRUE==empty($idjeu))
      	{
          $message=$message."<ERREUR>Les paramètres sont: nom, idjeu</ERREUR>";
      	}
				else
				{
			    creerPartie($db,$nom,$idjeu,$message);
				}
			}

   		if("creercarte"==$op)
			{
        if(TRUE==empty($nom))
      	{
          $message=$message."<ERREUR>Les paramètres sont: nom</ERREUR>";
      	}
				else
				{
				  creerCarte($db,$nom,$message);
				}
			}

   		if("nomcarteajouter"==$op)
			{
        if(TRUE==empty($nom) || TRUE==empty($idjeu) || TRUE==empty($x) || TRUE==empty($y) || TRUE==empty($idnom))
      	{
          $message=$message."<ERREUR>Les paramètres sont: nom, idjeu, x, y, idnom</ERREUR>";
      	}
				else
				{
				  nomCarteAjouter($db,$idjeu,$nom,$idnom,$x,$y, $message);
				}
			}
			
   		if("nomcartevider"==$op)
			{
        if(TRUE==empty($idjeu))
      	{
          $message=$message."<ERREUR>Les paramètres sont: idjeu</ERREUR>";
      	}
				else
				{
				  nomCarteVider($db,$idjeu,$message);
				}
			}

   		if(""==$message)
			{
    	  $message=$message."<ERREUR>opération ".$op."non reconnue. Les opérations sont : version, utilisateurs, creerjeu, creerpartie, creercarte, creernomcarte.</ERREUR>";
			}
	}
	echo $message;

?>
