<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
 <head>
    <title>VAOC : Aide</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="Description" content="VAOC, Aide"/>
    <meta name="Keywords" content="Vaoc, Aide, vol de l'aigle"/>
    <meta name="Identifier-URL" content="http://vaoc.free.fr/vaoc/aide.html"/>
    <meta name="revisit-after" content="31"/>
    <meta name="Copyright" content="copyright armand BERGER"/>
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
    <link rel="stylesheet" type="text/css" href="vaoc.css"/>
    <link href="css/bootstrap4.min.css" rel="stylesheet" />
    <link href="css/vaoc2.css" rel="stylesheet"></link>    
    <style type="text/css"> 
    body
    {
        background-color:white; 
        color:black; 
        background-image:url(images/fondaide.png);
    }
    .tableau td
    {
        padding: 5px;
    }
    </style>
<script type="text/javascript">

/** Fonction basculant la visibilité d'un élément dom
* @parameter anId string l'identificateur de la cible à montrer, cacher*/
function toggle(anId)
{
	node = document.getElementById(anId);
	if (node.style.display=="none")
	{
		// Contenu caché, le montrer
		node.style.display="block";
	}
	else
	{
		// Contenu visible, le cacher
		node.style.display = "none";
	}
	return true;
}

function fermetureGlobale()
{
	toggle('introduction');
	toggle('technique');
	toggle('principes');
	toggle('frequence');
	toggle('connexion');
	toggle('compte');
	toggle('qg');
	toggle('cartes');
	toggle('bataille');
	toggle('bilan');
	toggle('faq');
}
</script>
</head>
<body>
<div class="container">
<h1>AIDE</h1>
<div>
Si vous ne trouvez pas de réponses à vos questions dans cette page, ou souhaitez contacter l'auteur de VAOC vous pouvez lui écrire.
    <a id="id_ecrire" href="mailto:vaoc@free.fr?subject=VAOC : Questions"><input name="id_btn_ecrire" class="btn btn-info"
           id="id_btn_ecrire" 
           type="image" alt="Ecrire" src="images/btnEcrire2.png" value="submit" /></a>             
</div>
<div class="aidesection">
<a onclick = "toggle('introduction')" class="aide">
<img alt="section" class="aidesection" src="images/aidesection.png" />Introduction : Qu'est ce que VAOC ? VAOC et le "vol de l'aigle"
</a>
</div>
<div class="aidetexte" id="introduction">
<p class="aidetexte">
VAOC est un programme informatique et un site web qui permettent d'arbitrer une partie simulant une campagne Napoléonnienne entre plusieurs joueurs.<br/>
</p>
<table>
	<tr><td><a href="http://www.didier-rouy.webs.com/"><img alt="boite du vol de l'aigle" height="150px" class="aidesection" src="images/vol_de_laigle_boite1.jpg" /></a></td>
	<td>Les règles sur lesquelles s'appuient ces programmes s'appuient très largement sur le fantastique jeu de Didier Rouy, jeu que vous pouvez commander sur
		<a href="http://www.didier-rouy.webs.com/">http://www.didier-rouy.webs.com/</a>.</td>
	</tr>
</table><br/>
<p class="aidetexte">
Ce programme est une initiative personnelle réalisée sans le concours de Didier Rouy (mais avec son accord, merci Didier). Générallement pour des raisons techniques, certaines modifications ont dû être apportées par rapport au système initial, VAOC
n'est donc pas une réalisation strictement équivalente au jeu le vol de l'aigle. Il n'est pas nécessaire de posséder ou de connaître "le vol de l'aigle" pour jouer à VAOC.
</p>
<p class="aidetexte">
Pratiquant le wargame depuis plus de 25 ans, le "Vol de l'aigle" est selon moi le seul jeu a avoir apporté un véritable nouvelle dimension ludique en simulant de manière inégalé 
 le brouillard de guerre ainsi que la lenteur des communications et de toute action d'une manière générale à l'époque Napoléonienne.
</p>
<p class="aidetexte">
Après avoir joué plusieurs parties tant comme joueur que comme arbitre, j'ai fait le constat que d'une part, le temps de jeu d'une campagne la rendait difficilement jouable sur un week-end, 
d'autre part que la tâche d'arbitrage d'une partie était très lourde. Ces deux facteurs font que de très nombreuses parties sur le web ont été commencées puis abandonnées, généralement
par abandon d'un arbitre motivé mais épuisé par l'ampleur de la tâche.
</p>
<p class="aidetexte">
VAOC a donc pour mission d'effectuer (presque) tout le travail de l'arbitre. L'objectif étant ainsi d'augmenter le nombre de parties jouées et le nombre de joueurs participants.
</p>
<p class="aidetexte">
Pour ceux qui ont la chance de connaître "le vol de l'aigle", sachez que VAOC utilise les règles du troisième livret, cependant j'ai du effectuer quelques modifications des règles 
principallement pour des raisons techniques. Pour ceux qui possède la règle merci donc de lire attentivement cette aide, il y a, parfois des modifications sensibles. La gestion des batailles, la capacité spéciale de Napoléon
ne sont que quelques exemples.<br/> 
VAOC ne pourra donc pas remplacer le plaisir d'une vraie partie avec un arbitre réel capable de mettre du "rôle" dans le jeu. Donc, si vous entendez parler d'un arbitre qui cherche des joueurs pour
une partie, n'hésitez pas, inscrivez vous !<br/>
</p>
</div>
<div class="aidesection">
<a onclick = "toggle('technique')" class="aide"><img alt="section" class="aidesection" src="images/aidesection.png" />Navigateurs supportés et configuration techniques</a>
</div>
<div class="aidetexte"  id="technique" >
<p class="aidetexte">
VAOC est testé sur Internet Explorer (version 11 et supérieures), Chrome et Firefox. Il ne sera pas supporté sur d'autres navigateurs (ce qui ne veut pas dire que cela ne marche pas mais que s'il y a
des bugs spécifiques, je ne les corrigerai pas).
</p>
<p class="aidetexte">
Pour ceux que la technique passionne (ou intrigue) sachez que VAOC c'est environ 8000 lignes de code HTML/PHP et 17000 lignes de code C# .Net. Si vous avez des talents de graphiste, je suis intéressé
par vos propositions d'améliorations. Si vous êtes informaticien, je ne souhaite pas partager mes développements, exerçant comme chef de projet depuis 20 ans je 
ne connais que trop bien les difficultés liés à la gestion d'une équipe. Le code de la partie C# est disponible sur <a href="https://github.com/armandberger/VAOC">https://github.com/armandberger/VAOC</a>.
</p>
<p class="aidetexte">
J'ai commencé à travailler sur ce projet en 2007, la première partie a été jouée en 2012 sur une version 1. La version actuelle est utilisée depuis 2016. Cette durée ne veut cependant pas dire grand chose, comme tout projet
réalisé durant son temps libre, il a connu des gros phases d'arrêts, des reprises, etc.
</p>
</div>

<div class="aidesection">
<a onclick = "toggle('principes')" class="aide">
<img alt="section" class="aidesection" src="images/aidesection.png" />Principes généraux
</a>
</div>
<div id="principes" class="aidetexte">
<p class="aidetexte">
Dans VAOC, vous prenez le rôle d'un chef de corps à l'époque Napoléonnienne. Vous êtes donc à la tête de plusieurs divisions composées de fantassins et de cavaliers. Une partie se déroule dans le cadre
d'une campagne de quelques jours durant laquelle, avec d'autres joueurs, vous aller devoir essayer de remplir les objectifs qui vous sont assignés.
</p>
<p class="aidetexte">
Comme tout officier de cet époque vous allez transmettre vos ordres grâce à vos aides de camp qui se déplacent à cheval et cela prend du temps. Vous ne voyez que ce qui vous entoure et ne
savez que ce que vos sulbaternes, ou les autres officiers, ont bien voulu vous dire. A vous, à partir de ces informations parcellaires, de vous faire une idée de la situation générale et de
prendre les bonnes décisions pour mener votre camp à la victoire. 
</p>
<p class="aidetexte">
VAOC se déroule suivant une séquence de tours dont la durée est variable (voir tableau ci-dessous). Au début de chaque tour vous recevrez un courriel qui vous donnerez un état général de vos unités et le contenu des derniers messages
que vous avez reçus. Vous pouvez vous connecter sur le site à n'importe quel moment pour consulter l'histoire des échanges, envoyez des messages ou des ordres.<br/>
<table border="1" class="tableau">
    <tr>
        <td><b>Condition</b></td><td><b>Durée</b></td>
    </tr>
    <tr>
        <td>Distance entre les adversaires en mouvement > 30 kilomètres</td><td>8 heures</td>
    </tr>
    <tr>
        <td>Distance entre les adversaires en mouvement< 30 kilomètres</td><td>4 heures</td>
    </tr>
    <tr>
        <td>Distance entre les adversaires en mouvement < 15 kilomètres ou à l'arrêt < 5 kilomètres</td><td>2 heures</td>
    </tr>
    <tr>
        <td colspan="2">si une bataille vient de se terminer, que des renforts viennent d'arriver ou si le jour se lève, le tour reprend</td>        
    </tr>
    <tr>
        <td colspan="2">il n'y a jamais plus d'un tour d'arrêt durant la nuit</td>        
    </tr>
