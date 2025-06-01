<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Success</title>
  <style>
    body {
      font-family: 'Nunito Sans', 'Helvetica Neue', sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden; /* Hide overflow to prevent scrollbars */
    }
    
    .video-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1; /* Place behind other content */
      object-fit: cover;
    }
    
    .card {
      background: rgba(255, 255, 255, 0.05); /* Semi-transparent white background */
      border-radius: 8px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      max-width: 600px;
      padding: 40px;
      text-align: center;
      z-index: 1; /* Ensure card is above video background */
    }
    
    .icon-container {
      border-radius: 50%;
      background-color: #88B04B;
      color: #ffffff;
      width: 120px;
      height: 120px;
      margin: 0 auto 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 60px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    }
    
    h1 {
      color: #88B04B;
      font-weight: 900;
      font-size: 36px;
      margin-bottom: 10px;
    }
    
    p {
      color: #404F5E;
      font-size: 20px;
      line-height: 1.6;
      margin-bottom: 20px;
    }
    
    #back {
      width: 180px;
      height: 50px;
      background-color: #ff7200;
      border: none;
      border-radius: 25px;
      font-size: 18px;
      color: #ffffff;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    
    #back:hover {
      background-color: #ff8c3b;
    }
    
    #back a {
      text-decoration: none;
      color: #ffffff;
      font-weight: bold;
      line-height: 50px;
      display: block;
      height: 100%;
    }
  </style>
</head>
<body>
  <!-- Video Background -->
  <video autoplay muted  class="video-background">
    <source src="css/success.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>

  <!-- Content -->
  <div class="card">
    <div class="icon-container">
      ✓
    </div>
    <h1>Payment Successful</h1> 
    <p>Thank you for your payment. Your rental request has been received and we'll be in touch shortly!</p>
    <button id="back"><a href="cardetails.php">Search Cars</a></button>
  </div>
</body>
</html>
