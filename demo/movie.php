<?php require_once('./includes/header.php'); ?>
<?php require_once('./includes/functions.php'); ?>

<?php
$favorite_counts_file = './assets/movie-favorites.json';
$favorite_counts = [];
if (file_exists($favorite_counts_file)) {
  $favorite_counts = json_decode(file_get_contents($favorite_counts_file), true) ?? [];
}

if (isset($_GET['movie_id'])) {
  $movie_id = $_GET['movie_id'];
  $filtered_movies = array_filter($movies, function ($movie) use ($movie_id) {
    return $movie['id'] == $movie_id;
  });

  if (!empty($filtered_movies)) {
    $movie = reset($filtered_movies);
  } else {
    die("Movie not found.");
  }
} else {
  die("Movie ID is not specified.");
}
$favorites = isset($_COOKIE['favorite_movies']) ? json_decode($_COOKIE['favorite_movies'], true) : [];
$favorite = in_array($movie_id, $favorites);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite_status'])) {
  $favorite_status = $_POST['favorite_status'];
  if ($favorite_status == 1 && !$favorite) {
    $favorites[] = $movie_id;
    echo "The movie was added to Favorites";
    if (isset($favorite_counts[$movie_id])) {
      $favorite_counts[$movie_id]++;
    } else {
      $favorite_counts[$movie_id] = 1;
    }
  } elseif ($favorite_status == 0 && $favorite) {
    if (($key = array_search($movie_id, $favorites)) !== false) {
      unset($favorites[$key]);
      echo "The movie was removed from Favorites";
      if (isset($favorite_counts[$movie_id]) && $favorite_counts[$movie_id] > 0) {
        $favorite_counts[$movie_id]--;
      }
    }
  }
  setcookie('favorite_movies', json_encode($favorites), time() + (365 * 24 * 60 * 60), "/");
  file_put_contents($favorite_counts_file, json_encode($favorite_counts));
  $favorite = in_array($movie_id, $favorites);
}
?>

<?php if (isset($movie)) { ?>
  <h1><?php echo $movie['title']; ?></h1>
  <div class="text-center my-4">
    <form method="POST">
      <input type="hidden" name="favorite_status" value="<?php echo $favorite ? 0 : 1; ?>">
      <button type="submit"><?php echo $favorite ? 'Delete from Favorites' : 'Add to Favorites'; ?></button>
      <span class="badge bg-info"><?php echo $favorite_counts[$movie_id] ?? 0; ?></span>
    </form>
  </div>
  <div class="container my-5">
    <div class="row">
      <div class="col-md-3">
        <img class="card-img-top" src="<?php echo check_poster($movie['posterUrl']); ?>" alt="<?php echo $movie['title']; ?>" />
      </div>
      <div class="col-md-9">
        <p><strong>Director:</strong> <?php echo $movie['director']; ?></p>
        <p><strong>Release Year:</strong> <?php echo $movie['year']; ?></p>
        <p><strong>Runtime:</strong> <?php echo runtime_prettier($movie['runtime']); ?></p>
        <p><strong>Genres:</strong> <?php echo implode(', ', $movie['genres']); ?></p>
        <p><strong>Plot:</strong> <?php echo $movie['plot']; ?></p>
        <p>Cast:</p>
        <ul>
          <?php
          $actors = explode(', ', $movie['actors']);
          foreach ($actors as $actor) {
            echo '<li>' . $actor . '</li>';
          }
          ?>
        </ul>
      </div>
    </div>
  </div>
<?php } else { ?>
  <h1>Error. There is no movie here.</h1>
  <a href="movies.php" class="btn btn-primary">Go back to all movies</a>
<?php } ?>

<?php require_once('./includes/footer.php'); ?>