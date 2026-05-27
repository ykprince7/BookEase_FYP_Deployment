document.addEventListener("DOMContentLoaded", function () {
    get_bookings();
});

function get_bookings(search = "", page = 1) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/booking_records.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xhr.onload = function () {
        try {
            let data = JSON.parse(this.responseText);
            document.getElementById("table-data").innerHTML = data.table_data ||
                "<tr><td colspan='6' class='text-center py-4'>No Data Found!</td></tr>";
            document.getElementById("table-pagination").innerHTML = data.pagination || "";
        } catch(e) {
            console.error("Parse error:", e);
            console.log("Raw response:", this.responseText);
            document.getElementById("table-data").innerHTML =
                "<tr><td colspan='6' class='text-center text-danger'>Error loading data.</td></tr>";
        }
    };
    xhr.send("get_bookings=1&search=" + encodeURIComponent(search) + "&page=" + page);
}

function release_room(booking_id) {
    if (confirm("Are you sure you want to release this room?")) {
        let data = new FormData();
        data.append("booking_id", booking_id);
        data.append("release_booking", "");
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/booking_records.php", true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.onload = function () {
            try {
                let resp = JSON.parse(this.responseText);
                if (resp.success) {
                    alert(resp.message);
                    get_bookings();
                } else {
                    alert("Failed: " + resp.message);
                }
            } catch(e) {
                console.error("Release room parse error:", e);
                console.log("Raw response:", this.responseText);
                alert("Unexpected error releasing room.");
            }
        };
        xhr.send(data);
    }
}