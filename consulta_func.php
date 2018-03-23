<?php
 session_start();
 include('..\lib\funciones.php');

 $accion = $_POST["accion"];
//ver a que funcion se desea llamar
switch ($accion){
    case 1 : echo "<p> ... buscando </p>";
        //Obtener los parametros
        $cuenta = $_POST["cuenta"];
        $nombre = htmlspecialchars($_POST["nombre"]);
        $documento = $_POST["documento"];

        buscar($cuenta,$nombre,$documento);
    
        break;
}


 function buscar($cuenta,$nombre,$documento)
{
    
    //devuelve el listado de cliente segun el criterio dado
    //conectar a la sucursal
    $link = Conectar_SQL($_SESSION['suc_actual']);

    //realizar la busqueda
    if ($cuenta <> ''){
        //buscar por cuenta
        $result = sqlsrv_query($link,"Select PerCod,PerNom,PerDoc,PerDom,PerNum,PerTel from CLPERSO where percod in (select clicod from coclien where clicod=percod) and percod ='".$cuenta);
        echo "busco por doc";
    }elseif ($documento <> '') {
        //buscar por docuemnto
        $result = sqlsrv_query($link,"Select PerCod,PerNom,PerDoc,PerDom,PerNum,PerTel from CLPERSO where percod in (select clicod from coclien where clicod=percod) and perdoc=".$documento);
        echo "busco por doc";
    }else{
        //buscar por nombre
        $result = sqlsrv_query($link,"Select top 10 PerCod,PerNom,PerDoc,PerDom,PerNum,PerTel from CLPERSO where percod in (select clicod from coclien where clicod=percod) and pernom like '".$nombre."%' order by PerCod");
        echo "busco por nombre";
    }
    
    
    //mostrar los resultados 
    
    /*
    while ($row = sqlsrv_fetch_array($result))
    {			  
    echo "<tr>
            <td><a href='consulta.php?CliCod=".$row['PerCod']."'>".$row['PerCod']."</a></td>
            <td><a href='consulta.php?CliCod=".$row['PerCod']."'>".htmlentities(chop($row['PerNom']))."</a></td>
            <td>".$row['PerDoc']."</td>
            <td>".htmlentities(chop($row['PerDom']))." ".htmlentities($row['PerNum'])."</td>
            <td>".htmlentities(chop($row['PerTel']))."</td>
          </tr>";
    $i++;				
    }*/				
    //echo = "</table>" ;
    
} 


 ?>