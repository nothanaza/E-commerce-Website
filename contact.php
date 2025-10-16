<?php
session_start();
require_once 'components/db.php';
$mailConfig = require_once 'components/mail_config.php'; // load SMTP config

// PHPMailer autoload (from Composer)
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$name = $email = $phone = $inquiry_type = $subject = $message = '';
$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $inquiry_type = trim($_POST['inquiry'] ?? '');
    $subject = trim($_POST['subject'] ?? 'No Subject');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = "Name, email, and message are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        try {
            // 1) Save to DB (your existing code)
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, inquiry_type, subject, message) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $inquiry_type, $subject, $message]);

            // 2) Send email with PHPMailer (SMTP)
            $mail = new PHPMailer(true);

            // SMTP configuration (from your config)
            $mail->isSMTP();
            $mail->Host       = $mailConfig['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $mailConfig['username'];
            $mail->Password   = $mailConfig['password'];
            // choose encryption constant based on config
            if (strtolower($mailConfig['secure']) === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // for port 465
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // for port 587
            }
            $mail->Port       = $mailConfig['port'];

            // From / To / Reply-To
            $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
            $mail->addAddress($mailConfig['to_email']);        // where form messages go
            $mail->addReplyTo($email, $name);                  // reply to visitor

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Contact form: " . $subject;
            $mail->Body = "
                <h2>New contact form submission</h2>
                <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
                <p><strong>Inquiry:</strong> " . htmlspecialchars($inquiry_type) . "</p>
                <p><strong>Subject:</strong> " . htmlspecialchars($subject) . "</p>
                <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
            ";
            $mail->AltBody = "Name: $name\nEmail: $email\nPhone: $phone\nInquiry: $inquiry_type\nSubject: $subject\nMessage:\n$message";

            $mail->send();
            $success = "Thank you! Your message has been sent.";
            // clear form variables if desired
            $name = $email = $phone = $inquiry_type = $subject = $message = '';
        } catch (Exception $e) {
            // Log $e->getMessage() on server-side for debugging; show friendly message to user
            $error = "Message saved but failed to send email. Mailer Error: " . $e->getMessage();
        }
    }
}


// Get cart count for header
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_count += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact TechGiants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== VARIABLES & RESET ===== */
        :root {
            --primary: #e56b08ff;
            --secondary: #38c172;
            --accent: #9561e2;
            --dark: #222;
            --light: #f5f5f5;
            --gray: #6c757d;
            --navy: #001f3f;
            --light-navy: #2c3e50;
            --black: #000;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: var(--light);
            color: #333;
            line-height: 1.6;
        }
        
         
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

        .nav a {
  text-decoration: none;
  font-weight: bold;
  margin: 0 10px;
  font-size: 16px;
  color: #333;
  transition: color 0.3s;
}

