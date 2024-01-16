<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bot Cloner</title>
  <style>
body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f4f4f4;
      overflow-x: hidden; /* Prevent horizontal scroll */
    }

    .header-banner {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background-color: #007bff;
      color: #fff;
      padding: 10px 20px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .banner-image {
      border-radius: 50%;
      width: 40px;
      height: 40px;
      object-fit: cover;
    }

    .banner-slides {
      font-size: 30px;
      color: #fff;
      cursor: pointer;
      transition: transform 0.3s ease-in-out;
    }

    .banner-slides:hover {
      transform: scale(1.1);
    }

    .container {
      width: 80%;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      max-width: 600px; /* Added max-width for responsiveness */
    }

    h1 {
      text-align: center;
      color: #333;
    }

    form {
      margin-top: 20px;
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 5px;
      color: #555;
    }

    input[type="text"],
    input[type="submit"] {
      padding: 10px;
      width: 100%;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    input[type="submit"] {
      background-color: #007bff;
      color: #fff;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
      background-color: #0056b3;
    }

    /* Adjustments for smaller screens */
    @media only screen and (max-width: 600px) {
      .container {
        width: 90%;
        padding: 15px;
      }

      input[type="text"],
      input[type="submit"] {
        width: 90%;
      }
    }

    /* Styling for the submit button */
    input[type="submit"] {
      font-size: 16px;
    }

    /* Styling for error message (optional) */
    .error-message {
      color: red;
      font-size: 14px;
      margin-top: 5px;
    }

    /* Sidebar Styles */
    .sidebar {
      position: fixed;
      top: 0;
      left: -300px;
      width: 300px;
      height: 100%;
      background-color: #333; /* Updated background color */
      transition: left 0.4s ease;
      z-index: 999;
      padding-top: 20px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2); /* Added box shadow */
    }
  .btn-large {
            padding: 1em 2em;
            font-size: calc(1em + 0.3vw);
            background-color: #912c18; 
            color: #fff;
            border-radius: 10px;
            border: none;
            display: block;
            margin: 4vh auto; 
        }
         .custom-btn {
    background-color: #3498db; 
    color: white;
    border: none;
    cursor: pointer;
    font-size: 10px;
    text-align: center; 
}
    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sidebar ul li {
      padding: 15px;
      color: #fff;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .sidebar ul li:hover {
      background-color: #555;
    }

    .show-sidebar {
      left: 0;
    }

    /* Additional styles for sidebar icon */
    .banner-slides:hover {
      background-color: #005AAA;
    }

    /* Stylish alert-like message */
    .alert-message {
      text-align: center;
      padding: 15px;
      margin-top: 20px;
      font-size: 16px;
      background-color: #3498db;
      color: white;
      border-radius: 8px;
      display: none;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      animation: glitter 1s infinite;
    }

    @keyframes glitter {
      0% { background-color: #3498db; }
      50% { background-color: #0056b3; }
      100% { background-color: #3498db; }
    }

    .alert-message.show {
      display: block;
      opacity: 1;
    }

    /* Styling for Active Bots count */
    .active-bots {
      font-size: 18px;
      color: green;
      margin-top: 10px;
    }

    /* Styling for response messages */
    .response-message {
      text-align: center;
      font-size: 16px;
      margin-top: 10px;
    }

    .error-message {
      color: red;
    }

    .success-message {
      color: green;
    }
    .sidebar ul li {
      padding: 15px;
      color: #fff;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .sidebar ul li:hover {
      background-color: #555;
    }
  </style>
</head>
<body>
  <!-- Header and container -->
  <div class="header-banner">
    <img src="https://i.postimg.cc/TYVwshss/20231216-232336.jpg" alt="Profile Picture" class="banner-image">
    <div class="banner-slides" onclick="toggleSidebar()">&#9776;</div>
  </div>
  <div class="container">
    <h1>Chatgpt + Image Gen Ai</h1>

    <?php
    // Fetching and displaying Active Bots Count
    $apiUrl = "./bot.php";
    $apiResponse = file_get_contents($apiUrl);

    // Decode JSON response
    $data = json_decode($apiResponse, true);

    // Display the response
    if ($data && isset($data['active_bots_count'])) {
        echo '<p class="active-bots">Active Bots Count: ' . $data['active_bots_count'] . '</p>';
    } else {
        echo '<p class="response-message error-message">Failed to fetch Active Bots Count.</p>';
    }
    ?>

    <!-- Form for cloning bot -->
    <form action="" method="post">
      <label for="botToken">Enter Bot Token:</label>
      <input type="text" id="botToken" name="botToken" required>
      <input type="submit" name="submit" value="Clone Bot">
    </form>

    <?php

    if (isset($_POST['submit'])) {
    
        if (isset($_POST['botToken']) && !empty($_POST['botToken'])) {
         
            $botToken = $_POST['botToken'];
            $apiUrl = "./bot.php?bottoken=" . urlencode($botToken);
            
        
            $apiResponse = file_get_contents($apiUrl);

         
            $data = json_decode($apiResponse, true);

          
            if ($data && $data['status'] === 'success') {
                echo '<p class="active-bots">Active Bots Count: ' . $data['active_bots_count'] . '</p>';
                echo '<div class="alert-message show">Bot hosted successfully. Enjoy!</div>';
            } else {
                echo '<p class="response-message error-message">Abe Token Thikse Dalna Token Galat here😎😎</p>';
            }
        } else {
            echo '<p class="response-message error-message">Please enter a bot token.</p>';
        }
    }
    ?>
  </div>
  <center>
        <a href="https://t.me/devsnp">
            <button class="btn-11 custom-btn" style="width: 40vw; height: 40px; margin-top: 10px;"><b style="color: white; ">Made by Nep Coder join:-@devsnp</b></button>
        </a>
    </center>
  <!-- Sidebar and other content -->
  <div class="sidebar" id="sidebar">
    <ul>
      <li onclick="redirectTo('home')">Home</li>
      <li onclick="redirectTo('features')">Features</li>
      <li onclick="redirectTo('about')">About Us</li>
    </ul>
  </div>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('show-sidebar');
    }

    function redirectTo(page) {
      if (page === 'home') {
        window.location.href = 'https://t.me/devsnp'; // Replace 'https://example.com/home' with your desired location
      } else if (page === 'features') {
        window.location.href = 'https://t.me/devsnp'; // Replace 'https://example.com/features' with your desired location
      } else if (page === 'about') {
        window.location.href = 'https://t.me/devsnp'; // Replace 'https://example.com/about' with your desired location
      }
    }

    window.onload = function() {
      <?php

      if (isset($_POST['submit']) && isset($data) && $data['status'] === 'success') {
        echo 'document.querySelector(".alert-message").classList.add("show");';
      }
      ?>
    };
  </script>
</body>
</html>
