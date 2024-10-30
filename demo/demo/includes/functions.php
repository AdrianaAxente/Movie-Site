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

function find_movie_by_title($movie)
{
    if (!isset($_GET['s'])) return false;
    if (stripos($movie['title'], $_GET['s']) === false) {
        return false;
    } else {
        return true;
    }
}

function find_movie_by_genre($movie)
{
    if (!isset($_GET['genre'])) return false;
    if (in_array($_GET['genre'], $movie['genres'])) {
        return true;
    } else {
        return false;
    }
}

function check_poster($posterUrl)
{
    $ch = curl_init($posterUrl);

    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);

    curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code == 200) {
        return $posterUrl;
    } else {
        return './assets/no-image.png';
    }
}
function connect_db()
{
    $servername = "localhost";
    $username = "php-user";
    $password = "php-password";
    $dbname = "php-proiect";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Error connecting: " . $conn->connect_error);
    }

    return $conn;
}
