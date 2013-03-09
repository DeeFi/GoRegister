<?php
function GoRegister_get_pth_to_plugin() {
    //Pfad zu Stammverzeichnis
    Global $pth;

    $plugin = basename(dirname(__FILE__),"/");
    $plugin_pth = $pth['folder']['plugins'].$plugin.'/';

    return $plugin_pth;
}

$plugin_pth = GoRegister_get_pth_to_plugin();


function GoRegister_makeTextfield($name, $desc, $val){
    if(isset($_POST[$name]))
        $val = stripslashes(htmlentities($_POST[$name], ENT_QUOTES));
    $d = "<tr>\n";
    $d .= "<th><label for=".$name.">" .$desc.":</label></th>\n";
    $d .= "<td><input "; 

    //Check name to insert js to activate getdata.js
    if($name == 'last_name') 
    {  
        $d .= "onkeyup=\"getData();\" onblur=\"document.getElementById('divHelp').style.display = 'none';void();\" "; 
    } 

    $d .= "type=\"text\" name=\"".$name."\" id=\"".$name."\" size=\"24\" maxlength=\"64\" value=\"".$val."\"/></td>\n";
    $d .= "</tr>\n";
    return $d;
}

function GoRegister_makeRangSelect($name,$desc) {
    $d = "<th><label for=".$name.">".$desc.":</label></th>\n";
    $d .= "<td>\n";
    $d .= "<select name=".$name." id=".$name.">\n";
    for ($i = 9; $i > 0; $i--) {
        $str = $i . "d";
        if(isset($_POST[$name]) AND $str == $_POST[$name])
        $d .= "<option selected=\"selected\">".$str."</option>\r\n";
        else
        $d .= "<option>".$str."</option>\r\n";
    }
    for ($i = 1; $i <= 30; $i++){
        $str = $i . "k";
        if(isset($_POST[$name]) AND $str == $_POST[$name])
            $d .= "<option selected=\"selected\">".$str."</option>\r\n";
        else
            $d .= "<option>".$str."</option>\r\n";
    }
    return $d;
}

// Prüft den gegebenen String auf Fehler.
function GoRegister_checkStringInput($input, $desc, &$errors, $plugin_tx) {
    if(strlen($input) == 0 OR strlen($input) > 64)
        $errors[] = $desc . $plugin_tx['GoRegister']['eingabe_ungueltig'];
}


function GoRegister_include_getdata_js_css() 
{
    Global $plugin_pth;
    Global $hjs;



    if(file_exists($plugin_pth.'js/getdata.js')) 
    {
        $hjs .= "\n".'<script src="'.$plugin_pth.'js/getdata.js"></script>'."\n";
    }

    if(file_exists($plugin_pth.'css/style.css')) 
    {
        $hjs .= "\n".'<link rel="stylesheet" href="'.$plugin_pth.'css/style.css">'."\n";
    }
}



