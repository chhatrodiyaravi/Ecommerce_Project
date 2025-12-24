<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    // Not logged in → redirect to login and come back
    header('Location: login.php?redirect=profile.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Ensure `profile_photo` column exists in `users` table
$colStmt = $conn->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users' AND COLUMN_NAME = 'profile_photo' LIMIT 1");
$colStmt->bind_param('s', $dbname);
$colStmt->execute();
$colRes = $colStmt->get_result();
if (!$colRes || $colRes->num_rows == 0) {
    // Add column
    $conn->query("ALTER TABLE users ADD COLUMN profile_photo VARCHAR(255) DEFAULT NULL");
}
$colStmt->close();

// Fetch current user info (used for displaying current photo and for cleanup)
$stmt = $conn->prepare("SELECT username, email, profile_photo FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (!empty($user['profile_photo'])) {
        $_SESSION['profile_photo'] = $user['profile_photo'];
    }
} else {
    header('Location: logout.php');
    exit;
}
$stmt->close();

// Handle updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle profile photo upload if provided
    $uploaded_photo = null;
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_photo'];
        // Basic validations
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $message = "⚠️ Error uploading file.";
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $message = "⚠️ File too large (max 2MB).";
        } elseif (!in_array(mime_content_type($file['tmp_name']), $allowed)) {
            $message = "⚠️ Invalid file type. Use JPG, PNG, WebP or GIF.";
        } else {
            // Ensure upload directory exists
            $uploadDir = __DIR__ . '/uploads/profiles';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safeName = 'user_' . $user_id . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . '/' . $safeName;
            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                $uploaded_photo = 'uploads/profiles/' . $safeName;
            } else {
                $message = "⚠️ Failed to save uploaded file.";
            }
        }
    }
    // Update basic info
    $new_username = trim($_POST['username'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate username/email
    if (empty($new_username) || !preg_match('/^[A-Za-z0-9_]{3,20}$/', $new_username)) {
        $message = "⚠️ Username must be 3–20 characters (letters, numbers, underscore).";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "⚠️ Invalid email address.";
    } else {
        // Check uniqueness for username/email (exclude current user)
        $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id <> ?");
        $stmt->bind_param('ssi', $new_username, $new_email, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $message = "⚠️ Username or email already in use by another account.";
        } else {
            // Build update query
            if (!empty($new_password)) {
                if ($new_password !== $confirm_password) {
                    $message = "⚠️ Passwords do not match.";
                } elseif (strlen($new_password) < 6) {
                    $message = "⚠️ Password must be at least 6 characters.";
                } else {
                    $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                    if ($uploaded_photo) {
                        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, profile_photo = ? WHERE id = ?");
                        $stmt->bind_param('ssssi', $new_username, $new_email, $hashed, $uploaded_photo, $user_id);
                    } else {
                        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
                        $stmt->bind_param('sssi', $new_username, $new_email, $hashed, $user_id);
                    }
                    $ok = $stmt->execute();
                    if ($ok) {
                        // remove old photo file if we uploaded a new one
                        if ($uploaded_photo && !empty($user['profile_photo'])) {
                            $oldPath = __DIR__ . '/' . $user['profile_photo'];
                            if (file_exists($oldPath)) @unlink($oldPath);
                        }
                        if ($uploaded_photo) {
                            $user['profile_photo'] = $uploaded_photo;
                            $_SESSION['profile_photo'] = $uploaded_photo;
                        }
                        $message = "✅ Profile and password updated successfully.";
                        $_SESSION['username'] = $new_username;
                    } else {
                        $message = "❌ Database error: " . $conn->error;
                    }
                }
            } else {
                if ($uploaded_photo) {
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_photo = ? WHERE id = ?");
                    $stmt->bind_param('sssi', $new_username, $new_email, $uploaded_photo, $user_id);
                } else {
                    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                    $stmt->bind_param('ssi', $new_username, $new_email, $user_id);
                }
                $ok = $stmt->execute();
                if ($ok) {
                    if ($uploaded_photo && !empty($user['profile_photo'])) {
                        $oldPath = __DIR__ . '/' . $user['profile_photo'];
                        if (file_exists($oldPath)) @unlink($oldPath);
                    }
                    if ($uploaded_photo) {
                        $user['profile_photo'] = $uploaded_photo;
                        $_SESSION['profile_photo'] = $uploaded_photo;
                    }
                    $message = "✅ Profile updated successfully.";
                    $_SESSION['username'] = $new_username;
                } else {
                    $message = "❌ Database error: " . $conn->error;
                }
            }
        }
        $stmt->close();
    }
}

include 'header.php';
?>

<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('profilePhotoInput');
        const preview = document.getElementById('photoPreview');
        const changeBtn = document.getElementById('changePhotoBtn');
        if (!input || !preview) return;

        // Helper to show/hide elements
        function showInput(show) {
            input.style.display = show ? 'block' : 'none';
            if (changeBtn) changeBtn.style.display = show ? 'none' : 'inline-block';
        }

        // Initial state: if preview has a src (server-provided) hide chooser
        if (preview.src && preview.getAttribute('src') !== '') {
            // preview may be visible via inline style; hide input
            showInput(false);
        } else {
            showInput(true);
        }

        input.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) {
                // no file selected — if no existing preview, hide it
                if (!preview.getAttribute('src')) preview.style.display = 'none';
                return;
            }
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                input.value = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                preview.style.display = 'inline-block';
                // after display, hide chooser and show change button
                showInput(false);
            };
            reader.readAsDataURL(file);
        });

        if (changeBtn) {
            changeBtn.addEventListener('click', function() {
                // re-enable chooser
                showInput(true);
                // clear current selection
                input.value = '';
                // if there was no initial photo, hide preview until user chooses
                if (!preview.dataset.initial) {
                    preview.style.display = 'none';
                    preview.src = '';
                }
            });
        }
    });
</script>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Your Profile</h4>
                    <?php if (!empty($message)) echo "<div class='alert alert-info'>" . $message . "</div>"; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3 text-center">
                            <img id="photoPreview" src="<?php echo !empty($user['profile_photo']) ? htmlspecialchars($user['profile_photo']) : ''; ?>" <?php echo !empty($user['profile_photo']) ? 'data-initial="1"' : ''; ?> alt="Profile Photo" style="max-width:120px;max-height:120px;border-radius:8px;<?php echo empty($user['profile_photo']) ? 'display:none;' : ''; ?>">
                        </div>

                        <div class="mb-3" id="photoControls">
                            <label class="form-label">Profile Photo (optional)</label>
                            <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*" class="form-control" style="<?php echo empty($user['profile_photo']) ? '' : 'display:none;'; ?>">
                            <button type="button" id="changePhotoBtn" class="btn btn-link btn-sm mt-2" style="<?php echo empty($user['profile_photo']) ? 'display:none;' : ''; ?>">Change Photo</button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required
                                value="<?php echo htmlspecialchars($user['username']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required
                                value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>

                        <hr>
                        <p class="text-muted">Leave password fields blank to keep current password.</p>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control">
                        </div>

                        <button class="btn btn-primary">Save Changes</button>
                        <a href="index.php" class="btn btn-secondary ms-2">Back to Shop</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>