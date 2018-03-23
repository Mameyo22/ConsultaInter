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
// FUNCIONES

function consulta()
{
 //aumentar el tiempo de ejecucion
 set_time_limit(480);
 //inicializar totalizadores
 $Avencer = 0;
 $Vencido = 0;
 $Punitorios = 0;
 $cuota_mens = 0 ;
 $ChRe = 0.00;     //Cheques Rechazados
 $TaRe = 0;     //Tarjetas Rechazadas
 $ChDe = 0;     //Cheques a Depositar
 $SubTD = 0;    //Subtotal Deuda
 $SubTSal = 0;  //Subtotal Saldo
 $Total = 0;    //Total
 
 
 
 $link = Conectar_SQL($_SESSION['suc_actual']);
 //obtener los coeficientes de los punitorios
 $res_pun = sqlsrv_query($link,"Select CfgPunTol, CfgPunTas, CfgPunMin from CFGCPR");
 if (!$rowpun = sqlsrv_fetch_array($res_pun, SQLSRV_FETCH_ASSOC))
  {
    echo  '<p align=center> ATENCION: No se pudo obtener la tasa de punitorios.</p>';
  }

 // Obtener la info de las cuotas pendientes
 $result = sqlsrv_query($link,"select CCtTip,CCtLet,CCtPVt,CCtCom,CCtCuo,CONVERT(varchar,CCtFVC,112)as CCtFVC,CCtSal,MonCod from FaCCCli 
                        where Clicod=".$_SESSION['cliente_actual'].
                        " and (CCtEst='P' or CCtEst='R') order by CCtFVC");
 
 //armar la tabla
 echo  "<table width='100%'  cellpadding='4' cellspacing='0' id='titulo'>
                   <tr>
                     <td align='center'> Sucursal: ".$_SESSION['suc_actual'].'-'.$_SESSION['nom_suc_actual']." </td>
                     <td align='center'> Consulta de Cuenta: ".$_SESSION['cliente_actual'].'-'.$_SESSION['nombre_cliente_actual']." </td>
		   </tr>
	</table>";
				
//obtener los datos de la cuenta del empleado
 $res_cue = sqlsrv_query($link,"SELECT S.CreDes, C.CliObs, ISNULL(A.AgeNom,''), E.ClEsDsc, I.IntDes, P.PerIng, C.CliMonOto,U.GedEstDes
 FROM 
	COCLIEN C
	LEFT JOIN CLPERSO P ON C.CliCod = P.PerCod
	LEFT JOIN SICREDI S ON C.CreCod = S.CreCod
	LEFT JOIN CAGENTE A ON C.AgeCod = A.AgeCod
	LEFT JOIN COCLIEST E ON C.ClEsCod = E.ClEsCod
	LEFT JOIN SIINTER I ON C.IntCod = I.IntCod
	LEFT JOIN GEDEST U ON U.GedEstCod = P.GedEstCod
    WHERE C.CliCod=".$_SESSION['cliente_actual']);

 $row_cue = sqlsrv_fetch_array($res_cue, SQLSRV_FETCH_ASSOC);
 
 
 //Obtener saldo
 $res_saldo = sqlsrv_query($link,"SELECT TOP 1 MCLMONTO FROM MONPORCL WHERE MCLCLICOD = ".$_SESSION['cliente_actual']." ORDER BY MCLFECHA DESC");
 $row_saldo = sqlsrv_fetch_array($res_saldo , SQLSRV_FETCH_ASSOC);
 
 $SaldoCred = $row_saldo['MCLMONTO'];
 
 //armo la tabla
 echo  "<table width='100%'  cellpadding='4' cellspacing='0' class='tabla_datos'>";
 /* Eliminado a pedido de Angel Britez (Auditoria)
 echo "<tr>
        <td >Cliente</td>
        <td class='busqueda_text'>".$_SESSION['cliente_actual']."</td>
        <td class='busqueda_text'>".htmlentities($_SESSION['nombre_cliente_actual'])."</td>
     </tr>";
*/     
echo "<tr>
        <td>Estado</td>
        <td class='busqueda_text'>".$row_cue['ClEsDsc']."</td>
        <td>&nbsp</td>
        <td >Sueldo</td>
        <td class='busqueda_text' align=right>".number_format($row_cue['PerIng'],2,'.','')."</td>
    </tr>";
 echo "<tr>
        <td>Cheques Rechazados</td>
        <td class='busqueda_text'><div id=chqre align=right></div></td>
        <td>&nbsp;</td>
        <td>Cuota mens.</td>
        <td class='busqueda_text'><div id=cume align=right></td>
     </tr>";
 echo"<tr>
        <td>Cupones Rechazados</td>
        <td class='busqueda_text'><div id='tarre' align='right'></div></td>
        <td>&nbsp</td>
        <td>Monto Otorgado</td>
        <td class='busqueda_text' align=right>".number_format($row_cue['CliMonOto'],2,'.','')."</td>
     </tr>";
 echo"<tr>
        <td>Cuotas Vencidas</td>
        <td class='busqueda_text'><div id='cuove' align='right'></div></td>
     </tr>";
 echo"<tr>
        <td>Punitorios</td>
        <td class='busqueda_text'><div id='punit' align='right'></div></td>
     </tr>";
 echo "<tr>
     <td id='titulo'>SubTotal Deuda</td>
      <td class='busqueda_text'><div id='subtd' align='right'></div></td>
      <td></td>
      <td>Credito</td>
      <td class='busqueda_text'>".htmlentities($row_cue['CreDes'])."</td>
     </tr>";
 echo"<tr>
        <td>Cuotas a Vencer</td>
        <td class='busqueda_text'><div id='cuoave' align='right'></div></td>
        <td></td>
        <td>Interdiccion</td>
        <td class='busqueda_text'>".$row_cue['IntDes']."</td>
     </tr>";
 echo"<tr>
        <td>Cheques a Depositar</td>
        <td class='busqueda_text'><div id='chqde' align='right'></div></td>
        <td></td>
        <td>Saldo de Cred.</td>
        <td class='busqueda_text' align=right>".number_format($SaldoCred,2,'.','')."</td>
     </tr>";
 echo"<tr>
        <td id='titulo'>SubTotal Saldo</td>
        <td class='busqueda_text'><div id='subtsal' align='right'></div></td>
	<td></td>
	<td id='titulo'>Estudio </td>
	<td class='busqueda_text'><div id='estudio' align='right'>".htmlentities($row_cue['GedEstDes'])."</div></td>
     </tr>";
 echo "<tr>
     <td id='titulo'>Total</td>
      <td class='busqueda_text'><div id='total' align='right'></div></td>
     </tr>";
 echo "<tr>
        <td colspan=5 ><textarea name='textarea' class='busqueda_text' style='width:100%; height:100%;' valign=top> ".htmlentities($row_cue['CliObs'])."</textarea></td>    
     </tr>";    
 echo "</table>";

 //Detalle de Cuotas
 echo "<table width='100%' cellpadding='2' cellspacing='0' class='tabla_datos'>
        <tr>
            <td colspan=10 id='titulo' align='center'>Detalle de Cuotas</td>
        </tr>
                   <tr>
                     <td class='busqueda_fondo'><div align='center'>Tipo</div></td>
                     <td class='busqueda_fondo'><div align='center'>P.V.</div></td>
                     <td class='busqueda_fondo'><div align='center'>Letra</div></td>
                     <td class='busqueda_fondo'><div align='center'>Comprobante</div></td>
                     <td class='busqueda_fondo'><div align='center'>Cuota</div></td>
                     <td class='busqueda_fondo'><div align='center'>Vence</div></td>
                     <td class='busqueda_fondo'><div align='center'>Saldo</div></td>
                     <td class='busqueda_fondo'><div align='center'>Vencio</div></td>
                     <td class='busqueda_fondo'><div align='center'>Dias Venc.</div></td>
                     <td class='busqueda_fondo'><div align='center'>Punitorios</div></td>
                     <<td class='busqueda_fondo'><div align='center'>Ver</div></td>
				   </tr>";
   // recorrer los comprobantes
  while ($row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC))
  {
        //Nuevo manejo de fechas
        $fvc = Convertir_Fecha_ISO($row['CCtFVC']);
	$pun = 0;
 	$diasV = Dias($fvc);
        

	echo  " <tr class='tabla_datos'>
                     <td><div align='left'>".$row['CCtTip']."</div></td>
                     <td><div align='right'>".$row['CCtPVt']."</div></td>
                     <td><div align='center'>".$row['CCtLet']."</div></td>
                     <td><div align='right'>".$row['CCtCom']."</div></td>
                     <td><div align='center'>".$row['CCtCuo']."</div></td>
                     <td><div align='right'>".$fvc."</div></td>
                     <td><div align='right'>".number_format($row['CCtSal'],2,'.','')."</div></td>";
	 //vencido
	 echo "<td><div align='center'>".(($diasV > 0)? "Si" : "No" )."</div></td>"; 
	 //Dias vencidos
	 echo "<td><div align='center'>".(($diasV > 0)? $diasV : 0 )."</div></td>"; 
	 //Punitorios
         $pun = punitorios($row['CCtSal'],$diasV,$rowpun['CfgPunTol'],$rowpun['CfgPunTas'],$rowpun['CfgPunMin']);
	 echo "<td><div align='right'>".number_format($pun,2,'.','')."</div></td>";
   echo "<td><div align='center'><a href='VerImprime.php?CprTip=".$row['CCtTip']."&CprLet=".$row['CCtLet']."&CprPvt=".$row['CCtPVt']."&CprNum=".$row['CCtCom']."'><img src='../Imagen/flecha.gif' /> </a></div></td>";
	 echo  "</tr>";

   //actualizar los totalizadores
   $Avencer += (($diasV > 0)? 0 : $row['CCtSal']);
   $Vencido += (($diasV > 0)? $row['CCtSal'] : 0);
	 $Punitorios += $pun;
	 //Calcular la cuota mensual.
     //Expando la fecha de vto de la cuota
	 $fecha = explode('/',$fvc);
   //Obtengo la fecha actual
   $fechaactual = getdate();
    //Obtengo Año y mes
    $anio = $fechaactual['year'];
    $mes = $fechaactual['mon'];
    if ($mes == 12)
    {
      //Aumento un año
      $mes = 1;
      $anio +=1;
    }else{
      //Aumento un mes
      $mes += 1;
    }
	 if ( ($fecha[1]  == $mes) && ($fecha[2]  == $anio ) )
	  {
	   $cuota_mens += $row['CCtSal'];
	  }
	 
	 
 }

 echo  "</table>";
 //Detalle de Cheques Rechazados y a Depositar
 $res_che = sqlsrv_query($link,'select C.CheNro, B.BanDes,  C.CheImp, E.CheEstDsc, C.CheEstCod from CAJCHE C
	INNER JOIN CHEESTAD E ON C.CheEstCod=E.CheEstCod
	INNER JOIN SIBANCO B ON C.CheBcoCod = B.BanCod
 	WHERE C.CheCliCod ='.$_SESSION['cliente_actual'].'  AND C.CheEstCod IN (5,7,15,16)',array(), array('Scrollable' => 'buffered'));
 
 if (sqlsrv_num_rows($res_che) > 0){
    echo "<table width='100%' cellpadding='2' cellspacing='0' class='tabla_datos'>
           <tr>
               <td colspan=4 id='titulo' align='center'>Detalle de Cheques Rechazados y a Depositar</td>
           </tr>
           <tr>
             <td class='busqueda_fondo'><div align='center'>Nro. Cheque</div></td>
             <td class='busqueda_fondo'><div align='center'>Banco</div></td>
             <td class='busqueda_fondo'><div align='center'>Importe</div></td>
             <td class='busqueda_fondo'><div align='center'>Estado</div></td>
           </tr>";

    //Recorrer el resultado
    while ($row = sqlsrv_fetch_array($res_che , SQLSRV_FETCH_ASSOC))
    {
        echo "<tr>";
        echo "<td>".$row['CheNro']."</td>";
        echo "<td>".htmlentities($row['BanDes'])."</td>";
        echo "<td>".number_format($row['CheImp'],2, '.','')."</td>";
        echo "<td>".htmlentities($row['CheEstDsc'])."</td>";
        echo "</tr>";
        
        if ($row['CheEstCod'] == 5){
            $ChDe += $row['CheImp'];        //Cheque a Depositar
        }
        else {    
            $ChRe += $row['CheImp'];        //Rechazado, Gestion Judicial, Incobrable
        }
    }
    echo "</table>"; //Cheques
 }
 
 echo  "</table>";
 //Detalle de Tarjetas Rechazadas
 $res_tar = sqlsrv_query($link,"SELECT T.CJTLote, T.CJTCupon,T.CJTNTarj,  T.CJTImporte, E.TJEDsc FROM CAJTARJ T
	INNER JOIN TARJESTA E ON T.TJECod = E.TJECod
	WHERE T.CliCod = ".$_SESSION['cliente_actual']." AND
	 E.TJECod IN (2,3,4)",array(), array('Scrollable' => 'buffered'));
 
 if (sqlsrv_num_rows($res_tar) > 0){
    echo "<table width='100%' cellpadding='2' cellspacing='0' class='tabla_datos'>
           <tr>
               <td colspan=4 id='titulo' align='center'>Detalle de Tarjetas Rechazadas</td>
           </tr>
           <tr>
             <td class='busqueda_fondo'><div align='center'>Lote</div></td>
             <td class='busqueda_fondo'><div align='center'>Cupon</div></td>
             <td class='busqueda_fondo'><div align='center'>Num. Tarjeta</div></td>
             <td class='busqueda_fondo'><div align='center'>Importe</div></td>
             <td class='busqueda_fondo'><div align='center'>Estado</div></td>
           </tr>";

    //Recorrer el resultado
    while ($row = sqlsrv_fetch_array($res_tar  , SQLSRV_FETCH_ASSOC))
    {
        echo "<tr>";
        echo "<td>".$row['CJTLote']."</td>";
        echo "<td>".$row['CJTCupon']."</td>";
        echo "<td>".htmlentities($row['CJTNTarj'])."</td>";
        echo "<td>".number_format($row['CJTImporte'],2, '.','')."</td>";
        echo "<td>".htmlentities($row['TJEDsc'])."</td>";
        echo "</tr>";
        
        $TaRe += $row['CJTImporte'];        //Rechazado
    }
    echo "</table>"; //Cheques
 }
 
 //Totalizar
 $SubTD = $ChRe + $TaRe + $Vencido + $Punitorios;
 $SubTSal = $Avencer + $ChDe;
 
 $Total = $SubTD + $SubTSal;
 
 //Informacion en cabecera ------------------------------------------------------
 //mostrar la cuota mensual
 echo "<script language='javascript' type='text/javascript'>";
 echo "document.getElementById('cume').innerHTML = ".number_format($cuota_mens,2,'.','').";";
//Vencido y a Vencer
 echo "document.getElementById('cuove').innerHTML = ".number_format($Vencido,2,'.','').";";
 echo "document.getElementById('cuoave').innerHTML = ".number_format($Avencer,2,'.','').";";
 //punitorios
 echo "document.getElementById('punit').innerHTML = ".number_format($Punitorios,2,'.','').";";
 //Cheques
 echo "document.getElementById('chqre').innerHTML = ".number_format($ChRe,2,'.','').";";
 echo "document.getElementById('chqde').innerHTML = ".number_format($ChDe,2,'.','').";";
 //Tarjetas Rech
 echo "document.getElementById('tarre').innerHTML = ".number_format($TaRe,2,'.','').";";
 //Totales
 echo "document.getElementById('subtd').innerHTML = ".number_format($SubTD,2,'.','').";";
 echo "document.getElementById('subtsal').innerHTML = ".number_format($SubTSal,2,'.','').";";
 echo "document.getElementById('total').innerHTML = ".number_format($Total,2,'.','').";";
 echo "</script>";

 
}
//***********************************************************************
function verpagos()
{
 //aumentar el tiempo de ejecucion
 set_time_limit(480);
 //inicializar totalizadores
 $R_10 = 0;
 $R_11 = 0;
 $R_26 = 0;
 $R_61 = 0;
 $R_90 = 0;
 $total = 0;
 
 $link = Conectar_SQL($_SESSION['suc_actual']);

 // Obtener la info de la cuenta
 $result = sqlsrv_query($link," Select CONVERT(varchar,CCtFVC,112)as CCtFVC,CCtTip, CCtPVt, CCtLet,CCtCom,CCtCuo,CONVERT(varchar,CCtFCo,112)as CCtFCo,CCtImp,(CCtImp-CCtSal) as Pagado, CCtUIt 
 from FACCCLI 
 where (CCttip <> 'NDG' and CCtTip <> 'NCD' and CCtTip <> 'NCC' and CCtTip <> 'NDC' and CCtTip <> 'NDR' and CCtTip <> 'NCR' and CCtTip <> 'NCP' and CCtTip <> 'NDP' and CCtTip <> 'NDI' and CCtTip <> 'NCI') 
 and (CctEst='C' or CctEst='R' or CctEst='F') 
 and CCtIte=0  
 and CCtCuo<>0
 and Clicod=".$_SESSION['cliente_actual']." order by CCtFVC");
 
 //armar la tabla
echo "<table width=100%  border=0 cellspacing=0 cellpadding=0 id=titulo>
     <tr>
      <td align='center'> Sucursal: ".$_SESSION['suc_actual'].'-'.$_SESSION['nom_suc_actual']." </td>
      <td align='center'>Ver Pagos : ".$_SESSION['cliente_actual'].'-'.$_SESSION['nombre_cliente_actual']."</td>
     </tr>";
     
	  
echo "</table></td>";

 
echo "<tr><td>
      <table width='97%' cellpadding='2' cellspacing='0' class='tabla_datos'>
              <tr>
                     <td class='busqueda_fondo'><div align='center'>Fecha</div></td>
                     <td class='busqueda_fondo'><div align='center'>Tipo</div></td>
                     <td class='busqueda_fondo'><div align='center'>P.V.</div></td>
                     <td class='busqueda_fondo'><div align='center'>Comprobante</div></td>
                     <td class='busqueda_fondo'><div align='center'>Cuota</div></td>
                     <td class='busqueda_fondo'><div align='center'>Importe</div></td>
                     <td class='busqueda_fondo'><div align='center'>Pagado</div></td>
                     <td class='busqueda_fondo'><div align='center'>En Fecha</div></td>
                     <td class='busqueda_fondo'><div align='center'>Dias</div></td>
             </tr>";
   // recorrer los comprobantes
  while ($row = sqlsrv_fetch_array($result  , SQLSRV_FETCH_ASSOC))
  {
    $fvc = Convertir_Fecha_ISO($row['CCtFVC']);
	
	$Visualizar = 'S';/* add -001-Variable que me dice si se visualiza o no la informacion en pantalla */		
	
	//Obtener la usltima fecha de pago de cada cuota
	//Y verificar si se trata de un pago o de una aplicacion manual
/*	$res_cuo = sqlsrv_query($link,"select CCtFCo, Rec2Num
	                         from FACCCLI inner join CBRECLI 
									   ON FACCCLI.SucCod = CBRECLI.SucCod 
      									  AND FACCCLI.CCtCRTip = CBRECLI.RecTip 
      									  AND FACCCLI.CCtCRCom = CBRECLI.RecNum
							  where  Clicod=".$_SESSION['cliente_actual']." and ccttip='".$row['CCtTip']."' and CCtPVt=".$row['CCtPVt']." and CCtLet='".$row['CCtLet']."'
						    and CCtCom=".$row['CCtCom']." and CCtCuo=".$row['CCtCuo']." and cctite=".$row['CCtUIt']);
*/   
    /*{ add -001- verifico si es una Aplicacion Manual*/
	$res_Aplic = sqlsrv_query($link,"select Rec2Num
	                         from FACCCLI inner join CBRECLI 
									   ON FACCCLI.SucCod = CBRECLI.SucCod 
      									  AND FACCCLI.CCtCRTip = CBRECLI.RecTip 
      									  AND FACCCLI.CCtCRCom = CBRECLI.RecNum
							  where  Clicod=".$_SESSION['cliente_actual']." and ccttip='".$row['CCtTip']."' and CCtPVt=".$row['CCtPVt']." and CCtLet='".$row['CCtLet']."'
						    and CCtCom=".$row['CCtCom']." and CCtCuo=".$row['CCtCuo']." and cctite=".$row['CCtUIt']);
	$linea = sqlsrv_fetch_array($res_Aplic  , SQLSRV_FETCH_ASSOC);	
	if 	($linea['Rec2Num'] > 0)	
	{
		$apm = "S";
		$Visualizar = 'N';/*Variable que me dice si se visualiza o no la informacion en pantalla */		
	}	
	else
	{
	/*} add -001-*/
		$apm = "N";	
		$Visualizar = 'S';/*Variable que me dice si se visualiza o no la informacion en pantalla */	
	}/* add -001-*/
	
/* { add -001- Busco si es una aplicacion de una REF*/
	If ($Visualizar == 'S')
	{
		$res_Ref = sqlsrv_query($link,"select CCtCRTip,CONVERT(varchar,CCtFVC,112)as CCtFVC, CONVERT(varchar,CCtFMo,112)as CCtFMo
								 from FACCCLI 
								  where  Clicod=".$_SESSION['cliente_actual']." and ccttip='".$row['CCtTip']."' and CCtPVt=".$row['CCtPVt']." and CCtLet='".$row['CCtLet']."'
								and CCtCom=".$row['CCtCom']." and CCtCuo=".$row['CCtCuo']." and cctite=".$row['CCtUIt']);
		$linea = sqlsrv_fetch_array($res_Ref , SQLSRV_FETCH_ASSOC);					
		if 	($linea['CCtCRTip'] == 'REF'){	
      if(strcmp($row['CCtFVC'],$linea['CCtFMo']) < 0){
				$Ref = 'S';
				$Visualizar = 'S';
			}
			else			{
				if	(strcmp($row['CCtFVC'],$linea['CCtFMo']) == 0){
  				$Ref = 'S';
	  			$Visualizar = 'S';				
				}
				else{
		  		$Ref = 'N';
			  	$Visualizar = 'N';
				}
      }				
		}
    else {
      $Ref = 'N';
    }	
	}/*fin de visualizar*/	
	/* } add -001- */
	
	$res_cuo = sqlsrv_query($link,"select CONVERT(varchar,CCtFCo,112)as CCtFCo
	                         from FACCCLI 
							  where  Clicod=".$_SESSION['cliente_actual']." and ccttip='".$row['CCtTip']."' and CCtPVt=".$row['CCtPVt']." and CCtLet='".$row['CCtLet']."'
						    and CCtCom=".$row['CCtCom']." and CCtCuo=".$row['CCtCuo']." and cctite=".$row['CCtUIt']);
	if ($row_cuo = sqlsrv_fetch_array($res_cuo , SQLSRV_FETCH_ASSOC))
	{	
	  $fco = Convertir_Fecha_ISO($row_cuo['CCtFCo']); 
	}
	else
	{
    $fco = $fvc; 
  }
	if ($Ref == 'S')
	{
    $fco = Convertir_Fecha_ISO($linea['CCtFMo']);
  }	
	// verifico si la cuota a procesar es el anticipo, si es anticipo no se toma en cuenta   
	
	
	   
	//Calcular los dias de vencido, si se trata de una cuota
	// del -001-if ($apm == "N")
	
	
	if ($Visualizar == "S")// add -001-
 	  { $diasV = Dias($fvc,$fco);}
	else
	  { $diasV = 0; }  
	   
							
	if ($Visualizar == 'S') //add -001-
	{	
		echo  "<tr class='tabla_datos'>
				<a href='VerImprime.php?CprTip=".$row['CCtTip']."&CprLet=".$row['CCtLet']."&CprPvt=".$row['CCtPVt']."&CprNum=".$row['CCtCom']."'>
					 <td><div align='right'>". $fvc."</div></td>
						 <td><div align='right'>".$row['CCtTip']."</div></td>
						 <td><div align='right'>".$row['CCtPVt']."</div></td>
						 <td><div align='right'>".$row['CCtCom']."</div></td>
						 <td><div align='right'>".$row['CCtCuo']."</div></td>
						 <td><div align='right'>".number_format($row['CCtImp'],2)."</div></td>
						 <td><div align='right'>".number_format($row['Pagado'],2)."</div></td>
						 <td><div align='right'>".$fco."</div></td>";
		if ($apm == "N") 				 
			 { echo "<td><div align='left'>".(($diasV < 0)? 'ADEL' : $diasV )."</div></td>";}
		else
			 { echo "<td><div align='left'>Ap.Ma</div></td>";}
						
		echo " </a></tr>";
	 
		//actualizar los totalizadores
	    // del -001-if ($apm == "N")
		if ($Visualizar == 'S')
		{
			if ($diasV <=10 ) { $R_10 += 1;}	 
			if ($diasV >=11 and $diasV <=25) { $R_11 += 1;}	 
			if ($diasV >=26 and $diasV <=60) { $R_26 += 1;}	 
			if ($diasV >=61 and $diasV <=90) { $R_61 += 1;}	 
			if ($diasV >=91 ) { $R_90 += 1;}	 
			$total += 1;
		}	
	}
	//add-001- FIN DE VISUALIZAR
 }
 echo  "</table></td></tr>";
//Resultados
 echo "<tr><td height=51>
	   <table width=60% align=center cellpadding=0 cellspacing=0 border=1>
        <tr class=busqueda_fondo>
        <td><div align=center>Adel. a 10 </div></td>
        <td><div align=center>11 a 25 </div></td>
        <td><div align=center>26 a 60 </div></td>
        <td><div align=center>61 a 90 </div></td>
        <td><div align=center>M&aacute;s de 90 </div></td>
      </tr>";
echo "<tr class=busqueda_text>
        <td><div align=right>".$R_10."</div></td>
        <td><div align=right>".$R_11."</div></td>
        <td><div align=right>".$R_26."</div></td>
        <td><div align=right>".$R_61."</div></td>
        <td><div align=right>".$R_90."</div></td>
      </tr>";
echo "<tr class=busqueda_text>
        <td><div align=right>".(($total >0)? number_format($R_10*100/$total,2) : 0)." %</div></td>
        <td><div align=right>".(($total >0)? number_format($R_11*100/$total,2) : 0)." %</div></td>
        <td><div align=right>".(($total >0)? number_format($R_26*100/$total,2) : 0)." %</div></td>
        <td><div align=right>".(($total >0)? number_format($R_61*100/$total,2) : 0)." %</div></td>
        <td><div align=right>".(($total >0)? number_format($R_90*100/$total,2) : 0)." %</div></td>
      </tr>";
 
 echo "</table>";
 //mostrar los comprobantes					
}
//***********************************************************************
//***********************************************************************
function verdocumentos()
{ //muestra las facturas de un cliente
 //aumentar el tiempo de ejecucion
 set_time_limit(480);
 $link = Conectar_SQL($_SESSION['suc_actual']);

 // Obtener la info de la cuenta
 $result = sqlsrv_query($link,"select CONVERT(varchar,CprFec,112)as CprFec, SucCod, LCoCod, CprPVt, CprTip, CprLet, CprNum, CprGTot, CprCla 
                        from fafccca 
                        where Clicod=".$_SESSION['cliente_actual']."
						order by CprFec");
 
 //armar la tabla
 echo  "<table width='97%'  cellpadding='4' cellspacing='0' id='titulo'>
                   <tr>
                     <td align='center'> Sucursal: ".$_SESSION['suc_actual'].'-'.$_SESSION['nom_suc_actual']." </td>
                     <td align='center'> Documentos del Cliente ".$_SESSION['cliente_actual'].'-'.$_SESSION['nombre_cliente_actual']."</td>
					</tr>
				</table>";
 echo  "      <table width='97%' cellpadding='2' cellspacing='0' class='tabla_datos'>
                   <tr>
                     <td class='busqueda_fondo'><div align='center'>Fecha</div></td>
                     <td class='busqueda_fondo'><div align='center'>Tipo</div></td>
                     <td class='busqueda_fondo'><div align='center'>Suc</div></td>
                     <td class='busqueda_fondo'><div align='center'>Loc</div></td>
                     <td class='busqueda_fondo'><div align='center'>P.V.</div></td>
                     <td class='busqueda_fondo'><div align='center'>Letra</div></td>
                     <td class='busqueda_fondo'><div align='center'>Numero</div></td>
                     <td class='busqueda_fondo'><div align='center'>Mon</div></td>
                     <td class='busqueda_fondo'><div align='center'>Total</div></td>
                     <td class='busqueda_fondo'><div align='center'>Clas.</div></td>
                   </tr>";
   // recorrer los comprobantes
  while ($row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC))
  {

    echo  "<tr class='tabla_datos'>
	                 <a href='VerImprime.php?CprTip=".$row['CprTip']."&CprLet=".$row['CprLet']."&CprPvt=".$row['CprPVt']."&CprNum=".$row['CprNum']."'>
	                 <td><div align='right'>".  Convertir_Fecha_ISO($row['CprFec'])."</div></td>
                     <td><div align='left'>".$row['CprTip']."</div></td>
                     <td><div align='center'>".$row['SucCod']."</div></td>
                     <td><div align='center'>".$row['LCoCod']."</div></td>
                     <td><div align='right'>".$row['CprPVt']."</div></td>
                     <td><div align='center'>".$row['CprLet']."</div></td>
                     <td><div align='left'>".$row['CprNum']."</div></td>
                     <td><div align='center'>$</div></td>
                     <td><div align='right'>".number_format($row['CprGTot'],2)."</div></td>
                     <td><div align='right'>".$row['CprCla']."</div></td>
					 </a>
		</tr>";			 
 }
 echo  "</table>";
 //mostrar los comprobantes					

}
//***********************************************************************
function vercuotas()
{ //muestra las cuotas sin realizar ningun proceso
 //aumentar el tiempo de ejecucion
 set_time_limit(480);
 $link = Conectar_SQL($_SESSION['suc_actual']);

 // Obtener la info de la cuenta
 $result = sqlsrv_query($link,"select CCtSuc, CCtLCo, CCtPVt, CCtLet, CCtTip, CCtCom , CCtCuo, CCtIte, CONVERT(varchar,CCtFVC,112)as CCtFVC , CCtImp, CCtEst, CCtCRSuc, CCtCRLCo, CCtCRPVt, CCtCRLet, CCtCRTip, CCtCRCom 
                        from FaCCCli 
                        where Clicod=".$_SESSION['cliente_actual']);
 
 //armar la tabla
 echo  "<table width='97%'  cellpadding='4' cellspacing='0' id='titulo'>
                   <tr>
                     <td align='center'> Sucursal: ".$_SESSION['suc_actual'].'-'.$_SESSION['nom_suc_actual']." </td>
                     <td align='center'> Ver Cuotas : ".$_SESSION['cliente_actual'].'-'.$_SESSION['nombre_cliente_actual']."</td>
					</tr>
				</table>";
 echo  "      <table width='97%' cellpadding='2' cellspacing='0' class='tabla_datos'>
                   <tr>
                     <td class='busqueda_fondo'><div align='center'>S</div></td>
                     <td class='busqueda_fondo'><div align='center'>L</div></td>
                     <td class='busqueda_fondo'><div align='center'>P.V.</div></td>
                     <td class='busqueda_fondo'><div align='center'>L</div></td>
                     <td class='busqueda_fondo'><div align='center'>Tip</div></td>
                     <td class='busqueda_fondo'><div align='center'>Comp</div></td>
                     <td class='busqueda_fondo'><div align='center'>Cuo</div></td>
                     <td class='busqueda_fondo'><div align='center'>It</div></td>
                     <td class='busqueda_fondo'><div align='center'>Ven</div></td>
                     <td class='busqueda_fondo'><div align='center'>Imp</div></td>
                     <td class='busqueda_fondo'><div align='center'>E</div></td>
                     <td class='busqueda_fondo'><div align='center'>S</div></td>
                     <td class='busqueda_fondo'><div align='center'>L</div></td>
                     <td class='busqueda_fondo'><div align='center'>PV</div></td>
                     <td class='busqueda_fondo'><div align='center'>L</div></td>
                     <td class='busqueda_fondo'><div align='center'>Tip</div></td>
                     <td class='busqueda_fondo'><div align='center'>Ref</div></td>
                   </tr>";
   // recorrer los comprobantes
  while ($row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC))
  {
    echo  "<tr class='tabla_datos'>
				<a href='VerImprime.php?CprTip=".$row['CCtTip']."&CprLet=".$row['CCtLet']."&CprPvt=".$row['CCtPVt']."&CprNum=".$row['CCtCom']."'>
                     <td><div align='left'>".$row['CCtSuc']."</div></td>
                     <td><div align='right'>".$row['CCtLCo']."</div></td>
                     <td><div align='center'>".$row['CCtPVt']."</div></td>
                     <td><div align='right'>".$row['CCtLet']."</div></td>
                     <td><div align='center'>".$row['CCtTip']."</div></td>
                     <td><div align='left'>".$row['CCtCom']."</div></td>
                     <td><div align='right'>".$row['CCtCuo']."</div></td>
                     <td><div align='center'>".$row['CCtIte']."</div></td>
                     <td><div align='right'>".  Convertir_Fecha_ISO($row['CCtFVC'])."</div></td>
                     <td><div align='center'>".number_format($row['CCtImp'],2)."</div></td>
                     <td><div align='left'>".$row['CCtEst']."</div></td>
                     <td><div align='right'>".$row['CCtCRSuc']."</div></td>
                     <td><div align='center'>".$row['CCtCRLCo']."</div></td>
                     <td><div align='right'>".$row['CCtCRPVt']."</div></td>
                     <td><div align='center'>".$row['CCtCRLet']."</div></td>
                     <td><div align='right'>".$row['CCtCRTip']."</div></td>
				</a> ";
    if ( $row['CCtCRTip'] == "REC" || $row['CCtCRTip'] == 'PID' )
    {                     
      echo "<td><a href='#' onclick=verrecibo('".$row['CCtCRTip']."',".$row['CCtCRCom'].")><div align='right'>".$row['CCtCRCom']."</div></a></td></tr>";
    }else{
      echo "<td><div align='right'>".$row['CCtCRCom']."</div></td></tr>";
    }//if           
 } //while
 echo  "</table>";
 
}
//***********************************************************************
function verpunthogar()
{ //muestra las cuenta punthogar 
 //aumentar el tiempo de ejecucion
 set_time_limit(480);
 $total = 0;
 
 $link = Conectar_SQL($_SESSION['suc_actual']);

 // Obtener la info de la cuenta
 $result = sqlsrv_query($link,"select CPHSuc, CPHLCo, CPHPVt, CPHLet, CPHTip, CPHCom,CPHCuo,CONVERT(varchar,CPHFec,112)as CPHFec,CPHPun,CPHObs from PHPeCPH
                        where Clicod=".$_SESSION['cliente_actual']." order by CPHFec");
 
 //armar la tabla
 echo  "<table width='97%'  cellpadding='4' cellspacing='0' id='titulo'>
                   <tr>
                     <td align='center'> Sucursal: ".$_SESSION['suc_actual'].'-'.$_SESSION['nom_suc_actual']." </td>
                     <td align='center'> Cuenta Punthogar : ".$_SESSION['cliente_actual'].'-'.$_SESSION['nombre_cliente_actual']."</td>
					</tr>
		</table>";
 echo  "      <table width='97%' cellpadding='2' cellspacing='0' class='tabla_datos'>
                   <tr alt'Pensamdo...'>
                     <td class='busqueda_fondo'><div align='center'>Suc.</div></td>
                     <td class='busqueda_fondo'><div align='center'>Loc.</div></td>
                     <td class='busqueda_fondo'><div align='center'>P.V.</div></td>
                     <td class='busqueda_fondo'><div align='center'>L</div></td>
                     <td class='busqueda_fondo'><div align='center'>Tipo</div></td>
                     <td class='busqueda_fondo'><div align='center'>Numero</div></td>
 		             <td class='busqueda_fondo'><div align='center'>Cuo</div></td>
                     <td class='busqueda_fondo'><div align='center'>Fecha</div></td>
                     <td class='busqueda_fondo'><div align='center'>Puntos</div></td>
                     <td class='busqueda_fondo'><div align='center'>Comprobante Observación</div></td>
                   </tr>";
   // recorrer los comprobantes
  while ($row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC))
  {
    echo  "<tr class='tabla_datos'>
                     <td><div align='left'>".$row['CPHSuc']."</div></td>
                     <td><div align='right'>".$row['CPHLCo']."</div></td>
                     <td><div align='center'>".$row['CPHPVt']."</div></td>
                     <td><div align='right'>".$row['CPHLet']."</div></td>
                     <td><div align='center'>".$row['CPHTip']."</div></td>";
	if ($row['CPHTip'] == 'REC' || $row['CPHTip'] == 'PID')	{
 	    echo "    	 <td><a href='#' onclick=verrecibo('".$row['CPHTip']."',".$row['CPHCom'].")><div align='left'>".$row['CPHCom']."</div></a></td>";
    } else {					 
        echo "       <td><div align='left'>".$row['CPHCom']."</div></td>";
	} 
	echo "           <td><div align='right'>".$row['CPHCuo']."</div></td>
                     <td><div align='right'>".  Convertir_Fecha_ISO($row['CPHFec'])."</div></td>
                     <td><div align='right'>".number_format($row['CPHPun'],2)."</div></td>
                     <td><div align='left'>".$row['CPHObs']."</div></td>
		      </tr>";			 
   $total += round($row['CPHPun'],2);			  
 }
 echo "<tr class=busqueda_fondo>
          <td colspan=8><div align=right >Total:</div></td>
          <td ><div align=right class=busqueda_text>".number_format($total,2)."</div></td>
		  <td>&nbsp;</td>
		</tr>";
 echo  "</table>";
 //mostrar los comprobantes					

}
//***********************************************************************
function verpunthogarh()
{ //muestra las cuenta punthogar 
 //aumentar el tiempo de ejecucion
 set_time_limit(480);
 $total = 0;
 
 $link = Conectar_SQL($_SESSION['suc_actual']);

 // Obtener la info de la cuenta
 $result = sqlsrv_query($link,"select HPHSuc, HPHLCo,HPHPVt,HPHLet,HPHTip,HPHCom,CONVERT(varchar,HPHFec,112)as HPHFec,HPHPun,HPHObs  from PHPECHP
                        where Clicod=".$_SESSION['cliente_actual']." order by HPHFec");
 
 //armar la tabla
 echo  "<table width='97%'  cellpadding='4' cellspacing='0' id='titulo'>
                   <tr>
                     <td align='center'> Sucursal: ".$_SESSION['suc_actual'].'-'.$_SESSION['nom_suc_actual']." </td>
                     <td align='center'> Cuenta Punthogar Histórico ".$_SESSION['cliente_actual'].'-'.$_SESSION['nombre_cliente_actual']."</td>
					</tr>
		</table>";
 echo  "      <table width='97%' cellpadding='2' cellspacing='0' class='tabla_datos'>
                   <tr alt'Pensamdo...'>
                     <td class='busqueda_fondo'><div align='center'>Suc.</div></td>
                     <td class='busqueda_fondo'><div align='center'>Loc.</div></td>
                     <td class='busqueda_fondo'><div align='center'>P.V.</div></td>
                     <td class='busqueda_fondo'><div align='center'>L</div></td>
                     <td class='busqueda_fondo'><div align='center'>Tipo</div></td>
                     <td class='busqueda_fondo'><div align='center'>Numero</div></td>
                     <td class='busqueda_fondo'><div align='center'>Fecha</div></td>
                     <td class='busqueda_fondo'><div align='center'>Puntos</div></td>
                     <td class='busqueda_fondo'><div align='center'>Observación</div></td>
                   </tr>";
   // recorrer los comprobantes
  while ($row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC))
  {

    echo  "<tr class='tabla_datos'>
                     <td><div align='left'>".$row['HPHSuc']."</div></td>
                     <td><div align='right'>".$row['HPHLCo']."</div></td>
                     <td><div align='center'>".$row['HPHPVt']."</div></td>
                     <td><div align='right'>".$row['HPHLet']."</div></td>
                     <td><div align='center'>".$row['HPHTip']."</div></td>
                     <td><div align='left'>".$row['HPHCom']."</div></td>
                     <td><div align='right'>".  Convertir_Fecha_ISO($row['HPHFec'])."</div></td>
                     <td><div align='right'>".number_format($row['HPHPun'],2)."</div></td>
                     <td><div align='left'>".$row['HPHObs']."</div></td>
		      </tr>";			 
   $total += $row['HPHPun'];			  
 }
 echo "<tr class=busqueda_fondo>
          <td colspan=7><div align=right >Total:</div></td>
          <td ><div align=right class=busqueda_text>".$total."</div></td>
		  <td>&nbsp;</td>
		</tr>";
 echo  "</table>";
 //mostrar los comprobantes					

}
//***********************************************************************
function ver_doc_fecha($desde, $hasta)
{ //muestra las facturas entre un rango de fechas
 //aumentar el tiempo de ejecucion
 set_time_limit(480);
 
 //Convertir las fecha desde/hasta al formato aaaammdd para evitar regionalizacion
 $desde = Convertir_Fecha_SR($desde);
 $hasta = Convertir_Fecha_SR($hasta);
 
 $link = Conectar_SQL($_SESSION['suc_actual']);

 // Obtener la info de la cuenta
 $result = sqlsrv_query($link,"select CONVERT(varchar,CprFec,112)as CprFec, SucCod, LCoCod, CprPVt, CprTip, CprLet, CprNum, CprGTot, CprCla 
						from fafccca where CprFec between '".$desde."' and '".$hasta."'
						order by CprFec,CprTip, CprLet, CprPvt, CprNum");
echo "select CONVERT(varchar,CprFec,112)as CprFec, SucCod, LCoCod, CprPVt, CprTip, CprLet, CprNum, CprGTot, CprCla 
						from fafccca where CprFec between '".$desde."' and '".$hasta."'
						order by CprFec,CprTip, CprLet, CprPvt, CprNum";
 //armar la tabla
 /*
 echo  "      <table width='97%' cellpadding='2' cellspacing='0' class='tabla_datos'>
				   <tr>
					 <td class='busqueda_fondo'><div align='center'>Fecha</div></td>
					 <td class='busqueda_fondo'><div align='center'>Tipo</div></td>
					 <td class='busqueda_fondo'><div align='center'>Suc</div></td>
					 <td class='busqueda_fondo'><div align='center'>Loc</div></td>
					 <td class='busqueda_fondo'><div align='center'>P.V.</div></td>
					 <td class='busqueda_fondo'><div align='center'>Letra</div></td>
					 <td class='busqueda_fondo'><div align='center'>Numero</div></td>
					 <td class='busqueda_fondo'><div align='center'>Mon</div></td>
					 <td class='busqueda_fondo'><div align='center'>Total</div></td>
					 <td class='busqueda_fondo'><div align='center'>Clas.</div></td>
				   </tr>";
   // recorrer los comprobantes
  while ($row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC))
  {

	echo  "<tr class='tabla_datos'>
					 <a href='#' onclick=verfactura('".$row['CprTip']."','".$row['CprLet']."',".$row['CprPVt'].",".$row['CprNum'].")>
					 <td><div align='right'>".  Convertir_Fecha_ISO($row['CprFec'])."</div></td>
					 <td><div align='left'>".$row['CprTip']."</div></td>
					 <td><div align='center'>".$row['SucCod']."</div></td>
					 <td><div align='center'>".$row['LCoCod']."</div></td>
					 <td><div align='right'>".$row['CprPVt']."</div></td>
					 <td><div align='center'>".$row['CprLet']."</div></td>
					 <td><div align='left'>".$row['CprNum']."</div></td>
					 <td><div align='center'>$</div></td>
					 <td><div align='right'>".number_format($row['CprGTot'],2)."</div></td>
					 <td><div align='right'>".$row['CprCla']."</div></td>
		</a></tr>";			 
 }
 echo  "</table>";
 */
}
//***********************************************************************

function verdatos($cliente,$titulo)
{
 $link = Conectar_SQL($_SESSION['suc_actual']);
 //obtener los datos de cliente
 $result = sqlsrv_query($link,"Select * from Clperso where percod=".$cliente); 
 $row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC);
 
 echo "<table width='97%' height='420'  border='0' cellpadding='5' cellspacing='5' class='tabla_datos' align=center>";
 echo "<tr>
         <td class='busqueda_fondo' align='center'> Sucursal: ".$_SESSION['suc_actual'].'-'.$_SESSION['nom_suc_actual']." </td>
         <td class='busqueda_fondo'><div align='center'>Datos Personales ".$titulo."</div></td>
       </tr>";
 echo "<tr>
		<td>C&oacute;digo 
		<input name='textfield' type='text' class='busqueda_text' size='10' value=".$row['PerCod']."> 
		Nombre 
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities(chop($row['PerNom']))."'> 
		F. Nac. 
		<input name='textfield' type='text' class='busqueda_text' size='15' value='".Convertir_Fecha($row['PerFNa'])."'> 
		Sexo 
		<input name='textfield' type='text' class='busqueda_text' size='15' value=".(($row['PerSex'] == 'M')? 'Masculino' : 'Femenino')."> 
		</td>
	  </tr>";
 $res_aux = sqlsrv_query($link,"Select TDoDes from SITIDOC where TDoCod=".$row['TDoCod']);
 $row_aux = sqlsrv_fetch_array($res_aux , SQLSRV_FETCH_ASSOC);
 echo "<tr>
		<td>Tipo Documento 
		<input name='textfield' type='text' class='busqueda_text' size='5' value=".$row['TDoCod']."> 
		<input name='textfield' type='text' class='busqueda_text' size='40' value=".$row_aux['TDoDes']."> 
		Numero 
		<input name='textfield' type='text' class='busqueda_text' value=".$row['PerDoc']."></td>
	  </tr>";

 $res_aux = sqlsrv_query($link,"Select ECiDes from SIESCIV where ECiCod=".$row['ECiCod']);
 $row_aux = sqlsrv_fetch_array($res_aux, SQLSRV_FETCH_ASSOC);

 echo "<tr>
		<td>Est. Civil 
		<input name='textfield' type='text' class='busqueda_text' size='5' value=".$row['ECiCod'].">
		<input name='textfield' type='text' class='busqueda_text' size='40' value=".$row_aux['ECiDes']."> 
		Tel. 
		<input name='textfield' type='text' class='busqueda_text' size='20' value='".htmlentities($row['PerTel'])."'> 
		Cel. 
		<input name='textfield' type='text' class='busqueda_text' size='20' value='".htmlentities($row['PerCel'])."'></td>
	  </tr>";
 echo "<tr>
		<td>Domicilio 
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row['PerDom'])."'> 
		Numero 
		<input name='textfield' type='text' class='busqueda_text' size='10' value=".$row['PerNum']."> 
		Piso 
		<input name='textfield' type='text' class='busqueda_text' size='10' value=".htmlentities($row['PerPis'])."> 
		Depto. 
		<input name='textfield' type='text' class='busqueda_text' size='10' value='".htmlentities($row['PerDep'])."'></td>
	  </tr>";
 echo "<tr>
		<td>Manzana 
		<input name='textfield' type='text' class='busqueda_text' size='10' value=".htmlentities($row['PerMan'])."> 
		Entre 
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row['PerDomCa1'])."'> 
		y 
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row['PerDomCa2'])."'></td>
	  </tr>";
 $res_aux = sqlsrv_query($link,"Select LocNom from NULOCALI where Loccod=".$row['PerLoCo']);
 $row_aux = sqlsrv_fetch_array($res_aux , SQLSRV_FETCH_ASSOC);

 echo "<tr>
		<td>Barrio 
		<input name='textfield' type='text' class='busqueda_text' size='30' value='".htmlentities($row['PerBar'])."'> 
		Loc. 
		<input name='textfield' type='text' class='busqueda_text' size='10' value=".$row['PerLoCo']."> 
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row_aux['LocNom'])."'> 
		CPA 
		<input name='textfield' type='text' class='busqueda_text' size='10' value=".$row['PerLoCP']."></td>
	  </tr>";
 $res_aux = sqlsrv_query($link,"Select TViDes from SITIVIV where TViCod=".$row['TViCod']);
 $row_aux = sqlsrv_fetch_array($res_aux , SQLSRV_FETCH_ASSOC);

 echo "<tr>
		<td>Viv. 
		<input name='textfield' type='text' class='busqueda_text' size='5' value=".$row['TViCod']."> 
		<input name='textfield' type='text' class='busqueda_text' size='40' value=".$row_aux['TViDes']."> 
		Alq. $ 
		<input name='textfield' type='text' class='busqueda_text' size='20' value=".number_format($row['PerAlq'],2)."> 
		Hab. Desde 
		<input name='textfield' type='text' class='busqueda_text' size='15' value='".Convertir_Fecha($row['PerFHD'])."'></td>
	  </tr>";
 echo "<tr>
		<td>Mail 
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row['PerMail'])."'> 
		Ing. $ 
		<input name='textfield' type='text' class='busqueda_text' size='20' value=".number_format($row['PerIng'],2)."></td>
	  </tr>";
 $res_aux = sqlsrv_query($link,"Select ActDes from SIACTIV where ActCod=".$row['PerAcCo']);
 $row_aux = sqlsrv_fetch_array($res_aux , SQLSRV_FETCH_ASSOC);
 echo "<tr>
		<td>Act. 
		<input name='textfield' type='text' class='busqueda_text' size='5' value=".$row['PerAcCo']."> 
		<input name='textfield' type='text' class='busqueda_text' size='40' value=".$row_aux['ActDes'].">    
		Fec.Ing.Lab. 
		<input name='textfield' type='text' class='busqueda_text' size='15' value='".Convertir_Fecha($row['PerFIL'])."'></td>
	  </tr>";
 echo "<tr>
		<td>Empr. 
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row['PerEmp'])."'> 
		Dom. 
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row['PerEmpDom'])."'> 
		Nro. 
		<input name='textfield' type='text' class='busqueda_text' size='10' value=".$row['PerEmpNum']."></td>
	  </tr>";

 $res_aux = sqlsrv_query($link,"select ZonDes from SIZONAS where ZonCod=".$row['ZonCod']);
 $row_aux = sqlsrv_fetch_array($res_aux , SQLSRV_FETCH_ASSOC);
 echo "<tr>
		<td>Zona 
		<input name='textfield' type='text' class='busqueda_text' size='5' value=".$row['ZonCod'].">
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row_aux['ZonDes'])."'> 
		Tel. 
		<input name='textfield' type='text' class='busqueda_text' size='20' value='".htmlentities($row['PerEmpTel'])."'></td>
	  </tr>";

 $res_aux = sqlsrv_query($link,"Select LocNom from NULOCALI where Loccod=".$row['PerELoCo']);
 $row_aux = sqlsrv_fetch_array($res_aux , SQLSRV_FETCH_ASSOC);
 echo "<tr>
		<td>Barrio 
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row['PerEBa'])."'> 
		Loc. 
		<input name='textfield' type='text' class='busqueda_text' size='5' value=".$row['PerELoCo'].">
		<input name='textfield' type='text' class='busqueda_text' size='40' value='".htmlentities($row_aux['LocNom'])."'> 
		CPA 
		<input name='textfield' type='text' class='busqueda_text' size='10' value=".$row['PerELoCP']."></td>
	  </tr>";
 $res_aux = sqlsrv_query($link,"Select ActDes from SIACTIV where ActCod=".$row['PerAcACo']);
 $row_aux = sqlsrv_fetch_array($res_aux , SQLSRV_FETCH_ASSOC);
 echo "<tr>
		<td>Act. Ad. 
		<input name='textfield' type='text' class='busqueda_text' size='5' value=".$row['PerAcACo'].">
		<input name='textfield' type='text' class='busqueda_text' size='50' value='".$row_aux['ActDes']."'> 
		Ing. Ad. $ 
		<input name='textfield' type='text' class='busqueda_text' size='20' value=".$row['PerIngAd']."></td>
	  </tr>";
 echo "</table>";

}

