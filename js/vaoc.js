/* 
 * vaoc.js Copyright Armand Berger 2019
 */

 $(document).ready(function () {
    /* Fonctions pour les listes déroulantes typeahead et selection hors url */
    $('.dropdown-toggle').dropdown();
    $('.selectpicker').selectpicker({});
    
    /* Fonctions pour le chargement différé des destinations */
    $.fn.chargeNomsCarte = function()
    {
        console.log("chargeNomsCarte"+$(this).val());
        if ($(this).find("option").length < 10)
        {
            var monselect = this;//oblige de faire cela car chargement asynchrone la valeur est perdue sinon
            console.log("chargeNomsCarte chargement");
            $(this).find('option').remove();
            $.ajax({url: "./listenomscarte.php?partie=8"
                    ,success: function (data, statut) {
                        console.log(statut);
                        $(monselect).append(data);
                        console.log("fin append");
                    }
                    , error: function (data, statut, erreur){
                        console.log("erreur"+erreur);
                        $(monselect).append("<option>Erreur dans listenomscarte :"+erreur+"</option>");
                    }
            });
        }
        console.log("fin chargeNomsCarte"+$(this).val());
    }    
 });
 /*
$(function () {
    $(".dropdown-menu").on('click', 'li a', function () {
      var selText = $(this).text();
      var Drop = selID = $(this).parents('.dropdown').find('.dropdown-toggle');
      var selID = Drop.attr('id');
      
      //alert("sel_"+selID);
      Drop.html(selText+' <span class="caret"></span>');
       //$(this).closest(".dropdown-menu").prev().dropdown("toggle");
       document.getElementById("sel_"+selID).value=selText;
       //alert(document.getElementById("sel_"+selID).value);
    });
});
*/
/* Fonctions pour le tri des unités */
var dragImg = new Image(); // Il est conseillé de précharger l'image, sinon elle risque de ne pas s'afficher pendant le déplacement
dragImg.src = 'images/dragdrop.png';

function allowDrop(ev) {
    ev.preventDefault();
    }

    function entrerDrop(ev) {
        ev.preventDefault();
        var destination = document.getElementById(ev.target.id);
        destination.classList.remove("dropper");
        destination.classList.add("drop_hover");
        console.log("entrerDrop" + ev.target.id);
    }

    function quitterDrop(ev) {
        ev.preventDefault();
        var destination = document.getElementById(ev.target.id);
        destination.classList.remove("drop_hover");
        destination.classList.add("dropper");
        console.log("quitterDrop" + ev.target.id);
    }

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
    ev.dataTransfer.setDragImage(dragImg, 13, 13); // Une position de 40x40 pixels centrera l'image (de 80x80 pixels) sous le curseur
}

function drop(ev) {
    ev.preventDefault();

        var destination = document.getElementById(ev.target.id);
        destination.classList.remove("drop_hover");
        destination.classList.add("dropper");

var data = ev.dataTransfer.getData("text");
    var source = document.getElementById(data);
    var dropsuivant = source.nextElementSibling;
    //alert(data);
    //alert(ev.target.id);
    //alert(source.getAttribute("id_pion"));
    //alert(dropsuivant.id);
    ev.target.after(source);
    source.after(dropsuivant);
    //document.getElementById("orga").append(.appendChild(document.getElementById(data));
    //Mise a jour de l'ordre
    var ordre = document.getElementById("id_ordre_tri");
    var pion = document.getElementById("drop0");
    //alert(ordre.id);
    ordre.value = "";
    while (pion = pion.nextElementSibling)  // While there is a next sibling, loop
    {
        //alert(pion.getAttribute("id_pion"));
        if (pion.getAttribute("id_pion")) {
            if (ordre.value != "") { ordre.value += ","; }
            ordre.value += pion.getAttribute("id_pion");
        }            
    }
}

function Trier()
{
    console.log("Trier");
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php#troupes";
    formPrincipale.target = "_self";
    formPrincipale.submit();            
}
var memoTexteMessage = "";
var myTailleLimite = setInterval(function () { TailleLimite() }, 1000);

