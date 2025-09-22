<?php
session_start();
require 'data.php'; 

// Get cart count for header
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}

?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Gaming Hardware Store</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
  <!-- Header -->
    <header class="header">
    <div class="logo" onclick="window.location.href='index.php'">Tech Giants</div>
     <nav class="nav">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
    </nav>

     <div class="user-actions">
            <a href="signin.php" class="account-link">👤 My Account</a>
            <a href="cart.php" class="cart-link">🛒 <span class="cart-badge"><?= htmlspecialchars($cart_count) ?: 0 ?></span></a>
     </div>
    </header>

<!-- HERO -->
<section class="hero">
  <div class="container">
    <h1>Gaming Hardware Store</h1>
    <p>Discover premium gaming equipment from top brands</p>
    <div class="search-wrap">
      <!-- search icon -->
      <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none">
        <path d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z"
              stroke="#9aa0a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      <input id="search" class="search-input" placeholder="Search products..." />
    </div>
  </div>
</section>

<!-- FILTERS -->
<section class="filters">
  <div class="container">
    <div class="filters-inner">
      <div style="display:flex;gap:12px;flex-wrap:wrap">
        <!-- Category -->
        <div class="pill-select">
          <select id="f-category" class="pill">
            <?php foreach($categories as $c): ?>
              <option value="<?= htmlspecialchars($c['id']) ?>"><?= htmlspecialchars($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <!-- chevron -->
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M6 9l6 6 6-6" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <!-- Price -->
        <div class="pill-select">
          <select id="f-price" class="pill">
            <option value="all">All Prices</option>
            <option value="0-5000">Under R5,000</option>
            <option value="5000-15000">R5,000 - R15,000</option>
            <option value="15000-30000">R15,000 - R30,000</option>
            <option value="30000+">Over R30,000</option>
          </select>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M6 9l6 6 6-6" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
        <!-- Sort -->
        <div class="pill-select">
          <select id="f-sort" class="pill">
            <option value="featured">Featured</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
            <option value="name">Name A-Z</option>
            <option value="rating">Highest Rated</option>
          </select>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
            <path d="M6 9l6 6 6-6" stroke="#475569" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </div>
      </div>

      <div class="right-tools">
        <span id="count"><?= count($products) ?> products found</span>
        <div class="view-toggle">
          <button id="btn-grid" class="toggle-btn active" title="Grid view" aria-label="Grid view">
            <!-- grid icon -->
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
              <rect x="3" y="3" width="7" height="7" rx="2"></rect>
              <rect x="14" y="3" width="7" height="7" rx="2"></rect>
              <rect x="3" y="14" width="7" height="7" rx="2"></rect>
              <rect x="14" y="14" width="7" height="7" rx="2"></rect>
            </svg>
          </button>
          <button id="btn-list" class="toggle-btn" title="List view" aria-label="List view">
            <!-- list icon -->
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
              <rect x="3" y="4" width="18" height="3" rx="1"></rect>
              <rect x="3" y="10.5" width="18" height="3" rx="1"></rect>
              <rect x="3" y="17" width="18" height="3" rx="1"></rect>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PRODUCTS -->
<section class="products">
  <div class="container">
    <div id="wrap" class="grid"></div>
  </div>
</section>


<!-- SHOP BY CATEGORY SECTION -->
<section class="categories" style="padding:50px 0">
  <div class="container">
    <h2 style="text-align:center;font-size:28px;font-weight:800;margin-bottom:10px">Shop by Category</h2>
    <p style="text-align:center;color:var(--sub);margin-bottom:30px">Find exactly what you're looking for</p>
    <div class="category-grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;max-width:900px;margin:auto;">
      <?php
        $iconMap = [
          "gaming-pcs"     => "https://img.icons8.com/ios-filled/50/ffffff/computer.png",
          "graphics-cards" => "https://img.icons8.com/ios-filled/50/ffffff/video-card.png",
          "motherboards"   => "https://img.icons8.com/ios-filled/50/ffffff/motherboard.png",
          "monitors"       => "https://img.icons8.com/ios-filled/50/ffffff/monitor.png",
          "peripherals"    => "https://img.icons8.com/ios-filled/50/ffffff/keyboard.png",
          "audio"          => "https://img.icons8.com/ios-filled/50/ffffff/headphones.png"
        ];
        // Get all categories except "All Categories"
        $realCats = array_slice($categories, 1, 6);
        foreach ($realCats as $cat):
          $count = count(array_filter($products, fn($p) => $p['category'] === $cat['id']));
      ?>
        <div class="category-box" data-cat="<?= htmlspecialchars($cat['id']) ?>" style="background:#fff;border:1px solid #eceef2;border-radius:16px;text-align:center;padding:30px 20px;transition:transform 0.25s,box-shadow 0.25s;box-shadow:0 2px 6px rgba(0,0,0,0.05);cursor:pointer;">
          <div class="category-icon" style="background:#ff6600;border-radius:50%;width:60px;height:60px;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;">
            <img src="<?= $iconMap[$cat['id']] ?>" alt="<?= htmlspecialchars($cat['name']) ?> Icon" style="width:26px;height:26px;">
          </div>
          <div class="category-name" style="font-weight:600;color:#222;font-size:16px;margin-bottom:4px;"><?= htmlspecialchars($cat['name']) ?></div>
          <div class="category-count" style="font-size:13px;color:#666;"><?= $count ?> items</div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

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
  
  /* ====== DATA from PHP ====== */
  const PRODUCTS = <?php echo json_encode($products, JSON_UNESCAPED_SLASHES); ?>;

  /* ====== Helpers ====== */
  const fmtRand = v => "R" + Number(v).toLocaleString("en-ZA", {minimumFractionDigits:2, maximumFractionDigits:2});
  const el = sel => document.querySelector(sel);
  const wrap = el("#wrap");
  const countEl = el("#count");
  const searchEl = el("#search");
  const fCat = el("#f-category");
  const fPrice = el("#f-price");
  const fSort = el("#f-sort");
  const btnGrid = el("#btn-grid");
  const btnList = el("#btn-list");
  let view = "grid";

  function starSvg(){
    return `<svg width="18" height="18" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
      <path d="M10 1.5l2.6 5.27 5.82.84-4.21 4.1.99 5.78L10 14.98l-5.2 2.73.99-5.78L1.58 7.61l5.82-.84L10 1.5z" fill="#d1d5db"/>
    </svg>`;
  }

 
  function renderStars(stars){
  const fullStars = Math.floor(stars);
  const halfStar = stars % 1 >= 0.5 ? 1 : 0;
  const emptyStars = 5 - fullStars - halfStar;
  let html = '';
  for(let i=0; i<fullStars; i++) html += starSvg();
  if(halfStar) html += starSvgHalf();
  for(let i=0; i<emptyStars; i++) html += starSvgEmpty();
  return `<div class="stars">${html}</div>`;
}

function starSvg(){
  return `<svg width="18" height="18" viewBox="0 0 20 20"><path d="M10 1.5l2.6 5.27 5.82.84-4.21 4.1.99 5.78L10 14.98l-5.2 2.73.99-5.78L1.58 7.61l5.82-.84L10 1.5z" fill="#fbbf24"/></svg>`;
}
function starSvgHalf(){
  return `<svg width="18" height="18" viewBox="0 0 20 20"><defs><linearGradient id="half"><stop offset="50%" stop-color="#fbbf24"/><stop offset="50%" stop-color="#d1d5db"/></linearGradient></defs><path d="M10 1.5l2.6 5.27 5.82.84-4.21 4.1.99 5.78L10 14.98l-5.2 2.73.99-5.78L1.58 7.61l5.82-.84L10 1.5z" fill="url(#half)"/></svg>`;
}
function starSvgEmpty(){
  return `<svg width="18" height="18" viewBox="0 0 20 20"><path d="M10 1.5l2.6 5.27 5.82.84-4.21 4.1.99 5.78L10 14.98l-5.2 2.73.99-5.78L1.58 7.61l5.82-.84L10 1.5z" fill="#d1d5db"/></svg>`;
}

  function cardHTML(p, isList=false){
    const sale = p.old_price && p.price < p.old_price;
    const cat = p.category;
    const out = !p.in_stock;

    const topBadge = out
      ? `<div class="badge out">Out of Stock</div>`
      : (p.discount ? `<div class="badge">${p.discount}</div>` : ``);

    const rating = `
      <div style="display:flex;align-items:center;gap:8px;margin-top:2px">
       ${renderStars(p.stars)}
      <span class = "reviews">(${p.reviews})</span> 
        
      </div>`;

    const price = `
      <div class="price-wrap">
        <div class="price">${fmtRand(p.price)}</div>
        ${sale ? `<div class="old">${fmtRand(p.old_price)}</div>` : ``}
      </div>`;

  const btn = out
  ? `<button class="btn disabled" disabled>
       ${cartIcon()} Out of Stock
     </button>`
  : `
    <form method="POST" action="cart.php" style="display:inline;">
      <input type="hidden" name="add_to_cart" value="1">
      <input type="hidden" name="id" value="${p.id}">
      <input type="hidden" name="name" value="${p.name}">
      <input type="hidden" name="price" value="${fmtRand(p.price)}">
      <input type="hidden" name="image" value="${p.image}">
      <input type="hidden" name="quantity" value="1">
      <button type="submit" class="btn">${cartIcon()} Add to Cart</button>
    </form>
  `;

    return `
      <a href="product.php?id=${p.id}" class="card-link">
      <div class="card ${isList ? 'list-row' : ''}" data-id="${p.id}" data-cat="${p.category}" data-price="${p.price}" data-stars="${p.stars}">
        <div class="card-img">
          ${topBadge}
          <img src="${p.image}" alt="${p.name}">
        </div>
        <div class="card-body">
          <h3 class="name">${p.name}</h3>
          <div class="cat">${cat}</div>
          ${rating}
          ${price}
          <div class="actions">${btn}</div>
        </div>
      </div>`;
  }

  function cartIcon(){
    return `<svg width="18" height="18" viewBox="0 0 24 24" fill="#fff">
      <path d="M6 6h15l-1.5 9h-12L6 6z"></path><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle>
    </svg>`;
  }

  /* ====== Filtering & Sorting ====== */
  function passFilters(p){
    // search
    const q = (searchEl.value || '').trim().toLowerCase();
    if (q){
      const hay = `${p.name} ${p.category}`.toLowerCase();
      if (!hay.includes(q)) return false;
    }
    // category
    if (fCat.value !== 'all' && p.category !== fCat.value) return false;

    // price
    const price = Number(p.price);
    switch (fPrice.value){
      case '0-5000': if (!(price < 5000)) return false; break;
      case '5000-15000': if (!(price >= 5000 && price < 15000)) return false; break;
      case '15000-30000': if (!(price >= 15000 && price < 30000)) return false; break;
      case '30000+': if (!(price >= 30000)) return false; break;
    }
    return true;
  }

  function sortProducts(arr){
    const v = fSort.value;
    if (v === 'featured') return arr; // keep original order
    const a2 = [...arr];
    switch (v){
      case 'price-low': a2.sort((a,b)=>a.price-b.price); break;
      case 'price-high': a2.sort((a,b)=>b.price-a.price); break;
      case 'name': a2.sort((a,b)=>a.name.localeCompare(b.name)); break;
      case 'rating': a2.sort((a,b)=>b.stars-a.stars || b.reviews-a.reviews); break;
    }
    return a2;
  }

  function render(){
    const filtered = PRODUCTS.filter(passFilters);
    const sorted = sortProducts(filtered);
    countEl.textContent = `${sorted.length} products found`;
    wrap.className = (view === 'grid' ? 'grid' : 'list');

    wrap.innerHTML = sorted.map(p => cardHTML(p, view==='list')).join('');

    // wire "Add to Cart"
    wrap.querySelectorAll('[data-add]').forEach(btn=>{
      btn.addEventListener('click', e=>{
        const id = e.currentTarget.getAttribute('data-add');
        const item = PRODUCTS.find(x=>x.id===id);
        if (!item || !item.in_stock) return;
        // demo action
        alert(`${item.name} added to cart ✅`);
      });
    });
  }

  /* ====== Events ====== */
  [searchEl, fCat, fPrice, fSort].forEach(input=>{
    input.addEventListener('input', render);
    input.addEventListener('change', render);
  });

  btnGrid.addEventListener('click',()=>{
    view='grid';
    btnGrid.classList.add('active');
    btnList.classList.remove('active');
    render();
  });
  btnList.addEventListener('click',()=>{
    view='list';
    btnList.classList.add('active');
    btnGrid.classList.remove('active');
    render();
  });
// Handle category card clicks
document.querySelectorAll(".category-box").forEach(box => {
  box.addEventListener("click", () => {
    const cat = box.getAttribute("data-cat");
    fCat.value = cat;   // set dropdown to same category
    render();           // re-render products
    window.scrollTo({ top: 0, behavior: 'smooth' }); // optional: scroll up
  });
});


  // initial
  render();
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

    // ...existing code...

</script>
</body> 
</html>

