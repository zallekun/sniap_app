<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Data Sync - Database vs API vs Frontend</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { background: white; padding: 20px; margin: 10px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 15px 0; }
        .stat { background: #e3f2fd; padding: 15px; text-align: center; border-radius: 5px; }
        .stat-number { font-size: 2em; font-weight: bold; color: #1976d2; }
        .stat-label { color: #666; margin-top: 5px; }
        .comparison { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .api-data, .frontend-data { background: #f9f9f9; padding: 15px; border-radius: 5px; }
        button { background: #1976d2; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        button:hover { background: #1565c0; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Test Data Sync: Database vs API vs Frontend</h1>
        
        <div class="card">
            <h2>📊 Database Actual Data (Server-side PHP)</h2>
            <div class="stats-grid">
                <div class="stat">
                    <div class="stat-number"><?= $stats['total_users'] ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?= $stats['active_events'] ?></div>
                    <div class="stat-label">Active Events</div>
                </div>
                <div class="stat">
                    <div class="stat-number"><?= $stats['total_registrations'] ?></div>
                    <div class="stat-label">Total Registrations</div>
                </div>
            </div>
        </div>

        <div class="comparison">
            <div class="card">
                <h3>🔌 API Response</h3>
                <div class="api-data" id="apiData">
                    <button onclick="testAPI()">Test API</button>
                    <div id="apiResult">Click button to test API</div>
                </div>
            </div>

            <div class="card">
                <h3>🌐 Frontend Data</h3>
                <div class="frontend-data">
                    <button onclick="testFrontend()">Test Frontend Load</button>
                    <div id="frontendResult">Click button to test frontend data loading</div>
                </div>
            </div>

            <div class="card">
                <h3>✅ Verification</h3>
                <div id="verification">
                    <button onclick="verifySync()">Verify Data Sync</button>
                    <div id="verifyResult">Click to check if all data sources match</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const dbStats = {
            total_users: <?= $stats['total_users'] ?>,
            active_events: <?= $stats['active_events'] ?>,
            total_registrations: <?= $stats['total_registrations'] ?>
        };

        function testAPI() {
            document.getElementById('apiResult').innerHTML = 'Loading...';
            
            fetch('/api/admin/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('apiResult').innerHTML = `
                            <div class="success">✅ API Success!</div>
                            <div>Users: ${data.data.total_users}</div>
                            <div>Events: ${data.data.active_events}</div>
                            <div>Registrations: ${data.data.total_registrations}</div>
                            <div>Revenue: Rp ${data.data.total_revenue}</div>
                        `;
                        window.apiData = data.data;
                    } else {
                        document.getElementById('apiResult').innerHTML = `<div class="error">❌ API Error: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    document.getElementById('apiResult').innerHTML = `<div class="error">❌ API Error: ${error.message}</div>`;
                });
        }

        function testFrontend() {
            document.getElementById('frontendResult').innerHTML = 'Testing frontend data loading...';
            
            // Simulate frontend data loading like in admin dashboard
            fetch('/api/admin/dashboard/stats')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Same logic as frontend
                        const frontendUsers = data.data.total_users || 0;
                        const frontendEvents = data.data.active_events || 0;
                        const frontendRegistrations = data.data.total_registrations || 0;
                        
                        document.getElementById('frontendResult').innerHTML = `
                            <div class="success">✅ Frontend Load Success!</div>
                            <div>Users: ${frontendUsers}</div>
                            <div>Events: ${frontendEvents}</div>
                            <div>Registrations: ${frontendRegistrations}</div>
                        `;
                        
                        window.frontendData = {
                            total_users: frontendUsers,
                            active_events: frontendEvents,
                            total_registrations: frontendRegistrations
                        };
                    } else {
                        document.getElementById('frontendResult').innerHTML = `<div class="error">❌ Frontend Error: ${data.message}</div>`;
                    }
                })
                .catch(error => {
                    document.getElementById('frontendResult').innerHTML = `<div class="error">❌ Frontend Error: ${error.message}</div>`;
                });
        }

        function verifySync() {
            if (!window.apiData || !window.frontendData) {
                document.getElementById('verifyResult').innerHTML = '<div class="error">❌ Please test API and Frontend first!</div>';
                return;
            }

            const dbUsers = dbStats.total_users;
            const apiUsers = window.apiData.total_users;
            const frontendUsers = window.frontendData.total_users;

            const dbEvents = dbStats.active_events;
            const apiEvents = window.apiData.active_events;
            const frontendEvents = window.frontendData.active_events;

            const usersMatch = (dbUsers == apiUsers && apiUsers == frontendUsers);
            const eventsMatch = (dbEvents == apiEvents && apiEvents == frontendEvents);

            let result = '<h4>🔍 Data Sync Verification:</h4>';
            
            result += `<div>Users: DB(${dbUsers}) = API(${apiUsers}) = Frontend(${frontendUsers}) ${usersMatch ? '✅' : '❌'}</div>`;
            result += `<div>Events: DB(${dbEvents}) = API(${apiEvents}) = Frontend(${frontendEvents}) ${eventsMatch ? '✅' : '❌'}</div>`;
            
            if (usersMatch && eventsMatch) {
                result += '<div class="success"><strong>🎉 ALL DATA SOURCES ARE SYNCED!</strong></div>';
            } else {
                result += '<div class="error"><strong>⚠️ DATA MISMATCH DETECTED!</strong></div>';
            }

            document.getElementById('verifyResult').innerHTML = result;
        }

        // Auto-test on page load
        window.onload = function() {
            setTimeout(testAPI, 500);
            setTimeout(testFrontend, 1000);
        };
    </script>
</body>
</html>