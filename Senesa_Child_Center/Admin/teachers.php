<?php require __DIR__ . '/auth-admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Teachers - Senesa Child Center</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    :root {
      --cell-font: 13.5px;
      --cell-pad-y: 10px;
      --cell-pad-x: 10px;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }

    .modal-content {
      display: flex;
      flex-direction: column;
      gap: 12px;
      padding: 20px;
      max-width: 640px;
      width: 100%;
      border-radius: 8px;
      background-color: #fff;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .form-group label {
      font-size: 14px;
      color: #333;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ddd;
      font-size: 14px;
    }

    textarea {
      resize: vertical;
      height: 80px;
    }

    .modal-buttons {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
    }

    .modal-buttons button {
      padding: 10px 14px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
    }

    .photo-uploader {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .photo-uploader input {
      width: 70%;
    }

    .photo-uploader img {
      width: 72px;
      height: 72px;
      object-fit: cover;
      border-radius: 8px;
    }

    .content {
      padding: 30px;
    }

    .page-head {
      display: flex;
      gap: 12px;
      align-items: center;
      justify-content: space-between;
      flex-wrap: wrap;
      margin-bottom: 16px;
    }

    .filters {
      display: flex;
      gap: 10px;
      align-items: center;
      flex-wrap: wrap;
    }

    .search {
      padding: 10px 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
      min-width: 240px;
    }

    .select {
      padding: 10px 12px;
      border: 1px solid #ddd;
      border-radius: 8px;
    }

    .btn-primary {
      background: #ff6b6b;
      color: #fff;
      border: none;
      padding: 10px 14px;
      border-radius: 8px;
      cursor: pointer;
    }

    .btn-ghost {
      background: #fff;
      border: 1px solid #ddd;
      color: #333;
      padding: 10px 14px;
      border-radius: 8px;
      cursor: pointer;
    }

    .btn-outline {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 8px 14px;
      border: 1px solid #ff6b6b;
      color: #ff6b6b;
      border-radius: 999px;
      background: #fff;
      font-weight: 600;
    }

    .btn-outline:hover {
      background: #ff6b6b;
      color: #fff;
    }

    .table-wrap {
      width: 100%;
      overflow-x: hidden;
    }

    table.teachers {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      background: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      border-radius: 12px;
      overflow: hidden;
    }

    table.teachers thead {
      background: #f5f5f5;
    }

    table.teachers th,
    table.teachers td {
      padding: var(--cell-pad-y) var(--cell-pad-x);
      border-bottom: 1px solid #eee;
      text-align: left;
      font-size: var(--cell-font);
      vertical-align: top;
    }

    table.teachers th {
      white-space: nowrap;
      word-break: normal;
    }

    table.teachers td {
      word-break: break-word;
      overflow-wrap: anywhere;
    }

    table.teachers th:nth-child(1) {
      width: 36px;
    }

    table.teachers th:nth-child(2) {
      width: 60px;
    }

    table.teachers th:nth-child(6) {
      width: 70px;
    }

    table.teachers th:nth-child(11) {
      width: 92px;
    }

    table.teachers th:nth-child(12) {
      width: 220px;
    }

    .row-actions {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 6px;
      align-items: start;
    }

    .btn-sm {
      padding: 6px 10px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 12px;
      width: 100%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }

    .btn-view {
      background: #64748b;
      color: #fff;
    }

    .btn-edit {
      background: #4caf50;
      color: #fff;
    }

    .btn-danger {
      background: #ef4444;
      color: #fff;
    }

    .btn-toggle {
      background: #22c55e;
      color: #fff;
    }

    .empty {
      padding: 30px;
      color: #777;
      text-align: center;
    }

    .avatar {
      width: 44px;
      height: 44px;
      border-radius: 50%;
      object-fit: cover;
      border: 1px solid #eee;
    }

    .badge {
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 600;
      display: inline-block;
    }

    .st-active {
      background: #e8f5e9;
      color: #2e7d32;
    }

    .st-inactive {
      background: #fee2e2;
      color: #991b1b;
    }

    .wa-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 3px 8px;
      border-radius: 999px;
      background: #25d366;
      color: #fff;
      font-size: 12px;
      text-decoration: none;
      margin-left: 6px;
    }

    .wa-chip:hover {
      filter: brightness(0.95);
    }

    @media (max-width: 1280px) {
      :root {
        --cell-font: 12.5px;
        --cell-pad-y: 8px;
        --cell-pad-x: 8px;
      }

      table.teachers th:nth-child(12) {
        width: 200px;
      }

      .row-actions {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 6px;
      }
    }

    @media (max-width: 720px) {
      table.teachers,
      table.teachers thead,
      table.teachers tbody,
      table.teachers th,
      table.teachers td,
      table.teachers tr {
        display: block !important;
        width: 100% !important;
      }

      table.teachers thead {
        display: none !important;
      }

      table.teachers tr {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        margin-bottom: 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        overflow: hidden;
      }

      table.teachers td {
        border-bottom: 1px solid #f1f1f1;
        padding: 12px 14px;
        display: flex !important;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
      }

      table.teachers td:last-child {
        border-bottom: none;
      }

      table.teachers td::before {
        content: attr(data-col);
        font-weight: 600;
        color: #6b7280;
        flex: 0 0 45%;
        max-width: 45%;
      }

      table.teachers td > * {
        flex: 1 1 auto;
        text-align: right;
        word-break: break-word;
      }

      .row-actions {
        width: 100%;
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }

      .btn-sm {
        font-size: 12px;
      }
    }

    /* Style for file input */
    #imageUpload {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ddd;
      font-size: 14px;
      width: 100%;
    }

    #imageUpload:focus {
      border-color: #ff6b6b;
      outline: none;
    }

    .photo-uploader div {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    label {
      font-size: 14px;
      color: #333;
      margin-bottom: 6px;
    }

  </style>
</head>
<body>
  <!-- Sidebar -->
  <aside class="sidebar">
    <h2><i class="fas fa-school"></i> Senesa Child Center - Admin</h2>
    <nav>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="students.php"><i class="fas fa-user-graduate"></i> Enrollments</a>
      <a href="teachers.php" class="active"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
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
      <h1>Teachers</h1>
      <div class="admin-info dropdown">
        <i class="fas fa-user-circle"></i> Admin <i class="fas fa-caret-down"></i>
        <ul class="dropdown-menu">
          <li><a href="profile.php">Profile</a></li>
          <li><a href="change-password.php">Change Password</a></li>
          <li><a href="#" id="logoutBtn">Logout</a></li>
        </ul>
      </div>
    </header>

    <section class="content">
      <div style="margin-bottom:16px">
        <a href="dashboard.php" class="btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
      </div>

      <div class="page-head">
        <div>
          <h2 style="margin:0;color:#333">Teacher Directory</h2>
          <p style="margin:4px 0 0;color:#6b7280;font-size:13px">Manage teacher profiles, availability, and visibility.</p>
        </div>
        <div class="filters">
          <input id="searchBox" class="search" placeholder="Search name / role / qualification / WhatsApp" />
          <select id="statusFilter" class="select">
            <option value="">All Statuses</option>
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
          <button id="addBtn" class="btn-primary"><i class="fas fa-plus"></i> Add Teacher</button>
          <button id="importBtn" class="btn-ghost"><i class="fas fa-file-import"></i> Import JSON</button>
          <input id="importInput" type="file" accept="application/json" style="display:none" />
          <button id="exportCsvBtn" class="btn-ghost"><i class="fas fa-file-export"></i> Export CSV</button>
        </div>
      </div>

      <div class="table-wrap">
        <table class="teachers">
          <thead>
            <tr>
              <th>ID</th>
              <th>Photo</th>
              <th>Name</th>
              <th>Role</th>
              <th>Qualifications</th>
              <th>Exp (yrs)</th>
              <th>Phone</th>
              <th>Email</th>
              <th>WhatsApp</th>
              <th>Availability</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
      <div id="emptyState" class="empty" style="display:none;">No teachers found.</div>
    </section>

    <footer style="text-align:center;padding:15px;margin-top:auto;background:#fff;box-shadow:0 -2px 5px rgba(0,0,0,.1);">
      &copy; 2025 Senesa Child Center. All rights reserved.
    </footer>
  </main>

  <!-- View Modal -->
  <div id="viewModal" class="modal">
    <div class="modal-content" style="max-width:520px">
      <span class="close" id="closeView">&times;</span>
      <h3>Teacher Profile</h3>
      <div id="viewBody" style="margin-top:10px;text-align:left;color:#444"></div>
      <div class="modal-buttons">
        <button class="btn-ghost" id="closeView2">Close</button>
      </div>
    </div>
  </div>

  <!-- Add/Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content" style="max-width:640px">
      <span class="close" id="closeEdit">&times;</span>
      <h3 id="editTitle">Add Teacher</h3>
      <form id="teacherForm">
        <input type="hidden" id="teacherId" />

        <!-- Photo Section -->
        <div class="form-group photo-uploader">
            <div style="flex:1">
                <label for="photoUrl">Photo URL</label>
                <input id="photoUrl" name="photo" placeholder="https://… or ../teacher1.png" />
            </div>
            <div>
                <label for="imageUpload">Or upload image</label>
                <input id="imageUpload" type="file" accept="image/*" />
            </div>
        </div>

        <!-- Teacher Information -->
        <div class="split">
            <div class="form-group">
                <label for="firstName">First Name</label>
                <input type="text" id="firstName" name="first_name" placeholder="First Name" required />
            </div>
            <div class="form-group">
                <label for="lastName">Last Name</label>
                <input type="text" id="lastName" name="last_name" placeholder="Last Name" required />
            </div>
        </div>

        <div class="split">
            <div class="form-group">
                <label for="role">Role</label>
                <input id="role" name="role" placeholder="Teacher / Principal / Assistant" />
            </div>
            <div class="form-group">
                <label for="experience">Experience (years)</label>
                <input id="experience" name="experience" type="number" min="0" step="1" />
            </div>
        </div>

        <div class="form-group">
            <label for="qualifications">Qualifications</label>
            <input id="qualifications" name="qualifications" placeholder="e.g., Montessori Diploma, B.Ed." />
        </div>

        <div class="split">
            <div class="form-group">
                <label for="phone">Phone</label>
                <input id="phone" name="phone" placeholder="+94 …" />
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" />
            </div>
        </div>

        <div class="form-group">
            <label for="whatsapp">WhatsApp Number</label>
            <input id="whatsapp" name="whatsapp" placeholder="+94 7X XXX XXXX" />
        </div>

        <div class="form-group">
            <label for="availability">Availability (short note)</label>
            <textarea id="availability" name="availability" rows="2" placeholder="Weekdays 8:00–12:30; Parent meetings Fri 12:30–1:30"></textarea>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option>Active</option>
                <option>Inactive</option>
            </select>
        </div>

        <div class="modal-buttons">
            <button class="btn-primary" type="submit">Save</button>
            <button class="btn-ghost" type="button" id="cancelEdit">Cancel</button>
        </div>
    </form>

    </div>
  </div>

<script>
/* ========= tiny toast ========= */
const toast = (msg, ok = true) => {
  const t = document.createElement('div');
  t.textContent = msg;
  Object.assign(t.style, {
    position: 'fixed', top: '10px', right: '10px', zIndex: 9999,
    background: ok ? '#16a34a' : '#ef4444', color: '#fff',
    padding: '10px 14px', borderRadius: '8px', boxShadow: '0 2px 8px rgba(0,0,0,.15)',
    fontSize: '14px'
  });
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 2500);
};

