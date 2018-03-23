<?php
 session_start();
 include ('..\lib\funciones.php');
 require('../xajax/xajax.inc.php');
 include('modulos_ajax.php'); //funciones ajax 
 
//Declaracion de prototipos Ajax

 $xajax = new xajax();
 
 //$xajax->debugOn();

 $xajax->registerFunction('verfactura');
 $xajax->registerFunction('verrefin');
 $xajax->registerFunction('verndc');
 $xajax->registerFunction('verrecibo');
 $xajax->processRequests();

//Fin AJAX

//********************************************************************************************************************************
/*
 Esta pagina es la encargada de producir una pag. html con la info solicitada, que sera incluida en el iframe de la
 pagina principal.
 */
//**********************************************************************************************************************



// PRINCIPAL

//Obtener los parametros 
 /*
Nro de Factura 

 */
 $CprPvt = $_GET['CprPvt'];
 $CprTip = $_GET['CprTip'];
 $CprLet = $_GET['CprLet'];
 $CprNum = $_GET['CprNum'];

 
 //verfactura($CprTip, $CprLet, $CprPvt, $CprNum);


 ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>::: Intranet RIBEIRO CENTRAL:::</title>
<link href="ribeiro.css" rel="stylesheet" type="text/css">
<script language="javascript" src="modulos.js" type="text/javascript"></script>
<?php  //Codigo Ajax
  $xajax-> printJavascript('../xajax/');
?>
<script language="JavaScript" type="text/JavaScript">
<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);
//-->
</script>
</head>
<?php
 echo "<body onload=verfactura('".$CprTip."','".$CprLet."',".$CprPvt.",".$CprNum.")>";
?>
<!-- Ver factura -->
<div id="factura" style=" visibility:hidden; position:absolute; left:1px; top:1px; z-index:-1;" class="boton">
<table   border="1" cellpadding="0" cellspacing="0" >
  <tr>
      <td height="20">
	    <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr class="busqueda_fondo" >
           <td width="90%" >Detalle de Factura </td>
		   <td width="7%">
		      <!--<a href="javascript:imprimir2();"> -->
	            <img src="../Imagen/printer4.jpg" alt="Imprimir" width="25" height="20" onClick="javascript:window.print();">
              <!--</a>--> 				
		   </td>
           <td width="3%" align="right" valign="top">
	        <img src="../Imagen/close.gif" alt="Cerrar" width="20" height="20" onClick="javascript:history.back(1);" onMouseOver="this.src='../Imagen/close_over.gif'"  onMouseOut="this.src='../Imagen/close.gif'"></td>
          </tr>
      </table>
	 </td>
 </tr>
 <tr>
      <td valign="top">
		<table width='100%' height='100%'  border='0' cellpadding='0' cellspacing='0' >
		<tr>
    		<td height="77" valign="top" class='busqueda_fondo'>
       <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="busqueda_fondo">
         <tr>
           <td>
		    Sucursal
            <input class='busqueda_text' name='SucCod' type='text' id='SucCod' size='5'>
            <input class='busqueda_text' name='SucNom' type='text' id='SucNom' size='50' >
			Local
			<input class='busqueda_text' name='LocCod' type='text' id='LocCod' size='5' >
			<input class='busqueda_text' name='LocNom' type='text' id='LocNom' size='30' >
		  </td>
         </tr>
         <tr>
           <td>
		    Comprobante
             <input class='busqueda_text' name='pvt' type='text' id='pvt' size='10' >
             <input class='busqueda_text' name='comp' type='text' id='comp' size='25' >
             <input class='busqueda_text' name='letra' type='text' id='letra' size='10' >
             <input class='busqueda_text' name='tipo' type='text' id='tipo' size='10' >
			Fecha
			<input class='busqueda_text' type='text' name='textfield' id='fecha'>
		  </td>
         </tr>
         <tr>
           <td>
		    Cliente
             <input class='busqueda_text' name='Clicod' type='text' id='Clicod' size='10' >
             <input class='busqueda_text' name='Clinom' type='text' id='Clinom' size='40' >			
             CUIT
			<input class='busqueda_text' name='cuit' type='text' id='cuit' >
			I.B.
			<input class='busqueda_text' name='ib' type='text' id='ib' >
			</td>
         </tr>
       </table>
    </td>
 </tr>
