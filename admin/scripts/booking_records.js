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
    .then(response => response.json())
    .then(res => {
        alert(res.message);
        if (res.success) {
            get_bookings(document.getElementById('search_input').value);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Something went wrong!');
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

            document.getElementById("table-data").innerHTML = 
                data.table_data || "<tr><td colspan='6' class='text-center py-4'>No Data Found!</td></tr>";

            if (data.pagination) {
                document.getElementById("pagination").innerHTML = data.pagination;
            }
        } catch(e) {
            console.error("Response parsing error:", e);
            document.getElementById("table-data").innerHTML = 
                "<tr><td colspan='6' class='text-center text-danger'>Error loading bookings</td></tr>";
        }
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