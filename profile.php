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

// Ensure `address` column exists in `users` table
$colStmt2 = $conn->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users' AND COLUMN_NAME = 'address' LIMIT 1");
$colStmt2->bind_param('s', $dbname);
$colStmt2->execute();
$colRes2 = $colStmt2->get_result();
if (!$colRes2 || $colRes2->num_rows == 0) {
    // Add column
    $conn->query("ALTER TABLE users ADD COLUMN address VARCHAR(255) DEFAULT NULL");
}
$colStmt2->close();

// Ensure `mobile` column exists in `users` table
$colStmt3 = $conn->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'users' AND COLUMN_NAME = 'mobile' LIMIT 1");
$colStmt3->bind_param('s', $dbname);
$colStmt3->execute();
$colRes3 = $colStmt3->get_result();
if (!$colRes3 || $colRes3->num_rows == 0) {
    // Add column
    $conn->query("ALTER TABLE users ADD COLUMN mobile VARCHAR(50) DEFAULT NULL");
}
$colStmt3->close();

$stmt = $conn->prepare("SELECT username, email, profile_photo, address, mobile FROM users WHERE id = ? LIMIT 1");
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
    $new_address = trim($_POST['address'] ?? '');
    $new_mobile = trim($_POST['mobile'] ?? '');
    $remove_photo = isset($_POST['remove_photo']) && $_POST['remove_photo'] === '1';

    // Validate username/email/mobile/address
    if (empty($new_username) || !preg_match('/^[A-Za-z0-9_]{3,20}$/', $new_username)) {
        $message = "⚠️ Username must be 3–20 characters (letters, numbers, underscore).";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $message = "⚠️ Invalid email address.";
    } elseif (!empty($new_mobile) && !preg_match('/^[+\d][\d\s\-()]{6,20}$/', $new_mobile)) {
        $message = "⚠️ Invalid mobile number format.";
    } elseif (!empty($new_address) && strlen($new_address) > 255) {
        $message = "⚠️ Address is too long (max 255 characters).";
    } else {
        // Check uniqueness for username/email (exclude current user)
        $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id <> ?");
        $stmt->bind_param('ssi', $new_username, $new_email, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $message = "⚠️ Username or email already in use by another account.";
        } else {
            // Build update query (handle uploaded photo or explicit removal)
            if ($uploaded_photo) {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_photo = ?, address = ?, mobile = ? WHERE id = ?");
                $stmt->bind_param('sssssi', $new_username, $new_email, $uploaded_photo, $new_address, $new_mobile, $user_id);
            } elseif ($remove_photo) {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, profile_photo = NULL, address = ?, mobile = ? WHERE id = ?");
                $stmt->bind_param('ssssi', $new_username, $new_email, $new_address, $new_mobile, $user_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, address = ?, mobile = ? WHERE id = ?");
                $stmt->bind_param('ssssi', $new_username, $new_email, $new_address, $new_mobile, $user_id);
            }
            $ok = $stmt->execute();
            if ($ok) {
                // if uploading, remove old file; if removing, also remove old file and clear session
                if ($uploaded_photo && !empty($user['profile_photo'])) {
                    $oldPath = __DIR__ . '/' . $user['profile_photo'];
                    if (file_exists($oldPath)) @unlink($oldPath);
                }
                if ($remove_photo && !empty($user['profile_photo'])) {
                    $oldPath = __DIR__ . '/' . $user['profile_photo'];
                    if (file_exists($oldPath)) @unlink($oldPath);
                    $user['profile_photo'] = null;
                    unset($_SESSION['profile_photo']);
                }
                if ($uploaded_photo) {
                    $user['profile_photo'] = $uploaded_photo;
                    $_SESSION['profile_photo'] = $uploaded_photo;
                }
                // update local user array for immediate display
                $user['username'] = $new_username;
                $user['email'] = $new_email;
                $user['address'] = $new_address;
                $user['mobile'] = $new_mobile;
                $message = "✅ Profile updated successfully.";
                $_SESSION['username'] = $new_username;
            } else {
                $message = "❌ Database error: " . $conn->error;
            }
            $stmt->close();
        }
    }
}

include 'header.php';
?>

