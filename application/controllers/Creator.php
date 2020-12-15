<?php
class Creator extends CI_Controller{
	//constructor function to load codeigniter helpers
	function __construct(){
		parent::__construct();
		$this->load->helper(array('file', 'url','cookie','form','string', 'date'));
	}
	
	//function to redirect url to a specific function
	public function _remap($page, $action){
		$this->is_logedin();
		if($page == 'video' or $page == 'music' or $page == 'image' or 
			$page == 'app' or $page == 'book' or $page == 'index'){
			if($page == 'index'){
				$this->creator('video', $action);
			}
			else{
				$this->creator($page, $action);
			}
		}
		else if($page == 'ajax'){
			$this->ajax();
		}
		else if($page == 'edit'){
			$this->edit($action);
		}
		else if($page == 'setting'){
			$this->setting($action);
		}
		else{
			show_404();
		}
	}
	
	//function to control creator related functionalities
	public function creator($page, $action){
		$data['page'] = $page;
		$data['type'] = 'creator';
		$creator_id = $this->db->get_where('user', array('user_name' => get_cookie('dcms_username', true)));
		$creator_id = $creator_id->result()[0]->user_id;

		if(count($action) == 0){
			$data['contents'] = $this->db->get_where('content', array('type' => $page, 'user_id' => $creator_id));
			
			$this->load->view('templates/user_header', $data);
			$this->load->view('templates/creator_sidemenu', $data);
			$this->load->view('pages/dashboard/creator/'.$page, $data);
			$this->load->view('templates/footer');
		}
		if(count($action) > 0){
			if($action[0] == 'uploaded'){
				$data['error'] = 'uploaded successfuly';
				$data['contents'] = $this->db->get_where('content', array('type' => $page, 'user_id' => $creator_id));
				
				$this->load->view('templates/user_header', $data);
				$this->load->view('templates/creator_sidemenu', $data);
				$this->load->view('pages/dashboard/creator/'.$page, $data);
				$this->load->view('templates/footer');
			}		
			else if($action[0] == 'upload'){
				$this->upload($page);
			}
		}
	}
	
	//function to proccess data for ajax
	public function ajax(){
		$content_id = $this->input->post('content_id');
		$type = $this->input->post('type');
		$action = $this->input->post('action');
		if($action == 'delete'){
			$content = $this->db->get_where('content', array('content_id' => $content_id))->result()[0];
			$file_name = '';
			
			$this->db->where('content_id', $content_id);
			$this->db->delete('content');
			if($type == 'video'){
				$file_name = './dcms-content/user-content/videos/'.$content->file_name;
			}
			else if($type == 'music'){
				$file_name = './dcms-content/user-content/musics/'.$content->file_name;
			}
			else if($type == 'image'){
				$file_name = './dcms-content/user-content/images/'.$content->file_name;
			}
			else if($type == 'app'){
				$file_name = './dcms-content/user-content/apps/'.$content->file_name;
			}
			else if($type == 'book'){
				$file_name = './dcms-content/user-content/books/'.$content->file_name;
			}
			unlink($file_name);
			unlink('./dcms-content/images/content-thumbnail/'.$content->thumbnail);
			echo 'deleted successfuly';
		}
		else if($action == 'edit'){
			$title = $this->input->post('title');
			$description = $this->input->post('description');
			$tags = $this->input->post('tags');
			$content_info = array();
			if($title != ""){
				$content_info['content_name'] = $title;
			}
			if($description != ""){
				$content_info['description'] = $description;
			}
			$this->db->where('content_id', $content_id);
			$this->db->update('content', $content_info);
			echo 'edited successfuly';
		}
	}
	
	//creator setting function to update creator informations
	public function setting($action){
		$data['page'] = 'setting';
		$data['type'] = 'creator';
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="alert alert-warning">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			', '</div>');
		$this->form_validation->set_rules('username', 'username', 'is_unique[user.user_name]');
		if($this->form_validation->run() == false){
			$this->load->view('templates/user_header', $data);
			$this->load->view('templates/creator_sidemenu', $data);
			$this->load->view('pages/dashboard/creator/setting');
			$this->load->view('templates/footer');
		}
		else{
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$knownfor = $this->input->post('knownfor');
			$phonenumber = $this->input->post('phonenumber');
						
			$cookie_setting = array(
				'name' => '',
				'value' => '',
				'expire' => 86400 * 30,
				'httponly' => true
			);
			$user_info = array();
			$creator_info = array(
				'profession' => $knownfor
			);
			if($username != null){
				$user_info['user_name'] = $username;
			}
			if($password != null){
				$user_info['password'] = md5($password);
				$cookie_setting['name'] = 'dcms_password';
				$cookie_setting['value'] = $user_info['password'];
				set_cookie($cookie_setting);
			}
			if($phonenumber != null){
				$user_info['phone_number'] = $phonenumber;
			}
			$this->db->where('user_name', get_cookie('dcms_username', true));
			$user_id = $this->db->get('user');
			$user_id = $user_id->result()[0]->user_id;
			if($username != null or $password != null){
				$this->db->where('user_id', $user_id);
				$this->db->update('user', $user_info);
			}
			$this->db->where('user_id', $user_id);
			$this->db->update('creator', $creator_info);
			if($username != null){
				$cookie_setting['name'] = 'dcms_username';
				$cookie_setting['value'] = $user_info['user_name'];
				set_cookie($cookie_setting);
			}
			redirect('creator/setting');
		}
	}
	
