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
                if(strcmp($this->input->post('login'),"admin") == 0)
                {
                        if($this->passwordcheck($this->input->post('password'))){
                                $data['invites'] = $this->Invite_model->get_liste_invites();
                                $this->load->vars($data);
                                $this->load->view('accueil_admin');

                        }
                }
                else if($this->logincheck($this->input->post('login')) && $this->passwordcheck($this->input->post('password')))
                {
					if($this->Invite_model->verif_date($this->input->post('login')))
					{
						$this->connexion_wifi($this->input->post('login'),$this->input->post('password'));
					}
					else
					{
						$this->load->view('compte_invalide');
					}
		}
	}
}
    

    // Fonction qui vérifie que le login est bien dans la base de donnée
    
    public function logincheck($login)
    {
        $data = FALSE;
    	if(($this->Invite_model->login_in($login)) || ($this->Admin_model->login_in($login))){
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
        }else if(($this->Admin_model->password_for_login($this->input->post('login')) != array())){
		if($this->Admin_model->password_for_login($this->input->post('login'))[0]['password'] == $password){
			$data = TRUE;
		}
	}
        return $data;
    }
    
    // Fonction qui charge l'accueil de l'administrateur et les données qui y sont affichées
    
    public function accueil_admin()
    {
        	$data['invites'] = $this->Invite_model->get_liste_invites();
		$this->load->vars($data);
		$this->load->view('accueil_admin');
    }
    
    // Fonction qui permet de supprimer un invité de la base de données
    
    public function supprimer_invite($login=''){
        $this->Invite_model->supprimer_invite($login);
        $this->accueil_admin();
    }
    
    public function connexion_wifi($login,$password){
        redirect("HTTPS://securelogin.arubanetworks.com/cgi-bin/login?user=".$login."&password=".$password."&cmd=authenticate ");
    }
    
    // Fonction qui permet d'ajouter un invité dans la base de données grâce au remplissage d'un formulaire
    
    public function ajouter_invite(){
        $rules = [
						[ 'field'  => 'nom',
						  'label'  => 'Nom',
						  'rules'  => 'required',
						  'errors' => [
						  				'required' => 'Vous devez rentrer un nom.'
						  				]
						],
						[ 'field'  => 'prenom',
						  'label'  => 'Prenom',
						  'rules'  => 'required',
						  'errors' => [
						  				'required' => 'Vous devez rentrer un prénom.'
 						  				]
						],
						[ 'field'  => 'mail',
						  'label'  => 'Mail',
						  'rules'  => 'required',
						  'errors' => [
						  				'required' => 'Vous devez rentrer une adresse mail.'
						  				]
						]
					];
					
			$this->form_validation->set_rules($rules);
			
			if($this->form_validation->run()==FALSE){
				$this->load->view('ajouter_invite');
			}else{
				$resultat = $this->Invite_model->ajouter_invite($_POST['nom'], $_POST['prenom'], $_POST['mail']);
               			 $this->Mail_model->envoi_mail($_POST['nom'],$_POST['prenom'], $_POST['mail'],$resultat['login'],$resultat['password']);
		        $this->accueil_admin();
            }
    }
    
    // Fonction qui gère l'envoi du mail contenant les identifiants de l'invité
    
    public function renvoi_mail($login)
    {
        $invite = $this->Invite_model->get_invite($login);
        foreach ($invite as $key => $unInvite){
            if($unInvite['login'] == $login){
                $nom = $unInvite['nom'];
                $prenom = $unInvite['prenom'];
                $mail = $unInvite['mail'];
                $password = $unInvite['password'];
            }
        }
        $this->Mail_model->envoi_mail($nom,$prenom,$mail,$login,$password);
        $this->accueil_admin();
    }
}
