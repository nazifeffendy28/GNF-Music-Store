<?php /** includes/footer.php */ ?>
</div><!-- /.container -->

<footer class="bg-dark text-light py-4 mt-5">
  <div class="container text-center">
    <p class="mb-1"><i class="bi bi-music-note-beamed"></i> <strong><?= e(STORE_NAME) ?></strong> &mdash; Toko Alat Musik</p>
    <p class="small text-secondary mb-1">Tugas Pemrograman Web 1 &copy; <?= date('Y') ?></p>
    <a href="<?= url('admin/login.php') ?>" class="small text-secondary"><i class="bi bi-shield-lock"></i> Login Admin</a>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/main.js') ?>"></script>
</body>
</html>