</table>
</p>
<p class="aidetexte">La radio n'ayant pas encore été inventé, tous les ordres, tous les messages, toutes les informations sont transportées par des hommes à cheval. La distance entre vous
et votre interlocuteur est donc primordiale pour agir et réagir dans les temps. Si vous vous trouvez à plus de deux heures de cheval de vos troupes, dites vous bien que vous
ne controlez plus vraiment vos hommes.</p>
<p class="aidetexte">
Les cartes de l'époque étaient souvent imprécises voir fausses, cependant, toutes unités en déplacement prendra toujours
le trajet le plus direct pour aller d'un point A vers un point B sans se soucier de la présence éventuelle d'unités ennemies.</p>
<p class="aidetexte">
Toute unité qui effectue un mouvement suit le même principe : officiers, messagers, patrouille, etc.</p>	
<p class="aidetexte"><b>La fatigue</b><br/>
Lorsque qu'une unité se déplace ou combat, elle se fatigue<br/>
    <table border="1" class="tableau">
    <tr><td><b>Heure de marche</b></td><td>0</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td>
        <td>11</td><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td><td>20</td></tr>
    <tr><td><b>Fatigue Infanterie</b></td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>1</td><td>1</td><td>2</td><td>2</td><td>3</td><td>4</td>
        <td>5</td><td>6</td><td>8</td><td>10</td><td>12</td><td>14</td><td>18</td><td>20</td><td>22</td><td>24</td></tr>
    <tr><td><b>Fatigue Cavalerie</b></td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>1</td><td>2</td><td>2</td><td>3</td><td>4</td><td>5</td>
        <td>6</td><td>8</td><td>10</td><td>12</td><td>14</td><td>18</td><td>20</td><td>22</td><td>24</td><td>26</td></tr>
    </table>
Ajouter un point de fatigue supplémentaire par heure de marche de nuit. Le nombre d'heures de marche est réduite de la valeur d'expérience de l'unité. Toute heure de combat ajoute deux points de fatigue.
    </p>
<p>
La fatigue varie entre 0 et 100, chaque point correspond à 1&#37; des effectifs qui sont restés en arrière, doivent réparer leur équipement, etc, et ne sont donc plus immédiatement opérationnels. Lorsque la fatigue devient très importante, cela influe sur le moral de votre unité d'après la table suivante :<br/>
</p>
<table border="1" class="tableau">
<tr><td><b>Fatigue</b></td><td>20</td><td>30</td><td>40</td><td>50</td><td>60</td><td>80</td></tr>
<tr><td><b>Moral perdu</b></td><td>1</td><td>1</td><td>2</td><td>3</td><td>5</td><td>15</td></tr>
</table>
	<p class="aidetexte">
Le seul moyen pour une unité de diminuer sa fatigue et/ou son moral est de se reposer en n'effectuant aucune action durant une journée (de minuit à minuit).<br/>
Une unité au repos regagne 10 points de moral. Tant que le moral de l'unité n'ai pas au moins égal à la moitié de son moral maximum, la fatigue ne diminue pas. Si c'est bien le cas, le nombre de trainards est égal à l'effectif de l'unité x % de fatigue x modificateur de météo. 1/10 des trainards disparaissent définitivement, 2/10 des forment un convoi de blessés, les 7/10 restants réintègrent l'unité au prorata du moral de l'unité. La fatigue restante est alors calculé au prorata du nombre de trainards repris.<br/>
Par exemple, une unité de 10000 hommes est fatiguée à 60%, elle a un moral de 20 sur un maximum de 30. Il pleut, le ralliement se fait à 50%. On a donc une base de récupération de 10000x0,6(% de fatigue) x0,5(météo) = 3000 trainards. 300 sont morts, 600 sont blessés, de 2100 restants, seuls 2100x20(moral)/30 (moral max)= 1400 réintègrent l'unité. Le nouvel effectif théorique de l'unité est donc de 10000 - 300 (morts) - 600 (blessés) = 9100 soldats. Le nouvel effectif réel est de 4000 + 1400 = 5400 soldats. La fatigue est donc maintenant de 100 - (5400/9100)*100 = 41%.<br/>
Une unité qui est à 100% de fatigue ne peut plus recevoir d'ordre, elle se repose. Une unité qui à 0 au moral ou en ravitaillement, fuit automatiquement à chaque rencontre avec une troupe ennemie.
	</p>
	<p class="aidetexte">
Les unités progressent en une ligne continue dont la longueur dépend des effectifs, des types d'unités et des fourgons de la nation.<br/>
- 1000 fantassins occupent 500m.<br/>
- 1000 cavaliers occupent 2000m.<br/>
- 1 canon occupe 50m.<br/>
Il faut ajouter la taille des fourgons : Russes : +75%, Prussiens en 1806/Autrichiens : +50%, Anglais : +30%, Prussiens après 1806 : +15%, Français : 0%, 
	</p>
	<p class="aidetexte">
	Les dépôts et le ravitaillement. Vos unités disposent, en début de campagne, de la nourriture et des munitions nécessaires pour la mener à bien. Si une unité est coupée de tous vos dépôts, il ne se produira rien de fâcheux dans l'immédiat. Si par contre l'une de vos troupes est mis en repos une journée sans avoir d'accès à un dépôt, celle-ci ne sera pas en mesure de regagner du moral ou de la fatigue. Un dépôt ne peut pas ravitailler une unité située à une distance supérieure à 150 kilomètres. La présence d'un dépôt à porter est vérifiée à minuit.</p>
	<p class="aidetexte">
