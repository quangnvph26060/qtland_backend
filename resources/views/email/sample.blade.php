<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
        }
        .content {
            padding: 20px;
        }
        .content p {
            line-height: 1.6;
        }
        .footer {
            background-color: #f4f4f4;
            color: #555555;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thông Báo</h1>
        </div>
        <div class="content">
            <p>Xin chào: {{ $data['name'] }},</p>
            <p>Đây là thông tin của bạn:</p>
            <p><strong>Email:</strong> {{ $data['email'] }}</p>
            <p><strong>Password:</strong> {{ $data['password'] }}</p>
            <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Công ty bất động sản. Tất cả các quyền được bảo lưu.</p>
        </div>
    </div>
</body>
</html>