<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery and jQuery Validate -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJ+Y2v3l3Q9a2hYk0KcQ5p5Y5Q5p5e5h5j5v8=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('profilePhotoInput');
        const preview = document.getElementById('photoPreview');
        const changeBtn = document.getElementById('changePhotoBtn');
        const removeBtn = document.getElementById('removePhotoBtn');
        const removePhotoInput = document.getElementById('removePhotoInput');

        const editBtn = document.getElementById('editBtn');
        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const usernameInput = document.getElementById('usernameInput');
        const emailInput = document.getElementById('emailInput');
        const mobileInput = document.getElementById('mobileInput');
        const addressInput = document.getElementById('addressInput');

        if (!input || !preview) return;

        // store initial values to support Cancel
        const initialState = {
            username: usernameInput ? usernameInput.value : '',
            email: emailInput ? emailInput.value : '',
            mobile: mobileInput ? mobileInput.value : '',
            address: addressInput ? addressInput.value : '',
            photoSrc: preview ? preview.getAttribute('src') || '' : '',
            photoHasInitial: !!preview.dataset.initial
        };

        // Helper to show/hide file input only. Change button visibility
        // is controlled by edit mode so it's only shown while editing.
        function showInputChooser(show) {
            input.style.display = show ? 'block' : 'none';
        }

        // Initialize photo chooser visibility and hide change button by default
        if (preview.src && preview.getAttribute('src') !== '') {
            showInputChooser(false);
        } else {
            showInputChooser(true);
        }
        if (changeBtn) changeBtn.style.display = 'none';
        if (removeBtn) removeBtn.style.display = 'none';

        // Photo chooser change handler
        input.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (!file) {
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
                showInputChooser(false);
            };
            reader.readAsDataURL(file);
        });

        // jQuery Validate initialization
        if (window.jQuery && $('#profileForm').length) {
            // add username pattern method
            $.validator.addMethod('usernamePattern', function(value, element) {
                return this.optional(element) || /^[A-Za-z0-9_]{3,20}$/.test(value);
            }, 'Username must be 3–20 characters (letters, numbers, underscore).');

            // mobile pattern: allow + and digits with spaces/dashes/parens, 7-20 chars
            $.validator.addMethod('mobilePattern', function(value, element) {
                return this.optional(element) || /^[+\d][\d\s\-()]{6,20}$/.test(value);
            }, 'Invalid mobile number format.');

            $('#profileForm').validate({
                errorClass: 'is-invalid',
                validClass: 'is-valid',
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    if (element.parent('.input-group').length) {
                        error.insertAfter(element.parent());
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                rules: {
                    username: {
                        required: true,
                        usernamePattern: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    mobile: {
                        mobilePattern: true
                    },
                    address: {
                        maxlength: 255
                    }
                },
                messages: {
                    username: {
                        required: 'Username is required.'
                    },
                    email: {
                        required: 'Email is required.',
                        email: 'Enter a valid email.'
                    },
                    mobile: {
                        mobilePattern: 'Enter a valid mobile number (e.g. +123456789).'
                    },
                    address: {
                        maxlength: 'Address is too long (max 255 characters).'
                    }
                },
                submitHandler: function(form) {
                    // let server handle final validation
                    form.submit();
                }
            });
        }

        if (changeBtn) {
            changeBtn.addEventListener('click', function() {
                // choosing a new file cancels a remove request
                if (removePhotoInput) removePhotoInput.value = '0';
                showInputChooser(true);
                input.value = '';
                if (!preview.dataset.initial) {
                    preview.style.display = 'none';
                    preview.src = '';
                }
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                // mark for removal and clear preview/file chooser
                if (removePhotoInput) removePhotoInput.value = '1';
                if (input) input.value = '';
                if (preview) {
                    preview.src = '';
                    preview.style.display = 'none';
                }
                // show file chooser so user can pick a new file immediately
                showInputChooser(true);
                // hide change/remove buttons after removal
                if (changeBtn) changeBtn.style.display = 'none';
                if (removeBtn) removeBtn.style.display = 'none';
            });
        }

        // Toggle edit mode
        function setEditMode(on) {
            if (usernameInput) usernameInput.readOnly = !on;
            if (emailInput) emailInput.readOnly = !on;
            if (mobileInput) mobileInput.readOnly = !on;
            if (addressInput) addressInput.readOnly = !on;
            if (input) input.disabled = !on;
            if (changeBtn) changeBtn.disabled = !on;

            if (on) {
                if (saveBtn) saveBtn.style.display = '';
                if (cancelBtn) cancelBtn.style.display = '';
                if (editBtn) editBtn.style.display = 'none';
                // if preview exists keep chooser hidden until user clicks change; show remove button
                if (preview && preview.getAttribute('src') && preview.getAttribute('src') !== '') {
                    showInputChooser(false);
                    if (changeBtn) changeBtn.style.display = 'inline-block';
                    if (removeBtn) removeBtn.style.display = 'inline-block';
                } else {
                    showInputChooser(true);
                    if (changeBtn) changeBtn.style.display = 'none';
                    if (removeBtn) removeBtn.style.display = 'none';
                }
            } else {
                if (saveBtn) saveBtn.style.display = 'none';
                if (cancelBtn) cancelBtn.style.display = 'none';
                if (editBtn) editBtn.style.display = '';
                // hide change/remove buttons when not editing
                if (changeBtn) changeBtn.style.display = 'none';
                if (removeBtn) removeBtn.style.display = 'none';
                showInputChooser(!initialState.photoSrc);
            }
        }

        // Edit button
        if (editBtn) {
            editBtn.addEventListener('click', function() {
                // save current state for possible cancel (overwrite initialState temporary)
                tempState.username = usernameInput ? usernameInput.value : '';
                tempState.email = emailInput ? emailInput.value : '';
                tempState.mobile = mobileInput ? mobileInput.value : '';
                tempState.address = addressInput ? addressInput.value : '';
                tempState.photoSrc = preview ? preview.getAttribute('src') || '' : '';
                if (removePhotoInput) removePhotoInput.value = '0';
                setEditMode(true);
            });
        }

        // temp state used while editing
        const tempState = {
            username: initialState.username,
            email: initialState.email,
            mobile: initialState.mobile,
            address: initialState.address,
            photoSrc: initialState.photoSrc
        };

        // Cancel button restores previous values
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                if (usernameInput) usernameInput.value = initialState.username;
                if (emailInput) emailInput.value = initialState.email;
                if (mobileInput) mobileInput.value = initialState.mobile;
                if (addressInput) addressInput.value = initialState.address;
                // restore photo preview
                if (preview) {
                    preview.src = initialState.photoSrc || '';
                    if (initialState.photoSrc) {
                        preview.style.display = 'inline-block';
                    } else {
                        preview.style.display = 'none';
                    }
                }
                // clear file chooser
                if (input) input.value = '';
                if (removePhotoInput) removePhotoInput.value = '0';
                setEditMode(false);
            });
        }

        // Start in view mode
        setEditMode(false);
    });
