<?php
 session_start();
 include('..\lib\funciones.php');
//**********************************************************************************************************************
 
 //cliente a visualizar
  if (!isset($_SESSION['cliente_actual']))
  { $_SESSION['cliente_actual'] =  $HTTP_GET_VARS['CliCod'];}
  
 $cliente_actual = $_SESSION['cliente_actual'];
 $suc_actual = $_SESSION['suc_actual'] ;
  
  
?>



<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>::: Intranet RIBEIRO CENTRAL:::</title>
<link href="ribeiro.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="..\lib\funciones.js"></script>
<script language="javascript" type="text/javascript" src="modulos.js"></script>
</head>


<body onLoad="mueveReloj()">
<table width="760" border="0,5" align="center" cellpadding="0" cellspacing="0" id="principal">
  <tr>
    <td height="29" background="../Imagen/top1.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="73" background="../Imagen/bg.gif"><table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0" id="header">
      <tr>
        <td width="38%"><img src="../Imagen/logo.jpg"></td>
        <td width="62%"><table width="95%"  border="0" class="barra_info" id="barra_info">
          <tr>
            <td width="50%">Fecha: <b><?php echo date('d/m/Y'); ?></b></td>
            <td width="50%">Hora: <b id="reloj"></b></td>
          </tr>
          <tr>
            <td>Usuario: <b><?php echo $_SESSION['usunom']; ?></b></td>
            <td>Sucursal: <b><?php echo $_SESSION['sucnom']; ?></b></td>
          </tr>
          <tr>
            <td>Estaci&oacute;n: <b><?php echo $_SESSION['estacion']; ?></b></td>
            <td>Modo: <b><?php echo ($_SESSION['modo']=='D') ? "Desarrollo" : "Normal"; ?></b></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td background="../Imagen/bg.gif"></td>
  </tr>
  <tr>
    <td height="151" background="../Imagen/bg.gif">
	<div align="center">
	<!-- AREA DE TRABAJO -->
     <table width="95%" height="159"  border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td height="22" class="barra_info"> <div align="right">Ud esta consultando la sucursal : <span class="busqueda_fondo"><?php echo $_SESSION['nom_suc_actual']; ?></span> 
		   &nbsp; 
		   <input name="home" type="button" class="boton" id="home" value="Home" onClick="location.href='home.php?user=<?php echo $_SESSION['usuario']?>&succod=<?php echo $_SESSION['sucursal']?>'">
              <input name="menu" type="button" class="boton" id="menu" value="Menu" onClick="location.href='menu.php'">
            </div></td>
        </tr>
		<tr>
		 <td class="busqueda_fondo"> <div align="center">
		    Fecha Desde 
		       <input name="desde" type="text" class="busqueda_text" id="desde" size="15" maxlength="10"> 
		    Fecha Hasta 
		     <input name="hasta" type="text" class="busqueda_text" id="hasta" size="15" maxlength="10"> 
			 &nbsp;
		     <input name="actualizar" type="button" class="boton" id="actualizar" value="Actualizar" onClick="ver_doc_fecha()">
		   </div></td>
		</tr>
		<tr>
		 <td></td>
		</tr>
		<tr>
		 <td align="center" valign="top">
		  <table width="100%" height="117"  border="1" cellpadding="0" cellspacing="0">
            <tr>
              <td height="115" valign="top">
			   <div id="recept">
                  <div align="center">
                    <iframe width="100%" marginwidth="0" height="500" marginheight="0" scrolling="auto" frameborder="1" id="area_resultados" > </iframe>
			        </div>
			   </div>
			  </td>
            </tr>
          </table></td>
		</tr>
     </table>
	 
	</div></td>
  </tr>
  <tr>
    <td height="36" background="../Imagen/bottom1.gif">&nbsp;</td>
  </tr>
</table>
</body>
</html>
