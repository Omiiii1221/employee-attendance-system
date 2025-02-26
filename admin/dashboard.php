<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php"); // Redirect to login if not authenticated
    exit;
}

// Set the page title
$page_title = "Admin Dashboard";

// Include database connection and header
include '../config/db.php';
include '../includes/header.php';

// Fetch data for the charts
$today = date('Y-m-d');
$currentMonth = date('m');
$currentYear = date('Y');

// Fetch attendance overview data
$attendanceOverviewQuery = "
    SELECT 
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present,
        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent,
        SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS late,
        SUM(CASE WHEN status = 'On Leave' THEN 1 ELSE 0 END) AS on_leave
    FROM attendance
    WHERE MONTH(date) = $currentMonth AND YEAR(date) = $currentYear
";
$attendanceOverviewResult = mysqli_query($conn, $attendanceOverviewQuery);
$attendanceOverviewData = mysqli_fetch_assoc($attendanceOverviewResult);

// Fetch weekly attendance data for the line chart
$weeklyAttendanceQuery = "
    SELECT 
        WEEK(date) AS week,
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present,
        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent,
        SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS late,
        SUM(CASE WHEN status = 'On Leave' THEN 1 ELSE 0 END) AS on_leave
    FROM attendance
    WHERE MONTH(date) = $currentMonth AND YEAR(date) = $currentYear
    GROUP BY WEEK(date)
";
$weeklyAttendanceResult = mysqli_query($conn, $weeklyAttendanceQuery);
$weeklyAttendanceData = [];
while ($row = mysqli_fetch_assoc($weeklyAttendanceResult)) {
    $weeklyAttendanceData[] = $row;
}

// Fetch monthly attendance data for the bar chart
$monthlyAttendanceQuery = "
    SELECT 
        WEEK(date) AS week,
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present,
        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent,
        SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS late,
        SUM(CASE WHEN status = 'On Leave' THEN 1 ELSE 0 END) AS on_leave
    FROM attendance
    WHERE MONTH(date) = $currentMonth AND YEAR(date) = $currentYear
    GROUP BY WEEK(date)
";
$monthlyAttendanceResult = mysqli_query($conn, $monthlyAttendanceQuery);
$monthlyAttendanceData = [];
while ($row = mysqli_fetch_assoc($monthlyAttendanceResult)) {
    $monthlyAttendanceData[] = $row;
}
?>

<?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->

<main style="flex: 1; padding: 20px;">
    <h1 style="margin-bottom: 20px;">Welcome to Admin Dashboard</h1>

    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        <!-- Total Employees -->
        <div style="flex: 1; min-width: 250px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px;">
            <h2>Total Employees</h2>
            <p style="font-size: 24px; color: #4CAF50;">
                <?php
                $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM employees");
                if ($result) {
                    $data = mysqli_fetch_assoc($result);
                    echo $data['total'];
                } else {
                    echo "Error fetching data.";
                }
                ?>
            </p>
            <a href="add_employee.php" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #6a4bc7; color: white; border-radius: 4px; text-decoration: none;">Add New Employee</a>
        </div>

        <!-- Total Attendance Records -->
        <div style="flex: 1; min-width: 250px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px;">
            <h2>Total Attendance Records</h2>
            <p style="font-size: 24px; color: #F44336;">
                <?php
                $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM attendance");
                if ($result) {
                    $data = mysqli_fetch_assoc($result);
                    echo $data['total'];
                } else {
                    echo "Error fetching data.";
                }
                ?>
            </p>
            <a href="manage_attendance.php" style="display: inline-block; margin-top: 10px; padding: 10px 20px; background-color: #6a4bc7; color: white; border-radius: 4px; text-decoration: none;">Manage Attendance</a>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="margin-top: 40px;">
        <h2>Attendance Overview</h2>
        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px;">
                <canvas id="attendanceChart" style="width: 100%; height: 400px;"></canvas>
            </div>
            <div style="flex: 1; min-width: 300px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px;">
                <canvas id="weeklyAttendanceChart" style="width: 100%; height: 400px;"></canvas>
            </div>
            <div style="flex: 1; min-width: 300px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px;">
                <canvas id="monthlyAttendanceChart" style="width: 100%; height: 400px;"></canvas>
            </div>
        </div>
    </div>

    <!-- Additional Charts -->
    <div style="margin-top: 20px; display: flex; gap: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 100px;">
            <h2>Monthly Attendance Line Chart</h2>
            <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px;">
                <canvas id="monthlyAttendanceLineChart"></canvas>
            </div>
        </div>

        <div style="flex: 1; min-width: 150px;">
            <h2>Monthly Attendance Bar Chart</h2>
            <div style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); padding: 20px;">
                <canvas id="monthlyAttendanceBarChart"></canvas>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?> <!-- Include footer -->

