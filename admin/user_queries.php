<?php
  require('inc/essentials.php');
  require('inc/db_config.php');
  adminLogin();

  if(isset($_GET['seen']))
  {
    $frm_data = filteration($_GET);

    if($frm_data['seen']=='all'){
      $q = "UPDATE `user_queries` SET `seen`=?";
      $values = [1];
      if(update($q,$values,'i')){
        alert('success','Marked all as read!');
      }
      else{
        alert('error','Operation Failed!');
      }
    }
    else{
      $q = "UPDATE `user_queries` SET `seen`=? WHERE `sr_no`=?";
      $values = [1,$frm_data['seen']];
      if(update($q,$values,'ii')){
        alert('success','Marked as read!');
      }
      else{
        alert('error','Operation Failed!');
      }
    }
  }

  if(isset($_GET['del']))
  {
    $frm_data = filteration($_GET);

    if($frm_data['del']=='all'){
      $q = "DELETE FROM `user_queries`";
      if(mysqli_query($con,$q)){
        alert('success','All data deleted!');
      }
      else{
        alert('error','Operation failed!');
      }
    }
    else{
      $q = "DELETE FROM `user_queries` WHERE `sr_no`=?";
      $values = [$frm_data['del']];
      if(delete($q,$values,'i')){
        alert('success','Data deleted!');
      }
      else{
        alert('error','Operation failed!');
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - User Queries</title>
  <?php require('inc/links.php'); ?>
</head>
<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container-fluid" id="main-content">
    <div class="row">
      <div class="col-lg-10 ms-auto p-4 overflow-hidden">
        <h3 class="mb-4">USER QUERIES</h3>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">

            <div class="d-flex flex-wrap gap-2 justify-content-end mb-4">
              <a href="?seen=all" class="btn btn-primary shadow-none btn-sm d-inline-flex align-items-center gap-2 rounded-3 fw-semibold">
                <i class="bi bi-envelope-open" aria-hidden="true"></i>
                <span>Mark all read</span>
              </a>
              <a href="?del=all" class="btn btn-danger shadow-none btn-sm d-inline-flex align-items-center gap-2 rounded-3 fw-semibold">
                <i class="bi bi-trash3" aria-hidden="true"></i>
                <span>Delete all</span>
              </a>
            </div>

            <div class="table-responsive-md" style="max-height: 560px; overflow-y: auto;">
              <table class="table table-hover border user-queries-table">
                <thead class="sticky-top">
                  <tr class="bg-dark text-light">
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col" width="20%">Subject</th>
                    <th scope="col" width="30%">Message</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                    $q = "SELECT * FROM `user_queries` ORDER BY `sr_no` DESC";
                    $data = mysqli_query($con,$q);
                    $i = 1;

                    if(mysqli_num_rows($data) === 0){
                      echo '<tr><td colspan="7" class="text-center text-muted py-4">No queries found.</td></tr>';
                    }

                    while($row = mysqli_fetch_assoc($data))
                    {
                      $date = date('d-m-Y',strtotime($row['datentime']));
                      $seen = '<div class="admin-action-group">';
                      if($row['seen'] != 1){
                        $seen .= "<a href='?seen=$row[sr_no]' class='btn btn-admin-icon btn-admin-icon--edit shadow-none' title='Mark as read'><i class='bi bi-envelope-open' aria-hidden='true'></i></a>";
                      }
                      $seen .= "<a href='?del=$row[sr_no]' class='btn btn-admin-icon btn-admin-icon--danger shadow-none' title='Delete'><i class='bi bi-trash3' aria-hidden='true'></i></a>";
                      $seen .= '</div>';

                      echo<<<query
                        <tr>
                          <td>$i</td>
                          <td>$row[name]</td>
                          <td>$row[email]</td>
                          <td>$row[subject]</td>
                          <td>$row[message]</td>
                          <td>$date</td>
                          <td>$seen</td>
                        </tr>
                      query;
                      $i++;
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
  

  <?php require('inc/scripts.php'); ?>

</body>
</html>