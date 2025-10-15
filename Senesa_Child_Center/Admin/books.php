<?php require __DIR__ . '/auth-admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title>Books - Senesa Child Center</title>
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
  table.books{ width:100%; border-collapse:separate; border-spacing:0; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.08); border-radius:12px; overflow:hidden }
  table.books thead{ background:#f5f5f5 }
  table.books th, table.books td{ padding:var(--cell-pad-y) var(--cell-pad-x); border-bottom:1px solid #eee; text-align:left; font-size:var(--cell-font) }
  table.books th{ white-space:nowrap }
  .cover{ width:44px; height:60px; object-fit:cover; border:1px solid #eee; border-radius:6px }
  .badge{ padding:6px 10px; border-radius:999px; font-size:12px; font-weight:600; display:inline-block }
  .st-stock{background:#e8f5e9;color:#2e7d32}
  .st-oos{background:#fff7ed;color:#9a3412}
  .st-hidden{background:#fee2e2;color:#991b1b}
  .row-actions{ display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:6px }
  .btn-sm{ padding:6px 10px; border:none; border-radius:6px; cursor:pointer; font-size:12px; width:100%; display:inline-flex; align-items:center; justify-content:center; gap:6px; }
  .btn-view{background:#64748b;color:#fff}
  .btn-edit{background:#4CAF50;color:#fff}
  .btn-danger{background:#ef4444;color:#fff}
  .btn-toggle{background:#22c55e;color:#fff}
  .empty{padding:30px;color:#777;text-align:center}

  @media (max-width:1280px){ :root{ --cell-font:12.5px; --cell-pad-y:8px; --cell-pad-x:8px; } }
  @media (max-width:720px){
    table.books, table.books thead, table.books tbody, table.books th, table.books td, table.books tr{ display:block !important; width:100% !important }
    table.books thead{ display:none !important }
    table.books tr{ background:#fff; border:1px solid #eee; border-radius:12px; margin-bottom:12px; box-shadow:0 2px 6px rgba(0,0,0,.06); overflow:hidden }
    table.books td{ border-bottom:1px solid #f1f1f1; padding:12px 14px; display:flex !important; align-items:flex-start; justify-content:space-between; gap:10px }
    table.books td:last-child{ border-bottom:none }
    table.books td::before{ content:attr(data-col); font-weight:600; color:#6b7280; flex:0 0 45%; max-width:45% }
    table.books td>*{ flex:1 1 auto; text-align:right; word-break:break-word }
  }

  /* Modal styling */
  .modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(15, 23, 42, .45);
    backdrop-filter: saturate(120%) blur(2px);
    padding: 24px;
    z-index: 9999;
  }

  .modal .modal-content {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    width: 95%;
    max-width: 620px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0,0,0,.20);
  }

  .modal .close {
    position: absolute;
    top: 12px;
    right: 14px;
    font-size: 22px;
    cursor: pointer;
    color: #9ca3af;
  }

  .modal .close:hover {
    color: #6b7280;
  }

  .split {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 15px;
  }

  .form-group label {
    font-weight: 600;
    color: #374151;
  }

  .form-group input, .form-group select {
    padding: 10px 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
  }

  .modal-buttons {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
    margin-top: 20px;
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
      <a href="books.php" class="active"><i class="fas fa-book"></i> Books</a>
      <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
      <a href="users.php"><i class="fas fa-users-cog"></i> Users</a>
      <a href="orders.php"><i class="fas fa-receipt"></i> Orders</a>
      <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    </nav>
  </aside>

  <main class="main-content">
    <header class="topbar">
      <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
      <h1>Books</h1>
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
          <h2 style="margin:0;color:#333">Books Catalog</h2>
          <p style="margin:4px 0 0;color:#6b7280;font-size:13px">Controls what appears in public "Available Books".</p>
        </div>
        <div class="filters">
          <input id="searchBox" class="search" placeholder="Search title / author" />
          <select id="statusFilter" class="select">
            <option value="">All</option><option>In Stock</option><option>Out of Stock</option><option>Hidden</option>
          </select>
          <button id="addBtn" class="btn-primary"><i class="fas fa-plus"></i> Add Book</button>
          <button id="importBtn" class="btn-ghost"><i class="fas fa-file-import"></i> Import JSON</button>
          <input id="importInput" type="file" accept="application/json" style="display:none" />
          <button id="exportCsvBtn" class="btn-ghost"><i class="fas fa-file-export"></i> Export CSV</button>
        </div>
      </div>

      <div class="table-wrap">
        <table class="books">
          <thead>
            <tr>
              <th style="width:48px">ID</th>
              <th style="width:56px">Cover</th>
              <th>Title</th>
              <th style="width:160px">Author</th>
              <th style="width:130px">Price</th>
              <th style="width:90px">Stock</th>
              <th style="width:110px">Status</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
      <div id="emptyState" class="empty" style="display:none;">No books found.</div>
    </section>

    <footer style="text-align:center;padding:15px;margin-top:auto;background:#fff;box-shadow:0 -2px 5px rgba(0,0,0,.1);">
      &copy; 2025 Senesa Child Center. All rights reserved.
    </footer>
  </main>

  <!-- Add/Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content" style="max-width:620px">
      <span class="close" id="closeEdit">&times;</span>
      <h3 id="editTitle">Add Book</h3>
      <form id="bookForm">
        <input type="hidden" id="bookId" />
        <div class="split">
          <div class="form-group"><label for="image">Cover URL</label><input id="image" placeholder="../img/book1.jpeg" /></div>
          <div class="form-group"><label for="price">Price (LKR)</label><input id="price" type="number" min="0" step="1" /></div>
        </div>
        <div class="split">
          <div class="form-group"><label for="title">Title</label><input id="title" required /></div>
          <div class="form-group"><label for="author">Author</label><input id="author" /></div>
        </div>
        <div class="split">
          <div class="form-group"><label for="stock">Stock</label><input id="stock" type="number" min="0" step="1" /></div>
          <div class="form-group"><label for="status">Status</label>
            <select id="status"><option>In Stock</option><option>Out of Stock</option><option>Hidden</option></select>
          </div>
        </div>
        <div class="modal-buttons"><button class="btn-primary" type="submit">Save</button><button class="btn-ghost" type="button" id="cancelEdit">Cancel</button></div>
      </form>
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

/* Books API logic */
const API_BASE = '../api/books.php';

async function loadBooks() {
    try {
        const response = await fetch(`${API_BASE}?action=list`);
        const result = await response.json();
        return result.ok ? result.data : [];
    } catch (error) {
        console.error('Error loading books:', error);
        return [];
    }
}

async function saveBook(book) {
    try {
        const response = await fetch(`${API_BASE}?action=add`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(book)
        });
        return await response.json();
    } catch (error) {
        console.error('Error saving book:', error);
        return { ok: false, error: error.message };
    }
}

async function updateBook(book) {
    try {
        const response = await fetch(`${API_BASE}?action=update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(book)
        });
        return await response.json();
    } catch (error) {
        console.error('Error updating book:', error);
        return { ok: false, error: error.message };
    }
}

async function deleteBook(id) {
    try {
        const response = await fetch(`${API_BASE}?action=delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id })
        });
        return await response.json();
    } catch (error) {
        console.error('Error deleting book:', error);
        return { ok: false, error: error.message };
    }
}

/* Books UI logic */
let data = [];

const tbody=document.getElementById('tbody'); const emptyState=document.getElementById('emptyState');
const searchBox=document.getElementById('searchBox'); const statusFilter=document.getElementById('statusFilter');
const badge=s=>`<span class="badge ${s==='In Stock'?'st-stock':(s==='Out of Stock'?'st-oos':'st-hidden')}">${s}</span>`;
const fmt=n=> (n||n===0) ? 'Rs. '+Number(n).toLocaleString('en-LK') : '—';
// prefix ../ if needed and allow absolute/https paths to pass through
const imgPath = (p) => {
  if (!p) return '';
  if (p.startsWith('http') || p.startsWith('../')) return p;
  return p.startsWith('img/') ? '../'+p : p;
};

async function render(){
  const q=(searchBox.value||'').toLowerCase(); const st=statusFilter.value;
  
  // Load books from API
  data = await loadBooks();
  
  tbody.innerHTML='';
  const filtered=data.filter(r=>{
    const mQ=!q||[r.title,r.author].filter(Boolean).some(v=>String(v).toLowerCase().includes(q));
    const mS=!st||r.status===st; return mQ&&mS;
  });
  emptyState.style.display = filtered.length ? 'none' : 'block';
  filtered.forEach(r=>{
    const tr=document.createElement('tr');
    tr.innerHTML=`
      <td data-col="ID">${r.id}</td>
      <td data-col="Cover"><img class="cover" src="${imgPath(r.image)||''}" alt=""></td>
      <td data-col="Title">${r.title||'—'}</td>
      <td data-col="Author">${r.author||'—'}</td>
      <td data-col="Price">${fmt(r.price)}</td>
      <td data-col="Stock">${Number.isFinite(r.stock)? r.stock : '—'}</td>
      <td data-col="Status">${badge(r.status)}</td>
      <td data-col="Actions">
        <div class="row-actions">
          <button class="btn-sm btn-edit" data-id="${r.id}"><i class="fas fa-edit"></i> Edit</button>
          <button class="btn-sm btn-toggle" data-id="${r.id}" data-act="toggle"><i class="fas fa-toggle-on"></i> ${r.status==='Hidden'?'Show':'Hide'}</button>
          <button class="btn-sm btn-danger" data-id="${r.id}" data-act="delete"><i class="fas fa-trash"></i> Delete</button>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });
}
render(); searchBox.addEventListener('input',render); statusFilter.addEventListener('change',render);

/* edit modal */
const editModal=document.getElementById('editModal'); const closeEdit=document.getElementById('closeEdit'); const cancelEdit=document.getElementById('cancelEdit');
const editTitle=document.getElementById('editTitle'); const form=document.getElementById('bookForm'); const bookId=document.getElementById('bookId');
const image=document.getElementById('image'); const price=document.getElementById('price'); const title=document.getElementById('title'); const author=document.getElementById('author'); const statusEl=document.getElementById('status');
const stock=document.getElementById('stock');

function openAdd(){
  editTitle.textContent='Add Book'; bookId.value='';
  [image,price,title,author,stock].forEach(e=>e.value='');
  statusEl.value='In Stock';
  editModal.style.display='flex'; document.body.classList.add('modal-open');
}
function openEdit(row){
  editTitle.textContent='Edit Book'; bookId.value=row.id;
  image.value=row.image||''; price.value=row.price??''; title.value=row.title||''; author.value=row.author||'';
  statusEl.value=row.status||'In Stock';
  stock.value=Number.isFinite(row.stock)? row.stock : '';
  editModal.style.display='flex'; document.body.classList.add('modal-open');
}
function closeEditModal(){ editModal.style.display='none'; document.body.classList.remove('modal-open') }
closeEdit.addEventListener('click',closeEditModal); cancelEdit.addEventListener('click',closeEditModal);
window.addEventListener('click',e=>{ if(e.target===editModal) closeEditModal() });
document.getElementById('addBtn').addEventListener('click',openAdd);

tbody.addEventListener('click',async e=>{
  const btn=e.target.closest('button'); if(!btn) return;
  const id=Number(btn.dataset.id); const row=data.find(x=>x.id===id); if(!row)return;
  if(btn.classList.contains('btn-edit')) openEdit(row);
  else if(btn.dataset.act==='toggle'){
    const newStatus = row.status==='Hidden'?'In Stock':'Hidden';
    const result = await updateBook({ id, status: newStatus });
    if(result.ok) {
      row.status = newStatus;
      render();
    } else {
      alert('Error: ' + (result.error || 'Unknown error'));
    }
  } else if(btn.dataset.act==='delete'){
    if(confirm('Delete this book?')){
      const result = await deleteBook(id);
      if(result.ok) {
        data = data.filter(x => x.id !== id);
        render();
      } else {
        alert('Error: ' + (result.error || 'Unknown error'));
      }
    }
  }
});

form.addEventListener('submit',async e=>{
  e.preventDefault(); const id=bookId.value?Number(bookId.value):null;
  const payload={
    image:image.value.trim(),
    price:price.value?Number(price.value):null,
    title:title.value.trim(),
    author:author.value.trim(),
    status:statusEl.value,
    stock:stock.value ? Number(stock.value) : 0
  };
  // Auto status from stock if not Hidden
  if (payload.stock <= 0 && payload.status !== 'Hidden') payload.status = 'Out of Stock';
  if (payload.stock > 0 && payload.status === 'Out of Stock') payload.status = 'In Stock';

  let result;
  if(id){
    payload.id = id;
    result = await updateBook(payload);
  } else {
    result = await saveBook(payload);
  }
  
  if(result.ok) {
    render();
    closeEditModal();
  } else {
    alert('Error: ' + (result.error || 'Unknown error'));
  }
});

/* import/export */
const importBtn=document.getElementById('importBtn'); const importInput=document.getElementById('importInput');
importBtn.addEventListener('click',()=>importInput.click());
importInput.addEventListener('change',async ()=>{
  const f=importInput.files[0]; if(!f)return; const r=new FileReader();
  r.onload=async ()=>{ try{
      const t=JSON.parse(r.result);
      const stockVal = Number.isFinite(Number(t.stock)) ? Number(t.stock) : (t.stock===0?0:null);
      const rec = {
        title:t.title||'',
        author:t.author||'',
        price:t.price??null,
        image:t.image||t.cover||'',
        status:t.status||'In Stock',
        stock: stockVal ?? 0
      };
      if ((rec.stock<=0) && rec.status!=='Hidden') rec.status='Out of Stock';
      
      const result = await saveBook(rec);
      if(result.ok) {
        render();
        alert('Book imported.');
      } else {
        alert('Error importing book: ' + (result.error || 'Unknown error'));
      }
    }catch(e){alert('Invalid JSON.')}
    importInput.value='';
  };
  r.readAsText(f);
});

document.getElementById('exportCsvBtn').addEventListener('click',()=>{
  const rows=[['ID','Title','Author','Price','Image','Stock','Status','Created']].concat(
    data.map(r=>[r.id,r.title,r.author,r.price,r.image,Number.isFinite(r.stock)?r.stock:'',r.status,r.created_at])
  );
  const csv=rows.map(r=>r.map(x=>`"${String(x??'').replace(/"/g,'""')}"`).join(',')).join('\n');
  const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'}); const a=document.createElement('a');
  a.href=URL.createObjectURL(blob); a.download='books.csv'; a.click(); URL.revokeObjectURL(a.href);
});
</script>
</body>
</html>