<?php
//--------- Import init -------// 
require('../inc/init.inc.php');

// restriction d'acces, si l'utilisateur n'est pas admin alors il ne doit pas accéder à cette page.'
if(!utilisateur_est_admin())
{
  header("location:../connexion");
  exit();// permet d'arreter l'exécution du site(au cas où une personne malveillante ferait des injections via GET)
}

// mettre en place un controle pour savoir si l'utilisateur veut une suppression d'un produit
if(isset($_GET['action']) && $_GET['action'] == 'suppression' && !empty($_GET['id-article']) && is_numeric($_GET['id-article'])) 
{
  // is_numeric permet de savoir si l'information est bien une valeur numérique sans tenir compte de son type (les informations provenant de GET et de POST sont toujours de type string)
  // on fait une requete pour récupérer les informations de l'article afin de connaitre la photo pour la supprimer
  $id_article = $_GET['id-article'];
  $article_a_supprimer = $pdo->prepare("SELECT * FROM article WHERE id_article = ?");
  $article_a_supprimer->execute([$id_article]);

  $article_a_suppr = $article_a_supprimer->fetch(PDO::FETCH_ASSOC);
  // on vérifie si la photo existe
  if(!empty($article_a_suppr['photo']))
  {
    // on vérifie le chemin si le fichier existe
    $chemin_photo = RACINE_SERVEUR . 'assets/photo/' . $article_a_suppr['photo'];
    //$message .= $chemin_photo;
    if(file_exists($chemin_photo))
    {
      unlink($chemin_photo); // unlink() permet de supprimer un fichhier sur le serveur.
    }
  }
  $pdo->prepare("DELETE FROM article WHERE id_article = ?")->execute([$id_article]);    

  $message .= '<div class="alert alert-success">L\'article ' . $article_a_suppr['titre'] . ' avec l\' id ' . $id_article . ' à été supprimer </div>';

  // on modifie le GET
  $_GET['action'] = 'affichage';

}

//----- déclaration des variable ------//
$idarticle    = '';
$reference    = '';
$categorie    = '';
$titre        = '';
$description  = '';
$couleur      = '';
$taille       = '';
$sexe         = '';
$prix         = '';
$stock        = '';
$photo_bdd    = '';

// déclaration d'un variable de controle
  $erreur       = false;

//------------------------------------------------------------
// RECUPERAATION DES INFORMATION  D'UN ARTICLE A MODDIFIFIER
if(isset($_GET['action']) && $_GET['action'] == 'modification' && !empty($_GET['id-article']) && is_numeric($_GET['id-article'])) 
{
  $id_article = $_GET['id-article'];
  $article_a_modif = $pdo->prepare("SELECT * FROM article WHERE id_article = ?");
  $article_a_modif->execute([$id_article]);

  $article_actuel = $article_a_modif->fetch(PDO::FETCH_ASSOC);

  $idarticle    = $article_actuel['id_article'];
  $reference    = $article_actuel['reference'];
  $categorie    = $article_actuel['categorie'];
  $titre        = $article_actuel['titre'];
  $description  = $article_actuel['description'];
  $couleur      = $article_actuel['couleur'];
  $taille       = $article_actuel['taille'];
  $sexe         = $article_actuel['sexe'];
  $prix         = $article_actuel['prix'];
  $stock        = $article_actuel['stock'];
  // on récupère la photo de l'article dans une nouvelle variable
  $photo_actuelle = $article_actuel['photo'];
}