//***********************************************************************
function muestra_datos()
{
 $link = Conectar_SQL($_SESSION['suc_actual']);

 //muestra los datos personales del titular y sus garantes.
 echo "<a name=top>&nbsp;</a>";
 echo "<table width='100%'  border='1' cellpadding='4' cellspacing='4' class='tabla_datos'>
  <tr class='busqueda_fondo'>
    <td width='15%'>C&oacute;digo</td>
    <td width='39%'>Nombre </td>
    <td width='37%'>Clasificaci&oacute;n</td>
    <td width='9%'>&nbsp;</td>
  </tr>";
 //titular 
 echo "<tr>
    <td>".$_SESSION['cliente_actual']."</td>
    <td>".$_SESSION['nombre_cliente_actual']."</td>
    <td>Titular</td>
    <td><a href='#titular'>Ver Datos</a></td>
  </tr>";

 //obtener el conyuge
 $resultc = sqlsrv_query($link,'select PerCod,PerNom from clperso where percod in (select cygcod from coclien where clicod='.$_SESSION['cliente_actual'].')');
 $conyuge = 0 ;						 
 if ($rowc = sqlsrv_fetch_array($resultc , SQLSRV_FETCH_ASSOC))
 {						 
  echo "<tr>
    <td>".$rowc['PerCod']."</td>
    <td>".$rowc['PerNom']."</td>
    <td>Conyuge</td>
    <td><a href='#conyuge'>Ver Datos</a></td>
  </tr>";
  $conyuge = $rowc['PerCod'];
  }
 //garantes
 $resultg = sqlsrv_query ($link,"select PerCod,PerNom from Clperso where percod in (Select CdeCod from cocodeu 
                         where clicod=".$_SESSION['cliente_actual'].")");
 $i = 0;						 
 while ($rowg = sqlsrv_fetch_array($resultg , SQLSRV_FETCH_ASSOC))
 {						
  $i++; 
  echo "<tr>
    <td>".$rowg['PerCod']."</td>
    <td>".htmlentities(chop($rowg['PerNom']))."</td>
    <td>Garante</td>
    <td><a href='#garante".$i."'>Ver Datos</a></td>
  </tr>";
  $datos_g[$i] = $rowg['PerCod'];
  }
 echo "</table>";
 
 echo "<table height=400px><tr><td>&nbsp;</td></tr></table>";
 
 //mostrar los datos
 echo "<a name='titular'>";
 verdatos($_SESSION['cliente_actual'],'Titular');
 echo "<a href='#top'>Inicio</a>";

 if ($conyuge <> 0)
 {
	 echo "<a name='conyuge'>";
	 verdatos($conyuge,'Conyuge');
	 echo "<a href='#top'>Inicio</a>";
 } 
 
 
 $j = 1; 
 while ($j <= $i)
 {						
   echo "<a name='garante".$j."'>";
   verdatos($datos_g[$j],'Garante');
   echo "<a href='#top'>Inicio</a>";
   $j++;
 }

}
//***********************************************************************
function vercheques()
{ //muestra las cuenta punthogar 
 //aumentar el tiempo de ejecucion
 set_time_limit(480);
 $total = 0;
 
 $link = Conectar_SQL($_SESSION['suc_actual']);

 $query = "SELECT CONVERT(varchar,T1.CheArqFch,112)as CheArqFch, CONVERT(varchar,T1.CheFchPago,112)as CheFchPago, T3.BanDes, T1.CheNro, T1.CheImp, T2.CheEstDsc from CAJCHE T1
		  		  LEFT JOIN CHEESTAD T2 ON T1.CheEstCod = T2.CheEstCod
				  LEFT JOIN SIBANCO T3 ON T1.CheBcoCod=T3.BanCod
		  WHERE T2.CheEstCod in (5,7,15,16) AND CheCliCod=".$_SESSION['cliente_actual'];

 $result = sqlsrv_query($link,$query);
 
 //armar la tabla
 echo  "<table width='97%'  cellpadding='4' cellspacing='0' id='titulo'>
                    <tr>
                     <td align='center'> Sucursal: ".$_SESSION['suc_actual'].'-'.$_SESSION['nom_suc_actual']." </td>
                     <td align='center'> Cartera de Valores ".$_SESSION['cliente_actual'].'-'.$_SESSION['nombre_cliente_actual']."</td>
					</tr>
		</table>";
 echo  "      <table width='97%' cellpadding='2' cellspacing='0' class='tabla_datos'>
                   <tr>
                     <td class='busqueda_fondo'><div align='left'>Fecha de Caja</div></td>
                     <td class='busqueda_fondo'><div align='left'>Fecha Vto.</div></td>
                     <td class='busqueda_fondo'><div align='left'>Banco</div></td>
                     <td class='busqueda_fondo'><div align='right'>Nro. de Cheque</div></td>
                     <td class='busqueda_fondo'><div align='right'>Importe</div></td>
                     <td class='busqueda_fondo'><div align='right'>Estado</div></td>
                   </tr>";
   // recorrer los comprobantes
  while ($row = sqlsrv_fetch_array($result , SQLSRV_FETCH_ASSOC))
  {

    echo  "<tr class='tabla_datos'>
                     <td><div align='left'>".  Convertir_Fecha_ISO($row['CheArqFch'])."</div></td>
                     <td><div align='left'>".  Convertir_Fecha_ISO($row['CheFchPago'])."</div></td>
                     <td><div align='left'>".htmlentities($row['BanDes'])."</div></td>
                     <td><div align='right'>".$row['CheNro']."</div></td>
                     <td><div align='right'>".number_format($row['CheImp'],2)."</div></td>
                     <td><div align='right'>".htmlentities($row['CheEstDsc'])."</div></td>
		      </tr>";			 
   $total += $row['CheImp'];			  
 }
 echo "<tr class=busqueda_fondo>
          <td colspan=4><div align=right >Total:</div></td>
          <td ><div align=right class=busqueda_text>".number_format($total,2)."</div></td>
		  <td>&nbsp;</td>
		</tr>";
 echo  "</table>";

}
//***********************************************************************



