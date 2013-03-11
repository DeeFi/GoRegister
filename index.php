<?php

$delimiter = '|';

function GoRegister_get_pth_to_plugin() {
    //Pfad zu Stammverzeichnis
    Global $pth;

    $plugin = basename(dirname(__FILE__),"/");
    $plugin_pth = $pth['folder']['plugins'].$plugin.'/';

    return $plugin_pth;
}

$plugin_pth = GoRegister_get_pth_to_plugin();


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
    while (($data = fgetcsv ($datei, 1000, $delimiter)) !== false ) {
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

/**
 * Plugin GoRatingliste
 */

/**
 * Convert a comma separated file into an associated array.
 *
 * Script based on: 
 * @link http://gist.github.com/385876
 */


function GoRegister_csv_to_array($filename) {

    Global $plugin_pth;
    Global $delimiter;

    // Überprüfe ob die Datei existiert
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;
    
    $data = array();

    // Überprüfe den Fall "Ratingliste" / "Sonstige" und baue dann ein Array aus der csv
    if (($handle = fopen($filename, 'r')) !== FALSE) {
        switch ($filename) {
            case $plugin_pth.'ratinglist.csv':  // 1. Ratingliste
                $header = array("ID");
                while (($row = fgetcsv($handle, 1000)) !== FALSE) {
                    $data[] = array_combine($header, $row);
                }
                break;
            default:                            // 2. Sonstige = Anmeldeliste
                $header = array("Name", "Vorname", "Rang", "Stadt", "Land");
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                    $data[] = array_combine($header, $row);
                }
        }
        fclose($handle);
    }
    return $data;
}


function ratinglist() {

    Global $plugin_tx;
    Global $plugin_pth;

    $filename = $plugin_pth."ratinglist.csv";    

    $id = GoRegister_csv_to_array($filename);

    $i = 0; // Zähler für die folgende Schleife

    foreach ($id as $spieler) {

        $url = "http://www.europeangodatabase.eu/EGD/GetPlayerDataByPIN.php?pin=".$spieler['ID'];
        $data = file_get_contents($url);

        $save = json_decode($data,true); //Dekodiere EGD JSON-String zu einem Array

        /**
         * Aufbau des JSON-Array
         * [Pin_Player] - ID des Spielers in der Datenbank
         * [AGAID] - Falls vorhanden, ID in der AGA-Datenbank
         * [Last_Name] - Nachname
         * [Name] - Vorname
         * [Country_Code] - Land, zweistelliger Code
         * [Club] - Code des Clubs (zwei bis vierstellig)
         * [Grade] - Rang (20k-8d)
         * [Grade_n] - (Nummerische Speicherung des Rangs, aufsteigend
         * [EGF_Placement] - Platzierung in der Datenbank
         * [Gor] - Rating
         * [DGor] - ???
         * [Proposed_Grade] - Vorgeschlagener Rang (bei großen Abweichungen vom Rating)
         * [Tot_Tournaments] - Anzahl der gespielten Turniere
         * [Last_Appearance] - Code des letzten Turniers
         * [Elab_Date] - ???
         * [Hidden_History] -
         * [Real_Last_Name] - Nachname
         * [Real_Name] - Vorname
         */

        $daten[$i][0] = $save[Name];
        $daten[$i][1] = $save[Last_Name];
        $daten[$i][2] = $save[Gor];
        $daten[$i][3] = $save[Grade];
        $daten[$i][4] = $save[Tot_Tournaments];
        $daten[$i][5] = $save[Pin_Player];
        $i++;
    }

    //Sortiere die Daten absteigend nach Gor
    foreach ($daten as $key => $row) {
        $Gor[$key] = $row[2];
    }
    array_multisort($Gor, SORT_DESC, $daten);

    $output = '<table id="ratinglist">
                <thead>
                    <tr>
                        <th>Rang</th>
                        <th>Vorname</th>
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Rang</th>
                        <th>Turniere</th>
                    </tr>
                </thead>
                <tbody>';

    //Ausgabe der sortieren Liste
    $rank = 1;
    foreach ($daten as $key => $value) {

        if($rank % 2 == 0) {
            $zebra = 'even';
        }
        else {
            $zebra = 'odd';
        }

        $output .= '<tr class="'.$zebra.'">';
        $output .= '<td class="id">' .$rank. '</td>';
        $output .= '<td>' .$value[0]. '</td>';
        $output .= '<td>' .$value[1]. '</td>';
        $output .= '<td>' .$value[2]. '</td>';
        $output .= '<td>' .$value[3]. '</td>';
        $output .= '<td>' .$value[4]. '</td>';
        $output .= '</tr>';
        $rank++;
    }
    $output .= '</tbody>';
    $output .= '</table>';

    return $output;
}




?>