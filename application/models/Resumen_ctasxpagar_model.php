<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Resumen_ctasxpagar_model extends CI_Model {


	public function lista_buscador_pagos($txt,$f1,$f2,$emp_id){



		$this->db->join('erp_reg_documentos f','f.reg_id=c.reg_id');
		$this->db->join('erp_i_cliente cl','f.cli_id=cl.cli_id');
		$this->db->where("(cl.cli_raz_social like'%$txt%' or ctp_concepto like'%$txt%' or ctp_forma_pago like'%$txt%') and ctp_fecha_pago between '$f1' and '$f2' and reg_estado!=3 and c.emp_id=$emp_id and ctp_estado=1 and ctp_forma_pago!='7' and ctp_forma_pago!='8'", null);
		$this->db->order_by('ctp_fecha_pago','asc');

		$resultado=$this->db->get('erp_ctasxpagar c');
		return $resultado->result();


	}

	public function lista_una_ctaxpagar($id){
		$this->db->from('erp_ctasxpagar c');
		$this->db->join('erp_reg_documentos f','c.reg_id=f.reg_id');
		$this->db->join('erp_i_cliente cl','cl.cli_id=f.cli_id');
		$this->db->join('erp_formas_pago fp','fp.fpg_id=cast(c.ctp_forma_pago as integer)');
		$this->db->where("ctp_id", $id);
		$resultado=$this->db->get();
		return $resultado->row();
	}

	
}

?>