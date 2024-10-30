<?php require_once('./includes/header.php') ?>
<?php
if (isset($_GET['s']) && !empty($_GET['s'])) {
    $search = $_GET['s'];
    if (strlen($search) < 3) { ?>
        <h1>Search characters to short</h1>
        <p>Your search must be at least 3 characters long. Please try again.</p>
    <?php } else { ?>
        <h1>Search results for: <strong><?php echo $search; ?></strong> </h1>
    <?php } ?>
    <?php $filtered_movies = array_filter($movies, 'find_movie_by_title'); ?>
    <?php if (count($filtered_movies) === 0) { ?>
        <h1>No results found!</h1>
    <?php } else { ?>
        <div class="row">
            <?php foreach ($filtered_movies as $movie) { ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4" id="movie0-<?php echo $movie['id']; ?>">
                    <?php require('includes/archive-movie.php'); ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
<?php } else { ?>
    <h1>No search characters. Please try again.</h1>
<?php } ?>
<?php require('./includes/search-form.php') ?>
<?php require_once('./includes/footer.php') ?>