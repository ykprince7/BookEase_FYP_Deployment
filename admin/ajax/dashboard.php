<?php 

  require('__DIR__../inc/db_config.php');
  require('__DIR__../inc/essentials.php');
  adminLogin();

  function period_condition($period, $column = 'datentime')
  {
    if($period==1){
      return "WHERE $column BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
    }
    else if($period==2){
      return "WHERE $column BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
    }
    else if($period==3){
      return "WHERE $column BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
    }
    return "";
  }

  if(isset($_POST['booking_analytics']))
  {
    $frm_data = filteration($_POST);
    $period = isset($frm_data['period']) ? (int)$frm_data['period'] : 1;
    $condition = period_condition($period, 'bo.datentime');

    // Use stored booking amount from booking_order to reflect discounts/currency conversions.
    $bookings = mysqli_query($con, "SELECT bo.booking_status, bo.arrival, bo.refund, bo.trans_amt FROM booking_order bo $condition");

    $total_bookings = $active_bookings = $cancelled_bookings = 0;
    $total_amt = $active_amt = $cancelled_amt = 0;

    while ($row = mysqli_fetch_assoc($bookings)) {
        $amount = (float)$row['trans_amt'];
        // Total bookings
        if ($row['booking_status'] != 'pending' && $row['booking_status'] != 'payment failed' && $row['booking_status'] != 'payment_failed') {
            $total_bookings++;
            $total_amt += $amount;
        }
        // Active bookings
        if ($row['booking_status'] == 'booked' && $row['arrival'] == 1) {
            $active_bookings++;
            $active_amt += $amount;
        }
        // Cancelled bookings
        if ($row['booking_status'] == 'cancelled' && $row['refund'] == 1) {
            $cancelled_bookings++;
            $cancelled_amt += $amount;
        }
    }

    $output = [
        'total_bookings' => $total_bookings,
        'total_amt' => round($total_amt, 2),
        'active_bookings' => $active_bookings,
        'active_amt' => round($active_amt, 2),
        'cancelled_bookings' => $cancelled_bookings,
        'cancelled_amt' => round($cancelled_amt, 2)
    ];

    echo json_encode($output);
  }


  if(isset($_POST['user_analytics']))
  {
    $frm_data = filteration($_POST);
    $period = isset($frm_data['period']) ? (int)$frm_data['period'] : 1;
    $condition = period_condition($period, 'datentime');

    $total_reviews = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(sr_no) AS `count`
      FROM `rating_review` $condition"));

    $total_queries = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(sr_no) AS `count`
      FROM `user_queries` $condition"));

    $total_new_reg = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count`
    FROM `user_cred` $condition"));

    $output = ['total_queries' => $total_queries['count'],
      'total_reviews' => $total_reviews['count'],
      'total_new_reg' => $total_new_reg['count']
    ];

    $output = json_encode($output);

    echo $output;

  }

?>