</script>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Your Profile</h4>
                    <?php if (!empty($message)) echo "<div class='alert alert-info'>" . $message . "</div>"; ?>

                    <form id="profileForm" method="POST" enctype="multipart/form-data">
                        <div class="mb-3 text-center">
                            <img id="photoPreview" src="<?php echo !empty($user['profile_photo']) ? htmlspecialchars($user['profile_photo']) : ''; ?>" <?php echo !empty($user['profile_photo']) ? 'data-initial="1"' : ''; ?> alt="Profile Photo" style="max-width:120px;max-height:120px;border-radius:8px;<?php echo empty($user['profile_photo']) ? 'display:none;' : ''; ?>">
                        </div>

                        <div class="mb-3" id="photoControls">
                            <!-- <label class="form-label">Profile Photo (optional)</label> -->
                            <input type="file" id="profilePhotoInput" name="profile_photo" accept="image/*" class="form-control" style="<?php echo empty($user['profile_photo']) ? '' : 'display:none;'; ?>" disabled>
                            <button type="button" id="changePhotoBtn" class="btn btn-outline-primary btn-sm mt-2" style="<?php echo empty($user['profile_photo']) ? 'display:none;' : ''; ?>" disabled>Change Photo</button>
                            <button type="button" id="removePhotoBtn" class="btn btn-outline-danger btn-sm mt-2" style="display:none;">Remove Photo</button>
                            <input type="hidden" id="removePhotoInput" name="remove_photo" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" id="usernameInput" name="username" class="form-control" required
                                value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" id="emailInput" name="email" class="form-control" required
                                value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mobile Number</label>
                            <input type="text" id="mobileInput" name="mobile" class="form-control"
                                value="<?php echo htmlspecialchars($user['mobile'] ?? ''); ?>" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" id="addressInput" name="address" class="form-control"
                                value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" readonly>
                        </div>

                        <div class="d-flex align-items-center">
                            <button id="saveBtn" type="submit" class="btn btn-primary" style="display:none;">Save Changes</button>
                            <button id="cancelBtn" type="button" class="btn btn-secondary ms-2" style="display:none;">Cancel</button>
                            <button id="editBtn" type="button" class="btn btn-outline-primary ms-2">Edit</button>
                            <a href="index.php" class="btn btn-outline-primary ms-3">Back</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>