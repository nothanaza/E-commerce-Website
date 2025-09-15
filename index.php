<?php
session_start();

// Get cart count
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}

// Featured products
$featured_products = [
    [
        'id' => 'rtx-4090',
        'name' => 'NVIDIA RTX 4090 Gaming X Trio',
        'price' => 28999.99,
        'category' => 'Graphics Cards',
        'image' => 'https://images.unsplash.com/photo-1591799264318-7e6ef8ddb7ea?auto=format&fit=crop&w=400'
    ],
    [
        'id' => 'gaming-pc-pro',
        'name' => 'Tech Giants Gaming PC Pro',
        'price' => 45999.99,
        'category' => 'Gaming PCs',
        'image' => 'https://images.unsplash.com/photo-1587831990711-23ca6441447b?auto=format&fit=crop&w=400'
    ],
    [
        'id' => 'corsair-k95',
        'name' => 'Corsair K95 RGB Platinum',
        'price' => 3299.99,
        'category' => 'Peripherals',
        'image' => 'https://images.unsplash.com/photo-1541140532154-b024d705b90a?auto=format&fit=crop&w=400'
    ],
    [
        'id' => 'asus-monitor',
        'name' => 'ASUS ROG Swift PG27UQ',
        'price' => 12999.99,
        'category' => 'Monitors',
        'image' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=400'
    ]
];

