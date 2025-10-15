<?php
require __DIR__ . '/auth-admin.php';
require __DIR__ . '/../db.php';

function e($s){ return htmlspecialchars($s??'', ENT_QUOTES, 'UTF-8'); }
$me = (int)($_SESSION['user_id']??0);
$meRow = null;
if ($me) {
  $s=$conn->prepare("SELECT name FROM users WHERE id=?");
  $s->bind_param('i',$me); $s->execute();
  $meRow=$s->get_result()->fetch_assoc(); $s->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title>Users Management - Senesa Child Center</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <style>
    /* (same CSS you had) */
    .content{ padding:30px; }
    .users-header{ display:flex; gap:12px; align-items:center; justify-content:space-between; flex-wrap:wrap; margin-bottom:16px; }
    .users-header h2{ margin:0; color:#333; }
    .users-actions{ display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .search-input{ padding:10px 12px; border:1px solid #ddd; border-radius:8px; min-width:260px; }
    .btn-primary{ background:#ff6b6b; color:#fff; border:none; padding:10px 14px; border-radius:8px; cursor:pointer; }
    .btn-ghost{ background:#fff; border:1px solid #ddd; color:#333; padding:8px 12px; border-radius:8px; cursor:pointer; }
    .table-wrap{ width:100%; overflow-x:hidden; }
    table.users{ width:100%; border-collapse:separate; border-spacing:0; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.08); border-radius:12px; overflow:hidden; }
    table.users thead{ background:#f5f5f5; }
    table.users th, table.users td{ padding:12px 14px; text-align:left; border-bottom:1px solid #eee; font-size:14px; }
    table.users th{ color:#555; white-space:nowrap; }
    table.users tr:hover td{ background:#fafafa; }
    .badge{ padding:6px 10px; border-radius:999px; font-size:12px; font-weight:600; display:inline-block; }
    .role-admin{ background:#ffe6e6; color:#e63946; }
    .role-parent{ background:#e8f5e9; color:#2e7d32; }
    .status-active{ background:#e8f0ff; color:#1e40af; }
    .status-blocked{ background:#fff1f2; color:#be123c; }
    .row-actions{ display:flex; gap:8px; flex-wrap:wrap; }
    .btn-small{ padding:6px 10px; border:none; border-radius:6px; cursor:pointer; font-size:12px; }
    .btn-edit{ background:#4CAF50; color:#fff; }
    .btn-block{ background:#e63946; color:#fff; }
    .btn-unblock{ background:#1e90ff; color:#fff; }
    .empty{ text-align:center; padding:30px; color:#777; }
    .sidebar{ width:250px; } .main-content{ margin-left:250px; }
    /* --- Modern modal look (applies to all .modal) --- */
    .modal{
      position: fixed; inset: 0;
      display: none;              /* use .show to open */
      align-items: center; justify-content: center;
      background: rgba(17,24,39,.55);         /* soft dark overlay */
      -webkit-backdrop-filter: blur(6px);
      backdrop-filter: blur(6px);
      z-index: 1000;
    }
    .modal.show{ display:flex; }

    .modal .modal-content{
      width: min(560px, 92vw);
      background: #fff;
      border-radius: 16px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 20px 40px rgba(0,0,0,.12);
      padding: 22px 22px 18px;
    }

    .modal .modal-header{
      display:flex; align-items:center; justify-content:space-between;
      gap:12px; margin-bottom:12px;
    }
    .modal .modal-title{
      font-size: 20px; font-weight:700; color:#111827; margin:0;
    }
    .modal .btn-close{
      border:none; background:transparent; font-size:22px; line-height:1;
      color:#6b7280; cursor:pointer; padding:4px;
    }
    .modal .btn-close:hover{ color:#111827; }

    /* Form layout */
    #userForm .form-grid{
      display:grid; gap:12px;
      grid-template-columns: 1fr;
    }
    #userForm label{
      font-size:13px; color:#374151; font-weight:600; margin-bottom:6px; display:block;
    }
    #userForm input[type="text"],
    #userForm input[type="email"],
    #userForm select{
      width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:10px;
      font-size:14px; outline:none; background:#fff;
    }
    #userForm input:focus, #userForm select:focus{
      border-color:#ff6b6b; box-shadow:0 0 0 3px rgba(255,107,107,.15);
    }

    /* Footer buttons */
    .modal .modal-buttons{
      display:flex; gap:10px; justify-content:flex-end; margin-top:6px;
    }
    .modal .btn-primary{
      background:#ff6b6b; color:#fff; border:none; padding:10px 14px;
      border-radius:10px; cursor:pointer; font-weight:600;
    }
    .modal .btn-secondary, .btn-ghost{
      background:#fff; color:#111827; border:1px solid #e5e7eb;
      padding:10px 14px; border-radius:10px; cursor:pointer; font-weight:600;
    }
    .modal .btn-primary:hover{ filter:brightness(.95); }
    .modal .btn-secondary:hover{ background:#f9fafb; }

    /* Two-column on wider screens */
    @media (min-width: 720px){
      #userForm .form-grid{ grid-template-columns: 1fr 1fr; }
      #userForm .form-span-2{ grid-column: 1 / -1; }
    }

    @media (max-width:900px){
      .main-content{ margin-left:0 !important; width:100% !important; }
      .content{ padding:16px !important; }
      .users-header{ align-items:flex-start; } .users-actions{ width:100%; }
      .search-input{ width:100%; min-width:0; }
      .sidebar{ position:fixed; top:0; left:-250px; width:250px; height:100%; transition:left .2s ease; z-index:1000; }
      .sidebar.active{ left:0; }
    }
    @media (max-width:720px){
      table.users, table.users thead, table.users tbody, table.users th, table.users td, table.users tr{ display:block; width:100%; }
      table.users thead{ display:none; }
      table.users tr{ background:#fff; border:1px solid #eee; border-radius:12px; margin-bottom:12px; box-shadow:0 2px 6px rgba(0,0,0,.06); overflow:hidden; }
      table.users td{ border-bottom:1px solid #f1f1f1; padding:12px 14px; display:flex !important; align-items:flex-start; justify-content:space-between; gap:10px; }
      table.users td:last-child{ border-bottom:none; }
      table.users td::before{ content:attr(data-col); font-weight:600; color:#6b7280; flex:0 0 45%; max-width:45%; }
      table.users td > *{ flex:1 1 auto; text-align:right; word-break:break-word; }
      .row-actions{ width:100%; display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:8px; }
      .btn-small{ font-size:12px; }
    }
    html, body, #root, #app, .main-content, .content{ transform:none !important; transform-origin:top left !important; -webkit-text-size-adjust:100% !important; zoom:1 !important; }
  </style>
</head>
<body>
  <aside class="sidebar">
    <h2><i class="fas fa-school"></i> Senesa Child Center - Admin</h2>
    <nav>
      <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
      <a href="students.php"><i class="fas fa-user-graduate"></i> Enrollments</a>
      <a href="teachers.php"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
      <a href="programs.php"><i class="fas fa-book-open"></i> Programs</a>
      <a href="gallery.php"><i class="fas fa-images"></i> Gallery</a>
      <a href="books.php"><i class="fas fa-book"></i> Books</a>
      <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
      <a href="users.php" class="active"><i class="fas fa-users-cog"></i> Users</a>
      <a href="orders.php"><i class="fas fa-receipt"></i> Orders</a>
      <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    </nav>
  </aside>

  <main class="main-content">
    <header class="topbar">
      <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
      <h1>Users</h1>
      <div class="admin-info dropdown">
        <i class="fas fa-user-circle"></i> <?= e($meRow['name'] ?? 'Admin') ?> <i class="fas fa-caret-down"></i>
        <ul class="dropdown-menu">
          <li><a href="profile.php">Profile</a></li>
          <li><a href="change-password.php">Change Password</a></li>
          <li><a href="logout.php" id="logoutBtn">Logout</a></li>
        </ul>
      </div>

      <div id="logoutModal" class="modal modal--confirm" style="display:none">
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
        <a href="dashboard.php" class="btn-outline btn-outline-primary"><i class="fas fa-arrow-left"></i> Back</a>
      </div>

      <div class="users-header">
        <h2>Users Management</h2>
        <div class="users-actions">
          <input id="searchBox" class="search-input" type="text" placeholder="Search by name or email..." />
          <button id="btnSearch" class="btn-ghost"><i class="fas fa-search"></i> Search</button>
          <button id="addUserBtn" class="btn-primary"><i class="fas fa-user-plus"></i> Add User</button>
          <button id="importBtn" class="btn-ghost"><i class="fas a-file-import"></i> Import JSON</button>
          <input id="importInput" type="file" accept="application/json" style="display:none" />
          <button id="exportCsvBtn" class="btn-ghost"><i class="fas fa-file-export"></i> Export CSV</button>
        </div>
      </div>

      <div class="table-wrap">
        <table class="users" id="usersTable">
          <thead>
            <tr>
              <th style="width:60px;">ID</th>
              <th>Name</th>
              <th>Email</th>
              <th style="width:120px;">Role</th>
              <th style="width:120px;">Status</th>
              <th style="width:220px;">Actions</th>
            </tr>
          </thead>
          <tbody id="usersTbody"></tbody>
        </table>
        <div id="emptyState" class="empty" style="display:none;">No users found.</div>
      </div>
    </section>

    <footer style="text-align:center; padding:15px; margin-top:auto; background:#fff; box-shadow:0 -2px 5px rgba(0,0,0,0.1);">
      &copy; 2025 Senesa Child Center. All rights reserved.
    </footer>
  </main>

  <!-- Edit/Add User Modal -->
<div id="userModal" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h3 id="userModalTitle" class="modal-title">Add User</h3>
      <button class="btn-close" id="closeUserModal" aria-label="Close">&times;</button>
    </div>

    <form id="userForm">
      <input type="hidden" id="userId" />

      <div class="form-grid">
        <div class="form-span-2">
          <label for="userName">Full Name</label>
          <input type="text" id="userName" required />
        </div>

        <div class="form-span-2">
          <label for="userEmail">Email</label>
          <input type="email" id="userEmail" required />
        </div>

        <div>
          <label for="userRole">Role</label>
          <select id="userRole" required>
            <option value="Parent">Parent</option>
            <option value="Admin">Admin</option>
            <option value="Super Admin">Super Admin</option>
          </select>
        </div>

        <div>
          <label for="userPassword">Temp Password <small>(optional)</small></label>
          <input type="text" id="userPassword" placeholder="e.g. Temp#2025" />
        </div>
      </div>

      <div class="modal-buttons">
        <button type="button" id="cancelUser" class="btn-secondary">Cancel</button>
        <button type="submit" class="btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>


  <!-- Block/Unblock Confirm Modal -->
  <div id="blockModal" class="modal">
    <div class="modal-content" style="max-width:420px;">
      <span class="close" id="closeBlockModal">&times;</span>
      <h3 id="blockTitle">Block User</h3>
      <p id="blockMessage">Are you sure?</p>
      <div class="modal-buttons">
        <button id="confirmBlock" class="btn-primary">Confirm</button>
        <button id="cancelBlock" class="btn-ghost">Cancel</button>
      </div>
    </div>
  </div>

  <script>
    // dropdown & sidebar & logout modal
    const adminInfo=document.querySelector('.admin-info'), dd=adminInfo?.querySelector('.dropdown-menu');
    adminInfo?.addEventListener('click',e=>{e.stopPropagation();dd?.classList.toggle('show')});
    document.addEventListener('click',()=>dd?.classList.remove('show'));
    document.querySelector('.sidebar-toggle')?.addEventListener('click',()=>document.querySelector('.sidebar')?.classList.toggle('active'));
    const logoutLink=document.getElementById('logoutBtn'), logoutModal=document.getElementById('logoutModal');
    logoutLink?.addEventListener('click',e=>{ if(!logoutModal) return; e.preventDefault(); logoutModal.style.display='flex'; });
    document.getElementById('confirmLogout')?.addEventListener('click',()=>location.href='logout.php');
    document.getElementById('cancelLogout')?.addEventListener('click',()=>logoutModal.style.display='none');
    logoutModal?.querySelector('.close')?.addEventListener('click',()=>logoutModal.style.display='none');

    // ===== Users CRUD via ../api/users.php =====
    const tbody = document.getElementById('usersTbody');
    const emptyState = document.getElementById('emptyState');
    const qInput = document.getElementById('searchBox');
    const btnSearch = document.getElementById('btnSearch');

    function badgeRole(r){ return `<span class="badge ${r==='Admin'||r==='Super Admin'?'role-admin':'role-parent'}">${r}</span>`; }
    function badgeStatus(s){ return `<span class="badge ${s==='Active'?'status-active':'status-blocked'}">${s}</span>`; }
    function esc(s){ return (s??'').replace(/[&<>"']/g,m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;' }[m])); }

    async function loadUsers() {
      const r = await fetch('../api/users.php?action=list&q='+encodeURIComponent(qInput.value||''));
      const {ok,data} = await r.json();
      tbody.innerHTML = '';
      if(!ok || !data.length){ emptyState.style.display='block'; return; }
      emptyState.style.display='none';
      tbody.innerHTML = data.map(u => `
        <tr>
          <td data-col="ID">${u.id}</td>
          <td data-col="Name">${esc(u.name)}</td>
          <td data-col="Email">${esc(u.email)}</td>
          <td data-col="Role">${badgeRole(u.role)}</td>
          <td data-col="Status">${badgeStatus(u.status)}</td>
          <td data-col="Actions">
            <div class="row-actions">
              <button class="btn-small btn-edit" data-id="${u.id}"
                data-name="${esc(u.name)}" data-email="${esc(u.email)}" data-role="${esc(u.role)}">
                <i class="fas fa-edit"></i> Edit
              </button>
              ${u.can_modify ? (u.status==='Active'
                ? `<button class="btn-small btn-block" data-id="${u.id}" data-action="block"><i class="fas fa-ban"></i> Block</button>`
                : `<button class="btn-small btn-unblock" data-id="${u.id}" data-action="unblock"><i class="fas fa-unlock"></i> Unblock</button>`
              ) : '<em>It’s you</em>'}

              ${u.can_modify ? `<button class="btn-small btn-block" style="background:#b42318" data-action="delete" data-id="${u.id}">
                <i class="fas fa-trash-alt"></i> Delete</button>` : ''}
            </div>
          </td>
        </tr>
      `).join('');
    }

    btnSearch.addEventListener('click', loadUsers);
    qInput.addEventListener('keydown', e => { if(e.key==='Enter'){ e.preventDefault(); loadUsers(); }});

    // Add/Edit modal
    const userModal=document.getElementById('userModal');
    const closeUserModal=document.getElementById('closeUserModal');
    const cancelUser=document.getElementById('cancelUser');
    const userForm=document.getElementById('userForm');
    const userId=document.getElementById('userId');
    const userName=document.getElementById('userName');
    const userEmail=document.getElementById('userEmail');
    const userRole=document.getElementById('userRole');
    const userPassword=document.getElementById('userPassword');
    const userModalTitle=document.getElementById('userModalTitle');

    document.getElementById('addUserBtn').addEventListener('click', ()=>{
      userModalTitle.textContent='Add User';
      userId.value=''; userName.value=''; userEmail.value=''; userRole.value='Parent'; userPassword.value='';
      userModal.style.display='flex';
    });
    closeUserModal.addEventListener('click', ()=> userModal.style.display='none');
    cancelUser.addEventListener('click', ()=> userModal.style.display='none');
    window.addEventListener('click', e=>{ if(e.target===userModal) userModal.style.display='none'; });

    tbody.addEventListener('click', (e)=>{
      const btn = e.target.closest('button'); if(!btn) return;
      const id = btn.dataset.id;

      if (btn.classList.contains('btn-edit')) {
        userModalTitle.textContent='Edit User';
        userId.value = id;
        userName.value = btn.dataset.name;
        userEmail.value = btn.dataset.email;
        userRole.value = btn.dataset.role;
        userPassword.value = '';
        userModal.style.display='flex';
        return;
      }

      const action = btn.dataset.action;
      if (!action) return;
      if (action === 'delete' && !confirm('Delete this user? This cannot be undone.')) return;

      const fd = new FormData();
      fd.append('action', action==='delete' ? 'delete' : 'set_status');
      fd.append('id', id);
      if (action === 'block' || action === 'activate') fd.append('status', action==='block'?'Blocked':'Active');

      fetch('../api/users.php', { method:'POST', body: fd })
        .then(r=>r.json()).then(({ok,msg})=>{
          if(!ok){ alert(msg||'Action failed'); return; }
          loadUsers();
        }).catch(()=>alert('Network error'));
    });

    userForm.addEventListener('submit', (e)=>{
      e.preventDefault();
      const fd = new FormData();
      fd.append('action','save');
      if (userId.value.trim()!=='') fd.append('id', userId.value.trim());
      fd.append('name', userName.value.trim());
      fd.append('email', userEmail.value.trim());
      fd.append('role', userRole.value);
      if (userPassword.value.trim()!=='') fd.append('password', userPassword.value.trim());

      fetch('../api/users.php', { method:'POST', body: fd })
        .then(r=>r.json()).then(({ok,msg})=>{
          if(!ok){ alert(msg||'Save failed'); return; }
          userModal.style.display='none';
          loadUsers();
        }).catch(()=>alert('Network error'));
    });

    // Import JSON
    document.getElementById('importBtn').addEventListener('click', ()=> document.getElementById('importInput').click());
    document.getElementById('importInput').addEventListener('change', ()=>{
      const file = document.getElementById('importInput').files[0]; if(!file) return;
      const reader = new FileReader();
      reader.onload = ()=>{
        try{
          const u = JSON.parse(reader.result);
          const fd = new FormData();
          fd.append('action','save');
          fd.append('name', u.name||'');
          fd.append('email', u.email||'');
          fd.append('role', (u.role==='Admin'||u.role==='Super Admin') ? u.role : 'Parent');
          if (u.password) fd.append('password', u.password);
          fetch('../api/users.php', { method:'POST', body: fd })
            .then(r=>r.json()).then(({ok,msg})=>{
              if(!ok){ alert(msg||'Import failed'); return; }
              alert('User imported'); loadUsers();
            });
        }catch(e){ alert('Invalid JSON'); }
        document.getElementById('importInput').value='';
      };
      reader.readAsText(file);
    });

    // Export CSV
    document.getElementById('exportCsvBtn').addEventListener('click', ()=>{
      const q = encodeURIComponent(qInput.value||'');
      window.location = '../api/users.php?action=export&q='+q;
    });

    // initial load
    loadUsers();
  </script>
</body>
</html>