//-------------------------------------------------------
//----- Traitement du formulaire et enregistrement------//
if(isset($_POST['id_article']) && isset($_POST['reference']) && isset($_POST['categorie']) && isset($_POST['titre']) && isset($_POST['description']) && isset($_POST['couleur']) && isset($_POST['taille']) && isset($_POST['sexe']) && isset($_POST['prix']) && isset($_POST['stock']))
{
  $idarticle    = $_POST['id_article'];
  $reference    = $_POST['reference'];
  $categorie    = $_POST['categorie'];
  $titre        = $_POST['titre'];
  $description  = $_POST['description'];
  $couleur      = $_POST['couleur'];
  $taille       = $_POST['taille'];
  $sexe         = $_POST['sexe'];
  $prix         = $_POST['prix'];
  $stock        = $_POST['stock'];

  //vérifie que le champs reference est fournie.
  if(empty($reference))
  {
    $message .= '<div class="alert alert-danger">La référence du produit doit être renseigner</div>';
    $erreur = true;
  }

  //vérifie que le champs titre est fournie.
  if(empty($titre))
  {
    $message .= '<div class="alert alert-danger">Le titre du produit doit être renseigner</div>';
    $erreur = true;
  }

  // récupération de l'ancienne photo dans le cas d'une modification 
  if(isset($_GET['action']) && $_GET['action'] == "modification")
  {
    if(isset($_POST['ancienne_photo']))
    {
      $photo_bdd = $_POST['ancienne_photo'];
    }
  }


  // vérification si l'utilisateur a chargé une image
  if(!empty($_FILES['photo']['name']))
  {
    // si ce n'est pas vide alors un fichier a bien été chargé via le formulaire.
    
    // on concatène la référence sur le titre afin de ne jamais avoir un fichier avec un nom déja existant sur le serveur.
    $photo_bdd = $reference . '_'  . $_FILES['photo']['name'];

    // vérification de l'extention de l'image (extension acceptées: jpg, jpeg, png, gif)
    $extension = strrchr($_FILES['photo']['name'], '.');// cette fonction prédéfine permet de découper une chaine selon un caractére fournie en 2eme argument (ici le .) Attention, cette fonction découpera la chaine à partir de la derniere occurence du 2eme argument (donc nous renvoie la chaine comprise après le dernier ponit trouvbé)
    // exemple: maphoto.jpg => on récupère.jpeg
    // exemple: maphoto.photo.png => on récupere .png
    // var_dump($extension) 

    // on transforme $extension afin que tous les caracteres soient en minuscule
    $extension = strtolower($extension); 
    // on enlève le .
    $extension = substr($extension, 1); // exemple: .jpg => jpg
    // les extentions acceptées
    $tab_extension_valide = ["jpg", "jpeg", "png", "gif"];
    // nous pouvons donc vérifier si $extention fait partie des valeur autorisé dans $tab_extention_valide.
    $verif_extension = in_array($extension, $tab_extension_valide);

    if($verif_extension && !$erreur)
    {
      // si $verif_extention est égal à true et que $erreur n'est pas egale a true (il n'y a pas eu d'erreur au préalable)
      $photo_dossier = RACINE_SERVEUR . 'assets/photo/' . $photo_bdd;

      copy($_FILES['photo']['tmp_name'], $photo_dossier);
      // copy() permet de copier un fichier depuis un emplacement fourni en premier argument vers un autre emplacement fourni en deuxieme argument.
    }
    elseif(!$verif_extension) {
      $message .= '<div class="alert alert-danger">Attention, la photo n\' a pas une extension valide (extension acceptées: jpg/ jpeg/ png/ gif)</div>';
      $erreur = true;
    }
  }


  //vérifice s'il n'y'a pas 2 produit identique
    $article_unique = $pdo->prepare("SELECT * FROM article WHERE reference = :reference");
    $article_unique->bindParam(':reference', $reference, PDO::PARAM_STR);
    $article_unique->execute();

    if($article_unique->fetch() && isset($_GET['action']) && $_GET['action'] == 'ajout'){
      $message .= '<div class="alert alert-danger">Ce produit est déja présent dans la Base de donnée</div>';
      $erreur = true;
    } // fin de la condition qui vérifie que l'article est unqiue

  //si il n'a pas d'erreur on passe au traitement BDD
  if(!$erreur){

      if(isset($_GET['action']) && $_GET['action'] == 'ajout')
      {
        //insertion dans  la bdd de l'article
        $req_article = $pdo->prepare("INSERT INTO article (reference, categorie, titre, description, couleur, taille, sexe, prix, stock, photo) VALUES (:reference, :categorie, :titre, :description, :couleur, :taille, :sexe, :prix, :stock, :photo)");
      }elseif (isset($_GET['action']) && $_GET['action'] == 'modification')
      {
        $req_article = $pdo->prepare("UPDATE article SET reference = :reference, categorie = :categorie, titre = :titre, description = :description, couleur = :couleur, taille = :taille, sexe = :sexe, prix = :prix, stock = :stock, photo = :photo WHERE id_article = :id_article");
        $id_article = $_POST['id_article'];
        $req_article->bindParam(":id_article", $id_article, PDO::PARAM_STR);
      }
      
      $req_article->bindParam(':reference',   $reference,   PDO::PARAM_STR);
      $req_article->bindParam(':categorie',   $categorie,   PDO::PARAM_STR);
      $req_article->bindParam(':titre',       $titre,       PDO::PARAM_STR);
      $req_article->bindParam(':description', $description, PDO::PARAM_STR);
      $req_article->bindParam(':couleur',     $couleur,     PDO::PARAM_STR);
      $req_article->bindParam(':taille',      $taille,      PDO::PARAM_STR);
      $req_article->bindParam(':sexe',        $sexe,        PDO::PARAM_STR);
      $req_article->bindParam(':prix',        $prix,        PDO::PARAM_STR);
      $req_article->bindParam(':stock',       $stock,       PDO::PARAM_STR);
      $req_article->bindParam(':photo',       $photo_bdd,   PDO::PARAM_STR);

      $req_article->execute();

      // on redirige grace au GET action
      $_GET['action'] = 'affichage';  
  }// fin condition erreur
}//fin condition du traitement formulaire

