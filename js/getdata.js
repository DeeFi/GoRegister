function AjaxGet(url, data, callback)
{   var queryString = "";
    var n=0;
    for (i in data)
    {  if (n++>0)
       {  queryString += "&";
       }
       else
       {  queryString += "?";
       }
       queryString += i+"="+data[i];
    }
    var AJAX = null;
    if (window.XMLHttpRequest) AJAX = new XMLHttpRequest();
    else AJAX = new ActiveXObject("Microsoft.XMLHTTP");
    AJAX.onreadystatechange = function () {
         if (AJAX.readyState == 4 && AJAX.status == 200) 
         {  var data = eval('(' + AJAX.responseText + ')');
            callback(data);
         }
   }
   AJAX.open("GET", url+queryString, true);
   AJAX.send(null);
}

function getData() {

    if (document.getElementById('last_name').value.length < 3) { 
        return; 
    }

    var container = document.getElementById("formAnmeldungHelper");

    var last_name = document.getElementById('last_name').value;

    //Define path to module to find the ProxyAjax
    var module_name = "GoRegister"; 
    var path = "./plugins/" + module_name;
   
    // We launch "ProxyAjax.php" in order to allow the cross-domain ajax call to the EGD script "GetPlayerDataByData.php"
    // the function "AjaxGet" has 3 parameters: (url, GET data, callback function) 
    AjaxGet("http://www.europeangodatabase.eu/EGD/GetPlayerDataByData.php?lastname="+last_name, {},

        function (data) {  
            if (data.retcode != 'Ok') {  
                container.classList.remove("active");
                return;
            }

            else {  
                if (data.players.length == 0) {  
                    container.classList.remove("active");
                    return;
                }

                container.classList.add("active");

                var value     = new Object;
                var recordset = data.players;
              
                while (document.getElementById('helpTable_last_name').rows.length > 0) {   
                    document.getElementById('helpTable_last_name').deleteRow(0);
                }
              
                // for every row of the returned recordset, we populate the combo-box
                for (var i=0; i<recordset.length; i++) {  
                    line = recordset[i];
                    newRow = document.getElementById('helpTable_last_name').insertRow(i);
                    newRow.id = 'helpTR_'+i;
                    value[newRow.id]     = line;
                    newRow.onmouseover = function() { this.style.backgroundColor = '#ffff00';};
                    newRow.onmouseout  = function() { this.style.backgroundColor = '';};

                    // when the combo's row is clicked, we populate the form's fields
                    newRow.onclick  = function() { 

                        document.getElementById('last_name').value = value[this.id].Last_Name;
                        document.getElementById('name').value      = value[this.id].Name;
                        document.getElementById('strength').value  = value[this.id].Grade;
                        //document.getElementById('pin').value       = value[this.id].Pin_Player;
                        document.getElementById('club').value      = value[this.id].Club;
                        document.getElementById('country').value   = value[this.id].Country_Code;
                        //document.getElementById('gor').value       = value[this.id].Gor;
                        //document.getElementById('photo').src = "http://www.europeangodatabase.eu/EGD/Actions.php?key=" + value[this.id].Pin_Player;
                        
                        container.classList.remove("active");
                    }; 
             
                    var newCell = newRow.insertCell(0);
                    var x_field = line['Last_Name'].toUpperCase();
                    var x_start = x_field.indexOf(last_name.toUpperCase());
                    var y_field = line['Last_Name'];
                    if (x_start >= 0) {  
                    y_field = y_field.substring(0,x_start)+'<b>'
                    +y_field.substring(x_start,x_start+last_name.length)+'</b>'
                    +y_field.substring(x_start+last_name.length);

                    }

                newCell.innerHTML = y_field;
                newCell.className = "last_name";

                var newCell = newRow.insertCell(1);
                newCell.innerHTML = line['Name'];
                newCell.className = "name";

                var newCell = newRow.insertCell(2);
                newCell.innerHTML = line['Club'];
                newCell.className = "club";

                var newCell = newRow.insertCell(3);
                newCell.innerHTML = line['Country_Code'];
                newCell.className = "country";

                var newCell = newRow.insertCell(4);
                newCell.innerHTML = line['Grade'];
                newCell.className = "rank";
                 
                }
            }
        }
    );
}