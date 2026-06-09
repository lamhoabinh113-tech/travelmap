<?php
/**
 * Diagnostic script to print lines of the rendered dashboard HTML.
 * Bypasses login to render the dashboard for user 'aa' and prints lines 2085-2125.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Find user 'aa'
    $stmt = $db->prepare("SELECT * FROM users WHERE username = 'aa' LIMIT 1");
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // Fallback to first user
        $stmt = $db->prepare("SELECT * FROM users LIMIT 1");
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$user) {
        die("No users found in database.");
    }

    // Set session mock
    session_start();
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['full_name'] = $user['full_name'];
    $_SESSION['username']  = $user['username'];
    $_SESSION['avatar']    = !empty($user['avatar']) ? ($user['avatar']) : '';

    // Simulate LocationController->dashboard() variables
    $user_id = $user['id'];
    require_once '../app/models/LocationModel.php';
    $locationModel = new LocationModel($db);
    $locations = $locationModel->getAllByUserId($user_id);

    // Friends list
    $query = "SELECT u.id, u.full_name, u.username FROM users u 
              JOIN friendships f ON (u.id = f.friend_id OR u.id = f.user_id) 
              WHERE (f.user_id = :uid OR f.friend_id = :uid) AND u.id != :uid AND f.status = 'accepted'";
    $stmt = $db->prepare($query);
    $stmt->execute([':uid' => $user_id]);
    $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Friend locations
    $friend_locations = $locationModel->getFriendLocations($user_id);

    // Trips
    require_once '../app/models/TripModel.php';
    $tripModel = new TripModel($db);
    $trips = $tripModel->getByUser($user_id);

    // XP and Badge
    $q_user = "SELECT xp FROM users WHERE id = :uid";
    $s_user = $db->prepare($q_user);
    $s_user->execute([':uid' => $user_id]);
    $user_data = $s_user->fetch(PDO::FETCH_ASSOC);
    $user_xp = $user_data['xp'] ?? 0;
    
    $badge_name = "Explorer Lv.1";
    if ($user_xp >= 1000) $badge_name = "👑 Thánh Check-in";
    elseif ($user_xp >= 500) $badge_name = "🗺️ Kẻ lang thang";
    elseif ($user_xp >= 100) $badge_name = "🎒 Tân binh xê dịch";

    // Capture dashboard rendering
    ob_start();
    define('UPLOADS_URL', '/uploads');
    require '../app/views/location/dashboard.php';
    $html = ob_get_clean();

    // Output lines 2085 to 2125
    $lines = explode("\n", $html);
    echo "<h3>Lines 2080 to 2130 of rendered dashboard:</h3>";
    echo "<pre style='background:#f4f4f4; padding:15px; border-radius:8px;'>";
    for ($i = 2080; $i <= 2130; $i++) {
        if (isset($lines[$i - 1])) {
            echo htmlspecialchars($i . ": " . $lines[$i - 1]) . "\n";
        }
    }
    echo "</pre>";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
