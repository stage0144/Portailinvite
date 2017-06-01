<?php
class Mail_model extends CI_Model {
		
		public function __construct(){
			$this->load->database();
		}
    
        // Fonction permettant d'envoyer un mail générer automatiquement grâce aux différents paramètres
		
		public function envoi_mail($nom,$prenom,$email,$login,$password)
		{
			require_once(dirname(__FILE__) . '/../../phpmailer/PHPMailerAutoload.php');
			$mail = new PHPMailer();
			$mail->IsSMTP();
			$mail->SMTPDebug = 0;
			$mail->SMTPAuth = FALSE;
			$mail->Port     = 25;
			//$mail->SMTPSecure = "tls";
			$mail->SMTPAutoTLS = false;
			$mail->Username = "stagiaire.nantes@cheops.fr";
			$mail->Password = "@Password@";
			$mail->Host     = "10.44.1.1";
			$mail->Mailer   = "smtp";
			$mail->From 	= "stagiaire.nantes@cheops.fr";
			$mail->FromName = "Cheops Technology";
			//$mail->AddReplyTo("from email", "PHPPot");
			$adresse_mail_client = $email;
			$mail->AddAddress($adresse_mail_client);
			$mail->Subject = "Nouveau compte Portail Cheops";
			$mail->WordWrap   = 80;
			$lien_du_site = "12345";
			$content = "
				<p>Bonjour,<br/>
				   $prenom $nom</p>
				<p>Votre compte pour vous connecter au portail invite Cheops a ete cree :</p> 
                <p>Login : <b>$login</b></p>
				<p>Mot de passe : <b>$password</b></p>   
				<br/>
				<br/>
				<p>Cordialement,<br/>
				<h3>Cheops Technology</h3>";
			$mail->MsgHTML($content);
			$mail->IsHTML(true);
			if(!$mail->Send()) 
			echo "Problem sending email.";
			else 
			echo "email sent.";	
		}
}
			
?>
