<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php"); // Redirect to login if not authenticated
    exit;
}
?>

<header class="navbar">
    <h1 class="logo">Employee Attendance Management System</h1>
    <nav>
        <ul class="nav-links">
            <li><a href="../admin/dashboard.php">Dashboard</a></li>
            <li><a href="../admin/manage_employees.php">Manage Employees</a></li>
            <li><a href="../admin/manage_attendance.php">Manage Attendance</a></li>
            <li><a href="../admin/reports.php">Reports</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<script>
    let lastScrollTop = 0; // Keep track of the last scroll position
    const navbar = document.querySelector('.navbar'); // Get the navbar element

    // Event listener for scroll events
    window.addEventListener('scroll', function() {
        let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        // Check if the user is scrolling down or up
        if (currentScroll > lastScrollTop) {
            // Scrolling down
            navbar.style.top = "-80px"; // Hide navbar by moving it above the viewport
        } else {
            // Scrolling up
            navbar.style.top = "0"; // Show navbar
        }

        // Update the last scroll position
        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    });
</script>
<style>
    /* Navbar */
    .navbar {
        background-color: rgb(63, 16, 32); /* Primary color */
        padding: 0 0;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 100px;
        z-index: 1000;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        transition: top 0.3s ease-in-out, background-color 0.3s ease;
        display: flex;
        align-items: center;
    }

    /* Navbar container */
    .navbar .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 0 20px;
    }

    /* Logo styling */
    .navbar .logo {
        margin-right: 100px;
        margin-left: 30px;
        font-size: 25px;
        font-weight: bold;
        color: rgb(245, 239, 231); /* Lightest color */
        letter-spacing: 1px;
        text-transform: uppercase;
        transition: color 0.3s ease;
    }

    /* Navigation links styling */
    .navbar nav ul {
        display: flex;
        list-style: none;
    }

    .navbar nav ul li {
        margin-right: 50px;
    }

    .navbar nav ul li a {
        text-decoration: none;
        color: rgb(245, 239, 231); /* Lightest color */
        font-size: 20px;
        padding: 8px 10px;
        border-radius: 25px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    /* Hover effect for links */
    .navbar nav ul li a:hover {
        background-color: rgb(62, 88, 121); /* Secondary color */
        transform: scale(1.05);
    }

    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .navbar .container {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
        }

        .navbar nav ul {
            display: flex;
            list-style: none;
            width: auto;
            flex-direction: row;
            margin-top: 0;
            padding: 0;
        }

        .navbar nav ul li {
            margin-right: 15px;
            margin-bottom: 0;
            text-align: left;
        }

        .navbar .logo {
            margin-left: auto;
            margin-bottom: 0;
        }
    }
</style>