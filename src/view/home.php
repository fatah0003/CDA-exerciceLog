<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="index.php?action=saveStudent" method="post">
    <label>Prénom : <input type="text" name="firstname"></label><br>
    <label>Nom : <input type="text" name="lastname"></label><br>
    <label>Date de naissance : <input type="date" name="date_of_birth"></label><br>
    <label>Email : <input type="email" name="email"></label><br>

    <button type="submit">Ajouter</button>
</form>



    <!--  Incluant bouton ajouter et bouton modifier  -->

    <!--  input id pour suppression + bouton suppression  -->


    <?php if(isset($_GET['page']) && $_GET['page'] === 'displayByName'): ?>
        <!-- Afficher résulat unique  -->
    <?php else: ?>
        <h1>Affichage de tout les étudiants : </h1>
        <?php foreach($students as $student): ?>
            <p><?= $student->__toString() ?></p>
        <?php endforeach; ?>
    <?php endif;?>
</body>
</html>