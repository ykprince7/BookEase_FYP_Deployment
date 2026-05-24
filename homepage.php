<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookEase - Hotel Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sora:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/common.css">
    <style>
        :root {
            --primary-color: var(--teal);
            --secondary-color: #b8860b;
            --accent-color: #c93c3c;
            --light-bg: #f8fafc;
            --dark-text: #0f172a;
        }
        
        body {
            color: var(--dark-text);
        }
        
        /* Navigation */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 1.8rem;
        }
        
        .nav-link {
            font-weight: 500;
            color: var(--dark-text);
            padding: 8px 15px !important;
        }
        
        .nav-link:hover {
            color: var(--primary-color);
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-left: 15px;
        }
        
        /* Hero Section */
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1566073771259-6a8506099945?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 0;
            margin-bottom: 50px;
        }
        
        .booking-form {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .booking-form .form-control {
            border-radius: 5px;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        
        /* Registration Modal Styles */
        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
        }
        
        .modal-title {
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 6px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(var(--teal_rgb), 0.22);
        }
        
        .file-input-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }
        
        .file-input-container input[type="file"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-input-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background-color: #f8f9fa;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .file-input-label:hover {
            background-color: #f0f3f5;
            border-color: var(--primary-color);
        }
        
        .file-input-label span {
            color: var(--primary-color);
            font-weight: 600;
        }
        
        #fileName {
            color: #777;
            font-size: 0.9rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            max-width: 60%;
        }
        
        .dob-hint {
            color: #aaa;
            font-size: 0.85rem;
            margin-top: 5px;
        }
        
        .invalid-feedback {
            display: none;
            font-size: 0.85rem;
        }
        
        .password-strength {
            height: 4px;
            background-color: #e1e5eb;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
            display: none;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        /* Rooms Section */
        .rooms-section {
            padding: 50px 0;
            background-color: var(--light-bg);
        }
        
        .room-card {
            position: relative;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            margin-bottom: 30px;
            transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
            transform: translateY(0) scale(1);
            animation: room-card-enter 0.85s ease both;
            will-change: transform, box-shadow, opacity;
        }
        
        .room-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 28px 68px rgba(15, 23, 42, 0.18);
            border-color: rgba(52, 144, 220, 0.12);
        }
        
        .row > .col-md-4:nth-child(1) .room-card {
            animation-delay: 0.1s;
        }
        
        .row > .col-md-4:nth-child(2) .room-card {
            animation-delay: 0.18s;
        }
        
        .row > .col-md-4:nth-child(3) .room-card {
            animation-delay: 0.26s;
        }
        
        @keyframes room-card-enter {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .room-header {
            background: var(--primary-color);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .room-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin: 15px 0;
        }
        
        .room-features, .room-facilities, .room-guests {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }
        
        .room-features span, .room-facilities span {
            display: inline-block;
            background: #f0f0f0;
            padding: 5px 10px;
            margin: 3px;
            border-radius: 3px;
            font-size: 0.9rem;
        }
        
        /* Facilities Section */
        .facilities-section {
            padding: 50px 0;
        }
        
        .facilities-list {
            list-style: none;
            padding: 0;
            display: grid;
            gap: 1rem;
        }
        
        .facilities-list li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 18px 20px;
            line-height: 1.5;
            background-color: #fff;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
            cursor: pointer;
        }
        
        .facilities-list li:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
            background-color: rgba(46, 193, 172, 0.05);
            border-color: rgba(46, 193, 172, 0.2);
        }
        
        .facilities-list li::before {
            content: '✓';
            color: var(--primary-color);
            font-weight: 700;
            margin-right: 0.75rem;
        }
        
        .star-rating {
            color: var(--secondary-color);
        }
        
        /* Footer */
        .footer {
            background-color: #222;
            color: white;
            padding: 50px 0 20px;
        }
        
        .footer h5 {
            color: var(--secondary-color);
            margin-bottom: 20px;
        }
        
        .social-links a {
            color: white;
            font-size: 1.2rem;
            margin-right: 15px;
        }
        
        .copyright {
            border-top: 1px solid #444;
            padding-top: 20px;
            margin-top: 30px;
            text-align: center;
            color: #aaa;
        }
        
        /* Success Message */
        .success-message {
            display: none;
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            padding: 12px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-weight: 500;
            border-left: 4px solid #2ecc71;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="homepage.php">Book<span style=" color: #2ecc71;" >Ease</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="homepage.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="rooms.php">Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="facilities.php">Facilities</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php">Contact us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="compare.php">Compare Room</a>
                    </li>
                    <div class="nav-buttons">
                        <li class="nav-item">
                            <a class="btn btn-outline-primary" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#registrationModal">Register</button>
                        </li>
                    </div>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Registration Modal -->
    <div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationModalLabel">User Registration</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="success-message" id="registrationSuccess">
                        ✓ Registration successful! You can now log in.
                    </div>
                    
                    <form id="registrationForm" novalidate>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
                                <div class="invalid-feedback" id="nameError">
                                    Please enter a valid name (at least 2 characters)
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Enter your phone number" required>
                                <div class="invalid-feedback" id="phoneError">
                                    Please enter a valid phone number (10-15 digits)
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="address" class="form-label">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter your full address" required></textarea>
                                <div class="invalid-feedback" id="addressError">
                                    Please enter your address (at least 10 characters)
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="pincode" class="form-label">Pincode</label>
                                <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter pincode" required>
                                <div class="invalid-feedback" id="pincodeError">
                                    Please enter a valid pincode (4-10 digits)
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="dob" class="form-label">Date of birth</label>
                                <input type="date" class="form-control" id="dob" name="dob" required>
                                <div class="dob-hint">[mm/dd/yyyy]</div>
                                <div class="invalid-feedback" id="dobError">
                                    Please select your date of birth (must be 13+ years old)
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                                <div class="invalid-feedback" id="emailError">
                                    Please enter a valid email address
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="picture" class="form-label">Picture</label>
                                <div class="file-input-container">
                                    <input type="file" class="form-control" id="picture" name="picture" accept="image/*">
                                    <div class="file-input-label">
                                        <span>Choose File</span>
                                        <div id="fileName">No file chosen</div>
                                    </div>
                                </div>
                                <div class="invalid-feedback" id="pictureError">
                                    Please select a valid image file (JPG, PNG, GIF, max 5MB)
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                                <div class="password-strength" id="passwordStrength">
                                    <div class="strength-meter" id="strengthMeter"></div>
                                </div>
                                <div class="invalid-feedback" id="passwordError">
                                    Password must be at least 8 characters with letters and numbers
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
                                <div class="invalid-feedback" id="confirmPasswordError">
                                    Passwords do not match
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg" id="registerBtn">REGISTER</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Hero Section with Booking Form -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 mb-4">Welcome to BookEase</h1>
                    <p class="lead mb-4">Experience luxury and comfort in the heart of Nepal. Book your stay with us today!</p>
                </div>
                <div class="col-lg-6">
                    <div class="booking-form">
                        <h3 class="mb-4" style="color: var(--primary-color);">Check Booking Availability</h3>
                        <form action="check_availability.php" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="checkin" class="form-label">Check-in</label>
                                    <input type="date" class="form-control" id="checkin" name="checkin" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="checkout" class="form-label">Check-out</label>
                                    <input type="date" class="form-control" id="checkout" name="checkout" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="adults" class="form-label">Adults</label>
                                    <select class="form-control" id="adults" name="adults">
                                        <option value="1">1 Adult</option>
                                        <option value="2" selected>2 Adults</option>
                                        <option value="3">3 Adults</option>
                                        <option value="4">4 Adults</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="children" class="form-label">Children</label>
                                    <select class="form-control" id="children" name="children">
                                        <option value="0">0 Children</option>
                                        <option value="1">1 Child</option>
                                        <option value="2" selected>2 Children</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100">Check Availability</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section class="rooms-section">
        <div class="container">
            <h2 class="text-center mb-3" style="color: var(--primary-color);">OUR ROOMS</h2>
            <p class="text-center text-muted small mb-5 px-3">
                Tiers increase with price: each step up keeps everything from the tier below and adds more facilities.
            </p>
            <div class="row">
                <!-- Simple Room (entry tier) -->
                <div class="col-md-4">
                    <div class="room-card">
                        <div class="room-header">
                            <h3>Simple</h3>
                        </div>
                        <div class="room-body">
                            <div class="text-center room-price">NPR 1000 per night</div>
                            <div class="room-features">
                                <strong>Features:</strong><br>
                                <span>bedroom</span>
                                <span>balcony</span>
                                <span>kitchen</span>
                                <span>sofa</span>
                            </div>
                            <div class="room-facilities">
                                <strong>Facilities:</strong><br>
                                <span>WIFI</span>
                                <span>Television(TV)</span>
                                <span>Heater</span>
                                <span>Geyser</span>
                            </div>
                            <div class="room-guests">
                                <strong>Guests:</strong><br>
                                <span>2 Adults</span>
                                <span>1 Child</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Better Room (mid tier: all of Simple + AC) -->
                <div class="col-md-4">
                    <div class="room-card">
                        <div class="room-header">
                            <h3>Better</h3>
                        </div>
                        <div class="room-body">
                            <div class="text-center room-price">NPR 2000 per night</div>
                            <div class="room-features">
                                <strong>Features:</strong><br>
                                <span>bedroom</span>
                                <span>balcony</span>
                                <span>sofa</span>
                            </div>
                            <div class="room-facilities">
                                <strong>Facilities:</strong><br>
                                <span>Air Conditioner</span>
                                <span>WIFI</span>
                                <span>Television(TV)</span>
                                <span>Heater</span>
                                <span>Geyser</span>
                            </div>
                            <div class="room-guests">
                                <strong>Guests:</strong><br>
                                <span>3 Adults</span>
                                <span>2 Children</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Standard Room (top tier: adds premium amenities) -->
                <div class="col-md-4">
                    <div class="room-card">
                        <div class="room-header">
                            <h3>Standard</h3>
                        </div>
                        <div class="room-body">
                            <div class="text-center room-price">NPR 3000 per night</div>
                            <div class="room-features">
                                <strong>Features:</strong><br>
                                <span>bedroom</span>
                                <span>balcony</span>
                                <span>sofa</span>
                                <span>dressing table</span>
                            </div>
                            <div class="room-facilities">
                                <strong>Facilities:</strong><br>
                                <span>Air Conditioner</span>
                                <span>WIFI</span>
                                <span>Television(TV)</span>
                                <span>Heater</span>
                                <span>Geyser</span>
                                <span>Bar</span>
                                <span>SPA Access</span>
                            </div>
                            <div class="room-guests">
                                <strong>Guests:</strong><br>
                                <span>2 Adults</span>
                                <span>2 Children</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Facilities & Reviews Section -->
    <section class="facilities-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3 style="color: var(--primary-color);">OUR FACILITIES</h3>
                    <ul class="facilities-list">
                        <li>Geyser</li>
                        <li>Heater</li>
                        <li>Bar</li>
                        <li>SPA</li>
                        <li>Television (TV)</li>
                    </ul>
                    <a href="facilities.php" class="btn btn-outline-primary">More Facilities →</a>
                </div>
                <div class="col-md-6">
                    <h3 style="color: var(--primary-color);">Reviews</h3>
                    <div class="review-item mb-3">
                        <strong>Super</strong>
                        <div class="star-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                    <div class="review-item mb-3">
                        <strong>Good</strong>
                        <div class="star-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                    <div class="review-item">
                        <strong>Excellent</strong>
                        <div class="star-rating">
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                            <i class="bi bi-star-fill"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>BookEase</h5>
                    <p>Experience luxury and comfort in the heart of Nepal. Your perfect stay awaits.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="homepage.php" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="rooms.php" class="text-light text-decoration-none">Rooms</a></li>
                        <li><a href="facilities.php" class="text-light text-decoration-none">Facilities</a></li>
                        <li><a href="contact.php" class="text-light text-decoration-none">Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2024 BookEase. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // DOM elements for registration
        const registrationForm = document.getElementById('registrationForm');
        const registrationSuccess = document.getElementById('registrationSuccess');
        const passwordInput = document.getElementById('password');
        const passwordStrength = document.getElementById('passwordStrength');
        const strengthMeter = document.getElementById('strengthMeter');
        const fileInput = document.getElementById('picture');
        const fileNameDisplay = document.getElementById('fileName');
        const dobInput = document.getElementById('dob');
        
        // Set min date for check-in to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin').min = today;
        
        // Update checkout min date based on check-in
        document.getElementById('checkin').addEventListener('change', function() {
            document.getElementById('checkout').min = this.value;
        });
        
        // Set max date for date of birth (13 years ago)
        window.addEventListener('load', function() {
            const today = new Date();
            const minDate = new Date(today.getFullYear() - 13, today.getMonth(), today.getDate());
            const formattedMinDate = minDate.toISOString().split('T')[0];
            dobInput.setAttribute('max', formattedMinDate);
        });
        
        // Display selected file name
        fileInput.addEventListener('change', function(e) {
            const fileName = e.target.files.length > 0 ? e.target.files[0].name : 'No file chosen';
            fileNameDisplay.textContent = fileName;
            
            // Validate file if selected
            if (e.target.files.length > 0) {
                validateFile(e.target.files[0], 'pictureError');
            }
        });
        
        // Real-time password strength indicator
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            
            if (password.length === 0) {
                passwordStrength.style.display = 'none';
                return;
            }
            
            passwordStrength.style.display = 'block';
            
            // Calculate password strength
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 10;
            
            // Character variety checks
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 25;
            
            // Cap at 100
            strength = Math.min(strength, 100);
            
            // Update strength meter
            strengthMeter.style.width = `${strength}%`;
            
            // Update color based on strength
            if (strength < 40) {
                strengthMeter.style.backgroundColor = '#e74c3c'; // Red
            } else if (strength < 70) {
                strengthMeter.style.backgroundColor = '#f39c12'; // Orange
            } else {
                strengthMeter.style.backgroundColor = '#2ecc71'; // Green
            }
        });
        
        // Real-time validation for registration fields
        document.querySelectorAll('#registrationForm input, #registrationForm textarea').forEach(element => {
            // Validate on blur (when user leaves the field)
            element.addEventListener('blur', function() {
                validateRegistrationField(this);
            });
            
            // Remove error styling when user starts typing
            element.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const errorId = this.id + 'Error';
                document.getElementById(errorId).style.display = 'none';
            });
        });
        
        // Registration form submission handler
        registrationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset previous success message
            registrationSuccess.style.display = 'none';
            
            // Validate all fields
            let isValid = true;
            
            const fields = [
                {id: 'name', type: 'text', minLength: 2},
                {id: 'phone', type: 'phone'},
                {id: 'address', type: 'text', minLength: 10},
                {id: 'pincode', type: 'pincode'},
                {id: 'dob', type: 'date'},
                {id: 'email', type: 'email'},
                {id: 'password', type: 'password'},
                {id: 'confirmPassword', type: 'confirmPassword'}
            ];
            
            fields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!validateRegistrationField(element)) {
                    isValid = false;
                }
            });
            
            // Validate file if selected
            if (fileInput.files.length > 0) {
                if (!validateFile(fileInput.files[0], 'pictureError')) {
                    isValid = false;
                }
            }
            
            // If all validations pass
            if (isValid) {
                // Show success message
                registrationSuccess.style.display = 'block';
                
                // Simulate form submission
                const registerBtn = document.getElementById('registerBtn');
                const originalText = registerBtn.textContent;
                registerBtn.textContent = '✓ Registration Successful!';
                registerBtn.disabled = true;
                
                // Reset form after 2 seconds (simulation only)
                setTimeout(() => {
                    registrationForm.reset();
                    fileNameDisplay.textContent = 'No file chosen';
                    registerBtn.textContent = originalText;
                    registerBtn.disabled = false;
                    passwordStrength.style.display = 'none';
                    
                    // Close modal after 3 seconds
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('registrationModal'));
                        modal.hide();
                    }, 3000);
                }, 2000);
            }
        });
        
        // Function to validate individual registration field
        function validateRegistrationField(field) {
            const value = field.value.trim();
            const errorElement = document.getElementById(field.id + 'Error');
            
            // Hide error initially
            errorElement.style.display = 'none';
            field.classList.remove('is-invalid');
            
            let isValid = true;
            let errorMessage = '';
            
            // Field-specific validation
            switch(field.id) {
                case 'name':
                    if (value.length < 2) {
                        errorMessage = 'Please enter a valid name (at least 2 characters)';
                        isValid = false;
                    }
                    break;
                    
                case 'phone':
                    const phoneRegex = /^[0-9]{10,15}$/;
                    if (!phoneRegex.test(value)) {
                        errorMessage = 'Please enter a valid phone number (10-15 digits)';
                        isValid = false;
                    }
                    break;
                    
                case 'address':
                    if (value.length < 10) {
                        errorMessage = 'Please enter a valid address (at least 10 characters)';
                        isValid = false;
                    }
                    break;
                    
                case 'pincode':
                    const pincodeRegex = /^[0-9]{4,10}$/;
                    if (!pincodeRegex.test(value)) {
                        errorMessage = 'Please enter a valid pincode (4-10 digits)';
                        isValid = false;
                    }
                    break;
                    
                case 'dob':
                    if (value === '') {
                        errorMessage = 'Please select your date of birth';
                        isValid = false;
                    } else {
                        const birthDate = new Date(value);
                        const today = new Date();
                        const age = today.getFullYear() - birthDate.getFullYear();
                        if (age < 13) {
                            errorMessage = 'You must be at least 13 years old to register';
                            isValid = false;
                        }
                    }
                    break;
                    
                case 'email':
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        errorMessage = 'Please enter a valid email address';
                        isValid = false;
                    }
                    break;
                    
                case 'password':
                    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{8,}$/;
                    if (!passwordRegex.test(value)) {
                        errorMessage = 'Password must be at least 8 characters with letters and numbers';
                        isValid = false;
                    }
                    break;
                    
                case 'confirmPassword':
                    const password = document.getElementById('password').value;
                    if (value !== password) {
                        errorMessage = 'Passwords do not match';
                        isValid = false;
                    }
                    break;
            }
            
            // Show error if any
            if (!isValid) {
                errorElement.textContent = errorMessage;
                errorElement.style.display = 'block';
                field.classList.add('is-invalid');
            }
            
            return isValid;
        }
        
        // Function to validate file
        function validateFile(file, errorId) {
            const errorElement = document.getElementById(errorId);
            errorElement.style.display = 'none';
            document.getElementById('picture').classList.remove('is-invalid');
            
            let isValid = true;
            let errorMessage = '';
            
            // Check file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                errorMessage = 'Please select a valid image file (JPG, PNG, GIF)';
                isValid = false;
            }
            
            // Check file size (5MB max)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                errorMessage = 'Image size should be less than 5MB';
                isValid = false;
            }
            
            // Show error if any
            if (!isValid) {
                errorElement.textContent = errorMessage;
                errorElement.style.display = 'block';
                document.getElementById('picture').classList.add('is-invalid');
            }
            
            return isValid;
        }
        
        // Reset form when modal is closed
        document.getElementById('registrationModal').addEventListener('hidden.bs.modal', function () {
            registrationForm.reset();
            fileNameDisplay.textContent = 'No file chosen';
            registrationSuccess.style.display = 'none';
            passwordStrength.style.display = 'none';
            
            // Remove all error states
            document.querySelectorAll('#registrationForm .is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.style.display = 'none';
            });
        });
    </script>
</body>
</html>