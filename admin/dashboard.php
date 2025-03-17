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
    WHERE MONTH(date) = ? AND YEAR(date) = ?
";
$stmt = $conn->prepare($attendanceOverviewQuery);
$stmt->bind_param("ii", $currentMonth, $currentYear);
$stmt->execute();
$attendanceOverviewResult = $stmt->get_result();
$attendanceOverviewData = $attendanceOverviewResult->fetch_assoc();
$stmt->close();

// Fetch weekly attendance data for the charts
$weeklyAttendanceQuery = "
    SELECT 
        WEEK(date) AS week,
        SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) AS present,
        SUM(CASE WHEN status = 'Absent' THEN 1 ELSE 0 END) AS absent,
        SUM(CASE WHEN status = 'Late' THEN 1 ELSE 0 END) AS late,
        SUM(CASE WHEN status = 'On Leave' THEN 1 ELSE 0 END) AS on_leave
    FROM attendance
    WHERE MONTH(date) = ? AND YEAR(date) = ?
    GROUP BY WEEK(date)
";
$stmt = $conn->prepare($weeklyAttendanceQuery);
$stmt->bind_param("ii", $currentMonth, $currentYear);
$stmt->execute();
$weeklyAttendanceResult = $stmt->get_result();
$weeklyAttendanceData = [];
while ($row = $weeklyAttendanceResult->fetch_assoc()) {
    $weeklyAttendanceData[] = $row;
}
$stmt->close();

