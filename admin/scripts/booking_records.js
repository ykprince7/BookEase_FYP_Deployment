function release_room(booking_id)
{
    if (!confirm('Release this room?\n\nThis will mark the booking as completed and free up the room slot for new bookings. This cannot be undone.')) {
        return;
    }

    fetch('ajax/booking_records.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'release_booking=1&booking_id=' + booking_id
    })
    .then(response => response.text())
    .then(res => {
        if (res.trim() === '1') {
            alert('Room released successfully!');
            get_bookings(document.getElementById('search_input').value);
        } else {
            alert('Failed to release room. Please try again.');
        }
    })
    .catch(err => {
        console.error('Release error:', err);
        alert('Something went wrong! Please check console.');
    });
}

function get_bookings(search = "", page = 1)
{
    let xhr = new XMLHttpRequest();

    xhr.open("POST", "ajax/booking_records.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function ()
    {
        try {
            let data = JSON.parse(this.responseText);

            const tableBody      = document.getElementById("table-data");
            const paginationEl   = document.getElementById("table-pagination"); // fixed ID

            tableBody.innerHTML = data.table_data ||
                "<tr><td colspan='6' class='text-center py-4'>No Data Found!</td></tr>";

            if (paginationEl && data.pagination) {
                paginationEl.innerHTML = data.pagination;
            }

        } catch(e) {
            console.error("Parse error:", e);
            console.log("Raw response:", this.responseText);
            document.getElementById("table-data").innerHTML =
                "<tr><td colspan='6' class='text-center text-danger'>Error loading data.</td></tr>";
        }
    };

    xhr.onerror = function() {
        console.error("Network error occurred");
        document.getElementById("table-data").innerHTML =
            "<tr><td colspan='6' class='text-center text-danger'>Network error. Please try again.</td></tr>";
    };

    xhr.send(
        "get_bookings=1&search=" + encodeURIComponent(search) + "&page=" + page
    );
}

function change_page(page)
{
    get_bookings(document.getElementById('search_input').value, page);
}

function download(id)
{
    window.location.href = 'generate_pdf.php?gen_pdf&id=' + id;
}

window.onload = function ()
{
    get_bookings();
};