<?php
function sortmddata($array, $by, $order = 'ASC', $type = 'STRING') {
    // $array  - Zu sortierendes Multidimensionales Array
    // $by - Spalte nach der Sortiert werden soll
    // $order - ASC (aufsteigend) oder DESC (absteigend)
    // $type - NUM (numerisch) oder STRING
    $sortby = "sort$by";
    $firstval = current($array);

    $vals = array_keys($firstval);

    foreach ($vals as $init) {
        $keyname = "sort$init";
        $$keyname = array();
    }
    foreach ($array as $key => $row) {
        foreach ($vals as $names) {
            $keyname = "sort$names";
            $test = array();
            $test[$key] = $row[$names];
            $$keyname = array_merge($$keyname, $test);
        }
    }

    if ($order == "DESC") {
        if ($type == "num") {
            array_multisort($$sortby, SORT_DESC, SORT_NUMERIC, $array);
        } else {
            array_multisort($$sortby, SORT_DESC, SORT_STRING, $array);
        }
    } else {
        if ($type == "num") {
            array_multisort($$sortby, SORT_ASC, SORT_NUMERIC, $array);
        } else {
            array_multisort($$sortby, SORT_ASC, SORT_STRING, $array);
        }
    }

    return $array;
}

/**
 * @author Mathias Neumann : 
 * Parameteruebergabe hinzugefuegt, um verschiedene
 * Eingabelisten zu ermoeglichen.
 */
function anmelde_ausgabe($cvsdatei = 'datei.csv') {
    // Aufrufen der Language Dateien
    GLOBAL $plugin_tx;
    //Aktuelle URL
    GLOBAL $su;
    //Pfad zu Stammverzeichnis
    Global $pth;

    //öffnen der Datei
    $plugin = basename(dirname(__FILE__),"/");
    $plugin_pth = $pth['folder']['plugins'].$plugin.'/';
    $datei = fopen($plugin_pth . $cvsdatei,"r");

    // Alles einlesen
    while (($data = fgetcsv ($datei, 1000, "|")) !== false ) {
        $csv[] = $data;
    }
    fclose ($datei);

    // .. und sortieren
    $sort_column = (isset($_GET['sortcolumn']) ? $_GET['sortcolumn'] : 0);
    $sort_order  = (isset($_GET['sortorder']) ? $_GET['sortorder'] : 'ASC');

    // Begin für die Rangausgabe //
    if($sort_column=="2") {
        for($i=0; $i<count($csv); $i++) { // Datei zeilenweise durchlaufen
            $sort_column=55;
        if(preg_match("/d/", $csv[$i][2])) { // Fall 1: Rang gleich "d"
            $csv[$i][55] = (10 - rtrim($csv[$i][2],"dk"))/100;
            }
        else { // Fall 2: Rang gleich "k"
            $csv[$i][55] = (10 + rtrim($csv[$i][2],"dk"))/100;
        }
    }
  }
    // END für die Rangausgabe //

    $csv = sortmddata($csv, $sort_column, $sort_order);

    //Beginn des Outputs. Fixe Größe 5 Spalten für die Reihe. Titel sind in der Sprachdatei definiert.
    $o = '<!-- Beginn of Anmeldeliste Output -->' . "\n";
    $o .= '<table id="anmeldung">'. "\n";
    $o .= '<tr>'. "\n";
    $o .= '<th> ID </th>'. "\n";
    $o .= '<th>' .$plugin_tx['GoRegister']['ausgabe_name']. ' <a href="?'.$su.'&sortcolumn=0&amp;sortorder=DESC"><img src="'.$plugin_pth.'img/pfeil-r.png"></a> <a href="?'.$su.'&sortcolumn=0&amp;sortorder=ASC"><img src="'.$plugin_pth.'img/pfeil-h.png"></a></th>'. "\n";
    $o .= '<th>' .$plugin_tx['GoRegister']['ausgabe_vorname']. ' <a href="?'.$su.'&sortcolumn=1&amp;sortorder=DESC"><img src="'.$plugin_pth.'img/pfeil-r.png"></a> <a href="?'.$su.'&sortcolumn=1&amp;sortorder=ASC"><img src="'.$plugin_pth.'img/pfeil-h.png"></a></th>'. "\n";
    $o .= '<th>' .$plugin_tx['GoRegister']['ausgabe_rang']. ' <a href="?'.$su.'&sortcolumn=2&amp;sortorder=DESC"><img src="'.$plugin_pth.'img/pfeil-r.png"></a> <a href="?'.$su.'&sortcolumn=2&amp;sortorder=ASC"><img src="'.$plugin_pth.'img/pfeil-h.png"></a></th>'. "\n";
    $o .= '<th>' .$plugin_tx['GoRegister']['ausgabe_stadt']. ' <a href="?'.$su.'&sortcolumn=3&amp;sortorder=DESC"><img src="'.$plugin_pth.'img/pfeil-r.png"></a> <a href="?'.$su.'&sortcolumn=3&amp;sortorder=ASC"><img src="'.$plugin_pth.'img/pfeil-h.png"></a></th>'. "\n";
    $o .= '<th>' .$plugin_tx['GoRegister']['ausgabe_land']. ' <a href="?'.$su.'&sortcolumn=4&amp;sortorder=DESC"><img src="'.$plugin_pth.'img/pfeil-r.png"></a> <a href="?'.$su.'&sortcolumn=4&amp;sortorder=ASC"><img src="'.$plugin_pth.'img/pfeil-h.png"></a></th>'. "\n";
    $o .= '</tr>'. "\n";

    //Ausgabe der CSV
    $count = 1;
    foreach ($csv as $row) {

        if($count % 2 == 0) {
            $zebra = 'even';
        }
        else {
            $zebra = 'odd';
        }


        $o .= '<tr class="'.$zebra.'">'. "\n";
        $o .= '<td class="id">' .$count. '</td>'. "\n";
        $o .= '<td>' .$row[0]. '</td>'. "\n";
        $o .= '<td>' .$row[1]. '</td>'. "\n";
        $o .= '<td>' .$row[2]. '</td>'. "\n";
        $o .= '<td>' .$row[3]. '</td>'. "\n";
        //Ausgabe der Spalte Land; Mehrsprachig; mit Flagge
        $state = $row[4];
        $o .= '<td><img src="'.$plugin_pth.'img/flag/'.$plugin_tx['GoRegister']['flag_'.$state].'" /> ' .$plugin_tx['GoRegister']['land_'.$state]. '</td>'. "\n";
        $o .= '</tr>'. "\n";
        $count = $count+1;
    }

    $o .= '</table>'. "\n";
    $o .= '<!-- END of Anmeldeliste Output -->' . "\n";

    //Ausgabe weitergeben
    return $o;
}
?>

