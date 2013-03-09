function getData()
{  
    if (document.getElementById('last_name').value.length < 3)
   { return; }
   
   // We set the coordinates of the combo-box so that it's close to the user field
   var coords = getXYpos(document.getElementById('last_name'));
   document.getElementById('divHelp').style.left = coords.x+200+'px';
   document.getElementById('divHelp').style.top  = coords.y+20+'px';
   document.getElementById('divHelp').style.position='absolute';

   var last_name = document.getElementById('last_name').value;

    //Define path to module to find the ProxyAjax
    var module_name = "GoRegister"; 
    var path = "./plugins/" + module_name;
   
   // We launch "ProxyAjax.php" in order to allow the cross-domain ajax call to the EGD script "GetPlayerDataByData.php"
   // the function "AjaxGet" has 3 parameters: (url, GET data, callback function) 
   AjaxGet(path + "/ProxyAjax.php", {'url':"http://www.europeangodatabase.eu/EGD/GetPlayerDataByData.php?lastname="+last_name},
    function (data) 
    {  if (data.retcode != 'Ok')
       {  document.getElementById('divHelp').style.display = 'none';
          return;
       }
       else
       {  if (data.players.length == 0)
          {  document.getElementById('divHelp').style.display = 'none';
             return;
          }
          document.getElementById('divHelp').style.display = 'block';
          var value     = new Object;
          var recordset = data.players;
          while (document.getElementById('helpTable_last_name').rows.length > 0)
          {   document.getElementById('helpTable_last_name').deleteRow(0);
          }
          
          // for every row of the returned recordset, we populate the combo-box
          for (var i=0; i<recordset.length; i++)
          {  line = recordset[i];
             newRow = document.getElementById('helpTable_last_name').insertRow(i);
             newRow.id = 'helpTR_'+i;
             value[newRow.id]     = line;
             newRow.onmouseover = function() { this.style.backgroundColor = '#ffff00';};
             newRow.onmouseout  = function() { this.style.backgroundColor = '';};
             // when the combo's row is clicked, we populate the form's fields (including the photo)
             newRow.onclick  = function() 
                   { document.getElementById('last_name').value = value[this.id].Last_Name;
                     document.getElementById('name').value      = value[this.id].Name;
                     document.getElementById('strength').value  = value[this.id].Grade;
                     //document.getElementById('pin').value       = value[this.id].Pin_Player;
                     document.getElementById('club').value      = value[this.id].Club;
                     document.getElementById('country').value   = value[this.id].Country_Code;
                     //document.getElementById('gor').value       = value[this.id].Gor;
                     //document.getElementById('photo').src = "http://www.europeangodatabase.eu/EGD/Actions.php?key=" + value[this.id].Pin_Player;
                     document.getElementById('divHelp').style.display = 'none';
                   }; 
             
             var newCell = newRow.insertCell(0);
             var x_field = line['Last_Name'].toUpperCase();
             var x_start = x_field.indexOf(last_name.toUpperCase());
             var y_field = line['Last_Name'];
             if (x_start >= 0) 
             {  y_field = y_field.substring(0,x_start)+'<b>'
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

// Just to get the coords of an element on screen...
function getXYpos(elem) {
   if (!elem) {
      return {"x":0,"y":0};
   }
   if (getStyle(elem,'position') == 'relative')  var xy={"x":0,"y":0}
   else                                          var xy={"x":elem.offsetLeft,"y":elem.offsetTop}
   var par=getXYpos(elem.offsetParent);
   {   for (var key in par) {
           xy[key]+=par[key];
       }
   }
   return xy;
}


// get a style attribute
function getStyle(el,styleProp)
{   var x = el;
    if (x.currentStyle)
        var y = x.currentStyle[styleProp];
    else if (window.getComputedStyle)
        var y = document.defaultView.getComputedStyle(x,null).getPropertyValue(styleProp);
    return y;
}