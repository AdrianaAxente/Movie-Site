<?php require_once('./includes/header.php') ?>
<div class="container py-4">
  <?php
  $movie_list = $movies;
  $title = '';
  $is_favorites_page = isset($_GET['page']) && $_GET['page'] === 'favorites';

  if ($is_favorites_page) {
    $favorites = isset($_COOKIE['favorite_movies']) ? json_decode($_COOKIE['favorite_movies'], true) : [];

    if (!empty($favorites)) {
      $movie_list = array_filter($movies, function ($movie) use ($favorites) {
        return in_array($movie['id'], $favorites);
      });
      $title = 'Favorites ';
    } else {
      $movie_list = [];
      $title = 'Favorites ';
    }
  }

  $is_genre_set = isset($_GET['genre']) && in_array($_GET['genre'], $genres);
  if ($is_genre_set) {
    $movie_list = array_filter($movie_list, 'find_movie_by_genre');
    $title = ($is_favorites_page ? 'Favorites ' : '') . $_GET['genre'] . ' ';
  }
  ?>
  <h1><?php echo $title; ?>Movies</h1>
  <div class="row">
    <?php
    if (!empty($movie_list)) {
      foreach ($movie_list as $movie) { ?>
        <div class="col-md-4 mb-4" id="movie<?php echo $movie['id']; ?>">
          <?php require('./includes/archive-movie.php') ?>
        </div>
      <?php }
    } else {
      if ($is_favorites_page) { ?>
        <div class="col-12">
          <div class="alert alert-warning" role="alert">
            No movie found in your Favorite List<?php echo $is_genre_set ? " din genul '" . ($_GET['genre']) . "'" : ""; ?>.
          </div>
        </div>
        <a href="movies.php" class="btn btn-primary">See all movies</a>
      <?php } elseif ($is_genre_set) { ?>
        <div class="col-12">
          <div class="alert alert-warning" role="alert">
            No movie found with genre '<?php echo ($_GET['genre']); ?>'.
          </div>
        </div>
        <a href="movies.php" class="btn btn-primary">See all movies</a>
      <?php } else { ?>
        <div class="col-12">
          <div class="alert alert-warning" role="alert">
            No movie found.
          </div>
        </div>
        <a href="movies.php" class="btn btn-primary">See all movies</a>
    <?php }
    }
    ?>
  </div>
</div>
<?php require_once('./includes/footer.php') ?>