<section class="content" style="margin-top:-30px" class="page-break">
    <table width="100%">
        <tr>
            <td colspan="2">
                <table width="100%" style="margin-right: -10px;">
                    <tr>
                        <td> </td>
                        <td> </td>
                        <td width="10%"><img src="<?php echo base_url() . 'imagenes/' . $empresa->emp_logo ?>"
                                width="130px" height="70px"></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th width="130%" class="titulo_p" style="text-align: center;">COMPROBANTE DE EGRESO</th>
        </tr>
        <tr>
            <td>

            </td>

            <td width="40%" class="sub_titulo"><?php echo utf8_encode('N°: ')?>
                <label class="sub_titulo" style="color:red">
                    <?php echo $cuenta->ctp_secuencial?>
                </label>
            </td>

        </tr>
    </table>
    

    <br>
    <br>

    <table width="100%" id="encabezado3">
        <tbody>
            <tr>
                <th class="left">Fecha de pago:</th>
                <td class="left"><?php echo $cuenta->ctp_fecha_pago?></td>
            </tr>
            <tr>    
                <th class="left">Cliente/Proveedor:</th>
                <td class="left"><?php echo $cuenta->cli_raz_social?></td>
            </tr>
            <tr>    
                <th class="left">Total a pagar:</th>
                <td class="left"><?php echo number_format($cuenta->ctp_monto,$dec)?></td>
            </tr>
            <tr>    
                <th class="left">Concepto de pago:</th>
                <td class="left"><?php echo $cuenta->ctp_concepto?></td>
            </tr>
            <tr>    
                <th class="left">Forma de pago:</th>
                <td class="left"><?php echo $cuenta->fpg_descripcion?></td>
            </tr>
            <tr>    
                <th class="left">Banco:</th>
                <td class="left"><?php echo $banco->pln_descripcion?></td>
                <th class="left">Cheque/No.Doc:</th>
                <td class="left"><?php echo $cuenta->num_documento?></td>
            </tr>
        </tbody>
    </table>    
    <br>
    <table id="detalle" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th style="width:10%">Codigo</th>
                <th style="width:30%">Cuenta</th>
                <th>Concepto</th>
                <th>Debe</th>
                <th>Haber</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $td=0;
            $th=0;
            $n=0;
            foreach ($det_asiento as $det) {
                $n++;
                ?>
                <tr>
                    <td>
                        <?php echo $n ?>
                    </td>
                    <td style="width:10%">
                        <?php echo $det->pln_codigo ?>
                    </td>
                    <td style="width:30%">
                        <?php echo ucwords(strtolower($det->pln_descripcion)) ?>
                    </td>
                    <td>
                        <?php echo ucwords(strtolower($det->con_concepto)) ?>
                    </td>
                    <td class="numerico">
                        <?php echo number_format($det->vdebe, $dec) ?>
                    </td>
                    <td class="numerico">
                        <?php echo number_format($det->vhaber, $dec) ?>
                    </td>
                </tr>
                <?php
                $td+=round($det->vdebe,$dec);
                $th+=round($det->vhaber,$dec);
            }
            ?>

            <tr>
                <th class="numerico" colspan="4">TOTAL</th>
                <th class="numerico">
                    <?php echo number_format($td, $dec) ?>
                </th>
                <th class="numerico">
                    <?php echo number_format($th, $dec) ?>
                </th>
            </tr>
            
        </tbody>


    </table>
    <center>
        <table width="100%">
            <tr>
                    <td><br><br><br></td>
                    <td><br><br><br></td>
                    <td><br><br><br></td>
                </tr>
                <tr>
                    <td>_______________________________</td>
                    <td>_______________________________</td>
                    <td>_______________________________</td>

                </tr>
                <tr>
                    <td>AUTORIZADO</td>
                    <td>CONTADOR</td>
                    <td>CONTABILIZADO</td>
                </tr>
        </table>
    </center>



    <style type="text/css">
    *,
    label {
        font-size: 15px;
        font-family: "calibri", "nromal";
        margin-left: 6px;
        margin-right: 20px;
        justify-content: right;

    }



    .numerico {
        text-align: right;
    }

    #encabezado3 {
        border-top: 1px solid;
        border-bottom: 1px solid;
        text-align: left;
    }

    /*#encabezado3 tr {
        border-top: 1px solid;
        border-bottom: 1px solid;
        text-align: left;
    }*/

    /*#detalle{
        border-collapse: collapse;
    }*/

    #encabezado2 tr,
    #encabezado2 th,
    #encabezado2 td {
        font-weight: bold;
        justify-content: right;

    }



    #encabezado1 td,
    #encabezado1 th {
        text-align: left;
        font-size: 12px;
        font-weight: bold;

    }

    #encabezado3 td,
    #encabezado3 th {
        text-align: left;
        /*font-size: 12px;*/

    }

    #detalle td,
    #detalle th {
        /*border: 1px solid;
        border-color: #ffffff;
         background:#d7d7d7; */
        border-right: 2px solid #d7d7d7 !important;
        border-top: 2px solid #d7d7d7 !important;
        border-bottom: 2px solid #d7d7d7 !important;
        border-left: 2px solid #d7d7d7 !important;

    }

    #detalle tr:nth-child(2n-1) td,
    #detalle tr:nth-child(2n-1) th {
        background: #DFDFDF !important;

    }

    #info td,
    #info th,
    #info tr {
        border: none;

        border-right: 2px solid #ffffff !important;
        border-top: 2px solid #ffffff !important;
        border-bottom: 2px solid #ffffff !important;
        border-left: 2px solid #ffffff !important;

    }

    #info {
        background: white !important;
    }

    #pagos {
        border-top: 1px solid;
    }

    .titulo {
        font-size: 20px;
        font-weight: bold;
    }

    .mensaje {
        color: #828282;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        justify-content: right;
        font-weight: bolder;
    }

    .sub_titulo {
        font-size: 16px;
        text-align: center;
        font-family: "calibri", "bold";
    }

    .titulo_p {
        font-size: 19px;
        text-align: center;
        font-family: "calibri", "bold";

    }

    .digito {
        color: red;
    }

    th,
    td {
        padding-top: -5px;
        padding-bottom: 2px;
        padding-left: 3px;
        padding-right: 4px;
    }
    </style>