<!-- Fin Cabecera de Factura -->

 <tr>
    <td>
	  <div id="items">&nbsp;</div>
    </td>
</tr>
<!-- Fin items --> 
<tr>
  <td height='280'>
    <table  width='100%'  border='0' cellspacing='0' cellpadding='0'>
	 <tr>
<!-- Cuotas -->
   	    <td width='40%' valign='top'>
		 &nbsp;
         <!-- <div id="cuotas">&nbsp;</div> -->
		</td>
<!-- Fin Cuotas -->	
	  <td width='60%' align='right' >
	   <table class='totales' width="100%" border='0' cellspacing='5' cellpadding='0'>
		 <tr>
			  <td width="20%">&nbsp;</td>
			  <td width="25%">&nbsp;</td>
			  <td width="35%"><div align='right'>SUB TOTAL </div></td>
			  <td width="20%" class='busqueda_text'><div align="right" id="subtotal">&nbsp;</div></td>
		  </tr>
	  
		  <tr>
			 <td><div align='right'>IVA</div></td>
			 <td class='busqueda_text'><div align="right" id="iva">&nbsp;</div></td>
			 <td><div align='right'>IVA AD. </div></td>
			 <td class='busqueda_text'><div align="right" id="ivad">&nbsp;</div></td>
		  </tr>
			  
		  <tr>
			<td>&nbsp;</td>
			<td colspan='2'><div align='right'>INCIDENCIA INTERNOS </div></td>
			<td class='busqueda_text'><div align="right" id="ii">&nbsp;</div></td>
		  </tr>
				  
		  <tr>
			<td>&nbsp;</td>
			<td colspan='2'><div align='right'>RECARGO FINANCIERO</div></td>
			<td class='busqueda_text'><div align="right" id="rf">&nbsp;</div></td>
		 </tr>
		
		 <tr>
		   <td><div align='right'>IVA R.F.</div></td>
		   <td class='busqueda_text'><div align="right" id="ivarf">&nbsp;</div></td>
		   <td><div align='right'>IVA R.F.AD. </div></td>
		   <td class='busqueda_text'><div align="right" id="ivarfad">&nbsp;</div></td>
		 </tr>
				  
		 <tr>
			<td>&nbsp;</td>
			<td><div align='center'>&nbsp;</div></td>
			<td><div align='right'>PERCEPCIONES</div></td>
			<td class='busqueda_text'><div align="right" id="perc">&nbsp;</div></td>
		 </tr>
				  
		 <tr>
		   <td>&nbsp;</td>
		   <td><div align='center'></div></td>
		   <td><div align='right'>TOTAL</div></td>
		   <td class='busqueda_text'><div align="right" id="totalf">&nbsp;</div></td>
		</tr>
	  </table> <!-- Fin Totales !-->
	  </td>
     </tr>
<!-- Cuotas -->
     <tr>
   	    <td width='40%' valign='top'>
         <div id="cuotas">&nbsp;</div>
		</td>
	 </tr>	
<!-- Fin Cuotas -->	
	 
    </table>
  </td>
 </tr>
</table>
</td>
</tr>
</table>

<textarea name="textarea"></textarea>
</div>
		
<!-- FIN Ver Factura -->

<!-- Ver Refinaciacion -->
<div id="refin" style=" visibility:hidden; position:absolute; left:1px; top:1px; width:690px; height:400px; z-index:3;" class="boton">
  <table width="100%"  border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>
	    <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr class="busqueda_fondo">
           <td width="97%">Detalle de Refinanciaci�n </td>
           <td width="3%" align="right" valign="top">
	        <img src="../Imagen/close.gif" alt="Cerrar" width="19" height="17" onClick="javascript:history.back(1);" onMouseOver="this.src='../Imagen/close_over.gif'"  onMouseOut="this.src='../Imagen/close.gif'"></td>
          </tr>
       </table>
	  </td>
    </tr>
    <tr>
      <td colspan="10" class="cuotas">Comprobantes Cancelados </td>
	</tr>
	
	<tr>
	 <td>
 	   <div id='cancelados'></div> 
	 </td>
	</tr>	
	
	<tr>
        <td colspan="10" class="cuotas">Nota de D&eacute;bito por Refinanciaci&oacute;n </td>
    </tr>
	<tr>
	 <td>
		<div id='ndr'></div>
	 </td>	
	</tr>
	<tr>
	 <td>
		<div id='totalR' class="barra_nav"></div>
	 </td>	
	</tr>
    <tr>
       <td colspan="10" class="cuotas">Cuotas de Refinanciaci&oacute;n </td>
    </tr>
	<tr>
	 <td>
       <div id='cuotasR'></div>
	 </td>
	</tr>   
    <tr>
       <td colspan="10" class="cuotas">Pagos Efectuados </td>
    </tr>
    <tr>
	  <td>
        <div id='pagosR'></div>
      </td>
    </tr>
	<tr>
	 <td>
		<div class="barra_nav" id='saldo'></div>
	 </td>	
	</tr>
  </table>
