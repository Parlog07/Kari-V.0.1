<?php
require_once "../../config/autoload.php";
include "../../views/navbar.php"; 
require_once "admin_guard.php";

$db = new Database();
$pdo = $db->getConnection();

if (isset($_GET['toggle'])) {
    $stmt = $pdo->prepare("
        UPDATE users
        SET is_active = NOT is_active
        WHERE id = :id
    ");
    $stmt->execute(['id' => $_GET['toggle']]);
    header("Location: users.php");
    exit;
}

$users = $pdo->query("
    SELECT id, full_name, email, role, is_active
    FROM users
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Management | Admin Panel</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .header h1:before {
            content: "👥";
            font-size: 32px;
        }
        
        .user-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .content {
            padding: 30px;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
        }
        
        thead {
            background-color: #f8fafc;
        }
        
        th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 18px 20px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover {
            background-color: #f8fafc;
            transition: background-color 0.2s ease;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            min-width: 90px;
        }
        
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        
        .status-disabled {
            background-color: #fee2e2;
            color: #991b1b;
        }
        
        .role-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            background-color: #e0e7ff;
            color: #3730a3;
        }
        
        .action-link {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
            text-align: center;
            min-width: 100px;
        }
        
        .activate-btn {
            background-color: #10b981;
            color: white;
        }
        
        .activate-btn:hover {
            background-color: #0da271;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2);
        }
        
        .disable-btn {
            background-color: #ef4444;
            color: white;
        }
        
        .disable-btn:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(239, 68, 68, 0.2);
        }
        
        .no-data {
            text-align: center;
            padding: 50px 20px;
            color: #64748b;
            font-size: 16px;
        }
        
        .no-data-icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .footer {
            text-align: center;
            padding: 20px;
            color: #64748b;
            font-size: 14px;
            border-top: 1px solid #f1f5f9;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .container {
                border-radius: 0;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
            
            th, td {
                white-space: nowrap;
                padding: 14px 12px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>User Management</h1>
            <div class="user-count">
                <i class="fas fa-users"></i> <?= count($users) ?> Users
            </div>
        </div>
        
        <div class="content">
            <?php if (empty($users)): ?>
                <div class="no-data">
                    <div class="no-data-icon">👥</div>
                    <p>No users found in the database.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background-color: #e0e7ff; display: flex; align-items: center; justify-content: center; color: #4f46e5; font-weight: 600;">
                                        <?= strtoupper(substr($u['full_name'], 0, 1)) ?>
                                    </div>
                                    <span style="font-weight: 500;"><?= htmlspecialchars($u['full_name']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><span class="role-badge"><?= $u['role'] ?></span></td>
                            <td>
                                <span class="status-badge <?= $u['is_active'] ? 'status-active' : 'status-disabled' ?>">
                                    <?= $u['is_active'] ? 'Active' : 'Disabled' ?>
                                </span>
                            </td>
                            <td>
                                <a href="?toggle=<?= $u['id'] ?>" 
                                   class="action-link <?= $u['is_active'] ? 'disable-btn' : 'activate-btn' ?>">
                                    <?= $u['is_active'] ? '<i class="fas fa-ban"></i> Disable' : '<i class="fas fa-check-circle"></i> Activate' ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <p>User Management System &copy; <?= date('Y') ?> | Admin Panel</p>
        </div>
    </div>
    
    <script>
        // Add a simple confirmation for disable actions
        document.addEventListener('DOMContentLoaded', function() {
            const disableButtons = document.querySelectorAll('.disable-btn');
            
            disableButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const userName = this.closest('tr').querySelector('td:first-child span').textContent;
                    if (!confirm(`Are you sure you want to disable "${userName}"?`)) {
                        e.preventDefault();
                    }
                });
            });
            
            // Add subtle animation for table rows
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    row.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
</body>
</html>