//-------  code Teste pour traiter le formulaire----------//
/*$article = [];

$tablepdo = $pdo->query("SELECT * FROM article");
$colcount = $tablepdo->columnCount();

for ($i=0; $i < $colcount; $i++) { 

  $tablemeta = $tablepdo->getColumnMeta($i);
  $champ = $tablemeta['name'];
  if(isset($_POST[$champ])) {
    $article[$champ] = $_POST[$champ];
  } 
}

pre($article);*/

//------ affichage des tous les produit dans un tablea html ----------//

/*$articles = $pdo->query("SELECT * FROM article");
$col_article = $articles->columnCount();*/


//-----------------------------------------------//

//-------------------------------------------------//

//--------- Import du Header et du nav -------//
// l'affichage html commence ici 
include('../inc/header.inc.php');
include('../inc/nav.inc.php');
//----------------------------------------//

?>

<div class="container">

  <div class="starter-template">
    <h1>Gestion Boutique</h1>
    <?= $message; // messages destinés à l'utilisateur ?> 
    <a href="?action=ajout" class="btn btn-warning">Ajouter un produit</a> 
    <a href="?action=affichage" class="btn btn-primary">Afficher un produit</a> 
  </div>

<?php if(isset($_GET['action']) && ($_GET['action'] == 'ajout' || $_GET['action'] == 'modification')) { ?>
  <div class="row">
    <div class="col-sm-8 col-sm-offset-2 well">
      <form action="" method="post" enctype="multipart/form-data">

        <input class="form-control" type="hidden" name="id_article" id="" value="<?= $idarticle?>">
        
        <div class="form-group">
          <label for="reference">Réference produit <span class="text-danger">*</span></label>
          <input class="form-control" type="text" name="reference" id="reference" value="<?= $reference?>">
        </div>

        <div class="form-group">
          <label for="categorie">Categorie</label>
          <select class="form-control" name="categorie" id="categorie">
            <option value="t_shirt">T-Shirt</option>
            <option value="pantalon" <?php if($categorie == 'pantalon') { echo 'selected';} ?>>Pantalon</option>
            <option value="chaussure" <?php if($categorie == 'chaussure') { echo 'selected';} ?>>Chaussure</option>
            <option value="robe" <?php if($categorie == 'robe') { echo 'selected';} ?>>Robe</option>
            <option value="veste" <?php if($categorie == 'veste') { echo 'selected';} ?>>Veste</option>
          </select>
        </div>

        <div class="form-group">
          <label for="titre">Titre du produit : <span class="text-danger">*</span></label>
          <input class="form-control" type="text" name="titre" id="titre" value="<?= $titre?>">
        </div>

        <div class="form-group">
          <label for="description">Déscriprtion du Produit :</label>
          <textarea class='form-control' name="description" id="description" cols="30" rows="5"><?= $description?></textarea>
        </div>

        <div class="form-group">
          <label for="couleur">Couleur</label>
          <select class="form-control" name="couleur" id="couleur">
            <option value="noir">Noir</option>
            <option value="rouge" <?php if($couleur == 'rouge') { echo 'selected';} ?>>Rouge</option>
            <option value="bleu" <?php if($couleur == 'bleu') { echo 'selected';} ?>>Bleu</option>
            <option value="blanc" <?php if($couleur == 'blanc') { echo 'selected';} ?>>Blanc</option>
          </select>
        </div>

        <div class="form-group">
        <label class="title-checkbox">Taille</label>
          <div class="radio">
            <label class="radio-inline">
              <input type="radio" id="inlineCheckbox1" name="taille" value="xs" <?php if($taille == 'xs' OR !$taille) { echo 'checked';} ?>> XS
            </label>
            <label class="radio-inline">
              <input type="radio" id="inlineCheckbox2" name="taille" value="s" <?php if($taille == 's') { echo 'checked';} ?>> S
            </label>
            <label class="radio-inline">
              <input type="radio" id="inlineCheckbox3" name="taille" value="m" <?php if($taille == 'm') { echo 'checked';} ?>> M
            </label>
            <label class="radio-inline">
              <input type="radio" id="inlineCheckbox3" name="taille" value="l" <?php if($taille == 'l') { echo 'checked';} ?>> L
            </label>
            <label class="radio-inline">
              <input type="radio" id="inlineCheckbox3" name="taille" value="xl" <?php if($taille == 'xl') { echo 'checked';} ?>> XL
            </label>
          </div><!-- /.radio-->
        </div><!-- /.form-group-->

        <div class="form-group">
          <label for="sexe">Sexe</label>
          <select class="form-control" name="sexe" id="sexe">
            <option value="m">Homme</option>
            <option value="f" <?php if($sexe == 'f') { echo 'selected';} ?>> Femme</option>
          </select>
        </div>

        <?php 
        // affichage de la photo actuelle dans le cas d'une modification d'article
          if(isset($article_actuel)) // si cette variable existe alors nous sommes dans le cas d'une modification
          {
            echo '<div class="form-group">';
            echo '<label>Photo actuelle</label>';
            echo '<div><img data-action="zoom" src=" ' . URL . 'assets/photo/' . $photo_actuelle . '" class="img-thumbnail" width="210"></div>';
            // on crée un champ caché qui contiendra la nom de la photo afin de le récupérer lors de la validation du formulaire.
            echo '<input type="hidden" name="ancienne_photo" value="' . $photo_actuelle . '">';
            echo '</div>';
          }
        ?>

        <div class="form-group">
          <label for="exampleInputFile">Image</label>
          <input type="file" name="photo" id="exampleInputFile">
          <!--<p class="help-block">Example block-level help text here.</p>-->
        </div>

        <div class="form-group">
          <label for="prix">Prix :</label>
          <div class="input-group">
            <input class="form-control" type="text" name="prix" id="prix" value="<?= $prix?>">
            <div class="input-group-addon">	&euro;</div>
          </div>
        </div>

        <div class="form-group">
          <label for="stock">Stock :</label>
          <input class="form-control" type="text" name="stock" id="stock" value="<?= $stock?>">
        </div>

        <button type="submit" class="btn btn-primary btn-block">Enregistré</button>
       
      </form> 
    </div><!-- /.col-sm-4 -->
  </div><!-- /.row -->
<?php } // accolade correspondante à la condition sur l'affichage de formulaire
        // if(isset($_GET['action']) && $_GET['action'] == 'ajout')
