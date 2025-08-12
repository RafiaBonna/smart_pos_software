<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Add Category & Subcategory</h1>
        </div>
      </div>
    </div></section>

  <section class="content">

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Category & Subcategory Form</h3>
        <div class="card-tools">
          <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
            <i class="fas fa-minus"></i>
          </button>
          <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title">Category Information</h3>
          </div>
          <form>
            <div class="card-body">
              <div class="form-group">
                <label for="categoryName">Category Name</label>
                <input type="text" class="form-control" id="categoryName" placeholder="Enter category name">
              </div>
              <div class="form-group">
                <label for="parentCategory">Subcategory</label>
                <select class="form-control" id="parentCategory">
                  <option value="">Select a Parent Category</option>
                  <option value="1">Electronics</option>
                  <option value="2">Fashion</option>
                  <option value="3">Home & Garden</option>
                  </select>
              </div>
              <div class="form-group">
                <label for="subcategoryName">Subcategory Name</label>
                <input type="text" class="form-control" id="subcategoryName" placeholder="Enter subcategory name">
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-info">Add</button>
            </div>
          </form>
        </div>
      </div>
      </div>
    </section>
  </div>