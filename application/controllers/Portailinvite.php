<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portailinvite extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	
		
	 // Constructeur du contrôleur, il charge les différents modèles
	 
	public function __construct()
	{
		parent::__construct();
		// On charge les différents modèles qui ont été créés
		$this->load->model('Invite_model');
		$this->load->model('Mail_model');
		$this->load->model('Admin_model');
		$this->load->model('Compte_model');
	    $this->load->library('form_validation');
		$this->load->helper('url');
	}
	 
	 // Fonction qui charge la vue correspondant à la page de connexion qui est donc considéré comme l'index
	 
	public function index()
	{
		$rules = [
			[ 'field'  => 'login',
			'label'  => 'Login',
			'rules'  => 'callback_logincheck',
			'errors' => []
			],
			[ 'field'  => 'password',
			'label'  => 'Password',
			'rules'  => 'callback_passwordcheck',
			'errors' => []
			]
		];

		$this->form_validation->set_rules($rules);

		if($this->form_validation->run()==FALSE)
		{
			$this->load->view('connexion');
		}
		else
		{
			if($this->logincheck($this->input->post('login')) && $this->passwordcheck($this->input->post('password'))) // On vérifie que le login et le password sont bons
			{
				if($this->Invite_model->verif_date($this->input->post('login'))) // On vérifie que le compte est encore actif
				{
					$this->connexion_wifi($this->input->post('login'),$this->input->post('password'));
				}
				else
				{
					$this->load->view('compte_invalide');
				}
			}
			else
			{
				$this->load->view('mauvais_identifiants');
			}
		}
	}
    

    // Fonction qui vérifie que le login est bien dans la base de donnée
    
    public function logincheck($login)
    {
        $data = FALSE;
    	if(($this->Invite_model->login_in($login))){
        	$data = TRUE;
        }
        return $data;
    }
    
    // Fonction qui vérifie que le mot de passe correspond bien au login
	
	public function passwordcheck($password)
    {
		$data = FALSE;
    	if(($this->Invite_model->password_for_login($this->input->post('login')) != array())){
    		if($this->Invite_model->password_for_login($this->input->post('login'))[0]['password'] == $password){
    			$data = TRUE;
    		}
        }
        return $data;
    }    

 
    public function connexion_wifi($login,$password){
        redirect("HTTPS://securelogin.arubanetworks.com/cgi-bin/login?user=".$login."&password=".$password."&cmd=authenticate ");
    }
}
