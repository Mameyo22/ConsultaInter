<?php
//**********************************************************************************************************************
function verrefin($tipo,$letra,$pvt,$comp)
{
 //ampliar el tiempo de respuesta
  set_time_limit(620);

 $objResponse = new xajaxResponse();
 //Variables de session
 $SucCod = $_SESSION['suc_actual']; //Codigo de la sucursal remota
 $CliCod = $_SESSION['cliente_actual']; //Cliente actual

 //Conectar al servidor de la sucursal
 $link = Conectar_SQL($SucCod);

 //Comprobantes Cancelados
 $result = sqlsrv_query($link,"select CCtSuc, CCtLCo, CCtPVt, CCtLet, CCtTip, CCtCom, CCtCuo, CCtIte,CCtFVC,CCtImp,CCtUit  from facccli where
                        CliCod=".$CliCod." and CCtCRTip='".$tipo."' and CCtCRCom=".$comp." and CCtTip <>'NDR'");
 $respuesta = "<table width='100%'  border='0' cellpadding='0' cellspacing='0' class='tabla_datos'>";

 $respuesta .="<tr class='totales'>
          <td><div align='center'>Suc.</div></td>
          <td><div align='center'>Loc. </div></td>
          <td><div align='center'>Tipo</div></td>
          <td><div align='center'>P.Vta.</div></td>
          <td><div align='center'>Letra</div></td>
          <td><div align='center'>Numero</div></td>
          <td><div align='center'>Cuota</div></td>
          <td><div align='center'>Item</div></td>
          <td><div align='center'>Fecha</div></td>
          <td><div align='center'>Dias V.</div></td>
          <td><div align='center'>Importe</div></td>
        </tr>";

 $totalR = 0;
 while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
  {


	  //Obtener los dias de vencimiento hasta la fecha de refinanciacion

	  //Obtener la fecha de vto de la cuota, item = 0
	  $res_cuo = sqlsrv_query($link,"select CCtFVC from FACCCLI where
	                       Clicod=".$CliCod." and CCtTip='".$row['CCtTip']."' and CCtPVt=".$row['CCtPVt']." and CCtLet='".$row['CCtLet']."'
						    and CCtCom=".$row['CCtCom']." and CCtCuo=".$row['CCtCuo']." and CCtIte=0");
	  if ($row_cuo = sqlsrv_fetch_array($res_cuo, SQLSRV_FETCH_ASSOC))
	    {
          $fref = Convertir_Fecha($row['CCtFVC']);
	      $fvco = Convertir_Fecha($row_cuo['CCtFVC']);
          $diasV = Dias($fvco,$fref);
	    }
	 else
	    {$diasV = 0 ;}




      $respuesta .= "<tr>";
      $respuesta .= "<td><div align=center>".$row['CCtSuc']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtLCo']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtTip']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtPVt']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtLet']."</div></td>";
      $respuesta .= "<td><div align=right>".$row['CCtCom']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtCuo']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtIte']."</div></td>";
      $respuesta .= "<td><div align=center>".$fref."</div></td>";
      $respuesta .= "<td><div align=center>".$diasV."</div></td>";
      $respuesta .= "<td><div align=right>".number_format($row['CCtImp'],2)."</div></td>";
	  $totalR += $row['CCtImp'];
	  $respuesta .= "</tr>";
  }
 $respuesta .= "</table>";
 $objResponse->addAssign('cancelados','innerHTML',$respuesta);
 $totalR *= -1;
 $objResponse->addAssign('totalR','innerHTML','Total de Refinanciacion: '.$totalR);
 //Nota debito por refinanciacion
 $result = sqlsrv_query($link, "select CCtSuc, CCtLCo, CCtPVt, CCtLet, CCtTip, CCtCom, CCtCuo, CCtIte,CCtFVC,CCtImp from facccli where
                        CliCod=".$CliCod." and CCtCRTip='".$tipo."' and CCtCRCom=".$comp." and CCtTip ='NDR'");
 $respuesta = "<table width='100%'  border='0' cellpadding='0' cellspacing='0' class='tabla_datos'>";

 $respuesta .="<tr class='totales'>
          <td><div align='center'>Suc.</div></td>
          <td><div align='center'>Loc. </div></td>
          <td><div align='center'>Tipo</div></td>
          <td><div align='center'>P.Vta.</div></td>
          <td><div align='center'>Letra</div></td>
          <td><div align='center'>Numero</div></td>
          <td><div align='center'>Cuota</div></td>
          <td><div align='center'>Item</div></td>
          <td><div align='center'>Fecha</div></td>
          <td><div align='center'>Importe</div></td>
        </tr>";


 while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
  {
      $respuesta .= "<tr>";
      $respuesta .= "<td><div align=center>".$row['CCtSuc']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtLCo']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtTip']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtPVt']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtLet']."</div></td>";
      $respuesta .= "<td><div align=right>".$row['CCtCom']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtCuo']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtIte']."</div></td>";
      $respuesta .= "<td><div align=center>".Convertir_Fecha($row['CCtFVC'])."</div></td>";
      $respuesta .= "<td><div align=right>".number_format($row['CCtImp'],2)."</div></td>";
	  $respuesta .= "</tr>";
  }
 $respuesta .= "</table>";
 $objResponse->addAssign('ndr','innerHTML',$respuesta);


 //Cuotas Refinanciacion
 $result = sqlsrv_query("select CCtSuc, CCtLCo, CCtPVt, CCtLet, CCtTip, CCtCom, CCtCuo, CCtIte,CCtFVC,CCtImp
                       from facccli where CliCod=".$CliCod." and ccttip='".$tipo."' and CCtCom=".$comp."  and cctite=0 order by CCtCuo",$link);

 $respuesta = "<table width='100%'  border='0' cellpadding='0' cellspacing='0' class='tabla_datos'>";
 $respuesta .="<tr class='totales'>
          <td><div align='center'>Suc.</div></td>
          <td><div align='center'>Loc. </div></td>
          <td><div align='center'>Tipo</div></td>
          <td><div align='center'>P.Vta.</div></td>
          <td><div align='center'>Letra</div></td>
          <td><div align='center'>Numero</div></td>
          <td><div align='center'>Cuota</div></td>
          <td><div align='center'>Item</div></td>
          <td><div align='center'>Fecha</div></td>
          <td><div align='center'>Importe</div></td>
        </tr>";

 $saldo = 0;
 while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
  {
      $respuesta .= "<tr>";
      $respuesta .= "<td><div align=center>".$row['CCtSuc']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtLCo']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtTip']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtPVt']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtLet']."</div></td>";
      $respuesta .= "<td><div align=right>".$row['CCtCom']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtCuo']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtIte']."</div></td>";
      $respuesta .= "<td><div align=center>".Convertir_Fecha($row['CCtFVC'])."</div></td>";
      $respuesta .= "<td><div align=right>".number_format($row['CCtImp'],2)."</div></td>";
	  $saldo += $row['CCtImp'];
	  $respuesta .= "</tr>";
  }
 $respuesta .= "</table>";
 $objResponse->addAssign('cuotasR','innerHTML',$respuesta);

 //Pagos Efectuados
 $result = sqlsrv_query($link, "select CCtSuc, CCtLCo, CCtPVt, CCtLet, CCtTip, CCtCom, CCtCuo, CCtIte,CCtFVC,CCtImp
                       from facccli where CliCod=".$CliCod." and ccttip='".$tipo."' and CCtCom=".$comp."  and cctite<>0 order by CCtCuo");

 $respuesta = "<table width='100%'  border='0' cellpadding='0' cellspacing='0' class='tabla_datos'>";
 $respuesta .="<tr class='totales'>
          <td><div align='center'>Suc.</div></td>
          <td><div align='center'>Loc. </div></td>
          <td><div align='center'>Tipo</div></td>
          <td><div align='center'>P.Vta.</div></td>
          <td><div align='center'>Letra</div></td>
          <td><div align='center'>Numero</div></td>
          <td><div align='center'>Cuota</div></td>
          <td><div align='center'>Item</div></td>
          <td><div align='center'>Fecha</div></td>
          <td><div align='center'>Importe</div></td>
        </tr>";


 while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
  {
      $respuesta .= "<tr>";
      $respuesta .= "<td><div align=center>".$row['CCtSuc']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtLCo']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtTip']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtPVt']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtLet']."</div></td>";
      $respuesta .= "<td><div align=right>".$row['CCtCom']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtCuo']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['CCtIte']."</div></td>";
      $respuesta .= "<td><div align=center>".Convertir_Fecha($row['CCtFVC'])."</div></td>";
      $respuesta .= "<td><div align=right>".number_format($row['CCtImp'],2)."</div></td>";
	  $saldo += $row['CCtImp'];
	  $respuesta .= "</tr>";
  }
 $respuesta .= "</table>";
 $objResponse->addAssign('pagosR','innerHTML',$respuesta);
 $objResponse->addAssign('saldo','innerHTML','Saldo a Pagar: '.$saldo);


 //todo listo, ocultar el cuadro de espera
 $objResponse->addAssign('espera','style.visibility','hidden');
 $objResponse->addAssign('espera','style.zIndex','-2');

