document.addEventListener("DOMContentLoaded", function () {
    get_bookings();
  
    let assign_room_form = document.getElementById("assign_room_form");
  
    if (!assign_room_form) {
        console.warn("assign_room_form not found — assign room feature disabled.");
        return;
    }
  
    assign_room_form.addEventListener("submit", function (e) {
        e.preventDefault();
        let data = new FormData();
        data.append("room_no", assign_room_form.elements["room_no"].value);
        data.append("booking_id", assign_room_form.elements["booking_id"].value);
        data.append("assign_room", "");
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/new_bookings.php", true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.onload = function () {
            var myModal = document.getElementById("assign-room");
            var modal = bootstrap.Modal.getInstance(myModal);
            if (modal) modal.hide();
            let resp = (this.responseText || "").trim();
            if (resp === "1") {
                alert('success', "Room number assigned! Booking finalized.");
                assign_room_form.reset();
                get_bookings();
            } else if (resp === "email_failed_ok") {
                alert('success', "Room assigned. Confirmation email could not be sent.");
                assign_room_form.reset();
                get_bookings();
            } else if (resp === "not_logged_in") {
                alert('error', "Session expired. Please log in again.");
                window.location.href = "index.php";
            } else {
                console.error("Assign room unexpected response:", resp);
                alert('error', resp ? "Could not assign room: " + resp : "Could not assign room. Check that MySQL is running.");
            }
        };
        xhr.send(data);
    });
  });
  
  function get_bookings(search = "") {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_bookings.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.onload = function () {
        try {
            let data = JSON.parse(this.responseText);
            document.getElementById("table-data").innerHTML = data.table_data ||
                "<tr><td colspan='5' class='text-center py-4'>No Data Found!</td></tr>";
        } catch(e) {
            console.error("Parse error:", e);
            console.log("Raw response:", this.responseText);
            document.getElementById("table-data").innerHTML =
                "<tr><td colspan='5' class='text-center text-danger'>Error loading data.</td></tr>";
        }
    };
    xhr.send("get_bookings=1&search=" + encodeURIComponent(search));
  }
  
  function assign_room(id) {
    let form = document.getElementById("assign_room_form");
    if (form) {
        form.elements["booking_id"].value = id;
    }
  }
  
  function cancel_booking(id) {
    if (confirm("Are you sure, you want to cancel this booking?")) {
        let data = new FormData();
        data.append("booking_id", id);
        data.append("cancel_booking", "");
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/new_bookings.php", true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.onload = function () {
            let resp = this.responseText.trim();
            if (resp === "not_logged_in") {
                alert('error', "Session expired. Please login again.");
                window.location.href = "index.php";
                return;
            }
            if (resp == 1) {
                alert('success', "Booking Cancelled!");
                get_bookings();
            } else if (resp == "Email failed but booking cancelled") {
                alert('success', "Booking cancelled (email failed).");
                get_bookings();
            } else {
                console.error("Cancel booking unexpected response:", resp);
                alert('error', "Server Down! (debug: " + resp + ")");
            }
        };
        xhr.send(data);
    }
  }