<!-- Include Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Attendance Overview Data
    const attendanceOverviewData = {
        labels: ['Present', 'Absent', 'Late', 'On Leave'],
        datasets: [{
            label: 'Attendance Status',
            data: [
                <?php echo $attendanceOverviewData['present']; ?>,
                <?php echo $attendanceOverviewData['absent']; ?>,
                <?php echo $attendanceOverviewData['late']; ?>,
                <?php echo $attendanceOverviewData['on_leave']; ?>
            ],
            backgroundColor: ['#4CAF50', '#F44336', '#FFC107', '#2196F3']
        }]
    };

    const ctx = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: attendanceOverviewData,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Attendance Overview'
                }
            }
        }
    });

    // Weekly Attendance Data for Line Chart
    const weeklyAttendanceLabels = <?php echo json_encode(array_column($weeklyAttendanceData, 'week')); ?>;
    const weeklyAttendanceLineData = {
        labels: weeklyAttendanceLabels,
        datasets: [{
            label: 'Present',
            data: <?php echo json_encode(array_column($weeklyAttendanceData, 'present')); ?>,
            borderColor: '#4CAF50',
            fill: false
        }, {
            label: 'Absent',
            data: <?php echo json_encode(array_column($weeklyAttendanceData, 'absent')); ?>,
            borderColor: '#F44336',
            fill: false
        }, {
            label: 'Late',
            data: <?php echo json_encode(array_column($weeklyAttendanceData, 'late')); ?>,
            borderColor: '#FFC107',
            fill: false
        }, {
            label: 'On Leave',
            data: <?php echo json_encode(array_column($weeklyAttendanceData, 'on_leave')); ?>,
            borderColor: '#2196F3',
            fill: false
        }]
    };

    const ctxLine = document.getElementById('weeklyAttendanceChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: weeklyAttendanceLineData,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Weekly Attendance'
                }
            }
        }
    });

    // Monthly Attendance Data for Bar Chart
    const monthlyAttendanceLabels = <?php echo json_encode(array_column($monthlyAttendanceData, 'week')); ?>;
    const monthlyAttendanceBarData = {
        labels: monthlyAttendanceLabels,
        datasets: [{
            label: 'Present',
            data: <?php echo json_encode(array_column($monthlyAttendanceData, 'present')); ?>,
            backgroundColor: '#4CAF50'
        }, {
            label: 'Absent',
            data: <?php echo json_encode(array_column($monthlyAttendanceData, 'absent')); ?>,
            backgroundColor: '#F44336'
        }, {
            label: 'Late',
            data: <?php echo json_encode(array_column($monthlyAttendanceData, 'late')); ?>,
            backgroundColor: '#FFC107'
        }, {
            label: 'On Leave',
            data: <?php echo json_encode(array_column($monthlyAttendanceData, 'on_leave')); ?>,
            backgroundColor: '#2196F3'
        }]
    };

    const ctxBar = document.getElementById('monthlyAttendanceChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: monthlyAttendanceBarData,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Attendance'
                }
            }
        }
    });
</script>