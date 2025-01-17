<?php 
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login_teacher.php");
    exit(); // Always exit after a header redirect
}

include '../config.php';

// Ensure database connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/dash.css">
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="sidebar">
    <div class="logo-details">
        <i class='bx bx-diamond'></i>
        <span class="logo_name">Welcome</span>
    </div>
    <ul class="nav-links">
        <!-- Sidebar navigation items -->
        <li><a href="#" class="active"><i class='bx bx-grid-alt'></i><span class="links_name">Dashboard</span></a></li>
        <li><a href="exams.php"><i class='bx bx-book-content'></i><span class="links_name">Exams</span></a></li>
        <li><a href="results.php"><i class='bx bxs-bar-chart-alt-2'></i><span class="links_name">Results</span></a></li>
        <li><a href="records.php"><i class='bx bxs-user-circle'></i><span class="links_name">Records</span></a></li>
        <li><a href="messages.php"><i class='bx bx-message'></i><span class="links_name">Messages</span></a></li>
        <li><a href="settings.php"><i class='bx bx-cog'></i><span class="links_name">Settings</span></a></li>
        <li><a href="help.php"><i class='bx bx-help-circle'></i><span class="links_name">Help</span></a></li>
        <li class="log_out"><a href="../logout.php"><i class='bx bx-log-out-circle'></i><span class="links_name">Log out</span></a></li>
    </ul>
</div>
<section class="home-section">
    <nav>
        <div class="sidebar-button">
            <i class='bx bx-menu sidebarBtn'></i>
            <span class="dashboard">Teacher's Dashboard</span>
        </div>
        <div class="profile-details">
            <img src="<?php echo htmlspecialchars($_SESSION['img'] ?? 'default.png'); ?>" alt="pro">
            <span class="admin_name"><?php echo htmlspecialchars($_SESSION['fname'] ?? 'Teacher'); ?></span>
        </div>
    </nav>

    <div class="home-content">
        <div class="overview-boxes">
            <!-- Total students -->
            <div class="box">
                <div class="right-side">
                    <div class="box-topic">Records</div>
                    <div class="number">
                        <?php  
                        $sql = "SELECT COUNT(*) AS count FROM student";
                        $result = mysqli_query($conn, $sql);
                        echo $result ? mysqli_fetch_assoc($result)['count'] : 0;
                        ?>
                    </div>
                    <div class="brief"><span class="text">Total number of students</span></div>
                </div>
                <i class='bx bx-user ico'></i>
            </div>
            <!-- Total exams -->
            <div class="box">
                <div class="right-side">
                    <div class="box-topic">Exams</div>
                    <div class="number">
                        <?php  
                        $sql = "SELECT COUNT(*) AS count FROM exm_list";
                        $result = mysqli_query($conn, $sql);
                        echo $result ? mysqli_fetch_assoc($result)['count'] : 0;
                        ?>
                    </div>
                    <div class="brief"><span class="text">Total number of exams</span></div>
                </div>
                <i class='bx bx-book ico two'></i>
            </div>
            <!-- Total results -->
            <div class="box">
                <div class="right-side">
                    <div class="box-topic">Results</div>
                    <div class="number">
                        <?php  
                        $sql = "SELECT COUNT(*) AS count FROM atmpt_list";
                        $result = mysqli_query($conn, $sql);
                        echo $result ? mysqli_fetch_assoc($result)['count'] : 0;
                        ?>
                    </div>
                    <div class="brief"><span class="text">Number of available results</span></div>
                </div>
                <i class='bx bx-line-chart ico three'></i>
            </div>
            <!-- Announcements -->
            <div class="box">
                <div class="right-side">
                    <div class="box-topic">Announcements</div>
                    <div class="number">
                        <?php  
                        $sql = "SELECT COUNT(*) AS count FROM message";
                        $result = mysqli_query($conn, $sql);
                        echo $result ? mysqli_fetch_assoc($result)['count'] : 0;
                        ?>
                    </div>
                    <div class="brief"><span class="text">Total number of messages sent</span></div>
                </div>
                <i class='bx bx-paper-plane ico four'></i>
            </div>
        </div>

        <!-- Recent Results -->
        <div class="stat-boxes">
            <div class="recent-stat box">
                <div class="title">Recent results</div>
                <table id="res">
                    <thead>
                        <tr>
                            <th style="width:20%">Date</th>
                            <th style="width:35%">Name</th>
                            <th style="width:25%">Exam name</th>
                            <th style="width:20%">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM atmpt_list ORDER BY subtime DESC LIMIT 8";
                        $result = mysqli_query($conn, $sql);
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                $dptime = date("M d, Y", strtotime($row['subtime']));
                                $uname = $row['uname'];
                                $sql_name = "SELECT fname FROM student WHERE uname = ?";
                                $stmt_name = $conn->prepare($sql_name);
                                $stmt_name->bind_param('s', $uname);
                                $stmt_name->execute();
                                $result_name = $stmt_name->get_result();
                                $student = $result_name->fetch_assoc()['fname'] ?? "Not found";

                                $exid = $row['exid'];
                                $sql_exname = "SELECT exname FROM exm_list WHERE exid = ?";
                                $stmt_exname = $conn->prepare($sql_exname);
                                $stmt_exname->bind_param('s', $exid);
                                $stmt_exname->execute();
                                $result_exname = $stmt_exname->get_result();
                                $exam_name = $result_exname->fetch_assoc()['exname'] ?? "Not found";

                                echo "<tr>
                                        <td>$dptime</td>
                                        <td>$student</td>
                                        <td>$exam_name</td>
                                        <td>{$row['ptg']}%</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No recent results found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <div class="button">
                    <a href="results.php">See All</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="../js/script.js"></script>
</body>
</html>