function GoRegister_admin_formular_ausgabe($csvfile) 
{
    // Aufrufen der Language Dateien
    GLOBAL $plugin_tx;
    //Aktuelle URL
    GLOBAL $su;
    //Pfad zu Stammverzeichnis
    Global $plugin_pth;

    // Wenn Name gesetzt ist, wurde ein neuer Beitrag gesetzt, und es soll nichts gelöscht werden!
    if (!isset($_POST['name']))
    {
        if ($_GET['deleteid'])
        {
            $o .= GoRegister_delete_row_in_csv();
        }
    }

    GoRegister_include_getdata_js_css();


    $o .= '<h2>Vorangemeldete Spieler</h2>' . "\n";
    
    //Beginn des Outputs.
    $o .=   '<!-- Beginn of Anmeldeliste Output -->
            <table width="100%" border="1" cellpadding="4" cellspacing="0">   
            <tr>
            <th>' .$plugin_tx['GoRegister']['ausgabe_id']. '</th>
            <th>' .$plugin_tx['GoRegister']['ausgabe_name']. '</th>
            <th>' .$plugin_tx['GoRegister']['ausgabe_vorname']. '</th>
            <th>' .$plugin_tx['GoRegister']['ausgabe_rang']. '</th>
            <th>' .$plugin_tx['GoRegister']['ausgabe_stadt']. '</th>
            <th>' .$plugin_tx['GoRegister']['ausgabe_land']. '</th>
            <th>' .$plugin_tx['GoRegister']['ausgabe_loeschen']. '</th>
            </tr>'. "\n"; 

    //öffnen der Datei

    $datei = fopen($plugin_pth . $csvfile,"r");
    
    // Alles einlesen
    while (($data = fgetcsv ($datei, 1000, "|")) !== false ) 
    {
        $csv[] = $data;
    }
    fclose ($datei);
    
    //Ausgabe der CSV
    $count = 1;
    foreach ($csv as $row) 
    {
        // Count= ID-Zähler, [0] = Name, [1] = Vorname, [2] = Rang, [3] = Stadt, [4] = Land
        $o .= '<tr> 
                <td>' .$count. '</td>
                <td>' .$row[0]. '</td>
                <td>' .$row[1]. '</td>
                <td>' .$row[2]. '</td>
                <td>' .$row[3]. '</td>';
        //Ausgabe der Spalte Land; Mehrsprachig; mit Flagge
        $state = $row[4];
        $o .= '<td><img src="'.$plugin_pth.'img/flag/'.$plugin_tx['GoRegister']['flag_'.$state].'" /> ' .$plugin_tx['GoRegister']['land_'.$state]. '</td>
                <td><a href="?'.$su.'&deleteid='.$count.'">Löschen</td>
                </tr>'. "\n"; 
        $count = $count+1;
    }   
    $o .=   '</table
            <!-- END of Anmeldeliste Output -->
            <br />
            <h4>Neue Daten Eingeben</h4>' . "\n";

    //Formular für neue Daten
    $o .=   '<!-- Beginn of Neue Daten Eingeben -->
            <form action="'.$_GET['admin'].'" method="post">
            <table width="50%" border="1" cellpadding="4" cellspacing="0">';       
    $o .=   GoRegister_makeTextfield("last_name",$plugin_tx['GoRegister']['ausgabe_name'], ""); 
    $o .=   GoRegister_makeTextfield("name",$plugin_tx['GoRegister']['ausgabe_vorname'], "");
    $o .=   GoRegister_makeRangSelect("strength",$plugin_tx['GoRegister']['ausgabe_rang']);
    $o .=   GoRegister_makeTextfield("club",$plugin_tx['GoRegister']['ausgabe_stadt'], "");
    $o .=   GoRegister_makeTextfield("country",$plugin_tx['GoRegister']['ausgabe_land'], "");
    $o .=   '<tr>
            <td colspan="2" align="right">
            <input type="submit" value="'.$plugin_tx['GoRegister']['eingabe_senden'].'" name="buttonSubmit"/>
            <input type="reset" value="'.$plugin_tx['GoRegister']['eingabe_zuruecksetzen'].'" name="buttonReset"/> 
            </td>
            </tr>
            </table>
            </form>
            <!-- END of Neue Daten Eingeben-->
            <br />
            <br />'; // Todo: Reset funktioniert nicht!


    // TODO: Design Anpassen!
    $o .=  '<div id="divHelp">
                <h3>Neuen Spieler auswählen</h3>
                <img id="imgHelpExit" src=" '. $plugin_pth .'img/calx.gif" onclick="document.getElementById(\'divHelp\').style.display = \'none\';"> 
                <form id="helpForm_last_name">
                    <table id="helpTable_header">
                        <tr>
                            <th class="last_name">'. $plugin_tx['GoRegister']['ausgabe_name'] .'</th>
                            <th class="name">'. $plugin_tx['GoRegister']['ausgabe_vorname'] .'</th>
                            <th class="club">'. $plugin_tx['GoRegister']['ausgabe_stadt'] .'</th>
                            <th class="country">'. $plugin_tx['GoRegister']['ausgabe_land'] .'</th>
                            <th class="rank">'. $plugin_tx['GoRegister']['ausgabe_rang'] .'</th>
                        </tr>
                    </table>                
                    <table id="helpTable_last_name" name="helpTable_last_name" class="aForm_helpTables">
                    </table>
                </form>
            </div>';

    return $o;
}


