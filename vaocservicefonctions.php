<script language="php">

//fonctions pour le service
function jeux($db,&$message)
{
  $requete="SELECT ID_JEU, S_NOM, NOMBRE_TOURS, NOMBRE_PHASES, DT_INITIALE ";
	$requete.="FROM TAB_VAOC_JEU ";
	$requete.="ORDER BY S_NOM ";
	//echo $requete;
	$res_jeux = mysql_query($requete,$db);
  $message=$message."<JEUX>";
  while($row = mysql_fetch_object($res_jeux))
  {
    $message=$message."<JEU>";
  	$message=$message."<ID_JEU>".$row->ID_JEU."</ID_JEU>";
  	$message=$message."<S_NOM>".$row->S_NOM."</S_NOM>";
  	$message=$message."<NOMBRE_TOURS>".$row->NOMBRE_TOURS."</NOMBRE_TOURS>";
  	$message=$message."<NOMBRE_PHASES>".$row->NOMBRE_PHASES."</NOMBRE_PHASES>";
  	$message=$message."<DT_INITIALE>".$row->DT_INITIALE."</DT_INITIALE>";
  	$message=$message."</JEU>";
	}
  $message=$message."</JEUX>";
}

function creerJeu($db,$nom,$idcarte,$nbtours,$nbphases,$dateinitiale,&$message)
{
	//ajout du nouveau jeu		
	$requete="INSERT INTO TAB_VAOC_JEU (ID_CARTE, S_NOM, NOMBRE_TOURS, NOMBRE_PHASES, DT_INITIALE) 
			VALUES (".$idcarte.",'".$nom."',".$nbtours.",".$nbphases.",'".$dateinitiale."')";
  //echo "<br/>requete:".$requete;
	mysql_query($requete,$db);

	//recherche du nouvel identifiant
  $requete="SELECT ID_JEU FROM TAB_VAOC_JEU WHERE";
  $requete.=" TAB_VAOC_JEU.S_NOM='".$nom."'";
  $requete.=" AND TAB_VAOC_JEU.ID_CARTE=".$idcarte;
  //echo "<br/>sql_request:".$sql_request;
	$res_jeu = mysql_query($requete,$db);
	if (mysql_num_rows($res_jeu)>0)
	{
	  $row = mysql_fetch_object($res_jeu);
  	$message=$message."<ID_JEU>".$row->ID_JEU."</ID_JEU>";
	}
	else
	{
    $message=$message."<ERREUR>Echec lors de l'insertion:".$requete."</ERREUR>";
	}
}

function creerPartie($db,$nom,$idjeu,&$message)
{
	//ajout de la nouvelle partie		
	$requete="INSERT INTO TAB_VAOC_PARTIE (ID_JEU, S_NOM, I_TOUR, I_PHASE, DT_CREATION, DT_MISEAJOUR) 
			VALUES (".$idjeu.",'".$nom."',0,0,'".date("Y-m-d H:i:s")."','".date("Y-m-d H:i:s")."')";
  //echo "<br/>requete:".$requete;
	mysql_query($requete,$db);

	//recherche du nouvel identifiant
  $requete="SELECT ID_PARTIE FROM TAB_VAOC_PARTIE WHERE";
  $requete.=" TAB_VAOC_PARTIE.S_NOM='".$nom."'";
  $requete.=" AND TAB_VAOC_PARTIE.ID_JEU=".$idjeu;
  //echo "<br/>sql_request:".$sql_request;
	$res_partie = mysql_query($requete,$db);
	if (mysql_num_rows($res_partie)>0)
	{
	  $row = mysql_fetch_object($res_partie);
    $message=$message."<ID_PARTIE>".$row->ID_PARTIE."</ID_PARTIE>";
  }
  else
  {
    $message=$message."<ERREUR>Echec lors de l'insertion:".$requete."</ERREUR>";
	}
}

function utilisateurs($db,&$message)
{
  $requete="SELECT ID_UTILISATEUR, S_LOGIN, DT_CREATION, DT_DERNIERECONNEXION , S_NOM, S_PRENOM ";
	$requete.="FROM TAB_UTILISATEURS ";
	$requete.="ORDER BY S_NOM ";
	//echo $requete;
	$res_utilisateurs = mysql_query($requete,$db);
  $message=$message."<UTILISATEURS>";
  while($row = mysql_fetch_object($res_utilisateurs))
  {
    $message=$message."<UTILISATEUR>";
  	$message=$message."<S_NOM>".$row->S_NOM."</S_NOM>";
  	$message=$message."<S_PRENOM>".$row->S_PRENOM."</S_PRENOM>";
  	$message=$message."<S_LOGIN>".$row->S_LOGIN."</S_LOGIN>";
  	$message=$message."<DT_CREATION>".$row->DT_CREATION."</DT_CREATION>";
  	$message=$message."<DT_DERNIERECONNEXION>".$row->DT_DERNIERECONNEXION."</DT_DERNIERECONNEXION>";
  	$message=$message."<ID_UTILISATEUR>".$row->ID_UTILISATEUR."</ID_UTILISATEUR>";
  	$message=$message."</UTILISATEUR>";
	}
  $message=$message."</UTILISATEURS>";
}

