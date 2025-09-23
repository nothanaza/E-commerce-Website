<?php
// footer.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Footer - Tech Giants</title>
  <!-- TailwindCSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Custom Colors -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            secondary: "#4ade80", // green
            "secondary-foreground": "#000"
          }
        }
      }
    }
  </script>
</head>
<body class="bg-gray-100">

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

  <!-- JavaScript for Newsletter -->
  <script>
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