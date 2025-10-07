<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard UI</title>
    <link rel="shortcut icon" type="imagex/png" href="img/logo.png">
    <link rel="stylesheet" href="css/global.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #dcd4e5;
            margin: 0;
            padding: 0;
        }

        .dashboard {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            padding: 20px;
        }

        .sidebar {
            width: 20%;
            background-color: #29243d;
            border-radius: 12px;
            padding: 20px;
            color: var(--septenary-color);
        }

        .main-content {
            width: 75%;
            background-color: #312c48;
            border-radius: 12px;
            padding: 20px;
            color: var(--septenary-color);
        }

        .collections {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card {
            background-color: #4d4769;
            border-radius: 12px;
            padding: 15px;
            flex-basis: 20%;
            color: var(--septenary-color);
        }

    </style>
</head>
<body>
    <script src="js/lightmode.js"></script>
</body>
</html>
