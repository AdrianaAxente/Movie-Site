<?php require_once('./includes/header.php') ?>
<h1>Genres</h1>
<div class="row">
    <?php foreach ($genres as $genre) {
        echo $genre;
    }
    ?>
</div>
<?php require_once('./includes/footer.php') ?>