function TailleLimite() {
    var countedTextBox = document.getElementById("editor");
    var maxSize = 5000;

    //alert(countedTextBox.innerHTML);
    if (countedTextBox && countedTextBox.innerHTML.length >= maxSize) {
        countedTextBox.innerHTML = memoTexteMessage;
    }
    else {
        memoTexteMessage = countedTextBox.innerHTML;
    }

    var txtField = document.getElementById("cptMessage");
    if (txtField) {
        txtField.innerHTML = countedTextBox.innerHTML.length;
        //alert(txtField.innerHTML);
    }
}

function toggleLayer(whichLayer)
{
    var elem, vis;
    if (document.getElementById) // this is the way the standards work    
        elem = document.getElementById(whichLayer);
    else if (document.all) // this is the way old msie versions work      
        elem = document.all[whichLayer];
    else if (document.layers) // this is the way nn4 works    
        elem = document.layers[whichLayer];

    vis = elem.style;  // if the style.display value is blank we try to figure it out here  
    if (vis.display == '' && elem.offsetWidth != undefined && elem.offsetHeight != undefined)
        vis.display = (elem.offsetWidth != 0 && elem.offsetHeight != 0) ? 'block' : 'none';
    vis.display = (vis.display == '' || vis.display == 'block') ? 'none' : 'block';
}

function onOff(id, on)
{
    var elem, vis;
    if (document.getElementById) // this is the way the standards work    
        elem = document.getElementById(id);
    else if (document.all) // this is the way old msie versions work      
        elem = document.all[id];
    else if (document.layers) // this is the way nn4 works    
        elem = document.layers[id];

    vis = elem.style;  // if the style.display value is blank we try to figure it out here  
    if (on == 'O')
        vis.display = 'block';
    else
        vis.display = 'none';
}

function onOffCarte(id, historique, topographique, zoom, film)
{
    onOff("historique_" + id, historique);
    onOff("topographie_" + id, topographique);
    onOff("zoom_" + id, zoom);
    onOff("film_" + id, film);
}

function reloadFilm(idFilmRole, srcFilmRole)
{
    var elem;
    if (document.getElementById) // this is the way the standards work    
        elem = document.getElementById(idFilmRole);
    else if (document.all) // this is the way old msie versions work      
        elem = document.all[idFilmRole];
    else if (document.layers) // this is the way nn4 works    
        elem = document.layers[idFilmRole];

    //alert(elem.src);
    elem.src=srcFilmRole;//+"?a="+Math.random();//pour forcer un rechargement
    //alert(elem.src);
}

function onOffBataille(id, historique, topographique)
{
    onOff("historique_" + id, historique);
    onOff("topographie_" + id, topographique);
}

function callPage(nouvellePage)
{
    //alert ("callPage");
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = nouvellePage;
    //alert (nouvellePage);
    formPrincipale.target = "_blank";
    formPrincipale.submit();
}