</div>

<!-- Fin Ver Refinaciacion -->

<!-- Ver NDC/NCC -->
<div id="ndc" style=" visibility:hidden; position:absolute; left:1px; top:1px; width:690px; height:400px; z-index:4;" class="boton">
  <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="4">
	    <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr class="busqueda_fondo">
           <td width="97%">Detalle NC / ND </td>
           <td width="3%" align="right" valign="top">
	        <img src="../Imagen/close.gif" alt="Cerrar" width="19" height="17" onClick="javascript:history.back(1);" onMouseOver="this.src='../Imagen/close_over.gif'"  onMouseOut="this.src='../Imagen/close.gif'"></td>
          </tr>
       </table>
	  </td>
    </tr>
  <tr>
    <td colspan="4" class="tabla_datos">Sucursal
      <input name="NCDSucCod" type="text" class="busqueda_text" id="NCDSucCod" size="4"> <input name="NCDSucNom" type="text" class="busqueda_text" id="NCDSucNom" size="30"> 
      Local 
      <input name="NCDLocCod" type="text" class="busqueda_text" id="NCDLocCod" size="4"> <input name="NCDLocNom" type="text" class="busqueda_text" id="NCDLocNom" size="30">	</td>
  </tr>	
  <tr>
    <td colspan="4" class="tabla_datos">Comprob.
      <input name="NCDpvt" type="text" class="busqueda_text" id="NCDpvt" size="5"> <input name="NCDnum" type="text" class="busqueda_text" id="NCDnum" size="20"> <input name="NCDlet" type="text" class="busqueda_text" id="NCDlet" size="3"> <input name="NCDtip" type="text" class="busqueda_text" id="NCDtip" size="5"> <input name="NCDtipdes" type="text" class="busqueda_text" id="NCDtipdes" size="20">	</td>
  </tr>	
  <tr>
    <td colspan="4" class="tabla_datos">Cliente
      <input name="NCDCliCod" type="text" class="busqueda_text" id="NCDCliCod" size="10" style="text-align:right"> <input name="NCDCliNom" type="text" class="busqueda_text" id="NCDCliNom" size="60" >	</td>
  </tr>	
  <tr>
    <td colspan="4" class="tabla_datos">Domicilio
      <input name="NCDclidom" type="text" class="busqueda_text" id="NCDclidom" size="100">	</td>
  </tr>	
  <tr>
    <td colspan="4" class="tabla_datos">IVA
      <input name="NCDtiva" type="text" class="busqueda_text" id="NCDtiva" size="4"> <input name="NCDtivadesc" type="text" class="busqueda_text" id="NCDtivadesc" size="30"> 
      CUIT 
      <input name="NCDcuit" type="text" class="busqueda_text" id="NCDcuit" size="15"> 
      I.Brutos 
      <input name="NCDib" type="text" class="busqueda_text" id="NCDib" size="15">	</td>
  </tr>	
  <tr>
    <td colspan="4" class="tabla_datos">Tipo Doc.
      <input name="NCDtdoc" type="text" class="busqueda_text" id="NCDtdoc" size="4"> 
      <input name="NCDtdocdesc" type="text" class="busqueda_text" id="NCDtdocdesc" size="30"> 
      Docum. 
      <input name="NCDdoc" type="text" class="busqueda_text" id="NCDdoc" size="15"> 
      Contiene IVA? 
      <input name="NCDiva" type="text" class="busqueda_text" id="NCDiva" size="4"></td>
  </tr>	
  <tr>
    <td colspan="4" class="tabla_datos">Detalle 1
      <input name="NCDdet1" type="text" class="busqueda_text" id="NCDdet1" size="100">	</td>
  </tr>	
  <tr>
    <td colspan="4" class="tabla_datos">Detalle 2
      <input name="NCDdet2" type="text" class="busqueda_text" id="NCDdet2" size="100">	</td>
  </tr>	
  <tr>
    <td class="tabla_datos">Concepto 1   	  </td>
    <td class="tabla_datos"><input name="NCDcon1" type="text" class="busqueda_text" id="NCDcon1" size="50"></td>
    <td class="tabla_datos"><div align="right">Importe 1</div></td>
    <td class="tabla_datos"><div align="center">
      <input name="NCDimp1" type="text" class="busqueda_text" id="NCDimp1" size="20"  style="text-align:right">
    </div></td>
  </tr>	
  <tr>
    <td class="tabla_datos">Concepto 2   	  </td>
    <td class="tabla_datos"><input name="NCDcon2" type="text" class="busqueda_text" id="NCDcon2" size="50"></td>
    <td class="tabla_datos"><div align="right">Importe 2</div></td>
    <td class="tabla_datos"><div align="center">
      <input name="NCDimp2" type="text" class="busqueda_text" id="NCDimp2" size="20" style="text-align:right">
    </div></td>
  </tr>	
  <tr>
    <td class="tabla_datos">Concepto 3   	  </td>
    <td class="tabla_datos"><input name="NCDcon3" type="text" class="busqueda_text" id="NCDcon3" size="50"></td>
    <td class="tabla_datos"> <div align="right">Importe 3 </div></td>
    <td class="tabla_datos"><div align="center">
      <input name="NCDimp3" type="text" class="busqueda_text" id="NCDimp3" size="20" style="text-align:right">
    </div></td>
  </tr>	
  <tr>
    <td class="tabla_datos">&nbsp;
	</td>
    <td class="tabla_datos">&nbsp;</td>
    <td class="tabla_datos"><div align="right">Total Contado </div></td>
    <td class="tabla_datos"><div align="center">
      <input name="NCDtotcon" type="text" class="busqueda_text" id="NCDtotcon" size="20" style="text-align:right">
    </div></td>
  </tr>	
  <tr>
    <td class="tabla_datos">Observacion 1
	</td>
    <td class="tabla_datos"><input name="NCDobs1" type="text" class="busqueda_text" id="NCDobs1" size="50"></td>
    <td class="tabla_datos"><div align="right">Impuestos</div></td>
    <td class="tabla_datos"><div align="center">
      <input name="NCDimp" type="text" class="busqueda_text" id="NCDimp" size="20" style="text-align:right">
    </div></td>
  </tr>	
  <tr>
    <td class="tabla_datos">
	  <p>&nbsp;</p>
	  </td>
    <td class="tabla_datos">&nbsp;</td>
    <td class="tabla_datos"><div align="right">IVA 1 
          <input name="NCDpiva1" type="text" class="busqueda_text" id="NCDpiva1" size="10">
    </div></td>
    <td class="tabla_datos"><div align="center">
      <input name="NCDiva1" type="text" class="busqueda_text" id="NCDiva1" size="20" style="text-align:right">
    </div></td>
  </tr>	
  <tr>
    <td class="tabla_datos">Observacion 2
	</td>
    <td class="tabla_datos"><input name="NCDobs2" type="text" class="busqueda_text" id="NCDobs2" size="50"></td>
    <td class="tabla_datos"><div align="right">IVA 2 
          <input name="NCDpiva2" type="text" class="busqueda_text" id="NCDpiva2" size="10">
    </div></td>
    <td class="tabla_datos"><div align="center">
      <input name="NCDiva2" type="text" class="busqueda_text" id="NCDiva2" size="20" style="text-align:right">
    </div></td>
  </tr>	
  <tr>
    <td class="tabla_datos">&nbsp;
	</td>
    <td class="tabla_datos">&nbsp;</td>
    <td class="tabla_datos"><div align="right">Percepciones</div></td>
    <td class="tabla_datos"><div align="center">
      <input name="NCDper" type="text" class="busqueda_text" id="NCDper" size="20" style="text-align:right">
    </div></td>
  </tr>	
  <tr>
    <td height="22" class="tabla_datos">&nbsp;
	</td>
    <td class="tabla_datos">&nbsp;</td>
    <td class="tabla_datos"><div align="right">Total</div></td>
    <td class="tabla_datos"><div align="center">
      <input name="NCDtotal" type="text" class="busqueda_text" id="NCDtotal" size="20" style="text-align:right">
    </div></td>
  </tr>	
  </table> 