	//function that process uploaded content
	public function upload($page){
		$data['page'] = $page;
		$data['type'] = 'creator';
		
		$this->load->library('form_validation');
		
		$file_name = random_string('alnum', 32);
		$allowed_type = '';
		if($page == 'video'){
			$allowed_type = 'mp4|webm|ogg';
		}
		if($page == 'music'){
			$allowed_type = 'mp3|wav|ogg';
		}
		if($page == 'image'){
			$allowed_type = 'gif|jpg|png';
		}
		if($page == 'app'){
			$allowed_type = 'zip';
		}
		if($page == 'book'){
			$allowed_type = 'pdf|docx|epub';
		}
		$file_config = array(
			'upload_path' => './dcms-content/user-content/'.$page.'s/',
			'allowed_types' => $allowed_type,
			'file_name' => $file_name
		);
		$file_name = random_string('alnum', 32);
		$thumbnail_config = array(
			'upload_path' => './dcms-content/images/content-thumbnail/',
			'allowed_types' => 'gif|jpg|png',
			'file_name' => $file_name
		);
		$this->load->library('upload', $file_config);
		
		$this->form_validation->set_rules('title', 'title', 'required');
		if($page == 'book'){
			$this->form_validation->set_rules('author', 'author', 'required');
		}
		
		if($this->form_validation->run() == false){
			$this->load->view('templates/user_header', $data);
			$this->load->view('pages/dashboard/creator/upload', $data);
			$this->load->view('templates/footer');
		}
		else{
			$this->db->select('user_id');
			$user_id = $this->db->get_where('user',
				array('user_name' => get_cookie('dcms_username', true)));
			$content_id = random_string('alnum', 7);
			$upload_info = array(
				'content_id' => $content_id,
				'content_name' => $this->input->post('title', true),
				'release_date' => mdate('%Y-%m-%j %H:%I:00'),
				'thumbnail' => '',
				'description' => $this->input->post('description', true),
				'price' => $this->input->post('price' ,true),
				'user_id' => $user_id->result()[0]->user_id,
				'file_name' => '',
				'rating' => 0,
				'tag' => $this->input->post('tags', true),
				'type' => $page
			);
			$extra_info = array(
				'content_id' => $content_id
			);
			$data['error'] = '';
			if(!$this->upload->do_upload('file')){
				$data['error'] = $this->upload->display_errors('<div class="alert alert-warning">','</div>');
				$this->load->view('templates/user_header', $data);
				$this->load->view('pages/dashboard/creator/upload', $data);
				$this->load->view('templates/footer');
			}
			else{
				$upload_info['file_name'] = $this->upload->data('file_name');
				$this->upload->initialize($thumbnail_config);
				if(!$this->upload->do_upload('thumbnail')){
					$upload_info['thumbnail'] = 'default_'.$page.'_thumbnail.jpg';
				}
				else{
					$upload_info['thumbnail'] = $this->upload->data('file_name');
				}
				if($page == 'book'){
					$extra_info['author'] = $this->input->post('author', true);
				}
				if($page == 'app'){
					$extra_info['platform'] = $this->input->post('platform', true);
				}
				$this->db->insert('content', $upload_info);
				$this->db->insert($page, $extra_info);
				//$this->notify($upload_info['content_name'], $upload_info['content_id'], get_cookie('dcms_username', true));
				
				$data['error'] = 'uploaded successfuly';
				redirect('creator/'.$page.'/uploaded/');
			}
		}
	}
	
	// checks whether creator is loged in or not
	public function is_logedin(){
		if(get_cookie('dcms_username',  true) != null){
			$user = $this->db->get_where('user', array('user_name' => get_cookie('dcms_username', true)));
			$user = $user->result();
			if(count($user) > 0){
				if($user[0]->password != get_cookie('dcms_password', true)){					
					delete_cookie("dcms_username");
					delete_cookie("dcms_password");
					delete_cookie("dcms_type");
					redirect('/log_in');
				}
				else if($user[0]->type != 2){
					delete_cookie("dcms_username");
					delete_cookie("dcms_password");
					delete_cookie("dcms_type");
					redirect('/log_in');
				}
			}
			else{									
				delete_cookie("dcms_username");
				delete_cookie("dcms_password");
				delete_cookie("dcms_type");
				redirect('/log_in');
			}
		}
		else{
			redirect('/log_in');
		}
	}
}
?>