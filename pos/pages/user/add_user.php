<div class="content-wrapper">
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Add Users</h1>
        </div>
      </div>
    </div></section>

  <section class="content">

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">User Form</h3>
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
            <h3 class="card-title">User Information</h3>
          </div>
          <form>
            <div class="card-body">
              <div class="form-group">
                <label for="userName">Name</label>
                <input type="text" class="form-control" id="userName" placeholder="Enter name">
              </div>
              <div class="form-group">
                <label for="userRole">Role</label>
                <select class="form-control" id="userRole">
                  <option>Admin</option>
                  <option>Manager</option>
                  <option>User</option>
                </select>
              </div>
              <div class="form-group">
                <label for="userEmail">Email address</label>
                <input type="email" class="form-control" id="userEmail" placeholder="Enter email">
              </div>
              <div class="form-group">
                <label for="userPassword">Password</label>
                <input type="password" class="form-control" id="userPassword" placeholder="Password">
              </div>
              <div class="form-group">
                <label for="userFile">Profile Picture</label>
                <div class="input-group">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="userFile">
                    <label class="custom-file-label" for="userFile">Choose file</label>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-info">Add User</button>
            </div>
          </form>
        </div>
      </div>
      </div>
    </section>
  </div>