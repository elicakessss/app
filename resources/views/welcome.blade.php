<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paulinian Student Government E-Portfolio and Ranking System</title>
    <style>
/* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", sans-serif;
        }

        body {
            background: #ffffff;
            color: #1d4826;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Header */
        header {
            width: 100%;
            padding: 2rem 8%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        header .logo {
            font-weight: 700;
            letter-spacing: 1px;
            font-size: 1.2rem;
        }

        header img {
            width: 100%;
            max-width: 300px;
        }


        /* Hero Section */
        .hero {
            text-align: left;
            max-width: 1000px;
            padding: 6rem 8%;
            z-index: 2;
            position: relative;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            color: #1d4826;
            line-height: 1.1;
            margin-bottom: 1rem;
        }

        .hero h1 span {
            font-weight: 400;
            color: #555;
        }

        .hero p {
            color: #666;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            background: #1d4826;
            color: #fff;
            padding: 0.9rem 2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn:hover {
            color: #fac808;
        }

    </style>
</head>
<body>
    <header>
        <div class="logo"><img src="images/logo.png"></div>
    </header>
    

    <main>
        <section class="hero">
            <h1>Paulinian Student Government</h1>
            <h2>E-Portfolio and Ranking System</h2>
            <p>Your official digital hub for tracking all Paulinian Student Government achievements, contributions, and performance rankings.</p>
            <a href="/admin/login" class="btn">Admin Login</a>
            <a href="/student/login" class="btn">Student Login</a>
            <a style="color:white;">Made by Elijah</a>
        </section>
    </main>


</body>
</html>
