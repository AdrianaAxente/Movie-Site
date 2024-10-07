<?php require_once('./includes/header.php'); ?>
<?php require_once('./includes/functions.php'); ?>

<?php
$favorite_counts_file = './assets/movie-favorites.json';
$favorite_counts = [];
if (file_exists($favorite_counts_file)) {
  $favorite_counts = json_decode(file_get_contents($favorite_counts_file), true) ?? [];
}

if (isset($_GET['movie_id'])) {
  $movie_id = intval($_GET['movie_id']);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'favorite') {
  if (isset($_POST['favorite_status'])) {
    $favorite_status = $_POST['favorite_status'];
    if ($favorite_status == 1 && !$favorite) {
      $favorites[] = $movie_id;
      if (isset($favorite_counts[$movie_id])) {
        $favorite_counts[$movie_id]++;
      } else {
        $favorite_counts[$movie_id] = 1;
      }
      $message = "The movei was added to Favorites.";
    } elseif ($favorite_status == 0 && $favorite) {
      if (($key = array_search($movie_id, $favorites)) !== false) {
        unset($favorites[$key]);
        if (isset($favorite_counts[$movie_id]) && $favorite_counts[$movie_id] > 0) {
          $favorite_counts[$movie_id]--;
        }
        $message = "The movie was removed from Favorites.";
      }
    }
    setcookie('favorite_movies', json_encode(array_values($favorites)), time() + (365 * 24 * 60 * 60), "/");
    file_put_contents($favorite_counts_file, json_encode($favorite_counts));
    $favorite = in_array($movie_id, $favorites);
  }

  header("Location: " . $_SERVER['REQUEST_URI']);
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rating') {
  if (isset($_POST['rating'])) {
    $rating_value = intval($_POST['rating']);

    if ($rating_value >= 1 && $rating_value <= 5) {
      $ratings_file = './assets/movie-rating.json';
      $ratings = [];

      if (file_exists($ratings_file)) {
        $json_data = file_get_contents($ratings_file);
        $ratings = json_decode($json_data, true);
        if ($ratings === null) {
          $ratings = [];
        }
      }

      if (!isset($ratings[$movie_id])) {
        $ratings[$movie_id] = [];
      }

      if (isset($ratings[$movie_id][$rating_value])) {
        $ratings[$movie_id][$rating_value] += 1;
      } else {
        $ratings[$movie_id][$rating_value] = 1;
      }

      $json_data = json_encode($ratings, JSON_PRETTY_PRINT);

      if (file_put_contents($ratings_file, $json_data)) {
        $cookie_name = 'voted_movie_' . $movie_id;
        $cookie_value = $rating_value;
        setcookie($cookie_name, $cookie_value, time() + (365 * 24 * 60 * 60), "/");

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
      } else {
        $error_message = "Error. Please try again.";
      }
    }
  }
}

$ratings_file = './assets/movie-rating.json';
$average_rating_formated = 'Not rated yet.';
$total_votes = 0;
$total_score = 0;

if (file_exists($ratings_file)) {
  $json_data = file_get_contents($ratings_file);
  $ratings = json_decode($json_data, true);

  if ($ratings !== null && isset($ratings[$movie_id])) {
    foreach ($ratings[$movie_id] as $score => $count) {
      $total_votes += $count;
      $total_score += $score * $count;
    }

    if ($total_votes > 0) {
      $average_rating = $total_score / $total_votes;
      $average_rating_formated = number_format($average_rating, 2);
    }
  }
}

$cookie_name = 'voted_movie_' . $movie_id;
$has_voted = isset($_COOKIE[$cookie_name]);
$user_rating = $has_voted ? intval($_COOKIE[$cookie_name]) : null;

if (!file_exists($ratings_file) && $has_voted) {
  setcookie($cookie_name, '', time() - 3600, "/");
  $has_voted = false;
  $user_rating = null;
}
?>

<?php if (isset($movie)) { ?>
  <h1><?php echo $movie['title']; ?></h1>
  <div class="text-center my-4">
    <form method="POST">
      <input type="hidden" name="action" value="favorite">
      <input type="hidden" name="favorite_status" value="<?php echo $favorite ? 0 : 1; ?>">
      <button type="submit" class="btn btn-<?php echo $favorite ? 'danger' : 'success'; ?>">
        <?php echo $favorite ? 'Delete from Favorites' : 'Add to Favorites'; ?>
      </button>
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

  <div class="container my-4">
    <h3>Rate the movie:</h3>
    <?php if (!$has_voted): ?>
      <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>
      <form method="POST">
        <input type="hidden" name="action" value="rating">
        <div class="star-rating">
          <input type="radio" id="star5" name="rating" value="5" required>
          <label for="star5" title="5 stars">&#9733;</label>
          <input type="radio" id="star4" name="rating" value="4">
          <label for="star4" title="4 stars">&#9733;</label>
          <input type="radio" id="star3" name="rating" value="3">
          <label for="star3" title="3 stars">&#9733;</label>
          <input type="radio" id="star2" name="rating" value="2">
          <label for="star2" title="2 stars">&#9733;</label>
          <input type="radio" id="star1" name="rating" value="1">
          <label for="star1" title="1 star">&#9733;</label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit Rating</button>
      </form>
    <?php else: ?>
      <div class="alert alert-info" role="alert">
        You rated this movie with: <strong><?php echo $user_rating; ?></strong>.
      </div>
    <?php endif; ?>
  </div>

  <div class="container my-3">
    <?php
    if ($average_rating_formated !== 'Not rated yet.') {
      echo '<h3>Average Rating: ' . $average_rating_formated . ' / 5</h3>';
      echo '<p>' . $total_votes . ' visitors have rated this movie.</p>';
    } else {
      echo '<h3>Be the first to rate this movie.</h3>';
    }
    ?>
  </div>

<?php } else { ?>
  <h1>Error. There is no movie here.</h1>
  <a href="movies.php" class="btn btn-primary">Go back to all movies</a>
<?php } ?>

<?php require_once('./includes/footer.php'); ?>