La météo de la journée est réévaluée tous les jours à minuit. Les probabilités qu'un temps survienne est variable suivant chaque campagne. Les effets sur les mouvements sont détaillés par unité dans la page "campagne", les autres effets sont détaillés dans ce tableau.
<table border="1" class="tableau">
<tr><td ><b>Temps</b></td><td><b>Ravitaillement / Matériel</b></td><td><b>Fatigue</b></td><td><b>Ralliement</b></td>
<tr><td>Clair</td><td>100 %</td><td>x 1</td><td>100 %</td></tr>
<tr><td>Boue</td><td>0 %</td><td>x 2</td><td>0 %</td></tr>
<tr><td>Gelée</td><td>50 %</td><td>x 1,5</td><td>50 %</td></tr>
<tr><td>Neige</td><td>25 %</td><td>x 1,5</td><td>50 %</td></tr>
<tr><td>Pluie</td><td>50 %</td><td>x 1,5</td><td>50 %</td></tr>
<tr><td>Etouffant</td><td>50 %</td><td>x 1,5</td><td>50 %</td></tr>
</table>
	</p>
    <p class="aidetexte">
    <b>Matériel et ravitaillement</b><br/>
    Une unité consomme quotidiennement 10% de son ravitaillement (hors condition spécifique de la campagne).<br/>
    Une unité consomme 5% de son matériel par heure de combat.<br/>
    Une unité sans aucun matériel ou ravitaillement inapte au combat. Si elle engagée au combat (car aucune autre unité n'est disponible), elle subira des tirs sans riposter.
    Pour recevoir du matériel et du ravitaillement, une unité doit être au repos complet durant une journée. Le pourcentage de matériel ET de ravitaillement dépend alors de la taille du dépôt et
    de sa distance par rapport à l'unité suivant la table ci-dessous (voir également l’impact de la météo dans "Principes généraux"):
    <table border="1" class="tableau">
    <tr><td><b>Distance en km</b></td><td>0-10</td><td>11-25</td><td>26-50</td><td>51-100</td><td>100-150</td><td>Ravitaillement direct</td></tr>
    <tr><td><b>Type dépôt</b></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td><b>A</b></td><td>25 %</td><td>20 %</td><td>15 %</td><td>10 %</td><td>5 %</td><td>50.000 soldats</td></tr>
    <tr><td><b>B</b></td><td>15 %</td><td>10 %</td><td> 7 %</td><td> 5 %</td><td>3 %</td><td>40.000 soldats</td></tr>
    <tr><td><b>C</b></td><td>10 %</td><td> 7 %</td><td> 5 %</td><td> 3 %</td><td>1 %</td><td>30.000 soldats</td></tr>
    <tr><td><b>D</b></td><td> 5 %</td><td> 3 %</td><td> 1 %</td><td> 0 %</td><td>0 %</td><td>20.000 soldats</td></tr>
    </table>
    </p>
    <p class="aidetexte">La chaine logistique<br/>
    En tant que chef de corps vous devez vous assurez de faire reposer vos unités, qu'elles se placent non loin d'un dépôt central ou puisse aller chercher de quoi se ravitailler par un ravitaillement direct (voir le détail des ordres dans "Ecran du Quartier Général").<br/>
    En tant que chef d'armée, vous contrôlez les dépôts et vous devez assurer la chaine logistique globale, les convois se déplacant de 24 km/jour (1km/h), une bonne stratégie ne fonctionnera que si vous
    disposez d'une bonne logistique et ne prenez pas ce rôle à la légère ou votre armée aura de forts malus en combat voir même, se révélera incapable de combattre.<br/>
    Les dépôts "A" sont le coeur de cette stratégie, eux seuls peuvent produire du ravitaillement sous forme d'un convoi par jour.<br/>
    A partir de ces dépôts vitaux, deux options s'offrent à vous : soit reconstituer des dépôts de type A (en cumulant 4 convois) pour étendre la chaine logistique globale et couvrir
    un maximum de terrain mais avec un taux d'approvisionnement des unités lents, soit constituer des dépôts déstinés à être "pillés" par les 
    unités par le "ravitaillement direct", seule cette méthode permet de poursuivre de façon continue et rapide une offensive. Après quelques jours
    de campagne où les unités auront épuisées leur stock, la position de vos dépôts sera cruciale.
    </p>
</div>

<div class="aidesection"><a onclick = "toggle('frequence')" class="aide"><img alt="section" class="aidesection" src="images/aidesection.png" />Fréquence des mises à jour - Vacances - Bugs</a></div>
<div id="frequence" class="aidetexte">
<p class="aidetexte">
Ayant personnelement fait de nombreuses parties par courriels/forums, j'ai toujours trouvé que plus le rythme des mises à jour était soutenu, meilleur c'est. Initiallement, je vais passer
beaucoup de temps à vérifier que tout se passe "sans bug", il y aura donc une mise à jour par semaine, mais dés que je constaterai qu'il n'y a pas de problèmes particuliers, je
passerai à 2-4 mises à jours par semaine. A chaque fois, qu'un tour est en cours d'arbitrage, il ne vous sera plus possible de donner des ordres et vous verrez s'afficher "Un grognard passe actuellement le balai dans votre tente".
Vous recevrez un courriel automatiquement à chaque fois que le jeu sera à nouveau ouvert.
</p>
<p class="aidetexte">
Concernant vos congés, merci de me prévenir par un courriel privé de vos abscences, je vous informerai également de mes propres abscences. Je ferai de mon mieux pour en tenir compte mais je n'ai cependant pas l'intention 
d'être très souple sur les congés de tout à chacun.
D'une part, vous pouvez tout à fait donner vos ordres par smartphone ou tablette (l'interface est spécialement prévue pour ce type d'appareil), d'autre part, sachant qu'il y a plus de dix joueurs sur une partie, si celui-ci doit faire une pause à chaque fois que quelqu'un est
absent, il est évident que le jeu n'évoluera jamais. Enfin quelles que soit les conditions, sachez que vous pouvez toujours "rater" un tour puisque les actions de combat ont lieu toutes les deux heures, pour les
mouvements, les cas, historiques, où un chef assoupi n'a pas envoyé ses ordres aussi vite qu'il l'aurait dû ne manquent pas.
</p>
<p class="aidetexte">
Il exite un autre cas de suspension du jeu : la découverte d'un bug sur l'application. Malgré tout le soin accordé à cette réalisation et les nombreux tests que j'ai effectué, il
est certain qu'il y aura des bugs. N'hésitez donc surtout pas à m'écrire si vous remarquez un comportement qui vous semble suspect. J'y préterai toujours la plus grande attention.
Aucune campagne n'a jamais été définitement arrêtée à cause d'un bug. Le pire problème rencontré m'a
contraint à suspendre le jeu durant deux semaines, je suis donc très confiant sur le fait que toute partie sera menée à son terme, quelque soit les erreurs "d'arbitrage" qui pourraient
survenir.
</p>
</div>

<div class="aidesection"><a onclick = "toggle('connexion')" class="aide"><img alt="section" class="aidesection" src="images/aidesection.png" />Camp de rassemblement</a></div>
<div id="connexion" class="aidetexte">
<p class="aidetexte">
C'est à partir de cet écran que vous allez saisir votre identificant unique de joueur et le mot de passe que vous avez choisit. Vous pouvez avoir le même identifiant de joueur pour
joueur plusieurs rôles/officiers au sein d'une même partie ou de plusieurs parties. 
Si vous perdez votre mot de passe, vous pouvez le redemander en envoyant un courriel à l'administrateur (cliquer sur "écrire" tout en haut de cet écran).
</p>
<table>
	<tr>
	<td style="width:350px">
		<div><div class="aidenumero">1.</div> Saissez dans cette zone votre identifiant unique.</div>
	</td>
	<td rowspan="3"><img alt="ecran de connection" src="images/aideecrandeconnexion1.png"  width="400px"/></td>
	<td><div><div class="aidenumero">3.</div> Cliquez sur "connexion" pour vous authentifier.</div></td>
	</tr>
	<tr>
	<td>
		<div><div class="aidenumero">2.</div> Saissez dans cette zone votre mot de passe.</div>		
	</td>	
	<td>
		<div><div class="aidenumero">4.</div> Liste des parties en cours, dernières dates de mise à jour, en temps de jeu et en temps "réel".</div>
	</td>
	</tr>
	<tr>
	<td></td>
	<td>
		<div><div class="aidenumero">5.</div> Lorsque qu'une partie est terminée, il est possible, en cliquant sur son nom, d'aller consulter le compte-rendu, même sans s'authentifier.</div>
	</td>
	</tr>
	<tr>
</tr></table>
<p class="aidetexte">
Une fois authentifié, vous pouvez accéder à votre quartier général ou modifier vos renseignements personnels.
</p>
<table><tr>
	<td style="width:350px">
		<div><div class="aidenumero">1. </div>Cliquez sur "Deconnexion" si vous souhaitez vous connecter sous un autre nom.</div>
	</td>
	<td rowspan="2"><img alt="ecran de connection" src="images/aideecrandeconnexion2.png" width="400px"/></td>
	<td>
		<div><div class="aidenumero">3. </div>Cliquez sur "Accès au QG" pour vous rendre sur la page de votre quartier général.</div>
	</td>
	</tr>
	<tr>
	<td>
		<div><div class="aidenumero">2. </div>Cliquez sur "Modifier le compte" si vous souhaitez changer vos renseignements personnels.</div>
	</td>
</tr></table>
</div>

<div class="aidesection"><a onclick = "toggle('compte')" class="aide"><img alt="section" class="aidesection" src="images/aidesection.png" />Ecran des informations personnelles</a></div>
<div id="compte" class="aidetexte">
<p class="aidetexte">
Cet écran vous permez de modifier les rares informations personnelles qui vous sont demandées pour pouvoir jouer à VAOC. Ces informations vous sont demandées pour éviter
que quelqu'un puisse usurper votre compte et jouer à votre place. 
</p>
<table><tr>
	<td><div><div class="aidenumero">1.</div> Rappel de votre nom d'utilisateur, non modifiable.</div>
		<div><div class="aidenumero">2.</div> Saisisez votre nouveau mot de passe (ou l'ancien si vous ne souhaitez pas le modifier).</div>
		<div><div class="aidenumero">3.</div> Resaisisez votre mot de passe.</div>
		<div><div class="aidenumero">4.</div> Adresse de messagerie (courriel). C'est sur cette adresse que vous seront envoyés tous les messages relatifs au jeu.</div>
		<div><div class="aidenumero">5.</div> Nom.</div>
		<div><div class="aidenumero">6.</div> Votre Prénom.</div>
		<div><div class="aidenumero">7.</div> Une question dont vous êtes le seul à connaitre la réponse.</div>
		<div><div class="aidenumero">8.</div> La réponse à la question précédente.</div>
	</td>
	<td><img alt="ecran de connection" src="images/aideecrandeconfiguration.png" /></td>
	<td>
		<div><div class="aidenumero">9.</div> Cliquez sur "Mise à jour" si vous souhaitez sauvegarder les informations que vous avez saisies.</div>
		<div><div class="aidenumero">10.</div> Cliquez sur "Quitter" pour revenir à l'écran de connexion. Cette action ne sauvegarde pas ce que vous avez saisie, n'oubliez donc pas de cliquer sur "mise à jour" avant si vous souhaitez conserver vos modifications.</div>
	</td>
</tr></table>
</div>

<div class="aidesection"><a onclick = "toggle('qg')" class="aide"><img alt="section" class="aidesection" src="images/aidesection.png" />Ecran du Quartier Général</a></div>
<div id="qg" class="aidetexte">
<p class="aidetexte">
Cet écran est le plus dense et le plus important puisqu'il vous donne une vue complète de vos unités et des ordres donnés et reçus. 
</p>
    <table><tr>
	<td  style="width:350px">
	<div><div class="aidenumero">1.</div> En cliquant sur le bandeau ou le bouton "campagne", vous avez accès à un écran qui vous donne toutes les informations
	que vous devez savoir sur la situation générale, votre armée et les objectifs de cette campagne.</div>
	</td>
	<td><img alt="ecran de connection" src="images/aideecranqg0.png" width="400px"/></td>
	<td>
	<div><div class="aidenumero">2.</div> Date courante et date de fin de campagne. La campagne s'arrête soit à cette date soit si l'un des deux camps a vu la moitié de ses divisions fuir ou combat ou être détruite
	et qu'il a deux fois plus d'unités dans cette situation que son adversaire.<br/> Exemple : le camp A dipose de 10 unités, le camp B de 17. Si le camp a 5 unités ou plus ayant fuits au combat ou détruites et que
	le camp B a moins de 3 unité dans la même situation, la campagne s'arrête.<br/>
        Cette zone indique également la météo du jour. La météo de la journée est réévaluée tous les jours à minuit.<br/>
        <b>Merci de cocher la case "J'ai terminé de donner mes ordres" quand c'est le cas, cela peut permettre d'accélerer sensiblement la partie pour le bien de tous.</b>
        </div>
	</td>
</tr></table>
<hr style="display: block; margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto;border-style: inset;border-width: 3px;">
<table><tr>
	<td  style="width:350px">
	<div><div class="aidenumero">1.</div> La première section détaille vos caractéristiques personnelles.<br/> 
	Nom: l'officier supérieur que vous representez, si vous jouez plusieurs officiers de ce rang dans une ou plusieurs parties, une liste déroulante vous permet de passer de l'un à l'autre.<br/>
	Rang: plus votre lettre est petite, plus haut est votre grade. Dans cette bataille c'est l'officier le plus élevé qui commande. Un officier de rang A commandera donc, en bataille, les troupes d'un commandant de rang B ou C.<br/>
	Tactique: en bataille, si vous vous engagez personnelement au combat, ce bonus sera ajouté aux unités.<br/>
	Stratégique: le nombre de divisions que vous pouvez commander sans malus au combat.<br/>
	Position : votre position géographique actuelle.</div>
	<div><div class="aidenumero">2.</div> Liste des ordres données à votre officier. Il est possible de supprimer l'orde donné à ce tour en cliquant sur le bouton "supprimer" (poubelle).</div>
	<div><div class="aidenumero">3.</div> C'est ici que vous pouvez donner les ordres de mouvement de votre officier. Vous devez donner sa destination géographique et vous en prendrez 
        immédiatement la direction. Comme vous êtes un officier de renom et donc infatiguable vous pouvez
        galoper du soir au matin sans conséquence pour vous, il n'en est pas de même pour vos hommes (voir ci-dessous, la section des unité).<br/>
        Vous ne pouvez pas être capturés par les troupes ennemies même en vous promenant au milieu du GQG adverse. Cependant, pour éviter une "triche", 
        sachez que si vous vous retrouvez dans la zone de vision d'une troupe combattante ennemie sans être vous-même dans la zone de vision d'une unité combattante ennemie, vous ne pourrez plus
        donner AUCUN ordre sauf un ordre de mouvement pour vous permettre de vous sortir de ce mauvais pas.</div>
	</td>
	<td><img alt="ecran de connection" src="images/aideecranqg1.png" width="400px"/></td>
	<td>
	<div><div class="aidenumero">3 bis.</div> Si vous êtes le commandant en chef de l'armée, vous pouvez également transférer des unités d'un commandant à l'autre. Les commandants à qui l'on 
        attribue ou retire une unité n'ont aucune recours contre cette décision. Vous êtes le chef après tout !</div>
	<div><div class="aidenumero">4.</div> Carte de situation. Cette carte représente une zone de 20kmx20km. La zone la plus claire représente ce que vous voyez effectivement
	c'est à dire 5kmx5km de nuit et 10kmx10km de jour. Le reste de la carte est grisée n'est là que pour vous aider à vous situer.<br/>
	Si vous cliquez sur la carte, vous êtes dirigés vers la salle des cartes qui vous permet d'avoir une vision géographique globale de la campagne (voir détails ci-dessous)<br/>
	Sous la carte se trouvent trois icônes qui vous permettent d'afficher trois visions différentes de la carte.<br/>
	<img alt="icone historique" src="images/historique.PNG" /> C'est la vue par défaut, la carte historique d'état major réalisée par vos services cartographiques.<br/>
	<img alt="icone topographique" src="images/topographique.PNG" /> Dans cette vue, la zone claire (ce que vous voyez vraiment) affiche la géographie réelle du terrain qui vous entoure 
	(suivant un codage basique blanc=plaine, vert=forêt, marron=colline, bleu=eau). En effet, malgré tous leurs efforts il arrive que votre carte d'état-major ne représente pas tout
	à fait la réalité.<br/>
	<img alt="icone zoom" src="images/zoom.PNG" /> C'est vue affiche uniquement ce que vous voyez de façon grossie de manière à en avoir un meileur aperçu.<br/>
	<img alt="icone zoom" src="images/filmrole.PNG" /> C'est vue affiche une "vidéo" de ce que votre unité à vue depuis la précédente mise à jour.<br/>        
	</div>
	<div><div class="aidenumero">5.</div> Description de toutes les unités à votre portée de vue. Cette information est plus détaillée pour les unités de votre camp que pour celles de l'ennemi.</div>
	<div><div class="aidenumero">6.</div> Si vous êtes engagé dans une bataille, la carte de la bataille s'affichera dans cette zone, il vous suffit de cliquer dessus pour aller à l'écran des batailles et donner les ordres aux unités engagées.</div>
	</td>
</tr></table>
<hr style="display: block; margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto;border-style: inset;border-width: 3px;">
<table><tr>
	<td style="width:350px">
	<div><div class="aidenumero">1.</div> Une zone de papier blanc dans laquelle vous pouvez saisir le message à envoyer.</div>
	</td>
	<td><img alt="ecran de connection" src="images/aideecranqg2.png" width="400px"/></td>
	<td>
	<div><div class="aidenumero">2.</div> Il faut cliquer sur le bouton "envoyer le message" pour transmettre votre missive. A ce moment là, un aide de camp arrive, prend votre message et l'apporte à l'endroit que vous avez
            indiqué. Une fois arrivé sur place, il cherche le destinataire, et lui apporte votre message.<br/>
            Exception : Lorsque vous êtes présent à moins d’un kilomètre d’un correspondant, vous verrez 
            le mot « direct » s’affiche à côté de son nom. Dans ce cas, il est inutile de faire partir 
            un messager à cheval, il suffit d’élever un peu la voix. Tout message envoyé est donc directement 
            visible sur l’interface du destinataire et un courriel lui ai transmis pour l’en informer.
        </div>
	</td>
</tr></table>
<p class="aidetexte">
Votre aide de camp se déplace à cheval de jour comme de nuit. Même ainsi, il faut du temps pour envoyer un ordre ou un message lorsque l'on
est éloignés. Il arrivera toujours à destination, même s'il traverse des lignes
ennemis mais, dans ce cas, il est possible que votre message soit capturé et transmis à l'ennemi. Dans la réalité, ce n'était pas un, mais plusieurs cavaliers qui transportaient le même message, il était donc presque impossible
qu'ils soient tous interceptés.
</p>
    <hr style="display: block; margin-top: 0.5em; margin-bottom: 0.5em; margin-left: auto; margin-right: auto;border-style: inset;border-width: 3px;" />
<table><tr>
	<td style="width:350px">
	<div><div class="aidenumero">1.</div> La description d'une unité. Pour toutes les valeurs, Le nombre avant la barre de fraction indique la valeur courante, le nombre après, la valeur initiale ou maximale.<br/>
	Le nom de l'unité. <br/>
	Les effectifs de fantassins <img alt="infanterie" src="images/infanterie-26.png" style="background-color:black;" />, 
        de cavaliers <img alt="cavalerie" src="images/cavalerie-26.png" style="background-color:black;"/> et de canons <img alt="artillerie" src="images/artillerie-26.png" style="background-color:black;"/>. 
        La diminution des effectifs peut être liée soit au combat soit à la fatigue (voir ci-dessous).<br/> 
	Moral <img alt="moral" src="images/moral-26.png" style="background-color:black;"/>: Celui-ci diminue soit au combat soit suite à une très grande fatigue. Quand il tombe à zéro l'unité est en déroute. Une unité regagne du moral en étant au repos complet durant une journée.<br/>	
	Fatigue <img alt="fatigue" src="images/fatigue-26.png" style="background-color:black;"/>: Une unité qui se déplace ou combat se fatigue. Un point de fatigue correspond à la "perte" d'un pourcent de ses effectifs, en fait des trainards qui n'arrivent pas à suivre la cadence imposée. Ces effectifs
	rejoignent la troupe dés que celle-ci est reposée.<br/>
	Expérience <img alt="experience" src="images/experience-26.png" style="background-color:black;"/>: une valeur positive indique un bonus lors des combats (et réduit également légèrement votre fatigue).<br/>
	Matériel <img alt="materiel" src="images/materiel-26.png" style="background-color:black;"/>: Pourcentage représentant l'équipement, les armes, les munitions, etc. 5% sont perdus par heure de combat. Cela influence les combats (voir Ecran de Bataille). Les dépôts fournissent du matériel.<br/>
	Ravitaillement <img alt="ravitaillement" src="images/ravitaillement-26.png" style="background-color:black;"/>: Pourcentage indiquant le stock alimentaire d'une unité. 10% sont perdus tous les jours. Cela influence les combats (voir Ecran de Bataille). Les dépôts fournissent du ravitaillement. 
        Une unité sans moral et sans ravitaillement se rend immédiatement au contact de l'ennemi.
	</div>
        <div><div class="aidenumero">2.</div> La carte de position d'une unité. Il ne s'agit pas de la position en temps réel, mais de la carte que vous a transmis l'unité avec son dernier message.</div>
        <div><div class="aidenumero">3.</div> Date du Dernier message : A chaque fois que vous recevez un message de votre unité, celle-ci vous transmet ses effectifs, sa fatigue et son moral en plus de sa position et d'une carte associée. Toutes les informations
        que vous voyez sur votre unité sont les valeurs de l'unité au moment où elle a envoyé son message et non celles réelles à l'instant présent.<br/>
	Position : emplacement géographique.
        </div>
	</td>
	<td><img alt="ecran d'une unite" src="images/aideecranqg3.png" width="400px"/></td>
	<td>
	<div><div class="aidenumero">4.</div> Les ordres que vous avez envoyés à l'unité. Les ordres donnés durant l'heure peuvent être supprimés, les autres non. 
        Si vous donnez deux ordres dans le même tour, les ordres vont s'executer les uns à la suite des autres. Vous pouvez ainsi
	ordonner à une unité d'aller au point B en passant par A puis de détuire un pont sur place.</div>
	<div><div class="aidenumero">5.</div> Envoie d'un ordre de mouvement à une unité. Vous devez indiquer sa destination, l'heure de départ et le nombre d'heures de marche maximum par jour. Plus une unité marche, plus elle
	est fatiguée. L'ordre est transmis par un aide de camp, plus l'unité est éloignée de vous, plus il faudra de temps pour qu'elle reçoive votre ordre.<br/>
        <b>Exemple</b> : Il est 14h00 et vous donner l’ordre à une unité de marcher vers Berlin à 11h00 durant 6 heures. Un messaer part et l’unité reçoit le message à 14h35. Tout ordre précédemment en cours est annulé et l’unité va commencer immédiatement à aller vers Berlin de 14h35 à 17h00. A 17h00 l’unité arrête son mouvement (elle aura donc marchée durant 2h25 le premier jour), le lendemain, elle reprendra son mouvement à partir de 11h00 et jusqu’à 17h00, puis tous les jours suivants tant qu’elle n’aura pas atteint Berlin ou reçu un autre ordre.
	</div>
	<div><div class="aidenumero">6.</div> Cette section permet d'envoyer une patrouille en reconnaissance sur un lieu précis (une unité dispose d'une patrouille pour 1000 cavaliers). Une patrouille se déplace de jour comme de nuit, dés qu'elle a atteint la destination que vous lui avez
	indiquée ou qu'elle rencontre un ennemi, elle rentre immédiatement et fait un rapport où elle indique tout ce qu'elle a vu au moment où la patrouille s'est arrêtée. Le rapport est toujours transmis au chef de division qui, ensuite, vous le renverra.</div>
	<div><div class="aidenumero">7.</div> Certaines unités peuvent également réaliser des ordres particuliers (voir ci-dessous).<br/>
</div>
  </td>
</tr>
</table>
  <p class="aidetexte">
    <b>Ordres Particuliers</b>
      <table border="1" cellpadding="5px">
        <tr>
          <td>
            <b>Ordre</b>
          </td>
          <td>Qui</td>
          <td>Description</td>
        </tr>
        <tr>
          <td>
            <b>Endommager un pont</b>
          </td>
          <td>Unité combattante, Pontonniers</td>
          <td>
            Endommage le tablier du pont, retardant d'une heure
            (2 heures pour un fleuve large) la traversée de celui-ci. Une unité à qui l'on donne l'ordre de détruire un pont, cherche le pont le plus proche à 5 kilomètres alentour et envoie un détachement pour l'endommager.
            Il faut 2 heures pour endommager 100m de pont, ce temps est doublé de nuit. Si, durant cette opération, une unité ennemie se trouve à moins de 3 kilomètres du pont, la destruction du
            pont est suspendue.
          </td>
        </tr>
        <tr>
          <td>
            <b>Se fortifier</b>
          </td>
          <td>Unité combattante</td>
          <td>
            Une unité qui ne bouge pas peut construire des fortifications de campagne. Il faut 24 heure pour construire des fortifications, qui peuvent être doublées. Chaque niveau
            de fortification donne un bonus de +1 dé en combat. Les fortifications crées sont irrémédiablement détruites dès que l’unité quitte sa position et ne peuvent donc jamais être réutilisées. Construire des fortications ne fatigue pas une unité, 
            cependant, ce n'est pas une position de repos et elle ne permet pas non plus de regagner de la fatigue et/ou du moral.
          </td>
        </tr>
        <tr>
          <td>
            <b>Ravitaillement direct</b>
          </td>
          <td>Unité combattante</td>
          <td>
            Une unité qui ne fait aucune action durant une journée (de minuit à minuit) et a reçu un ordre de ravitaillement direct durant cette période prélève
            son ravitaillement directement sur un dépôt se trouvant à moins de 3 kilomètres de sa position (si plusieurs dépôts sont elligibles, celui de moindre niveau sera choisi). 
            Le ravitaillement et le matériel de l'unité sont alors remis à 100% de leur capacité.<br/>
            Le nombre de "points de ravitaillement direct" pris sur le dépôt est égal à 100% de prélévement sur le matériel ou le ravitaillement.<br/>
            <u>Exemple</u>, une unité de 500 soldats se repose à côté d’un dépôt avec 56% de ravitaillement et 67% de matériel, elle retire 500*(1-0,56 + 1-0,67) = 385 points de soldats ravitaillés.<br/>
            Le nombre de points d'un dépôt depend de son niveau (voir tableau ci-dessous). Quand le nombre de soldats ravitaillés est atteint, le dépôt perd un niveau ou est détruit s'il est de niveau D.
            Un dépôt de niveau A n'est pas réduit mais il ne pourrai plus effectuer aucun ravitaillement direct durant une semaine.
          </td>
        </tr>
        <tr>
          <td>
            <b>Construire un pont</b>
          </td>
          <td>Pontonniers</td>
          <td>
            Les pontonniers peuvent construire un ponton de bateau sur l'emplacement d'un gué.
            La durée de construction est de 6 heures pour 100m de ponton. La présence d'une unité ennemie à moins de 3kilomètres suspend les travaux.
            Une unité ennemie qui arrive sur un ponton le détruit immédiatement.
          </td>
        </tr>
        <tr>
          <td>
            <b>Réparer un pont</b>
          </td>
          <td>Pontonniers</td>
          <td>Les pontonniers peuvent réparer un pont endommagé. Il faut 4 heures pour réparer 100m de ponton. La présence d'une unité ennemie  à moins de 3kilomètres suspend les travaux.</td>
        </tr>
        <tr>
          <td>
            <b>Renforcer</b>
          </td>
          <td>Convoi, renforts</td>
          <td>
            Un convoi de ravitaillement, à l'emplacement d'un dépôt existant peut le renforcer. Dans ce cas, le dépôt ciblé progresse d'un niveau ('D' devient 'C', etc.).<br/>
            Un dépôt de type « A » nécessite des bâtiments importants qui ne sont disponibles que dans de grandes villes dont la liste est fournie pour chaque campagne.
            Un dépôt de type "A" ne peut pas constitué en dehors de ces villes.<br/>
            Un "convoi" de renforts, provenant soit de renforts de campagne, soit de blessés soignés dans un hôpital peuvent également venir intégrér une unité située au même endroit qu'elle.
            Le niveau d'expérience, fatigue, moral, ravitaillement et équipement est calculée au prorata des deux unités qui fusionnent.
          </td>
        </tr>
        <tr>
          <td>
            <b>Générer un convoi</b>
          </td>
          <td>Dépôt</td>
          <td>
            Un dépôt peut génère un convoi toutes les 24 heures. Attention, si ce dépôt n'est pas de type A,celui-ci perd un niveau dans l'opération.
            Un dépôt de type D qui génère un convoi se transforme lui-même en convoi.
          </td>
        </tr>
        <tr>
          <td>
            <b>Etablir un dépôt</b>
          </td>
          <td>Convoi</td>
          <td>
            Un convoi de ravitaillement devient un dépôt de type D à l'emplacement où l'ordre est executé.
            Un convoi ne ravitaille pas d'unités tant qu'il ne s'est pas établi en dépôt.
          </td>
        </tr>
        <tr>
            <td>
                <b>Ligne de ravitaillement</b>
            </td>
            <td>Dépôt A</td>
            <td>
                Toutes les 24 heures, un convoi est crée et envoyé à la destination indiquée.
            </td>
        </tr>
        <tr>
            <td>
                <b>Réduire le dépôt</b>
            </td>
            <td>Dépôt A</td>
            <td>
                Le dépôt est réduit au niveau B et un convoir est généré permettant, par la suite, de réduire à nouveau le dépôt en générant des convois. Cela peut permettre, au final, de déplacer un dépôt A ou de le détruire pour que l'ennemi ne puisse s'en emparer.
            </td>
        </tr>
      </table>
    </p>
    <p class="aidetexte">
      <b>Ordres successifs</b><br/>
      Certains ordres peuvent être transmis successivement en une seule fois à une unité. Il est par exemple possible de donner un ordre de mouvement suivi d'un ordre de déstruction de pont. Attention
      cependant, le deuxième ordre ne sera executé qu'une fois le premier totalement terminé. Par exemple, "Aller de A à B à partir de 10h00 durant 2 heures puis détruire un pont". Ne veut pas dire que l'unité
      se déplacera durant deux heures puis détruire le pont le plus proche mais qu'elle se déplacera de 10h00 à 12h00 tous les jours jusqu'à atteindre le point B, puis détuira le pont présent.
    </p>
<table><tr>
	<td style="width:350px">
	<div><div class="aidenumero">1.</div> La liste de tous les messages que vous avez reçus.</div>
	</td>
	<td><img alt="ecran de connection" src="images/aideecranqg4.png" /></td>
	<td>
	<div><div class="aidenumero">2.</div> Vous pouvez cliquer sur les entêtes de colonne pour trier les messages.</div>
	<div><div class="aidenumero">3.</div> Vous pouvez choisir le nombre de message à afficher par page et afficher la page de message que vous souhaitez voir.</div>
	</td>
</tr></table>
</div>

<div class="aidesection">
<a onclick = "toggle('cartes')" class="aide"><img alt="section" class="aidesection" src="images/aidesection.png" />Salle des cartes</a>
</div>
<div id="cartes" class="aidetexte">
<p class="aidetexte">
Dans la salle des cartes vous pouvez consulter la carte complète de la campagne et rechercher les noms des villes et lieux dit qui n'y sont pas forcément inscrits. 
</p>
<table><tr>
	<td><div><div class="aidenumero">1.</div> liste des lieux nommés de la carte.</div>
		<div><div class="aidenumero">2.</div> Cliquez sur "Rechercher" pour vous positioner à l'emplacement de ce lieu.</div>
		<div><div class="aidenumero">3.</div> Les flèches permettent de se déplacer sur la carte.</div>
	</td>
	<td><img alt="ecran de connection" src="images/aideecrandescartes.png" /></td>
	<td>
		<div><div class="aidenumero">4.</div> En déplaçant la souris au-dessus de la carte, vous verrez les noms des lieux-dits sur lesquels vous êtes s'afficher.</div>
		<div><div class="aidenumero">5.</div> En cliquant sur la carte, vous pourrez accéder à une carte avec une échelle inférieure. En cliquant à nouveau sur la carte
		vous revenez à l'échelle d'origine.</div>
	</td>
</tr></table>
</div>

<div class="aidesection"><a onclick = "toggle('bataille')" class="aide"><img alt="section" class="aidesection" src="images/aidesection.png" />Ecran de bataille</a></div>
<div id="bataille" class="aidetexte">
<p class="aidetexte">
Une bataille se déclenche dés que deux unités combattantes se recontrent de jour (pas de bataille la nuit). Une bataille se termine soit à la tombée de la nuit soit lorsque
l'un des deux camps a quitté le champ de bataille à cause de la fuite de ses unités ou par ce qu'il a ordonné une retraite.<br/>
Au moment du premier contact, un champ de bataille simplifié est crée suivant la position des unités des adversaires. Le champ de bataille est un carré
de 6 kilomètres de coté dont le centre est le premier point de contact entre deux unités ennemi. Il comprend un centre et deux ailes pour
chaque camp, chaque zone pouvant être séparée par un obstacle naturel (fleuve, etc.). Un champ de bataille est soit horizontal, soit vertical dans sa présentation.<br/>
C'est d'ici que vous pouvez observer une bataille en cours, les forces disponibles de part et d'autre, et, si vous êtes l'officier le plus titré présent, donner les ordres. 
Si vous n'êtes pas l'officier le plus titré (lettre de votre rang "A" étant le meilleur), vous ne pouvez qu'observer ce qui se passe mais ne pourrez donner aucun ordre, même à vos unités.<br/>
Si aucun officier n'est présent, une unité se positionnera d'elle même au centre pour affronter l'ennemi.<br/>
<span style="text-decoration:underline;">TRES IMPORTANT</span> : Toute unité qui se retrouve dans la zone de 6 km du champ de bataille est immédiatement intégrée à celui-ci, de plus
toute unité qui entre en contact avec une unité engagée au combat devient également partie prenante dans la bataille, ceci pour éviter que des unités ne parviennent pas à rejoindre un combat
car leur mouvement est entravé par d'autres troupes.<br />
<span style="text-decoration:underline;">TRES IMPORTANT (bis)</span> : Toute unité qui se retrouve sur un champ de bataille, qu'elle soit engagée ou pas, détruit son ordre courant et ne pourra en recevoir d'autre
tant que la bataille n'est pas terminée. Un officier doit donc veiller à lui redonner un ordre dés une bataille terminée s'il veut que l'unité agisse à nouveau.<br />
<span style="text-decoration:underline;">Note</span> : Dans la règle officielle, le leader de plus haut rang hiérarchique dirige la bataille et ne peut pas s'impliquer tactiquement.Dans VAOC, cela pose deux problèmes :<br/> 
1) Napoléon ne pourra jamais s'engager, son bonus tactique ne pourra jamais être utilisé.<br/>
2) s'il y a deux chefs de même niveau, les joueurs doivent normalement décider entre eux qui dirige le combat et qui peut être impliqué tactiquement et il serait complexe de 
mettre en place ce "mini" forum.<br/>
Dans VAOC, le premier joueur arrivé dirige le combat mais tous les leaders présents peuvent être engagés tactiquement au choix de celui qui dirige le combat.<br/>
Une passe d'armes a lieu toutes les deux heures, passe durant laquelle les unités font feu, subissent des pertes, fuient, etc.<br/>
Durant une bataille, toute unité qui rentre dans le champ de bataille est immédiatement mis en réserve, ses ordres sont annulées et elles ne peut reprendre son mouvement tant que la
bataille n'est pas terminée.<br/>
Un "échange de tirs" dans une bataille a lieu toutes les deux heures. Le centre et les ailes combattent les unes contre les autres, en lançant des dés qui font
perdre du moral aux troupes ennemis, quand une troupe voit son moral réduit à zéro, elle fuit le champ de bataille (unitée indiquée en pointillée). Toute unité engagée subit
des pertes au combat égales à un pourcentage de ses effectifs valant la moitié de ses pertes en moral ou suite à la poursuite de la cavalerie de l'adversaire si son propre camp n'est pas victorieux.
</p>
<table><tr>
	<td>
		<div><div class="aidenumero">1.</div> Situation réelle sur le terrain, le terrain de bataille étant une vue simplifiée de ce terrain réel.</div>
		<div><div class="aidenumero">2.</div> Bataille modélisée, si le terrain d'une unité lui confère un bonus (exprimé en d6), vous pouvez le voir en y plaçant la souris.<br/>
