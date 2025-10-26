<?php $title = 'Edit ToDo'; include __DIR__ . '/partials/header.php'; ?>
<section class="card">
  <h1>Edit ToDo</h1>
  <form method="post" class="form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <div class="form-group">
      <label for="title">Title</label>
      <input id="title" name="title" type="text" required value="<?= htmlspecialchars($todo['title'] ?? '') ?>">
    </div>
    <div class="form-group">
      <label for="notes">Notes</label>
      <textarea id="notes" name="notes" rows="6"><?= htmlspecialchars($todo['notes'] ?? '') ?></textarea>
    </div>
    <div class="form-actions">
      <a class="btn" href="/">Cancel</a>
      <button class="btn btn-primary" type="submit">Save</button>
    </div>
  </form>
</section>
<?php include __DIR__ . '/partials/footer.php'; ?>
