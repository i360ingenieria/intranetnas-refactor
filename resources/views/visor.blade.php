<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visor Excel - {{ $filename }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); font-family: 'Segoe UI', sans-serif; padding: 20px; }
        .excel-container { max-width: 1400px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.15); }
        .header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
        .header h2 { font-size: 18px; margin: 0; color: white; display: flex; align-items: center; gap: 12px; }
        .btn-back { background: rgba(255,255,255,0.2); border: none; padding: 8px 16px; border-radius: 8px; color: white; text-decoration: none; cursor: pointer; }
        .tabs { background: #f8f9fa; padding: 10px 25px 0 25px; border-bottom: 1px solid #e0e0e0; display: flex; gap: 6px; flex-wrap: wrap; }
        .tab { background: #f1f3f4; border: 1px solid #e0e0e0; border-bottom: none; padding: 10px 24px; font-size: 13px; cursor: pointer; border-radius: 10px 10px 0 0; color: #5f6368; }
        .tab.active { background: white; border-bottom: 3px solid #1e7145; color: #1e7145; font-weight: 600; }
        .toolbar { background: white; padding: 12px 25px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; }
        .search-box { display: flex; align-items: center; border: 1px solid #ddd; border-radius: 8px; padding: 6px 12px; }
        .search-box input { border: none; outline: none; padding: 6px 10px; width: 280px; }
        .stats { display: flex; gap: 25px; font-size: 13px; }
        .table-wrapper { overflow: auto; max-height: 65vh; background: white; }
        .excel-table { border-collapse: collapse; font-size: 13px; width: 100%; }
        .excel-table th { background: #2c3e50; color: white; padding: 12px; text-align: left; position: sticky; top: 0; }
        .excel-table td { padding: 10px; border: 1px solid #e8e8e8; }
        .excel-table tr:nth-child(even) { background: #fafbfc; }
        .excel-table tr:hover { background: #f0f0f0; }
        .footer { background: #f8f9fa; padding: 10px 25px; border-top: 1px solid #e0e0e0; display: flex; justify-content: space-between; font-size: 12px; color: #666; }
        .title-cell { background: #e8f0fe !important; font-weight: 700 !important; color: #1e7145 !important; border-left: 3px solid #1e7145 !important; }
    </style>
</head>
<body>
    <div class="excel-container">
        <div class="header">
            <h2><i class="fas fa-file-excel"></i> {{ $filename }}</h2>
            <a href="javascript:void(0)" onclick="window.close()" class="btn-back"><i class="fas fa-arrow-left"></i> Cerrar</a>
        </div>
        
        <div id="tabsContainer" class="tabs"></div>
        
        <div class="toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Buscar...">
            </div>
            <div class="stats">
                <div><i class="fas fa-table"></i> <span id="rowCount">0</span> filas</div>
                <div><i class="fas fa-columns"></i> <span id="colCount">0</span> columnas</div>
            </div>
        </div>
        
        <div id="tableWrapper" class="table-wrapper">
            <div style="text-align:center; padding:80px;">
                <i class="fas fa-spinner fa-pulse" style="font-size:48px; color:#1e7145;"></i>
                <p>Cargando archivo...</p>
            </div>
        </div>
        
        <div class="footer">
            <span><i class="fas fa-info-circle"></i> <span id="resultInfo">Listo</span></span>
        </div>
    </div>

    <script>
        var sheets = @json($sheets);
        var currentSheet = 0;
        var titleColors = ['#2c3e50', '#16a085', '#e67e22', '#9b59b6', '#3498db', '#e74c3c', '#1abc9c', '#f39c12'];
        
        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            return String(text).replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }
        
        function renderSheet(index) {
            var sheet = sheets[index];
            if (!sheet) return;
            
            var headers = sheet.headers || [];
            var rows = sheet.rows || [];
            
            document.getElementById('rowCount').innerHTML = rows.length.toLocaleString();
            document.getElementById('colCount').innerHTML = headers.length;
            document.getElementById('resultInfo').innerHTML = 'Mostrando ' + rows.length.toLocaleString() + ' filas';
            
            var html = '<table class="excel-table">';
            html += '<thead>';
            html += '<tr>';
            html += '<th>#</th>';
            for (var i = 0; i < headers.length; i++) {
                var bgColor = titleColors[i % titleColors.length];
                var headerText = headers[i] || 'Columna ' + (i + 1);
                html += '<th style="background:' + bgColor + '; color:white;">' + escapeHtml(headerText) + '</th>';
            }
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            
            for (var i = 0; i < rows.length; i++) {
                html += '<tr>';
                html += '<td style="font-weight:600;">' + (i + 1) + '</td>';
                for (var j = 0; j < headers.length; j++) {
                    var cell = (rows[i][j] !== undefined && rows[i][j] !== null) ? rows[i][j] : '';
                    html += '<td>' + escapeHtml(cell) + '</td>';
                }
                html += '</tr>';
            }
            html += '</tbody>';
            html += '</table>';
            
            document.getElementById('tableWrapper').innerHTML = html;
            
            var tabs = document.querySelectorAll('.tab');
            for (var i = 0; i < tabs.length; i++) {
                if (i === index) {
                    tabs[i].classList.add('active');
                } else {
                    tabs[i].classList.remove('active');
                }
            }
        }
        
        var tabsContainer = document.getElementById('tabsContainer');
        for (var i = 0; i < sheets.length; i++) {
            var sheet = sheets[i];
            var btn = document.createElement('button');
            btn.className = 'tab';
            if (i === 0) btn.classList.add('active');
            btn.innerHTML = '<i class="fas fa-table"></i> ' + escapeHtml(sheet.name) + ' (' + sheet.rows.length + ')';
            btn.onclick = (function(idx) {
                return function() {
                    renderSheet(idx);
                };
            })(i);
            tabsContainer.appendChild(btn);
        }
        
        if (sheets.length > 0) {
            renderSheet(0);
        }
        
        var searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                var filter = this.value.toUpperCase();
                var rows = document.querySelectorAll('#tableWrapper tbody tr');
                var visible = 0;
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i];
                    var text = row.innerText.toUpperCase();
                    var show = text.indexOf(filter) > -1;
                    row.style.display = show ? '' : 'none';
                    if (show) visible++;
                }
                var resultSpan = document.getElementById('resultInfo');
                if (resultSpan) {
                    if (filter) {
                        resultSpan.innerHTML = 'Buscando: ' + visible + ' de ' + sheets[currentSheet].rows.length + ' filas';
                    } else {
                        resultSpan.innerHTML = 'Mostrando ' + sheets[currentSheet].rows.length + ' filas';
                    }
                }
            });
        }
    </script>
</body>
</html>