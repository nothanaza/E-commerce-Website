<?php
require 'data.php';

// Get product ID from URL
$id = $_GET['id'] ?? null;
$product = null;

if ($id) {
    foreach ($products as $p) {
        if ($p['id'] === $id) {
            $product = $p;
            break;
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>
    <?= $product ? htmlspecialchars($product['name']) : 'Product Not Found' ?> - Gaming Hardware Store
  </title>
  <link rel="stylesheet" href="style.css" />
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>

<div class="container" style="padding:40px 0">
  <?php if ($product): ?>
    <a href="shop.php" class="back-link">← Back to Shop</a>

    
      <div class="product-detail">

  <!-- LEFT: Image + Thumbnails -->
  <div class="product-detail-img">
    <img id="main-img" src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>">
    <?php if (!$product['in_stock']): ?>
      <div class="badge out">Out of Stock</div>
    <?php elseif ($product['discount']): ?>
      <div class="badge"><?= $product['discount'] ?></div>
    <?php endif; ?>

    <!-- Thumbnails -->
    <div class="thumbs">
      <img src="<?= $product['image'] ?>" alt="thumb 1" onclick="swapImage(this)">
      <img src="<?= $product['image'] ?>" alt="thumb 2" onclick="swapImage(this)">
      <img src="<?= $product['image'] ?>" alt="thumb 3" onclick="swapImage(this)">
    </div>
  </div>

  <!-- RIGHT: Product Info -->
  <div class="product-detail-info">
    <h1><?= htmlspecialchars($product['name']) ?></h1>
    <div class="cat"><?= htmlspecialchars($product['category']) ?></div>

    <!-- Rating -->
    <div class="rating">
      <?= str_repeat("⭐", floor($product['stars'])) ?>
      <?php if ($product['stars'] - floor($product['stars']) >= 0.5): ?>⭐<?php endif; ?>
      <span class="reviews">(<?= $product['reviews'] ?> reviews)</span>
    </div>

    <!-- Price -->
    <div class="price-wrap">
      <div class="price" id="total-price" data-unit="<?= $product['price'] ?>">
  R<?= number_format($product['price'], 2) ?>
</div>
      <?php if ($product['old_price']): ?>
        <div class="old">R<?= number_format($product['old_price'], 2) ?></div>
        <div class="badge save">Save <?= $product['discount'] ?></div>
      <?php endif; ?>
    </div>

    <!-- Short Description -->
    <p class="short-desc">
      Ultimate gaming performance with the latest hardware. 
      Built for serious gamers who demand the best performance in AAA titles and competitive gaming.
    </p>

    <!-- Key Features -->
    <div class="features">
      <h3>Key Features</h3>
      <ul>
        <li>Latest Intel Core i9 processor</li>
        <li>RTX 4080 Graphics Card</li>
        <li>32GB DDR5 RAM</li>
        <li>1TB NVMe SSD</li>
        <li>Liquid cooling system</li>
        <li>RGB lighting</li>
        <li>Pre-installed Windows 11</li>
      </ul>
    </div>

    <!-- Quantity + Add to Cart -->
    <?php if ($product['in_stock']): ?>
      <form method="POST" action="cart.php" class="cart-form">
        <label for="qty">Quantity:</label>
        <div class="qty-control">
          <button type="button" class="qty-bt" onclick="changeQty(-1)">-</button>
          <input type="number" id="qty" name="quantity" value="1" min="1">
          <button type="button" class="qty-bt" onclick="changeQty(1)">+</button>
        </div>

        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <input type="hidden" name="name" value="<?= htmlspecialchars($product['name']) ?>">
        <input type="hidden" name="price" value="<?= $product['price'] ?>">
        <input type="hidden" name="image" value="<?= $product['image'] ?>">

        <div class="cart-actions">
          <button type="submit" class="bt primary">
  🛒 Add to Cart - <span id="button-price">R<?= number_format($product['price'], 2) ?></span>
</button>

          <button type="button" class="bt icon">♡</button>
          <button type="button" class="bt icon">⤴</button>
        </div>
      </form>

      <p class="stock-info in">🟢 In Stock - Ready to Ship</p>
    <?php else: ?>
      <button class="bt disabled" disabled>Out of Stock</button>
    <?php endif; ?>
  </div>
</div>



  <?php else: ?>
    <h2>Product not found ❌</h2>
    <p><a href="shop.php">← Back to shop</a></p>
  <?php endif; ?>
</div>
<!-- Tabs Section -->
<div class="tabs-wrapper">
  <div class="tab-buttons">
    <button class="tab-btn active" onclick="openTab(event, 'tab-specs')">Specifications</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-features')">Features</button>
    <button class="tab-btn" onclick="openTab(event, 'tab-reviews')">Reviews</button>
  </div>

  <div class="tab-panels">

    <!-- Specifications -->
    <div class="tab-panel active" id="tab-specs">
      <div class="spec-grid">
        <div><strong>Switch Type:</strong><span>Mechanical Blue</span></div>
        <div><strong>Key Layout:</strong><span>104-key</span></div>
        <div><strong>Mouse DPI:</strong><span>12000</span></div>
        <div><strong>Polling Rate:</strong><span>1000Hz</span></div>
        <div><strong>Cable:</strong><span>USB 2.0</span></div>
        <div><strong>Compatibility:</strong><span>Windows, Mac, Linux</span></div>
      </div>
    </div>

    <!-- Features -->
    <div class="tab-panel" id="tab-features">
      <h4>Product Features</h4>
      <ul class="feature-list">
        <li>Mechanical Blue Switches</li>
        <li>Per-key RGB Lighting</li>
        <li>12000 DPI Gaming Mouse</li>
        <li>Programmable Macros</li>
        <li>Braided Cables</li>
        <li>Gaming Software Included</li>
      </ul>
    </div>

    <!-- Reviews -->
    <div class="tab-panel" id="tab-reviews">
      <p>⭐ This product is amazing! – by GamerX</p>
      <p>⭐⭐⭐ Solid performance, worth the price. – by ElitePlayer</p>
      <p>⭐⭐⭐⭐⭐ Absolutely love it! – by StreamQueen</p>
    </div>

  </div>
</div>

<!-- Footer -->
  <footer class="bg-black text-white">
    <div class="container mx-auto px-4 py-12">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

        <!-- Company Info -->
        <div class="space-y-4">
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-secondary rounded-lg flex items-center justify-center">
              <span class="text-secondary-foreground font-bold text-sm">TG</span>
            </div>
            <span class="text-xl font-bold">Tech Giants</span>
          </div>
          <p class="text-gray-300 text-sm leading-relaxed">
            South Africa's premier destination for gaming hardware and accessories. 
            We provide cutting-edge technology for serious gamers who demand the best performance.
          </p>
          <div class="space-y-2">
            <div class="flex items-center space-x-2 text-sm text-gray-300">
              <span class="text-secondary">📍</span>
              <span>Pretoria, Gauteng</span>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-300">
              <span class="text-secondary">📞</span>
              <span>+27 21 123 4567</span>
            </div>
            <div class="flex items-center space-x-2 text-sm text-gray-300">
              <span class="text-secondary">✉️</span>
              <span>info@techgiants.co.za</span>
            </div>
          </div>
        </div>

        <!-- About Us -->
        <div class="space-y-4">
          <h3 class="font-semibold text-lg">About Us</h3>
          <div class="space-y-3">
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Our Story</a>
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Why Choose Us</a>
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Gaming Community</a>
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Expert Reviews</a>
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Careers</a>
          </div>
        </div>

        <!-- Quick Links -->
        <div class="space-y-4">
          <h3 class="font-semibold text-lg">Quick Links</h3>
          <div class="space-y-3">
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Gaming PCs</a>
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Graphics Cards</a>
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Gaming Peripherals</a>
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Special Deals</a>
            <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Build Configurator</a>
          </div>
        </div>

        <!-- Social & Support -->
        <div class="space-y-4">
          <h3 class="font-semibold text-lg">Connect With Us</h3>
          <div class="space-y-4">
            <div class="space-y-3">
              <a href="https://instagram.com/techgiants" target="_blank" class="flex items-center space-x-2 text-gray-300 hover:text-secondary transition-colors text-sm group">
                <span class="group-hover:scale-110 transition-transform">📸</span>
                <span>@techgiants</span>
              </a>
              <a href="https://techgiants.co.za" target="_blank" class="flex items-center space-x-2 text-gray-300 hover:text-secondary transition-colors text-sm group">
                <span class="group-hover:scale-110 transition-transform">🌍</span>
                <span>techgiants.co.za</span>
              </a>
              <a href="https://tiktok.com/@techgiants" target="_blank" class="flex items-center space-x-2 text-gray-300 hover:text-secondary transition-colors text-sm group">
                <span class="group-hover:scale-110 transition-transform">🎵</span>
                <span>@techgiants</span>
              </a>
            </div>
            <div class="space-y-2 pt-2">
              <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Customer Support</a>
              <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Warranty Claims</a>
              <a href="#" class="block text-gray-300 hover:text-secondary transition-colors text-sm">Return Policy</a>
            </div>
          </div>
        </div>
      </div>

      <!-- Separator -->
      <div class="my-8 h-px bg-gray-800"></div>

      <!-- Bottom Section -->
      <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
        <div class="flex flex-col md:flex-row items-center space-y-2 md:space-y-0 md:space-x-6 text-sm text-gray-400">
          <p>&copy; 2024 Tech Giants. All rights reserved.</p>
          <div class="flex space-x-4">
            <a href="#" class="hover:text-secondary transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-secondary transition-colors">Terms of Service</a>
            <a href="#" class="hover:text-secondary transition-colors">Shipping Info</a>
          </div>
        </div>
        <div class="flex items-center space-x-2 text-sm text-gray-400">
          <span>Powered by</span>
          <span class="text-secondary font-semibold">Gaming Excellence</span>
        </div>
      </div>

      <!-- Newsletter -->
      <div class="mt-8 p-6 bg-gray-900 rounded-lg">
        <div class="text-center space-y-4">
          <h4 class="font-semibold text-lg">Stay Updated with Tech Giants</h4>
          <p class="text-gray-300 text-sm">
            Get the latest gaming hardware news, exclusive deals, and product launches delivered to your inbox.
          </p>
          <form id="newsletterForm" class="flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
            <input 
              type="email" 
              id="email" 
              placeholder="Enter your email"
              class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:border-secondary"
              required
            />
            <button type="submit" class="px-6 py-2 bg-secondary text-black font-semibold rounded-lg hover:opacity-90 transition">
              Subscribe
            </button>
          </form>
          <p id="message" class="text-sm mt-2"></p>
        </div>
      </div>
    </div>
  </footer>
<script>
function openTab(evt, tabId) {
  // Hide all panels
  document.querySelectorAll('.tab-panel').forEach(panel => panel.classList.remove('active'));
  
  // Remove active from all buttons
  document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));

  // Show selected panel and button
  document.getElementById(tabId).classList.add('active');
  evt.currentTarget.classList.add('active');
}


function changeQty(change) {
  const qtyInput = document.getElementById('qty');
  let qty = parseInt(qtyInput.value) || 1;
  qty += change;
  if (qty < 1) qty = 1;
  qtyInput.value = qty;

  updatePrice(qty);  
}

function updatePrice(qty) {
  const priceEl = document.getElementById('total-price');
  const buttonPriceEl = document.getElementById('button-price'); 

  if (!priceEl || !buttonPriceEl) return;

  const unit = parseFloat(priceEl.dataset.unit);
  if (isNaN(unit)) return;

  const total = unit * qty;
  const formatted = "R" + total.toFixed(2);

  priceEl.textContent = formatted;
  buttonPriceEl.textContent = formatted;
}

tailwind.config = {
      theme: {
        extend: {
          colors: {
            secondary: "#ff6a00", // green
            "secondary-foreground": "#000"
          }
        }
      }
    }
 
    document.getElementById('newsletterForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('email').value;
      const message = document.getElementById('message');

      if (!email.includes('@')) {
        message.textContent = "❌ Please enter a valid email.";
        message.className = "text-red-500 text-sm mt-2";
      } else {
        message.textContent = "✅ Thank you for subscribing!";
        message.className = "text-green-500 text-sm mt-2";
        document.getElementById('email').value = "";
      }
    });

</script>


</body>
</html>