pour les obstacles éventuels entre les deux fronts, le bonus que confère cet avantage s'applique aux deux adversaires.</div>
		<div><div class="aidenumero">3.</div> Nom du commandant en chef, lui seul peut donner des ordres aux unites présentes, y compris aux autres chefs au combat.<br/>
	Il peut ordonner une retraite générale (une unité ne peut faire retraite individuellement) au quel cas, toutes les unités fuient le champ de bataille.</div>
	</td>
	<td><img alt="ecran de bataille" src="images/aideecrandebataille.png" /></td>
	<td>
		<div><div class="aidenumero">4.</div> Listes des unités en réserve, le commandant en chef peut leur ordonner de s'engager au centre ou sur l'une des ailes.<br/>
		On ne peut engager une unité sur une aile que s'il y a au moins une unité au centre.</div>
		<div><div class="aidenumero">5.</div> Unités présentes sur le terrain. En déplaçant la souris sur le terrain son nom s'affiche ainsi que la valeur d'un éventuel
		bonus au combat pour les unités présentes. Si une unité est encadrée d'un trait épais, celle-ci est en défense ce qui lui confére également un bonus. Pour être en
		défense, l'unité devait être à l'arrêt au moment où la bataille s'est déclenchée.</div> 
		<div><div class="aidenumero">6.</div> Zone adverse, vous pouvez y voir quelques informations limitées sur les forces présentes de l'ennemi. 
                    Les unités encadrées en rouge sont en défense. Les unités encadrées en gris sont fortifiées.</div>
		<div><div class="aidenumero">7.</div> Niveau d'engagement sur la zone. Le niveau d'engagement final est le niveau le plus élevé choisit par l'un des deux adversaires.
    Le niveau d'engagement définit le nombre de dés de base lancés (de 1 à 3).</div>
	</td>
