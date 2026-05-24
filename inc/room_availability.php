<?php
/**
 * Room booking capacity — uses rooms.quantity from admin (never auto-changed here).
 * Only booking_status = booked occupies a slot.
 * Admin release sets status to completed → slot is free immediately.
 * Pending / payment_failed / cancelled / completed never count.
 */

if (!function_exists('activeBookingStatusSql')) {

    function activeBookingStatusSql($alias = '')
    {
        $col = $alias !== '' ? "`{$alias}`.`booking_status`" : '`booking_status`';
        return "(LOWER(TRIM({$col})) = 'booked')";
    }

    function countActiveBookingsForRoom($room_id, $check_in, $check_out, $con)
    {
        $room_id = (int) $room_id;
        $active = activeBookingStatusSql();
        $q = "SELECT COUNT(*) AS `cnt` FROM `booking_order`
              WHERE `room_id` = ?
              AND {$active}
              AND `check_out` > ? AND `check_in` < ?";
        $res = select($q, [$room_id, $check_in, $check_out], 'iss');
        $row = mysqli_fetch_assoc($res);
        return (int) ($row['cnt'] ?? 0);
    }

    function getRoomBookingLimit($room_id, $con)
    {
        $res = select(
            "SELECT `quantity` FROM `rooms` WHERE `id`=? AND `removed`=0 LIMIT 1",
            [(int) $room_id],
            'i'
        );
        if ($res && ($row = mysqli_fetch_assoc($res))) {
            return max(1, (int) $row['quantity']);
        }
        return 1;
    }

    function cancelUnpaidBookingsForDateRange($room_id, $check_in, $check_out, $con)
    {
        $room_id = (int) $room_id;
        $q = "UPDATE `booking_order` SET `booking_status`='cancelled'
              WHERE `room_id` = ?
              AND LOWER(TRIM(`booking_status`)) IN ('pending', 'payment_failed', 'payment failed')
              AND `check_out` > ? AND `check_in` < ?";
        return update($q, [$room_id, $check_in, $check_out], 'iss');
    }

    function cancelStalePendingForDateRange($room_id, $check_in, $check_out, $con)
    {
        return cancelUnpaidBookingsForDateRange($room_id, $check_in, $check_out, $con);
    }

    /**
     * @return array{available:bool,limit:int,used:int,remaining:int}
     */
    function checkRoomDateAvailability($room_id, $check_in, $check_out, $con)
    {
        $limit = getRoomBookingLimit((int) $room_id, $con);
        $used  = countActiveBookingsForRoom($room_id, $check_in, $check_out, $con);
        $remaining = max(0, $limit - $used);

        return [
            'available' => $remaining > 0,
            'limit'     => $limit,
            'used'      => $used,
            'remaining' => $remaining,
        ];
    }
}