function GoRegister_admin_GoRegister($csvfile) {
    // Aufrufen der Sprach-Dateien
    GLOBAL $plugin_tx;

    Global $plugin_pth;

    
    if (isset($_POST["buttonSubmit"]) && $_POST["buttonSubmit"] == "Senden")
    {
        //Eingabe, falls vorhanden, prüfen
        // Fehlerarray:
        $errors = array();
        // Eingabevariablen:
        $name = ""; $vorname = ""; $rang = ""; $stadt = ""; $land = "";  
        $keineDaten = true;
        // Eingaben konvertieren.
        $name = $_POST["last_name"];
        $vorname = $_POST["name"];
        $rang = $_POST["strength"];
        $stadt = $_POST["club"];
        $land = $_POST["country"];    
        // Eingaben prüfen.
        GoRegister_checkStringInput($name, $plugin_tx['GoRegister']['ausgabe_name'], $errors, $plugin_tx);
        GoRegister_checkStringInput($vorname, $plugin_tx['GoRegister']['ausgabe_vorname'], $errors, $plugin_tx);
        GoRegister_checkStringInput($vorname, $plugin_tx['GoRegister']['ausgabe_rang'], $errors, $plugin_tx);
        GoRegister_checkStringInput($stadt, $plugin_tx['GoRegister']['ausgabe_stadt'], $errors, $plugin_tx);
        GoRegister_checkStringInput($land, $plugin_tx['GoRegister']['ausgabe_land'], $errors, $plugin_tx);        
        $land = strtolower($land);                    
        $keineDaten = false;
        
        // CSV Datei öffenen und eingegebene Daten eintragen  ...
        if($keineDaten == false AND count($errors) == 0)
        {
            //Öffne die Datei mit Lese- und Schreibrechten, platziere den Zeiger am Ende der Datei 
            $fp = fopen($plugin_pth . $csvfile, 'a+');

            //Generierung des Eintrags in die CSV Datei.
            $list = array ($name, $vorname, $rang, $stadt, $land);
            //Eintragen in die Datei und schließen!
            fputcsv($fp, $list, '|'); 
            fclose($fp);
        }
        else 
        {
            // Fehlerausgabe-Anfang
            if($keineDaten == false AND count($errors) > 0)
            {
                $o .= 'Fehler <br />'."\n";
                $o .=  '<ul>'."\n";
                    foreach($errors as $error)
                        $o .=  '<li>' . $error . '<li>'."\n";               
                $o .=  '<ul>'."\n"; 
            }       
        }
        $inhalt = GoRegister_admin_formular_ausgabe($csvfile); 
    }
    else 
    {    
        $inhalt = GoRegister_admin_formular_ausgabe($csvfile);
    }
    $o .= $inhalt;
    return $o;
}  //End of admin_GoRegister()

/*
 * Liest den Namen der CSV-Datei ein. Dieser wird in einer Datei "settings.dat" abgespeichert.
 * 
 * Autor: Mathias Neumann (http://www.maneumann.com)
 * 20.03.2010
 */
function GoRegister_readCSVFile()
{
    //Pfad zu Stammverzeichnis
    Global $plugin_pth;

    $settingsfile = $plugin_pth . "settings.dat";
    if(file_exists($settingsfile))
    {
        $file = fopen($settingsfile, 'r');
        $filename = trim(fgets($file));
        fclose($file);
        
        if(strlen($filename) != 0 AND file_exists($plugin_pth . $filename))
            return $filename;
    }
    
    // Standard-Eingabedatei.
    return "datei.csv";
}

/**
 * Convert a comma separated file into an associated array.
 * @author Dennis Fischer
 * 
 * @param string $filename Path to the CSV file
 * @return array
 * Script based on: 
 * @link http://gist.github.com/385876
 */

function GoRegister_csv_to_array($filename='')
{
    Global $plugin_pth;

    $filename = $plugin_pth.GoRegister_readCSVFile();

    $delimiter = '|';
    if(!file_exists($filename) || !is_readable($filename))
        return FALSE;
    
    $header = array("Name", "Vorname", "Rang", "Stadt", "Land");
    $data = array();
    if (($handle = fopen($filename, 'r')) !== FALSE)
    {
        while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
        {
            $data[] = array_combine($header, $row);
        }
        fclose($handle);
    }
    return $data;
}


function GoRegister_delete_row_in_csv()
{
    Global $plugin_pth;

    $filename_old = GoRegister_readCSVFile();
    $filename_new = $filename_old.'.tmp';

    $old_file = $plugin_pth.$filename_old;
    $new_file = $plugin_pth.$filename_new;
    $handler = fopen($new_file, 'w+');

    $csv_in_array = GoRegister_csv_to_array();

    if(isset($_GET['deleteid']))
    {
        $delete_row = $_GET['deleteid'];
        $count = 1;
    }

    foreach ($csv_in_array as $fields) 
    {
        if ($delete_row != $count) 
        {
            fputcsv($handler, $fields, '|');
        }
        $count++; 
    }

    fclose($handler);
    unlink($old_file);
    rename($new_file,$old_file);
}

 
/*
 * Schreibt den Namen der CSV-Datei in die Datei "settings.dat".
 * 
 * Autor: Mathias Neumann (http://www.maneumann.com)
 * 
 */
