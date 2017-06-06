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
			$mail->SMTPAuth = FALSE; // Désactive ou active l'authentification du serveur SMTP => peut poser des problèmes de sécurité
			$mail->Port     = 25;
			//$mail->SMTPSecure = "tls";
			$mail->SMTPAutoTLS = false; //Dans le cas ou on ne met pas d'authentification il est nécessaire de mettre cette ligne car le TLS se met par défaut
			$mail->Username = "stagiaire.nantes@cheops.fr"; // Identifiants du compte SMTP
			$mail->Password = "@Password@"; // Mot de passe du compte SMTP
			$mail->Host     = "10.44.1.1"; // Serveur SMTP
			$mail->Mailer   = "smtp"; 
			$mail->From 	= "stagiaire.nantes@cheops.fr"; // Adresse Mail qui se chargera de l'envoi 
			$mail->FromName = "Cheops Technology"; // Nom d'envoi
			//$mail->AddReplyTo("from email", "PHPPot");
			$adresse_mail_client = $email;
			$mail->AddAddress($adresse_mail_client);
			$mail->Subject = "Nouveau compte Portail Cheops"; //Objet du mail
			$mail->WordWrap   = 80;
			// COntenu du Mail
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
