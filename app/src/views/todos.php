<?php $title = 'My ToDos'; include __DIR__ . '/partials/header.php'; ?>
<section class="actions card">
  <h1>My ToDos</h1>
  <form method="post" action="/todo/add" class="form inline">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <input type="text" name="title" placeholder="New task" required>
    <input type="text" name="notes" placeholder="Notes (optional)">
    <button class="btn btn-primary" type="submit">Add</button>
  </form>
</section>

<?php if (empty($todos)): ?>
  <p class="muted">No entries yet. Create your first one above!</p>
<?php else: ?>
  <ul class="todo-list">
    <?php foreach ($todos as $t): ?>
      <li class="todo-item <?= $t['is_done'] ? 'done' : '' ?>">
        <div class="todo-main">
          <form method="post" action="/todo/<?= (int)$t['id'] ?>/toggle" class="inline">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <button class="checkbox" title="Toggle done" type="submit"><?= $t['is_done'] ? '✓' : '' ?></button>
          </form>
          <div class="todo-text">
            <div class="todo-title"><?= htmlspecialchars($t['title']) ?></div>
            <?php if (!empty($t['notes'])): ?>
              <div class="todo-notes"><?= nl2br(htmlspecialchars($t['notes'])) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="todo-actions">
          <a class="btn btn-small" href="/todo/<?= (int)$t['id'] ?>/edit">Edit</a>
          <form method="post" action="/todo/<?= (int)$t['id'] ?>/delete" class="inline" onsubmit="return confirm('Really delete?');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <button class="btn btn-danger btn-small" type="submit">Delete</button>
          </form>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php include __DIR__ . '/partials/footer.php'; ?>
