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
            <li><a href="../auth/logout.php">Logout</li>
        </ul>
    </nav>
    </div>
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
        background-color:#333;
        /* Set the navbar background to transparent */
        padding: 0 0;
        /* Remove any extra padding */
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        height: 100px;
        /* Set fixed height to 70px */
        z-index: 1000;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        /* Subtle shadow to maintain visibility */
        transition: top 0.3s ease-in-out, background-color 0.3s ease;
        display: flex;
        align-items: center;
        /* Vertically center the contents */
    }

    /* Navbar container */
    .navbar .container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        /* Center items vertically */
        width: 100%;
        padding: 0 20px;
        /* Adjusted for left and right padding */
    }

    /* Logo styling */
    .navbar .logo {
        margin-right: 100px;
        margin-left: 30px;
        font-size: 25px;
        /* Reduced font size to fit within 70px height */
        font-weight: bold;
        color: #fff;
        /* Keep logo white for contrast */
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
        /* Reduced space between items */
    }

    .navbar nav ul li a {
        text-decoration: none;
        color: #fff;
        /* Keep links white for contrast */
        font-size: 20px;
        /* Adjusted font size */
        padding: 8px 10px;
        /* Reduced padding to make it fit within 70px height */
        border-radius: 25px;
        transition: background-color 0.3s ease, transform 0.3s ease;
    }

    /* Hover effect for links */
    .navbar nav ul li a:hover {
        background-color: #00c6ff;
        transform: scale(1.05);
        /* Slightly smaller scale for better fitting */
    }

    /* Mobile responsive adjustments */
    @media (max-width: 768px) {
        .navbar .container {
            flex-direction: row;
            /* Keep elements in a row */
            justify-content: space-between;
            /* Ensure space between logo and nav items */
            align-items: center;
            /* Vertically center the items */
            padding: 0 10px;
        }

        .navbar nav ul {
            display: flex;
            list-style: none;
            width: auto;
            flex-direction: row;
            /* Keep nav items in a row */
            margin-top: 0;
            padding: 0;
        }

        .navbar nav ul li {
            margin-right: 15px;
            /* Add space between list items */
            margin-bottom: 0;
            text-align: left;
        }

        .navbar .logo {
            margin-left: auto;
            /* Push the logo to the right */
            margin-bottom: 0;
        }
    }
</style>