// PRINCIPAL

//Obtener los parametros 
 /*
 Operación
 
   1 : Consulta de Cuenta
   2 : Ver Pagos
   3 : Ver Cuotas
   4 : Ver Documentos
   5 : Punthogar
   6 : Punthogar Historico  
   7 : Documentos por fecha 
   8 : Datos del Cliente
 */
 $operacion = $_GET['op'];

 switch ($operacion) {
   case 1 : consulta();
   break;
   case 2: verpagos();
   break;
   case 3: vercuotas();
   break;
   case 4: verdocumentos();
   break;
   case 5 : verpunthogar();
   break;
   case 6 : verpunthogarh();
   break;
   case 7 :  $desde = $_GET['fd'];
			 $hasta = $_GET['fh'];
	   		 ver_doc_fecha($desde,$hasta);
   break;
   case 8: muestra_datos();
   break;
   case 9: vercheques();
   break;
   
 }


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
<body>

<!-- Ver factura -->
<div id="factura" style=" visibility:hidden; position:absolute; left:1px; top:1px; width:690px; height:400px; z-index:-1;" class="boton">
<table width="690px" height="400px"  border="1" cellpadding="0" cellspacing="0" >
  <tr>
      <td height="20">
	    <table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
          <tr class="busqueda_fondo" >
           <td width="90%" >Detalle de Factura </td>
		   <td width="7%">
		      <a href="javascript:VerImprimir('factura');"> 
	            <img src="../Imagen/printer1.jpg" alt="Imprimir" width="19" height="17" >
              </a> 				
		   </td>
           <td width="3%" align="right" valign="top">
	        <img src="../Imagen/close.gif" alt="Cerrar" width="19" height="17" onClick="cerrar('factura');" onMouseOver="this.src='../Imagen/close_over.gif'"  onMouseOut="this.src='../Imagen/close.gif'"></td>
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
<!-- Cuotas -->
<tr>
  <td height='280'>
    <table  width='100%'  border='0' cellspacing='0' cellpadding='0'>
	  <tr>
   	    <td width='40%' valign='top'>
         <div id="cuotas">&nbsp;</div>
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
	   </table>
	   </td>
     </tr>
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
           <td width="97%">Detalle de Refinanciación </td>
           <td width="3%" align="right" valign="top">
	        <img src="../Imagen/close.gif" alt="Cerrar" width="19" height="17" onClick="cerrar('refin');" onMouseOver="this.src='../Imagen/close_over.gif'"  onMouseOut="this.src='../Imagen/close.gif'"></td>
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
	        <img src="../Imagen/close.gif" alt="Cerrar" width="19" height="17" onClick="cerrar('ndc');" onMouseOver="this.src='../Imagen/close_over.gif'"  onMouseOut="this.src='../Imagen/close.gif'"></td>
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
	         <img src="../Imagen/close.gif" alt="Cerrar" width="19" height="17" onClick="cerrar('recibo');" onMouseOver="this.src='../Imagen/close_over.gif'"  onMouseOut="this.src='../Imagen/close.gif'">
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