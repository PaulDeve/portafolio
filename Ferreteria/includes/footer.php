    </main>
        </div>
      </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="/Ferreteria/assets/js/app.js"></script>
    <?php
    if (session_status() == PHP_SESSION_NONE) session_start();
    if (!empty($_SESSION['success'])): ?>
      <script>showToast('success','<?= addslashes($_SESSION['success']) ?>');</script>
    <?php unset($_SESSION['success']); endif; ?>
    <?php if (!empty($_SESSION['error'])): ?>
      <script>showToast('error','<?= addslashes($_SESSION['error']) ?>');</script>
    <?php unset($_SESSION['error']); endif; ?>
  </body>
</html>
