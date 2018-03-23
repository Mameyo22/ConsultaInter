//Modulos JavaScript Auxiliares para la consulta

function vercuenta()
{ //muestra los datos de la cuenta
 document.getElementById('area_resultados').src = "muestra_info.php?op=1";
}


function verpagos()
{ //muestra los pagos del cliente actual
 document.getElementById('area_resultados').src = "muestra_info.php?op=2";
}

function vercuotas()
{ //muestra las cuotas del cliente actual si procesar

 document.getElementById('area_resultados').src = "muestra_info.php?op=3";
}

function verdocumentos()
{ //muestra las cuotas del cliente actual si procesar
 document.getElementById('area_resultados').src = "muestra_info.php?op=4";
}

function verpunthogar()
{ //muestra las cuotas del cliente actual si procesar

 document.getElementById('area_resultados').src = "muestra_info.php?op=5";
}
function verpunthogarh()
{ //muestra las cuotas del cliente actual si procesar

 document.getElementById('area_resultados').src = "muestra_info.php?op=6";
}
function imprimir()
{
	//imprime la pagina del iframe
  window.area_resultados.focus();
  parent.window.print();
}

function ver_doc_fecha()
{ //muestra los documentos entre las fechas desde / hasta
 var fdesde = document.getElementById('desde').value;
 var fhasta = document.getElementById('hasta').value;
 if (isDate(fdesde) && isDate(fhasta))
   {
	   document.getElementById('area_resultados').src = "muestra_info.php?op=7&fd="+fdesde+"&fh="+fhasta;
   }
}

function verdatos()
{
 document.getElementById('area_resultados').src = "muestra_info.php?op=8";
}

function vercheques()
{
 document.getElementById('area_resultados').src = "muestra_info.php?op=9";
}

//--------------------------------------------------------------------------------------------
function cerrar(idtag)
{//cierra la ventana de ver factura
 document.getElementById(idtag).style.visibility="hidden";
}

function verfactura(tipo,letra,pvt,numero)
{
 //llamar a ver factura o refin, segun corresponda
 if (tipo == 'FCE' || tipo =='FCC' || tipo=='FCO' || tipo=='NCD' || tipo=='FPH')
  { 
    //mostrar cuadro de espera
    document.getElementById('espera').style.zIndex ="2";
    document.getElementById('espera').style.visibility="visible";
     xajax_verfactura(tipo,letra,pvt,numero);
    //mostrar la factura
    document.getElementById('factura').style.zIndex ="1";
    document.getElementById('factura').style.visibility="visible";
  }
 if (tipo == 'REF')
  {	
    //mostrar cuadro de espera
    document.getElementById('espera').style.zIndex ="2";
    document.getElementById('espera').style.visibility="visible";
    xajax_verrefin(tipo,letra,pvt,numero);
    //mostrar la factura
    document.getElementById('refin').style.zIndex ="1";
    document.getElementById('refin').style.visibility="visible";
  }
 if (tipo == 'NDC' || tipo == 'NCC')
  {	
    //mostrar cuadro de espera
    document.getElementById('espera').style.zIndex ="2";
    document.getElementById('espera').style.visibility="visible";
    xajax_verndc(tipo,letra,pvt,numero);
    //mostrar la factura
    document.getElementById('ndc').style.zIndex ="1";
    document.getElementById('ndc').style.visibility="visible";
  }
  
}

function verrecibo(rectip, recnum)
{
 if (recnum > 0)
 {
 //mostrar cuadro de espera
    //mostrar cuadro de espera
    document.getElementById('espera').style.zIndex ="2";
    document.getElementById('espera').style.visibility="visible";
     xajax_verrecibo(rectip, recnum);
    //mostrar la factura
    document.getElementById('recibo').style.zIndex ="1";
    document.getElementById('recibo').style.visibility="visible";
  }   
}

function imprimir2()
{ if ((navigator.appName == "Netscape")) { window.print() ; 
} 
else
{ var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';document.body.insertAdjacentHTML('beforeEnd', WebBrowser); WebBrowser1.ExecWB(7, -1); WebBrowser1.outerHTML = "";
}
}


//-----------------------------------------------------------------------------------------------