<?php require __DIR__ . '/auth-admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Enrollments - Senesa Child Center</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

  <style>
    /* ===== HARD RESET to prevent any page-wide zoom/scale ===== */
    html, body {
      zoom: 1 !important;               /* Chrome/Edge */
      -ms-zoom: 1 !important;           /* Legacy Edge/IE */
      -webkit-text-size-adjust: 100% !important;
    }
    html, body, .main-content, .content,
    #app, #root {
      transform: none !important;       /* kill accidental transform scale */
      transform-origin: top left !important;
    }

    /* ===== Shared scale (match Teachers) ===== */
    :root{
      --cell-font:13.5px;   /* desktop */
      --cell-pad-y:10px;
      --cell-pad-x:10px;
    }

    .content{padding:30px}
    .page-head{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;margin-bottom:16px}
    .filters{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
    .search{padding:10px 12px;border:1px solid #ddd;border-radius:8px;min-width:240px}
    .select{padding:10px 12px;border:1px solid #ddd;border-radius:8px}
    .btn-primary{background:#ff6b6b;color:#fff;border:none;padding:10px 14px;border-radius:8px;cursor:pointer}
    .btn-ghost{background:#fff;border:1px solid #ddd;color:#333;padding:10px 14px;border-radius:8px;cursor:pointer}

    .btn-outline{
      display:inline-flex;align-items:center;gap:8px;
      padding:8px 14px;border:1px solid #ff6b6b;color:#ff6b6b;border-radius:999px;background:#fff;font-weight:600
    }
    .btn-outline:hover{background:#ff6b6b;color:#fff}

    /* ===== Layout sanity (same as Teachers) ===== */
    .sidebar{ width:250px; }                     /* matches dashboard.css */
    .main-content{
      margin-left:250px;                         /* sit beside sidebar */
      max-width:none;                            /* no narrowing */
    }

    /* ===== Table (mirrors Teachers) ===== */
    .table-wrap{ width:100%; overflow-x:hidden; -webkit-overflow-scrolling:touch; } /* no horizontal scroll */
    table.enroll{
      width:100%;
      border-collapse:separate;
      border-spacing:0;
      background:#fff;
      box-shadow:0 2px 6px rgba(0,0,0,.08);
      border-radius:12px;
      overflow:hidden;
    }
    table.enroll thead{ background:#f5f5f5; }
    table.enroll th, table.enroll td{
      padding:var(--cell-pad-y) var(--cell-pad-x);
      border-bottom:1px solid #eee;
      text-align:left;
      font-size:var(--cell-font);
      vertical-align:top;
      word-break:break-word;
      overflow-wrap:anywhere;
    }
    table.enroll th{ white-space:nowrap; word-break:normal; }

    /* width hints for balance */
    table.enroll th:nth-child(1){width:36px;}     /* ID */
    table.enroll th:nth-child(6){width:110px;}    /* Start */
    table.enroll th:nth-child(7){width:70px;}     /* Days */
    table.enroll th:nth-child(8){width:110px;}    /* Schedule */
    table.enroll th:nth-child(9){width:100px;}    /* Status */
    table.enroll th:nth-child(10){width:120px;}   /* Submitted */
    table.enroll th:nth-child(11){width:220px;}   /* Actions */

    .badge{padding:6px 10px;border-radius:999px;font-size:12px;font-weight:600;display:inline-block}
    .st-pending{background:#fff7ed;color:#9a3412}
    .st-approved{background:#e8f5e9;color:#2e7d32}
    .st-rejected{background:#fee2e2;color:#991b1b}

    .row-actions{
      display:grid;
      grid-template-columns:repeat(2,minmax(0,1fr));
      gap:6px;
      align-items:start;
    }
    .btn-sm{
      padding:6px 10px;border:none;border-radius:6px;cursor:pointer;font-size:12px;
      width:100%; display:inline-flex; align-items:center; justify-content:center; gap:6px;
    }
    .btn-view{background:#64748b;color:#fff}
    .btn-edit{background:#4CAF50;color:#fff}
    .btn-approve{background:#22c55e;color:#fff}
    .btn-reject{background:#ef4444;color:#fff}

    .empty{padding:30px;color:#777;text-align:center}

    /* ===== Same medium-screen downscale as Teachers ===== */
    @media (max-width:1280px){
      :root{ --cell-font:12.5px; --cell-pad-y:8px; --cell-pad-x:8px; }
      table.enroll th:nth-child(11){width:200px;}
    }

    /* ===== Mobile card layout (kept) ===== */
    @media (max-width:720px){
      table.enroll,
      table.enroll thead,
      table.enroll tbody,
      table.enroll th,
      table.enroll td,
      table.enroll tr { display:block; width:100%; }

      table.enroll thead { display:none; }

      table.enroll tr{
        background:#fff;
        border:1px solid #eee;
        border-radius:12px;
        margin-bottom:12px;
        box-shadow:0 2px 6px rgba(0,0,0,.06);
        overflow:hidden;
      }
      table.enroll td{
        border-bottom:1px solid #f1f1f1;
        padding:12px 14px;
        display:flex !important;
        align-items:flex-start;
        justify-content:space-between;
        gap:10px;
      }
      table.enroll td:last-child{ border-bottom:none; }

      table.enroll td::before{
        content: attr(data-col);
        font-weight:600;
        color:#6b7280;
        flex:0 0 45%;
        max-width:45%;
      }
      table.enroll td > *{
        flex:1 1 auto;
        text-align:right;
        word-break:break-word;
      }
      .row-actions{ width:100%; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; }
      .btn-sm{ font-size:12px; }
    }

    /* ==== Modern modal look & form controls ==== */
.modal{
  display:none; position:fixed; inset:0; z-index:1100;
  background:rgba(17,24,39,.5);           /* dark translucent overlay */
  align-items:center; justify-content:center;
  padding:24px;
}
.modal .modal-content{
  width:100%; max-width:700px;
  background:#fff; border-radius:16px;
  box-shadow:0 20px 60px rgba(0,0,0,.20);
  padding:24px 24px 18px;
  position:relative;
  animation:pop .15s ease-out;
}
@keyframes pop{ from{transform:translateY(6px); opacity:.6} to{transform:none; opacity:1} }

.modal .close{
  position:absolute; right:14px; top:12px;
  font-size:24px; font-weight:700; color:#94a3b8; cursor:pointer;
}
.modal .close:hover{ color:#64748b; }

.modal h3{
  margin:0 0 14px; color:#111827; font-size:20px; font-weight:700;
}

/* two-column responsive grid for fields */
.form-grid{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap:14px 18px;
}
@media (max-width:680px){ .form-grid{ grid-template-columns: 1fr; } }
.span-2{ grid-column:1/-1; }

/* tidy form groups */
.form-group{ display:flex; flex-direction:column; }
.form-group label{
  font-weight:600; color:#374151; margin-bottom:6px; font-size:14px;
}
.form-group input,
.form-group select,
.form-group textarea{
  width:100%;
  border:1px solid #e5e7eb; border-radius:10px;
  padding:11px 12px; font-size:14px; outline:none; background:#fff;
}
.form-group textarea{ min-height:100px; resize:vertical; }
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus{
  border-color:#ff8a8a; box-shadow:0 0 0 4px rgba(255,107,107,.15);
}

/* modal footer buttons */
.modal-buttons{
  margin-top:18px; display:flex; gap:10px; justify-content:flex-end;
}
.modal-buttons .btn-primary{
  background:#ff6b6b; border:none; color:#fff; padding:10px 16px;
  border-radius:10px; font-weight:600; cursor:pointer;
}
.modal-buttons .btn-primary:hover{ filter:brightness(.95); }
.modal-buttons .btn-ghost{
  background:#fff; border:1px solid #e5e7eb; color:#374151;
  padding:10px 16px; border-radius:10px; cursor:pointer;
}


    /* ✅ Do NOT reserve space for the sidebar on mobile */
    @media (max-width: 900px){
      .main-content{
        margin-left: 0 !important;
        width: 100% !important;
      }
      .content{ padding:16px !important; }      /* softer padding on small screens */
    }

    /* ✅ When the sidebar is toggled as an overlay, don't push the content */
    .sidebar.active + .main-content{
      margin-left: 0 !important;
    }

    /* (optional) keep the sidebar off-canvas on mobile */
    @media (max-width: 900px){
      .sidebar{
        position: fixed;
        top: 0; left: -250px; width: 250px; height: 100%;
        transition: left .2s ease;
        z-index: 1000; /* sits above content when opened */
      }
      .sidebar.active{ left: 0; }
    }

  </style>
</head>

<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2><i class="fas fa-school"></i> Senesa Child Center - Admin</h2>
    <nav>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="students.php" class="active"><i class="fas fa-user-graduate"></i> Enrollments</a>
      <a href="teachers.php"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
      <a href="programs.php"><i class="fas fa-book-open"></i> Programs</a>
      <a href="gallery.php"><i class="fas fa-images"></i> Gallery</a>
      <a href="books.php" ><i class="fas fa-book"></i> Books</a>
      <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
      <a href="users.php"><i class="fas fa-users-cog"></i> Users</a>
      <a href="orders.php"><i class="fas fa-receipt"></i> Orders</a>
      <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    </nav>
  </aside>

  <!-- Main -->
  <main class="main-content">
    <header class="topbar">
      <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
      <h1>Enrollments</h1>

      <div class="admin-info dropdown">
        <i class="fas fa-user-circle"></i> Admin <i class="fas fa-caret-down"></i>
        <ul class="dropdown-menu">
          <li><a href="profile.php">Profile</a></li>
          <li><a href="change-password.php">Change Password</a></li>
          <li><a href="#" id="logoutBtn">Logout</a></li>
        </ul>
      </div>

      <!-- logoutModal -->
      <div id="logoutModal" class="modal modal--confirm">
        <div class="modal-content">
          <span class="close">&times;</span>
          <h3>Confirm Logout</h3>
          <p>Are you sure you want to logout?</p>
          <div class="modal-buttons">
            <button id="confirmLogout" class="btn-danger">Yes, Logout</button>
            <button id="cancelLogout" class="btn-secondary">Cancel</button>
          </div>
        </div>
      </div>
    </header>

    <section class="content">
      <div style="margin-bottom:16px">
        <a href="dashboard.php" class="btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
      </div>

      <div class="page-head">
        <div>
          <h2 style="margin:0;color:#333">Enrollment Requests</h2>
          <p style="margin:4px 0 0;color:#6b7280;font-size:13px">Review, add, and manage child enrollments.</p>
        </div>
        <div class="filters">
          <input id="searchBox" class="search" placeholder="Search child / parent / plan" />
          <select id="statusFilter" class="select">
            <option value="">All Statuses</option>
            <option value="Pending">Pending</option>
            <option value="Approved">Approved</option>
            <option value="Rejected">Rejected</option>
          </select>
          <button id="addBtn" class="btn-primary"><i class="fas fa-plus"></i> Add Enrollment</button>
          <button id="importBtn" class="btn-ghost"><i class="fas fa-file-import"></i> Import JSON</button>
          <input id="importInput" type="file" accept="application/json" style="display:none" />
          <button id="exportCsvBtn" class="btn-ghost"><i class="fas fa-file-export"></i> Export CSV</button>
          <button id="exportPdfBtn" class="btn-ghost">
            <i class="fas fa-file-pdf"></i> Export PDF
          </button>
        </div>
      </div>

      <div class="table-wrap">
        <table class="enroll">
          <thead>
            <tr>
              <th>ID</th>
              <th>Child</th>
              <th>Parent</th>
              <th>Nursery Choice</th>
              <th>Selected Plan</th>
              <th>Start</th>
              <th>Days</th>
              <th>Schedule</th>
              <th>Status</th>
              <th>Submitted</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
      <div id="emptyState" class="empty" style="display:none;">No enrollments found.</div>
    </section>

    <footer style="text-align:center;padding:15px;margin-top:auto;background:#fff;box-shadow:0 -2px 5px rgba(0,0,0,.1);">
      &copy; 2025 Senesa Child Center. All rights reserved.
    </footer>
  </main>

  <!-- View Modal -->
  <div id="viewModal" class="modal">
    <div class="modal-content" style="max-width:520px">
      <span class="close" id="closeView">&times;</span>
      <h3>Enrollment Details</h3>
      <div id="viewBody" style="margin-top:10px;text-align:left;color:#444"></div>
      <div class="modal-buttons">
        <button class="btn-ghost" id="closeView2">Close</button>
      </div>
    </div>
  </div>

  <!-- Add/Edit Modal -->
<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close" id="closeEdit">&times;</span>
    <h3 id="editTitle">Add Enrollment</h3>

    <form id="enrollForm">
      <input type="hidden" id="enrollId" />

      <div class="form-grid">
        <div class="form-group">
          <label for="childFirst">Child First Name</label>
          <input id="childFirst" required />
        </div>
        <div class="form-group">
          <label for="childLast">Child Last Name</label>
          <input id="childLast" required />
        </div>

        <div class="form-group">
          <label for="parentFirst">Parent First Name</label>
          <input id="parentFirst" required />
        </div>
        <div class="form-group">
          <label for="parentLast">Parent Last Name</label>
          <input id="parentLast" required />
        </div>

        <div class="form-group">
          <label for="parentEmail">Parent Email</label>
          <input id="parentEmail" type="email" />
        </div>
        <div class="form-group">
          <label for="parentPhone">Parent Phone</label>
          <input id="parentPhone" />
        </div>

        <div class="form-group">
          <label for="choice">Nursery Choice</label>
          <select id="choice" required>
            <option value="Only Nursery">Only Nursery</option>
            <option value="Nursery + Daycare">Nursery + Daycare</option>
            <option value="Only a Daycare plan">Only a Daycare plan</option>
          </select>
        </div>
        <div class="form-group">
          <label for="plan">Selected Plan (if Daycare)</label>
          <input id="plan" placeholder="e.g., Full-Day Adventure" />
        </div>

        <div class="form-group">
          <label for="startDate">Start Date</label>
          <input id="startDate" type="date" />
        </div>
        <div class="form-group">
          <label for="daysPerWeek">Days/Week</label>
          <select id="daysPerWeek">
            <option value=""></option>
            <option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
          </select>
        </div>

        <div class="form-group">
          <label for="schedule">Schedule</label>
          <select id="schedule">
            <option>Full Day</option>
            <option>Half Day (AM)</option>
            <option>Half Day (PM)</option>
          </select>
        </div>
        <div class="form-group">
          <label for="status">Status</label>
          <select id="status" required>
            <option>Pending</option>
            <option>Approved</option>
            <option>Rejected</option>
          </select>
        </div>
      </div>

      <div class="modal-buttons">
        <button class="btn-ghost" type="button" id="cancelEdit">Cancel</button>
        <button class="btn-primary" type="submit">Save</button>
      </div>
    </form>
  </div>
</div>

  <!-- libs for PDF -->
  <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.4/dist/jspdf.plugin.autotable.min.js"></script>

  <script>
/* ============================
   Shared sidebar/dropdown/logout
============================ */
const adminInfo    = document.querySelector('.admin-info');
const dropdownMenu = adminInfo?.querySelector('.dropdown-menu');
const logoutBtn    = document.getElementById('logoutBtn');
const logoutModal  = document.getElementById('logoutModal');
const closeLogout  = logoutModal?.querySelector('.close');
const confirmLogout= document.getElementById('confirmLogout');
const cancelLogout = document.getElementById('cancelLogout');
const toggleBtn    = document.querySelector('.sidebar-toggle');
const sidebar      = document.querySelector('.sidebar');

adminInfo?.addEventListener('click', (e)=>{ e.stopPropagation(); dropdownMenu?.classList.toggle('show'); });
document.addEventListener('click', ()=> dropdownMenu?.classList.remove('show'));
logoutBtn?.addEventListener('click', (e)=>{
  e.preventDefault();
  if (!logoutModal) return;
  logoutModal.style.display = 'flex';
  dropdownMenu?.classList.remove('show');
  document.body.classList.add('modal-open');
});
function closeLogoutModal(){
  if (!logoutModal) return;
  logoutModal.style.display = 'none';
  document.body.classList.remove('modal-open');
}
closeLogout?.addEventListener('click', closeLogoutModal);
cancelLogout?.addEventListener('click', closeLogoutModal);
window.addEventListener('click', (e)=>{ if(e.target === logoutModal) closeLogoutModal(); });
document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape' && logoutModal?.style.display === 'flex') closeLogoutModal(); });
confirmLogout?.addEventListener('click', ()=>{ document.body.classList.remove('modal-open'); window.location.href = 'admin-login.html'; });
toggleBtn?.addEventListener('click', ()=> sidebar?.classList.toggle('active'));


/* ============================
   Enrollments logic (DB-backed)
============================ */
const API_BASE = '../api/enrollment.php';
const API_LIST   = `${API_BASE}?action=list`;
const API_CREATE = `${API_BASE}?action=create`;
const API_STATUS = `${API_BASE}?action=update_status`;

let data = []; // current table rows from DB

const tbody        = document.getElementById('tbody');
const emptyState   = document.getElementById('emptyState');
const searchBox    = document.getElementById('searchBox');
const statusFilter = document.getElementById('statusFilter');

function badge(status){
  const cls = status==='Approved'?'st-approved':(status==='Rejected'?'st-rejected':'st-pending');
  return `<span class="badge ${cls}">${status}</span>`;
}

async function loadRows(){
  const q  = encodeURIComponent(searchBox.value || '');
  const st = encodeURIComponent(statusFilter.value || '');
  const url = `${API_LIST}&q=${q}&status=${st}`;
  try{
    const res = await fetch(url);
    const out = await res.json();
    if(!out.ok) throw new Error(out.error || 'Failed to load');
    data = out.rows || [];
    render();
  }catch(err){
    console.error(err);
    data = [];
    render();
    alert('Could not load enrollments from the server.');
  }
}

function render(){
  tbody.innerHTML = '';
  if(!data.length){
    emptyState.style.display='block';
    return;
  }
  emptyState.style.display='none';

  data.forEach(r=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td data-col="ID"><span>${r.id}</span></td>
      <td data-col="Child"><span>${r.child_first || ''} ${r.child_last || ''}</span></td>
      <td data-col="Parent"><span>${r.parent_first || ''} ${r.parent_last || ''}</span></td>
      <td data-col="Nursery Choice"><span>${r.nursery_choice || '—'}</span></td>
      <td data-col="Selected Plan"><span>${r.plan || '—'}</span></td>
      <td data-col="Start"><span>${r.start_date || '—'}</span></td>
      <td data-col="Days"><span>${r.days_per_week || '—'}</span></td>
      <td data-col="Schedule"><span>${r.schedule || '—'}</span></td>
      <td data-col="Status"><span>${badge(r.status || 'Pending')}</span></td>
      <td data-col="Submitted"><span>${r.submitted_at || '—'}</span></td>
      <td data-col="Actions">
        <div class="row-actions">
          <button class="btn-sm btn-view" data-id="${r.id}"><i class="fas fa-eye"></i> View</button>
          <button class="btn-sm btn-edit" data-id="${r.id}" title="Edit details"><i class="fas fa-edit"></i> Edit</button>
          <button class="btn-sm btn-approve" data-id="${r.id}" data-act="Approved"><i class="fas fa-check"></i> Approve</button>
          <button class="btn-sm btn-reject"  data-id="${r.id}" data-act="Rejected"><i class="fas fa-times"></i> Reject</button>
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// first load + filter bindings
loadRows();
searchBox.addEventListener('input',  loadRows);
statusFilter.addEventListener('change', loadRows);

// View modal — DB columns (flat)
const viewModal = document.getElementById('viewModal');
const viewBody  = document.getElementById('viewBody');
const closeView = document.getElementById('closeView');
const closeView2= document.getElementById('closeView2');
[closeView, closeView2].forEach(el=> el.addEventListener('click', ()=> viewModal.style.display='none'));
window.addEventListener('click', e=> { if(e.target===viewModal) viewModal.style.display='none'; });

function kv(label, val){ return `<p><strong>${label}:</strong> ${val || '—'}</p>`; }

tbody.addEventListener('click', async (e)=>{
  const btn = e.target.closest('button'); if(!btn) return;
  const id  = Number(btn.dataset.id);
  const row = data.find(x=>x.id===id);
  if(!row) return;

  if(btn.classList.contains('btn-view')){
    viewBody.innerHTML = `
      <h4>Parent</h4>
      ${kv('Name', `${row.parent_first || ''} ${row.parent_last || ''}`)}
      ${kv('Email', row.parent_email)}
      ${kv('Phone', row.parent_phone)}
      ${kv('Address', `${row.address || ''}${row.city ? ', ' + row.city : ''}`)}

      <h4>Child</h4>
      ${kv('Name', `${row.child_first || ''} ${row.child_last || ''}`)}
      ${kv('DOB', row.dob)}
      ${kv('Gender', row.gender)}
      ${kv('Allergies', row.allergies)}
      ${kv('Special Needs', row.special_needs)}

      <h4>Plan</h4>
      ${kv('Choice', row.nursery_choice)}
      ${kv('Selected Plan', row.plan)}
      ${kv('Start', row.start_date)}
      ${kv('Days/Week', row.days_per_week)}
      ${kv('Schedule', row.schedule)}
      ${kv('Extras', row.extras)}

      <h4>Emergency & Consents</h4>
      ${kv('Emergency', `${row.emergency_name || ''}${row.emergency_phone ? ' ('+row.emergency_phone+')' : ''}`)}
      ${kv('Physician', `${row.physician || ''}${row.physician_phone ? ' ('+row.physician_phone+')' : ''}`)}
      ${kv('Authorized Pickup', row.authorized_pickup)}
      ${kv('Notes', row.notes)}
      ${kv('Consents', `Medical: ${row.consent_medical ? 'Yes' : 'No'}, Policy: ${row.consent_policy ? 'Yes' : 'No'}, Photo: ${row.consent_photo ? 'Yes' : 'No'}`)}
    `;
    viewModal.style.display='flex';
  }
  else if(btn.classList.contains('btn-edit')){
    // Pre-fill modal with DB row values (for future update wiring)
    openEditWithRow(row);
  }
  else if(btn.dataset.act === 'Approved' || btn.dataset.act === 'Rejected'){
    try{
      const fd = new FormData();
      fd.append('id', id);
      fd.append('status', btn.dataset.act);
      const res = await fetch(API_STATUS, { method:'POST', body: fd });
      const out = await res.json();
      if(!out.ok) throw new Error(out.error || 'Update failed');
      await loadRows(); // refresh with filters applied
    }catch(err){
      console.error(err);
      alert('Could not update status.');
    }
  }
});


/* ============================
   Add/Edit Modal wiring
============================ */
const editModal   = document.getElementById('editModal');
const closeEdit   = document.getElementById('closeEdit');
const cancelEdit  = document.getElementById('cancelEdit');
const editTitle   = document.getElementById('editTitle');
const enrollForm  = document.getElementById('enrollForm');
const enrollId    = document.getElementById('enrollId');

const childFirst  = document.getElementById('childFirst');
const childLast   = document.getElementById('childLast');
const parentFirst = document.getElementById('parentFirst');
const parentLast  = document.getElementById('parentLast');
const parentEmail = document.getElementById('parentEmail');
const parentPhone = document.getElementById('parentPhone');
const choice      = document.getElementById('choice');
const plan        = document.getElementById('plan');
const startDateEl = document.getElementById('startDate');
const daysPerWeek = document.getElementById('daysPerWeek');
const scheduleEl  = document.getElementById('schedule');
const statusEl    = document.getElementById('status');

document.getElementById('addBtn').addEventListener('click', openAdd);
function openAdd(){
  editTitle.textContent='Add Enrollment';
  enrollId.value='';
  childFirst.value=''; childLast.value='';
  parentFirst.value=''; parentLast.value='';
  parentEmail.value=''; parentPhone.value='';
  choice.value='Only Nursery';
  plan.value='';
  startDateEl.value='';
  daysPerWeek.value='';
  scheduleEl.value='Full Day';
  statusEl.value='Pending';
  editModal.style.display='flex';
  document.body.classList.add('modal-open');
}

function openEditWithRow(row){
  editTitle.textContent='Edit Enrollment';
  enrollId.value = row.id;
  childFirst.value  = row.child_first   || '';
  childLast.value   = row.child_last    || '';
  parentFirst.value = row.parent_first  || '';
  parentLast.value  = row.parent_last   || '';
  parentEmail.value = row.parent_email  || '';
  parentPhone.value = row.parent_phone  || '';
  choice.value      = row.nursery_choice || 'Only Nursery';
  plan.value        = row.plan || (row.nursery_choice==='Only Nursery' ? 'Only Nursery' : '');
  startDateEl.value = row.start_date || '';
  daysPerWeek.value = row.days_per_week || '';
  scheduleEl.value  = row.schedule || 'Full Day';
  statusEl.value    = row.status || 'Pending';
  editModal.style.display='flex';
  document.body.classList.add('modal-open');
}

function closeEditModal(){
  editModal.style.display='none';
  document.body.classList.remove('modal-open');
}
closeEdit.addEventListener('click', closeEditModal);
cancelEdit.addEventListener('click', closeEditModal);
window.addEventListener('click', e=> { if(e.target===editModal) closeEditModal(); });

/* Build a payload in the SAME shape the public form sends */
function buildCreatePayloadFromAdminModal(){
  const choiceVal = choice.value;

  let nurseryPlanValue, selectedPlan;
  if (choiceVal === 'Only Nursery') {
    nurseryPlanValue = 'only-nursery';
    selectedPlan = 'Only Nursery';              // display string in table
  } else if (choiceVal === 'Nursery + Daycare') {
    nurseryPlanValue = 'nursery-and-daycare';
    selectedPlan = (plan.value.trim() || '—');
  } else {
    // "Only a Daycare plan"
    nurseryPlanValue = 'only-daycare';
    selectedPlan = (plan.value.trim() || '—');
  }

  // Optional: require a plan when daycare is involved
  if ((nurseryPlanValue === 'nursery-and-daycare' || nurseryPlanValue === 'only-daycare')
      && !plan.value.trim()) {
    alert('Please enter the daycare plan.');
    return null;
  }

  return {
    parentFirst : parentFirst.value.trim(),
    parentLast  : parentLast.value.trim(),
    parentEmail : parentEmail.value.trim(),
    parentPhone : parentPhone.value.trim(),
    address     : '',
    city        : '',

    childFirst  : childFirst.value.trim(),
    childLast   : childLast.value.trim(),
    dob         : '',
    age         : '',
    gender      : '',
    allergies   : '',
    specialNeeds: '',

    'nursery-plan': nurseryPlanValue,
    plan        : selectedPlan,
    startDate   : startDateEl.value,
    daysPerWeek : daysPerWeek.value,
    schedule    : scheduleEl.value,
    extras      : [],

    physician       : '',
    physicianPhone  : '',
    authorizedPickup: '',
    notes           : '',

    consentMedical: false,
    consentPolicy : false,
    consentPhoto  : false,

    timestamp: new Date().toISOString()
  };
}


// Save (Create) from admin modal
enrollForm.addEventListener('submit', async (e)=>{
  e.preventDefault();

  const id = enrollId.value ? Number(enrollId.value) : null;

  if (!id) {
    // CREATE
    const payload = buildCreatePayloadFromAdminModal();
    if (!payload) return; // stop if validation failed

    try{
      const res = await fetch(API_CREATE, {
        method: 'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });
      const out = await res.json();
      if(!out.ok) throw new Error(out.error || 'Create failed');

      // If admin selected a non-pending status, update it now
      const desiredStatus = statusEl.value || 'Pending';
      if (desiredStatus !== 'Pending') {
        const fd = new FormData();
        fd.append('id', out.id);
        fd.append('status', desiredStatus);
        const res2 = await fetch(API_STATUS, { method:'POST', body: fd });
        const out2 = await res2.json();
        if(!out2.ok) throw new Error(out2.error || 'Status update failed');
      }

      await loadRows();
      closeEditModal();
    }catch(err){
      console.error(err);
      alert('Could not save enrollment.');
    }
  } else {
    // EDIT (details) — needs an API to update fields (not just status).
    alert('Editing enrollment details is not connected to the database yet. If you want this, I can add an action=update to api/enrollment.php and wire it here.');
  }
});


/* ============================
   Import (JSON) -> DB
============================ */
const importBtn   = document.getElementById('importBtn');
const importInput = document.getElementById('importInput');
importBtn.addEventListener('click', ()=> importInput.click());
importInput.addEventListener('change', async ()=>{
  const file = importInput.files[0]; if(!file) return;
  try{
    const text = await file.text();
    const payload = JSON.parse(text); // same format as public form
    const res = await fetch(API_CREATE, {
      method: 'POST',
      headers: {'Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    const out = await res.json();
    if(!out.ok) throw new Error(out.error || 'Import failed');
    await loadRows();
    alert('Application imported and saved to DB.');
  }catch(err){
    console.error(err); alert('Invalid JSON or server error.');
  }finally{
    importInput.value = '';
  }
});

/* ============================
   Export CSV (from current data)
============================ */
document.getElementById('exportCsvBtn').addEventListener('click', ()=>{
  const rows = [[
    'ID','Child First','Child Last','Parent First','Parent Last','Email','Phone',
    'Nursery Choice','Selected Plan','Start Date','Days/Week','Schedule','Status','Submitted'
  ]].concat(
    data.map(r=>[
      r.id, r.child_first || '', r.child_last || '', r.parent_first || '', r.parent_last || '', r.parent_email || '', r.parent_phone || '',
      r.nursery_choice || '', r.plan || '', r.start_date || '', r.days_per_week || '', r.schedule || '', r.status || '', r.submitted_at || ''
    ])
  );
  const csv = rows.map(r=>r.map(x=>`"${String(x??'').replace(/"/g,'""')}"`).join(',')).join('\n');
  const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'enrollments.csv';
  a.click();
  URL.revokeObjectURL(a.href);
});


/* ============================
   Export PDF (detailed report) — fixed widths
============================ */
(function(){
  const pdfBtn = document.getElementById('exportPdfBtn');
  if(!pdfBtn) return;

  const pad2 = n => String(n).padStart(2,'0');
  const fmtDate = (dt) => (!dt || isNaN(+dt)) ? '—'
                     : `${dt.getFullYear()}-${pad2(dt.getMonth()+1)}-${pad2(dt.getDate())}`;
  const parseYMD = (s) => {
    if(!s) return null;
    const m = String(s).trim().match(/^(\d{4})-(\d{2})-(\d{2})/);
    if(!m) return null;
    const d = new Date(+m[1], +m[2]-1, +m[3]);
    return isNaN(+d) ? null : d;
  };
  const nowStamp = () => {
    const d = new Date();
    return `${fmtDate(d)} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
  };

  function buildSummary(rows){
    const total = rows.length;
    const byStatus = rows.reduce((acc,r)=>{
      const k = (r.status || 'Pending').toLowerCase();
      acc[k] = (acc[k]||0) + 1;
      return acc;
    }, {});
    const daysVals = rows.map(r => Number(r.days_per_week || 0)).filter(v => Number.isFinite(v) && v>0);
    const avgDays = daysVals.length ? (daysVals.reduce((a,b)=>a+b,0)/daysVals.length) : 0;

    const startDates = rows.map(r => parseYMD(r.start_date)).filter(Boolean);
    const minD = startDates.length ? new Date(Math.min(...startDates)) : null;
    const maxD = startDates.length ? new Date(Math.max(...startDates)) : null;

    return {
      total,
      pending:   byStatus['pending']  || 0,
      approved:  byStatus['approved'] || 0,
      rejected:  byStatus['rejected'] || 0,
      avgDays:   avgDays.toFixed(2),
      fromDate:  fmtDate(minD),
      toDate:    fmtDate(maxD)
    };
  }

  function addFooter(doc){
    const pageCount = doc.getNumberOfPages();
    for (let i=1;i<=pageCount;i++){
      doc.setPage(i);
      const w = doc.internal.pageSize.getWidth();
      const h = doc.internal.pageSize.getHeight();
      doc.setFontSize(9);
      doc.text(`Page ${i} of ${pageCount}`, w - 70, h - 16);
    }
  }

  pdfBtn.addEventListener('click', ()=>{
    const rows = Array.isArray(data) ? data : [];
    const summary = buildSummary(rows);
    const { jsPDF } = window.jspdf;

    // Landscape + margins
    const doc = new jsPDF({ unit:'pt', format:'a4', orientation:'landscape' });
    const margin = { left: 40, right: 40, top: 60, bottom: 30 };
    const pageW = doc.internal.pageSize.getWidth();

    // Header
    doc.setFontSize(16);
    doc.text('Enrollment Summary Report', margin.left, 34);
    doc.setFontSize(10);
    doc.text(`Generated: ${nowStamp()}`, margin.left, 50);

    const activeStatus = document.getElementById('statusFilter')?.value || 'All';
    const searchTerm   = document.getElementById('searchBox')?.value || '—';
    const rightX = pageW - margin.right;

    // Filters (grey, right-aligned)
    doc.setFontSize(10);
    doc.setTextColor(120);
    doc.text(`Filters: Status = ${activeStatus}, Search = ${searchTerm}`, rightX, 50, { align: 'right' });

    // Org line (darker, right-aligned, italic)
    doc.setTextColor(0);
    doc.setFont(undefined, 'italic');
    doc.text('Senesa Child Center - Admin', rightX, 64, { align: 'right' });
    doc.setFont(undefined, 'normal'); // reset

    doc.text('Prepared by: Admin', margin.left, 64);


    // Summary table
    const summaryRows = [
      ['Total Records', String(summary.total)],
      ['Pending',      String(summary.pending)],
      ['Approved',     String(summary.approved)],
      ['Rejected',     String(summary.rejected)],
      ['Average Days/Week', String(summary.avgDays)],
      ['Start Date Range', `${summary.fromDate} to ${summary.toDate}`]
    ];
    doc.autoTable({
      startY: 80,
      theme: 'plain',
      head: [['Summary', 'Value']],
      body: summaryRows,
      styles: { fontSize: 10, cellPadding: 5 },
      headStyles: { fillColor: [240,240,240] },
      margin
    });

    
    // Detailed table — widths tuned to fit inside A4 landscape
    const head = [['ID','Child','Parent','Nursery','Plan','Start','Days','Sched','Status','Submitted']];
    const body = rows.map(r => ([
      r.id,
      `${r.child_first||''} ${r.child_last||''}`.trim(),
      `${r.parent_first||''} ${r.parent_last||''}`.trim(),
      r.nursery_choice || '—',
      r.plan || '—',
      r.start_date || '—',
      String(r.days_per_week || '—'),
      r.schedule || '—',
      r.status || 'Pending',
      r.submitted_at || '—'
    ]));

    doc.autoTable({
      head,
      body,
      startY: doc.lastAutoTable.finalY + 14,
      margin,                                   // keep your margin object
      tableWidth: doc.internal.pageSize.getWidth() - margin.left - margin.right,
      styles: { fontSize: 9, cellPadding: 3, overflow: 'linebreak', valign: 'top' },
      headStyles: { fillColor: [255, 107, 107], textColor: [255, 255, 255], fontStyle: 'bold' },
      alternateRowStyles: { fillColor: [248,248,248] },
      columnStyles: {
        0:{ cellWidth: 30,  halign:'center' },   // ID
        1:{ cellWidth: 105 },                    // Child
        2:{ cellWidth: 100 },                    // Parent
        3:{ cellWidth: 90 },                     // Nursery
        4:{ cellWidth: 110 },                    // Plan
        5:{ cellWidth: 75,  halign:'center' },   // Start
        6:{ cellWidth: 36,  halign:'center' },   // Days
        7:{ cellWidth: 70 },                     // Sched
        8:{ cellWidth: 55,  halign:'center' },   // Status
        9:{ cellWidth: 90 }                      // Submitted
      },
      didParseCell: (d) => {
        if (d.section === 'body' && d.column.index === 8) {
          const v = String(d.cell.raw || '').toLowerCase();
          if (v === 'approved'){ d.cell.styles.textColor = [34,197,94]; d.cell.styles.fontStyle='bold'; }
          else if (v === 'rejected'){ d.cell.styles.textColor = [239,68,68]; d.cell.styles.fontStyle='bold'; }
          else { d.cell.styles.textColor = [154,52,18]; d.cell.styles.fontStyle='bold'; } // pending
        }
      },
      didDrawPage: (data) => {
        doc.setFontSize(12);
        doc.text('Detailed Records', margin.left, data.settings.startY - 8);
      }
    });


        doc.autoTable({
        startY: doc.lastAutoTable.finalY + 12,
        theme: 'plain',
        head: [['Totals','Value']],
        body: [
          ['Total Enrollments', String(summary.total)],
          ['Pending', String(summary.pending)],
          ['Approved', String(summary.approved)],
          ['Rejected', String(summary.rejected)]
        ],
        styles:{ fontSize:10, cellPadding:4 },
        headStyles:{ fillColor:[240,240,240] },
        margin
      });

        addFooter(doc);
        doc.save(`enrollments_${new Date().toISOString().slice(0,10)}.pdf`);
      });
    })();


</script>

</body>
</html>
