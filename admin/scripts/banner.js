/* admin/scripts/banner.js */

let bannerData = {};

// ── Load banner data ────────────────────────────────────────
function loadBanner() {
  const fd = new FormData();
  fd.append('get_banner', '');
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/banner_crud.php', true);
  xhr.onload = function () {
    try {
      bannerData = JSON.parse(this.responseText);
      renderBannerData(bannerData);
    } catch (e) { console.error('Banner load failed', e); }
  };
  xhr.send(fd);
}

function renderBannerData(d) {
  const active = parseInt(d.is_active) === 1;

  // Stats
  const ss = document.getElementById('stat-status');
  ss.textContent = active ? 'Active' : 'Inactive';
  ss.style.color = active ? '#2d9c95' : '#dc3545';
  document.getElementById('stat-points').textContent = d.loyalty_points != null ? d.loyalty_points : '—';
  document.getElementById('stat-claims').textContent = d.total_claims ?? 0;

  // Toggle
  document.getElementById('banner-toggle').checked = active;

  // Settings table
  document.getElementById('preview-title').textContent       = d.title       || '—';
  document.getElementById('preview-subtitle').textContent    = d.subtitle    || '—';
  document.getElementById('preview-description').textContent = d.description ? (d.description.substring(0, 80) + (d.description.length > 80 ? '…' : '')) : '—';
  document.getElementById('preview-points').textContent      = d.loyalty_points != null ? d.loyalty_points + ' pts' : '—';
  document.getElementById('preview-expiry').textContent      = d.expiry_date || 'No expiry';

  // Background image
  if (d.bg_image) {
    const base = window.location.origin + window.location.pathname.replace(/\/[^/]*$/, '/');
    document.getElementById('preview-img').src = base + '../images/banner/' + d.bg_image;
    document.getElementById('preview-img-container').classList.remove('d-none');
    document.getElementById('preview-no-img').classList.add('d-none');
    document.getElementById('preview-img-info').classList.remove('d-none');
  } else {
    document.getElementById('preview-img-container').classList.add('d-none');
    document.getElementById('preview-no-img').classList.remove('d-none');
    document.getElementById('preview-img-info').classList.add('d-none');
  }
}

// ── Toggle banner active / inactive ────────────────────────
document.getElementById('banner-toggle').addEventListener('change', function () {
  const status = this.checked ? 1 : 0;
  const fd = new FormData();
  fd.append('toggle_banner', '');
  fd.append('status', status);
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/banner_crud.php', true);
  xhr.onload = function () {
    if (this.responseText === 'success') {
      alert('success', status === 1 ? 'Banner is now active!' : 'Banner has been hidden.');
      loadBanner();
    } else {
      alert('error', 'Failed to update banner status.');
    }
  };
  xhr.send(fd);
});

// ── Save edit form ──────────────────────────────────────────
document.getElementById('edit_banner_form').addEventListener('submit', function (e) {
  e.preventDefault();

  const title = this.elements['title'].value.trim();
  const pts   = parseInt(this.elements['loyalty_points'].value);
  if (!title)           { alert('error', 'Title is required!');           return; }
  if (isNaN(pts) || pts < 1) { alert('error', 'Points must be at least 1!'); return; }

  const fd = new FormData(this);
  fd.append('upd_banner', '');

  const modal = bootstrap.Modal.getInstance(document.getElementById('edit-banner-modal'));
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/banner_crud.php', true);
  xhr.onload = function () {
    const res = this.responseText.trim();
    if (res === 'success') {
      alert('success', 'Banner updated successfully!');
      if (modal) modal.hide();
      loadBanner();
    } else if (res === 'inv_img')    { alert('error', 'Invalid image format! Use JPG, PNG, or WEBP.');
    } else if (res === 'inv_size')   { alert('error', 'Image too large! Max 2 MB.');
    } else if (res === 'upd_failed') { alert('error', 'Image upload failed!');
    } else { alert('error', 'Failed to update banner.'); }
  };
  xhr.send(fd);
});

// ── Populate form when modal opens ─────────────────────────
document.getElementById('edit-banner-modal').addEventListener('show.bs.modal', function () {
  const d = bannerData;
  document.getElementById('edit_title').value       = d.title          || '';
  document.getElementById('edit_subtitle').value    = d.subtitle       || '';
  document.getElementById('edit_description').value = d.description    || '';
  document.getElementById('edit_points').value      = d.loyalty_points != null ? d.loyalty_points : 500;
  document.getElementById('edit_expiry_date').value = d.expiry_date    || '';
  document.getElementById('edit_bg_image').value    = '';
});

// ── Remove background image ─────────────────────────────────
document.getElementById('remove-bg-btn').addEventListener('click', function () {
  if (!confirm('Remove the background image? The banner will use curated hotel photos instead.')) return;
  const fd = new FormData();
  fd.append('remove_bg_image', '');
  const xhr = new XMLHttpRequest();
  xhr.open('POST', 'ajax/banner_crud.php', true);
  xhr.onload = function () {
    if (this.responseText.trim() === 'success') {
      alert('success', 'Background image removed.');
      loadBanner();
    } else { alert('error', 'Failed to remove image.'); }
  };
  xhr.send(fd);
});

// ── Init ────────────────────────────────────────────────────
loadBanner();
