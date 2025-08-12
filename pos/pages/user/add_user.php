
<?php
include("config.php");
if(isset($_POST['submit'])){
  $name=$_POST['username'];
  $role=$_POST['userrole'];
  $email=$_POST['mail'];
  $password=$_POST['word'];
  $sql="INSERT INTO 'users' ('username','userrole','mail','word') VALUES ('$name','$role','$email','$password')";
  $result =$conn->query($sql);
  if($result == TRUE){
    echo "New record created successfully";
  } else{
    echo "Failed to create new record.";
  }
}
?>

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
                <input type="text" class="form-control" name="username" placeholder="Enter name">
              </div>
              <div class="form-group">
                <label for="userRole">Role</label>
                <select class="form-control" name="userrole">
                  <option>Admin</option>
                  <option>Manager</option>
                  <option>User</option>
                </select>
              </div>
              <div class="form-group">
                <label for="userEmail">Email address</label>
                <input type="email" class="form-control" name="mail" placeholder="Enter email">
              </div>
              <div class="form-group">
                <label for="userPassword">Password</label>
                <input type="password" class="form-control" name="word" placeholder="Password">
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