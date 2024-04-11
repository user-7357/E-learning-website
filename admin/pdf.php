<?php

include '../components/connect.php';

if (isset($_COOKIE['tutor_id'])) {
    $tutor_id = $_COOKIE['tutor_id'];
} else {
    $tutor_id = '';
    header('location:login.php');
}

if (isset($_POST['delete'])) {
    $delete_id = $_POST['pdf_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_pdf = $conn->prepare("SELECT * FROM `pdf` WHERE id = ? AND tutor_id = ? LIMIT 1");
    $verify_pdf->execute([$delete_id, $tutor_id]);

    if ($verify_pdf->rowCount() > 0) {



        $delete_pdf_thumb = $conn->prepare("SELECT * FROM `pdf` WHERE id = ? LIMIT 1");
        $delete_pdf_thumb->execute([$delete_id]);
        $fetch_thumb = $delete_pdf_thumb->fetch(PDO::FETCH_ASSOC);
        unlink('../uploaded_files/' . $fetch_thumb['thumb']);
        $delete_bookmark = $conn->prepare("DELETE FROM `bookmark` WHERE pdf_id = ?");
        $delete_bookmark->execute([$delete_id]);
        $delete_pdf = $conn->prepare("DELETE FROM `pdf` WHERE id = ?");
        $delete_pdf->execute([$delete_id]);
        $message[] = 'pdf deleted!';
    } else {
        $message[] = 'pdf already deleted!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>pdfs</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">

</head>

<body>

    <?php include '../components/admin_header.php'; ?>

    <section class="pdfs">

        <h1 class="heading">added pdf</h1>

        <div class="box-container">

            <div class="box" style="text-align: center;">
                <h3 class="title" style="margin-bottom: .5rem;">add new pdf</h3>
                <a href="add_pdf.php" class="btn">add pdf</a>
            </div>

            <?php
            $select_pdf = $conn->prepare("SELECT * FROM `branch` WHERE tutor_id = ? ORDER BY date DESC");
            $select_pdf->execute([$tutor_id]);
            if ($select_pdf->rowCount() > 0) {
                while ($fetch_pdf = $select_pdf->fetch(PDO::FETCH_ASSOC)) {
                    $pdf_id = $fetch_pdf['id'];
                    $count_videos = $conn->prepare("SELECT * FROM `content` WHERE pdf_id = ?");
                    $count_videos->execute([$pdf_id]);
                    $total_videos = $count_videos->rowCount();
                    ?>
                    <div class="box">
                        <div class="flex">
                            <div><i class="fas fa-circle-dot"
                                    style="<?php if ($fetch_pdf['status'] == 'active') {
                                        echo 'color:limegreen';
                                    } else {
                                        echo 'color:red';
                                    } ?>"></i><span
                                    style="<?php if ($fetch_pdf['status'] == 'active') {
                                        echo 'color:limegreen';
                                    } else {
                                        echo 'color:red';
                                    } ?>">
                                    <?= $fetch_pdf['status']; ?>
                                </span></div>
                            <div><i class="fas fa-calendar"></i><span>
                                    <?= $fetch_pdf['date']; ?>
                                </span></div>
                        </div>
                        <div class="thumb">
                            <span>
                                <?= $total_videos; ?>
                            </span>
                            <img src="../uploaded_files/<?= $fetch_pdf['thumb']; ?>" alt="">
                        </div>
                        <h3 class="title">
                            <?= $fetch_pdf['title']; ?>
                        </h3>
                        <p class="description">
                            <?= $fetch_pdf['description']; ?>
                        </p>
                        <form action="" method="post" class="flex-btn">
                            <input type="hidden" name="pdf_id" value="<?= $pdf_id; ?>">
                            <a href="update_pdf.php?get_id=<?= $pdf_id; ?>" class="option-btn">update</a>
                            <input type="submit" value="delete" class="delete-btn"
                                onclick="return confirm('delete this pdf?');" name="delete">
                        </form>
                        <a href="view_pdf.php?get_id=<?= $pdf_id; ?>" class="btn">view pdf</a>
                    </div>
                    <?php
                }
            } else {
                echo '<p class="empty">no pdf added yet!</p>';
            }
            ?>

        </div>

    </section>













    <?php include '../components/footer.php'; ?>

    <script src="../js/admin_script.js"></script>

    <script>
        document.querySelectorAll('.playlists .box-container .box .description').forEach(content => {
            if (content.innerHTML.length > 100) content.innerHTML = content.innerHTML.slice(0, 100);
        });
    </script>

</body>

</html>