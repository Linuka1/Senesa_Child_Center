<?php require __DIR__ . '/auth-admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title>Orders - Senesa Child Center</title>
<link rel="stylesheet" href="dashboard.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<style>
    :root{ --cell-font:13.5px; --cell-pad-y:10px; --cell-pad-x:10px; }
    .content{padding:30px}
    .page-head{display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;margin-bottom:16px}
    .filters{display:flex;gap:10px;align-items:center;flex-wrap:wrap}
    .search{padding:10px 12px;border:1px solid #ddd;border-radius:8px;min-width:220px}
    .select,.input{padding:10px 12px;border:1px solid #ddd;border-radius:8px}
    .btn-primary{background:#ff6b6b;color:#fff;border:none;padding:10px 14px;border-radius:8px;cursor:pointer}
    .btn-ghost{background:#fff;border:1px solid #ddd;color:#333;padding:10px 14px;border-radius:8px;cursor:pointer}
    .btn-outline{display:inline-flex;align-items:center;gap:8px;padding:8px 14px;border:1px solid #ff6b6b;color:#ff6b6b;border-radius:999px;background:#fff;font-weight:600}
    .btn-outline:hover{background:#ff6b6b;color:#fff}

    .table-wrap{ width:100%; overflow-x:hidden; }
    table.orders{ width:100%; border-collapse:separate; border-spacing:0; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,.08); border-radius:12px; overflow:hidden }
    table.orders thead{ background:#f5f5f5 }
    table.orders th, table.orders td{ padding:var(--cell-pad-y) var(--cell-pad-x); border-bottom:1px solid #eee; text-align:left; font-size:var(--cell-font) }
    table.orders th{ white-space:nowrap }
    .row-actions{ display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:6px }
    .btn-sm{ padding:6px 10px; border:none; border-radius:6px; cursor:pointer; font-size:12px; width:100%; display:inline-flex; align-items:center; justify-content:center; gap:6px; }
    .btn-edit{background:#4CAF50;color:#fff}
    .btn-danger{background:#ef4444;color:#fff}
    .btn-view{background:#64748b;color:#fff}

    .badge{ padding:6px 10px; border-radius:999px; font-size:12px; font-weight:600; display:inline-block }
    .pay-paid{background:#e8f5e9;color:#2e7d32}
    .pay-pending{background:#fff7ed;color:#9a3412}
    .pay-failed{background:#fee2e2;color:#991b1b}
    .ful-new{background:#e0f2fe;color:#075985}
    .ful-packed{background:#e0e7ff;color:#3730a3}
    .ful-shipped{background:#fef9c3;color:#854d0e}
    .ful-complete{background:#f0fdf4;color:#166534}

    .empty{padding:30px;color:#777;text-align:center}

    /* Modal */
    .modal{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.4); align-items:center; justify-content:center; z-index:1000 }
    .modal .modal-content{ background:#fff; border-radius:12px; padding:20px; width:95%; max-width:900px; position:relative; box-shadow:0 6px 20px rgba(0,0,0,.15) }
    .modal .close{ position:absolute; top:12px; right:14px; font-size:22px; cursor:pointer }
    .split{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .form-group{display:flex;flex-direction:column;gap:6px}
    .form-group input,.form-group select,.form-group textarea{padding:10px 12px;border:1px solid #ddd;border-radius:8px}
    .form-row{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px}
    .modal-buttons{display:flex;gap:8px;justify-content:flex-end;margin-top:14px}

    /* Items builder */
    .builder{border:1px solid #eee;border-radius:10px;padding:12px}
    .builder .row{display:grid;grid-template-columns:2fr 100px 120px 120px 40px;gap:8px;margin-bottom:8px;align-items:center}
    .builder .row input,.builder .row select{padding:8px 10px;border:1px solid #ddd;border-radius:8px}
    .builder .remove{background:#fee2e2;border:none;border-radius:8px;cursor:pointer;height:36px;display:flex;align-items:center;justify-content:center}
    .builder .head{display:grid;grid-template-columns:2fr 100px 120px 120px 40px;gap:8px;margin-bottom:6px;color:#6b7280;font-size:12px}
    .summary{display:flex;justify-content:space-between;align-items:center;margin-top:8px}
    .summary .total{font-weight:700;font-size:16px}

    @media (max-width:1024px){
      .split{grid-template-columns:1fr}
      .form-row{grid-template-columns:repeat(2,minmax(0,1fr))}
      .builder .row,.builder .head{grid-template-columns:1fr 80px 100px 100px 36px}
    }
    @media (max-width:720px){
      table.orders, table.orders thead, table.orders tbody, table.orders th, table.orders td, table.orders tr{ display:block !important; width:100% !important }
      table.orders thead{ display:none !important }
      table.orders tr{ background:#fff; border:1px solid #eee; border-radius:12px; margin-bottom:12px; box-shadow:0 2px 6px rgba(0,0,0,.06); overflow:hidden }
      table.orders td{ border-bottom:1px solid #f1f1f1; padding:12px 14px; display:flex !important; align-items:flex-start; justify-content:space-between; gap:10px }
      table.orders td:last-child{ border-bottom:none }
      table.orders td::before{ content:attr(data-col); font-weight:600; color:#6b7280; flex:0 0 45%; max-width:45% }
      table.orders td>*{ flex:1 1 auto; text-align:right; word-break:break-word }
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
      <a href="messages.php"><i class="fas fa-envelope"></i> Messages</a>
      <a href="users.php"><i class="fas fa-users-cog"></i> Users</a>
      <a href="orders.php" class="active"><i class="fas fa-receipt"></i> Orders</a>
      <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
    </nav>
  </aside>

  <main class="main-content">
    <header class="topbar">
      <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
      <h1>Orders</h1>
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
          <h2 style="margin:0;color:#333">Orders & Transactions</h2>
          <p style="margin:4px 0 0;color:#6b7280;font-size:13px">All book purchases from the public site.</p>
        </div>
        <div class="filters">
          <input id="searchBox" class="search" placeholder="Search order/buyer/email" />
          <input id="fromDate" type="date" class="input" />
          <input id="toDate" type="date" class="input" />
          <select id="payFilter" class="select">
            <option value="">All Payments</option>
            <option>paid</option><option>pending</option><option>failed</option>
          </select>
          <select id="fulFilter" class="select">
            <option value="">All Fulfillment</option>
            <option>new</option><option>packed</option><option>shipped</option><option>complete</option>
          </select>
          <button id="addBtn" class="btn-primary"><i class="fas fa-plus"></i> Add Order</button>
          <button id="exportCsvBtn" class="btn-ghost"><i class="fas fa-file-export"></i> Export CSV</button>
        </div>
      </div>

      <div class="table-wrap">
        <table class="orders">
          <thead>
            <tr>
              <th style="width:120px">Order ID</th>
              <th style="width:140px">Date</th>
              <th>Buyer</th>
              <th style="width:160px">Email</th>
              <th>Items</th>
              <th style="width:110px">Amount</th>
              <th style="width:110px">Payment</th>
              <th style="width:120px">Fulfillment</th>
              <th style="width:220px">Actions</th>
            </tr>
          </thead>
          <tbody id="tbody"></tbody>
        </table>
      </div>
      <div id="emptyState" class="empty" style="display:none;">No orders found.</div>
    </section>

    <footer style="text-align:center;padding:15px;margin-top:auto;background:#fff;box-shadow:0 -2px 5px rgba(0,0,0,.1);">
      &copy; 2025 Senesa Child Center. All rights reserved.
    </footer>
  </main>

  <!-- Add/Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" id="closeEdit">&times;</span>
      <h3 id="editTitle">Add Order</h3>

      <form id="orderForm">
        <input type="hidden" id="rowId" />

        <div class="split">
          <div class="form-group">
            <label for="orderId">Order ID (optional)</label>
            <input id="orderId" placeholder="e.g., ORD-2025-0001" />
          </div>
          <div class="form-group">
            <label for="createdAt">Date & Time</label>
            <input id="createdAt" type="datetime-local" />
          </div>
        </div>

        <div class="split">
          <div class="form-group">
            <label for="buyerName">Buyer Name</label>
            <input id="buyerName" required />
          </div>
          <div class="form-group">
            <label for="buyerEmail">Buyer Email</label>
            <input id="buyerEmail" type="email" required />
          </div>
        </div>

        <!-- Items Builder -->
        <div class="form-group">
          <label>Items</label>
          <div class="builder" id="builder">
            <div class="head"><div>Book</div><div>Qty</div><div>Unit Price</div><div>Line Total</div><div></div></div>
            <div id="itemRows"></div>
            <button type="button" id="addItemBtn" class="btn-ghost"><i class="fas fa-plus"></i> Add Item</button>
            <div class="summary">
              <div class="total">Total: <span id="grandTotal">Rs. 0</span></div>
              <div style="color:#6b7280;font-size:12px">Changing **Paid** will decrement stock in Books.</div>
            </div>
          </div>
        </div>

        
        <div class="form-row">
          <div class="form-group">
            <label for="paymentStatus">Payment Status</label>
            <select id="paymentStatus">
              <option>paid</option><option>pending</option><option>failed</option>
            </select>
          </div>
          <div class="form-group">
            <label for="paymentRef">Payment Ref</label>
            <input id="paymentRef" placeholder="e.g., PG_ABC123" />
          </div>
          <div class="form-group">
            <label for="fulfillmentStatus">Fulfillment</label>
            <select id="fulfillmentStatus">
              <option>new</option><option>packed</option><option>shipped</option><option>complete</option>
            </select>
          </div>
          <div class="form-group">
            <label for="notes">Notes</label>
            <input id="notes" placeholder="Optional note..." />
          </div>
        </div>

        <div class="modal-buttons">
          <button class="btn-primary" type="submit">Save</button>
          <button class="btn-ghost" type="button" id="cancelEdit">Cancel</button>
        </div>
      </form>
    </div>
  </div>

<script>
/* ==== shared UI (same behavior as other pages) ==== */
const adminInfo=document.querySelector('.admin-info'); 
const dropdownMenu=adminInfo?.querySelector('.dropdown-menu');
const logoutBtn=document.getElementById('logoutBtn'); 
const logoutModal=document.getElementById('logoutModal');
const closeLogout=logoutModal?.querySelector('.close'); 
const confirmLogout=document.getElementById('confirmLogout');
const cancelLogout=document.getElementById('cancelLogout'); 
const toggleBtn=document.querySelector('.sidebar-toggle');
const sidebar=document.querySelector('.sidebar');
adminInfo?.addEventListener('click',e=>{e.stopPropagation();dropdownMenu?.classList.toggle('show')});
document.addEventListener('click',()=>dropdownMenu?.classList.remove('show'));
logoutBtn?.addEventListener('click',e=>{e.preventDefault(); if(!logoutModal)return; logoutModal.style.display='flex'; dropdownMenu?.classList.remove('show'); document.body.classList.add('modal-open')});
function closeLogoutModal(){ if(!logoutModal)return; logoutModal.style.display='none'; document.body.classList.remove('modal-open') }
closeLogout?.addEventListener('click',closeLogoutModal); cancelLogout?.addEventListener('click',closeLogoutModal);
window.addEventListener('click',e=>{ if(e.target===logoutModal) closeLogoutModal() }); 
document.addEventListener('keydown',e=>{ if(e.key==='Escape'&&logoutModal?.style.display==='flex') closeLogoutModal() });
confirmLogout?.addEventListener('click',()=>{ document.body.classList.remove('modal-open'); location.href='admin-login.html' });
toggleBtn?.addEventListener('click',()=> sidebar?.classList.toggle('active'));

/* ==== helpers ==== */
const fmtLKR = n => (n||n===0) ? 'Rs. '+Number(n).toLocaleString('en-LK') : '—';
const badgePay = s => `<span class="badge ${s==='paid'?'pay-paid':(s==='pending'?'pay-pending':'pay-failed')}">${s}</span>`;
const badgeFul = s => {
  const map={new:'ful-new',packed:'ful-packed',shipped:'ful-shipped',complete:'ful-complete'};
  return `<span class="badge ${map[s]||'ful-new'}">${s}</span>`;
};
function uid(){ return Math.random().toString(36).slice(2,8).toUpperCase(); }
function defaultOrderId(){ const now=new Date(); const y=now.getFullYear(); return `ORD-${y}-${uid()}`; }
const parseDate = s => s ? new Date(String(s).replace(' ', 'T')) : new Date();

/* ==== data (DB-backed) ==== */
let orders = [];      // loaded from API
let books  = [];      // loaded from API
let booksLoaded=false;

/* ==== UI refs ==== */
const tbody=document.getElementById('tbody'); 
const emptyState=document.getElementById('emptyState');
const searchBox=document.getElementById('searchBox');
const fromDate=document.getElementById('fromDate'); 
const toDate=document.getElementById('toDate');
const payFilter=document.getElementById('payFilter'); 
const fulFilter=document.getElementById('fulFilter');
document.getElementById('exportCsvBtn').addEventListener('click',exportCSV);

/* ==== render ==== */
function render(){
  const q=(searchBox.value||'').toLowerCase();
  const pf=payFilter.value; 
  const ff=fulFilter.value;
  const fd=fromDate.value? new Date(fromDate.value) : null;
  const td=toDate.value? new Date(toDate.value+'T23:59:59') : null;

  tbody.innerHTML='';
  const filtered = orders.filter(o=>{
    const text=[o.orderId,o.buyer?.name,o.buyer?.email,o.paymentRef].filter(Boolean).join(' ').toLowerCase();
    const matchQ = !q || text.includes(q);
    const matchP = !pf || o.paymentStatus===pf;
    const matchF = !ff || o.fulfillmentStatus===ff;
    const dt = parseDate(o.createdAt);
    const matchD = (!fd || dt>=fd) && (!td || dt<=td);
    return matchQ && matchP && matchF && matchD;
  });

  emptyState.style.display = filtered.length ? 'none':'block';

  filtered.forEach(o=>{
    const itemsShort = (o.items||[]).map(it => `${it.title||('Book '+it.bookId)} × ${it.qty}`).join(', ');
    const tr=document.createElement('tr');
    tr.innerHTML = `
      <td data-col="Order ID">${o.orderId}</td>
      <td data-col="Date">${parseDate(o.createdAt).toLocaleString()}</td>
      <td data-col="Buyer">${o.buyer?.name||'—'}</td>
      <td data-col="Email">${o.buyer?.email||'—'}</td>
      <td data-col="Items">${itemsShort||'—'}</td>
      <td data-col="Amount">${fmtLKR(o.amount)}</td>
      <td data-col="Payment">${badgePay(o.paymentStatus)}</td>
      <td data-col="Fulfillment">${badgeFul(o.fulfillmentStatus)}</td>
      <td data-col="Actions">
        <div class="row-actions">
          <button class="btn-sm btn-view" data-act="view" data-id="${o.orderId}"><i class="fas fa-eye"></i> View</button>
          <button class="btn-sm btn-edit" data-act="edit" data-id="${o.orderId}"><i class="fas fa-edit"></i> Edit</button>
          <button class="btn-sm btn-danger" data-act="delete" data-id="${o.orderId}"><i class="fas fa-trash"></i> Delete</button>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });
}
[searchBox, fromDate, toDate, payFilter, fulFilter].forEach(el => el.addEventListener('input', render));

/* ==== modal & form ==== */
const editModal=document.getElementById('editModal');
const closeEdit=document.getElementById('closeEdit');
const cancelEdit=document.getElementById('cancelEdit');
const editTitle=document.getElementById('editTitle');
const form=document.getElementById('orderForm');

const rowId=document.getElementById('rowId');
const orderId=document.getElementById('orderId');
const createdAt=document.getElementById('createdAt');
const buyerName=document.getElementById('buyerName');
const buyerEmail=document.getElementById('buyerEmail');
const paymentStatus=document.getElementById('paymentStatus');
const paymentRef=document.getElementById('paymentRef');
const fulfillmentStatus=document.getElementById('fulfillmentStatus');
const notes=document.getElementById('notes');

const itemRows=document.getElementById('itemRows');
const addItemBtn=document.getElementById('addItemBtn');
const grandTotalEl=document.getElementById('grandTotal');
let formItems=[];  // [{bookId,title,qty,unitPrice}]

document.getElementById('addBtn').addEventListener('click', openAdd);
closeEdit.addEventListener('click', closeModal);
cancelEdit.addEventListener('click', closeModal);
window.addEventListener('click', e=>{ if(e.target===editModal) closeModal(); });

tbody.addEventListener('click', async e=>{
  const btn = e.target.closest('button'); if(!btn) return;
  const id = btn.dataset.id;
  const act = btn.dataset.act;
  const idx = orders.findIndex(x=>x.orderId===id);
  if(idx<0) return;

  if(act==='view'){ openEdit(orders[idx], true); }
  else if(act==='edit'){ openEdit(orders[idx], false); }
  else if(act==='delete'){
    if(confirm('Delete this order?')){
      try { await deleteOrder(id); await loadOrders(); render(); }
      catch(err){ console.error(err); alert('Failed to delete: '+err.message); }
    }
  }
});

async function openAdd(){
  await fetchBooks();
  editTitle.textContent = 'Add Order';
  rowId.value = '';
  orderId.value = defaultOrderId();
  createdAt.value = new Date(Date.now() - new Date().getTimezoneOffset() * 60000).toISOString().slice(0, 16);
  buyerName.value = '';
  buyerEmail.value = '';
  paymentStatus.value = 'pending';
  paymentRef.value = '';
  fulfillmentStatus.value = 'new';
  notes.value = '';
  formItems = [];
  drawItems();
  editModal.style.display = 'flex'; document.body.classList.add('modal-open');
}

function openEdit(order, readOnly=false){
  editTitle.textContent = readOnly ? 'View Order' : 'Edit Order';
  rowId.value = order.orderId;
  orderId.value = order.orderId;

  const dt = parseDate(order.createdAt);
  createdAt.value = new Date(dt - dt.getTimezoneOffset()*60000).toISOString().slice(0,16);

  buyerName.value = order.buyer?.name||'';
  buyerEmail.value = order.buyer?.email||'';
  paymentStatus.value = order.paymentStatus||'pending';
  paymentRef.value = order.paymentRef||'';
  fulfillmentStatus.value = order.fulfillmentStatus||'new';
  notes.value = order.notes||'';

  formItems = (order.items||[]).map(x=>({...x}));
  drawItems();

  Array.from(form.elements).forEach(el => { el.disabled = readOnly; });
  cancelEdit.disabled = false;
  editModal.style.display='flex'; document.body.classList.add('modal-open');
}
function closeModal(){
  editModal.style.display = 'none';
  document.body.classList.remove('modal-open');
  Array.from(form.elements).forEach(el => { el.disabled = false; });
}

/* ==== items builder ==== */
addItemBtn.addEventListener('click', ()=> addItemRow());

async function fetchBooks() {
  if (booksLoaded) return;
  try {
    const res = await fetch('../api/fetch_books.php', { headers: { 'Accept': 'application/json' }});
    const raw = await res.text();
    const data = JSON.parse(raw);
    if (Array.isArray(data)) { books = data; booksLoaded=true; }
  } catch (err) { console.error('Error fetching books:', err); }
}
window.addEventListener('DOMContentLoaded', async ()=>{ await fetchBooks(); await loadOrders(); render(); });

function addItemRow(pref){
  const row = document.createElement('div'); row.className='row';
  const sel = document.createElement('select');
  if (books.length) {
    sel.innerHTML = `<option value="">Select a book...</option>` + books.map(b =>
      `<option value="${b.id}" data-price="${Number(b.price)||0}">${b.title} (#${b.id})</option>`
    ).join('');
  } else {
    sel.innerHTML = `<option value="">No books found (check API)</option>`;
  }

  const qty = document.createElement('input'); qty.type='number'; qty.min='1'; qty.value = pref?.qty || 1;
  const unit = document.createElement('input'); unit.type='number'; unit.min='0'; unit.step='1';
  const total = document.createElement('input'); total.type='number'; total.readOnly = true;
  const rm = document.createElement('button'); rm.type='button'; rm.className='remove'; rm.innerHTML='<i class="fas fa-times"></i>';

  if(pref?.bookId){
    sel.value = String(pref.bookId);
    unit.value = pref.unitPrice ?? (Number(sel.selectedOptions[0]?.dataset?.price)||0);
  }else{
    unit.value = 0;
  }

  function recalc(){
    const q = Number(qty.value||0);
    const u = Number(unit.value||0);
    total.value = q*u;
    calcGrand();
  }

  sel.addEventListener('change', ()=> {
    const p = Number(sel.selectedOptions[0]?.dataset?.price||0);
    if(!pref?.unitPrice) unit.value = p;
    recalc();
  });
  qty.addEventListener('input', recalc);
  unit.addEventListener('input', recalc);
  rm.addEventListener('click', ()=> {
    itemRows.removeChild(row);
    syncItemsFromDOM();
    calcGrand();
  });

  row.appendChild(sel);
  row.appendChild(qty);
  row.appendChild(unit);
  row.appendChild(total);
  row.appendChild(rm);
  itemRows.appendChild(row);

  if(pref){
    qty.value = pref.qty ?? 1;
    unit.value = pref.unitPrice ?? Number(sel.selectedOptions[0]?.dataset?.price||0);
  }
  recalc();
}

function drawItems(){
  itemRows.innerHTML='';
  if(!formItems.length){ addItemRow(); }
  else formItems.forEach(it=> addItemRow(it));
  calcGrand();
}
function syncItemsFromDOM(){
  const rows = Array.from(itemRows.children);
  formItems = rows.map(r=>{
    const [sel, qty, unit, total] = r.querySelectorAll('select,input');
    const bookId = Number(sel.value||0) || null;
    const book = books.find(b=>b.id===bookId);
    return {
      bookId,
      title: book?.title || (sel.value? `#${sel.value}` : ''),
      qty: Number(qty.value||0),
      unitPrice: Number(unit.value||0)
    };
  }).filter(x=>x.bookId && x.qty>0);
}
function calcGrand(){
  syncItemsFromDOM();
  const sum = formItems.reduce((a,b)=>a + (b.qty*b.unitPrice), 0);
  grandTotalEl.textContent = fmtLKR(sum);
  return sum;
}

/* ==== API helpers ==== */
async function apiJSON(url, opts){
  const res = await fetch(url, opts);
  const txt = await res.text();
  let data; try { data = JSON.parse(txt); } catch(e){ throw new Error('Server did not return JSON:\n'+txt); }
  if (!res.ok || data.ok===false) throw new Error(data?.error || 'Request failed');
  return data;
}

async function loadOrders(){
  const data = await apiJSON('../api/orders.php?action=list', { headers:{'Accept':'application/json'} });
  // normalize to UI shape
  orders = data.map(o=>({
    orderId: o.order_code,
    createdAt: o.created_at,
    buyer: { name:o.buyer_name, email:o.buyer_email },
    items: (o.items||[]).map(it=>({ bookId:Number(it.product_id), title:it.title, qty:Number(it.qty), unitPrice:Number(it.unit_price) })),
    amount: Number(o.total_amount||0),
    paymentStatus: String(o.payment_status||'').toLowerCase(),
    paymentRef: o.payment_ref||'',
    fulfillmentStatus: String(o.fulfillment_status||'').toLowerCase(),
    notes: o.notes||''
  }));
}

async function createOrder(rec){
  return await apiJSON('../api/orders.php?action=create', {
    method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(rec)
  });
}

async function updateOrder(rec){
  return await apiJSON('../api/orders.php?action=update', {
    method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(rec)
  });
}

async function deleteOrder(order_code){
  return await apiJSON('../api/orders.php?action=delete', {
    method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({orderId:order_code})
  });
}


/* ==== save (create or update) ==== */
form.addEventListener('submit', async (e) => {
  e.preventDefault();

  const amount = calcGrand();
  if (!formItems.length) { alert('Add at least one item.'); return; }

  const isUpdate = !!rowId.value;
  const rec = {
    orderId: (orderId.value||'').trim() || defaultOrderId(),
    createdAt: new Date(createdAt.value||Date.now()).toISOString(),
    buyer: { name: buyerName.value.trim(), email: buyerEmail.value.trim() },
    items: formItems.map(x=>({...x})),
    amount,
    paymentStatus: paymentStatus.value,          // 'paid' | 'pending' | 'failed'
    paymentRef: (paymentRef.value||'').trim(),
    fulfillmentStatus: fulfillmentStatus.value,  // 'new' | 'packed' | 'shipped' | 'complete'
    notes: (notes.value||'').trim()
  };

  try {
    if (isUpdate) await updateOrder(rec);
    else          await createOrder(rec);

    await loadOrders();
    render();
    closeModal();
    alert('Order saved.');
  } catch (err) {
    console.error(err);
    alert('Failed to save order: ' + err.message);
  }
});

/* ==== export ==== */
function exportCSV(){
  const rows=[['Order ID','Date','Buyer','Email','Items','Amount','Payment','Payment Ref','Fulfillment','Notes']].concat(
    orders.map(o=>[
      o.orderId,
      parseDate(o.createdAt).toLocaleString(),
      o.buyer?.name||'',
      o.buyer?.email||'',
      (o.items||[]).map(it=>`${it.title} x${it.qty} @ ${it.unitPrice}`).join(' | '),
      o.amount,
      o.paymentStatus,
      o.paymentRef||'',
      o.fulfillmentStatus,
      o.notes||''
    ])
  );
  const csv=rows.map(r=>r.map(x=>`"${String(x??'').replace(/"/g,'""')}"`).join(',')).join('\n');
  const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a');
  a.href=URL.createObjectURL(blob); a.download='orders.csv'; a.click(); URL.revokeObjectURL(a.href);
}
</script>


</body>
</html>
