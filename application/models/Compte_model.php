<?php

	class compte_model extends CI_Model {
		
		public function __construct(){
			$this->load->database();
		}

		//Fonction qui permet de récupérer tous les types de compte sous forme d'un tableau

		public function get_liste_type_comptes(){
			$data = $this->db->select('*')
				->from('portail_invite.compte')
				->get()
				->result_array();
			return $data;
		}

		//Fonction qui permet de récupérer la durée durant laquelle le compte est actif selon le type de compte

		public function get_duree($type){
			$data = $this->db->select('duree')
			         ->from('portail_invite.compte')
			         ->where('type', $type)
			         ->get()
			         ->result_array();
			return $data;
		}

		//Fonction qui permet d'ajouter un nouveau compte

		public function ajoute_compte($type,$duree){
			$data = array(
				'type' => $type,
				'duree' => $duree
			);
			$this->db->insert('portail_invite.compte',$data);
		}

	}

?>
