function release_room(booking_id) {
  if (!confirm('Release this room?\n\nThis will mark the booking as completed and free up the room slot for new bookings. This cannot be undone.')) return;

  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/booking_records.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    let res = JSON.parse(this.responseText);
    if (res.success) {
      alert('success', res.message);
      get_bookings(document.getElementById('search_input').value);
    } else {
      alert('error', res.message);
    }
  };

  xhr.send('release_booking&booking_id=' + booking_id);
}

function get_bookings(search = "", page = 1) {
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/booking_records.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
  xhr.onload = function () {
    let data = JSON.parse(this.responseText);
    document.getElementById("table-data").innerHTML = data.table_data;
    document.getElementById("pagination").innerHTML = data.pagination;
  };
  xhr.send("get_bookings&search=" + search + "&page=" + page);
}

function change_page(page){
  get_bookings(document.getElementById('search_input').value,page);
}

function download(id){
  window.location.href = 'generate_pdf.php?gen_pdf&id='+id;
}


window.onload = function(){
  get_bookings();
}