<?php
if (!function_exists('csrf_token')) {
    function csrf_token(): string { return $_SESSION['csrf_token'] ?? ''; }
}
$title = $title ?? 'ToDo Demo';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title) ?></title>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <div class="brand">
      <img src="/assets/logo.svg" alt="Logo" class="logo">
      <span class="brand-name">ToDo Demo</span>
    </div>
    <nav>
      <?php if (!empty($_SESSION['user'])): ?>
        <span class="welcome">Hi, <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
        <a href="/logout" class="btn btn-link">Logout</a>
      <?php else: ?>
        <a href="/login" class="btn btn-link">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