.nav a:hover {
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
    
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .section {
            padding: 60px 0;
        }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        @media (max-width: 768px) {
            .two-column {
                grid-template-columns: 1fr;
            }
        }
        

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .hero p {
            font-size: 1.2rem;
            color: #ccc;
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .info-box h2, .info-box h3, .card h3 {
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .follow-us p {
            margin-bottom: 1.2rem;
            margin-top: -0.2rem;
            color: #333;
        }
        
        
        .btn {
            padding: 12px 24px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
        }
        
        .btn-primary:hover {
            background: #e56b08ff;
        }
        
        .btn-black {
            background: var(--black);
            color: white;
            border: none;
            width: 100%; 
            padding: 15px;
            font-size: 1.1rem;
        }
        
        .btn-black:hover {
            background: #333;
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .btn-outline:hover {
            background: white;
            color: var(--dark);
        }
        
        .live-chat-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .live-chat-btn i {
            font-size: 1.2rem;
        }
        
        .faq-btn {
            color: #333;
            border: 2px solid #333;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            display: inline-block;
            margin-top: 10px;
            transition: all 0.3s ease;
        }
        
        .faq-btn:hover {
            background: #333;
            color: white;
        }
        
    
        .card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .icon-container {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        
        .icon-container i {
            font-size: 2rem;
        }
        
        .address .icon-container {
            border-color: #d8e5f8;
            background-color: #f0f6ff;
        }

        .phone .icon-container {
            border-color: #d8f8e0;
            background-color: #f0fff2;
        }

        .email .icon-container {
            border-color: #f8e8d8;
            background-color: #fff8f0;
        }

        .hours .icon-container {
            border-color: #e7d8f8;
            background-color: #f6f0ff;
        }
        
        .address i { color: #3490dc; }
        .phone i { color: #38c172; }
        .email i { color: #e56b08ff; }
        .hours i { color: #9561e2; }
        
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        
        .form-button-container {
            text-align: center;
        }
        
    
        .hero {
            background: linear-gradient(to right, var(--black), #222);
            color: white;
            text-align: center;
            position: relative;
        }
        
        .info-box {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .map-placeholder {
            width: 100%;
            height: 200px;
            background: #eee;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #aaa;
        }
        
        .faq-cta {
            text-align: center;
            padding: 2rem;
        }
        
        
        .newsletter {
            background: var(--black);
            color: white;
            text-align: center;
            padding: 30px 20px;
        }
        
        .newsletter-box {
            background-color: var(--navy);
            border-radius: 10px;
            padding: 40px;
            max-width: 1080px;
            margin: 0 auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .newsletter h2 {
            margin-bottom: 1rem;
            color: white;
        }
        
        .newsletter p {
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            color: #ccc;
        }
        
        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
        }
        
        .newsletter-input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 5px 0 0 5px;
            font-size: 1rem;
            background-color: var(--light-navy);
            color: white;
        }
        
        .newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .newsletter-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0 25px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .newsletter-btn:hover {
            background: #cc5a06;
        }
        
    
        .social-links, .footer-social {
            list-style: none;
            padding: 0;
        }
        
        .social-links li, .footer-social li {
            margin-bottom: 0.8rem;
        }
        
        .social-links a, .footer-social a {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #333;
        }
        
        .social-links a:hover, .footer-social a:hover {
            opacity: 0.8;
        }
        
        .social-links i, .footer-social i {
            font-size: 1.4rem;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .social-links a {
            font-size: 1.1rem;
        }
        
        .instagram i, .footer-instagram i { color: #E1306C; }
        .tiktok i, .footer-tiktok i { color: #000000; }
        .website i, .footer-website i { color: #3498db; }
        
        .footer-tiktok i {
            background-color: white;
            border-radius: 3px;
        }
        
    
    /*Footer Styles*/
/* ================= FOOTER ================= */
.site-footer {
  background-color: #000;
  color: #f3f4f6;
  padding-top: 3rem;
  font-size: 0.875rem;
}

/* Top section: 4 columns */
.footer-top {
  display: grid;
  grid-template-columns: 1fr;
  gap: 2rem;
  padding: 0 1rem 2rem;
  max-width: 1200px;
  margin: 0 auto;
}

.footer-col h4 {
  font-size: 1rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.footer-col ul {
  list-style: none;
  padding: 0;
}

.footer-col ul li {
  margin-bottom: 0.5rem;
}

.footer-col ul li a {
  color: #d1d5db;
  text-decoration: none;
  transition: color 0.2s ease;
}

.footer-col ul li a:hover {
  color: #f97316;
}

/* Logo */
.footer-logo {
  display: flex;
  align-items: center;
  margin-bottom: 1rem;
}

.logo-box {
  width: 2.5rem;
  height: 2.5rem;
  background: #f97316;
  color: #fff;
  font-weight: 700;
  border-radius: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 0.5rem;
}

.brand-name {
  font-weight: 700;
  font-size: 1.2rem;
}

.footer-description {
  margin-bottom: 1rem;
  color: #9ca3af;
}

.footer-contact li {
  margin-bottom: 0.3rem;
  color: #d1d5db;
}

/* Middle row */
.footer-middle {
  border-top: 1px solid #374151;
  padding: 1rem;
  text-align: center;
  font-size: 0.85rem;
  color: #9ca3af;
  margin: 0 5rem 0 5rem;
}

.footer-links {
  margin: 0.5rem 0;
}

.footer-links a {
  margin: 0 0.75rem;
  color: #9ca3af;
  text-decoration: none;
}

.footer-links a:hover {
  color: #f97316;
}

.powered {
  margin-top: 0.5rem;
}

.powered span {
  color: #f97316;
  font-weight: 600;
}

/* Newsletter */
.footer-newsletter {
    background: #111827;
    color: #fff;
    text-align: center;
    padding: 2rem 1rem 2rem;
    margin: 2rem auto 0 auto; /* Center horizontally */
    border-radius: 0.5rem 0.5rem 0 0;
    max-width: 1000px; /* Optional: make it narrower for better centering */
}

.footer-newsletter h3 {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.footer-newsletter p {
  color: #d1d5db;
  margin-bottom: 1rem;
}

.newsletter-form {
  display: flex;
  justify-content: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.newsletter-form input {
  padding: 0.75rem 1rem;
  border-radius: 0.375rem;
  border: 1px solid #374151;
  background: #1f2937;
  color: #f3f4f6;
  flex: 1;
  max-width: 250px;
}

.newsletter-form button {
  padding: 0.75rem 1.5rem;
  background: #f97316;
  color: #fff;
  border: none;
  border-radius: 0.375rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.newsletter-form button:hover {
  background: #ea580c;
}

/* Responsive */
@media (min-width: 768px) {
  .footer-top {
    grid-template-columns: repeat(4, 1fr);
  }

  .footer-middle {
    display: flex;
    justify-content: space-between;
    align-items: center;
    text-align: left;
  }

  .footer-links {
    margin: 0;
  }
}

    </style>
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

    <section class="hero section">
        <div class="container">
            <h1>Get in Touch</h1>
            <p>Have questions about our products? Need technical support? Our expert team is here to help you level up your gaming experience.</p>
            <a href="#" class="btn btn-primary live-chat-btn">
                <i class="fas fa-comment-dots"></i> Live Chat
            </a>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="card-grid">
                <div class="card address">
                    <div class="icon-container">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Visit Our Store</h3>
                    <p>123 Gaming Street<br>Pretoria, Gauteng<br>South Africa 0001</p>
                </div>
                <div class="card phone">
                    <div class="icon-container">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Call Us</h3>
                    <p>+27 21 123 4567<br>+27 21 123 4568<br>Mon-Fri: 8AM-6PM</p>
                </div>
                <div class="card email">
                    <div class="icon-container">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Us</h3>
                    <p>info@techgiants.co.za<br>support@techgiants.co.za<br>sales@techgiants.co.za</p>
                </div>
                <div class="card hours">
                    <div class="icon-container">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Business Hours</h3>
                    <p>Monday - Friday: 8AM - 6PM<br>Saturday: 9AM - 4PM<br>Sunday: Closed</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="two-column">
                <div class="info-box">
                    <h2>Send us a Message</h2>
                    <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                   <?php if ($success): ?>
    <p class="success" style="color: green; font-weight: bold; text-align: center; margin-bottom: 1rem;"><?= htmlspecialchars($success) ?></p>
<?php elseif ($error): ?>
    <p class="error" style="color: red; font-weight: bold; text-align: center; margin-bottom: 1rem;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?> 

<form method="POST" action="contact.php">
    <div class="form-group">
        <label for="name">Full Name *</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Your full name" value="<?= htmlspecialchars($name) ?>" required>
    </div>
    <div class="form-group">
        <label for="email">Email Address *</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="your.email@example.com" value="<?= htmlspecialchars($email) ?>" required>
    </div>
    <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" class="form-control" placeholder="+27 XX XXX XXXX" value="<?= htmlspecialchars($phone) ?>">
    </div>
    <div class="form-group">
        <label for="inquiry">Inquiry Type</label>
        <select id="inquiry" name="inquiry" class="form-control">
            <option value="">Select a category</option>
            <option value="general" <?= $inquiry_type === 'general' ? 'selected' : '' ?>>General Inquiry</option>
            <option value="support" <?= $inquiry_type === 'support' ? 'selected' : '' ?>>Technical Support</option>
            <option value="sales" <?= $inquiry_type === 'sales' ? 'selected' : '' ?>>Sales Question</option>
            <option value="warranty" <?= $inquiry_type === 'warranty' ? 'selected' : '' ?>>Warranty Claim</option>
            <option value="feedback" <?= $inquiry_type === 'feedback' ? 'selected' : '' ?>>Feedback</option>
        </select>
    </div>
    <div class="form-group">
        <label for="subject">Subject</label>
        <input type="text" id="subject" name="subject" class="form-control" placeholder="Brief description of your inquiry" value="<?= htmlspecialchars($subject) ?>">
    </div>
    <div class="form-group">
        <label for="message">Message *</label>
        <textarea id="message" name="message" class="form-control" placeholder="Please provide details about your inquiry..." required><?= htmlspecialchars($message) ?></textarea>
    </div>
    <div class="form-button-container">
        <button type="submit" class="btn btn-black">Send Message</button>
    </div>
</form>
                </div>
                
                <div>
                    <div class="info-box">
                        <h3>Interactive Map</h3>
                        <p>Visit our store in Pretoria</p>
                        <div class="map-placeholder">
                            <span>[Map Placeholder]</span>
                        </div>
                    </div>
                    
                    <div class="info-box follow-us">
                        <h3>Follow Us</h3>
                        <p>Stay connected for the latest gaming news and deals</p>
                        
                        <ul class="social-links">
                            <li>
                                <a href="https://www.instagram.com/the_tech_giants1?igsh=MTV3bHp6OXpma2d1dg==" target="_blank" class="instagram">
                                    <i class="fab fa-instagram"></i> @techgiants
                                </a>
                            </li>
                            <li>
                                <a href="https://www.tiktok.com/@the_tech_giants?_t=ZS-8zjDmUwy463&_r=1" target="_blank" class="tiktok">
                                    <i class="fab fa-tiktok"></i> @techgiants
                                </a>
                            </li>
                            <li>
                                <a href="https://thetechgiants.co.za/" target="_blank" class="website">
                                    <i class="fas fa-globe"></i> techgiants.co.za
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="info-box faq-cta">
                        <h3>Need Quick Answers?</h3>
                        <p>Check out our frequently asked questions for instant solutions.</p>
                        <a href="#" class="faq-btn">View FAQ</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
  <footer class="site-footer">
  <div class="footer-top">
    <!-- Column 1: Logo + Info -->
    <div class="footer-col">
      <div class="footer-logo">
        <div class="logo-box">TG</div>
        <span class="brand-name">Tech Giants</span>
      </div>
      <p class="footer-description">
        South Africa's premier destination for gaming hardware and accessories. 
        We provide cutting-edge technology for serious gamers who demand the best performance.
      </p>
      <ul class="footer-contact">
        <li>📍 Pretoria, Gauteng</li>
        <li>📞 +27 21 123 4567</li>
        <li>✉️ info@techgiants.co.za</li>
      </ul>
    </div>

    <!-- Column 2: About -->
    <div class="footer-col">
      <h4>Quick Links</h4>
      <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="about.php">Why Choose Us</a></li>
        <li><a href="shop.php">Shop</a></li>
         <li><a href="contact.php">Contact Us</a></li>
      </ul>
    </div>

    <!-- Column 3: Quick Links -->
    <div class="footer-col">
      <h4>Categories</h4>
      <ul>
            <li><a href="gaming-pcs.php">Gaming PCs</a></li>
            <li><a href="graphic-cards.php">Graphics Cards</a></li>
             <li><a href="audio.php">Audio</a></li>
            <li><a href="monitors.php">Monitors</a></li>
            <li><a href="motherboards.php">Motherboards</a></li>
             <li><a href="peripherals.php">Peripherals</a></li>
       </ul>
    </div>

    <!-- Column 4: Connect -->
    <div class="footer-col">
      <h4>Connect With Us</h4>
      <ul>
        <li>📸 @techgiants</li>
        <li>🌍 techgiants.co.za</li>
        <li>🎵 @techgiants</li>
      </ul>
      <ul class="footer-support">
        <li><a href="#">Customer Support</a></li>
        <li><a href="#">Warranty Claims</a></li>
        <li><a href="#">Return Policy</a></li>
      </ul>
    </div>
  </div>

  <!-- Middle Row -->
  <div class="footer-middle">
    <p>© 2024 Tech Giants. All rights reserved.</p>
    <div class="footer-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Shipping Info</a>
    </div>
    <p class="powered">Powered by <span>Gaming Excellence</span></p>
  </div>

  <!-- Newsletter -->
  <div class="footer-newsletter">
    <h3>Stay Updated with Tech Giants</h3>
    <p>Get the latest gaming hardware news, exclusive deals, and product launches delivered to your inbox.</p>
    <form class="newsletter-form">
      <input type="email" placeholder="Enter your email" required>
      <button type="submit">Subscribe</button>
    </form>
  </div>
</footer>

</body>
</html>
