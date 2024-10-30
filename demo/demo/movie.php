<?php require_once('./includes/header.php'); ?>
<?php require_once('./includes/functions.php'); ?>

<?php
// Favorites
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

$has_voted = isset($_COOKIE['voted_movie_' . $movie_id]);
$favorites = isset($_COOKIE['favorite_movies']) ? json_decode($_COOKIE['favorite_movies'], true) : [];
$favorite = in_array($movie_id, $favorites);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  if ($_POST['action'] === 'favorite') {
    if (isset($_POST['favorite_status'])) {
      $favorite_status = $_POST['favorite_status'];
      if ($favorite_status == 1 && !$favorite) {
        $favorites[] = $movie_id;
        $favorite_counts[$movie_id] = ($favorite_counts[$movie_id] ?? 0) + 1;
        $message = "The movie was added to Favorites.";
      } elseif ($favorite_status == 0 && $favorite) {
        if (($key = array_search($movie_id, $favorites)) !== false) {
          unset($favorites[$key]);
          $favorite_counts[$movie_id] = max(0, ($favorite_counts[$movie_id] ?? 1) - 1);
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

  // Ratings

  if ($_POST['action'] === 'rating') {
    if (isset($_POST['rating'])) {
      $rating_value = intval($_POST['rating']);
      if ($rating_value >= 1 && $rating_value <= 5) {
        $ratings_file = './assets/movie-rating.json';
        $ratings = file_exists($ratings_file) ? json_decode(file_get_contents($ratings_file), true) : [];
        $ratings[$movie_id] = $ratings[$movie_id] ?? [];

        $ratings[$movie_id][$rating_value] = ($ratings[$movie_id][$rating_value] ?? 0) + 1;
        file_put_contents($ratings_file, json_encode($ratings, JSON_PRETTY_PRINT));

        setcookie('voted_movie_' . $movie_id, $rating_value, time() + (365 * 24 * 60 * 60), "/");
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
      }
    }
  }

  // Reviews

  if ($_POST['action'] === 'review') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    $consent = isset($_POST['consent']);
    if ($consent && !empty($name) && !empty($email) && !empty($message)) {
      $db = new mysqli('localhost', 'php-user', 'php-password', 'php-proiect');
      if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
      }
      $db->query("CREATE TABLE IF NOT EXISTS reviews (
                id INT AUTO_INCREMENT PRIMARY KEY,
                movie_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY unique_review (movie_id, email)
            )");
      $stmt = $db->prepare("SELECT * FROM reviews WHERE movie_id = ? AND email = ?");
      $stmt->bind_param("is", $movie_id, $email);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
        $error_message = "You have already submitted a review for this movie.";
      } else {
        $stmt = $db->prepare("INSERT INTO reviews (movie_id, name, email, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $movie_id, $name, $email, $message);
        if ($stmt->execute()) {
          $success_message = "Review submitted successfully!";
          $review_submitted = true;
        } else {
          $error_message = "Error submitting review. Please try again.";
        }
      }

      $stmt->close();
      $db->close();
    } else {
      $error_message = "Please fill in all fields and accept data processing.";
    }
  }
}
$db = new mysqli('localhost', 'php-user', 'php-password', 'php-proiect');
if ($db->connect_error) {
  die("Connection failed: " . $db->connect_error);
}

$stmt = $db->prepare("SELECT name, message FROM reviews WHERE movie_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$reviews = $stmt->get_result();
?>

<?php if (isset($movie)) { ?>
  <h1><?php echo $movie['title']; ?></h1>
  <div class="text-center my-4">
    <form method="POST">
      <input type="hidden" name="action" value="favorite">
      <input type="hidden" name="favorite_status" value="<?php echo $favorite ? 0 : 1; ?>">
      <button type="submit" class="btn btn-<?php echo $favorite ? 'danger' : 'success'; ?>">
        <?php echo $favorite ? 'Remove from Favorites' : 'Add to Favorites'; ?>
      </button>
      <span class="badge bg-info"><?php echo $favorite_counts[$movie_id] ?? 0; ?></span>
    </form>
  </div>

  <div class="container my-5">
    <div class="row">
      <div class="col-md-3">
        <img class="card-img-top" src="<?php echo check_poster($movie['posterUrl']); ?>" alt="<?php echo ($movie['title']); ?>" />
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
            echo '<li>' . ($actor) . '</li>';
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
        <button type="submit" class="btn btn-primary">Submit Rating</button>
      </form>
    <?php else: ?>
      <div class="alert alert-success" role="alert">
        Thank you for rating this movie!
      </div>
    <?php endif; ?>
  </div>

  <div class="container my-4">
    <h3>Reviews:</h3>
    <?php if (isset($success_message)): ?>
      <div class="alert alert-success" role="alert">
        <?php echo $success_message; ?>
      </div>
    <?php elseif (isset($error_message)): ?>
      <div class="alert alert-danger" role="alert">
        <?php echo $error_message; ?>
      </div>
    <?php endif; ?>

    <?php if (!isset($review_submitted)): ?>
      <h4>Leave a review:</h4>
      <form method="POST">
        <input type="hidden" name="action" value="review">
        <div class="mb-3">
          <label for="name" class="form-label">Your Name</label>
          <input type="text" class="form-control" name="name" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Your Email</label>
          <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
          <label for="message" class="form-label">Your Review</label>
          <textarea class="form-control" name="message" required></textarea>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="consent" required>
          <label class="form-check-label">I consent to the processing of my data</label>
        </div>
        <button type="submit" class="btn btn-primary">Submit Review</button>
      </form>
    <?php endif; ?>
  </div>

  <div class="container my-4">
    <h4>All Reviews:</h4>
    <ul class="list-group">
      <?php while ($row = $reviews->fetch_assoc()): ?>
        <li class="list-group-item">
          <strong><?php echo ($row['name']); ?>:</strong> <?php echo ($row['message']); ?>
        </li>
      <?php endwhile; ?>
    </ul>
  </div>
<?php } else { ?>
  <h1>Movie not found</h1>
<?php } ?>

<?php require_once('./includes/footer.php'); ?>