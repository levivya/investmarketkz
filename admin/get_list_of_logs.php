<?php
// Note that !== did not exist until 4.0.0-RC2

if ($handle = opendir('../logs/')) {

    //echo "Directory handle: $handle\n";
    //echo "Files:\n";

    /* This is the correct way to loop over the directory. */
    while (false !== ($file = readdir($handle))) {
        echo '<a href="../logs/'.$file.'" >'.$file.'</a><br>';
    }

    /* This is the WRONG way to loop over the directory. */
   /*
    while ($file = readdir($handle)) {
        echo "$file\n";
    }
   */
    closedir($handle);

    echo '<br><a href="index.php" >Страница Администратора</a>';

}
?>