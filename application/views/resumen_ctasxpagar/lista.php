<section class="content-header">
	
	  	<form id="exp_excel" style="float:right;padding:0px;margin: 0px;" method="post" action="<?php echo base_url();?>resumen_ctasxpagar/excel/<?php echo $permisos->opc_id?>/<?php echo $fec1?>/<?php echo $fec2?>" onsubmit="return exportar_excel()"  >
        	<input type="submit" value="EXCEL" class="btn btn-success" />
        	<input type="hidden" id="datatodisplay" name="datatodisplay">
       	</form>
       	<input type="button" value="PDF" class="btn btn-warning"  onclick="envio(0,2)" style="float:right;"/>
       	<h1>
        Resumen Cuentas por Pagar <?php echo $titulo?>
      </h1>
</section>
<section class="content">
	<div class="box box-solid">
		<div class="box box-body">
			
			<div class="row">
				<div class="col-md-9">
					<form action="<?php echo $buscar;?>" method="post" id="frm_buscar">
						
					<table width="100%">
						<tr>
							<td><label>Buscar:</label></td>
							<td><input type="text" id='txt' name='txt' class="form-control" style="width: 180px" value='<?php echo $txt?>'/></td>
							<td style="display: none;"><input type="date" id='fec1' name='fec1' class="form-control" style="width: 150px" value='<?php echo $fec1?>' /></td>
							<td><label>Desde:</label></td>
							<td><input type="date" id='fec1' name='fec1' class="form-control" style="width: 150px" value='<?php echo $fec1?>' /></td>
							<td><label>Hasta:</label></td>
							<td><input type="date" id='fec2' name='fec2' class="form-control" style="width: 150px" value='<?php echo $fec2?>' /></td>
							<td><button type="button" class="btn btn-info" onclick="envio(0,0)"><span class="fa fa-search"></span> Buscar</button>
							</td>
						</tr>
					</table>
					</form>
				</div>			
			</div>
			
			<br>
			<div class="row">
				<div class="col-md-12">
					<table id="tbl_list" class="table table-bordered table-list table-hover" width="100%">
						<thead>
							<th>No</th>
							<th>Fecha Pago</th>
							<th>Proveedor</th>
							<th>Documento</th>
							<th>Concepto</th>
							<th>Forma de pago</th>
							<!-- <th>Cuenta</th> -->
							<th>Valor $</th>
							<th>Acciones</th>
						</thead>
						<tbody>
						<?php 
						$n=0;
						$total=0;
						if(!empty($pagos)){
							foreach ($pagos as $pago) {
								$n++;
								
						?>			
							<tr class='success'>
								<td><?php echo $n?></td>		
								<td><?php echo $pago->ctp_fecha_pago?></td>
								<td><?php echo $pago->cli_raz_social?></td>
								<td><?php echo $pago->num_documento?></td>
								<td><?php echo $pago->ctp_concepto?></td>
								<td><?php echo $pago->ctp_forma_pago?></td>
								<!-- <td><?php echo $pago->ctp_banco?></td> -->
								<td class="number"><?php echo str_replace(',', '', number_format($pago->ctp_monto,$dec))?></td>
								<td align="center">
									<div class="btn-group">
										<?php 
							        	if($permisos->rop_reporte){
										?>
											<a href="#" onclick="envio('<?php echo $pago->ctp_id?>',1)" class="btn btn-sm btn-warning" title="RIDE"> <span class="fa fa-file-pdf-o" ></span></a>
										<?php 
										}
										?>
									</div>
								</td>
							</tr>
						<?php
								$total+=round($pago->ctp_monto,$dec);
							}
						}
						?>
								<tr class='success'>
										<td class='total' colspan="5" style='font-weight: bolder;'></td>
										<td class='total' style='font-weight: bolder;'> TOTAL</td>
										<td class='number total' style='font-weight: bolder;'><?php echo str_replace(',','', number_format($total,$dec))?></td>
										<td class='total' style='font-weight: bolder;'> </td>
								</tr>
						</tbody>
					</table>
				</div>	
			</div>
		</div>
	</div>

</section>

<script type="text/javascript">
	function envio(id,opc){
		if(opc==0){
			url='<?php echo $buscar?>';
		}else if(opc==1){
			url="<?php echo base_url();?>resumen_ctasxpagar/show_frame/"+id+"/<?php echo $permisos->opc_id?>";
		}else if(opc==2){
			url="<?php echo base_url();?>resumen_ctasxpagar/show_frame2/<?php echo $permisos->opc_id?>";
		}
		
		$('#frm_buscar').attr('action',url);
		$('#frm_buscar').submit();
	}
</script>
<style type="text/css">
	td{
		padding-top: 2px !important;
		padding-bottom: 2px !important;
	}
</style>

