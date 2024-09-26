<?php require_once('./includes/header.php') ?>
<div class="container py-4">
  <h1>Movies</h1>
  <div class="row">
    <?php
    foreach ($movies as $movie) { ?>
      <div class="col-md-4 mb-4" id="movie0 <?php echo $movie['id']; ?>">
        <?php require('./includes/archive-movie.php') ?>
      </div>
    <?php } ?>
  </div>
</div>
<?php require_once('./includes/footer.php') ?>