// Fetch tasks for the logged-in user
$user_id = $_SESSION['user_id'];
$tasksQuery = "SELECT * FROM tasks WHERE user_id = ?";
$stmt = $conn->prepare($tasksQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasksResult = $stmt->get_result();
$tasks = [];
while ($row = $tasksResult->fetch_assoc()) {
    $tasks[] = $row;
}
$stmt->close();
?>

<?php include '../includes/navbar.php'; ?> <!-- Include navigation bar -->

<main style="flex: 1; padding: 20px;">
    <h1 style="margin-bottom: 20px;">Welcome to Admin Dashboard</h1>

    <div style="display: flex; flex-wrap: wrap; gap: 20px;">
        <!-- Total Employees -->
        <div style="flex: 1; min-width: 200px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); padding: 15px; display: flex; align-items: center; justify-content: space-between;">
            <div>
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
            <div style="width: 100px; height: 100px; border: 3px solid black; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <a href="add_employee.php">
            <img src="/employee-attendance-system/assets/employee.png" alt="Attendance Icon" style="width: 100%; height: 100%; object-fit: cover;">
        </a>            </div>
        </div

            <!-- Total Attendance Records -->
        <div style="flex: 1; min-width: 200px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); padding: 15px; display: flex; align-items: center; justify-content: space-between;">
            <div>
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
                <a href="add_attendance.php" class="btn">Add New Attendance</a>
            </div>
            <div style="width: 100px; height: 100px; border: 3px solid black; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <a href="add_attendance.php">
            <img src="/employee-attendance-system/assets/attendance.png" alt="Attendance Icon" style="width: 100%; height: 100%; object-fit: cover;">
        </a>
            </div>
        </div>
    </div> <!-- Closing div for flex container -->

    <!-- Charts Section -->
    <div style="margin-top: 40px;margin-bottom: 40px;">
        <h2>Attendance Overview</h2>
        <div style="display: flex; gap: 30px; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 150px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); padding: 15px;">
                <canvas id="attendanceChart" style="width: 100%; height: 300px;"></canvas> <!-- Adjusted height -->
            </div>
            <div style="flex: 1; min-width: 150px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); padding: 15px;">
                <canvas id="monthlyAttendanceChart" style="width: 100%; height: 400px;"></canvas>
            </div>
            <div style="flex: 1; min-width: 150px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); padding: 15px;">
                <div>
                    <h2>To-Do List</h2>
                    <form id="todoForm">
                        <input type="text" id="taskInput" placeholder="Enter a new task" required>
                        <button type="submit">Add Task</button>
                    </form>
                    <ul id="taskList">
                        <?php foreach ($tasks as $task) { ?>
                            <li class="task-item" data-id="<?php echo $task['id']; ?>">
                                <span class="task-text <?php echo $task['completed'] ? 'completed' : ''; ?>"><?php echo htmlspecialchars($task['task_text']); ?></span>
                                <button class="complete-btn"><?php echo $task['completed'] ? 'Undo' : 'Complete'; ?></button>
                                <button class="delete-btn">Delete</button>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
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
                <?php echo $attendanceOverviewData['present'] ?? 0; ?>,
                <?php echo $attendanceOverviewData['absent'] ?? 0; ?>,
                <?php echo $attendanceOverviewData['late'] ?? 0; ?>,
                <?php echo $attendanceOverviewData['on_leave'] ?? 0; ?>
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

    // Monthly Attendance Data for Bar Chart
    const monthlyAttendanceLabels = <?php echo json_encode(array_column($weeklyAttendanceData, 'week')); ?>;
    const monthlyAttendanceBarData = {
        labels: monthlyAttendanceLabels,
        datasets: [{
            label: 'Present',
            data: <?php echo json_encode(array_column($weeklyAttendanceData, 'present')); ?>,
            backgroundColor: '#4CAF50'
        }, {
            label: 'Absent',
            data: <?php echo json_encode(array_column($weeklyAttendanceData, 'absent')); ?>,
            backgroundColor: '#F44336'
        }, {
            label: 'Late',
            data: <?php echo json_encode(array_column($weeklyAttendanceData, 'late')); ?>,
            backgroundColor: '#FFC107'
        }, {
            label: 'On Leave',
            data: <?php echo json_encode(array_column($weeklyAttendanceData, 'on_leave')); ?>,
            backgroundColor: '#2196F3'
        }]
    };

    const ctxMonthlyBar = document.getElementById('monthlyAttendanceChart').getContext('2d');
    new Chart(ctxMonthlyBar, {
        type: 'bar',
        data: monthlyAttendanceBarData,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Attendance'
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Week'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Number of Employees'
                    },
                    beginAtZero: true
                }
            }
        }
    });

    // To-Do List Functionality
    const todoForm = document.getElementById('todoForm');
    const taskInput = document.getElementById('taskInput');
    const taskList = document.getElementById('taskList');

    todoForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const taskText = taskInput.value.trim();
        if (taskText !== '') {
            addTask(taskText);
            taskInput.value = '';
        }
    });

    function addTask(taskText) {
        fetch('add_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    task_text: taskText
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const listItem = document.createElement('li');
                    listItem.className = 'task-item';
                    listItem.dataset.id = data.task_id;

                    const taskSpan = document.createElement('span');
                    taskSpan.textContent = taskText;
                    taskSpan.className = 'task-text';

                    const completeButton = document.createElement('button');
                    completeButton.textContent = 'Complete';
                    completeButton.className = 'complete-btn';
                    completeButton.addEventListener('click', function() {
                        toggleCompleteTask(listItem);
                    });

                    const deleteButton = document.createElement('button');
                    deleteButton.textContent = 'Delete';
                    deleteButton.className = 'delete-btn';
                    deleteButton.addEventListener('click', function() {
                        deleteTask(listItem);
                    });

                    listItem.appendChild(taskSpan);
                    listItem.appendChild(completeButton);
                    listItem.appendChild(deleteButton);
                    taskList.appendChild(listItem);
                }
            });
    }

    function toggleCompleteTask(listItem) {
        const taskId = listItem.dataset.id;
        const taskSpan = listItem.querySelector('.task-text');
        const completeButton = listItem.querySelector('.complete-btn');
        const completed = !taskSpan.classList.contains('completed');

        fetch('toggle_complete_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    task_id: taskId,
                    completed: completed
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    taskSpan.classList.toggle('completed');
                    completeButton.textContent = completed ? 'Undo' : 'Complete';
                }
            });
    }

    function deleteTask(listItem) {
        const taskId = listItem.dataset.id;

        fetch('delete_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    task_id: taskId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    taskList.removeChild(listItem);
                }
            });
    }

    document.querySelectorAll('.complete-btn').forEach(button => {
        button.addEventListener('click', function() {
            toggleCompleteTask(button.closest('.task-item'));
        });
    });

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            deleteTask(button.closest('.task-item'));
        });
    });
</script>
<style>
    .task-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .task-text {
        flex: 1;
    }

    .completed {
        text-decoration: line-through;
        color: #888;
    }

    .complete-btn,
    .delete-btn {
        margin-left: 10px;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .complete-btn {
        background-color: #4CAF50;
        color: white;
    }

    .delete-btn {
        background-color: #F44336;
        color: white;
    }
</style>