function callQuitter()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "index.php";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callVictoire()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocvictoire.php";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callHistorique()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaochistorique.php";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callChangementRole()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callDonnerOrdreUnite(id_pion, id_element_unite)
{
    $.ajax(
        {
            url: 'setaction.php',
            type: 'POST',
            dataType: 'html',
            success: function(data)
            {
                //alert("success"+data);
            }
            , error: function(data)
            {
                //alert("error"+data);
            }
        }
    );
    //alert(id_element_unite);
    //document.getElementById("postback").value = 0; -> marche pas ce truc
    //alert(document.getElementById("postback").value);
    //alert(id_element_unite);
    if (id_element_unite === "id_envoyermessagea")
    {
        var message = document.getElementById("editor").innerHTML;
        //alert(message);
        //alert(message.slice(message.length-4, message.length));
        //alert(message.slice(-4));
        if (message.length>4 && message.slice(message.length-4, message.length)==="<br>")
        {
            document.getElementById("id_message").value=message.slice(0,message.length-4);
        }
        else
        {
            document.getElementById("id_message").value=message;
        }
        //alert(document.getElementById("id_message").value);
    }
    if (id_element_unite === "id_changementNom")
    {
        var message = document.getElementById("nom_pion" + id_pion).value;
        //alert("nom_pion" + id_pion);
        //alert(message);
        document.getElementById("id_message").value=message;
    }
    document.getElementById(id_element_unite).value = id_pion;
    //alert(document.getElementById(id_element_unite).value);
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php#tableau_pion" + id_pion;
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callSupprimerOrdre(id_pion, id_ordre)
{
    $.ajax(
        {
            url: 'setaction.php',
            type: 'POST',
            dataType: 'html',
            success: function(data)
            {
                //alert("success"+data);
            }
            , error: function(data)
            {
                //alert("error"+data);
            }
        }
    );
    document.getElementById("id_supprimerallera").value = id_ordre;
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php#tableau_pion" + id_pion;
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callChangementNom(id_pion, id_element_unite, nom_invariant)
{
    $.ajax(
        {
            url: 'setaction.php',
            type: 'POST',
            dataType: 'html',
            success: function(data)
            {
                //alert("success"+data);
            }
            , error: function(data)
            {
                //alert("error"+data);
            }
        }
    );
    var message = document.getElementById("nom_pion" + id_pion).value;
    //alert("nom_pion" + id_pion);
    //alert(message);
    document.getElementById("id_message").value=nom_invariant + message;
    document.getElementById(id_element_unite).value = id_pion;
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php#tableau_pion" + id_pion;
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callAllerALapage(id_pion, i_page)
{
    //alert("id_pion=" + id_pion + " i_page=" + i_page);
    formPrincipale = document.getElementById("principal");
    formPrincipale.target = "_self";
    if (id_pion < 0)
    {
        id = "pageNum_recus";//liste des messages en bas de page
        formPrincipale.action = "vaocqg.php#tableau_messages";
    }
    else
    {
        id = "pageNum_" + id_pion;
        formPrincipale.action = "vaocqg.php#tableau_pion" + id_pion;
    }
    //alert(id);
    document.getElementById(id).value = i_page;
    //alert(document.getElementById(id).value);
    formPrincipale.submit();
}

function changeRecepteur()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callCarte()
{
    callPage("vaoccarte.php");
}

function callBataille()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocbataille.php";
    formPrincipale.target = "_blank";
    formPrincipale.submit();
}

function callOrdresTermines(bordreterminesprecedant)
{
    //alert(bordreterminesprecedant);
    if (0 == bordreterminesprecedant)
    {
        document.getElementById("id_ordres_termines").value = 1;
    }
    else
    {
        document.getElementById("id_ordres_termines").value = 0;
    }
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callNombreMessagesPage()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php#tableau_messages";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callListePontsVilles()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php#tableau_messages";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function callMoi()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = window.location.href;
    formPrincipale.target = "_self";
    //alert(window.location.hash);
    //alert(window.location.href);
    formPrincipale.submit();
}

function counterUpdate(opt_countedTextBox, opt_countBody,
        opt_maxSize)
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

function callTri(tri)
{
    //alert(tri);
    //alert(document.getElementById("tri_liste").value);
    if (document.getElementById("tri_liste").value == tri)
    {
        if (document.getElementById("ordre_tri_liste").value == "")
        {
            document.getElementById("ordre_tri_liste").value = "DESC";
        }
        else
        {
            document.getElementById("ordre_tri_liste").value = "";
        }
    }
    else
    {
        document.getElementById("tri_liste").value = tri;
        document.getElementById("ordre_tri_liste").value = "";
    }
    //alert(document.getElementById("tri_liste").value);

    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php#tableau_messages";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

function changeEmetteur()
{
    formPrincipale = document.getElementById("principal");
    formPrincipale.action = "vaocqg.php#tableau_messages";
    formPrincipale.target = "_self";
    formPrincipale.submit();
}

$(function () {
        $('#editor').wysiwyg();
});
