Plugin für CMSimple
===================
GoRegister, Version 0.8

Funktionen
----------

### Frontend
- GoRegister: Ausgabe durch: `#CMSimple $output.= GoRegister_Ausgabe("dateiname.csv");#`
- GoRatinglist: Verwaltung einer Liste von Spielern, deren Namen in einer Ratingliste ausgegeben werden soll. Ausgabe durch: `#CMSimple $output.= ratinglist();#`

### Backend
- Ausgabe im Backend durch die Auswahl des Menüpunktes "GoRegister"
- Hinzufügen neuer Daten im Backend. Daten werden fortlaufend in die CSV Datei eingetragen. Es findet keine Sortierung statt.

ToDo
----
### Frontend
- Nothing yet
        
### Backend
- Add delete option to ratinglist
- Merge [GoRegister](https://github.com/DeeFi/GoRegister/),[GoRatinglist](https://github.com/DeeFi/GoRatinglist/), GoTournament

Anmerkung
---------
Einfügen und Ausgabe in eine CSV Datei
Vordefiniertes Format:
Name|Vorname|Rang|Stadt|Land

Authors
-------
**Dennis Fischer**
- Twitter: [ichderfisch](https://twitter.com/ichderfisch)
- Github: [DeeFi](https://github.com/DeeFi)

**Matthis Neumann**
- Web: [Homepage](http://www.maneumann.com)

Copyright
---------
Copyright: 2007-2013 Dennis Fischer, Matthias Neumann
