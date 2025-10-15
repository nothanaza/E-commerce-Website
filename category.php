<?php
session_start();
require_once 'components/db.php';

// Get category ID
$category_id = $_GET['id'] ?? null;

// Fetch category info
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$category_id]);
$category = $stmt->fetch();

if (!$category) {
  header("Location: shop.php");
  exit;
}

// Fetch products from this category
$stmt = $pdo->prepare("
  SELECT p.*, c.name AS category_name 
  FROM products p 
  LEFT JOIN categories c ON p.category_id = c.id 
  WHERE p.category_id = ?
");
$stmt->execute([$category_id]);
$products = $stmt->fetchAll() ?: [];

// Get cart count
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars($category['name']) ?> | Tech Giants</title>
<link rel="stylesheet" href="style.css">
<script src="https://cdn.tailwindcss.com"></script>
<style>
  .hero {
    position: relative;
    text-align: center;
    color: white;
    padding: 120px 20px;
    overflow: hidden;
  }
  .hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
  }
  .hero-content {
    position: relative;
    z-index: 2;
    max-width: 800px;
    margin: auto;
  }
  .hero h1 {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 12px;
  }
  .hero p {
    font-size: 1.2rem;
  }
  .grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 20px;
  }
  .card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    transition: transform 0.2s ease;
  }
  .card:hover {
    transform: translateY(-4px);
  }
  .card-img {
    position: relative;
    aspect-ratio: 1 / 1;
  }
  .card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .badge.out {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ef4444;
    color: white;
    font-size: 0.8rem;
    padding: 3px 8px;
    border-radius: 4px;
  }
  .card-body {
    padding: 15px;
    text-align: center;
  }
  .card-body .name {
    font-size: 1.05rem;
    font-weight: 600;
    margin-bottom: 5px;
  }
  .price {
    color: #111827;
    font-weight: 600;
    margin: 8px 0;
  }
  .btn {
    background: #111827;
    color: white;
    padding: 8px 14px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
  }
  .btn:hover {
    background: #1f2937;
  }
  .out-of-stock {
    background: #9ca3af;
    cursor: not-allowed;
  }
  .filters {
    padding: 20px 0;
    background: #f9fafb;
  }
  .filters-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
  }
  .pill {
    padding: 8px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
  }
</style>
</head>

<body>
<?php include 'components/header.php'; ?>

<!-- HERO -->
<section class="hero" style="
  background: url('<?= htmlspecialchars($category['image'] ?? 'images/default-hero.jpg') ?>') center/cover no-repeat;
">
  <div class="hero-content">
    <h1><?= htmlspecialchars($category['name']) ?></h1>
    <p>Explore premium products in our <?= htmlspecialchars($category['name']) ?> collection.</p>
  </div>
</section>

<!-- FILTERS -->
<section class="filters">
  <div class="container" style="max-width:1200px;margin:auto;">
    <div class="filters-inner">
      <div>
        <select id="f-sort" class="pill">
          <option value="featured">Featured</option>
          <option value="price-low">Price: Low to High</option>
          <option value="price-high">Price: High to Low</option>
          <option value="name">Name A-Z</option>
          <option value="rating">Highest Rated</option>
        </select>
      </div>
      <div>
        <span id="count"><?= count($products) ?> products found</span>
      </div>
    </div>
  </div>
</section>

<!-- PRODUCTS -->
<section style="padding:40px 0;">
  <div class="container" style="max-width:1200px;margin:auto;">
    <div id="wrap" class="grid"></div>
  </div>
</section>

<?php include 'components/footer.php'; ?>

<script>
  const PRODUCTS = <?php echo json_encode($products, JSON_UNESCAPED_SLASHES); ?>;
  const wrap = document.getElementById("wrap");
  const countEl = document.getElementById("count");
  const sortEl = document.getElementById("f-sort");

  const fmtRand = v => "R" + Number(v).toLocaleString("en-ZA", {minimumFractionDigits:2, maximumFractionDigits:2});

  function renderStars(stars){
    const full = Math.floor(stars);
    const half = stars % 1 >= 0.5;
    const empty = 5 - full - (half ? 1 : 0);
    let s = '';
    for(let i=0;i<full;i++) s += `<svg width="16" height="16" fill="#fbbf24"><path d="M10 1.5l2.6 5.27 5.82.84-4.21 4.1.99 5.78L10 14.98l-5.2 2.73.99-5.78L1.58 7.61l5.82-.84L10 1.5z"/></svg>`;
    if(half) s += `<svg width="16" height="16"><defs><linearGradient id="half"><stop offset="50%" stop-color="#fbbf24"/><stop offset="50%" stop-color="#d1d5db"/></linearGradient></defs><path fill="url(#half)" d="M10 1.5l2.6 5.27 5.82.84-4.21 4.1.99 5.78L10 14.98l-5.2 2.73.99-5.78L1.58 7.61l5.82-.84L10 1.5z"/></svg>`;
    for(let i=0;i<empty;i++) s += `<svg width="16" height="16" fill="#d1d5db"><path d="M10 1.5l2.6 5.27 5.82.84-4.21 4.1.99 5.78L10 14.98l-5.2 2.73.99-5.78L1.58 7.61l5.82-.84L10 1.5z"/></svg>`;
    return `<div class="stars" style="display:flex;gap:2px;justify-content:center;">${s}</div>`;
  }

  function cardHTML(p){
    const out = !p.in_stock;
    const badge = out ? `<div class="badge out">Out of Stock</div>` : '';
    const btn = out
      ? `<button class="btn out-of-stock" disabled>Out of Stock</button>`
      : `<form method="POST" action="shop.php"><input type="hidden" name="add_to_cart" value="1"><input type="hidden" name="id" value="${p.id}"><input type="hidden" name="name" value="${p.name}"><input type="hidden" name="price" value="${p.price}"><input type="hidden" name="image" value="${p.image}"><input type="hidden" name="quantity" value="1"><button class="btn">Add to Cart</button></form>`;
    
    return `
      <div class="card" data-id="${p.id}">
        <div class="card-img">
          ${badge}
          <img src="${p.image}" alt="${p.name}">
        </div>
        <div class="card-body">
          <h3 class="name">${p.name}</h3>
          ${renderStars(p.stars || 0)}
          <div class="price">${fmtRand(p.price)}</div>
          ${btn}
        </div>
      </div>
    `;
  }

  function sortProducts(arr){
    const v = sortEl.value;
    if(v==='featured') return arr;
    const sorted = [...arr];
    if(v==='price-low') sorted.sort((a,b)=>a.price-b.price);
    if(v==='price-high') sorted.sort((a,b)=>b.price-a.price);
    if(v==='name') sorted.sort((a,b)=>a.name.localeCompare(b.name));
    if(v==='rating') sorted.sort((a,b)=>b.stars - a.stars);
    return sorted;
  }

  function render(){
    const arr = sortProducts(PRODUCTS);
    wrap.innerHTML = arr.map(cardHTML).join('');
    countEl.textContent = `${arr.length} products found`;
  }

  sortEl.addEventListener('change', render);
  document.addEventListener('DOMContentLoaded', render);
</script>
</body>
</html>
