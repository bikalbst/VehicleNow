<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";

// Create connection to MySQL server (without selecting database)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected to MySQL server successfully.<br>";

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS vehiclenow";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db("vehiclenow");

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    USER_ID INT AUTO_INCREMENT PRIMARY KEY,
    FNAME VARCHAR(50) NOT NULL,
    LNAME VARCHAR(50) NOT NULL,
    EMAIL VARCHAR(100) NOT NULL UNIQUE,
    PHONE VARCHAR(15) NOT NULL,
    PASSWORD VARCHAR(255) NOT NULL,
    CREATION_DATE DATETIME DEFAULT CURRENT_TIMESTAMP,
    IS_ADMIN CHAR(1) DEFAULT 'N'
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully.<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Create cars table
$sql = "CREATE TABLE IF NOT EXISTS cars (
    CAR_ID INT AUTO_INCREMENT PRIMARY KEY,
    CAR_NAME VARCHAR(100) NOT NULL,
    CAR_IMG VARCHAR(255) NOT NULL,
    FUEL_TYPE VARCHAR(50) NOT NULL,
    CAPACITY INT NOT NULL,
    TRANSMISSION VARCHAR(50) NOT NULL,
    MODEL_YEAR INT NOT NULL,
    PRICE DECIMAL(10,2) NOT NULL,
    AVAILABLE CHAR(1) DEFAULT 'Y',
    ADDED_DATE DATETIME DEFAULT CURRENT_TIMESTAMP,
    DESCRIPTION TEXT
)";

if ($conn->query($sql) === TRUE) {
    echo "Cars table created successfully.<br>";
} else {
    echo "Error creating cars table: " . $conn->error . "<br>";
}

// Create booking table
$sql = "CREATE TABLE IF NOT EXISTS booking (
    BOOK_ID INT AUTO_INCREMENT PRIMARY KEY,
    CAR_ID INT NOT NULL,
    EMAIL VARCHAR(100) NOT NULL,
    BOOK_DATE DATE NOT NULL,
    FROM_DT DATE NOT NULL,
    TO_DT DATE NOT NULL,
    AMOUNT DECIMAL(10,2) NOT NULL,
    STATUS VARCHAR(20) DEFAULT 'Pending',
    PAYMENT_METHOD VARCHAR(50) DEFAULT 'Cash',
    CREATED_AT DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (CAR_ID) REFERENCES cars(CAR_ID),
    FOREIGN KEY (EMAIL) REFERENCES users(EMAIL)
)";

if ($conn->query($sql) === TRUE) {
    echo "Booking table created successfully.<br>";
} else {
    echo "Error creating booking table: " . $conn->error . "<br>";
}

// Check if admin user already exists
$checkAdmin = "SELECT * FROM users WHERE EMAIL='admin@example.com'";
$result = $conn->query($checkAdmin);

// Insert sample admin user if not exists
if ($result->num_rows == 0) {
    $sql = "INSERT INTO users (FNAME, LNAME, EMAIL, PHONE, PASSWORD, IS_ADMIN) 
            VALUES ('Admin', 'User', 'admin@example.com', '1234567890', MD5('admin123'), 'Y')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Admin user created successfully.<br>";
    } else {
        echo "Error creating admin user: " . $conn->error . "<br>";
    }
} else {
    echo "Admin user already exists.<br>";
}

// Check if we already have sample cars
$checkCars = "SELECT * FROM cars";
$result = $conn->query($checkCars);

// Insert sample cars if none exist
if ($result->num_rows == 0) {
    $sql = "INSERT INTO cars (CAR_NAME, CAR_IMG, FUEL_TYPE, CAPACITY, TRANSMISSION, MODEL_YEAR, PRICE, DESCRIPTION) VALUES
            ('Toyota Camry', 'camry.jpg', 'Petrol', 5, 'Automatic', 2022, 5000, 'Comfortable sedan with excellent fuel efficiency'),
            ('Honda Civic', 'civic.jpg', 'Petrol', 5, 'Manual', 2021, 4500, 'Reliable and sporty compact car'),
            ('Ford Mustang', 'mustang.jpg', 'Petrol', 4, 'Automatic', 2022, 8000, 'Iconic American muscle car with powerful engine'),
            ('Hyundai Tucson', 'tucson.jpg', 'Diesel', 5, 'Automatic', 2021, 6000, 'Compact SUV with modern features and comfort'),
            ('Maruti Swift', 'swift.jpg', 'Petrol', 5, 'Manual', 2020, 3500, 'Economical hatchback with good handling'),
            ('Tata Nexon', 'nexon.jpg', 'Diesel', 5, 'Manual', 2021, 4800, 'Compact SUV with excellent safety features'),
            ('Mahindra Thar', 'thar.jpg', 'Diesel', 4, 'Manual', 2021, 7500, 'Off-road capable SUV with rugged design'),
            ('Kia Seltos', 'seltos.jpg', 'Petrol', 5, 'Automatic', 2022, 5500, 'Feature-rich compact SUV with premium feel')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Sample cars added successfully.<br>";
    } else {
        echo "Error adding sample cars: " . $conn->error . "<br>";
    }
} else {
    echo "Sample cars already exist.<br>";
}

// Add PAYMENT_METHOD column to booking table if it doesn't exist
$checkColumn = "SHOW COLUMNS FROM booking LIKE 'PAYMENT_METHOD'";
$result = $conn->query($checkColumn);

if ($result->num_rows == 0) {
    $sql = "ALTER TABLE booking ADD COLUMN PAYMENT_METHOD VARCHAR(50) DEFAULT 'Cash' AFTER STATUS";
    
    if ($conn->query($sql) === TRUE) {
        echo "PAYMENT_METHOD column added to booking table.<br>";
    } else {
        echo "Error adding PAYMENT_METHOD column: " . $conn->error . "<br>";
    }
} else {
    echo "PAYMENT_METHOD column already exists in booking table.<br>";
}

// Create necessary indexes for better performance
$sql = "CREATE INDEX IF NOT EXISTS idx_car_available ON cars(AVAILABLE)";
if ($conn->query($sql) === TRUE) {
    echo "Index on cars.AVAILABLE created.<br>";
} else {
    echo "Error creating index on cars.AVAILABLE: " . $conn->error . "<br>";
}

$sql = "CREATE INDEX IF NOT EXISTS idx_booking_status ON booking(STATUS)";
if ($conn->query($sql) === TRUE) {
    echo "Index on booking.STATUS created.<br>";
} else {
    echo "Error creating index on booking.STATUS: " . $conn->error . "<br>";
}

$sql = "CREATE INDEX IF NOT EXISTS idx_booking_dates ON booking(FROM_DT, TO_DT)";
if ($conn->query($sql) === TRUE) {
    echo "Index on booking dates created.<br>";
} else {
    echo "Error creating index on booking dates: " . $conn->error . "<br>";
}

echo "<br><strong>Database setup completed successfully!</strong><br>";
echo "You can now <a href='index.php'>visit the homepage</a> or <a href='login.php'>login</a> with admin@example.com / admin123";

// Close connection
$conn->close();
?> 