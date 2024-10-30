<div class="card">
    <!-- <?php echo $movie['id']; ?> -->
    <img class="card-img-top" src="<?php echo check_poster($movie['posterUrl']); ?>" alt="<?php echo $movie['title']; ?>" />
    <div class="card-body">
        <h5 class="card-title"><?php echo $movie['title'] ?></h5>
        <p class="card-text"><?php echo substr($movie['plot'], 0, 100) . '...'; ?></p>
        <a href="movie.php?movie_id=<?php echo $movie['id']; ?>" class="btn btn-primary">Read more</a>
    </div>
</div>