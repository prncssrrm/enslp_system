<?php
// homepage.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>EnSLP FFC Manufacturing System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Inter', sans-serif;
}

body {
    background-color: #ffffff;
    color: #1f2937;
}

/* NAVBAR */
.navbar {
    background: linear-gradient(90deg, #0a1f44, #0d3b8c);
    padding: 18px 60px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.navbar h1 {
    color: #ffffff;
    font-size: 20px;
    font-weight: 700;
}

.navbar a {
    color: #ffffff;
    text-decoration: none;
    margin-left: 25px;
    font-size: 14px;
    transition: 0.3s;
}

.navbar a:hover {
    opacity: 0.7;
}

/* HERO */
.hero {
    padding: 110px 20px;
    text-align: center;
    background: linear-gradient(to bottom, #eef2ff, #e0e7ff);
}

.hero h2 {
    font-size: 36px;
    color: #0a1f44;
    margin-bottom: 15px;
    font-weight: 700;
}

.hero p {
    max-width: 700px;
    margin: auto;
    margin-bottom: 35px;
    color: #475569;
    font-size: 16px;
    line-height: 1.6;
}

.btn {
    background-color: #0d3b8c;
    color: white;
    padding: 12px 30px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    transition: 0.3s;
    box-shadow: 0 4px 12px rgba(13,59,140,0.2);
}

.btn:hover {
    background-color: #0a2e6e;
    transform: translateY(-2px);
}

/* STATS */
.stats {
    background-color: #0d3b8c;
    color: white;
    display: flex;
    justify-content: center;
    gap: 80px;
    padding: 40px 20px;
    text-align: center;
    flex-wrap: wrap;
}

.stats h2 {
    font-size: 28px;
}

.stats p {
    font-size: 14px;
    opacity: 0.9;
}

/* SECTION */
section {
    padding: 70px 20px;
    max-width: 1100px;
    margin: auto;
}

section h3 {
    text-align: center;
    margin-bottom: 35px;
    color: #0a1f44;
    font-size: 24px;
}

.about p {
    text-align: center;
    line-height: 1.8;
    color: #475569;
    max-width: 800px;
    margin: auto;
}

/* FEATURES */
.features {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
    justify-content: center;
}

.card {
    background-color: #f8fafc;
    padding: 30px;
    width: 320px;
    border-radius: 12px;
    transition: 0.3s;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.card:hover {
    transform: translateY(-6px);
}

.card h4 {
    margin-bottom: 15px;
    color: #0d3b8c;
}

.card ul {
    padding-left: 18px;
    font-size: 14px;
    color: #334155;
    line-height: 1.7;
}

/* FOOTER */
footer {
    text-align: center;
    padding: 20px;
    background-color: #0a1f44;
    color: white;
    font-size: 13px;
}

/* RESPONSIVE */
@media(max-width: 768px) {
    .navbar {
        flex-direction: column;
        gap: 10px;
    }

    .features {
        flex-direction: column;
        align-items: center;
    }

    .stats {
        flex-direction: column;
        gap: 25px;
    }
}
</style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <h1>EnSLP Inc.</h1>
    <div>
        <a href="#">Home</a>
        <a href="#about">About</a>
        <a href="#modules">Modules</a>
        <a href="login.php">Login</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <h2>EnSLP Inc. Integrated Management System</h2>
    <p>
        A centralized platform designed to manage Flexible Flat Cable (FFC) production — 
        from raw material tracking to process monitoring and quality control. 
        Improve efficiency, ensure accuracy, and streamline manufacturing operations.
    </p>
    <a href="login.php" class="btn">Access System</a>
</div>

<!-- STATS -->
<div class="stats">
    <div>
        <h2>2,500+</h2>
        <p>FFC Units Produced</p>
    </div>
    <div>
        <h2>120+</h2>
        <p>Active Production Batches</p>
    </div>
    <div>
        <h2>98.7%</h2>
        <p>Production Accuracy</p>
    </div>
</div>

<!-- ABOUT -->
<section class="about" id="about">
    <h3>About the System</h3>
    <p>
        The EnSLP Integrated Management System is designed specifically for 
        Flexible Flat Cable (FFC) manufacturing operations. It enables efficient 
        tracking of production processes, monitoring of batch progress, and 
        management of workforce activities — ensuring accurate, reliable, and 
        streamlined manufacturing performance.
    </p>
</section>

<!-- MODULES -->
<section id="modules">
    <h3>Manufacturing Modules</h3>
    <div class="features">

        <div class="card">
            <h4>Production Management</h4>
            <ul>
                <li></li>
                <li>Production Scheduling</li>
                <li>Real-Time Status Updates</li>
                <li>Output Monitoring</li>
            </ul>
        </div>

        <div class="card">
            <h4>Inventory Control</h4>
            <ul>
                <li>Raw Material Tracking</li>
                <li>Stock Level Monitoring</li>
                <li>Material Usage Logs</li>
                <li>Low Stock Alerts</li>
            </ul>
        </div>

        <div class="card">
            <h4>Quality Assurance</h4>
            <ul>
                <li>Defect Tracking</li>
                <li>Inspection Reports</li>
                <li>Quality Metrics</li>
                <li>Production Accuracy Analysis</li>
            </ul>
        </div>

    </div>
</section>

<!-- FOOTER -->
<footer>
© 2026 EnSLP Inc. || Lot 1 Blk 1 Science Park III, Sto. Tomas City Batangas, Philippines
</footer>

</body>
</html>