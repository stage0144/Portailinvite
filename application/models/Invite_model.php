<?php 
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
			         
			if ($data != null) // Vu que l'on a récupérer dans un tableau les données correspondant au login, si celui-ci est null alors le login n'est pas présent dans la BDD
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
			//On va supprimer l'invité dans la BDD invite, mais aussi dans celle du radius
			$this->db->where('login', $login);
			$this->db->delete('portail_invite.invite');
			$this->db->where('username', $login);
			$this->db->delete('radius.radcheck');
		}
        
        
        // Fonction qui récupère sous forme de tableau tous les invités enregistré dans la BDD
        
		public function get_liste_invites()
        {
			$data = $this->db->select('*')
				->from('portail_invite.invite')
				->get()
				->result_array();
			return $data;
		}
        
        // Fonction qui ajoute dans la BDD un invité. A partir du nom et du prénom, le mot de passe et le login seront générés
        
		public function ajouter_invite($nom,$prenom,$mail,$type,$duree)
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
			$test = 7;
			$data = array(
				'nom' => $nom,
				'prenom' => $prenom,
				'mail' => $mail,
				'login' => $login,
				'type' => $type,
				'password' => $password,
				'date_inscription' => date("d-m-Y"), //On récupère la date actuelle
				'date_desactivation' => date("d-m-Y",mktime(0, 0, 0, date("m")  , date("d")+$duree[0]['duree'], date("Y"))) //On prend la date actuelle à laquelle on rajoute le nombre de jours correspondant au type de compte
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
	         
			if($data != NULL) // si c'est null le login n'est pas présent dans la BDD 
			{
				$date_courante = date("d-m-Y"); // On récupère la date actuelle
				$date_fin = $data[0]['date_desactivation']; 
				$djour = explode("-", $date_courante);  // On va créer un tableau avec les dates en utilisant le "-" comme séparateur
				$dfin = explode("-" , $date_fin); 
				$auj = $djour[2].$djour[1].$djour[0]; // On inverse ensuite les éléments du tableau pour former un nombre qui nous permettra de comparer les dates
				$finab = $dfin[2].$dfin[1].$dfin[0];
				if ($auj>$finab)
				{
					return false; //dans ce cas la le compte est inactif
				}
				else
				{
					return true; //le compte est encore actif
				}
			}
		}
		
		// Fonction permettant de réinitialiser la date d'inscription d'un invité pour lui redonner accès à la wifi dans la journée
		
		public function reinitialiser_date($login,$duree)
		{
			$data = array(
				'date_desactivation' => date("d-m-Y",mktime(0, 0, 0, date("m")  , date("d")+$duree[0]['duree'], date("Y"))) // On rajoute tant de jours en fonction du type de compte
			);			
			$this->db->where('login', $login);
			$this->db->update('portail_invite.invite', $data);
		}
    }
?>
