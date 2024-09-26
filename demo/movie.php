<?php require_once('./includes/header.php') ?>
<?php $filtered_movies = array_filter($movies, 'find_movie_by_id'); ?>
<?php if (isset($filtered_movies) && $filtered_movies) {
  $movie = reset($filtered_movies);
} ?>
<?php if (isset($movie) && $movie) { ?>
  <h1><?php echo $movie['title']; ?></h1>
  <div class="container my-5">
    <div class="row">
      <div class="col-md-3">
        <img
          class="card-img-top"
          src="<?php echo $movie['posterUrl']; ?>"
          alt="<?php echo $movie['title']; ?>" />
      </div>
      <div class="col-md-9">
        <p><strong>Director:</strong> <?php echo $movie['director']; ?></p>
        <p><strong>Release Year:</strong> <?php echo $movie['year']; ?>
          <?php $releaseYear = $movie['year'];
          $movieAge = check_old_movie($movie['year']);
          if ($movieAge !== false && $movieAge > 40) {
            echo '<span class = "badge bg-danger ms-2">Old Movie:' .  $movieAge  . 'years</span>';
          }
          ?></p>
        <p><strong>Runtime:</strong>
          <?php $lungimefilm = 142;
          echo runtime_prettier($lungimefilm); ?></p>
        <p><strong>Genres</strong> <?php echo implode(', ', $movie['genres']); ?> </p>
        <p><strong>Plot:</strong> <?php echo $movie['plot']; ?>"</p>
        <p>Cast:</p>
        <ul>
          <?php $actors = explode(', ', $movie['actors']);
          foreach ($actors as $actor) {
            echo '<li>' . $actor . '</li>';
          } ?>
        </ul>
      </div>
    </div>
  <?php } else { ?>
    <h1>Error. There is no movie here.</h1>
    <a href="movies.php" class="btn btn-primary">
      Go back to all movies.
    </a>
  <?php } ?>
  </div>
  <?php require_once('./includes/footer.php') ?>