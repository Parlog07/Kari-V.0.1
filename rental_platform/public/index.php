<?php
require_once "../config/autoload.php";

// 1️⃣ Prepare DB & model
$db = new Database();
$pdo = $db->getConnection();
$rentalModel = new Rental($pdo);

// 2️⃣ Read filters from GET
$filters = [
    'city'       => $_GET['city'] ?? null,
    'min_price'  => $_GET['min_price'] ?? null,
    'max_price'  => $_GET['max_price'] ?? null,
    'start_date' => $_GET['start_date'] ?? null,
    'end_date'   => $_GET['end_date'] ?? null,
];

// 3️⃣ Pagination setup
$limit = 6;
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 4️⃣ Fetch rentals + count
$rentals = $rentalModel->search($filters, $limit, $offset);
$totalResults = $rentalModel->countSearchResults($filters);
$totalPages = (int) ceil($totalResults / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Available Rentals</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .rental { border: 1px solid #ccc; padding: 10px; margin-bottom: 10px; }
        .pagination a { margin: 0 5px; text-decoration: none; }
        .pagination strong { margin: 0 5px; }
    </style>
</head>
<body>

<h1>Available Rentals</h1>

<!-- 🔍 SEARCH FORM -->
<form method="GET">
    <input type="text" name="city" placeholder="City"
           value="<?php echo htmlspecialchars($_GET['city'] ?? ''); ?>">

    <input type="number" name="min_price" placeholder="Min price"
           value="<?php echo htmlspecialchars($_GET['min_price'] ?? ''); ?>">

    <input type="number" name="max_price" placeholder="Max price"
           value="<?php echo htmlspecialchars($_GET['max_price'] ?? ''); ?>">

    <input type="date" name="start_date"
           value="<?php echo htmlspecialchars($_GET['start_date'] ?? ''); ?>">

    <input type="date" name="end_date"
           value="<?php echo htmlspecialchars($_GET['end_date'] ?? ''); ?>">

    <button type="submit">Search</button>
</form>

<hr>

<!-- 🏠 RENTALS LIST -->
<?php if (empty($rentals)): ?>
    <p>No rentals found.</p>
<?php else: ?>
    <?php foreach ($rentals as $rental): ?>
        <div class="rental">
            <h3><?php echo htmlspecialchars($rental['title']); ?></h3>
            <p><strong>City:</strong> <?php echo htmlspecialchars($rental['city']); ?></p>
            <p><strong>Price per night:</strong> <?php echo htmlspecialchars($rental['price_per_night']); ?></p>
            <p><?php echo htmlspecialchars(substr($rental['description'], 0, 100)); ?>...</p>

            <a href="rental_detail.php?id=<?php echo $rental['id']; ?>">View details</a>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- 📄 PAGINATION -->
<?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">
                Previous
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i === $page): ?>
                <strong><?php echo $i; ?></strong>
            <?php else: ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                    <?php echo $i; ?>
                </a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">
                Next
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

</body>
</html>