return $objResponse;
}
//**********************************************************************************************************************
function verfactura($tipo,$letra,$pvt,$comp)
{

 //ampliar el tiempo de respuesta
  set_time_limit(840);

 $objResponse = new xajaxResponse();

 /*
 Esta pagina recibe como par�metros la clave de factura: tipo, letra, pvt, numero
 y muestra la cabecera, el detalle y las cuotas de la factura.
 */
 //Variables de session
 $SucCod = $_SESSION['suc_actual']; //Codigo de la sucursal remota

 //Conectar al servidor de la sucursal
 $link = Conectar_SQL($SucCod);


	 //Cabecera : Obtengo los datos
	 $result = sqlsrv_query($link, "Select SucCod, LCoCod, CprPVt, CprTip, CprLet, CprNum,CliCod,CprGSubTot,CprGToRFSI,CprGToIRF1,CprGToIRF2,
							CprGToIIU1,CprGToIIU2,CprGToIInt,CprGTot,CprFec,CprCliNom,CprCliCUIT,CprGPIB,CprGPIH, CprGPIM, CprFToRFSR, CprIIBCod
							from fafccca where CprPvt=".$pvt." and CprTip='".$tipo."' and CprLet='".$letra."' and CprNum=".$comp,array(),array('Scrollable' => 'buffered'));

	 if (sqlsrv_num_rows($result) == 0)
		{ die('No hay resultados');}
	 else
		{$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);}

	 //obtengo el nombre del local comercial
	 $resloc = sqlsrv_query($link, "Select LCoNom from SiLocal where LCoCod=".$row['LCoCod']." and SucCod=".$row['SucCod']);
	 $rowloc = sqlsrv_fetch_array($resloc, SQLSRV_FETCH_ASSOC);

	 //primer linea
	 $objResponse->addAssign('SucCod','value',$row['SucCod']);
	 $objResponse->addAssign('SucNom','value',htmlentities($_SESSION['nom_suc_actual']));
	 $objResponse->addAssign('LocCod','value',$row['LCoCod']);
	 $objResponse->addAssign('LocNom','value',$rowloc['LCoNom']);

	 //segunda linea

	 $objResponse->addAssign('pvt','value',$row['CprPVt']);
	 $objResponse->addAssign('comp','value',$row['CprNum']);
	 $objResponse->addAssign('letra','value',$row['CprLet']);
	 $objResponse->addAssign('tipo','value',$row['CprTip']);
	 $objResponse->addAssign('fecha','value',Convertir_Fecha($row['CprFec']));

	//tercera linea
	 $objResponse->addAssign('Clicod','value',$row['CliCod']);
	 $objResponse->addAssign('Clinom','value',htmlentities($row['CprCliNom']));
	 $objResponse->addAssign('cuit','value',$row['CprCliCUIT']);
	 $objResponse->addAssign('ib','value',$row['CprIIBCod']);


	//Items
	//Obetngo los items de la factura, utilizo otra variable para los resultados, porque de la cabecera
	// aun voy a necesitar datos

	 $resit = sqlsrv_query($link, "Select e.CprCan, e.ArtCod, e.ArtDsc, e.CprPUSI, e.CprGTSI, e.SLiDes, f.MarDes, e.CprDev from
							(Select c.*, d.SliDes from
							 (Select a.CprCan, a.ArtCod, a.CprPUSI, a.CPRGTSI, b.ArtDsc, b.SliCod,b.MarCod,b.Lincod, a.CprDev from Fafccit a inner join CpArtic b
							   on a.ArtCod=b.ArtCod
							   where CprPvt=".$pvt." and CprTip='".$tipo."' and CprLet='".$letra."' and CprNum=".$comp.") c inner join CpSuLin d
							 on c.SliCod=d.SLiCod and c.Lincod=d.LinCod ) e inner join CpMarca f
							on e.MarCod=f.MarCod");

	$respuesta ="<table class='items' width='100%'  border='1' cellspacing='0' cellpadding='0' >
				  <tr>
					<th width='9%' scope='col' class='cabecera'>Cantidad</th>
					<th width='10%' scope='col' class='cabecera'>Art&iacute;culo</th>
					<th width='53%' scope='col' class='cabecera'>Descripci&oacute;n</th>
					<th width='14%' scope='col' class='cabecera'>Unitario</th>
					<th width='14%' scope='col' class='cabecera'>Sub Total </th>
					<th width='14%' scope='col' class='cabecera'>Devuelto</th>
				  </tr>";

	 while ($rowit = sqlsrv_fetch_array($resit, SQLSRV_FETCH_ASSOC))
	 {
	  $respuesta .= "<tr>";
	  $respuesta .= "<td align='right' class='busqueda_text'>".$rowit['CprCan']."</td>"; //Cantidad
	  $respuesta .= "<td class='busqueda_text'>".$rowit['ArtCod']."</td>"; //Articulo

	  $respuesta .= "<td class='busqueda_text'>".htmlentities($rowit['SLiDes']." ".$rowit['ArtDsc']." ".$rowit['MarDes'])."</td>"; //Descripcion

	  $respuesta .= "<td align='right' class='busqueda_text'>".number_format($rowit['CprPUSI'],2)."</td>"; //Unitario
	  $respuesta .= "<td align='right' class='busqueda_text'>".number_format($rowit['CprGTSI'],2)."</td>"; //Sub Total
	  $respuesta .= "<td align='right' class='busqueda_text'>".number_format($rowit['CprDev'],0)."</td>"; //Devueltos
	  $respuesta .= "</tr>";
	 }
	 $respuesta .= "</table>";

	 $objResponse->addAssign('items','innerHTML',$respuesta);

	//Pie

	  //Cuotas
	  $respuesta ="<table class='cuotas'  border='1' align='center' cellpadding='2' cellspacing='0'>
						  <tr>
							<th scope='col' class='totales'>Cuota</th>
							<th scope='col' class='totales'>Vto.</th>
							<th scope='col' class='totales'>Importe</th>
						  </tr>";

		//items cuotas
		$total = 0.0;
	   //Obtengo los datos
	   if ($rescuo = sqlsrv_query($link,"Select CCtCuo,CCtFVC,CCtImp from FaCCCli where CCtPvt=".$pvt." and CCtTip='".$tipo."' and CCtLet='".$letra."' and CCtCom=".$comp." order by CCTCuo,CCtIte"))
		{
			while ($rowcuo = sqlsrv_fetch_array($rescuo, SQLSRV_FETCH_ASSOC))
			{
			 $respuesta .= "<tr>";
			 $respuesta .= "<td align='right'>".$rowcuo['CCtCuo']."</td>"; // Cuota
			 $respuesta .= "<td align='center'>".Convertir_Fecha($rowcuo['CCtFVC'])."</td>"; // Fecha Vto.
			 $respuesta .= "<td align='right'>".number_format($rowcuo['CCtImp'],2)."</td>"; //Importe
			 $respuesta .= "</tr>";
			 $total += $rowcuo['CCtImp'];
			}
		}
		//total
		$respuesta .="<tr>
						<td colspan='2' align='right'>Total </td>
						<td><div align='right' id='total'>".number_format($total,2)."</div></td>
					  </tr>";

	  $objResponse->addAssign('cuotas','innerHTML',$respuesta);

	 //Totales de factura - Cabecera
	  $objResponse->addAssign('subtotal','innerHTML',number_format($row['CprGSubTot'],2)); //subtotal

	  $objResponse->addAssign('iva','innerHTML',number_format($row['CprGToIIU1'],2)); //iva

	  $objResponse->addAssign('ivad','innerHTML',number_format($row['CprGToIIU2'],2)); // iva AD

	  $objResponse->addAssign('ii','innerHTML',number_format($row['CprGToIInt'],2)); //imp internos

	  //Calcular el RF segun el nuevo criterio de Promocion: 24.02.11
	  // Si RFSR = 0 o es igual a RFSI -> RFSI, sino es la diferencia
	  //
	  //if (($row['CprFToRFSR'] == 0) or ($row['CprGToRFSI'] == $row['CprFToRFSR']) )
	  //{
	  $rf = $row['CprGToRFSI'] ;
		//}else{
		//$rf = $row['CprFToRFSR'] - $row['CprGToRFSI'];
	  // }
	  $objResponse->addAssign('rf','innerHTML',number_format( $rf,2)); //Rec. Fin

	  $objResponse->addAssign('ivarf','innerHTML',number_format($row['CprGToIRF1'],2)); //iva rf

	  $objResponse->addAssign('ivarfad','innerHTML',number_format($row['CprGToIRF2'],2)); //iva r f ad

	  //Nuevo esquema de IIBB
	  $iibb = number_format($row['CprGPIB'] + $row['CprGPIH'] + $row['CprGPIM'],2);

	  $objResponse->addAssign('perc','innerHTML',$iibb); //percepciones

	  $objResponse->addAssign('totalf','innerHTML',number_format($row['CprGTot'],2)); //total
	 //todo listo, ocultar el cuadro de espera
	  $objResponse->addAssign('espera','style.visibility','hidden');
	  $objResponse->addAssign('espera','style.zIndex','-2');
return $objResponse;
}
//**********************************************************************************************************************
function verndc($tipo,$letra,$pvt,$comp)
{
 //ampliar el tiempo de respuesta
  set_time_limit(120);

 $objResponse = new xajaxResponse();

 /*
 Esta pagina recibe como par�metros la clave de factura: tipo, letra, pvt, numero
 y muestra la cabecera, el detalle y las cuotas de la factura.
 */
 //Variables de session
 $SucCod = $_SESSION['suc_actual']; //Codigo de la sucursal remota

 //Conectar al servidor de la sucursal
 $link = Conectar_SQL($SucCod);


	 //Cabecera : Obtengo los datos
	 $result = sqlsrv_query($link, "select SucCod, LCoCod, NCDPVt, NCDTip, NCDLet, NCDNum, NCDCliCod,NCDCliNom,NCDCliCUIT,NCDCliIng,NCDDet1,NCDDet2,NCDCon1,
							NCDCon2, NCDCon3, NCDImp1, NCDImp2, NCDImp3, NCDTotCtd, NCDIpt, NCDPIVA1,NCDPIVA2,NCDIIVA1,NCDIIVA2,NCDTot,NCDObs1,NCDObs2,
						    NCDIVA, TiICod, NCDCliDoc,NCDCliDom, NCDTDoCod,NCDGPIB
 							from CrNotDC where NCDPVt=".$pvt." and NCDTip='".$tipo."' and NCDLet='".$letra."' and NCDNum=".$comp,array(), array('Scrollable' => 'buffered'));

	 if (sqlsrv_num_rows($result) == 0)
		{ die('No hay resultados');}
	 else
		{$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);}

	 //obtengo el nombre del local comercial
	 $resloc = sqlsrv_query($link,"Select LCoNom from SiLocal where LCoCod=".$row['LCoCod']." and SucCod=".$row['SucCod']);
	 $rowloc = sqlsrv_fetch_array($resloc, SQLSRV_FETCH_ASSOC);

	 //primer linea
	 $objResponse->addAssign('NCDSucCod','value',$row['SucCod']);
	 $objResponse->addAssign('NCDSucNom','value',htmlentities($_SESSION['nom_suc_actual']));
	 $objResponse->addAssign('NCDLocCod','value',$row['LCoCod']);
	 $objResponse->addAssign('NCDLocNom','value',$rowloc['LCoNom']);

	 //segunda linea

	 $objResponse->addAssign('NCDpvt','value',$row['NCDPVt']);
	 $objResponse->addAssign('NCDnum','value',$row['NCDNum']);
	 $objResponse->addAssign('NCDlet','value',$row['NCDLet']);
	 $objResponse->addAssign('NCDtip','value',$row['NCDTip']);
	 $res_aux = sqlsrv_query($link, "select TiCDes from SITICOM where TiCCod='".$row['NCDTip']."'");
	 if ($row_aux = sqlsrv_fetch_array($res_aux, SQLSRV_FETCH_ASSOC))
	   { $objResponse->addAssign('NCDtipdes','value',saca_acentos($row_aux['TiCDes']));}

	//tercera linea
	 $objResponse->addAssign('NCDCliCod','value',$row['NCDCliCod']);
	 $objResponse->addAssign('NCDCliNom','value',htmlentities($row['NCDCliNom']));

	//cuarta linea
	 $objResponse->addAssign('NCDCliDom','value',htmlentities($row['NCDCliDom']));

	//5 linea
	 $objResponse->addAssign('NCDtiva','value',$row['TiICod']);
	 //buscar desc tipo de iva
	 $res_aux = sqlsrv_query($link, "select  TiIDes from SITIIVA where TiICod=".$row['TiICod']);
	 $row_aux = sqlsrv_fetch_array($res_aux, SQLSRV_FETCH_ASSOC);
	 $objResponse->addAssign('NCDtivadesc','value',$row_aux['TiIDes']);

	 $objResponse->addAssign('NCDcuit','value',$row['NCDCliCUIT']);
	 $objResponse->addAssign('NCDib','value',$row['NCDCliIng']);

	//6 linea
	 $objResponse->addAssign('NCDtdoc','value',$row['NCDTDoCod']);
	 //buscar desc tipo docume
	 $res_aux = sqlsrv_query($link,"select  TDoDes from SITIDOC where TDoCod=".$row['NCDTDoCod']);
	 $row_aux = sqlsrv_fetch_array($res_aux, SQLSRV_FETCH_ASSOC);
	 $objResponse->addAssign('NCDtdocdesc','value',$row_aux['TDoDes']);

	 $objResponse->addAssign('NCDdoc','value',$row['NCDCliDoc']);
     $objResponse->addAssign('NCDiva','value',$row['NCDIVA']);

	 //7 linea
     $objResponse->addAssign('NCDdet1','value',htmlentities($row['NCDDet1']));
	 //8 linea
     $objResponse->addAssign('NCDdet2','value',htmlentities($row['NCDDet2']));

	 //9 linea
     $objResponse->addAssign('NCDcon1','value',htmlentities($row['NCDCon1']));
     $objResponse->addAssign('NCDimp1','value',$row['NCDImp1']);
	 //10 linea
     $objResponse->addAssign('NCDcon2','value',htmlentities($row['NCDCon2']));
     $objResponse->addAssign('NCDimp2','value',$row['NCDImp2']);
	 //11 linea
     $objResponse->addAssign('NCDcon3','value',htmlentities($row['NCDCon3']));
     $objResponse->addAssign('NCDimp3','value',$row['NCDImp3']);

	 //12 linea
     $objResponse->addAssign('NCDtotcon','value',$row['NCDTotCtd']);
	 //13 linea
     $objResponse->addAssign('NCDobs1','value',$row['NCDObs1']);
     $objResponse->addAssign('NCDimp','value',$row['NCDIpt']);
	 //14 linea
     $objResponse->addAssign('NCDpiva1','value',$row['NCDPIVA1']);
     $objResponse->addAssign('NCDiva1','value',$row['NCDIIVA1']);
	 //15 linea
     $objResponse->addAssign('NCDobs2','value',$row['NCDObs2']);
     $objResponse->addAssign('NCDpiva2','value',$row['NCDPIVA2']);
     $objResponse->addAssign('NCDiva2','value',$row['NCDIIVA2']);
	 //16 linea
     $objResponse->addAssign('NCDper','value',$row['NCDGPIB']);
	 //17 linea
     $objResponse->addAssign('NCDtotal','value',$row['NCDTot']);


	 //todo listo, ocultar el cuadro de espera
	  $objResponse->addAssign('espera','style.visibility','hidden');
	  $objResponse->addAssign('espera','style.zIndex','-2');
return $objResponse;
}
//**********************************************************************************************************************
function verrecibo($rectip,$recnum)
{
 //ampliar el tiempo de respuesta
  set_time_limit(120);

 $objResponse = new xajaxResponse();
 //Variables de session
 $SucCod = $_SESSION['suc_actual']; //Codigo de la sucursal remota
 $CliCod = $_SESSION['cliente_actual']; //Cliente actual

 //Conectar al servidor de la sucursal
 $link = Conectar_SQL($SucCod);

     //Cabecera del recibo
     $result = sqlsrv_query($link,"select SucCod, LCoCod, RecTip, RecNum, RecFec, RecCaj, UsuCod, RecTot, RecAnu
                           from cbrecca where RecTip='".$rectip."' and RecNum=".$recnum,array(), array('Scrollable' => 'buffered'));
     if (sqlsrv_num_rows($result) == 0)
		{ die('No hay resultados');}
	 else
		{$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);}

	 //obtengo el nombre del local comercial
	 $resloc = sqlsrv_query($link, "Select LCoNom from SiLocal where LCoCod=".$row['LCoCod']." and SucCod=".$row['SucCod']);
	 $rowloc = sqlsrv_fetch_array($resloc, SQLSRV_FETCH_ASSOC);


	 //primer linea
	 $objResponse->addAssign('suc','value',$row['SucCod']);
	 $objResponse->addAssign('nom','value',htmlentities($_SESSION['nom_suc_actual']));
	 $objResponse->addAssign('loc','value',$row['LCoCod']);
	 $objResponse->addAssign('lnom','value',$rowloc['LCoNom']);

	 //segunda linea

	 $objResponse->addAssign('RecTip','value',$row['RecTip']);
	 $objResponse->addAssign('RecNum','value',$row['RecNum']);
	 $objResponse->addAssign('fec','value',Convertir_Fecha($row['RecFec']));


	//tercera linea
	 $objResponse->addAssign('UsuCod','value',$row['UsuCod']);
	 $objResponse->addAssign('RecCaj','value',$row['RecCaj']);
	 $objResponse->addAssign('RecAnu','value',$row['RecAnu']);
	 $objResponse->addAssign('RecTot','value',number_format($row['RecTot'],2));


 // Lineas de Recibo
 $result = sqlsrv_query($link,"select Rec1Suc, Rec1LCo, Rec1Pvt, Rec1Tip, Rec1Let, Rec1Num, Rec1Cuo, Rec1Imp
                        from cbrecli where  RecTip='".$rectip."' and RecNum=".$recnum);
 $respuesta = "<table width='100%'  border='0' cellpadding='0' cellspacing='0' class='tabla_datos'>";

 $respuesta .="<tr class='barra_nav'>
          <td><div align='center'>Suc.</div></td>
          <td><div align='center'>Loc. </div></td>
          <td><div align='center'>Tipo</div></td>
          <td><div align='center'>P.Vta.</div></td>
          <td><div align='center'>Letra</div></td>
          <td><div align='center'>Numero</div></td>
          <td><div align='center'>Cuota</div></td>
          <td><div align='center'>Importe</div></td>
        </tr>";

 while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
  {
      $respuesta .= "<tr>";
      $respuesta .= "<td><div align=center>".$row['Rec1Suc']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['Rec1LCo']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['Rec1Tip']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['Rec1Pvt']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['Rec1Let']."</div></td>";
      $respuesta .= "<td><div align=right>".$row['Rec1Num']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['Rec1Cuo']."</div></td>";
      $respuesta .= "<td><div align=right>".number_format($row['Rec1Imp'],2)."</div></td>";
	  $respuesta .= "</tr>";
  }
 $respuesta .= "</table>";

 $objResponse->addAssign('lineas','innerHTML',$respuesta);

 //monedas de recibo
 $result = sqlsrv_query($link,"select RecLin, a.MonCod, b.MonDes, RecCot, RecImp
                        from cbrecmo a, cpmoned b where
                        a.MonCod = b.MonCod and RecTip='".$rectip."' and RecNum=".$recnum);
 $respuesta = "<table width='100%'  border='0' cellpadding='0' cellspacing='0' class='tabla_datos'>";

 $respuesta .="<tr class='barra_nav'>
          <td><div align='center'>Linea</div></td>
          <td><div align='center'>Moneda</div></td>
          <td><div align='center'>Descripcion</div></td>
          <td><div align='center'>Cotizacion</div></td>
          <td><div align='center'>Importe</div></td>
        </tr>";

 while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC))
  {
      $respuesta .= "<tr>";
      $respuesta .= "<td><div align=center>".$row['RecLin']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['MonCod']."</div></td>";
      $respuesta .= "<td><div align=center>".$row['MonDes']."</div></td>";
      $respuesta .= "<td><div align=center>".number_format($row['RecCot'],2)."</div></td>";
      $respuesta .= "<td><div align=right>".number_format($row['RecImp'],2)."</div></td>";
	  $respuesta .= "</tr>";
  }
 $respuesta .= "</table>";


 $objResponse->addAssign('moneda','innerHTML',$respuesta);

 //todo listo, ocultar el cuadro de espera
 $objResponse->addAssign('espera','style.visibility','hidden');
 $objResponse->addAssign('espera','style.zIndex','-2');

return $objResponse;
}
//**********************************************************************************************************************

?>
