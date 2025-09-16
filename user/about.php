<?php
session_start();

// Get cart count for header
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>

<style>

   :root {
            --primary: #e6771d;
            --secondary: #38c172;
            --accent: #9561e2;
            --dark: #222;
            --light: #f5f5f5;
            --gray: #6c757d;
        }
        
/* ========== Global Styles ========== */
body {
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #fff;
  color: #111827;
  line-height: 1.6;
}

.container {
  width: 90%;
  max-width: 1200px;
  margin: 0 auto;
}

h1, h2, h3 {
  font-weight: bold;
  color: white;


}

p {
  color: #4b5563;
}

/* ========== Header Styles ========== */

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
            cursor: pointer;
        }

        .nav button {
            background: none;
            border: none;
            font-weight: bold;
            margin: 0 10px;
            cursor: pointer;
            font-size: 16px;
            color: #333;
            transition: color 0.3s;
        }

        .nav button:hover {
            color: #ff6a00;
        }

        .user-actions {
            display: flex;
            align-items: center;
        }

        .account-link, .cart-link {
            text-decoration: none;
            color: #333;
            margin-left: 10px;
            transition: color 0.3s;
        }

        .account-link:hover, .cart-link:hover {
            color: #ff6a00;
        }

        .cart-badge {
            background: #ff6a00;
            color: #fff;
            padding: 3px 8px;
            border-radius: 50%;
            font-size: 12px;
        }

