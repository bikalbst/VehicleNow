<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services We Offer - Car Rental</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Reset CSS */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body Styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f5f5f5;
    }
/* Navigation Bar Styling */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 40px;
    /* box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1); */
}

.content {
    font-family: "Poppins", sans-serif;
}

.logo {
    color: #ff7200;
    font-size: 24px;
}

.menu ul {
    display: flex;
    justify-content: center;
    list-style: none;
}

.menu ul li {
    margin-left: 20px;
}

.menu ul li a {
    text-decoration: none;
    color: #333333;
    font-weight: bold;
    transition: color 0.3s ease;
}

.menu ul li a:hover {
    color: #ff7200;
}

/* Services Section Styling */
.services {
    background-color: #fff;
    padding: 50px 20px;
    text-align: center;
}

.services h1 {
    margin-bottom: 40px;
    font-size: 36px;
    color: #333;
    text-transform: uppercase;
}

.services-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-around;
    gap: 20px;
}

.service-item {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    width: 100%;
    max-width: 300px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
}

.service-item:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
}

.service-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.service-item h2 {
    margin: 20px 0 10px;
    font-size: 20px;
    color: #ff7200;
}

.service-item p {
    font-size: 16px;
    color: #666;
    padding: 0 10px 20px;
}

/* Footer Styling */
.footer {
    background-color: #333;
    color: #fff;
    padding: 20px;
    text-align: center;
}

.social-icons a {
    color: #fff;
    font-size: 24px;
    margin: 0 10px;
    transition: color 0.3s ease;
}

.social-icons a:hover {
    color: #ff7200;
}

/* Media Queries for Responsive Design */
@media (max-width: 768px) {
    .navbar {
        flex-direction: column;
    }

    .menu ul {
        flex-direction: column;
    }

    .menu ul li {
        margin: 10px 0;
    }

    .services-container {
        flex-direction: column;
        align-items: center;
    }
}

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <header class="navbar">
        <div class="icon">
            <h1 class="logo">VehicleNow</h1>
        </div>
        <nav class="menu">
            <ul>
                <li><a href="index.php">HOME</a></li>
                <li><a href="aboutus.html">ABOUT</a></li>
                <li><a href="#services">SERVICES</a></li>
                <li><a href="contactus.html">CONTACT</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="content">
        <!-- Services Section -->
        <section id="services" class="services">
            <h1>Services We Offer</h1>
            <div class="services-container">
                <div class="service-item">
                    <img src="images/services/luz.jpg" alt="Luxury Car Rental">
                    <h2>Luxury Car Rental</h2>
                    <p>Experience the ultimate in luxury and comfort with our exclusive range of high-end cars.</p>
                </div>
                <div class="service-item">
                    <img src="images/services/fam.png" alt="Airport Pickup & Drop">
                    <h2>Family-Friendly SUV Rentals</h2>
                    <p>Discover our spacious and versatile SUV rentals designed for family comfort and safety.</p>
                </div>
                <div class="service-item">
                    <img src="images/services/electric.jpg" alt="Self-Drive Car Rental">
                    <h2>Eco-Friendly Car Rentals</h2>
                    <p>Go green with our eco-friendly car rental options. Reduce your carbon footprint and enjoy a smooth, sustainable ride with our electric and hybrid vehicles.</p>
                </div>
                <div class="service-item">
                    <img src="images/services/assist.png" alt="24/7 Roadside Assistance">
                    <h2>24/7 Roadside Assistance</h2>
                    <p>Travel with peace of mind knowing that help is just a call away, anytime, anywhere.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2024 VehicleNow. All Rights Reserved.</p>
        <div class="social-icons">
            <a href="https://www.facebook.com/"><ion-icon name="logo-facebook"></ion-icon></a>
            <a href="https://www.instagram.com/"><ion-icon name="logo-instagram"></ion-icon></a>
            <a href="https://myaccount.google.com/"><ion-icon name="logo-google"></ion-icon></a>
        </div>
    </footer>

    <script src="https://unpkg.com/ionicons@5.4.0/dist/ionicons.js"></script>
</body>
</html>