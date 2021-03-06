<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cont_master_setting extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$simkes=$this->load->database('default', TRUE);
		//$simobat=$this->load->database('obat', TRUE);
		
		// cek session
		if ($this->session->userdata('logged_in') == false && $this->session->userdata('id_akses') !== 1) {
			$this->session->unset_userdata();
			$this->session->sess_destroy();
			redirect('login');
		}
		
		$this->load->library('template');
		
		$this->load->library('Datatables');
        $this->load->library('table');
		
		$this->output->set_header('Last-Modified: ' . gmdate("D, d M Y H:i:s") . ' GMT');
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	}
	
	/***MASTER PENGGUNA - setting aplikasi***/
	function pengguna($par1 = '', $par2 = '', $par3 = '')
	{
		if (!$this->session->userdata('logged_in') == true)
		{
			redirect('login');
		}
		
		if ($par1 == 'tambah') {
			$data['id_user'] = '';
			$data['email'] = $this->input->post('email');
			$data['password'] = md5($this->input->post('password'));
			$data['nip'] = $this->input->post('nip');
			$data['nama'] = $this->input->post('nama');
			$data['id_akses'] = $this->input->post('id_akses');
			$data['kd_puskesmas'] = $this->session->userdata('kd_puskesmas');
			
			$this->m_crud->simpan('user', $data);
			$this->session->set_flashdata('flash_message', 'Data user berhasil disimpan!');
			redirect('cont_master_setting/pengguna', 'refresh');
		}
		if ($par1 == 'ubah' && $par2 == 'do_update') {
			$data['email'] = $this->input->post('email');
			//$data['password'] = $this->input->post('password');
			$data['nip'] = $this->input->post('nip');
			$data['nama'] = $this->input->post('nama');
			$data['id_akses'] = $this->input->post('id_akses');
			$data['kd_puskesmas'] = $this->session->userdata('kd_puskesmas');
			
			$this->m_crud->perbaharui('id_user', $par3, 'user', $data);
			$this->session->set_flashdata('flash_message', 'Data user berhasil diperbaharui!');
			redirect('cont_master_setting/pengguna', 'refresh');
			
		} else if ($par1 == 'ubah') {
			$data['edit_pengguna'] = $this->m_crud->get_user_by_id($par2);
		}
		if ($par1 == 'hapus') {
			$this->db->where('id_user', $par2);
			$this->db->delete('user');
			$this->session->set_flashdata('flash_message', 'Data user berhasil dihapus!');
			redirect('cont_master_setting/pengguna', 'refresh');
		}
		
		
		/*	switch($this->input->post('id_akses')){
			case 1: //admin dinkes
				
				$data['kd_puskesmas'] = "DK";
				break;
			case 7: //operator
				
				$data['kd_puskesmas'] = $this->session->userdata('kd_puskesmas');
				break;
		}	*/
			
		
		$data['page_name']  = 'pengguna';
		$data['page_title'] = 'Pengguna';
		//$data['pengguna']	= $this->m_crud->get_all_user_akses();
		
		$tmpl = array('table_open' => '<table id="dyntable" class="table table-bordered">');
        $this->table->set_template($tmpl);
 		$this->table->set_heading('Nama', 'NIP', 'Email', 'Group', 'Nama Instansi','Aksi');
		
		$data['list_grup'] = $this->m_crud->get_list_grup(); 
			
		$this->template->display('pengguna', $data);
		
	}

	/***MASTER GROUP PENGGUNA - setting aplikasi***/
	function group_pengguna($par1 = '', $par2 = '', $par3 = '')
	{
		if (!$this->session->userdata('logged_in') == true)
		{
			redirect('login');
		}
		
		if ($par1 == 'tambah') {
			$data['id_akses'] = $this->input->post('kd_group_pengguna');
			$data['akses'] = $this->input->post('group_pengguna');
			
			$this->m_crud->simpan('akses', $data);
			$this->session->set_flashdata('flash_message', 'Data group pengguna berhasil disimpan!');
			redirect('cont_master_setting/group_pengguna', 'refresh');
		}
		if ($par1 == 'ubah' && $par2 == 'do_update') {
			$data['id_akses'] = $this->input->post('kd_group_pengguna');
			$data['akses'] = $this->input->post('group_pengguna');
			
			$this->m_crud->perbaharui('id_akses', $par3, 'akses', $data);
			$this->session->set_flashdata('flash_message', 'Data group pengguna berhasil diperbaharui!');
			redirect('cont_master_setting/group_pengguna', 'refresh');
			
		} else if ($par1 == 'ubah') {
			$data['edit_group_pengguna'] = $this->m_crud->get_group_pengguna_by_id($par2);
		}
		if ($par1 == 'hapus') {
			$this->db->where('id_akses', $par2);
			$this->db->delete('akses');
			$this->session->set_flashdata('flash_message', 'Data group pengguna berhasil dihapus!');
			redirect('cont_master_setting/group_pengguna', 'refresh');
		}
		
		$data['page_name']  = 'group_pengguna';
		$data['page_title'] = 'Group Pengguna';
		$data['group_pengguna']	= $this->m_crud->get_all_group_pengguna();
		$tmpl = array('table_open' => '<table id="dyntable" class="table table-bordered">');
       		$this->table->set_template($tmpl);
		// Cek menu view berdasarkan user akses
		switch($this->session->userdata('id_akses')){
			case 1: //admin dinkes
				$this->table->set_heading('Kode Akses','Group Pengguna','aksi');
				break;
			case 7: //operator
				$this->table->set_heading('Kode Akses','Group Pengguna');
				break;
		}
 		//$this->table->set_heading('Group Pengguna','Aksi');
		$this->template->display('group_pengguna', $data);
				
	}
	
	public function sinkronisasi(){
		$data['page_name']  = 'sinkronisasi';
		$data['page_title'] = 'Sinkronisasi Data';
			
		$this->template->display('sinkronisasi', $data);
	}
	
	
	
	function setting_puskesmas()
	{
		if ($this->session->userdata('logged_in') == true)
		{
			$text = "SELECT * FROM puskesmas where status ='1' ";
			$hasil = $this->m_crud->manualQuery($text);
			$r = $hasil->num_rows();
			if($r>0){
				foreach($hasil->result() as $t){
					$d['kd_puskesmas']	= $t->kd_puskesmas;
					$d['nm_puskesmas']	= $t->nm_puskesmas;
					$d['alamat']		= $t->alamat;
					
				}
			}else{
					$d['kd_puskesmas'] 	= '';
					$d['nm_puskesmas'] 	= '';
					$d['alamat'] 		= '';
					
			}	
			$d['page_name']  = 'setting';
			$d['page_title'] = 'Setting Puskesmas';
			
			
			$this->template->display('setting_puskesmas', $d, true);
		} else {
			redirect('login');
		}
		
	}
	
	public function simpan()
	{
		
		$cek = $this->session->userdata('logged_in');
		if(!empty($cek)){
		
			$up['kd_puskesmas']	= $this->input->post('kd_puskesmas');
			$up['nm_puskesmas']	= $this->input->post('nm_puskesmas');
			$up['alamat']		= $this->input->post('alamat');
			
			$id['kd_puskesmas']	= $this->input->post('kd_puskesmas');
			
			$data = $this->m_crud->getSelectedData("puskesmas",$id);	
				if($data->num_rows()>0){
					$this->m_crud->updateData("puskesmas",$up);
					echo 'Update data Sukses';
				}else{
					$this->m_crud->insertData("puskesmas",$up);
					echo 'Simpan data Sukses';		
				}
				redirect('cont_master_setting/setting_puskesmas');
		}else{
				header('location:'.base_url());
		}
	
		
	}
	
}
?>