?>

<?php if(isset($_GET['action']) && $_GET['action'] == 'affichage') { 
      
  $articles = $pdo->query("SELECT * FROM article");
  $col_article = $articles->columnCount();

  echo '<div class="row">';
  echo '<table class="table table-hover">';
    // affiche le nom des collones dans mon tableua
    echo '<thead><tr>';
    for ($i=0;$i < $col_article; $i++)
    { 
      $articlemeta = $articles->getColumnMeta($i);

      echo '<th>' . $articlemeta['name'] . '</th>';
    }
    echo '<th>modifié</th>';
    echo '<th>supprimé</th>';
    echo '</tr></thead>';

    echo '<tbody>';
    // récupere les ligne dans la table article de la bdd
    while($article = $articles->fetch(PDO::FETCH_ASSOC))
    {
      echo '<tr>';
        // parcoure chaque collones de la ligne récupéreé de la table article
        foreach ($article as $key => $value) {
          if ($key == 'description') 
          {
            echo '<td>' . substr($value, 0, 56) . '... <a href="#" >voir la fiche produit</a></td>';
          }elseif ($key == 'photo')
          {
            echo '<td><img data-action="zoom"  width=120 src="'. URL . 'assets/photo/' . $value .'"></td>';
          }else
          {
            echo '<td>' . $value . '</td>';
          }
        }
            echo '<td><a href="?action=modification&id-article=' . $article['id_article'] . '"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a></td>';
            echo '<td><a onclick="return(confirm(\'etes-vous sûre de supprimer?\'));" href="?action=suppression&id-article=' . $article['id_article'] . '"><span class="glyphicon glyphicon-trash text-danger" aria-hidden="true"></span></a></td>';   
      echo '</tr>';
    }
    echo '</tbody>';
  echo '</table>';
  echo '</div><!-- /.row-->';

 } // accolade correspondante à la condition sur l'affichage de formulaire
        // if(isset($_GET['action']) && $_GET['action'] == 'affichage')
?>

</div><!-- /.container -->

<?php
//---------  Import footer site -------------//
include('../inc/footer.inc.php');
