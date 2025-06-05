<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>개발 서버 ADMIN</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            text-align: center;
        }

        h1 {
            font-size: 1.5em;
            color: #333;
        }

        .button-container {
            margin-top: 20px;
        }

        button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #4CAF50;
            color: white;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
<h1>PHP 개발 서버 ADMIN 입니다.</h1>
<p>이동하고 싶은 매체를 클릭해주세요:</p>
<div class="button-container">
    <button onclick="window.location.href='/hana/login.php'">하나머니</button>
    <button onclick="window.location.href='/benepia/login.php'">베네피아</button>
    <button onclick="window.location.href='/happyscreen/login.php'">해피스크린</button>
    <button onclick="window.location.href='/moneyweather/login.php'">돈버는날씨</button>
    <button onclick="window.location.href='/okcashbag/login.php'">돈버는키보드(OCB)</button>
    <button onclick="window.location.href='/shinhancard/login.php'">신한카드</button>
    <button onclick="window.location.href='/valuewalk/login.php'">가치워크</button>
    <button onclick="window.location.href='/finnq/login.php'">핀크</button>
    <button onclick="window.location.href='/paybooc/login.php'">페이북</button>
    <button onclick="window.location.href='/hanapay/login.php'">하나페이</button>

</div>
</body>

</html>
