<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP 개발 서버 ADMIN</title>
    <link rel="icon" href="/favicon.png"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 40px 20px;
            font-family: 'Inter', sans-serif;
            background: #f7f9fc;
            color: #333;
        }

        h1 {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        .grid {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            max-width: 900px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .card h2 {
            font-size: 1.1rem;
            margin: 0;
            color: #4a90e2;
        }

        .footer-button {
            display: block;
            margin: 40px auto 0;
            padding: 12px 30px;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            background-color: #333;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .footer-button:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<h1>PHP 개발 서버 ADMIN</h1>
<p style="font-size: 1.2rem; font-weight: 600; color: #4a90e2; margin-bottom: 10px;">안녕하세요!</p>
<p>이동하고 싶은 매체를 클릭하세요:</p>

<div class="grid">
    <div class="card" onclick="location.href='/hana/login.php'"><h2>하나머니</h2></div>
    <div class="card" onclick="location.href='/benepia/login.php'"><h2>베네피아</h2></div>
    <div class="card" onclick="location.href='/happyscreen/login.php'"><h2>해피스크린</h2></div>
    <div class="card" onclick="location.href='/moneyweather/login.php'"><h2>돈버는날씨</h2></div>
    <div class="card" onclick="location.href='/okcashbag/login.php'"><h2>돈버는키보드 (OCB)</h2></div>
    <div class="card" onclick="location.href='/shinhancard/login.php'"><h2>신한카드</h2></div>
    <div class="card" onclick="location.href='/valuewalk/login.php'"><h2>가치워크</h2></div>
    <div class="card" onclick="location.href='/finnq/login.php'"><h2>핀크</h2></div>
    <div class="card" onclick="location.href='/paybooc/login.php'"><h2>페이북</h2></div>
</div>
</body>
</html>
