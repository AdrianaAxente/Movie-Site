<?php
function runtime_prettier($minutes)
{
    $hours = floor($minutes / 60);
    $minutesleft = $minutes % 60;

    return "{$hours} hours {$minutesleft} minutes";
}

function check_old_movie($year)
{
    $currentYear = date('Y');
    $age = $currentYear - $year;
    return $age;
}

function find_movie_by_id($movie)
{

    if (!isset($_GET['movie_id'])) return false;
    if (intval($_GET['movie_id']) === $movie['id']) {
        return true;
    } else {
        return false;
    }
}
