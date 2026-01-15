<?php
include __DIR__ . '/../../config.php';

$category_name = "";
$parent_id = "";
$id = "";
$message = "";

// Parent category list ana (Dropdown er jonno)
$categories = [];
$cat_sql = "SELECT id, category_name FROM categories WHERE parent_id IS NULL";
$cat_result = $conn->query($cat_sql);
if ($cat_result && $cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) $categories[] = $row;
}

// UPDATE LOGIC: Jokhon Update button-e click kora hobe
if (isset($_POST["btnUpdate"])) {
    $id = $_POST["id"];
    $category_name = trim($_POST["category_name"]);
    $parent_id = trim($_POST["parent_id"]);

    if ($category_name) {
        $stmt = $conn->prepare("UPDATE categories SET category_name=?, parent_id=? WHERE id=?");
        
        if(empty($parent_id)) {
            $null_parent_id = NULL; 
            $stmt->bind_param("sii", $category_name, $null_parent_id, $id);
        } else {
            $stmt->bind_param("sii", $category_name, $parent_id, $id);
        }

        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>Category updated successfully! Redirecting...</div>";
            
            // ETAI REDIRECT LOGIC: 1 second por auto manage_category (page 5) e niye jabe
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'home.php?page=5';
                }, 1000);
            </script>";
        } else {
            $message = "<div class='alert alert-danger'>Error updating record: " . $conn->error . "</div>";
        }
        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning'>Category name is required.</div>";
    }
}

// EDIT DATA LOAD: Page-e dhokar somoy purono data load kora
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $category_name = $row['category_name'];
        $parent_id = $row['parent_id'];
    } else {
        $message = "<div class='alert alert-danger'>No category found with that ID.</div>";
    }
    $stmt->close();
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="card card-primary mt-3">
            <div class="card-header">
                <h3 class="card-title">Update Category</h3>
            </div>
            
            <div class="card-body">
                <?php echo $message; ?>

                <form action="home.php?page=6&id=<?php echo $id; ?>" method="post">
                    <input type="hidden" name="id" value="<?php echo $id ?>">
                    
                    <div class="form-group">
                        <label for="category_name">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" value="<?php echo htmlspecialchars($category_name); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="parent_id">Parent Category (Optional)</label>
                        <select name="parent_id" class="form-control">
                            <option value="">None (Main Category)</option>
                            <?php foreach ($categories as $cat): ?>
                                <?php if ($cat['id'] != $id): // Nije nijer parent hote parbe na ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php if($parent_id == $cat['id']) echo "selected"; ?>>
                                        <?php echo htmlspecialchars($cat['category_name']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="card-footer px-0">
                        <button type="submit" name="btnUpdate" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="home.php?page=5" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>