<?php
    require_once "config.php";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if  ($conn->connect_error) {
        die("Connection Failed: " . $conn->connect_error);
    }

    session_start(); 
    if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] === false) {
        header("Location: login.php");
        exit();
    }

    /*
     * Display the users a form that they can use to update the ratings of their 
     * music.
     */
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Update"])) {
        $music_id = intval($_POST['id']);
        $sql = "SELECT username, artist, song, rating FROM ratings WHERE id=$music_id";
        $result = mysqli_query($conn, $sql);

        $row = mysqli_fetch_assoc($result);
        $music_username = $row['username'];
        $music_artist = $row['artist'];
        $music_song = $row['song'];
        $music_rating = $row['rating'];

        ?>


<form action="" method="post">
    <!-- Fixed Username (Cannot be changed) -->
    <label>Username: <?php echo $music_username; ?></label>
    <input type="hidden" name="username" value="<?php echo $music_username; ?>">
    <label> ID </label>
    <input type='hidden' name='id' value=<?php echo $music_id ?>>
    <br />
    <!-- Editable Fields (Pre-filled with database information) -->
    <label>Artist:</label>
    <input type="text" name="artist" value="<?php echo $music_artist; ?>" required><br>

    <label>Song:</label>
    <input type="text" name="song" value="<?php echo $music_song; ?>" required><br>

    <label>Rating:</label>
    <input type="number" name="rating" value="<?php echo $music_rating; ?>" min="1" max="10" required><br>

    <input type="submit" value="Update" name="Update_Table">
</form>

<?php

        }

        else if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Update_Table"])) {
            
            $new_artist = $_POST['artist'];
            $new_song = $_POST['song'];
            $new_rating = $_POST['rating'];
            $id = intval($_POST['id']);

            $sql = "UPDATE ratings SET artist = ?, song = ?, rating = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssi", $new_artist, $new_song, $new_rating, $id);
            try {
                if (mysqli_stmt_execute($stmt) === TRUE) {
                    header("Location: main.php");
                    exit();
                } else {
                    echo "Error: " . $sql . "<br>" . mysqli_error($conn);
                }
            }
            catch (Exception $e) {
                echo $e;
            }
        } 
?>