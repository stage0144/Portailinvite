<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Admin_model extends CI_Model {

        public function __construct(){
                $this->load->database();
        }


// Fonction permettant de vérifier si le login existe dans la BDD

	public function login_in($login)
        {
                $data = $this->db->select('login')
                         ->from('portail_invite.admin')
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

// Fonction vérifiant que le password correspond bien au login dans la BDD

	public function password_for_login($login)
        {
                $data = $this->db->select('password')
                         ->from('portail_invite.admin')
                         ->where('login', $login)
                         ->get()
                         ->result_array();

                return $data;
        }
}
?>
