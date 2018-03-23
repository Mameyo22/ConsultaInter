<?php
 session_start();
 include('..\lib\funciones.php');

 //Sucursal actual: si no esta creada, viene de la pagina de seleccion, sino no hay que modifircarla
 $suc_actual = $_SESSION['suc_actual'] ;
 //conectar a la sucursal
 $link = Conectar_SQL($suc_actual) or error('Error al conectar a la Sucursal',-1);

// eliminar la variable de cliente si viene de una pagina posterior
 if (isset($_SESSION['cliente_actual']))
   { 
     unset($_SESSION['cliente_actual']);
   } 

 
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
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
    <script language="javascript" type="text/javascript" src="consulta_cab.js"></script>
  </head>


  <body>
    <div class="header">
      <img src="../Imagen/logo.jpg" >
      <div class="titulo">Consulta de Cuentas Intersucursal</div>
    </div>
    <div class="menu_head">
      <button id="home"><i class="fa fa-home"></i>Home</button>
      <button id="menu"><i class="fa fa-bars"></i>Menu</button>
      Ud esta consultando la sucursal : <span><?php echo $_SESSION['nom_suc_actual']; ?></span>
    </div>
    <div class="form_busq">
        <label for="cuenta">Cuenta</label> 
        <input name="cuenta" type="text" class="busqueda_text" id="cuenta" onKeyPress="return acceptNum(event)">
        <label for="nombre">Nombre</label>
        <input name="nombre" type="text" class="busqueda_text" id="nombre">
        <label for="documento">Documento</label> 
        <input name="documento" type="text" class="busqueda_text" id="documento" onKeyPress="return acceptNum(event)"> 
        <button id="buscar"><i class ="fa fa-search" ></i> Buscar </button>
    </div>
    <div id="resultado">
    </div>
  </body>
</html>
