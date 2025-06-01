# Car Rental System - Payment Integration

This project is a car rental website with multiple payment options including Cash, Debit Card, and eSewa integration.

## Database Setup

Before using the website, you need to set up the database. There are two ways to do this:

### Option 1: Using the PHP Setup Script (Recommended)

1. Make sure your XAMPP server is running (Apache and MySQL)
2. Navigate to http://localhost/6thsem/setup_database.php in your browser
3. This script will automatically:
   - Create the database if it doesn't exist
   - Create all required tables
   - Add sample data including car listings and an admin user
   - Set up necessary indexes and relationships

### Option 2: Using the SQL Script Directly

If you prefer using phpMyAdmin or another MySQL client:

1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Go to the SQL tab
3. Copy the contents of the `setup_database.sql` file in this project
4. Paste and execute the SQL commands

## Payment System

The car rental system includes three payment options:

1. **Cash Payment**

   - Generates a professional invoice/bill
   - Allows downloading the bill as PDF
   - Provides option to email the bill to the customer

2. **Debit Card Payment**

   - Visual debit card interface that updates as you type
   - Secure form for entering card details
   - Simulated payment processing

3. **eSewa Payment**
   - Integration with Nepal's popular digital wallet
   - Simple ID/MPIN based payment interface
   - Alternative payment options

## Admin Login

After database setup, you can log in as admin using:

- Email: admin@example.com
- Password: admin123

## Files and Structure

- `booking.php` - Main booking page with payment method selection
- `payment-bill.php` - Cash payment option with bill generation
- `card-payment.php` - Debit card payment interface
- `esewa-payment.php` - eSewa payment integration
- `booking-confirmation.php` - Confirmation page after successful booking

## Using the System

1. Browse available cars on the homepage
2. Select a car and booking dates
3. Complete the booking form and select a payment method
4. Process payment through your chosen method
5. Receive booking confirmation

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web browser with JavaScript enabled
- XAMPP, WAMP, or similar local server environment
