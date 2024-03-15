<?php

require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$client = new Client();

try {
    $pdo = new PDO("mysql:host=".$_ENV['DB_HOST'].";dbname=".$_ENV['DB_DATABASE'].";charset=utf8", $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "connected database!".PHP_EOL;
    
    $postsResponse = $client->request('GET', 'https://jsonplaceholder.typicode.com/posts');
    
    echo "made request to posts, status: ".$postsResponse->getStatusCode().PHP_EOL;

    $commentsResponse = $client->request('GET', 'https://jsonplaceholder.typicode.com/comments');

    echo "made request to comments, status: ".$commentsResponse->getStatusCode().PHP_EOL;

    $posts = json_decode($postsResponse->getBody()->getContents(), true);
    $comments = json_decode($commentsResponse->getBody()->getContents(), true);

    $sql = "CREATE TABLE IF NOT EXISTS posts (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        userId INT(11) NOT NULL,
        title VARCHAR(255) NOT NULL,
        body LONGTEXT NOT NULL
    );";

    $sql .= "CREATE TABLE IF NOT EXISTS comments (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        postId INT(11) NOT NULL,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        body LONGTEXT NOT NULL
    );";

    $statement = $pdo->prepare($sql);
    $statement->execute();

    echo "tables created".PHP_EOL;

    foreach ($posts as $post) {
        $postSql = "INSERT INTO posts (userId, title, body) VALUES (:userId, :title, :body)";
        $statement = $pdo->prepare($postSql);
        
        $statement->bindValue('userId', $post["userId"]);
        $statement->bindValue('title', $post["title"]);
        $statement->bindValue('body', $post["body"]);
        $statement->execute();
    }

    echo "posts are inserted".PHP_EOL;

    foreach ($comments as $comment) {
        $commentSql = "INSERT INTO comments (postId, name, email, body) VALUES (:postId, :name, :email, :body)";
        $statement = $pdo->prepare($commentSql);
        
        $statement->bindValue('postId', $comment["postId"]);
        $statement->bindValue('name', $comment["name"]);
        $statement->bindValue('email', $comment["email"]);
        $statement->bindValue('body', $comment["body"]);
        $statement->execute();
    }

    echo "comments are inserted".PHP_EOL;
    echo "DONE!";

} catch(Exception $e) {
    echo $e->getMessage();
}