</tr></table>
<p class="aidetexte">
Détails sur la résolution d'un combat<br/>
Pour ceux qui aiment bien comprendre les mécanismes du jeu, je détaille dans ce paragraphe toutes les phases d'un combat.<br/>
1) On détermine, pour chaque camp, quel est l'officier de plus haut rang présent pour qu'il dirige le combat. En cas d'égalité, c'est le premier arrivé qui dirige, si plusieurs sont arrivés durant la même heure, l'officier
est tiré au hasard.<br/>
2) Si aucune des unités présentes n'a été engagée par un joueur (ou qu'il n'y a pas de joueurs), une unité est choisie au hasard pour être engagé au centre.<br/>
3) Calcul du modificateur stratégique, il vaut 0 si le nombre d'unités engagés en bataille est inférieur ou égal au niveau stratégique de l'officier qui dirige le combat, de -1 si le nombre d'unités engagées
est supérieur à ce niveau, de -2 si aucun officier ne dirige le combat.<br/>
4) On calcule le nombre de dés disponible pour chaque camp, secteur par secteur. Ce nombre de dés est égal à 1 à 3 suivant le niveau d'engagement + la somme des valeurs d'expérience des unités engagées + la somme de l'efficacité  (voir tableau ci-dessous) des unités engagées
+ le modificateur stratégique calculé précedemment + 2 si une unité amie occupe
un autre front voisin libre de tout ennemi + bonus tactique du meilleur officier engagé dans le secteur (+1 a minima si un officier est présent) + rapport de force des effectifs entre soit et l'ennemi (ex +2 si l'on est à 2 contre 1) avec un maximum de 6 contre 1. 
Si l'une des unités présente est en defense, on ajoute également le bonus du
terrain ainsi que son bonus de fortification (1 à 2 dés). Une unité est en défense si elle n'était pas en mouvement au déclenchement de la bataille. Quel que soit le résultat de cette somme, le nombre de dés est toujours au minimum égal à 3.<br />
5) On lance, secteur par secteur, les dés (à 6 faces) de chaque camp, la somme de ces dés divisée par 2 est la valeur de moral perdue par chaque unité du camp adverse. La somme de ces dés divisée par 4 est le pourcentage d'effectif perdu par chaque unité du camp adverse<br/>
6) Si une unité voit son moral réduit à zéro, elle fuit le combat, se dirigeant automatiquement vers son officier responsable durant deux heures. Toute unité qui fuit fait perdre 10 de moral à toutes les autres
unités dans son secteur, cela peut avoir un effet boule de neige...<br/>
7) Si l'un des camps n'a plus d'unités engagées (suite à une fuite ou à une retraite) ou que la nuit survient la bataille est terminée.<br/>
9) Si l'arrêt est dû à la nuit, il n'y a ni vainqueur ni vaincu sinon toutes les unités du camp vainqueur gagnent 5 de moral.<br/>
10) Le vainqueur engage une poursuite sur le perdant (voir détail ci-dessous). Pour faire partie ou être engagée dans une poursuite, l'unité doit avoir été engagée durant la bataille, 
sinon celle-ci ne sera que "spectacteur" sans aucun impact sur elle si ce n'est que son mouvement est bloqué et ses ordres supprimés.<br/>
</p>
<p class="aidetexte">
Réorganiser le champ de bataille<br/>
Une unité qui a combattu sur le champ de bataille peut-être mise en retrait le tour suivant. Elle ne combattra donc pas au prochain engagement mais pourra
être remise sur un autre secteur avant l'engagement suivant.<br/>
Attention cependant si toutes les unités qui restent engagées cassent au moral, la bataille est perdue, 
même si, potentiellement il existe des unités en réserve qui pourraient être engagées au tour suivant. Toute 
unité engagée au moins une fois, sera prise en compte pour la poursuite (comme poursuivie ou poursuivant).
</p>
<p class="aidetexte">Unités sans valeur<br/>
Une unité dont le moral tombe à zéro est automatiquement en "fuite". Une unité avec un matériel ou un ravitaillement à zéro est "inapte" au combat. 
Ces unités peuvent se retrouver engagées au combat si aucun commandant en chef n'est présent ou si seules des unités sans valeur sont présentes (choix du'une unité par défaut devant 
engagée au moins un round de combat) mais elles ne feront aucun dommage à l'ennemi, se contentant d'encaisser les tirs et une éventuelle poursuite.
</p>
<p class="aidetexte">Faire retraite<br/>
Lorsque vous donnez un ordre de retraite générale, celui-ci s'impose à toutes les unités présents sur le champ de bataille.<br/>
<ul>
    <li>Soit aucun engagement n'a encore eu lieu, auquel cas, une unité disponible est choisit au hasard, positionnée au centre du champ de bataille. L'ennemi effectue un tir, sans riposte de votre part suivi d'une éventuelle poursuite.</li>
    <li>Soit un engagement a déjà eu lieu, et seule une poursuite éventuelle est engagée.</li>
