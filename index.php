<?php
$userHome = getenv('HOME');
$output_path = $userHome . '/Desktop/';

if (!file_exists('files')) {
    mkdir('files', 0777, true);
}

if ($_POST) {
    // Globals at top of file
    $files = glob("files/*.csv");
    $headers_option = isset($_POST["headers"]) ? $_POST["headers"] : 'exclude'; // 'include', 'exclude', or 'no-header'
    $out = fopen($output_path . "/" . $_POST["outputname"] . ".csv", "w");

    $headers_written = false;

    foreach ($files as $file) {
        $in = fopen($file, "r");

        // Read the first line to handle headers
        $first_line = fgets($in); // Get the first line of the file

        if ($headers_option == 'include' && !$headers_written) {
            fwrite($out, $first_line); // Write the header from the first file
            $headers_written = true;
        } elseif ($headers_option == 'exclude') {
            // Skip the first line (header) entirely for all subsequent files
            // No need to write anything for the first line in this case
        } elseif ($headers_option == 'no-header') {
            // Include all rows, including the first row
            fwrite($out, $first_line); // Write the first row of each file
        }

        // Write the rest of the file contents (skip header for subsequent files if needed)
        while ($line = fgets($in)) {
            fwrite($out, $line);
        }
        fclose($in);
    }

    fclose($out);

    // Delete uploaded files after combining them
    foreach ($files as $file) {
        unlink($file); // Delete each uploaded file
    }
}
?>



<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/pico.classless.min.css">
    <link rel="stylesheet" href="css/dd-upload.css" />
    <link rel="stylesheet" href="styles.css" />
    <script src="js/dd-upload.js"></script>
    <script>
        // JavaScript to handle CSV preview and progress spinner
        let uploadedFiles = [];
        let spinner = document.getElementById("spinner");
        let form = document.getElementById("combine-form");

        document.addEventListener('DOMContentLoaded', function() {
            const upzone = document.getElementById('upzone');
            const previewAccordion = document.getElementById('preview-accordion');
            const previewContent = document.getElementById('preview-content');
            const fileInput = document.getElementById('csv-upload');

            upzone.addEventListener('drop', function(event) {
                event.preventDefault();
                const files = event.dataTransfer.files;
                handleFileUpload(files);
            });

            function handleFileUpload(files) {
                uploadedFiles = Array.from(files);
                if (uploadedFiles.length > 0) {
                    // Load preview for the first file
                    previewAccordion.style.display = 'block';
                    loadCSVPreview(uploadedFiles[0]);
                }
            }

            function loadCSVPreview(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const content = e.target.result;
                    previewContent.textContent = content.split('\n').slice(0, 10).join('\n'); // Preview first 10 lines
                };
                reader.readAsText(file);
            }

            form.addEventListener("submit", function(event) {
                // Show the spinner when the form is submitted
                spinner.style.display = "block";
            });
        });
    </script>
    <title>CSV File Combiner</title>
</head>

<body>
    <main class="container">
        <h1>Combine CSV Files</h1>

        <h2>Step 1.</h2>
        <p>Drag and drop some CSV files onto the dropzone below.</p>
        <div id="upzone">
            Drop CSV Files Here
        </div>
        <small>Files saved to the "files" folder in the site root.</small>
        <div id="upstat"></div>

        <!-- Preview Accordion Section -->
        <details id="preview-accordion" style="display:none;">
            <summary role="button">Preview first CSV</summary>
            <pre id="preview-content"></pre>
        </details>

        <h2>Step 2.</h2>
        <blockquote>
            <p style="margin-bottom: 10px;"><b>How should we handle headers in the CSVs?</b></p>
            <ul>
                <li>If the files have a header row and you want to include it, choose "Include".
                    <ul>
                        <li>It will only be included once</li>
                    </ul>
                </li>
                <li>If there is a header row and you want to exclude it, choose "Exclude".</li>
                <li>If there is no header row, choose "No Header".</li>
            </ul>
        </blockquote>

        <form id="combine-form" action="index.php" method="POST">
            <!-- Option field for CSV headers (horizontal radio buttons) -->
            <div class="radio-group">
                <label for="headers">Headers:</label>
                <label>
                    <input type="radio" id="include" name="headers" value="include" checked>
                    Include
                </label>
                <label>
                    <input type="radio" id="exclude" name="headers" value="exclude">
                    Exclude
                </label>
                <label>
                    <input type="radio" id="no-header" name="headers" value="no-header">
                    No Header Row
                </label>
            </div><br>

            <h2>Step 3.</h2>
            Output File Name: (no extension)<br>
            <input type="text" name="outputname" value=""><br>

            <p>&nbsp;</p>
            <input type="submit" value="COMBINE">
            <p><small>Output file will be saved to the desktop and input files will be deleted.</small></p>
        </form>


    </main>
</body>

</html>