function GoRegister_writeCSVFile($filename)
{
    //Pfad zu Stammverzeichnis
    Global $plugin_pth;

    $settingsfile = $plugin_pth . "settings.dat";
    
    $o = '<i>Aktualisiere Einstellungen in "' . $settingsfile . '".</i><br/>';
    $file = fopen($settingsfile, 'w+');
    fputs($file, $filename);
    fclose($file);        
    return $o;
}

/*
 * Erstellt eine ComboBox fuer die Auswahl der CSV-Datei.
 * 
 * Autor: Mathias Neumann (http://www.maneumann.com)
 * 20.03.2010
 */
function GoRegister_makeFileChooser($name, $desc, $val)
{
    //Pfad zu Stammverzeichnis
    Global $plugin_pth;

    // Enumerate csv files.
    $csvfiles = array(); 
    $dirh  = opendir($plugin_pth);
    while ($filename = readdir($dirh)) 
    {
        // Check extension.
        if(substr($filename, strrpos($filename , '.')+1) == 'csv') 
            $csvfiles[] = $filename;
    }
    sort($csvfiles);
    closedir($dirh);

    $o = "<th><label for=" . $name . ">" . $desc . ":</label></th>
            <td>
            <select name=".$name." id=".$name.">\n";
            foreach ($csvfiles as $filename) 
            {
                $o .= '<i>' . $filename . '</i><br/>' . "\n";
                if($val == $filename)
                    $o .= "<option selected=\"selected\">" . $filename . "</option>\r\n";
                else
                    $o .= "<option>" . $filename . "</option>\r\n";
            }
    $o .= "</select>\n";
    return $o;
}

/*
 * Angepasst von Mathias Neumann (http://www.maneumann.com)
 *
 * - Auswahl einer CSV-Datei in der Admin-Oberfläche
 *
 * 20.03.2010
 */
if(isset($GoRegister))
{
    //Pfad zu Stammverzeichnis
    Global $plugin_pth;

    $admin= isset($_POST['admin']) ? $_POST['admin'] : $_GET['admin'];
    $action= isset($_POST['action']) ? $_POST['action'] : $_GET['action'];
    
    $o.= print_plugin_admin('on');
    if($admin<>'plugin_main')
    {
        $o .= plugin_admin_common($action,$admin,$plugin);
    }
    
    // Check for csv-file.
    if(!isset($_POST["CSVfile"]) OR strlen(trim($_POST["CSVfile"])) == 0)
        $cvsfile = GoRegister_readCSVFile();
    else
    { 
        if(file_exists($plugin_pth . trim($_POST["CSVfile"])))
        {
            $cvsfile = trim($_POST["CSVfile"]);
            //$o .= '<br/><i>Gewaehlte Eingabedatei "' . $cvsfile . '".</i><br/>' . "\n";
            // Store file.
            $o .= GoRegister_writeCSVFile($cvsfile, $o);
        }
        else
        {           
            // Read back old value.
            $cvsfile = GoRegister_readCSVFile();
            $o .= '<br/><i style="color: red; font-size: 120%;">Gewaehlte Eingabedatei "' . trim($_POST["CSVfile"]) . '" existiert nicht. Wähle alte Datei "' . $cvsfile . '".</i><br/>' . "\n";
        }
        
        // Remove value from $_POST to avoid showing the invalid value.
        unset($_POST['CSVfile']);
    }
    
    if($admin=='') 
    {
        // Ausgabe der aktuell betrachteten Listendatei (zur Orientierung).
        $o .= '<br/><b>Aktuelle Liste: ' . $cvsfile . '</b>' . "\n";
        
        $o .= GoRegister_admin_GoRegister($cvsfile);
    }
 
 
    if ($admin == 'plugin_main')
    {
        $o .= '<br/>
                <h4>Quelldatei angeben<h4>
                <form action="' . htmlspecialchars($_SERVER['REQUEST_URI']) . '" method="post">
                <table width="50%" border="1" cellpadding="4" cellspacing="0">';     
        $o .= GoRegister_makeFileChooser("CSVfile", "CSV-Eingabedatei:", $cvsfile); 
        $o .= '<tr>
                <td colspan="2" align="right">
                <input type="submit" value="'.$plugin_tx['GoRegister']['eingabe_senden'].'" name="buttonSubmit"/>
                <input type="reset" value="'.$plugin_tx['GoRegister']['eingabe_zuruecksetzen'].'" name="buttonReset"/>
                </td>
                </tr>
                </table>
                $</form>'."\n";
    }
}
?>
