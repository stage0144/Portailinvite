<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administrateur extends CI_Controller {

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
                        $this->load->view('connexion_admin');
                }
                else 
                {
					if($this->logincheck($this->input->post('login')) && $this->passwordcheck($this->input->post('password'))) // On vérifie que le login et le password sont bons
					{
						$data['invites'] = $this->Invite_model->get_liste_invites(); // on charge la liste des invités que l'on exporte vers la vue
                            			$this->load->vars($data);
                            			$this->load->view('accueil_admin');
					}
				}
		}
    

    // Fonction qui vérifie que le login est bien dans la base de donnée
    
    public function logincheck($login)
    {
        $data = FALSE;
    	if(($this->Admin_model->login_in($login)) || ($this->Admin_model->login_in($login))){
        	$data = TRUE;
        }
        return $data;
    }
    
    // Fonction qui vérifie que le mot de passe correspond bien au login
	
	public function passwordcheck($password)
    {
    	$data = FALSE;
		if(($this->Admin_model->password_for_login($this->input->post('login')) != array())){
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
			
			if($this->form_validation->run()==FALSE){ // Le formulaire n'a pas été encore validé
				$data['type'] = $this->Compte_model->get_liste_type_comptes(); // on charge les différents types de comptes pour que l'administrateur puisse choisir
                		$this->load->vars($data);
				$this->load->view('ajouter_invite');
			}else{
				$duree = $this->Compte_model->get_duree($_POST['type']);	
				$resultat = $this->Invite_model->ajouter_invite($_POST['nom'], $_POST['prenom'], $_POST['mail'],$_POST['type'],$duree); // On ajoute dans la BDD l'invité
               			$this->Mail_model->envoi_mail($_POST['nom'],$_POST['prenom'], $_POST['mail'],$resultat['login'],$resultat['password']); // On envoi le mail récapitulatif
		        	$this->accueil_admin();
            }
    }
    
    // Fonction qui gère l'envoi du mail contenant les identifiants de l'invité
    
    public function reinitaliser_compte($login)
    {
        $invite = $this->Invite_model->get_invite($login);
        foreach ($invite as $key => $unInvite){ // On va récupérer toutes les informations du compte à partir du login
            if($unInvite['login'] == $login){
                $nom = $unInvite['nom'];
                $prenom = $unInvite['prenom'];
                $mail = $unInvite['mail'];
                $password = $unInvite['password'];
		$type = $unInvite['type'];
            }
        }
	$duree = $this->Compte_model->get_duree($type); // On récupère la durée durant laquelle le compte est actif selon le type de compte
        $this->Mail_model->envoi_mail($nom,$prenom,$mail,$login,$password); // On renvoi un mail
        $this->Invite_model->reinitialiser_date($login,$duree); // On rajoute la durée correspondant au type de compte pour le réactiver
        $this->accueil_admin();
    }

	public function ajouter_compte(){
		$rules = [
                                                [ 'field'  => 'type',
                                                  'label'  => 'Type',
                                                  'rules'  => 'required',
                                                  'errors' => []
                                                ],
                                                [ 'field'  => 'duree',
                                                  'label'  => 'Duree',
                                                  'rules'  => 'required',
                                                  'errors' => []
						]
               			 ];

		$this->form_validation->set_rules($rules);
               	if($this->form_validation->run()==FALSE){ // Dans le cas où le formulaire n'a pas été encore valider on affiche la vue
                	$this->load->view('ajouter_compte');
                }else{
                   	$resultat = $this->Compte_model->ajoute_compte($_POST['type'], $_POST['duree']); // On insère dans la BDD le nouveau type de compte puis on recharge l'accueil
                        $this->accueil_admin();
            	}
	}
}
?>
