<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

return function (App $app, PDO $pdo) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get('/', function (Request $request, Response $response) {
        $response->getBody()->write('Hello world!');
        return $response;
    });

    $app->get('/posts', function (Request $request, Response $response) use($pdo) {
        try {
            $stmt = $pdo->query("select * from posts");
            if (!$stmt) {
                throw new Exception("Error executing query.");
            }
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$users) {
                throw new Exception("No posts found.");
            }
            $response->getBody()->write(json_encode($users));
            return $response->withHeader('Content-Type', 'application/json');
        } catch(Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->get('/comments', function (Request $request, Response $response) use($pdo) {
        try {
            $stmt = $pdo->query("select * from comments");
            if (!$stmt) {
                throw new Exception("Error executing query.");
            }
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$comments) {
                throw new Exception("No comments found.");
            }
            $response->getBody()->write(json_encode($comments));
            return $response->withHeader('Content-Type', 'application/json');
        } catch(Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });

    $app->get('/posts/{post_id}/comments', function (Request $request, Response $response, $args) use($pdo) {
        try {
            $postId = (int) $args['post_id'];
            $stmt = $pdo->prepare("select * from comments where postId = :postId");
            $stmt->bindValue("postId", $postId);
            $stmt->execute();
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$comments) {
                throw new Exception("No comments found.");
            }
            $response->getBody()->write(json_encode($comments));
            return $response->withHeader('Content-Type', 'application/json');
        } catch(Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    });
};
