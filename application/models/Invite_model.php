<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Invite_model extends CI_Model {
		
	public function __construct(){
		$this->load->database();
	}
        
        // Fonction qui vérifie directement dans la BDD que le mot de passe correspond au login
        
        public function password_for_login($login)
	{
		$data = $this->db->select('password')
		        ->from('portail_invite.invite') // On sélectionne la base de donnée et la table
		        ->where('login', $login) //On filtre en prenant les élements correspondants au login
				->get()
				->result_array(); // On retourne le résultat sous forme d'un tableau
			         
		return $data;
	}
        
        // Fonction qui vérifie directement dans la BDD que le login y est présent
        
        public function login_in($login)
	{
		$data = $this->db->select('login')			        
			 ->from('portail_invite.invite')
		         ->where('login', $login)
		         ->get()
		         ->result_array();
			         
		if ($data != null)
		{
			return true;
		}
		else 
		{
			return false;
		}
	}
        
        // Fonction qui supprime un invité dans la BDD grâce à son login
        
    public function supprimer_invite($login)
	{
		$this->db->where('login', $login);
		$this->db->delete('portail_invite.invite');
		$this->db->where('username', $login);
        $this->db->delete('radius.radcheck');
	}
        
        
        // Fonction qui récupère sous forme de tableau tous les invités enregistré dans la BDD
        
        public function get_liste_invites(){
		$data = $this->db->select('*')
		     ->from('portail_invite.invite')
		     ->get()
		     ->result_array();
        	return $data;
        }
        
        // Fonction qui ajoute dans la BDD un invité. A partir du nom et du prénom, le mot de passe et le login seront générés
        
        public function ajouter_invite($nom,$prenom,$mail)
	{
            
            // construction du login à partir du nom et du prénom et d'un nombre aléatoire
            
		if(strlen($nom) >= 4 && strlen($prenom) >= 4)
            	{
                	$login = $nom[0].$nom[1].$nom[2].$nom[3].$prenom[0].$prenom[1].$prenom[2].$prenom[3].rand(1,99);   
            	}
            	else
            	{
                	$login = $nom.$prenom.rand(1,99); 
            	}
            
            // Génération aléatoire du mot de passe 
            
            	$password = "";
           	 for($i = 0; $i <= 8; $i++)
            	{
                	$random = rand(97,122);
                	$password .= chr($random);
            	}
            
		$data = array(
			'nom' => $nom,
			'prenom' => $prenom,
			'mail' => $mail,
			'login' => $login,
        	'password' => $password,
        	'date_inscription' => $date("d.m.Y")
		);
			
		$this->db->insert('portail_invite.invite',$data);

	    // Enregistrement dans la base de donnée radius 
		
		 $data = array(
                        'username' => $login,
                        'attribute' => "Cleartext-Password",
                        'op' => ":=",
                        'value' => $password
                );

		 $this->db->insert('radius.radcheck',$data);
		
        $resultat['login'] = $login;
		$resultat['password'] = $password;
		return $resultat;
	}
        
        // Fonction permettant de récupérer les informations d'un invité dans la BDD grâce à son login
        
        public function get_invite($login)
        {
            $data = $this->db->select('*')
	         ->from('portail_invite.invite')
	         ->get()
	         ->result_array();
            return $data;
        }
        
        // Fonction permettant de vérifier que le compte invité a été créé dans la journée 
        
        public function verif_date($login)
        {
			$data = $this->db->select('*')
	         ->from('portail_invite.invite')
	         ->where('login', $login)
	         ->get()
	         ->result_array();
	         
	         if($data != NULL)
	         {
					$date_courante = date("d.m.Y");
					$data[0]['date_inscription'];
					if($date_courante != $data[0]['date_inscription']){
						return false;
					}
					else 
					{
						return true;
					}
			 }
		}
		
		// Fonction permettant de réinitialiser la date d'inscription d'un invité pour lui redonner accès à la wifi dans la journée
		
		public function reinitialiser_date($login)
		{
			$data = array(
    			'date_inscription' => date("d.m.Y"),
			);			
			$this->db->where('login', $login);
			$this->db->update('portail_invite.invite', $data);
		}
    }
?>