</ul>
Le fait de faire une retraite immédiate est donc potentiellement plus risqué (vous ne savez pas qu'elle unité engagera l'assaut et vous subissez un tour sans riposter), son seul "intérêt" est de permettre à vos unités de fuir deux heures plus tôt.
</p>
  <p class="aidetexte"><b>Calcul de l'efficacité en combat</b>
  <table border="1" class="tableau">
    <tr><td><b>Matériel</b></td><td>0-20</td><td>21-40</td><td>41-60</td><td>61-80</td><td>81-100</td></tr>
<tr><td><b>Ravitaillement</b></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
<tr><td><b>0-20</b></td><td>-2</td><td>-2</td><td>-1</td><td>-1</td><td>0</td></tr>
<tr><td><b>21-40</b></td><td>-2</td><td>-1</td><td>-1</td><td>0</td><td>+1</td></tr>
<tr><td><b>41-60</b></td><td>-1</td><td>-1</td><td>0</td><td>+1</td><td>+1</td></tr>
<tr><td><b>61-80</b></td><td>-1</td><td>0</td><td>+1</td><td>+1</td><td>+2</td></tr>
<tr><td><b>81-100</b></td><td>0</td><td>+1</td><td>+1</td><td>+2</td><td>+2</td></tr>
</table>
  </p>
  <p class="aidetexte">
    Du rôle de l'artillerie<br/>
    Le camp qui possède le plus de canons sur une zone relance un nombre de dés égal au rapport des canons entre les deux camps pour un maximum de 2. Seuls les résultats de 1 ou 2 est relancé.
    Exemple : Le camp A dipose de 63 canons, le camp B, de 20. Le camp A a donc droit a 63/20= 3 relances réduite au maximum de 2.
    Le camp A lance 8 dés avec les résultats suivants : 6,5,5,4,3,3,3,2. Seul le "2" est uniquement relancé.
  </p>
    <p class="aidetexte">Vous n'êtes pas immortel.<br/>
    Bien qu'ils ne puissent rien vous arriver durant vos mouvements, il n'en est pas de même durant un combat. A chaque phase de combat (toutes les deux heures donc), si un camp fait quatre "6" sur les dés lancés, un chef du camp
    opposé est blessé. Dans ce cas, on lance deux dés avec le résultat suivant :
    <table border="1" class="tableau">
        <tr><td><b>Score</b></td><td><b>Résultat</b></td></tr>
      <tr>
        <td>2-7</td>
        <td>La blessure est légère, le score correspond au nombre d'heures où l'on va devoir vous soigner. 
        Pendant que vous êtes dans les mains du médecin, vous ne saurez plus en mesure de donner des ordres.</td>
      </tr>
      <tr>
        <td>8-11</td>
        <td>La blessure est grave, le score correspond au nombre de jours où l'on va devoir vous soigner.
        Durant deux heures, vous ne pouvez plus commander le temps de nommer un remplaçant. 
        Vous prenez ensuite le rôle de ce remplaçant, son rang hierarchique est toujours inférieur de un au rang de son prédécesseur, 
        il en est générallement de même pour ses capacités stratégiques et tactiques mais pas toujours. Quand le chef d'origine est rétabli, 
        il reprend sa place à la tête de ses unitées.</td>
      </tr>
      <tr>
        <td>12</td>
        <td>Vous êtes mort ! Ce cas est géré comme une blessure grave, sauf que, bien sur, le chef d'origine ne revient jamais.</td>
      </tr>
    </table>    
    </p>
