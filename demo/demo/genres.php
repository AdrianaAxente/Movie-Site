<?php require_once('./includes/header.php') ?>
<h1>Genres</h1>
<div class="row genres-list">
    <?php foreach ($genres as $genre) { ?>
        <div class="col-12 col-md-6 col-lg-4 mb-4">
            <a href="movies.php?genre=<?php echo $genre; ?>" class="btn btn-outline-primary d-block m-1 d-flex align-items-center justify-content-center text-center">
                <?php echo $genre; ?>
            </a>
        </div>
    <?php } ?>
</div>
<?php require_once('./includes/footer.php') ?>