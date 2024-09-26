<div class="card">
    <?php echo $movie['id']; ?>
    <img src="<?php echo $movie['posterUrl'] ?>" class="card-img-top" alt="<?php echo $movie['title'] ?>">
    <div class="card-body">
        <h5 class="card-title"><?php echo $movie['title'] ?></h5>
        <p class="card-text"><?php echo $movie['plot'] . '...'; ?></p>
        <a href="movie.php?movie_id=<?php echo $movie['id']; ?>" class="btn btn-primary">Read more</a>
    </div>
</div>