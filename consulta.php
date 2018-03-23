<?php
 session_start();
 include('..\lib\funciones.php');
 error_reporting(E_ALL);
//**********************************************************************************************************************
 
 //cliente a visualizar
  if (!isset($_SESSION['cliente_actual']))
  { $_SESSION['cliente_actual'] =  $_GET['CliCod'];}
  //Sucursal
   if (!isset($_SESSION['suc_actual']))
  { $_SESSION['suc_actual'] = $_GET['sucursal']; }
  
 $cliente_actual = $_SESSION['cliente_actual'];
 $suc_actual = $_SESSION['suc_actual'] ;
 //conectar a la sucursal
 $link = Conectar_SQL($suc_actual) or error('Error al conectar a la Sucursal',-1);
 
 //obtener el nombre del cliente
 $result = sqlsrv_query($link,"Select PerNom from CLPERSO where PerCod=".$cliente_actual);
 if ($row = sqlsrv_fetch_array($result))
  {
   $_SESSION['nombre_cliente_actual'] = htmlentities($row['PerNom']);
  }
//Obtener el nombre de la sucursal  
$result = sqlsrv_query($link,"Select SucDsc from STSUCUR where succod=".$suc_actual);
if ($row = sqlsrv_fetch_array($result))
{
	$_SESSION['nom_suc_actual'] = $row['SucDsc'];
}
  
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


<body>
	<div class="workarea" >
    <div class="header">
      <img src="../Imagen/logo.jpg" >
      <div class="titulo">Consulta de Cuentas Intersucursal</div>
    </div>
    <div class="menu_head">
      Ud esta consultando la sucursal : <span><?php echo $_SESSION['nom_suc_actual']; ?></span>
        <input name="home" type="button" class="boton" id="home" value="Home" onClick="location.href='home.php?user=<?php echo $_SESSION['usuario']?>&succod=<?php echo $_SESSION['sucursal']?>'">
        <input name="menu" type="button" class="boton" id="menu" value="Menu" onClick="location.href='menu.php'">
        <input name="imprimir" type="button" class="boton" id="imprimir" value="Imprimir" onClick="imprimir()">
        <input name="volver" type="button" class="boton" id="volver" value="Volver" onClick="location.href='consulta_cab.php'"> 
    </div>
    <ul>
      <li><a href="#" onClick="vercuenta()"> Cuenta </a></i>
      <li><a href="#" onClick="verpagos()"> Pagos </a></i>
      <li><a href="#" onClick="vercuotas()">Ver Cuotas </a></i>
      <li><a href="#" onClick="verdocumentos()">Documentos</a></li>
      <li><a href="#" onClick="verpunthogar()">Punthogar</a></li>
      <li><a href="#" onClick="verdatos()">Datos del Cliente</a></li>
      <li><a href="#" onClick="vercheques()">Cartera de Valores</a></li>
    </ul>
	</div>
</body>
</html>
