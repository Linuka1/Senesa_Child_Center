<?php require __DIR__ . '/auth-admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Senesa Child Center</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    </head>
    <body>

        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fas fa-school"></i> Senesa Child Center - Admin</h2>
            <nav>
            <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="students.php"><i class="fas fa-user-graduate"></i> Enrollments</a>
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

        <!-- Main Content -->
        <main class="main-content">
            <header class="topbar">
                <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
                <h1>Dashboard</h1>
                <div class="admin-info dropdown">
                        <i class="fas fa-user-circle"></i> Admin <i class="fas fa-caret-down"></i>
                        <ul class="dropdown-menu">
                            <li><a href="profile.php">Profile</a></li>
                            <li><a href="change-password.php">Change Password</a></li>
                            <li><a href="logout.php" id="logoutBtn">Logout</a></li>
                        </ul>
                </div>

                <script>
                    document.querySelectorAll('.dropdown-menu a').forEach(a => {
                        const href = a.getAttribute('href') || '';
                        if (href.includes('change-password.php') || href.includes('profile.php')) {
                          a.addEventListener('click', () => {
                            sessionStorage.setItem('returnTo', window.location.href);
                          });
                        }
                    });
                </script>

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
                <div class="cards">
                    <!-- Enrollments -->
                    <div class="card">
                      <h3>Total Enrollments</h3>
                      <p id="cardEnrollments" style="margin-bottom:8px">120</p>
                      <div>
                        <a href="students.php" class="btn-outline" style="text-decoration:none;">
                          <i class="fas fa-user-graduate"></i> View Enrollments
                        </a>
                      </div>
                    </div>

                    <!-- Teachers -->
                    <div class="card">
                      <h3>Teachers</h3>
                      <p id="cardTeachers" style="margin-bottom:8px">15</p>
                      <div>
                        <a href="teachers.php" class="btn-outline" style="text-decoration:none;">
                          <i class="fas fa-chalkboard-teacher"></i> View Teachers
                        </a>
                      </div>
                    </div>

                    <!-- Programs -->
                    <div class="card">
                      <h3>Programs</h3>
                      <p id="cardPrograms" style="margin-bottom:8px">0</p>
                      <div>
                        <a href="programs.php" class="btn-outline" style="text-decoration:none;">
                          <i class="fas fa-book-open"></i> View Programs
                        </a>
                      </div>
                    </div>

                    <!-- Unread Messages -->
                    <div class="card">
                      <h3>Unread Messages</h3>
                      <p id="cardUnread" style="margin-bottom:8px">0</p>
                      <div>
                        <a href="messages.php" class="btn-outline" style="text-decoration:none;">
                          <i class="fas fa-envelope"></i> View Inbox
                        </a>
                      </div>
                    </div>

                    <!-- Orders Today -->
                    <div class="card" id="ordersCard">
                        <h3>Orders Today</h3>
                        <p id="ordersRevenue" style="font-weight:700; font-size:20px; margin:6px 0;">Rs. 0</p>
                        <div style="display:flex; gap:12px; flex-wrap:wrap; color:#555;">
                            <span><i class="fas fa-check-circle" style="color:#16a34a;"></i> <b id="ordersPaid">0</b> Paid</span>
                            <span><i class="fas fa-hourglass-half" style="color:#d97706;"></i> <b id="ordersPending">0</b> Pending</span>
                            <span><i class="fas fa-times-circle" style="color:#dc2626;"></i> <b id="ordersFailed">0</b> Failed</span>
                        </div>
                        <div style="margin-top:8px;">
                            <a href="orders.php" class="btn-outline" style="text-decoration:none;">
                              <i class="fas fa-receipt"></i> View Orders
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Reports Section -->
            <div class="reports" style="padding: 30px;">
                <h2 style="margin-bottom: 20px; color:#333;">Reports & Analytics</h2>

                <div style="margin-bottom: 20px;">
                  <button id="exportPdfBtn"   class="btn" style="padding:10px 20px; background:#ff6b6b; color:#fff; border:none; border-radius:6px; cursor:pointer;">Export to PDF</button>
                  <button id="exportExcelBtn" class="btn" style="padding:10px 20px; background:#4CAF50; color:#fff; border:none; border-radius:6px; cursor:pointer;">Export to Excel</button>
                </div>

                <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:30px;">
                  <div class="chart-container"><canvas id="activityChart"></canvas></div>
                  <div class="chart-container"><canvas id="enrollmentChart"></canvas></div>
                  <div class="chart-container"><canvas id="salesChart"></canvas></div>
                </div>
            </div>

            <footer style="text-align:center; padding:15px; margin-top:auto; background:#fff; box-shadow:0 -2px 5px rgba(0,0,0,0.1);">
                &copy; 2025 Senesa Child Center. All rights reserved.
            </footer>
        </main>

        <!-- dropdown / logout / sidebar -->
        <script>
            const adminInfo = document.querySelector('.admin-info');
            const dropdownMenu = adminInfo.querySelector('.dropdown-menu');
            const logoutBtn = document.getElementById('logoutBtn');
            const logoutModal = document.getElementById('logoutModal');
            const closeLogout = logoutModal.querySelector('.close');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            const sidebar = document.querySelector('.sidebar');

            adminInfo.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdownMenu.classList.toggle('show');
            });
            document.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
            });
            logoutBtn.addEventListener('click', e => {
                e.preventDefault();
                logoutModal.style.display = 'flex';
                dropdownMenu.classList.remove('show');
                document.body.classList.add('modal-open');
            });
            function closeLogoutModal(){
                logoutModal.style.display = 'none';
                document.body.classList.remove('modal-open');
            }
            closeLogout.addEventListener('click', closeLogoutModal);
            window.addEventListener('click', e => {
                if(e.target === logoutModal) closeLogoutModal();
            });
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
        </script>

        <!-- Charts + card logic -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
          /* keys (keeping others as-is) */
          const K_ORDERS   = 'senesa_orders_v1';
          const K_ENROLLS  = 'senesa_enrollments_v2';
          const K_MESSAGES = 'senesa_messages_v1';
          const K_TEACHERS = 'senesa_teachers_v1';

          /* utils */
          const get = (k, fallback=[]) => { try { const v=JSON.parse(localStorage.getItem(k)||'null'); return Array.isArray(v)?v:fallback; } catch{ return fallback; } };
          const LKR = n => (n||n===0) ? 'Rs. '+Number(n).toLocaleString('en-LK') : 'Rs. 0';
          const lastMonths = (n=6) => { const a=[], now=new Date(); for(let i=n-1;i>=0;i--){ const d=new Date(now.getFullYear(), now.getMonth()-i, 1); a.push({key:`${d.getFullYear()}-${d.getMonth()+1}`, label:d.toLocaleString('en',{month:'short'})}); } return a; };
          const sameDay = (a,b)=> a.getFullYear()===b.getFullYear() && a.getMonth()===b.getMonth() && a.getDate()===b.getDate();

          /* ===== API-based Programs count ===== */
          const API_PROGRAMS = '../api/programs.php';
          async function renderProgramsCard(){
            try{
              const res  = await fetch(API_PROGRAMS+'?action=count');
              const json = await res.json();
              const n = json.ok ? (json.data.count || 0) : 0;
              document.getElementById('cardPrograms').textContent = n;
            }catch(e){
              document.getElementById('cardPrograms').textContent = 0;
            }
          }

          /* other cards (still from localStorage for now) */
          function renderEnrollmentsCard(){
            const list = get(K_ENROLLS);
            document.getElementById('cardEnrollments').textContent = list.length || 0;
          }
          function renderTeachersCard(){
            const list = get(K_TEACHERS);
            document.getElementById('cardTeachers').textContent = (list.length || 0);
          }
          function renderMessagesCard(){
            const msgs = get(K_MESSAGES);
            const unread = msgs.reduce((n,m)=>{
              const st = (m && m.status!=null) ? String(m.status).trim().toLowerCase() : '';
              if (st) return n + (st === 'unread' ? 1 : 0);
              if (Object.prototype.hasOwnProperty.call(m||{}, 'read')) return n + (m.read === false ? 1 : 0);
              return n;
            },0);
            document.getElementById('cardUnread').textContent = unread;
          }
          function renderOrdersToday(){
            const now = new Date();
            const orders = get(K_ORDERS);
            let paid=0,pending=0,failed=0,revenue=0;
            orders.forEach(o=>{
              const d=new Date(o.createdAt||o.created);
              if(sameDay(d,now)){
                if(o.paymentStatus==='paid'){ paid++; revenue += (o.amount||0); }
                else if(o.paymentStatus==='pending'){ pending++; }
                else if(o.paymentStatus==='failed'){ failed++; }
              }
            });
            document.getElementById('ordersRevenue').textContent=LKR(revenue);
            document.getElementById('ordersPaid').textContent=paid;
            document.getElementById('ordersPending').textContent=pending;
            document.getElementById('ordersFailed').textContent=failed;
          }

          /* initial renders */
          renderEnrollmentsCard();
          renderTeachersCard();
          renderMessagesCard();
          renderOrdersToday();
          renderProgramsCard(); // <-- now from API

          // live update when other tabs/pages change data
          window.addEventListener('storage', e=>{
            if(e.key===K_ENROLLS)  renderEnrollmentsCard();
            if(e.key===K_TEACHERS) renderTeachersCard();
            if(e.key===K_MESSAGES) renderMessagesCard();
            if(e.key===K_ORDERS)   renderOrdersToday();
            // (no localStorage key for programs anymore — it comes from API)
          });

          /* charts from stores (unchanged for now) */
          const months = lastMonths(6);
          const orders = get(K_ORDERS);
          const enrolls = get(K_ENROLLS);

          const activeParents = months.map(m=>{
            const set = new Set();
            orders.forEach(o=>{
              if(o.paymentStatus!=='paid') return;
              const d=new Date(o.createdAt||o.created);
              const key=`${d.getFullYear()}-${d.getMonth()+1}`;
              if(key===m.key && o.buyer?.email) set.add(o.buyer.email.toLowerCase());
            });
            return set.size;
          });

          const enrollBar = months.map(m=>{
            if(enrolls.length){
              return enrolls.reduce((acc,row)=>{
                const d=new Date(row.submittedAt||row.createdAt||row.created||Date.now());
                const k=`${d.getFullYear()}-${d.getMonth()+1}`;
                return acc + (k===m.key ? 1 : 0);
              },0);
            }else{
              return orders.reduce((acc,o)=>{
                const d=new Date(o.createdAt||o.created);
                const k=`${d.getFullYear()}-${d.getMonth()+1}`;
                return acc + (k===m.key ? 1 : 0);
              },0);
            }
          });

          const booksRevenue  = orders.filter(o=>o.paymentStatus==='paid').reduce((s,o)=>s+(o.amount||0),0);
          const photosRevenue = 0;
          const daycareRevenue= 0;

          const activityChart = new Chart(document.getElementById('activityChart'), {
            type: 'line',
            data: {
              labels: months.map(m=>m.label),
              datasets: [{ label:'Active Parents', data: activeParents, borderColor:'#ff6b6b', fill:false, tension:0.3 }]
            }
          });

          const enrollmentChart = new Chart(document.getElementById('enrollmentChart'), {
            type: 'bar',
            data: {
              labels: months.map(m=>m.label),
              datasets: [{ label: enrolls.length ? 'Enrollments' : 'Orders', data: enrollBar, backgroundColor:'#4CAF50' }]
            }
          });

          const salesChart = new Chart(document.getElementById('salesChart'), {
          type: 'pie',
          data: {
              labels: ['Books', 'Photos', 'Daycare'],
              datasets: [{
                  data: [booksRevenue, photosRevenue, daycareRevenue],
                  backgroundColor: ['#ff6b6b', '#ffa502', '#1e90ff']
              }]
          },
          options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                  legend: { position: 'bottom' }
              }
          }
        });
        </script>

        <!-- Export -->
        <script>
            // PDF
            document.getElementById('exportPdfBtn').addEventListener('click', async () => {
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF({ unit: 'pt', format: 'a4' });
                const mm = v => v * 2.83465;
                const pageWidth  = pdf.internal.pageSize.getWidth();
                const margin = mm(12);
                let y = margin;

                const addChart = (id, title) => {
                  const canvas = document.getElementById(id);
                  const imgData = canvas.toDataURL('image/png', 1.0);
                  pdf.setFont('helvetica', 'bold'); pdf.setFontSize(12); pdf.text(title, margin, y); y += mm(8);
                  const maxW = pageWidth - margin*2;
                  const aspect = canvas.height / canvas.width;
                  const imgW = maxW, imgH = imgW * aspect;
                  if (y + imgH > pdf.internal.pageSize.getHeight() - margin) { pdf.addPage(); y = margin; }
                  pdf.addImage(imgData, 'PNG', margin, y, imgW, imgH); y += imgH + mm(10);
                };

                pdf.setFont('helvetica', 'bold'); pdf.setFontSize(16);
                pdf.text('Senesa Child Center — Reports & Analytics', margin, y); y += mm(14);

                addChart('activityChart',   'Active Parents (Line)');
                addChart('enrollmentChart', 'Monthly Enrollments/Orders (Bar)');
                addChart('salesChart',      'Sales Breakdown (Pie)');

                pdf.save('senesa-reports.pdf');
            });

            // Excel
            document.getElementById('exportExcelBtn').addEventListener('click', () => {
                const wb = XLSX.utils.book_new();
                const summary = [
                  ['Metric', 'Value'],
                  ['Total Enrollments', document.getElementById('cardEnrollments').textContent],
                  ['Teachers',          document.getElementById('cardTeachers').textContent],
                  ['Programs',          document.getElementById('cardPrograms').textContent],
                  ['Unread Messages',   document.getElementById('cardUnread').textContent],
                  ['Orders Today (Paid Revenue)', document.getElementById('ordersRevenue').textContent]
                ];
                XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(summary), 'Summary');

                const activitySheet = [['Month', ...activityChart.data.labels],
                                       ['Active Parents', ...activityChart.data.datasets[0].data]];
                XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(activitySheet), 'Active Parents');

                const enrollSheet = [['Month', ...enrollmentChart.data.labels],
                                     [enrollmentChart.data.datasets[0].label, ...enrollmentChart.data.datasets[0].data]];
                XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(enrollSheet), 'Enroll/Orders');

                const pie = salesChart.data;
                const salesRows = [['Category', 'Amount'], ...pie.labels.map((l,i)=>[l, pie.datasets[0].data[i]])];
                XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(salesRows), 'Sales');

                XLSX.writeFile(wb, 'senesa-reports.xlsx');
            });
        </script>
    </body>
</html>
