<?php
// contact us page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact TechGiants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #e6771d;
            --secondary: #38c172;
            --accent: #9561e2;
            --dark: #222;
            --light: #f5f5f5;
            --gray: #6c757d;
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
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
            background: #d46912;
        }
        
        /* New black button style */
        .btn-black {
            background: #000;
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
        
        .section {
            padding: 60px 0;
        }
        
        .hero {
            background: linear-gradient(to right, #000, #222);
            color: white;
            text-align: center;
            position: relative;
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
        
        .live-chat-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .live-chat-btn i {
            font-size: 1.2rem;
        }
        
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
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
        
        .card h3 {
            margin-bottom: 1rem;
        }
        
        .card i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        /* Updated Icon Styling */
        .icon-container {
            width: 80px;
            height: 80px;
            background-color: #f7f7f7;
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
            margin-bottom: 0;
        }
        
        .address .icon-container {
            border: 1px solid #d8e5f8;
            background-color: #f0f6ff;
        }

        .phone .icon-container {
            border: 1px solid #d8f8e0;
            background-color: #f0fff2;
        }

        .email .icon-container {
            border: 1px solid #f8e8d8;
            background-color: #fff8f0;
        }

        .hours .icon-container {
            border: 1px solid #e7d8f8;
            background-color: #f6f0ff;
        }
        
        .address i { color: #3490dc; }
        .phone i { color: #38c172; }
        .email i { color: #e6771d; }
        .hours i { color: #9561e2; }
        
        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        
        @media (max-width: 768px) {
            .two-column {
                grid-template-columns: 1fr;
            }
        }
        
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
        
        .info-box {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .info-box h3 {
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .follow-us p {
            margin-bottom: 1.2rem;
            margin-top: -0.2rem;
            color: #333;
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
        
        .social-links {
            list-style: none;
            padding: 0;
        }
        
        .social-links li {
            margin-bottom: 0.8rem;
        }
        
        .social-links a {
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.1rem;
            color: #333;
        }
        
        .social-links a:hover {
            opacity: 0.8;
        }
        
        .instagram i { color: #E1306C; }
        .tiktok i { color: #000000; }
        .website i { color: #3498db; }
        
        .social-links i {
            font-size: 1.4rem;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .faq-cta {
            text-align: center;
            padding: 2rem;
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
</head>
<body>
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
                    
                    <form>
                        <div class="form-group">
                            <label for="name">Full Name *</label>
                            <input type="text" id="name" class="form-control" placeholder="Your full name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address *</label>
                            <input type="email" id="email" class="form-control" placeholder="your.email@example.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" class="form-control" placeholder="+27 XX XXX XXXX">
                        </div>
                        
                        <div class="form-group">
                            <label for="inquiry">Inquiry Type</label>
                            <select id="inquiry" class="form-control">
                                <option value="">Select a category</option>
                                <option value="general">General Inquiry</option>
                                <option value="support">Technical Support</option>
                                <option value="sales">Sales Question</option>
                                <option value="warranty">Warranty Claim</option>
                                <option value="feedback">Feedback</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" class="form-control" placeholder="Brief description of your inquiry">
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" class="form-control" placeholder="Please provide details about your inquiry..." required></textarea>
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
                                <a href="https://instagram.com/techgiants" target="_blank" class="instagram">
                                    <i class="fab fa-instagram"></i> @techgiants
                                </a>
                            </li>
                            <li>
                                <a href="https://tiktok.com/@techgiants" target="_blank" class="tiktok">
                                    <i class="fab fa-tiktok"></i> @techgiants
                                </a>
                            </li>
                            <li>
                                <a href="https://techgiants.co.za" target="_blank" class="website">
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