/* ========== Hero Section ========== */
.gradient-cta {
  background: linear-gradient(to right, #000000, #111827);
  text-align: center;
  padding: 60px 20px;
}

.gradient-cta h1 {
  font-size: 2rem;
  margin-bottom: 1rem;
}

.gradient-cta p {
  font-size: 1.2rem;
  max-width: 700px;
  margin: 0 auto 2rem auto;
  color: #d1d5db;
}

.btn {
  display: inline-block;
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 1rem;
  text-decoration: none;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.3s, color 0.3s;
}

.btn-primary {
  background: #f97316;
  color: #fff;
  border: none;
}

.btn-primary:hover {
  background: #ea580c;
}



/* ========== Stats Section ========== */
.bg-muted-50 {
  background: #f9fafb;
}

.about-stats,
section.bg-muted-50 .grid {
  display: flex;
  justify-content: space-around;
  flex-wrap: wrap;
}

.about-stats .stat,
section.bg-muted-50 .text-center {
  text-align: center;
  margin: 20px;
}

.about-stats h2,
.text-3xl {
  font-size: 2rem;
  color: #f97316;
  margin: 10px 0;

}

.text{
  color: #6b7280;
}

.stats-bg {
  background: #f97316;
  border-radius: 50%;
  width: 90px;
  height: 90px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 10px auto;
}

.text-secondary {
  color: #f97316;
}

.text-secondary-foreground {
  color: #fff;
  height: auto;
  width: 60px;
  align-items: center;
  justify-content: center;  
  padding-bottom: 6px;
}

/* ========== Story Section ========== */

h2{
  color: #111827;
}

p{
  color: #6b7280;
  font-size : 0.7rem;
}
section .grid {
  display: flex;
  gap: 2rem;
  padding: 0 1rem;
}

@media (min-width: 1024px) {
  section .grid.lg\\:grid-cols-2 {
    grid-template-columns: repeat(2, 1fr);
  }
}
.flex{

  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1rem 0 0 4rem;
}
section img {
  max-width: 500px;
  height: auto;
  border-radius: 8px;
  box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

/* ========== Values Section ========== */
.value-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
 background: #f9fafb;
 padding: 2rem;
 margin-top:1rem;
}

.card {
  text-align: center;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  transition: box-shadow 0.3s ease;
}

.card:hover {
  box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

.card h3 {
  color: #111827;
  margin-bottom: 0.5rem;
}

.card p {
  font-size: 0.8rem;
  color: #6b7280;
}

.value-bg {
  background: #f97316;
  border-radius: 50%;
  width: 5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 5px auto 10px auto;
}

.value-foreground {
  color: #fff; 
  height: 50px;
  align-items: center;
  padding-top: 8px;
  margin-top:0.5rem;
}

/* ========== CTA Section ========== */
.cta-section {
  background: #f97316;
  text-align: center;
  padding: 20px 20px;
}

.cta-section h2 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  color: #fff;
}

.cta-section p {
  font-size: 1.2rem;
  color: rgba(255, 255, 255, 0.9);
}

.cta-section .btn {
  border: 2px solid #fff;
  color: #fff;
  background: transparent;
  margin: 0 10px;
  padding: 10px 20px;
  font-size: 1rem;
}

.cta-section .btn:hover {
  background: #fff;
  color: #f97316;
}

/*Footer Styles*/
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
        
        .gaming-excellence {
            color: var(--primary);
            font-weight: bold;
        }

        /* NEW STYLES FOR FIXED FOOTER */
        .contact-info {
            margin-top: 1rem;
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 0.8rem;
        }
        
        .checkbox-icon {
            margin-right: 10px;
            margin-top: 3px;
            color: var(--primary);
            min-width: 20px;
        }
        
        .contact-details {
            color: #ccc;
            line-height: 1.4;
        }
        
        .footer-connect ul li {
            display: flex;
            align-items: center;
            margin-bottom: 0.8rem;
        }
        
        .footer-connect .checkbox-icon {
            color: var(--primary);
        }
        
        .footer-connect .contact-details {
            color: #ccc;
        }
        
        .support-links {
            margin-top: 1.5rem;
        }
        
        .support-links a {
            color: #ccc;
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
        }
        
        .support-links a:hover {
            color: white;
            text-decoration: underline;
        }
        
        /* Social media icons in footer */
        .footer-social {
            list-style: none;
            padding: 0;
            margin-top: 1rem;
        }
        
        .footer-social li {
            margin-bottom: 0.8rem;
        }
        
        .footer-social a {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1rem;
            color: #ccc;
        }
        
        .footer-social a:hover {
            color: white;
        }
        
        .footer-social i {
            font-size: 1.2rem;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .footer-instagram i { color: #E1306C; }
        .footer-website i { color: #3498db; }
        .footer-tiktok i { color: #000000; background-color: white; border-radius: 3px; }
     
</style>

<!DOCTYPE html>
<html lang="en">
<!-- Header -->
    <header class="header">
        <div class="logo" onclick="window.location.href='home.php'">Tech Giants</div>
        <nav class="nav">
            <button data-page="home">Home</button>
            <button data-page="shop">Shop</button>
            <button data-page="about">About Us</button>
            <button data-page="contact">Contact</button>
        </nav>
        <div class="user-actions">
            <a href="account.php" class="account-link">👤 My Account</a>
            <a href="cart.php" class="cart-link">🛒 <span class="cart-badge"><?= htmlspecialchars($cart_count) ?: 0 ?></span></a>
        </div>
    </header>

<body class="min-h-screen bg-background text-foreground dark">

<?php include 'components/header.php'; ?>

<main>
    <!-- Hero Section -->
    <section class="relative gradient-cta text-white py-20">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl font-bold mb-6">About Tech Giants</h1>
                <p class="text-xl text-gray-300 mb-8">
                    Born from passion, driven by excellence. We're South Africa's premier destination 
                    for gaming hardware, serving the gaming community since 2016.
                </p>
                <a href="contact.php" class="btn btn-primary btn-lg">
                    Get in Touch
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-muted-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="stats-bg">
                        <svg class="w-8 h-8 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-secondary mb-2">50,000+</div>
                    <div class="text">Happy Customers</div>
                </div>
                
                <div class="text-center">
                    <div class="stats-bg">
                        <svg class="w-8 h-8 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-secondary mb-2">200,000+</div>
                    <div class="text">Products Sold</div>
                </div>
                
                <div class="text-center">
                    <div class="stats-bg">
                        <svg class="w-8 h-8 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-secondary mb-2">8+</div>
                    <div class="text">Years Experience</div>
                </div>
                
                <div class="text-center">
                    <div class="stats-bg">
                        <svg class="w-8 h-8 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <div class="text-3xl font-bold text-secondary mb-2">150+</div>
                    <div class="text">Gaming Communities</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Story Section -->
    <section class="our-story">
        <div class="container ">
            <div class="grid">
                <div>
                    <h2 >Our Story</h2>
                    <div >
                        <p>
                            Founded in 2016 in Pretoria, Gauteng, Tech Giants started as a small
                            group of passionate gamers who were frustrated with the lack of quality 
                            gaming hardware available in South Africa.
                        </p>
                        <p>
                            What began as importing graphics cards for our own gaming rigs quickly 
                            evolved into something bigger. We realized that the South African gaming 
                            community deserved better access to cutting-edge technology at fair prices.
                        </p>
                        <p>
                            Today, we're proud to serve over 50,000 satisfied customers across South Africa, 
                            providing everything from high-end gaming PCs to the latest peripherals and components.
                        </p>
                        <p>
                            Our mission remains the same: to empower gamers with the tools they need to achieve 
                            their full potential, whether they're casual players or professional esports athletes.
                        </p>
                    </div>
                </div>
                
                <div class="flex ">
                    <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx0ZWFtJTIwd29ya2luZ3xlbnwwfHx8fDE3NTY5OTIyMzV8MA&ixlib=rb-4.1.0&q=80&w=1080"
                         alt="Tech Giants Team"
                         class="w-full rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class=" value-section ">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold mb-4">Our Values</h2>
                <p class="text-xl text-muted-foreground">
                    The principles that guide everything we do
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">

                <div class="card rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300">
                    <div class="value-bg ">
                        <svg class="w-8 h-8 value-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-lg mb-3 text-card-foreground">Performance First</h3>
                    <p class="text-muted-foreground text-sm">We believe in providing the highest performance gaming hardware that gives you the competitive edge you need.</p>
                </div>


                <div class="card rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300">
                    <div class="value-bg">
                        <svg class="w-8 h-8 value-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-lg mb-3 text-card-foreground">Quality Assurance</h3>
                    <p class="text-muted-foreground text-sm">Every product is rigorously tested to ensure it meets our high standards before reaching your hands.</p>
                </div>

                <div class="card rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300">
                    <div class="value-bg">
                        <svg class="w-8 h-8 value-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-lg mb-3 text-card-foreground">Community Driven</h3>
                    <p class="text-muted-foreground text-sm">We're gamers ourselves, building solutions for the gaming community with passion and dedication.</p>
                </div>

                <div class="card rounded-lg p-6 text-center hover:shadow-lg transition-shadow duration-300">
                    <div class="value-bg">
                        <svg class="w-8 h-8 value-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-medium text-lg mb-3 text-card-foreground">Expert Support</h3>
                    <p class="text-muted-foreground text-sm">Our team of gaming experts provides personalized recommendations and technical support.</p>
                </div>

            </div>
            
        </div>
    </section>

    <!-- CTA Section -->
    <section class=" cta-section">
        <div class="container mx-auto px-4 text-center">
            <div>
                <h2 class="text-4xl font-bold mb-4">Ready to Level Up Your Gaming?</h2>
                <p class="text-xl mb-8" style="opacity: 0.9;">
                    Join thousands of satisfied gamers who trust Tech Giants for their gaming needs.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="shop.php" class="btn btn-lg border border-white text-white hover:bg-white hover:text-secondary transition-colors">
                        Browse Products
                    </a>
                    <a href="contact.php" class="btn btn-lg border border-white text-white hover:bg-white hover:text-secondary transition-colors">
                        Contact Us
                    </a>
                </div>
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
    
    
</main>

<?php>

<script>
// Mobile menu toggle
document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
});
</script>

</body>
</html>