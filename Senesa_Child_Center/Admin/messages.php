<?php require __DIR__ . '/auth-admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" /><meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title>Messages - Senesa Child Center</title>
<link rel="stylesheet" href="dashboard.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
  :root{ --cell-font:13.5px; --cell-pad-y:10px; --cell-pad-x:10px; }
  .content{padding:30px}
  .page-head{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;margin-bottom:16px}
  .filters{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
  .search{padding:10px 12px;border:1px solid #ddd;border-radius:8px;min-width:240px}
  .select{padding:10px 12px;border:1px solid #ddd;border-radius:8px}
  .btn-primary{background:#ff6b6b;color:#fff;border:none;padding:10px 14px;border-radius:8px;cursor:pointer}
  .btn-ghost{background:#fff;border:1px solid #ddd;color:#333;padding:10px 14px;border-radius:8px;cursor:pointer}
  .btn-outline{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border:1px solid #ff6b6b;color:#ff6b6b;border-radius:999px;background:#fff;font-weight:600}
  .btn-outline:hover{background:#ff6b6b;color:#fff}

  .table-wrap{ width:100%; overflow-x:hidden; }
  table.msg{ width:100%; border-collapse:separate; border-spacing:0; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.08); border-radius:12px; overflow:hidden }
  table.msg thead{ background:#f5f5f5 }
  table.msg th, table.msg td{ padding:var(--cell-pad-y) var(--cell-pad-x); border-bottom:1px solid #eee; text-align:left; font-size:var(--cell-font) }
  .badge{ padding:6px 10px; border-radius:999px; font-size:12px; font-weight:600; display:inline-block }
  .st-unread{background:#e0ecff;color:#1e40af}
  .st-read{background:#e8f5e9;color:#2e7d32}
  .row-actions{ display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px }
  .btn-sm{ padding:6px 10px; border:none; border-radius:6px; cursor:pointer; font-size:12px; width:100%; display:inline-flex; align-items:center; justify-content:center; gap:6px; }
  .btn-view{background:#64748b;color:#fff}
  .btn-toggle{background:#22c55e;color:#fff}
  .btn-danger{background:#ef4444;color:#fff}
  .empty{padding:30px;color:#777;text-align:center}

  @media (max-width:720px){
    table.msg, table.msg thead, table.msg tbody, table.msg th, table.msg td, table.msg tr{ display:block !important; width:100% !important }
    table.msg thead{ display:none !important }
    table.msg tr{ background:#fff; border:1px solid #eee; border-radius:12px; margin-bottom:12px; box-shadow:0 2px 6px rgba(0,0,0,.06); overflow:hidden }
    table.msg td{ border-bottom:1px solid #f1f1f1; padding:12px 14px; display:flex !important; align-items:flex-start; justify-content:space-between; gap:10px }
    table.msg td:last-child{ border-bottom:none }
    table.msg td::before{ content:attr(data-col); font-weight:600; color:#6b7280; flex:0 0 45%; max-width:45% }
    table.msg td>*{ flex:1 1 auto; text-align:right; word-break:break-word }
  }
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
      <a href="books.php" ><i class="fas fa-book"></i> Books</a>
      <a href="messages.php" class="active"><i class="fas fa-envelope"></i> Messages</a>
      <a href="users.php"><i class="fas fa-users-cog"></i> Users</a>
      <a href="orders.php"><i class="fas fa-receipt"></i> Orders</a>
      <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    </nav>
  </aside>

  <main class="main-content">
    <header class="topbar">
      <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
      <h1>Messages</h1>
      <div class="admin-info dropdown">
        <i class="fas fa-user-circle"></i> Admin <i class="fas fa-caret-down"></i>
        <ul class="dropdown-menu">
          <li><a href="profile.php">Profile</a></li>
          <li><a href="change-password.php">Change Password</a></li>
          <li><a href="#" id="logoutBtn">Logout</a></li>
        </ul>
      </div>
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
      <div style="margin-bottom:16px"><a href="dashboard.php" class="btn-outline"><i class="fas fa-arrow-left"></i> Back</a></div>
      <div class="page-head">
        <div>
          <h2 style="margin:0;color:#333">Inbox</h2>
          <p style="margin:4px 0 0;color:#6b7280;font-size:13px">Messages from public “Contact Us”.</p>
        </div>
        <div class="filters">
          <input id="searchBox" class="search" placeholder="Search name / email / subject" />
          <select id="statusFilter" class="select"><option value="">All</option><option>Unread</option><option>Read</option></select>
          <button id="importBtn" class="btn-ghost"><i class="fas fa-file-import"></i> Import JSON</button>
          <input id="importInput" type="file" accept="application/json" style="display:none" />
          <button id="exportCsvBtn" class="btn-ghost"><i class="fas fa-file-export"></i> Export CSV</button>
        </div>
      </div>

      <div class="table-wrap">
        <table class="msg">
          <thead><tr><th style="width:56px">ID</th><th>From</th><th>Subject</th><th style="width:140px">Received</th><th style="width:100px">Status</th><th style="width:220px">Actions</th></tr></thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
      <div id="emptyState" class="empty" style="display:none;">No messages found.</div>
    </section>

    <footer style="text-align:center;padding:15px;margin-top:auto;background:#fff;box-shadow:0 -2px 5px rgba(0,0,0,.1);">
      &copy; 2025 Senesa Child Center. All rights reserved.
    </footer>
  </main>

  <!-- View Modal -->
  <div id="viewModal" class="modal">
    <div class="modal-content" style="max-width:640px">
      <span class="close" id="closeView">&times;</span>
      <h3>Message</h3>
      <div id="viewBody" style="margin-top:10px;text-align:left;color:#444"></div>
      <div class="modal-buttons"><button class="btn-ghost" id="closeView2">Close</button></div>
    </div>
  </div>

<script>
/* shared UI */
const adminInfo=document.querySelector('.admin-info'); const dropdownMenu=adminInfo?.querySelector('.dropdown-menu');
const logoutBtn=document.getElementById('logoutBtn'); const logoutModal=document.getElementById('logoutModal');
const closeLogout=logoutModal?.querySelector('.close'); const confirmLogout=document.getElementById('confirmLogout');
const cancelLogout=document.getElementById('cancelLogout'); const toggleBtn=document.querySelector('.sidebar-toggle');
const sidebar=document.querySelector('.sidebar');
adminInfo?.addEventListener('click',e=>{e.stopPropagation();dropdownMenu?.classList.toggle('show')});
document.addEventListener('click',()=>dropdownMenu?.classList.remove('show'));
logoutBtn?.addEventListener('click',e=>{e.preventDefault(); if(!logoutModal)return; logoutModal.style.display='flex'; dropdownMenu?.classList.remove('show'); document.body.classList.add('modal-open')});
function closeLogoutModal(){ if(!logoutModal)return; logoutModal.style.display='none'; document.body.classList.remove('modal-open') }
closeLogout?.addEventListener('click',closeLogoutModal); cancelLogout?.addEventListener('click',closeLogoutModal);
window.addEventListener('click',e=>{ if(e.target===logoutModal) closeLogoutModal() }); document.addEventListener('keydown',e=>{ if(e.key==='Escape'&&logoutModal?.style.display==='flex') closeLogoutModal() });
confirmLogout?.addEventListener('click',()=>{ document.body.classList.remove('modal-open'); location.href='admin-login.html' });
toggleBtn?.addEventListener('click',()=> sidebar?.classList.toggle('active'));

/* Messages logic (DB-backed) */
const API_BASE   = '../api/messages.php';
const API_LIST   = `${API_BASE}?action=list`;
const API_CREATE = `${API_BASE}?action=create`;   // used by import
const API_TOGGLE = `${API_BASE}?action=toggle`;
const API_DELETE = `${API_BASE}?action=delete`;

let data = [];

const tbody        = document.getElementById('tbody');
const emptyState   = document.getElementById('emptyState');
const searchBox    = document.getElementById('searchBox');
const statusFilter = document.getElementById('statusFilter');

const badge = s => `<span class="badge ${s==='Unread'?'st-unread':'st-read'}">${s}</span>`;

async function loadRows(){
  const q  = encodeURIComponent(searchBox.value || '');
  const st = encodeURIComponent(statusFilter.value || '');
  try{
    const res = await fetch(`${API_LIST}&q=${q}&status=${st}`);
    const out = await res.json();
    if(!out.ok) throw new Error(out.error || 'Failed');
    data = out.rows || [];
    render();
  }catch(err){
    console.error(err);
    data = [];
    render();
    alert('Could not load messages from the server.');
  }
}

function render(){
  tbody.innerHTML = '';
  if(!data.length){ emptyState.style.display='block'; return; }
  emptyState.style.display='none';
  data.forEach(r=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td data-col="ID">${r.id}</td>
      <td data-col="From">
        <div>
          <strong>${r.name || '—'}</strong>
          <div style="color:#6b7280">${r.email || ''}${r.phone ? ' • '+r.phone : ''}</div>
        </div>
      </td>
      <td data-col="Subject">${r.subject || '—'}</td>
      <td data-col="Received">${r.received_at || '—'}</td>
      <td data-col="Status">${badge(r.status || 'Unread')}</td>
      <td data-col="Actions">
        <div class="row-actions">
          <button class="btn-sm btn-view"   data-id="${r.id}"><i class="fas fa-eye"></i> View</button>
          <button class="btn-sm btn-toggle" data-id="${r.id}"><i class="fas fa-envelope-open"></i> Mark ${r.status==='Unread'?'Read':'Unread'}</button>
          <button class="btn-sm btn-danger" data-id="${r.id}"><i class="fas fa-trash"></i> Delete</button>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });
}

searchBox.addEventListener('input', loadRows);
statusFilter.addEventListener('change', loadRows);
loadRows();

/* View modal (unchanged) */
const viewModal=document.getElementById('viewModal'); 
const viewBody=document.getElementById('viewBody');
const closeView=document.getElementById('closeView'); 
const closeView2=document.getElementById('closeView2');
[closeView,closeView2].forEach(el=>el.addEventListener('click',()=>viewModal.style.display='none'));
window.addEventListener('click',e=>{ if(e.target===viewModal) viewModal.style.display='none' });

tbody.addEventListener('click', async (e)=>{
  const btn = e.target.closest('button'); if(!btn) return;
  const id  = Number(btn.dataset.id);
  const row = data.find(x=>x.id===id); if(!row) return;

  if(btn.classList.contains('btn-view')){
    viewBody.innerHTML = `
      <p><strong>From:</strong> ${row.name||'—'} &lt;${row.email||'—'}&gt; ${row.phone? ' • '+row.phone:''}</p>
      <p><strong>Subject:</strong> ${row.subject||'—'}</p>
      <p style="white-space:pre-wrap"><strong>Message:</strong>\n${row.message||'—'}</p>
      <p style="color:#6b7280"><strong>Received:</strong> ${row.received_at||'—'}</p>`;
    viewModal.style.display='flex';
  }
  else if(btn.classList.contains('btn-toggle')){
    const fd = new FormData(); fd.append('id', id);
    try{
      const r = await fetch(API_TOGGLE, { method:'POST', body: fd });
      const j = await r.json();
      if(!j.ok) throw new Error(j.error || 'Toggle failed');
      await loadRows();
    }catch(err){ alert('Could not update status.'); }
  }
  else if(btn.classList.contains('btn-danger')){
    if(!confirm('Delete this message?')) return;
    const fd = new FormData(); fd.append('id', id);
    try{
      const r = await fetch(API_DELETE, { method:'POST', body: fd });
      const j = await r.json();
      if(!j.ok) throw new Error(j.error || 'Delete failed');
      await loadRows();
    }catch(err){ alert('Could not delete message.'); }
  }
});

/* Import JSON -> create row in DB */
const importBtn=document.getElementById('importBtn'); 
const importInput=document.getElementById('importInput');
importBtn.addEventListener('click',()=>importInput.click());
importInput.addEventListener('change', async ()=>{
  const f = importInput.files[0]; if(!f) return;
  try{
    const text = await f.text();
    const t = JSON.parse(text);
    const payload = {
      name: t.name||'', email: t.email||'', phone: t.phone||'',
      subject: t.subject||'', message: t.message||''
    };
    const res = await fetch(API_CREATE, {
      method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload)
    });
    const out = await res.json();
    if(!out.ok) throw new Error(out.error || 'Import failed');
    await loadRows();
    alert('Message imported.');
  }catch(err){ alert('Invalid JSON or server error.'); }
  finally { importInput.value=''; }
});

/* Export CSV from current table */
document.getElementById('exportCsvBtn').addEventListener('click', ()=>{
  const rows = [['ID','Name','Email','Phone','Subject','Message','Received','Status']]
    .concat(data.map(r=>[r.id,r.name,r.email,r.phone,r.subject,r.message,r.received_at,r.status]));
  const csv = rows.map(r=>r.map(x=>`"${String(x??'').replace(/"/g,'""')}"`).join(',')).join('\n');
  const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
  const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'messages.csv'; a.click();
  URL.revokeObjectURL(a.href);
});
</script>
</body>
</html>