</div>
<!-- Fin Ver NDC/NCC -->

<!-- -------------------------------------------------------------------------------------- -->

<!-- Ver Recibo -->
<div id="recibo" style=" visibility:hidden; position:absolute; left:1px; top:1px; width:690px; height:300px; z-index:-1;" class="boton">
 <table width="100%" height="100%"  border="1" cellpadding="0" cellspacing="0">
  <tr>
      <td height="20">
	    <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr class="busqueda_fondo">
           <td width="97%">Detalle de Recibo </td>
            <td width="3%" align="right" valign="top">
	         <img src="../Imagen/close.gif" alt="Cerrar" width="19" height="17" onClick="javascript:history.back(1);" onMouseOver="this.src='../Imagen/close_over.gif'"  onMouseOut="this.src='../Imagen/close.gif'">
	        </td>
          </tr>
        </table>
	  </td>
  </tr>
  <tr>
   <td valign="top">
     <table width='100%' height='100%'  border='0' cellpadding='0' cellspacing='0' >
	   <tr>
    	<td height="77" valign="top" class='busqueda_fondo'>
         <table width="100%"  border="0" cellpadding="0" cellspacing="0" class="busqueda_fondo">
         <tr>
           <td>
		    Sucursal
            <input class='busqueda_text' name='suc' type='text' id='suc' size='5' >
            <input class='busqueda_text' name='snom' type='text' id='nom' size='50' >
			Local
			<input class='busqueda_text' name='lcod' type='text' id='loc' size='5' >
			<input class='busqueda_text' name='lnom' type='text' id='lnom' size='30' >
		  </td>
         </tr>
         <tr>
           <td>
		    Comprobante
             <input class='busqueda_text' name='RecTip' type='text' id='RecTip' size='10' >
             <input class='busqueda_text' name='RecNum' type='text' id='RecNum' size='25' >
			Fecha
			<input class='busqueda_text' type='text' name='fec' id='fec'>
		  </td>
         </tr>
         <tr>
           <td>
		    Usuario
             <input class='busqueda_text' name='UsuCod' type='text' id='UsuCod' size='10' >
             Caja
			<input class='busqueda_text' name='RecCaj' type='text' id='RecCaj' >
			Anulado
			<input class='busqueda_text' name='RecAnu' type='text' id='RecAnu' >
			Total
			<input class='busqueda_text' name='RecTot' type='text' id='RecTot' >
			</td>
         </tr>
       </table>
   </td>
  </tr>
<!-- Fin Cabecera de Recibo -->

<!-- Lineas de Recibo -->
  <tr>
    <td align="center" class="cuotas">Items Cobrados</td>
  </tr>  
  <tr>
    <td>
	  <div id="lineas">&nbsp;</div>
    </td>
  </tr>
<!-- Fin lineas de recibo --> 
<!-- Cuotas -->
  <tr>
    <td align="center" class="cuotas">Monedas Recibidas</td>
  </tr>  
  <tr>
    <td>
	  <div id="moneda">&nbsp;</div>
    </td>
  </tr>
 </table>
</div>
	
<!-- FIN Ver Recibo -->




<!-- DIV de Espera -->
 <div id="espera" style=" visibility:hidden; position:absolute; left:115px; top:136px; width:420px; height:25px; z-index:2; background-color: #FFFFFF; layer-background-color: #FFFFFF; border: 1px none #000000;">
   <div align="center" >
     <p>&nbsp;</p>
     <p>Aguarde por favor... <img src="../Imagen/Progreso4.gif" width="16" height="16"></p>
     <p>&nbsp;</p>
   </div>
</div>
 <!-- Fin Espera -->
 

</body> 
</html>