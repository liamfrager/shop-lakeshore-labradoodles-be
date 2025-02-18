<?php 
    include_once __DIR__ . '../../config/loadEnv.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        function fetchData(event) {
            event.preventDefault();

            const route = document.getElementById('route').value;
            const apiUrl = `${BE_DOMAIN}/api/${route}`;

            fetch(apiUrl)
                .then(response => response.json())
                .then(data => {
                    const resultDiv = document.getElementById('result');
                    resultDiv.innerHTML = `<pre>${JSON.stringify(data, null, 2)}</pre>`;
                })
                .catch(error => {
                    const resultDiv = document.getElementById('result');
                    resultDiv.innerHTML = `<p>Error fetching data: ${error}</p>`;
                });
        }
    </script>
</head>
<body>
    <form onsubmit="fetchData(event)">
        <span>
            <?php echo getenv("BE_DOMAIN") . "/api/" ?>
        </span>
        <input id="route" type="text" placeholder="your/route/here" required>
        <button type="submit">Submit</button>
    </form>

    <div id="result"></div>

    <script>
        const BE_DOMAIN = "<?php echo getenv('BE_DOMAIN'); ?>";
    </script>
</body>
</html>