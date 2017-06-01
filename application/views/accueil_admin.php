<!DOCTYPE html>
<html lang="fr">
    
    <head>
    
        <meta charset="utf-8">
        <title>Mon application</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Connexion à mon application">
        <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" />
        <!-- ci-dessous notre fichier CSS -->
        <link rel="stylesheet" type="text/css" href="http://<?php echo base_url(); ?>portailinvite/assets/css/app.css" />
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300" />
        <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Lato:400,700,300" />
        <script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
        <script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </head>
<body>
<div class="container">
<div class="row">
<div class="col-xs-12">
    
    <div class="accueil"> 
        <div class="row">
        <div class="col-xs-12 col-sm-6 col-sm-offset-1">
            <h1>Accueil administrateur de la WI-FI Cheops Invité</h1>
            <h2>Liste des invités enregistré</h2>
            <a class="btn btn-success btn btn-success" href="<?php echo base_url(); ?>index.php/portailinvite/ajouter_invite"><i class="material-icons">Ajouter un invité</i></a>
            <a class="btn btn-success btn btn-success" href="<?php echo base_url(); ?>index.php"><i class="material-icons">Déconnexion</i></a>
            <div name="login" role="form" class="form-horizontal" method="post" accept-charset="utf-8">
                <div class="col-md-6">
            <?php if($invites == array()){?>
				<h3>La liste des invités est vide</h3>
            <?php } else { ?>
                <table class="table table-striped">
                <thead>
                <tr>
                    <th>login</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Adresse mail</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach ($invites as $key => $unInvite) :?>
                            <tr>
									<?php echo "<td>".$unInvite['login']."</td>"; ?>
									<?php echo "<td>".$unInvite['nom']."</td>"; ?>
									<?php echo "<td>".$unInvite['prenom']."</td>"; ?>
                                   <?php echo " <td>".$unInvite['mail']."</td>"; ?>
									<td><a class="btn btn-success btn btn-success" href="<?php echo base_url(); ?>index.php/portailinvite/supprimer_invite/<?php echo $unInvite['login'];?>"><i class="material-icons">supprimer</i></a></td>
                                    <td><a class="btn btn-success btn btn-success" href="<?php echo base_url(); ?>index.php/portailinvite/renvoi_mail/<?php echo $unInvite['login'];?>"><i class="material-icons">Renvoyer un mail</i></a></td>
							</tr>
				    <?php endforeach ?>
                </tbody>
                </table>
                <?php } ?>
                </div>
            </div>
        </div>
        </div>
    </div>    
</div>
</div>
</div>
</body>
</html>
