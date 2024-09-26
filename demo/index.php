<?php require_once('./includes/header.php') ?>
<?php date_default_timezone_set('Europe/Bucharest');
$currentHour = date('H');
if ($currentHour >= 5 && $currentHour < 12) {
  $greeting = "Good morning";
} elseif ($currentHour >= 12 && $currentHour < 18) {
  $greeting = "Good afternoon";
} elseif ($currentHour >= 18 && $currentHour < 22) {
  $greeting = "Good evening";
} else {
  $greeting = "Good night";
}
?>
<div class="container">
  <h1><?php echo $greeting ?></h1>
</div>
<?php require_once('./includes/footer.php') ?>