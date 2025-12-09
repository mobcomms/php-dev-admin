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
            display: block; /* a 태그를 블록 요소로 */
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.05);
            padding: 20px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none; /* 밑줄 제거 */
            color: inherit; /* 글자색 상속 */
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
    </style>
</head>
<body>

<h1>PHP 개발 서버 ADMIN</h1>
<p>이동하고 싶은 매체를 클릭하세요:</p>

<div class="grid">
    <a href="/okcashbag/login.php" target="_blank" class="card"><h2>돈버는키보드 (OCB)</h2></a>
    <a href="/hana/login.php" target="_blank" class="card"><h2>하나머니</h2></a>
    <a href="/moneyweather/login.php" target="_blank" class="card"><h2>돈버는날씨</h2></a>
    <a href="/happyscreen/login.php" target="_blank" class="card"><h2>해피스크린</h2></a>
    <a href="/valuewalk/login.php" target="_blank" class="card"><h2>가치워크</h2></a>
</div>

<p>
    PHP 어드민 종료 요청 (12/9)<br>
    → 페이북, 하나페이, 신한카드, 핀크, 베네피아
</p>

</body>
</html>
