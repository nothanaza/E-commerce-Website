<style>
/* ================= HEADER ================= */
header {
  background-color: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid #e5e7eb;
}

.dark header {
  background-color: rgba(17, 24, 39, 0.9);
}

/* Buttons */
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 500;
  border-radius: 0.375rem;
  padding: 0.5rem 1rem;
  cursor: pointer;
  transition: background 0.2s, color 0.2s;
  text-decoration: none;
}

.btn-ghost {
  background: transparent;
  color: #374151;
}

.btn-ghost:hover {
  background: #f3f4f6;
}

.dark .btn-ghost {
  color: #f9fafb;
}

.dark .btn-ghost:hover {
  background: rgba(255, 255, 255, 0.1);
}

/* Search Input */
.input {
  width: 100%;
  border: 1px solid #e5e7eb;
  border-radius: 0.375rem;
  padding: 0.5rem 0.75rem;
  font-size: 0.875rem;
  background: #fff;
  color: #111827;
}

.input:focus {
  outline: none;
  border-color: #f97316;
  box-shadow: 0 0 0 2px rgba(249, 115, 22, 0.2);
}

/* Cart Badge */
.badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 1.25rem;
  height: 1.25rem;
  font-size: 0.75rem;
  font-weight: 600;
  border-radius: 9999px;
  padding: 0 0.25rem;
}

.badge-secondary {
  background: #f97316;
  color: #fff;
}

/* Layout helpers */
.container {
  max-width: 1200px;
  margin: 0 auto;
}

.bg-background {
  background: #ffffff;
}

.text-secondary-foreground {
  color: #fff;
}

.bg-secondary {
  background: #f97316;
}

.text-secondary {
  color: #f97316;
}

.cursor-pointer {
  cursor: pointer;
}


</style>

<?php echo "HEADER LOADED"; ?>

<?php
session_start();
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur">
  <div class="container flex h-16 items-center justify-between px-4 mx-auto">
    
    <!-- Logo -->
    <div class="flex items-center space-x-2 cursor-pointer" onclick="window.location.href='index.php'">
      <div class="w-8 h-8 bg-secondary rounded-lg flex items-center justify-center">
        <span class="text-secondary-foreground font-bold text-sm">TG</span>
      </div>
      <span class="text-lg font-bold">Tech Giants</span>
    </div>

    <!-- Navigation -->
    <nav class="hidden md:flex items-center space-x-6">
      <a href="index.php" class="btn btn-ghost">Home</a>
      <a href="shop.php" class="btn btn-ghost">Shop</a>
      <a href="about.php" class="btn btn-ghost">About Us</a>
      <a href="contact.php" class="btn btn-ghost">Contact</a>
    </nav>

    <!-- Search -->
    <div class="hidden md:flex items-center flex-1 max-w-md mx-6">
      <div class="relative w-full">
        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-muted-foreground"></i>
        <input 
          type="text" 
          placeholder="Search products..." 
          class="input pl-10 w-full"
        >
      </div>
    </div>

    <!-- Right Side -->
    <div class="flex items-center space-x-4">
      <!-- Account -->
      <a href="account.php" class="btn btn-ghost hidden md:flex">
        <i class="fas fa-user"></i>
      </a>

      <!-- Cart -->
      <a href="cart.php" class="btn btn-ghost relative">
        <i class="fas fa-shopping-cart"></i>
        <?php if ($cart_count > 0): ?>
          <span class="badge badge-secondary absolute -top-2 -right-2">
            <?php echo $cart_count; ?>
          </span>
        <?php endif; ?>
      </a>

      <!-- Mobile Menu -->
      <button class="btn btn-ghost md:hidden" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>
</header>

<!-- Mobile menu (hidden by default) -->
<nav id="mobile-menu" class="hidden md:hidden bg-background border-b">
  <div class="flex flex-col space-y-2 p-4">
    <a href="index.php" class="btn btn-ghost w-full text-left">Home</a>
    <a href="shop.php" class="btn btn-ghost w-full text-left">Shop</a>
    <a href="about.php" class="btn btn-ghost w-full text-left">About Us</a>
    <a href="contact.php" class="btn btn-ghost w-full text-left">Contact</a>
    <a href="account.php" class="btn btn-ghost w-full text-left">Account</a>
  </div>
</nav>

<script>
function toggleMobileMenu() {
  document.getElementById('mobile-menu').classList.toggle('hidden');
}
</script>
