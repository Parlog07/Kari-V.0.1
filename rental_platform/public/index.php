<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rentals</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        header {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .search-form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        input:focus {
            outline: none;
            border-color: #3498db;
        }

        .search-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-btn:hover {
            background-color: #2980b9;
        }

        .rentals-count {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .rentals-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .rental-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .rental-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .rental-image {
            height: 200px;
            background-color: #ecf0f1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #95a5a6;
        }

        .rental-content {
            padding: 20px;
        }

        .rental-header {
            margin-bottom: 15px;
        }

        .rental-title {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .rental-location {
            color: #7f8c8d;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .rental-price {
            color: #27ae60;
            font-size: 20px;
            font-weight: 600;
            margin: 10px 0;
        }

        .rental-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .view-details-btn {
            display: inline-block;
            background-color: #f8f9fa;
            color: #3498db;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-size: 14px;
            border: 1px solid #ddd;
            transition: all 0.2s;
        }

        .view-details-btn:hover {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }

        .no-rentals {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .no-rentals h3 {
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .no-rentals p {
            color: #95a5a6;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 40px;
        }

        .pagination-btn {
            padding: 10px 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .pagination-btn:hover:not(:disabled) {
            background-color: #f8f9fa;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-numbers {
            display: flex;
            gap: 5px;
        }

        .page-number {
            padding: 10px 15px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .page-number:hover {
            background-color: #f8f9fa;
        }

        .page-number.active {
            background-color: #3498db;
            color: white;
            border-color: #3498db;
        }

        @media (max-width: 768px) {
            .rentals-list {
                grid-template-columns: 1fr;
            }
            
            .form-group {
                min-width: 100%;
            }
            
            .pagination {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Available Rentals</h1>
        <p>Find your perfect stay from our curated selection of rental properties</p>
    </header>

    <main>
        <form class="search-form" method="GET" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="Enter city name">
                </div>
                <div class="form-group">
                    <label for="min_price">Min Price</label>
                    <input type="number" id="min_price" name="min_price" placeholder="0" min="0">
                </div>
                <div class="form-group">
                    <label for="max_price">Max Price</label>
                    <input type="number" id="max_price" name="max_price" placeholder="1000" min="0">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date">
                </div>
                <div class="form-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date">
                </div>
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" class="search-btn">Search</button>
                </div>
            </div>
        </form>

        <?php
        // Assume $rentals array is provided from backend
        // This is just for template structure - actual data would come from backend
        $rentals = []; // This would be populated by backend logic
        $totalRentals = count($rentals);
        ?>

        <div class="rentals-count">
            <?php if ($totalRentals > 0): ?>
                Showing <?php echo $totalRentals; ?> rental<?php echo $totalRentals !== 1 ? 's' : ''; ?>
            <?php endif; ?>
        </div>

        <?php if ($totalRentals > 0): ?>
            <div class="rentals-list">
                <?php foreach ($rentals as $rental): ?>
                    <div class="rental-card">
                        <div class="rental-image">
                            <!-- Image would be displayed here if available -->
                            <span>Rental Image</span>
                        </div>
                        <div class="rental-content">
                            <div class="rental-header">
                                <h3 class="rental-title"><?php echo htmlspecialchars($rental['title'] ?? 'Untitled Rental'); ?></h3>
                                <div class="rental-location">
                                    <span>📍</span>
                                    <span><?php echo htmlspecialchars($rental['city'] ?? 'Unknown City'); ?></span>
                                </div>
                            </div>
                            <div class="rental-price">
                                $<?php echo number_format($rental['price_per_night'] ?? 0, 2); ?> <small>/ night</small>
                            </div>
                            <p class="rental-description">
                                <?php echo htmlspecialchars($rental['short_description'] ?? 'No description available.'); ?>
                            </p>
                            <a href="rental_detail.php?id=<?php echo urlencode($rental['id'] ?? ''); ?>" class="view-details-btn">
                                View Details
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-rentals">
                <h3>No rentals found</h3>
                <p>Try adjusting your search criteria or check back later for new listings.</p>
            </div>
        <?php endif; ?>

        <!-- Pagination UI (frontend only, no logic) -->
        <?php if ($totalRentals > 0): ?>
            <div class="pagination">
                <button class="pagination-btn" disabled>Previous</button>
                <div class="page-numbers">
                    <span class="page-number active">1</span>
                    <span class="page-number">2</span>
                    <span class="page-number">3</span>
                    <span class="page-number">4</span>
                    <span class="page-number">5</span>
                </div>
                <button class="pagination-btn">Next</button>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>