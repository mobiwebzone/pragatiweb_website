<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <span class="navbar-text text-white me-auto">
      👤 <?php echo htmlspecialchars($_SESSION['username']); ?> |
      <?php echo htmlspecialchars($_SESSION['email']); ?>
    </span>
    <a class="btn btn-outline-light ms-auto" href="logout.php">Logout</a>
  </div>
</nav>