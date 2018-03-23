<?php
 session_start();
 include('..\lib\funciones.php');

 
//Sucursal actual: si no esta creada, viene de la pagina de seleccion, sino no hay que modifircarla
 if (!isset($_SESSION['suc_actual']))
  { $_SESSION['suc_actual'] = $_POST['sucursal']; }

 $suc_actual = $_SESSION['suc_actual'] ;
 //conectar a la sucursal
 $link = Conectar_SQL($suc_actual) or error('Error al conectar a la Sucursal',-1);
 
 //obtener la info de la sucursal
 $result = sqlsrv_query($link,"Select SucDsc from STSUCUR where succod=".$suc_actual);
 if ($row = sqlsrv_fetch_array($result))
  {
   $_SESSION['nom_suc_actual'] = $row['SucDsc'];
  }else{
    print_r(sqlsrv_errors());
  }

?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">    

    <title> ::: Intranet RIBEIRO CENTRAL:::</title>

    <link href="ribeiro.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../jquery/jquery-ui-1.11.0/jquery-ui.css"> 

    <script src="../jquery/jquery-1.11.1.min.js" type="text/javascript"></script>
    <script src="../jquery/jquery-ui-1.11.0/jquery-ui.js" type="text/javascript"></script>

    <script language="javascript" type="text/javascript" src="..\lib\funciones.js"></script>
  </head>
  <body>
    <div class="header">
      <img src="../Imagen/logo.jpg" >
      <div class="titulo">Consulta de Cuentas Intersucursal</div>
    </div>
    <div class="menu_head">
      <a href="home.php"><button><i class="fa fa-home"></i>Home</button></a>
      Ud esta consultando la sucursal : <span><?php echo $_SESSION['nom_suc_actual']; ?></span>
    </div>

    <div class="menu">
      <h1>Menu Principal </h1>
      <ul>
        <li><a href="consulta_cab.php"><button><i class="fa fa-address-card"></i> Consulta de Cuentas</button> </a></li>
        <li><a href="docu_fecha.php"><button> <i class="fa fa-calendar"></i> Ver Documentos por Fecha </button> </a></li>
      </ul>
    </div> 
  </body>
</html>
