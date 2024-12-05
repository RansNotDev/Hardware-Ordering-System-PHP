<div class="container-fluid" style="margin-top: 98px">
    <div class="col-lg-12">
        <div class="row">
            <!-- FORM Panel for Creating New Category -->
            <div class="col-md-4">
                <form action="partials/_categoryManage.php" method="post" enctype="multipart/form-data">
                    <div class="card mb-3">
                        <div class="card-header" style="background-color: rgb(111, 202, 203);">
                            Create New Category
                        </div>
                        <div class="card-body">
                            <div class="form-group text-center">
                                <label for="image" class="control-label d-block">Category Image</label>
                                <input type="file" name="image" id="image" accept=".jpg" style="display: none;" required>
                                <img id="imagePreview" src="../img/default.jpg" alt="Image Preview"
                                    style="border-radius: 50%; width: 120px; height: 120px; cursor: pointer; margin-top: 10px;"
                                    onclick="document.getElementById('image').click();">
                                <small class="form-text text-muted mx-3">Choose a .jpg image file.</small>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Category Name: </label>
                                <input type="text" class="form-control" name="name" required>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Category Description: </label>
                                <textarea cols="30" rows="3" class="form-control" name="desc" required></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="mx-auto">
                                    <button type="submit" name="createCategory" class="btn btn-sm btn-primary"> Create </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table Panel -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-hover mb-0">
                            <thead style="background-color: rgb(111, 202, 203);">
                                <tr>
                                    <th class="text-center" style="width: 7%;">ID</th>
                                    <th class="text-center">Image</th>
                                    <th class="text-center" style="width: 58%;">Category Detail</th>
                                    <th class="text-center" style="width: 18%;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM `categories`";
                                $result = mysqli_query($conn, $sql);
                                if ($result) {
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        $catId = $row['categorieId'];
                                        $catName = $row['categorieName'];
                                        $catDesc = $row['categorieDesc'];
                                        $catImage = $row['image']; // Image stored in database

                                        // Convert binary image data to base64
                                        $imageData = base64_encode($catImage);
                                        $src = 'data:image/jpeg;base64,' . $imageData;

                                        echo '<tr>
                                                <td class="text-center">' . htmlspecialchars($catId) . '</td>
                                                <td>
                                                    <img src="' . $src . '" alt="Category Image" width="150px" height="150px">
                                                </td>
                                                <td>
                                                    <p><b>Name:</b> ' . htmlspecialchars($catName) . '</p>
                                                    <p><b>Description:</b> ' . htmlspecialchars($catDesc) . '</p>
                                                </td>
                                                <td class="text-center">
                                                    <div class="row mx-auto" style="width: 112px">
                                                        <button class="btn btn-sm btn-primary" type="button" data-toggle="modal" data-target="#updateCategory' . $catId . '">Edit</button>
                                                        <form action="partials/_categoryManage.php" method="POST" style="display:inline;">
                                                            <button name="removeCategory" class="btn btn-sm btn-danger" style="margin-left: 9px;">Delete</button>
                                                            <input type="hidden" name="catId" value="' . $catId . '">
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>';
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>Error retrieving categories: " . mysqli_error($conn) . "</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Updating Category -->
<?php
$catsql = "SELECT * FROM `categories`";
$catResult = mysqli_query($conn, $catsql);
while ($catRow = mysqli_fetch_assoc($catResult)) {
    $catId = $catRow['categorieId'];
    $catName = $catRow['categorieName'];
    $catDesc = $catRow['categorieDesc'];
    $catImage = $catRow['image'];
?>

    <!-- Modal -->
    <div class="modal fade" id="updateCategory<?php echo $catId; ?>" tabindex="-1" role="dialog" aria-labelledby="updateCategory<?php echo $catId; ?>" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(111, 202, 203);">
                    <h5 class="modal-title" id="updateCategory<?php echo $catId; ?>">Category Id: <?php echo $catId; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">    
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="partials/_categoryManage.php" method="post" enctype="multipart/form-data">
                        <div class="form-group text-center">
                            <label for="catImage<?php echo $catId; ?>" class="control-label d-block">Category Image</label>
                            <input type="file" name="image" id="catImage<?php echo $catId; ?>" accept=".jpg" style="display: none;" onchange="document.getElementById('catImagePreview<?php echo $catId; ?>').src = window.URL.createObjectURL(this.files[0])">
                            <img id="catImagePreview<?php echo $catId; ?>" src="data:image/jpeg;base64,<?php echo base64_encode($catImage); ?>" alt="Category Image"
                                style="border-radius: 50%; width: 120px; height: 120px; cursor: pointer; margin-top: 10px;"
                                onclick="document.getElementById('catImage<?php echo $catId; ?>').click();">
                        </div>
                        <div class="form-group">
                            <label for="name">Category Name:</label>
                            <input class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($catName); ?>" type="text" required>
                        </div>
                        <div class="form-group">
                            <label for="desc">Description:</label>
                            <textarea class="form-control" id="desc" name="desc" rows="2" required><?php echo htmlspecialchars($catDesc); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-success" name="updateCategory">Update</button>
                        <input type="hidden" name="categoryId" value="<?php echo $catId; ?>">
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
}
?>

<script>
    document.getElementById('image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
</script>