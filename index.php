<?php
// Database connection
$conn = new mysqli("localhost","root","","events_db");
if($conn->connect_error){
    die("Connection failed: ".$conn->connect_error);
}

// Insert event
if(isset($_POST['submit'])){
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $desc = $_POST['description'];
    $status = $_POST['status'];

    $sql = "INSERT INTO events (event_name,event_date,description,status) VALUES ('$name','$date','$desc','$status')";
    if($conn->query($sql)){
        $msg = "✅ Event added successfully!";
    } else {
        $msg = "❌ Error: ".$conn->error;
    }
}

// Delete event
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM events WHERE id=$id");
    $msg = "🗑️ Event deleted!";
}

// Edit event
$editData = null;
if(isset($_GET['edit'])){
    $id = $_GET['edit'];
    $res = $conn->query("SELECT * FROM events WHERE id=$id");
    $editData = $res->fetch_assoc();
}

// Update event
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $name = $_POST['event_name'];
    $date = $_POST['event_date'];
    $desc = $_POST['description'];
    $status = $_POST['status'];

    $sql = "UPDATE events SET event_name='$name', event_date='$date', description='$desc', status='$status' WHERE id=$id";
    if($conn->query($sql)){
        $msg = "✅ Event updated successfully!";
    } else {
        $msg = "❌ Error updating: ".$conn->error;
    }
}

// Fetch all events
$result = $conn->query("SELECT * FROM events ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Management</title>
    <style>
        body{font-family: Arial; background:#f4f4f4; padding:20px;}
        .container{width:500px; margin:auto; background:#fff; padding:20px; border-radius:8px;}
        input, textarea, select{width:100%; padding:8px; margin:5px 0;}
        input[type="submit"]{background:#007bff; color:white; border:none; cursor:pointer;}
        input[type="submit"]:hover{background:#0056b3;}
        table{width:90%; margin:20px auto; border-collapse:collapse;}
        th, td{border:1px solid #ccc; padding:10px; text-align:center;}
        th{background:#007bff; color:white;}
        a{color:#007bff; text-decoration:none;}
    </style>
</head>
<body>
<div class="container">
    <h2>🎯 Event Management CRUD</h2>

    <?php if(isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

    <form method="POST">
        <h3><?php echo $editData ? "✏️ Edit Event" : "➕ Add Event"; ?></h3>
        <input type="hidden" name="id" value="<?php echo $editData['id'] ?? ''; ?>">
        Event Name:<br>
        <input type="text" name="event_name" required value="<?php echo $editData['event_name'] ?? ''; ?>"><br>
        Event Date:<br>
        <input type="date" name="event_date" required value="<?php echo $editData['event_date'] ?? ''; ?>"><br>
        Description:<br>
        <textarea name="description"><?php echo $editData['description'] ?? ''; ?></textarea><br>
        Status:<br>
        <select name="status">
            <option value="Open" <?php if(isset($editData) && $editData['status']=='Open') echo "selected"; ?>>Open</option>
            <option value="Closed" <?php if(isset($editData) && $editData['status']=='Closed') echo "selected"; ?>>Closed</option>
        </select><br>
        <?php if($editData){ ?>
            <input type="submit" name="update" value="Update Event">
        <?php } else { ?>
            <input type="submit" name="submit" value="Add Event">
        <?php } ?>
    </form>

    <h3>📋 All Events</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Date</th>
            <th>Description</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($row=$result->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['event_name']; ?></td>
            <td><?php echo $row['event_date']; ?></td>
            <td><?php echo $row['description']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <a href="?edit=<?php echo $row['id']; ?>">✏️ Edit</a> |
                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this event?');">🗑️ Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
