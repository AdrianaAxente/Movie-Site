<?php require_once('./includes/header.php') ?>
<?php
if (isset($_GET['s']) && !empty($_GET['s'])) {
    $search = $_GET['s'];
    if (strlen($search) < 3) { ?>
        <h1>Search characters to short</h1>
        <p>Your search must be at least 3 characters long. Please try again.</p>
    <?php } else { ?>
        <h1>Search results for: <?php echo $search; ?> </h1>
    <?php }
} else { ?>
    <h1>No search characters. Please try again.</h1>
<?php } ?>
<!-- <h1>Search results for: FRAZA_DE_CAUTARE</h1> -->
<?php require('./includes/search-form.php') ?>
<?php require_once('./includes/footer.php') ?>