$categories = [
    ['id' => 'gaming-pcs', 'name' => 'Gaming PCs'],
    ['id' => 'graphics-cards', 'name' => 'Graphics Cards'],
    ['id' => 'motherboards', 'name' => 'Motherboards'],
    ['id' => 'monitors', 'name' => 'Monitors'],
    ['id' => 'peripherals', 'name' => 'Peripherals'],
    ['id' => 'audio', 'name' => 'Audio']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>The Tech Giants - Gaming Hardware Store</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      color: #222;
    }

    /* Header */
    .header {
      background: #fff;
      border-bottom: 1px solid #ddd;
      padding: 15px 40px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .logo {
      font-size: 22px;
      font-weight: bold;
      color: #ff6a00;
    }

    .nav button {
      background: none;
      border: none;
      font-weight: bold;
      margin: 0 10px;
      cursor: pointer;
      font-size: 16px;
      color: #333;
    }

    .nav button:hover {
      color: #ff6a00;
    }

    .cart-badge {
      background: #ff6a00;
      color: #fff;
      padding: 3px 8px;
      border-radius: 50%;
      font-size: 12px;
    }

    /* HERO FIX */
    .hero-section {
      position: relative;
      display: flex;
      justify-content: space-between;
      align-items: center;
      color: #fff;
      padding: 80px 40px;
      gap: 40px;
      background: url("https://images.unsplash.com/photo-1663419523419-038d7a8eb31f?auto=format&fit=crop&w=1600&q=80") no-repeat center center/cover;
    }

    .hero-overlay {
      position: absolute;
      inset: 0;
      background: rgba(0, 0, 0, 0.7);
      z-index: 1;
    }

    .hero-content {
      position: relative;
      z-index: 2;
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      max-width: 1200px;
      margin: auto;
    }

    .hero-text {
      max-width: 50%;
    }

    .hero-text .badge {
      display: inline-block;
      background: #ff6600;
      color: #fff;
      padding: 5px 12px;
      border-radius: 20px;
      font-size: 14px;
      margin-bottom: 20px;
    }

    .hero-text h1 {
      font-size: 44px;
      font-weight: bold;
      line-height: 1.2;
      margin-bottom: 15px;
    }

    .hero-text h1 .highlight {
      color: #ff6600;
    }

    .hero-text p {
      color: #ccc;
      font-size: 18px;
      line-height: 1.5;
      margin-bottom: 25px;
    }

    .hero-buttons {
      display: flex;
      gap: 15px;
    }

    .btn-primary {
      background: #ff6600;
      color: #fff;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: bold;
      text-decoration: none;
    }
    .btn-primary:hover { background: #e65c00; }

    .btn-secondary {
      background: #fff;
      color: #000;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: bold;
      text-decoration: none;
    }
    .btn-secondary:hover { background: #f2f2f2; }

    .hero-image {
      border: 2px solid #ff6600;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 0 20px rgba(255, 102, 0, 0.4);
      flex: 1;
      max-width: 480px;
      position: relative;
      z-index: 2;
    }
    .hero-image img { width: 100%; height: auto; }

    /* Features */
    .features-section {
      background: #f5f5f7;
      padding: 40px 20px;
    }

    .features-grid {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }

    .feature-card {
      background: #fff;
      padding: 25px;
      border-radius: 16px;
      text-align: center;
      width: 280px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .feature-icon { font-size: 28px; color: #ff6a00; margin-bottom: 10px; }
    .feature-title { font-size: 18px; font-weight: bold; }
    .feature-description { color: #555; margin-top: 5px; }

    /* Categories */
    .categories-section { padding: 40px 20px; text-align: center; }
    .categories-title { font-size: 28px; margin-bottom: 25px; }
    .categories-grid { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
    .category-card {
      background: #fff;
      border: 1px solid #eee;
      border-radius: 12px;
      padding: 20px;
      width: 160px;
      transition: 0.3s;
      cursor: pointer;
    }
    .category-card:hover { transform: translateY(-5px); box-shadow: 0 6px 18px rgba(0,0,0,0.1); }
    .category-icon { font-size: 28px; color: #ff6a00; margin-bottom: 10px; }
    .category-title { font-weight: bold; }

    /* Featured Products FIX */
    .featured-products-section { padding: 40px 20px; }
    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .section-title { font-size: 26px; }
    .view-all {
      background: #ff6600;
      color: #fff;
      padding: 10px 18px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      transition: background 0.3s;
    }
    .view-all:hover { background: #e65c00; }
    .products-grid { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; }
    .product-card {
      background: #fff;
      border: 1px solid #eee;
      border-radius: 12px;
      width: 240px;
      overflow: hidden;
      transition: 0.3s;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 6px 18px rgba(0,0,0,0.15); }
    .product-image { width: 100%; height: 180px; object-fit: cover; }
    .product-info { padding: 15px; }
    .product-category { font-size: 12px; color: #888; }
    .product-name { font-size: 16px; font-weight: bold; margin: 8px 0; }
    .product-footer { display: flex; justify-content: space-between; align-items: center; }
    .product-price { font-weight: bold; color: #ff6a00; }
    .add-to-cart-button {
      background: #ff6a00;
      color: #fff;
      border: none;
      padding: 6px 12px;
      border-radius: 6px;
      cursor: pointer;
    }
    .add-to-cart-button:hover { background: #e65c00; }

    /* CTA */
    .cta-section {
      background: #ff6a00;
      text-align: center;
      padding: 50px 20px;
      color: white;
    }
    .cta-title { font-size: 28px; margin-bottom: 15px; }
    .cta-button {
      background: white;
      color: #ff6a00;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      margin-top: 15px;
    }

   .newsletter {
            background: var(--dark);
            color: white;
            text-align: center;
            padding: 40px 0;
        }
        
        .newsletter h2 {
            margin-bottom: 1rem;
        }
        
        .newsletter p {
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .newsletter-input {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px 0 0 5px;
            font-size: 1rem;
        }
        
        .newsletter-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-weight: 600;
        }
        
        footer {
            background: #222;
            color: white;
            padding: 60px 0 20px;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1rem;
        }
        
        .tg-logo {
            width: 50px;
            height: 50px;
            background: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            color: white;
        }
        
        .brand-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .footer-column h3 {
            color: white;
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
        }
        
        .footer-column ul {
            list-style: none;
        }
        
        .footer-column ul li {
            margin-bottom: 0.8rem;
        }
        
        .footer-column a {
            color: #ccc;
            text-decoration: none;
        }
        
        .footer-column a:hover {
            color: white;
            text-decoration: underline;
        }
        
        .footer-contact i {
            color: var(--primary);
            margin-right: 8px;
            width: 20px;
            height: 20px;
            text-align: center;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #444;
            color: #ccc;
            font-size: 0.9rem;
        }
        
  </style>
</head>
<body>

  <!-- Header -->
  <header class="header">
    <div class="logo">Tech Giants</div>
    <nav class="nav">
      <button data-page="home">Home</button>
      <button data-page="shop">Shop</button>
      <button data-page="about">About Us</button>
      <button data-page="contact">Contact</button>
    </nav>
    <div>
      🛒 <span class="cart-badge"><?= $cart_count ?></span>
    </div>
  </header>

  <main>
    <!-- ✅ FIXED HERO -->
    <section class="hero-section">
      <div class="hero-overlay"></div>
      <div class="hero-content">
        <div class="hero-text">
          <span class="badge">New Generation Gaming</span>
          <h1>
            Unleash Your <br>
            <span class="highlight">Gaming Potential</span>
          </h1>
          <p>
            Premium gaming hardware and accessories for serious gamers. 
            Experience the ultimate performance with our cutting-edge technology.
          </p>
          <div class="hero-buttons">
            <a href="#" class="btn-primary">Shop Gaming PCs →</a>
            <a href="#" class="btn-secondary">Browse Components</a>
          </div>
        </div>
        <div class="hero-image">
          <img src="https://images.unsplash.com/photo-1663419523419-038d7a8eb31f?auto=format&fit=crop&w=1080" alt="Gaming Setup">
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="features-section">
      <div class="features-grid">
        <div class="feature-card"><div class="feature-icon">⚡</div><h3 class="feature-title">High Performance</h3><p class="feature-description">Latest hardware for maximum gaming performance</p></div>
        <div class="feature-card"><div class="feature-icon">🛡</div><h3 class="feature-title">2 Year Warranty</h3><p class="feature-description">Comprehensive warranty on all products</p></div>
        <div class="feature-card"><div class="feature-icon">🚚</div><h3 class="feature-title">Fast Shipping</h3><p class="feature-description">Free shipping on orders over R3500</p></div>
      </div>
    </section>

    <!-- Categories -->
    <section class="categories-section">
      <h2 class="categories-title">Shop by Category</h2>
      <div class="categories-grid">
        <?php foreach ($categories as $category): ?>
          <div class="category-card">
            <div class="category-icon">🎮</div>
            <p class="category-title"><?= htmlspecialchars($category['name']); ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- Featured Products FIX -->
    <section class="featured-products-section">
      <div class="section-header">
        <h2 class="section-title">Featured Products</h2>
        <a href="shop.php" class="view-all">View All Products</a>
      </div>
      <div class="products-grid">
        <?php foreach ($featured_products as $product): ?>
          <div class="product-card">
            <img src="<?= $product['image']; ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
            <div class="product-info">
              <span class="product-category"><?= htmlspecialchars($product['category']); ?></span>
              <h3 class="product-name"><?= htmlspecialchars($product['name']); ?></h3>
              <div class="product-footer">
                <span class="product-price">R <?= number_format($product['price'], 2); ?></span>
                <button class="add-to-cart-button">Add to Cart</button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
      <h2 class="cta-title">Ready to Upgrade Your Gaming?</h2>
      <p>Join thousands of gamers who trust Tech Giants for their hardware.</p>
      <button class="cta-button">Start Shopping Now</button>
    </section>
  </main>

  <!-- Footer -->
  <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <div class="footer-brand">
                        <div class="tg-logo">TG</div>
                        <div class="brand-text">
                            <h3 style="margin: 0; line-height: 1.2;">Tech Giants</h3>
                        </div>
                    </div>
                    <p>South Africa's premier destination for gaming hardware and accessories. We provide cutting-edge technology for serious gamers who demand the best performance.</p>
                    
                    <div class="contact-info">
                        <div class="contact-item">
                            <span class="checkbox-icon"><i class="fas fa-map-marker-alt"></i></span>
                            <div class="contact-details">
                                Pretoria, Gauteng
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="checkbox-icon"><i class="fas fa-phone"></i></span>
                            <div class="contact-details">
                                +27 21 123 4567
                            </div>
                        </div>
                        <div class="contact-item">
                            <span class="checkbox-icon"><i class="fas fa-envelope"></i></span>
                            <div class="contact-details">info@techgiants.co.za</div>
                        </div>
                    </div>
                </div>
                
                <div class="footer-column">
                    <h3>About Us</h3>
                    <ul>
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">Why Choose Us</a></li>
                        <li><a href="#">Gaming Community</a></li>
                        <li><a href="#">Expert Reviews</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Gaming PCs</a></li>
                        <li><a href="#">Graphics Cards</a></li>
                        <li><a href="#">Gaming Peripherals</a></li>
                        <li><a href="#">Special Deals</a></li>
                        <li><a href="#">Build Configurator</a></li>
                    </ul>
                </div>
                
                <div class="footer-column footer-connect">
                    <h3>Connect With Us</h3>
                    <ul class="footer-social">
                        <li>
                            <a href="#" class="footer-instagram">
                                <i class="fab fa-instagram"></i> @techgiants
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-website">
                                <i class="fas fa-globe"></i> techgiants.co.za
                            </a>
                        </li>
                        <li>
                            <a href="#" class="footer-tiktok">
                                <i class="fab fa-tiktok"></i> @techgiants
                            </a>
                        </li>
                    </ul>
                    
                    <div class="support-links">
                        <a href="#">Customer Support</a>
                        <a href="#">Warranty Claims</a>
                        <a href="#">Return Policy</a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>© 2024 Tech Giants. All rights reserved. <a href="#">Privacy Policy</a> <a href="#">Terms of Service</a> <a href="#">Shipping Info</a> Powered by <span class="gaming-excellence">Gaming Excellence</span></p>
            </div>
        </div>
    </footer>
     <section class="newsletter">
        <div class="container">
            <h2>Stay Updated with Tech Giants</h2>
            <p>Get the latest gaming hardware news, exclusive deals, and product launches delivered to your inbox.</p>
            
            <form class="newsletter-form">
                <input type="email" class="newsletter-input" placeholder="Enter your email">
                <button type="submit" class="newsletter-btn">Subscribe</button>
            </form>
        </div>
    </section>
</body>
</html>
