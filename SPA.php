<?php
$id = $author = $title = $type = $isbn = $date = $lan = $des = "";
$author2 = $title2 = $type2 = $isbn2 = $date2 = $lan2 = $des2 = "";
$isbnErr = $dateErr = $genErr = "";
$isbn2Err = $date2Err = $gen2Err = "";
$con = mysqli_connect('localhost','root','','cs230');

if (isset($_GET['edit'])){
    $id = $_GET['edit'];
    $record = mysqli_query($con,"SELECT * FROM ebook_metadata WHERE id=$id");

    $n = mysqli_fetch_array($record);
    $author2 = $n['creator'];
    $title2 = $n['title'];
    $type2 = $n['type'];
    $isbn2 = $n['identifier'];
    $date2 = $n['date'];
    $lan2 = $n['language'];
    $des2 = $n['description'];
}
else if (isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($con,"DELETE FROM ebook_metadata WHERE id=$id");
    header("Location: index.php");
}

else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save'])) {
        $author = test_input($_POST["author"]);
        $title = test_input($_POST["title"]);
        $type = test_input($_POST["type"]);
        $isbn = $_POST["isbn"];
        if (!preg_match("/^[0-9]{3}([-| ])*[0-9]{1}([-| ])*[0-9]{4}([-| ])*[0-9]{4}([-| ])*[0-9]{1}$/", $isbn)) $isbnErr = "* Invalid ISBN";
        else $isbnErr = "";
        $date = test_input($_POST["date"]);
        if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date)) $dateErr = "* Invalid date";
        else $dateErr = "";
        $lan = test_input($_POST["lan"]);
        $des = test_input($_POST["des"]);

        if ($isbnErr != "" || $dateErr != "") $genErr = "NOTICE: Insertion Failed - See error messages for more details.";

        //Insert into table
        if ($isbnErr == "" && $dateErr == "") {
            $query = "INSERT INTO ebook_metadata (creator,title,type,identifier,date,language,description) VALUES ('$author','$title','$type','$isbn','$date','$lan','$des')";
            mysqli_query($con, $query);
            $success = "Data successfully inserted.";
            header("Location: index.php");
        }
    }
    else if (isset($_POST['update'])){
        $id = test_input($_POST["id"]);
        $author2 = test_input($_POST["author2"]);
        $title2 = test_input($_POST["title2"]);
        $type2 = test_input($_POST["type2"]);
        $isbn2 = $_POST["isbn2"];
        if (!preg_match("/^[0-9]{3}([-| ])*[0-9]{1}([-| ])*[0-9]{4}([-| ])*[0-9]{4}([-| ])*[0-9]{1}$/", $isbn2)) $isbn2Err = "* Invalid ISBN";
        else $isbn2Err = "";
        $date2 = test_input($_POST["date2"]);
        if (!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date2)) $date2Err = "* Invalid date";
        else $date2Err = "";
        $lan2 = test_input($_POST["lan2"]);
        $des2 = test_input($_POST["des2"]);

        if ($date2Err != "" || $isbn2Err != "") $gen2Err = "NOTICE: Update Failed - See error messages for more details.";

        //Update table
        if ($isbn2Err == "" && $date2Err == "") {
            $query2 = "UPDATE ebook_metadata SET creator = '$author2', title = '$title2', type = '$type2', identifier = '$isbn2', date = '$date2', language = '$lan2', description = '$des2' WHERE id = $id";
            mysqli_query($con, $query2);
            $success2 = "Data successfully updated.";
            header("Location: index.php");
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta name="google" content="notranslate">
    <title>Single Page Application</title>
    <style>
        .container{width:700px;}
        .container input,select{width: 200px;margin-left:20%;}
        span{color:red;}
        h1,.container {font-family:helvetica;}
        table {border-collapse: collapse;}
        table, th, td {border: 1px solid black;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Row Data</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            creator:<input type="text" name="author" value="<?php echo $author;?>">
            <br><br>
            title:<input type="text" name="title" value="<?php echo $title;?>">
            <br><br>
            type:<select name="type">
                <option style="display:none">Select</option>
                <option>Biography</option>
                <option>History</option>
                <option>Science Fiction</option>
                <option>Technical</option>
            </select>
            <br><br>
            identifier:<input type="text" name="isbn" value="<?php echo $isbn;?>" placeholder="<Enter a 13 digit ISBN code>">
            <span class="error"> <?php echo $isbnErr;?></span>
            <br><br>
            date:<input type="text" name="date" value="<?php echo $date;?>" placeholder="<YYYY-MM-DD>">
            <span class="error"> <?php echo $dateErr;?></span>
            <br><br>
            language:<select name="lan">
                <option style="display:none">Select</option>
                <option>en</option>
                <option>fr</option>
                <option>de</option>
                <option>es</option>
            </select>
            <br><br>
            description:<input type="text" name="des" value="<?php echo $des;?>">
            <br><br>
            <button class="btn" type="submit" name="save">Insert</button>
            <span class="error"> <?php echo $genErr;?></span>
        </form>
    </div>

    <hr>

    <h1>Retrieve Table Data</h1>
    <br>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Creator</th>
                <th>Title</th>
                <th>Type</th>
                <th>Identifier</th>
                <th>Date</th>
                <th>Language</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $results = mysqli_query($con,"SELECT * FROM ebook_metadata");
        while ($row = mysqli_fetch_array($results)) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['creator']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td><?php echo $row['identifier']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['language']; ?></td>
                    <td><?php echo $row['description']; ?></td>
                    <td><a href="index.php?edit=<?php echo $row['id']; ?>"  >Edit</a></td>
                    <td><a href="index.php?delete=<?php echo $row['id']; ?>"  >Delete</a></td>
                </tr>
        <?php }?>
        </tbody>
    </table>

    <hr>

    <div class = "container">
        <h1>Update Table Data</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            creator:<input type="text" name="author2" value="<?php echo $author2;?>">
            <br><br>
            title:<input type="text" name="title2" value="<?php echo $title2;?>">
            <br><br>
            type:<select name="type2">
                <option style="display:none">Select</option>
                <option>Biography</option>
                <option>History</option>
                <option>Science Fiction</option>
                <option>Technical</option>
            </select>
            <br><br>
            identifier:<input type="text" name="isbn2" value="<?php echo $isbn2;?>" placeholder="<Enter a 13 digit ISBN code>">
            <span class="error"> <?php echo $isbn2Err;?></span>
            <br><br>
            date:<input type="text" name="date2" value="<?php echo $date2;?>" placeholder="<YYYY-MM-DD>">
            <span class="error"> <?php echo $date2Err;?></span>
            <br><br>
            language:<select name="lan2">
                <option style="display:none">Select</option>
                <option>en</option>
                <option>fr</option>
                <option>de</option>
                <option>es</option>
            </select>
            <br><br>
            description:<input type="text" name="des2" value="<?php echo $des2;?>">
            <br><br>
            <button class="btn" type="submit" name="update">Update</button>
            <span class="error"> <?php echo $gen2Err;?></span>
        </form>
    </div>

</body>
</html>