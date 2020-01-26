<?php
/**
* Mesurer les performances et deboguer un script php
* http://www.fobec.com/php5/1180/mesurer-performances-deboguer-script-php.html
* @author Fobec 06/2016
*/
class Debug_Logger {
 
//Singleton: instance de la class
protected static $instance;
//Tableau pour stocker les données
static private $time_line_firststamp = array();
static private $time_line_laststamp = array();
static private $time_line = array();
static private $time_pool = array();
static private $time_periods = array();
static private $time_periods_count =array();
 
/**
* Constructeur
*/
private function __construct() {
/* pas besoin dans ce cas */
}
 
/**
* Singleton: instance à partir d'un contexte statique
* @return type
*/
static public function getInstance() {
    if (!isset(self::$instance)) {
        self::$instance = new self;
    }

    return self::$instance;
}
 
/**
* Clonage non utilisé
*/
private function __clone() {
/* do nothing here */
}
 
/**
* Remettre à zéro
*/
static public function reset() {
    self::$instance = null;
    self::$time_periods = array();
    self::$time_pool = array();
    self::$time_periods_count = array();
}
 
/**
* Timeline: lancer la mesure
*/
public function timeStart() {
    self::$time_line_laststamp = microtime(true);
    self::$time_line_firststamp = self::$time_line_laststamp;
    self::$time_line[] = array(0, 0, 'start');
}
 
/**
* Timeline: ajouter un point d'arrêt
*/
public function timeAddPoint($msg = '') {
    $t_cur = microtime(true);
    $t_elapse_last = $t_cur - self::$time_line_laststamp;
    $t_elapse_first = $t_cur - self::$time_line_firststamp;


    self::$time_line_laststamp = $t_cur;
    self::$time_line[] = array($t_elapse_first, $t_elapse_last, $msg);
}
 
/**
* Timeline: arrêter la mesure
*/
public function timeStop() {
    $t_cur = microtime(true);
    $t_elapse_last = $t_cur - self::$time_line_laststamp;
    $t_elapse_first = $t_cur - self::$time_line_firststamp;


    self::$time_line_laststamp = $t_cur;
    self::$time_line[] = array($t_elapse_first, $t_elapse_last, 'end');
}
 
/**
* Cumul: débuter l'addition du temps d'execution
* @param type $msg
*/
public function time_mesure_start($msg = '') {
    if (!empty($msg)) {
    $id = $msg;
    } else {
    $id = 'dummy';
    }
    self::$time_pool[$id] = array('start', microtime(true), $msg);
}
 
/**
* Cumul: arrêter l'addition du temps d'execution
* @param type $msg
*/
public function time_mesure_end($msg = '') {
    if (!empty($msg)) {
        $id = $msg;
    } else {
        $id = 'dummy';
    }
    //Time Elapse
    $time_elapse = -1;
    if (isset(self::$time_pool[$id]) && self::$time_pool[$id][0] == 'start') {
        $time_elapse = microtime(true) - self::$time_pool[$id][1];
    }
    //Concat
    if ($time_elapse > -1) {
        $time_count = 0;
        $compteur = 0;
        if (isset(self::$time_periods[$msg])) {
            $time_count = self::$time_periods[$msg];
            $compteur = self::$time_periods_count[$msg];
        }
        self::$time_periods[$msg] = $time_count + $time_elapse;
        self::$time_periods_count[$msg] = $compteur + 1;
    }
}
 
/**
* Timeline: visualiser le résultat sous forme de string
* @return string
*/
public function getTimeLineString() {
    $buf = '';
    if (count(self::$time_line) > 0) 
    {
        $buf.="<div class=\"row\">";
        $buf.="<div class=\"col-3\"><b>debut</b></div><div class=\"col-3\"><b>fin</b></div><div class=\"col-6\"><b>position</b></div>";
        $buf.="</div>";

        //$buf.=str_pad('debut', 12) . "t" . str_pad('fin', 12) . "t" . 'tag' . "<br/>";
        foreach (self::$time_line as $row) 
        {
            $buf.="<div class=\"row\">";
            $buf.="<div class=\"col-3\">".number_format($row[0], 10, ',', ' ') . " s</div>";
            $buf.="<div class=\"col-3\">".number_format($row[1], 10, ',', ' ') . " s</div>";
            $buf.="<div class=\"col-6\">".$row[2] . "</div>";
            $buf.="</div>";
        }
    }
    return $buf;
}
 
/**
* Cumul: visualiser le résultat sous forme de string
* @return string
*/
public function getTimeMesureString() {
    $buf = '';
    if (count(self::$time_periods) > 0) {
        $maxlen = 0;
        foreach (self::$time_periods as $key => $val) {
            $maxlen = max($maxlen, strlen($key));
        }
        $maxlen+=2;
        
        //$buf.="<table class=\”table table-striped\”><thead><tr><th>fonction</th><th>temps</th></tr></thead><tbody>";
        $buf.="<div class=\"row\">";
        $buf.="<div class=\"col-6\"><b>fonction</b></div><div class=\"col-5\"><b>temps</b></div><div class=\"col-5\"><b>nb</b></div>";
        $buf.="</div>";
        //$buf.=str_pad('tag', $maxlen) . "t" . 'time' . "<br/>";
        arsort(self::$time_periods);
        foreach (self::$time_periods as $key => $val) 
        {
            $buf.="<div class=\"row\">";
            $buf.="<div class=\"col-6\">".str_pad($key, $maxlen) . "</div>";
            $buf.="<div class=\"col-5\">".number_format($val, 10, ',', ' ') . " s</div>";
            $buf.="<div class=\"col-1\">".self::$time_periods_count[$key] . "</div>";
            $buf.="</div>";
            //$buf.="<tr><td>".$key."</td><td>".number_format($val, 10, ',', ' ')."</td></tr>";
            //$buf.=str_pad($key, $maxlen) . "t";
            //$buf.=number_format($val, 10, ',', ' ') . "t" . "<br/>";
        }
        $buf.="</tbody></table>";
    }
    return $buf;
}
}
?>