<p class="aidetexte">La poursuite<br/>
    Si une bataille se termine à cause de la nuit, aucune poursuite n'est engagée car il n'y a ni vainqueur, ni vaincu<br/>
    Seules les unités ayant été engagées au moins durant un round durant une bataille sont prises en compte dans la poursuite.<br/>
    Lorsqu'une bataille se termine par retraite ou par la fuite d'un des camps, toute unité de cavalerie du vainqueur <b>avec un moral supérieur ou égal à 20</b> entame la poursuite du perdant.<br/>
    Un nombre de pertes est déterminé suivant le ratio de cavalerie vainqueur/vaincu et le moral des cavaleries poursuivantes (voir tableau ci-dessous). Les pertes sont ensuite affectées par tranche de 100 pertes
    (arrondies à l'inférieur) en commençant par les unités avec le plus de cavalerie plus celles avec le plus de moral.<br/>
    Une unité avec un moral à zéro perd le double de soldats, une unité d'infanterie perd le double de fantassins.<br/>
    Exemple : Un camp décide de faire retraite. A ce moment il a en bataille les unités suivantes :<br/>
    A : 3410 cavaliers, moral 0; B: 2200 cavaliers, moral 23; C: 8400 fantassins, moral 0; D: 2300 fantassins, moral 0.<br/>
    Le vainqueur a les unités de cavalerie suivantes :<br/>
    a : 4460 cavaliers, moral 28; b: 3700 cavaliers, moral 7; c:880 cavaliers, moral 31 (non engagée).<br/>
    La poursuite n'est menée que par la cavalerie (a). (b) n'a pas le moral suffisant, (c) n'a pas été engagée.<br/>
    Le ratio est de 4460/(3410+2200) = 0,795, soit 1/1 (arrondi au plus proche). Le moral résiduel du vainqueur est de 28.<br/>
    Sur un jet de dé de 2, le vaincu perd 4460x8% = 356 soldats arrondis à 300 soldats.<br/>
    L'unité A perd en premier (elle a le plus de cavaliers) 200 cavaliers (double par le moral à 0).<br/>
    L'unité B perd en second 100 cavalies.</br>
    L'unité C ou D (au hasard, pas de cavaliers, même moral) perd 400 fantassins (moral à 0, infanterie). Pour la suite on suppose que C a pris les pertes.<br/>
    D ne subit aucun effet car les 300 pertes ont été prises par les autres unités avant elle.<br/>
    Les unités A, B et C perdent également 8 points de moral, c'est à dire que B n'a plus que 15 de moral au lieu de 23.<br/>
    Ces trois unités perdent également 24 points de matériel, 16 points de ravitaillement et 16% de leur artillerie (arrondi à l'inférieur), tout cela sera recupéré sous forme de
    butin par toutes les unités engagées du vainqueur (au prorata de l'effectif engagé).    
  </p>  
  <p class="aidetexte"><b>Calcul du ratio de perte par poursuite</b>
  <table border="1" class="tableau">
<tr><td colspan="6"><b>Ratio cavalerie vainque/vaincu</b></td>&nbsp;<td></td><td colspan="2"><b>Moral résiduel du vainqeur</b></td></tr>
<tr><td><b><1/2</b></td><td><b>1/2</b></td><td><b>1/1</b></td><td><b>2/1</b></td><td><b>3/1</b></td><td><b>4/1</b></td><td>&nbsp;</td><td><b>21-30</b></td><td><b>>30</b></td></tr>
<tr><td>2</td><td>1</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>4</td><td>6</td></tr>
<tr><td>3</td><td>2</td><td>1</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>6</td><td>8</td></tr>
<tr><td>4</td><td>3</td><td>2</td><td>1</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>8</td><td>10</td></tr>
<tr><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td><td>&nbsp;</td><td>&nbsp;</td><td>10</td><td>12</td></tr>
<tr><td>6</td><td>5</td><td>4</td><td>3</td><td>2</td><td>1</td><td>&nbsp;</td><td>12</td><td>14</td></tr>
<tr><td>&nbsp;</td><td>6</td><td>5</td><td>4</td><td>3</td><td>2</td><td>&nbsp;</td><td>14</td><td>16</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td>6</td><td>5</td><td>4</td><td>3</td><td>&nbsp;</td><td>16</td><td>18</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>6</td><td>5</td><td>4</td><td>&nbsp;</td><td>18</td><td>20</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>6</td><td>5</td><td>&nbsp;</td><td>20</td><td>22</td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>6</td><td>&nbsp;</td><td>22</td><td>24</td></tr>
</table>
      Pertes en moral = % de pertes en effectifs.<br/>
      Pertes en ravitaillement = 2 x % de pertes en effectifs.<br/>
      Pertes en matériel  = 3 x % de pertes en effectifs.<br/>
      Pertes en artillerie = 2 x % de pertes en effectifs (mais seule la moitié est récupérée par le vainqueur).<br/>
<p class="aidetexte">Des blessés et des prisonniers<br/>
    Tout le monde ne meurt pas sur le champ de bataille. Une fois l'un des camps vaincu, il reste sur le terrain de nombreux blessés et des prisonniers
    que le vainqueur du combat devra gérer (non, passez tout le monde par les armes n'est pas une option). Après une bataille, le camp victorieux verra donc
    apparaître des convois de blessés et de prisonniers parmi ses troupes.<br/>
    Les blessés doivent être amenés à un hôpital. Après une semaine de soins, un pourcentage des troupes, dépendant de la nation et du scénario, est soigné et
    sera disponible comme renfort pour réintégrer vos troupes. Si un convoi de blessés rencontre une troupe ennemie, ils sont fait prisonniers.<br/>
    Les prisonniers sont sous escorte équivalente à 10% du nombre de prisonniers. Lorsque les prisonniers sont conduits à une prison, cette escorte est disponible
    comme "renfort" que vous pouvez réintégrer dans vos unités. Si un convoi de prisonniers rencontrent une troupe armée ennemie, les prisonniers deviennent des
    renforts et l'escorte est prisonnière de cette nouvelle unité.<br/>
    Les emplacements des hôpitaux et des prisons sur le terrain sont connus de tous les camps.<br/>
    Si une troupe ennemie arrivent sur un hôpital, tous les blessés deviennent des prisonniers de l'ennemi.<br/>
    Si une troupe ennemie arrivent sur une prison, tous les prisonniers deviennent des renforts pour l'ennemi.<br/>
  </p>
</div>

<div class="aidesection">
<a onclick = "toggle('bilan')" class="aide">
<img alt="section" class="aidesection" src="images/aidesection.png" />Bilan (Compte-rendu)
</a>
</div>
<div id="bilan" class="aidetexte">
<p class="aidetexte">
Lorsque qu'une partie est terminée, il est possible d'aller consulter son compte-rendu en cliquant sur le nom de la partie, même sans y avoir participé.<br/>
Le compte-rendu permet de voir le bilan des pertes et des scores de toutes les unités de la partie
</p>
</div>

<div class="aidesection">
<a onclick = "toggle('faq')" class="aide">
<img alt="section" class="aidesection" src="images/aidesection.png" />F.A.Q.
</a>
</div>
<div id="faq" class="aidetexte">
<p class="aidetexte">
C'est dans cette section que sont données les réponses aux questions les plus frequemment posées par les joueurs.
</p> 
<p class="aidetexte">
<b>Le "vol de l'aigle" est une bonne régle mais pourrait-on, pour les combats, utiliser la règle "old empire", traduction française, version 6.144, du club des wargameurs de Tagada sur Seine ?</b><br/>
</p>
<p class="aidetexte">
VAOC est déstiné à aider à l'arbitrage du "vol de l'aigle", pas à produire une énième simulation des conflits napoléoniens aussi bonne soit-elle. Par ailleurs, je ne suis pas du tout un expert
de l'époque Napoléonnienne période que j'affectionne beaucoup moins que la seconde guerre mondiale. VAOC s'attache donc à reproduire, du mieux possible, les règles imaginées et écritent
par Didier Rouy, les seules fois ou celles-ci ne sont pas suivis c'est uniquement pour des raisons techniques ou par souci de pas passer des jours à coder un point spécifique, jamais
parce que je considère que cette modification produire une meilleure simulation que la règle d'orgine. Le "vol de l'aigle" est et restera la référence absolue de VAOC. 
</p>
<p class="aidetexte">
<b>Qu'est ce qu'une PATROUILLEMESSAGER, par rapport à une patrouille et à un messager ?</b><br/>
</p>
<p class="aidetexte">
Quand vous envoyez une patrouille, celle-ci se déplace à la vitesse d'une patrouille jusqu'à ce qu'elle arrive à son point de destination ou qu'elle rencontre un ennemi.
A ce moment là, elle se transforme en PATROUILLEMESSAGER et revient vers vous pour donner le résultat de sa patrouille. Il s'agit donc d'une patrouille qui vient rapporter un message (et à la vitesse
de celui-ci). Ce n'est donc plus vraiment une patrouille mais pas non plus un messager.
</p>
<p class="aidetexte">
<b>Que se passe-t-il si j'envoie deux unités en même temps sur la même route ?</b><br/>
</p>
<p class="aidetexte">
Si deux unités empruntent le même chemin, l'une d'elle prendra la route, l'autre marchera, à coté, suivant le terrain entourant la route, évidemment à une vitesse moindre. Exception notable
des ponts où une seule unité peut passer à la fois, les ponts peuvent donc constituer de véritables goulets d'étranglements.
</p>
<p class="aidetexte">
<b>Je suis dans la même ville que le général Dupont, on peut se trouver une gargotte pour discuter en direct ?</b><br/>
</p>
<p class="aidetexte">
Dans ce cas, dans la liste des destinataires de messages, vous verrez afficher (direct) derrière son nom. Si vous lui écrivez le message sera directement visible par votre interlocuteur et un courriel l'avertira de votre message.
<br/>
</p>
<p class="aidetexte">
<b>J'ai envoyé un message a un autre officier (joueur) a une destination qui n'est pas la bonne, que va faire le messager ?</b><br/>
</p>
<p class="aidetexte">
Le messager va se rendre au point indiqué, constatant l'abscence du destintaire il vérifie si celui-ci est présent dans un rayon de 30 kilomètres autour de la position. Si c'est le cas
il s'y rend et remet le message, sinon, il reviendra pour te prévenir qu'il n'a pas pu remettre le message qui lui a été remis.<br />
Pour rappel, le nombre de messagers que l'on peut envoyer n'est pas limité.
</p>
<p class="aidetexte">
<b>J'ai donné des ordres de mouvement et pourtant, les positions des unités dans le tableau transmis toutes les heures ne changent pas, pourquoi ?</b><br/>
</p>
<p class="aidetexte">
La position donnée d'une unité est celle de l'officier qui marche toujours en fin de colonne (ce qui permet de le rattraper facilement si on veut lui envoyer un nouvel ordre) et donc va être le dernier à quitter la position courante de l'unité.
De plus, la position d'une unité n'est mise à jour que lorsque celle-ci vous envoie un message (n'importe lequel). Imaginons qu'une unité reçoive un ordre de mouvement pour aler de A à B, que cela prenne 6 heures et que rien de particulier n'arrive durant le mouvement. Vous allez recevoir un message comme quoi l'unité a reçu ton ordre de mouvement, puis, durant 6 heures, l'unité sera marquée comme étant en A, 6 heures plus tard vous recevrez un message comme quoi elle est bien arrivée à destination, et, brutalement, elle sera à B dans le tableau de résumé.
Par ailleurs, tous les jours, à minuit, toutes les unités envoient un rapport de position et d'état de leur troupes.
C'est la même chose pour les informations affichées dans l'écran de QG, avec la carte. 
</p>
<p class="aidetexte">
<b>Si une unité est composée de plusieurs types de troupes, quelle est la vitesse de déplacement ?</b><br/>
</p>
<p class="aidetexte">
La vitesse de déplacement d'une unité est celle de sa composante la plus lente.
</p>
<p class="aidetexte">
<b>Est-il possible d'emprunter des routes hors-carte ?</b><br/>
</p>
<p class="aidetexte">
Rien ne peut sortir de la carte
</p>
<p class="aidetexte">
<b>Pourquoi la destination du mouvement de mon unité indiquée n’est pas la même que celle que j’ai donnée dans mon ordre ?</b><br/>
</p>
<p class="aidetexte">
La destination affichée est basée sur le lieu nommé le plus proche du point d’arrivée du mouvement et non de celui donné dans l’ordre.
<img alt="exemple destination" class="aidesection" src="images/aide_faq_destination.png" />
<br/>Exemple : vous indiquez à une unité d’aller à 15 kms au Sud-Est du point A, c’est-à-dire au point B. Comme le point B n’est situé qu’à 5 kms du point C, l’indication finale qui vous sera fourni sera « aller à 5 km au Sud-Ouest de C ».

</p>

</div>

<div align='center'>
    <input name="id_quitter" class="btn btn-default"
           id="id_quitter" onclick="javascript:window.close();return false;"
           type="image" alt="quitter" src="images/btnQuitter2.png" value="submit" />                
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="js/jquery-3.4.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="js/bootstrap4.min.js"></script>
<!-- https://github.com/steveathon/bootstrap-wysiwyg : pour l'editor -->
<script src="js/bootstrap-wysiwyg.min.js"></script>

<!-- https://github.com/jeresig/jquery.hotkeys  : pour l'editor -->
<script src="js/jquery.hotkeys.js"></script>

<!-- pour le lazy loading des images  -->
<script src="js/jquery.lazy.min.js"></script>

<script type="text/javascript">
<!--  au depart on ferme tout -->
fermetureGlobale();
</script>
</div>
</body>
</html> 