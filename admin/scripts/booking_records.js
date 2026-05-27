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
        alert(res.message || 'Operation completed');
        if (res.success) {
            get_bookings(document.getElementById('search_input').value);
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

            const tableBody = document.getElementById("table-data");

            if (data.table_data) {
                // Clean up any leading/trailing newlines or broken HTML
                let cleanHtml = data.table_data.trim();
                
                // Safety: Ensure it starts with <tr> if it's row data
                if (cleanHtml && !cleanHtml.startsWith('<tr')) {
                    cleanHtml = cleanHtml.replace(/^[^<]*/, ''); // Remove any junk before first <tr>
                }

                tableBody.innerHTML = cleanHtml || "<tr><td colspan='6' class='text-center py-4'>No Data Found!</td></tr>";
            } else {
                tableBody.innerHTML = "<tr><td colspan='6' class='text-center py-4'>No Data Found!</td></tr>";
            }

            if (data.pagination) {
                const paginationEl = document.getElementById("pagination");
                if (paginationEl) {
                    paginationEl.innerHTML = data.pagination;
                }
            }
        } 
        catch(e) {
            console.error("Response parsing error:", e);
            console.log("Raw response:", this.responseText); // For debugging
            document.getElementById("table-data").innerHTML = 
                `<tr><td colspan='6' class='text-center text-danger'>
                    Error loading bookings. Check console for details.
                 </td></tr>`;
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