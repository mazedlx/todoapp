<?php $title = 'Login'; include __DIR__ . '/partials/header.php'; ?>
<section class="card">
  <h1>Login</h1>
  <?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="post" action="/login" class="form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <div class="form-group">
      <label for="username">Username</label>
      <input id="username" name="username" type="text" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>
    </div>
    <div class="form-actions">
      <button class="btn btn-primary" type="submit">Login</button>
    </div>
    <p class="hint">Default user: <code>user</code> / <code>secure</code></p>
  </form>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
