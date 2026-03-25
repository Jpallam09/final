<?php
$c = mysqli_connect("localhost", "root", "", "students_db");
if (isset($_POST['add']) || isset($_POST['update'])) {
    [$n, $e, $co, $s, $g, $b, $id] = [$_POST['name'], $_POST['email'], $_POST['course'], $_POST['section'], $_POST['gender'], $_POST['birthday'], $_POST['id'] ?? ''];
    mysqli_query($c, isset($_POST['add'])
        ? "INSERT INTO students(name,email,course,section,gender,birthday) VALUES('$n','$e','$co','$s','$g','$b')"
        : "UPDATE students SET name='$n',email='$e',course='$co',section='$s',gender='$g',birthday='$b' WHERE id=$id");
    header("Location: ?");  
}
    if (isset($_GET['delete'])) { mysqli_query($c, "DELETE FROM students WHERE id=" . $_GET['delete']); header("Location: ?"); }

    $edit = isset($_GET['edit']);
    $row = $edit ? mysqli_fetch_assoc(mysqli_query($c, "SELECT * FROM students WHERE id=" . $_GET['edit'])) : [];
    
    $search = mysqli_real_escape_string($c, $_GET['search'] ?? '');
    $result = mysqli_query($c, $search ? "SELECT * FROM students WHERE name LIKE '%$search%'" : "SELECT * FROM students");
?>
<h2>Enrolment Form</h2>
<form method="POST">
<input type="hidden" name="id" value="<?= $row['id'] ?? '' ?>">
<?php foreach (['name','email','course','section'] as $f) { ?>
<?= ucfirst($f) ?>: <input type="<?= $f=='email'?'email':'text' ?>" name="<?= $f ?>" value="<?= $row[$f] ?? '' ?>">
<?php } ?>
    Gender:
<select name="gender">
<?php foreach (['Male','Female'] as $g) { ?>
<option <?= ($row['gender'] ?? '') === $g ? 'selected' : '' ?>><?= $g ?></option>
<?php } ?>
</select>
Birthday: <input type="date" name="birthday" value="<?= $row['birthday'] ?? '' ?>">
<button name="<?= $edit?'update':'add' ?>"><?= $edit?'Update':'Add' ?></button>
</form>
<form method="GET">
<input type="text" name="search" value="<?= $search ?>">
<button>Search</button>
<a href="?">Reset</a>
</form>
<table border="1">
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Course</th>
<th>Section</th>
<th>Gender</th>
<th>Birthday</th>
<th>Enrolled At</th>
<th>Action</th>
</tr>
<?php while ($d = mysqli_fetch_assoc($result)) { ?>
<tr>
<td><?= $d['id'] ?></td>
<td><?= $d['name'] ?></td>
<td><?= $d['email'] ?></td>
<td><?= $d['course'] ?></td>
<td><?= $d['section'] ?></td>
<td><?= $d['gender'] ?></td>
<td><?= $d['birthday'] ?></td>
<td><?= $d['created_at'] ?></td>
<td>
<a href="?edit=<?= $d['id'] ?>">Edit</a>
<a href="?delete=<?= $d['id'] ?>">Delete</a>
</td>
</tr>
<?php } ?>
</table>

<!--
CREATE DATABASE students_db;
USE students_db;
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50),
    email VARCHAR(100),
    course VARCHAR(25),
    section VARCHAR(25),
    gender VARCHAR(25),
    birthday DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-->
