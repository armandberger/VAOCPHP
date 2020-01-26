<script language="php">
  /*** Ensemble de fonctions utilisées pour les connections aux bases de données ***/

  //functions pour les bases de données
  function db_connect()
  {
    $servername = 'localhost';
    $dbname = 'vaoc';
    $login = 'root';//vaoc ou root
    $password = 'voxvox';//'stopSTOP' ou '' ou 'Kleio2000' ou 'root' ou 'voxvox';
    //note ces fonctions sont obsoletes sous PHP 7
    $dbh = @mysql_connect($servername, $login, $password) or die ("<p><font size='+2' color='white'>probleme de connexion à la base : ".mysql_error()."</font></p>");
    //mysql_set_charset("utf8", $dbh);
    //$dbh = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root', '');

    @mysql_select_db($dbname, $dbh) or die ("<p><font size='+2' color='white'>probleme dans selection base : ".mysql_error()."</font></p>");
    return $dbh ;
};

function db_disconnect(&$dbh)
{
	mysql_close($dbh);
	$dbh=0;
};

</script>
