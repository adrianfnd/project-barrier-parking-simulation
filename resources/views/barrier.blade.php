<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barrier Parking Simulator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .gate-container {
            position: relative;
            width: 200px;
            height: 150px;
            margin: 0 auto;
        }

        .gate {
            width: 100%;
            height: 20px;
            background-color: #333;
            position: absolute;
            bottom: 0;
            transition: transform 0.5s;
            transform-origin: left bottom;
        }

        .gate.open {
            transform: rotate(-90deg);
        }

        .road {
            width: 100%;
            height: 40px;
            background-color: #777;
            position: absolute;
            bottom: 0;
        }

        .status-text {
            text-align: center;
            margin-top: 10px;
            font-weight: bold;
        }

        .sirene {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ff0000;
            position: absolute;
            top: 10px;
            right: 10px;
            animation: blink 1s infinite;
            display: none;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        #ticketModal .modal-dialog {
            max-width: 300px;
        }

        .ticket {
            width: 100%;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .ticket-header {
            text-align: center;
            margin-bottom: 10px;
        }

        .ticket-content {
            margin-bottom: 10px;
        }

        .ticket-barcode {
            text-align: center;
        }

        .ticket-footer {
            text-align: center;
            font-size: 0.8em;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Barrier Parking Simulator</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h2 class="text-center">Palang Parkir Masuk</h2>
                    </div>
                    <div class="card-body">
                        <div class="gate-container">
                            <div class="road"></div>
                            <div id="gate1" class="gate"></div>
                            <div id="sirene1" class="sirene"></div>
                        </div>
                        <div id="gate1Status" class="status-text">Ready</div>
                        <button id="requestTicket1" class="btn btn-primary mt-2">Request Ticket</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-3">
                    <div class="card-header">
                        <h2 class="text-center">Palang Parkir Keluar</h2>
                    </div>
                    <div class="card-body">
                        <div class="gate-container">
                            <div class="road"></div>
                            <div id="gate2" class="gate"></div>
                            <div id="sirene2" class="sirene"></div>
                        </div>
                        <div id="gate2Status" class="status-text">Ready</div>
                        <p class="text-muted mt-2">Silakan scan tiket Anda ke Petugas Parkir untuk keluar.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">Recent API Logs</h2>
            </div>
            <div class="card-body">
                <pre id="apiLogs" class="bg-light p-3" style="max-height: 300px; overflow-y: auto;"></pre>
            </div>
        </div>
    </div>

     <!-- Ticket Modal -->
     <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <div id="ticketContent"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <script>
        function updateLogs() {
            $.get('/get-logs', function(data) {
                var logsHtml = '';
                data.logs.forEach(function(log) {
                    logsHtml += log.timestamp + ' - Gate ' + log.gate + '\n';
                    logsHtml += 'Request: ' + log.request + '\n';
                    logsHtml += 'Response: ' + log.response + '\n\n';
                });
                $('#apiLogs').html(logsHtml);

                updateGateStatus('gate1', 'gate1Status', 'sirene1', data.gate1Status, data.sirene1Status);
                updateGateStatus('gate2', 'gate2Status', 'sirene2', data.gate2Status, data.sirene2Status);
            });
        }

        function updateGateStatus(gateId, statusId, sireneId, status, sireneStatus) {
            var gateElement = $('#' + gateId);
            var statusElement = $('#' + statusId);
            var sireneElement = $('#' + sireneId);
            statusElement.text(status);
            if (status === 'Open') {
                gateElement.addClass('open');
                statusElement.removeClass('text-secondary').addClass('text-success');
            } else {
                gateElement.removeClass('open');
                statusElement.removeClass('text-success').addClass('text-secondary');
            }
            if (sireneStatus === 'On') {
                sireneElement.show();
            } else {
                sireneElement.hide();
            }
        }

        function requestTicket(gateNo) {
            const photoData = "ini adalah foto";

            $.post('/api/gate', {
                ClientType: 121,
                GateNo: gateNo,
                foto_masuk: photoData
            }, function(response) {
                console.log('Ticket requested for Gate ' + gateNo);
                updateLogs();
                if (response.Status === 1 && response.TicketUrl) {
                    window.open(response.TicketUrl, '_blank');
                }
            });
        }

        $('#requestTicket1').click(function() {
            requestTicket(1);
        });

        $('#requestTicket2').click(function() {
            requestTicket(2);
        });

        setInterval(updateLogs, 1000);
    </script>
</body>

</html>
