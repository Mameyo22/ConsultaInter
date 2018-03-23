<?php 
    session_start();
    include('..\lib\funciones.php');

    //Limpiar la variable de sesion que contiene la sucursal sobre la que estoy trabajando

    if (isset($_SESSION['suc_actual']))
    { 
        unset($_SESSION['suc_actual']);
    } 
    //rellenar el array de autocompletar con los datos de las sucursales
    $links =  conectar_SQL(0);

    $arreglo_php = array();
    $arreglo_suc = array();

    $result = sqlsrv_query($links,"SELECT SucCod, SucDsc FROM STSUCUR where SucTip='SU'" ); 
    while ($row = sqlsrv_fetch_array($result)){
        array_push($arreglo_php,array($row['SucCod'],$row['SucDsc']));
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

    <script>
        $(function(){
            var autocompletar = new Array();
            var sucursales = new Array();
            <?php
            for($p = 0;$p < count($arreglo_php); $p++){ ?>
                autocompletar.push('<?php echo $arreglo_php[$p][1]; ?>');
                sucursales.push('<?php echo $arreglo_php[$p][0]; ?>');
            <?php } ?>

            $("#buscar").autocomplete({source: autocompletar});
            $("#buscar").autocomplete({minLength: 2});    

            document.getElementById("buscar").onblur = function(){
                sucdes = $("#buscar").val();
                for(i = 0, j = autocompletar.length; i < j; i++){
                    if (autocompletar[i].toUpperCase() === sucdes.toUpperCase()) {
                        document.getElementById("sucursal").value = sucursales[i];
                    }
                }
            };

            $("#btn_suc").click(function(){
                if ($("#sucursal").val() <= 0){
                    alert("No existe la Sucursal");
                    document.getElementById("buscar").focus();
                }else{
                    document.getElementById("suc_frm").submit();
                }
            });
        });        
    </script>
</head>

<body>
  <div class="header">
    <img src="../Imagen/logo.jpg" >
    <div class="titulo">Consulta de Cuentas Intersucursal</div>
  </div>
  <div class="bloque">
    <div class="titulo">Consulta de Cuentas</div>
    <form action="menu.php" method="post" id="suc_frm">
			<h1>Seleccione la sucursal sobre la que desea trabajar</h1>
			<input type="text" id="buscar" value="Buscar Sucursal..." onfocus="if (this.value == 'Buscar Sucursal...') {this.value = '';}" onblur="if (this.value == ''){this.value = 'Buscar Sucursal...';}"  style="text-transform:uppercase;"/>    
            <input type="hidden" id="sucursal" name="sucursal" value="0" />
 	        <input name="btn_suc" type="button" id="btn_suc" value="Seleccionar" />
			<div><em> Seleccione esta opcion para consultar las cuentas de una sucursal determinada </em> </div>
		</form>
  </div>
    
  <div class="bloque">
		<div class="titulo">Consulta de Clientes Repetidos</div>
        <form action="tclisuc.php" method="post">
            <h1>Ingrese Numero de Documento/CUIT/CUIL (sin guiones) del Cliente a Consultar:</h1>
            <input name="clidoc" type="text" id="clidoc">
            <input name="btn_suc" type="submit" id="btn_suc" value="Consultar >>" >
            <div><img src="../Imagen/Admin-64.png" align="middle"></div>
            <div><em>Seleccione esta opcion para verificar si una persona es cliente en mas de una sucursal. </em>	</div>
        </form>
  </div>
</body>
</html>