function creerCarte($db,$nom,&$message)
{
  //on vérifie si la carte n'existe pas déjà
  $requete="SELECT ID_CARTE FROM TAB_VAOC_CARTE WHERE";
  $requete.=" TAB_VAOC_CARTE.S_NOM='".$nom."'";
  //echo "<br/>sql_request:".$sql_request;
	$res_carte = mysql_query($requete,$db);
	if (mysql_num_rows($res_carte)>0)
	{
    $message=$message."<ERREUR>Une carte du même nom existe déjà</ERREUR>";
		return;
	}
	
	//ajout de la nouvelle carte		
	$requete="INSERT INTO TAB_VAOC_CARTE (S_NOM) VALUES ('".$nom."')";
  //echo "<br/>requete:".$requete;
	mysql_query($requete,$db);

	//recherche du nouvel identifiant
  $requete="SELECT ID_CARTE FROM TAB_VAOC_CARTE WHERE";
  $requete.=" TAB_VAOC_CARTE.S_NOM='".$nom."'";
  //echo "<br/>sql_request:".$sql_request;
	$res_carte = mysql_query($requete,$db);
	if (mysql_num_rows($res_carte)>0)
	{
	  $row = mysql_fetch_object($res_carte);
  	$message=$message."<ID_CARTE>".$row->ID_CARTE."</ID_CARTE>";
	}
	else
	{
    $message=$message."<ERREUR>Echec lors de l'insertion</ERREUR>";
	}
}

function nomCarteVider($db,$idjeu,&$message)
{
  //on compte le nombre de lignes que l'on va supprimer (c'est purement informatif)
  $requete="SELECT ID_NOM FROM TAB_VAOC_NOMS_CARTE WHERE";
  $requete.=" ID_JEU=".$idjeu;
	$res_carte = mysql_query($requete,$db);
	$nb_lignes=mysql_num_rows($res_carte);

  $requete="DELETE FROM TAB_VAOC_NOMS_CARTE WHERE ID_JEU=".$idjeu;
	$res_carte = mysql_query($requete,$db);
	if (false==$res_carte)
	{
    $message="<ERREUR>Erreur à l'execution de :".requete."</ERREUR>";
		return;
	}
  $message.="<RETOUR>".$nb_lignes." lignes supprimés dans TAB_VAOC_NOMS_CARTE</RETOUR>";
}

function nomCarteAjouter($db,$idjeu,$nom,$idnom,$idcase,$x,$y,&$message)
{
  //on vérifie si le nom n'existe pas déjà
  $requete="SELECT S_NOM FROM TAB_VAOC_NOMS_CARTE WHERE";
  $requete.=" ID_NOM='".$idnom."'";
  $requete.=" AND ID_JEU='".$idjeu."'";
  //echo "<br/>sql_request:".$sql_request;
	$res_carte = mysql_query($requete,$db);
	if (mysql_num_rows($res_carte)>0)
	{
	  $row = mysql_fetch_object($res_carte);
    $message=$message."<ERREUR>L'identifant du nom existe déjà sur la carte avec le nom :".$row->S_NOM."</ERREUR>";
		return;
	}
	
	//ajout du nouveau nom		
	$requete="INSERT INTO TAB_VAOC_NOMS_CARTE (ID_NOM, ID_JEU, S_NOM, I_X, I_Y) VALUES (".$idnom.",".$idjeu.",'".$nom."',".$x.",".$y.")";
  //echo "<br/>requete:".$requete;
	mysql_query($requete,$db);

	//recherche du nouvel identifiant (en fait, là, on renvoit juste l'identifiant de carte pour dire que c'est ok
  $requete="SELECT ID_NOM FROM TAB_VAOC_NOMS_CARTE WHERE";
  $requete.=" S_NOM='".$nom."'";
  $requete.=" AND ID_JEU='".$idjeu."'";
  //echo "<br/>sql_request:".$sql_request;
	$res_carte = mysql_query($requete,$db);
	if (mysql_num_rows($res_carte)>0)
	{
	  $row = mysql_fetch_object($res_carte);
  	$message=$message."<ID_NOM>".$row->ID_NOM."</ID_NOM>";
	}
	else
	{
    $message=$message."<ERREUR>";
    $message.="Echec lors de l'insertion : ".$requete;
    $message.="</ERREUR>";
	}
}

function miseAjour($db,$idpartie,$encours,&$message)
{
  //on compte le nombre de lignes que l'on va supprimer (c'est purement informatif)
  $requete="UPDATE TAB_VAOC_PARTIE";
  $requete.=" SET FL_MISEAJOUR=".$encours;
  $requete.=" WHERE ID_PARTIE=".$idpartie;
	$res_miseajour = mysql_query($requete,$db);

	if (false==$res_miseajour)
	{
    $message="<ERREUR>Erreur à l'execution de :".requete."</ERREUR>";
		return;
	}
  $message.="<RETOUR>OK</RETOUR>";
}

</script>
