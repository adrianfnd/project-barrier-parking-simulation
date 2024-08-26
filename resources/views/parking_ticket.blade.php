<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Parking Ticket</title>
    <style>
        @font-face {
            font-family: 'Code39AzaleaFont';
            src: url('font-awesome/font/Code39Azalea.woff') format('woff'),
                 url('font-awesome/font/Code39Azalea.ttf') format('truetype');
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        #ticket {
            width: 80mm;
            padding: 5mm;
            margin: 10mm auto;
            background: #FFF;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 16pt;
            text-align: center;
            margin: 5mm 0;
        }
        .info {
            font-size: 10pt;
            margin-bottom: 3mm;
        }
        .barcode {
            text-align: center;
            margin: 5mm 0;
        }
        .barcode svg {
            max-width: 100%;
        }
        .ticket-id {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 5mm;
        }
        .footer {
            font-size: 8pt;
            text-align: center;
            margin-top: 5mm;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <div id="ticket">
        <h1>PARKING TICKET</h1>
        <div class="info">
            <p>Date: {{ $date }}</p>
            <p>Time In: {{ $time }}</p>
        </div>
        <div class="barcode">
            <svg id="barcode"></svg>
        </div>
        <div class="ticket-id">
            ID: {{ $ticketId }}
        </div>
        <div class="footer">
            SIMPAN TIKET INI<br>
            KEHILANGAN TIKET DIKENAKAN DENDA<br>
            TERIMA KASIH
        </div>
    </div>

    <script>
        JsBarcode("#barcode", "{{ $ticketId }}", {
            width: 2,
            height: 50,
            displayValue: false
        });
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>