/* ========= config ========= */
const API = '../api/teachers.php';
let data = [];   // full dataset from server (latest)
let view = [];   // filtered view for table (search/status)

/* ========= helpers ========= */
function teacherImgSrc(p) {
  const placeholder = 'assets/teacher-placeholder.png';
  if (!p) return placeholder;
  if (/^https?:\/\//i.test(p)) return p;  // absolute URL
  let rel = String(p).replace(/^\/+/, ''); // strip leading slashes
  if (!/^uploads\/teachers\//i.test(rel)) rel = 'uploads/teachers/' + rel;
  return '../' + rel; // admin pages live one folder deeper
}

/* ========= API calls ========= */
async function apiList() {
  try {
    const r = await fetch(API + '?action=list', { cache: 'no-store' });
    const raw = await r.text();
    // console.log('teachers list raw =>', raw);
    let j;
    try { j = JSON.parse(raw); }
    catch (e) { console.error('Bad JSON from API:', e, raw); return []; }
    if (!j.ok) { console.error('API error:', j.error); return []; }
    return Array.isArray(j.data) ? j.data : [];
  } catch (err) {
    console.error('Network error:', err);
    return [];
  }
}

async function apiCreateFD(fd) {
  fd.append('action', 'create');
  const r = await fetch(API, { method: 'POST', body: fd });
  const raw = await r.text();
  // console.log('teachers.php create raw =>', raw);
  try { return JSON.parse(raw); } catch { return { ok:false, error:'Non-JSON from server', raw }; }
}

async function apiUpdateFD(id, fd) {
  fd.append('action', 'update');
  fd.append('id', id);
  const r = await fetch(API, { method: 'POST', body: fd });
  const raw = await r.text();
  // console.log('teachers.php update raw =>', raw);
  try { return JSON.parse(raw); } catch { return { ok:false, error:'Non-JSON from server', raw }; }
}

async function apiToggle(id) {
  const f = new FormData();
  f.append('action', 'toggle');
  f.append('id', id);
  return (await fetch(API, { method: 'POST', body: f })).json();
}

async function apiDelete(id) {
  const f = new FormData();
  f.append('action', 'delete');
  f.append('id', id);
  return (await fetch(API, { method: 'POST', body: f })).json();
}

/* ========= render ========= */
function renderTeachers(teachers) {
  const tbody = document.getElementById('tbody');
  const empty = document.getElementById('emptyState');
  tbody.innerHTML = '';

  if (!teachers.length) {
    empty.style.display = 'block';
    return;
  }
  empty.style.display = 'none';

  teachers.forEach(t => {
    const exp = t.experience_years ?? t.experienceYears ?? t.experience ?? '';
    const img = teacherImgSrc(t.photo);

    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td data-col="ID">${t.id}</td>
      <td data-col="Photo"><img src="${img}" alt="${t.first_name||''}" class="avatar"/></td>
      <td data-col="Name">${(t.first_name||'') + ' ' + (t.last_name||'')}</td>
      <td data-col="Role">${t.role||''}</td>
      <td data-col="Qualifications">${t.qualifications||''}</td>
      <td data-col="Exp (yrs)">${exp||'—'}</td>
      <td data-col="Phone">${t.phone||''}</td>
      <td data-col="Email">${t.email||''}</td>
      <td data-col="WhatsApp">${t.whatsapp||''}</td>
      <td data-col="Availability">${t.availability||''}</td>
      <td data-col="Status">
        <span class="badge ${String(t.status).toLowerCase()==='active'?'st-active':'st-inactive'}">
          ${t.status||'Active'}
        </span>
      </td>
      <td data-col="Actions">
        <div class="row-actions">
          <button class="btn-sm btn-view"   data-id="${t.id}"><i class="fas fa-eye"></i> View</button>
          <button class="btn-sm btn-edit"   data-id="${t.id}"><i class="fas fa-edit"></i> Edit</button>
          <button class="btn-sm btn-toggle" data-id="${t.id}" data-act="toggle"><i class="fas fa-power-off"></i> Toggle</button>
          <button class="btn-sm btn-danger" data-id="${t.id}" data-act="delete"><i class="fas fa-trash"></i> Delete</button>
        </div>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

/* ========= filtering ========= */
function applyFilters() {
  const q = (document.getElementById('searchBox')?.value || '').toLowerCase().trim();
  const st = (document.getElementById('statusFilter')?.value || '').toLowerCase().trim();

  view = data.filter(t => {
    const hay = [
      t.first_name, t.last_name, t.role, t.qualifications,
      t.phone, t.email, t.whatsapp, t.availability, t.status
    ].map(x => String(x || '').toLowerCase()).join(' ');
    const passQ  = !q || hay.includes(q);
    const passSt = !st || String(t.status || '').toLowerCase() === st;
    return passQ && passSt;
  });

  renderTeachers(view);
}

/* ========= modal helpers (edit/view) ========= */
function openAdd() {
  const form = document.getElementById('teacherForm');
  const editModal = document.getElementById('editModal');
  document.getElementById('editTitle').textContent = 'Add Teacher';
  document.getElementById('teacherId').value = '';
  form.reset();
  const prev = document.getElementById('photoPreview');
  if (prev) prev.src = 'assets/teacher-placeholder.png';
  editModal.style.display = 'flex';
  document.body.classList.add('modal-open');
}

function openEdit(t) {
  const form = document.getElementById('teacherForm');
  const editModal = document.getElementById('editModal');
  document.getElementById('editTitle').textContent = 'Edit Teacher';
  document.getElementById('teacherId').value = t.id;

  document.getElementById('photoUrl').value      = t.photo || '';
  document.getElementById('firstName').value     = t.first_name || '';
  document.getElementById('lastName').value      = t.last_name || '';
  document.getElementById('role').value          = t.role || '';
  document.getElementById('experience').value    = t.experience_years ?? t.experience ?? '';
  document.getElementById('qualifications').value= t.qualifications || '';
  document.getElementById('phone').value         = t.phone || '';
  document.getElementById('email').value         = t.email || '';
  document.getElementById('whatsapp').value      = t.whatsapp || '';
  document.getElementById('availability').value  = t.availability || '';
  document.getElementById('status').value        = t.status || 'Active';

  const prev = document.getElementById('photoPreview');
  if (prev) prev.src = teacherImgSrc(t.photo);

  editModal.style.display = 'flex';
  document.body.classList.add('modal-open');
}

function closeEditModal() {
  const editModal = document.getElementById('editModal');
  editModal.style.display = 'none';
  document.body.classList.remove('modal-open');
}

function viewTeacher(t) {
  const viewModal = document.getElementById('viewModal');
  const viewBody  = document.getElementById('viewBody');
  const created   = t.created_at || t.createdAt || '—';
  viewBody.innerHTML = `
    <div style="display:flex;gap:12px;align-items:center;margin-bottom:8px">
      <img src="${teacherImgSrc(t.photo)}" class="avatar" alt="${t.first_name||''} ${t.last_name||''}"/>
      <div><h4 style="margin:0">${t.first_name||'—'} ${t.last_name||'—'}</h4>
      <div style="color:#6b7280">${t.role || ''}</div></div>
    </div>
    <p><strong>Qualifications:</strong> ${t.qualifications || '—'}</p>
    <p><strong>Experience:</strong> ${(t.experience_years ?? t.experience ?? '—')} years</p>
    <p><strong>Phone:</strong> ${t.phone || '—'}</p>
    <p><strong>Email:</strong> ${t.email || '—'}</p>
    <p><strong>WhatsApp:</strong> ${t.whatsapp || '—'}</p>
    <p><strong>Availability:</strong> ${t.availability || '—'}</p>
    <p><strong>Status:</strong> ${t.status || '—'}</p>
    <p style="color:#6b7280"><strong>Created:</strong> ${created}</p>
  `;
  viewModal.style.display = 'flex';
  document.body.classList.add('modal-open');
}

function closeViewModal() {
  const viewModal = document.getElementById('viewModal');
  viewModal.style.display = 'none';
  document.body.classList.remove('modal-open');
}

// utility: refresh + normalize IDs as numbers
async function refresh() {
  const rows = await apiList();
  // normalize ids to numbers once so comparisons work
  data = rows.map(r => ({ ...r, id: Number(r.id) || 0 }));
  applyFilters();
}

// small CSV helper
function toCSV(arr) {
  if (!arr.length) return '';
  const cols = [
    'id','photo','first_name','last_name','role','qualifications',
    'experience_years','phone','email','whatsapp','availability','status','created_at'
  ];
  const esc = v => `"${String(v??'').replace(/"/g,'""')}"`;
  const head = cols.join(',');
  const body = arr.map(r => cols.map(c => esc(r[c])).join(',')).join('\n');
  return head + '\n' + body;
}

document.addEventListener('DOMContentLoaded', () => {
  const addBtn        = document.getElementById('addBtn');
  const cancelEdit    = document.getElementById('cancelEdit');
  const closeEditBtn  = document.getElementById('closeEdit');
  const closeView     = document.getElementById('closeView');
  const closeView2    = document.getElementById('closeView2');
  const searchBox     = document.getElementById('searchBox');
  const statusFilter  = document.getElementById('statusFilter');
  const form          = document.getElementById('teacherForm');
  const tbody         = document.getElementById('tbody');

  // NEW: import/export buttons
  const importBtn     = document.getElementById('importBtn');
  const importInput   = document.getElementById('importInput');
  const exportCsvBtn  = document.getElementById('exportCsvBtn');

  // open/close modals
  addBtn?.addEventListener('click', openAdd);
  cancelEdit?.addEventListener('click', closeEditModal);
  closeEditBtn?.addEventListener('click', closeEditModal);
  closeView?.addEventListener('click', () => {
    document.getElementById('viewModal').style.display = 'none';
    document.body.classList.remove('modal-open');
  });
  closeView2?.addEventListener('click', () => {
    document.getElementById('viewModal').style.display = 'none';
    document.body.classList.remove('modal-open');
  });

  // live filters
  searchBox?.addEventListener('input', applyFilters);
  statusFilter?.addEventListener('change', applyFilters);

  // image preview (optional <img id="photoPreview">)
  document.getElementById('imageUpload')?.addEventListener('change', e => {
    const file = e.target.files?.[0]; if (!file) return;
    const rd = new FileReader();
    rd.onload = ev => { const p = document.getElementById('photoPreview'); if (p) p.src = ev.target.result; };
    rd.readAsDataURL(file);
  });

  // SAVE (create/update)
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const first = document.getElementById('firstName').value.trim();
    const last  = document.getElementById('lastName').value.trim();
    if (!first || !last) { toast('First and Last Name are required', false); return; }

    const fd = new FormData(form);
    const file = document.getElementById('imageUpload')?.files?.[0];
    if (file) fd.append('file', file);

    const idVal = document.getElementById('teacherId').value;
    const id = idVal ? Number(idVal) : null;

    try {
      const resp = id ? await apiUpdateFD(id, fd) : await apiCreateFD(fd);
      if (resp.ok) {
        toast('Teacher saved!');
        closeEditModal();
        await refresh();
      } else {
        toast(resp.error || 'Save failed', false);
      }
    } catch (err) {
      console.error(err); toast('Error while saving teacher', false);
    }
  });

  // ROW ACTIONS — FIXED: compare ids as numbers
  tbody?.addEventListener('click', async (e) => {
    const btn = e.target.closest('button'); if (!btn) return;
    const id  = Number(btn.dataset.id);
    const t   = data.find(x => Number(x.id) === id); // normalize compare
    if (!t) return;

    if (btn.classList.contains('btn-view')) {
      viewTeacher(t);
    } else if (btn.classList.contains('btn-edit')) {
      openEdit(t);
    } else if (btn.dataset.act === 'toggle') {
      try { await apiToggle(id); await refresh(); }
      catch (err) { console.error(err); toast('Toggle failed', false); }
    } else if (btn.dataset.act === 'delete') {
      if (!confirm(`Delete ${t.first_name} ${t.last_name}?`)) return;
      try { await apiDelete(id); await refresh(); }
      catch (err) { console.error(err); toast('Delete failed', false); }
    }
  });

  // IMPORT JSON — expects an array of teacher objects (keys like your DB)
  importBtn?.addEventListener('click', () => importInput?.click());
  importInput?.addEventListener('change', async () => {
    const file = importInput.files?.[0]; if (!file) return;
    try {
      const text = await file.text();
      const arr = JSON.parse(text);
      if (!Array.isArray(arr)) throw new Error('JSON must be an array');
      // create sequentially; keep it simple
      for (const o of arr) {
        const fd = new FormData();
        // map known fields
        [
          'first_name','last_name','role','qualifications','experience_years',
          'phone','email','whatsapp','availability','status','photo'
        ].forEach(k => { if (o[k] != null) fd.append(k, o[k]); });
        const resp = await apiCreateFD(fd);
        if (!resp.ok) console.warn('Import item failed:', resp);
      }
      toast('Import complete');
      await refresh();
    } catch (err) {
      console.error(err); toast('Import failed', false);
    } finally {
      importInput.value = '';
    }
  });

  // EXPORT CSV — exports current filtered view
  exportCsvBtn?.addEventListener('click', () => {
    const csv = toCSV(view);
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'teachers.csv';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(a.href);
  });

  // initial load
  refresh();
});
</script>


</body>
</html>
