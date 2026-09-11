<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Queue System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Smart Queue Management</h1>

        
        <div class="card operator-panel">
            <h2>Operator Paneli</h2>
            <button id="nextBtn" class="btn btn-next">Növbəti Müştərini Çağır (/next)</button>
            <div id="currentServing" class="serving-box">
                Hal-hazırda xidmət göstərilir: <span>Hələ heç kim</span>
            </div>
        </div>

        
        <div class="card">
            <h2>Yeni Müştəri Əlavə Et</h2>
            <form id="addCustomerForm">
                <input type="text" id="customerName" placeholder="Müştərinin adı" required>
                <button type="submit" class="btn">Növbəyə Daxil Ol</button>
            </form>
        </div>

      
        <div class="card">
            <h2>Sıranı Yoxla</h2>
            <div class="search-box">
                <input type="number" id="checkId" placeholder="Müştəri ID-si">
                <button id="checkBtn" class="btn btn-secondary">Yoxla</button>
            </div>
            <p id="checkResult"></p>
        </div>

        
        <div class="card">
            <h2>Gözləyən Növbə</h2>
            <ul id="queueList"></ul>
        </div>
    </div>

    <script src="public/js/app.js"></script>
</body>
</html>