<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Resumen_ctasxpagar extends CI_Controller
{

	private $permisos;

	function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('s_login')) {
			redirect(base_url());
		}
		$this->load->library('backend_lib');
		$this->load->model('backend_model');
		$this->permisos = $this->backend_lib->control();
		$this->load->library('form_validation');
		$this->load->model('empresa_model');
		$this->load->model('emisor_model');
		$this->load->model('resumen_ctasxpagar_model');
		$this->load->model('ctasxcobrar_model');
		$this->load->model('reg_factura_model');
		$this->load->model('reg_nota_credito_model');
		$this->load->model('cliente_model');
		$this->load->model('vendedor_model');
		$this->load->model('bancos_tarjetas_model');
		$this->load->model('auditoria_model');
		$this->load->model('menu_model');
		$this->load->model('estado_model');
		$this->load->model('configuracion_model');
		$this->load->model('forma_pago_model');
		$this->load->model('caja_model');
		$this->load->model('opcion_model');
		$this->load->model('plan_cuentas_model');
		$this->load->model('configuracion_cuentas_model');
		$this->load->model('asiento_model');
		$this->load->library('html2pdf');
		$this->load->library('html5pdf');
		$this->load->library('html4pdf');
		$this->load->library('Zend');
		$this->load->library('export_excel');
		$this->load->library("nusoap_lib");

	}

	public function _remap($method, $params = array())
	{

		if (!method_exists($this, $method)) {
			$this->index($method, $params);
		} else {
			return call_user_func_array(array($this, $method), $params);
		}
	}


	public function menus()
	{
		$menu = array(
			'menus' => $this->menu_model->lista_opciones_principal('1', $this->session->userdata('s_idusuario')),
			'sbmopciones' => $this->menu_model->lista_opciones_submenu('1', $this->session->userdata('s_idusuario'), $this->permisos->sbm_id),
			'actual' => $this->permisos->men_id,
			'actual_sbm' => $this->permisos->sbm_id,
			'actual_opc' => $this->permisos->opc_id
		);
		return $menu;
	}


	public function index($opc_id)
	{
		$rst_opc = $this->opcion_model->lista_una_opcion($opc_id);
		$rst_cja = $this->caja_model->lista_una_caja($rst_opc->opc_caja);

		///buscador 
		if ($_POST) {
			$text = trim($this->input->post('txt'));
			$ids = $this->input->post('tipo');
			$f1 = $this->input->post('fec1');
			$f2 = $this->input->post('fec2');
			
			$cns_pagos = $this->resumen_ctasxpagar_model->lista_buscador_pagos($text, $f1, $f2, $rst_cja->emp_id); 
			
		} else {
			$text = '';
			$f1 = date('Y-m-d');
			$f2 = date('Y-m-d');
			$cns_pagos = $this->resumen_ctasxpagar_model->lista_buscador_pagos($text, $f1, $f2, $rst_cja->emp_id);
			
		}
		$conf = $this->configuracion_model->lista_una_configuracion('2');
		$dec = $conf->con_valor;
		$pagos =array();
		foreach ($cns_pagos as $pago) {
			$rst_fp=$this->forma_pago_model->lista_una_forma_pago_id($pago->ctp_forma_pago);
			$rst_cta=$this->plan_cuentas_model->lista_un_plan_cuentas($pago->ctp_forma_pago);
			$forma="";
			if(!empty($rst_fp)){
				$forma=$rst_fp->fpg_descripcion;
			}

			$cuenta="";
			if(!empty($rst_cta)){
				$cuenta=$rst_cta->pln_codigo;
			}
			$det= (object) array(
								'ctp_fecha_pago'=>$pago->ctp_fecha_pago,
								'cli_raz_social'=>$pago->cli_raz_social,
								'num_documento'=>$pago->num_documento,
								'ctp_concepto'=>$pago->ctp_concepto,
								'ctp_forma_pago'=>$forma,
								'ctp_banco'=>$cuenta,
								'ctp_monto'=>$pago->ctp_monto,
								'ctp_id'=>$pago->ctp_id,
								);
			array_push($pagos,$det);
		}
		$data = array(
			'permisos' => $this->permisos,
			'pagos' => $pagos,
			'titulo' => ucfirst(strtolower($rst_cja->emp_nombre)),
			'opc_id' => $rst_opc->opc_id,
			'buscar' => base_url() . strtolower($rst_opc->opc_direccion) . $rst_opc->opc_id,
			'txt' => $text,
			'fec1' => $f1,
			'fec2' => $f2,
			'dec' => $dec,

		);
		$this->load->view('layout/header', $this->menus());
		$this->load->view('layout/menu', $this->menus());
		$this->load->view('resumen_ctasxpagar/lista', $data);
		$modulo = array('modulo' => 'resumen_ctasxpagar');
		$this->load->view('layout/footer_bodega', $modulo);
	}


	public function show_frame($id, $opc_id)
	{
		if ($_POST) {
			$text = trim($this->input->post('txt'));
			$fec1 = $this->input->post('fec1');
			$fec2 = $this->input->post('fec2');
			
		} else {
			$fec1 = date('Y-m-d');
			$fec2 = date('Y-m-d');
			$text = '';
			
		}
		$permisos = $this->backend_model->get_permisos($opc_id, $this->session->userdata('s_rol'));
		$rst_opc = $this->opcion_model->lista_una_opcion($opc_id);
		$rst_cja = $this->caja_model->lista_una_caja($rst_opc->opc_caja);
		if ($permisos->rop_reporte) {
			$data = array(
				'titulo' => 'Resumen Cuentas por Pagar ' . ucfirst(strtolower($rst_cja->emp_nombre)),
				'regresar' => base_url() . strtolower($rst_opc->opc_direccion) . $rst_opc->opc_id,
				'direccion' => "resumen_ctasxpagar/show_pdf/$id/$opc_id",
				'fec1' => $fec1,
				'fec2' => $fec2,
				'txt' => $text,
				'estado' => '',
				'tipo' => '',
				'vencer' => '',
				'vencido' => '',
				'pagado' => '',
				'familia' => '',
				'tip' => '',
				'detalle' => '',
			);
			$this->load->view('layout/header', $this->menus());
			$this->load->view('layout/menu', $this->menus());
			$this->load->view('pdf/frame_fecha', $data);
			$modulo = array('modulo' => 'factura');
			$this->load->view('layout/footer', $modulo);
		}
	}

	public function show_pdf($id, $opc_id)
	{
		$rst_opc = $this->opcion_model->lista_una_opcion($opc_id);
		$rst_cja = $this->caja_model->lista_una_caja($rst_opc->opc_caja);
		$rst_asi = $this->asiento_model->lista_un_asiento_modulo($id,'11');
		$asientos = $this->asiento_model->lista_un_asiento($rst_asi->con_asiento);
		$conf = $this->configuracion_model->lista_una_configuracion('2');
		$dec = $conf->con_valor;

		$cuentas = array();
        $cns_cuentas = $this->asiento_model->lista_detalle_asiento($asientos->con_asiento);
            foreach ($cns_cuentas as $rst_cuentas) {
                if (!empty($rst_cuentas->con_concepto_debe)) {
                    array_push($cuentas, $rst_cuentas->con_concepto_debe . '&' . $rst_cuentas->con_id . '&0');
                }

                if (!empty($rst_cuentas->con_concepto_haber)) {
                    array_push($cuentas, $rst_cuentas->con_concepto_haber . '&' . $rst_cuentas->con_id . '&1');
                }
            }
            //Eliminar Duplicados del Array
            $n = 0;
            $j = 1;
            $td = 0;
            $th = 0;
            $det_asiento=array();
            while ($n < count($cuentas)) {
                $cta = explode('&', $cuentas[$n]);
                $rst_cuentas1 = $this->plan_cuentas_model->lista_un_plan_cuentas($cta[0]);
                $vdebe = 0;
                $vhaber = 0;
                if ($cta[2] == 0) {
                    $rst_v = $this->asiento_model->listar_asientos_debe($asientos->con_asiento, $cta[0], $cta[1]);
                    $vdebe = $rst_v->con_valor_debe;
                    $vhaber = 0;
                } else {
                    $rst_v = $this->asiento_model->listar_asientos_haber($asientos->con_asiento, $cta[0], $cta[1]);
                    $vdebe = 0;
                    $vhaber = $rst_v->con_valor_haber;
                }
                $n++;
                
                $det=(object) array(
                					'pln_codigo'=>$rst_cuentas1->pln_codigo,
                					'pln_descripcion'=>$rst_cuentas1->pln_descripcion,
                					'con_concepto'=>$rst_v->con_concepto,
                					'vdebe'=>$vdebe,
                					'vhaber'=>$vhaber,
                				);
                array_push($det_asiento, $det);
        }
        $cuenta=$this->resumen_ctasxpagar_model->lista_una_ctaxpagar($id);
        $empresa=$this->empresa_model->lista_una_empresa($rst_cja->emp_id);
		$data = array(
			'dec' => $dec,
			'cuenta' => $cuenta,
			'banco' => $this->plan_cuentas_model->lista_un_plan_cuentas($cuenta->ctp_banco),
			'det_asiento' => $det_asiento,
			'empresa' => $empresa
		);


		$this->html4pdf->empresa($empresa->emp_nombre,$empresa->emp_identificacion,$empresa->emp_direccion,$empresa->emp_telefono,$empresa->emp_logo);
		$this->html4pdf->filename('pdf_resumen_ctasxpagar.pdf');
		$this->html4pdf->paper('a4', 'portrait');
    	$this->html4pdf->html(utf8_decode($this->load->view('pdf/pdf_resumen_ctasxpagar', $data, true)));
    	$this->html4pdf->output(array("Attachment" => 0));

		

	}

	public function excel($opc_id, $fec1, $fec2)
	{
		$rst_opc = $this->opcion_model->lista_una_opcion($opc_id);
		$rst_cja = $this->caja_model->lista_una_caja($rst_opc->opc_caja);

		$titulo = 'Resumen Cuentas por Pagar ' . ucfirst(strtolower($rst_cja->emi_nombre));
		$file = "resumen_ctasxpagar" . date('Ymd');
		$data = $_POST['datatodisplay'];
		$this->export_excel->to_excel($data, $file, $titulo, $fec1, $fec2);
	}

	public function show_frame2($opc_id){
		if($_POST){
			$text= trim($this->input->post('txt'));
			$fec1= $this->input->post('fec1');
			$fec2= $this->input->post('fec2');
		}else{
			$fec1=date('Y-m-d');
			$fec2=date('Y-m-d');
			$text='';
		}
		$permisos=$this->backend_model->get_permisos($opc_id,$this->session->userdata('s_rol'));
		$rst_opc=$this->opcion_model->lista_una_opcion($opc_id);
		$rst_cja=$this->caja_model->lista_una_caja($rst_opc->opc_caja);
	
    	if($permisos->rop_reporte){
    		$data=array(
					'titulo'=>'Resumen Cuentas por Pagar '.ucfirst(strtolower($rst_cja->emp_nombre)),
					'regresar'=>base_url().strtolower($rst_opc->opc_direccion).$rst_opc->opc_id,
					'direccion'=>"resumen_ctasxpagar/reporte/$opc_id/$fec1/$fec2/$text",
					'fec1'=>$fec1,
					'fec2'=>$fec2,
					'txt'=>$text,
					'estado'=>'',
					'tipo'=>'',
					'vencer'=>'',
					'vencido'=>'',
					'pagado'=>'',
					'familia'=>'',
					'tip'=>'',
					'detalle'=>'',
				);
			$this->load->view('layout/header',$this->menus());
			$this->load->view('layout/menu',$this->menus());
			$this->load->view('pdf/frame_fecha',$data);
			$modulo=array('modulo'=>'asiento');
			$this->load->view('layout/footer',$modulo);
		}
    }

	public function reporte($opc_id,$f1,$f2,$text=""){

		$rst_opc = $this->opcion_model->lista_una_opcion($opc_id);
		$rst_cja = $this->caja_model->lista_una_caja($rst_opc->opc_caja);
		require_once APPPATH.'third_party/fpdf/fpdf.php';
		$pdf = new FPDF();
	    $pdf->AddPage('L','A4',0);
	    $pdf->AddFont('Calibri-light','');//$pdf->SetFont('Calibri-Light', '', 9);
        $pdf->AddFont('Calibri-bold','');//$pdf->SetFont('Calibri-bold', '', 9);
	    $pdf->AliasNbPages();

	    ///buscador 
		$cns = $this->resumen_ctasxpagar_model->lista_buscador_pagos($text, $f1, $f2, $rst_cja->emp_id);
			
	    $dc=$this->configuracion_model->lista_una_configuracion('2');
	    $dec=$dc->con_valor;
	    $emisor=$this->empresa_model->lista_una_empresa($rst_cja->emp_id);

        $pdf->SetFont('Calibri-light', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(300, 5, utf8_decode($emisor->emp_nombre), 0, 0, 'L');
        $pdf->Ln();
        $pdf->Cell(300, 5, $emisor->emp_identificacion, 0, 0, 'L');
        $pdf->Ln();
        $pdf->Cell(300, 5, $emisor->emp_ciudad."-".$emisor->emp_pais, 0, 0, 'L');
        $pdf->Ln();
        $pdf->Cell(300, 5, utf8_decode("TELÉFONO: " ). $emisor->emp_telefono, 0, 0, 'L');
        $pdf->Ln();
        $pdf->SetX(0);
        $pdf->Cell(190, 5, $pdf->Image('./imagenes/'.$emisor->emp_logo, 250, 4, 25), 0, 0, 'R');
        $pdf->Ln();
        $pdf->Ln();


	    $pdf->SetFont('Calibri-bold', '', 14);
        $pdf->Cell(300, 5, "RESUMEN PAGOS", 0, 0, 'C');
		$pdf->Ln();
		$pdf->Ln();

		$pdf->SetTextColor(0,0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(10, 5, "No.", 'TB', 0, 'C');
        $pdf->Cell(20, 5, "Fecha Pago", 'TB', 0, 'C');
        $pdf->Cell(60, 5, "Proveedor", 'TB', 0, 'C');
        $pdf->Cell(50, 5, "Documento", 'TB', 0, 'C');
        $pdf->Cell(90, 5, "Concepto / Doc.Pago", 'TB', 0, 'C');
        $pdf->Cell(30, 5, "Forma Pago", 'TB', 0, 'C');
        $pdf->Cell(15, 5, "Valor", 'TB', 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 9);
        $n = 0;
        $tot=0;
        foreach ($cns as $rst) {
        	$rst_fp=$this->forma_pago_model->lista_una_forma_pago_id($rst->ctp_forma_pago);
			// $rst_cta=$this->plan_cuentas_model->lista_un_plan_cuentas($pago->ctp_forma_pago);
            $n++;
            $pdf->Cell(10, 5, $n, 0, 0, 'L');
            $pdf->Cell(20, 5, $rst->ctp_fecha_pago, 0, 0, 'L');
            $pdf->Cell(60, 5, substr(utf8_decode($rst->cli_raz_social),0,30), 0, 0, 'L');
            $pdf->Cell(50, 5, $rst->reg_num_documento, 0, 0, 'C');
            $pdf->Cell(90, 5, substr(utf8_decode($rst->ctp_concepto),0,50), 0, 0, 'L');
            $pdf->Cell(30, 5, utf8_decode($rst_fp->fpg_descripcion), 0, 0, 'L');
            $pdf->Cell(15, 5, number_format($rst->ctp_monto, 2), 0, 0, 'R');
            $pdf->Ln();
            $tot+=round($rst->ctp_monto, 2);
            
        } 
        $pdf->SetFont('Arial', 'B', 10);   
        $pdf->Cell(230, 5, "", 'TB', 0, 'C');
        $pdf->Cell(30, 5, "TOTAL", 'TB', 0, 'C');
        $pdf->Cell(15, 5, number_format($tot, 2), 'TB', 0, 'R');
        $pdf->Ln();
    	

	    $pdf->Output('rep_resumen_ctasxpagar.php' , 'I' );
	}    

		
}