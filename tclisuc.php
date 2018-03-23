<?php
 session_start();
 include('..\lib\funciones.php');
 
 //Obtener parametros
 $clidoc = $_POST['clidoc'];
 
 if ($clidoc == 0 || !isset($clidoc))
  {
  	error('Debe Ingresar el Nro de Documento',1);
  }
  
  //Limpiar las variables de sesion
  unset($_SESSION['cliente_actual']);
  unset($_SESSION['suc_actual']);

  
?>

<!-- esqueleto de pagina base -->


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title> ::: Intranet RIBEIRO CENTRAL:::</title>
<link href="ribeiro.css" rel="stylesheet" type="text/css">
<script language="javascript" type="text/javascript" src="..\lib\funciones.js"></script>



</head>


<body onLoad="mueveReloj()" >
<table width="760" border="0,5" align="center" cellpadding="0" cellspacing="0" id="principal">
  <tr>
    <td height="29" background="../Imagen/top1.gif">&nbsp;</td>
  </tr>
  <tr>
    <td height="73" background="../Imagen/bg.gif"><table width="95%"  border="0" align="center" cellpadding="0" cellspacing="0" id="header">
      <tr>
        <td width="38%"><img src="../Imagen/logo.jpg" width="200" height="68"></td>
        <td width="62%"><table width="95%"  border="0" class="barra_info" id="barra_info">
          <tr>
            <td width="50%">Fecha: <b><? echo date('d/m/Y'); ?></b></td>
            <td width="50%">Hora: <b id="reloj"></b></td>
          </tr>
          <tr>
            <td>Usuario: <b><? echo $_SESSION['usunom']; ?></b></td>
            <td>Sucursal: <b><? echo $_SESSION['sucnom']; ?></b></td>
          </tr>
          <tr>
            <td>Estaci�n: <b><? echo $_SESSION['estacion']; ?></b></td>
            <td>Modo: <b><? echo ($_SESSION['modo']=='D') ? "Desarrollo" : "Normal"; ?></b></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td background="../Imagen/bg.gif"></td>
  </tr>
  <tr>
    <td height="22" background="../Imagen/bg.gif">
	<div align="center">
	<!-- AREA DE TRABAJO -->
	<?
		$link = mssql_connect($Servidor_Central,$Usuario,$Password);
		 mssql_select_db('GEXCENTRAL',$link);
		if ($clidoc != 0 ){
		$result = mssql_query("select s.SucCod, s.SucDsc, c.CliCod, p.PerNom,d.TDoDes , p.PerDoc, p.PerDom, p.PerNum, p.PerTel from COCLIEN c 
								inner join CLPERSO p on c.Sucursal=p.Sucursal and c.CliCod = p.PerCod
								inner join STSUCUR s on c.Sucursal = s.SucCod
								inner join SITIDOC d on p.TDoCod = d.TDoCod
								where p.PerDoc = ".$clidoc."
								or p.PerCUIL = '".$clidoc."'
								or C.CliCUIT = '".$clidoc."'
								");
	?>
	<div align="center"><input name="home" type="button" class="boton" id="home" value="Home" onClick="location.href='home.php?user=<? echo $_SESSION['usuario']?>&succod=<? echo $_SESSION['sucursal']?>'"></div>
	<br>
	
	<table width='95%'  cellpadding='5' cellspacing='0' class='tabla_empleados'>
          <th>Sucursal</th>
          <th>Nro. Cliente</th>
          <th>Nombre Cliente</th>
          <th>Tipo Doc.</th>
          <th>Numero Doc.</th>
          <th>Calle</th>
          <th>Nro</th>
          <th>Telefono</th>
		  <th>Ver</th>
	<?
	while ($row = mssql_fetch_array($result))
	{
		echo "<tr>";
		echo "<td><div align=left>".$row['SucDsc']."</div></td>";
		echo "<td><div align=right>".$row['CliCod']."</div></td>";
		echo "<td><div align=left>".htmlentities(chop($row['PerNom']))."</div></td>";
		echo "<td><div align=left>".$row['TDoDes']."</div></td>";
		echo "<td><div align=right>".$row['PerDoc']."</div></td>";
		echo "<td><div align=left>".htmlentities(chop($row['PerDom']))."</div></td>";
		echo "<td><div align=right>".$row['PerNum']."</div></td>";
		echo "<td><div align=right>".$row['PerTel']."</div></td>";
		echo "<td><a href='consulta.php?CliCod=".$row['CliCod']."&sucursal=".$row['SucCod']."'>	<img src='../Imagen/flecha.gif'> </a></td>";
	}
	} //if
	?>

	</table>
	<!-- FIN  AREA DE TRABAJO -->
	</div></td>
  </tr>
  <tr>
    <td height="36" background="../Imagen/bottom1.gif">&nbsp;</td>
  </tr>
</table>
    <div align="center"> <p> v201609</p> </div>    
</body>
</html>
