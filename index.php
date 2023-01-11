<?php require("config.php"); ?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/pico.classless.min.css">
  <link rel="stylesheet" href="css/dd-upload.css" />
  <script src="js/dd-upload.js"></script>
  <title>CSV File Combiner</title>
</head>

<body>
  <main class="container">
    <h1>Combine CSV Files</h1>
    <p>
      Drag and drop some CSV files onto step 1. This will upload them to the /files directory in this site. Once that finishes, give the output file a name in step 2. When you hit the go button, you'll get an output CSV file on the desktop and all the input files will be moved to a date/time folder in /done.
    </p>
    <h2>Step 1.</h2>
    <div id="upzone">
      Drop CSV Files Here
    </div>
    <div id="upstat"></div>
  </main>

  <main class="container">
    <h2>Step 2.</h2>
    <form action="index.php" method="POST">
      Output File Name: (no extension)<br>
      <input type="text" name="outputname" value="">
      <input type="submit" value="Submit">
    </form>

    <?php if ($_POST) {

      $files = glob("files/*.csv");
      $out = fopen(
        $output_path . "/" . $_POST["outputname"] . ".csv",
        "w"
      );
      foreach ($files as $file) {
        $in = fopen($file, "r");
        while ($line = fread($in, filesize($file))) {
          fwrite($out, $line);
        }
        fclose($in);
        unlink($file);
      }
      fclose($out);
    ?>
    <?php
    } ?>
  </main>
</body>

</html>