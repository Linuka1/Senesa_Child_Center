<?php require __DIR__ . '/auth-admin.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Profile - Senesa Child Center</title>
        <link rel="stylesheet" href="dashboard.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
            /* Card */
            .profile-container{
            position:relative;
            max-width:600px;
            margin:50px auto;
            background:#fff;
            padding:30px;
            border-radius:12px;
            box-shadow:0 4px 8px rgba(0,0,0,.1);
            text-align:center;
            }
            .profile-container img{
            width:250px; height:250px; border-radius:50%;
            margin:50px 0 15px 0; object-fit:cover;
            }
            .profile-container h2{ margin:10px 0 5px; font-size:24px; color:#333; }
            .profile-container p.role{ color:#ff6b6b; margin-bottom:20px; font-weight:500; }

            .profile-info{ text-align:left; margin-top:20px; }
            .profile-info p{ margin:10px 0; font-size:16px; color:#555; }

            /* Buttons */
            .button-group{ display:flex; flex-direction:column; gap:12px; margin-top:15px; }
            .edit-actions{ display:flex; justify-content:center; gap:12px; margin-top:15px; }

            .cancel-btn{
            padding:12px 25px; background:#ff6b6b; color:#fff;
            border-radius:8px; border:none; cursor:pointer; font-weight:500;
            }
            .cancel-btn:hover{ background:#e55b5b; }

            .edit-btn{
            display:inline-flex; align-items:center; gap:6px;
            padding:12px 24px; background:#4CAF50; color:#fff;
            border-radius:8px; font-weight:600; text-decoration:none;
            transition:background .2s;
            }
            .edit-btn:hover{ background:#45a049; }

            .btn-outline{
            display:inline-flex; align-items:center; gap:6px;
            padding:8px 14px; border:1px solid #ff6b6b; color:#ff6b6b;
            border-radius:8px; background:#fff; font-weight:600; text-decoration:none;
            transition:all .2s;
            }
            .btn-outline:hover{ background:#ff6b6b; color:#fff; }

            .btn-outline-primary{ border-color:#ff6b6b; color:#ff6b6b; background:transparent; }
            .btn-outline-primary:hover{ background:#ff6b6b; color:#fff; border-color:#ff6b6b; }

            /* Header row in the card */
            .profile-head{ display:flex; align-items:center; position:relative; gap:12px; }

            /* Desktop/tablet: title centered over the row; Back on the left */
            .profile-title{
            position:absolute; left:0; right:0; margin:0; text-align:center;
            pointer-events:none; /* allows full Back-button clicks */
            }
            .back-btn{ position:relative; z-index:2; }

            /* Phones: Back on its own row (left), title centered below */
            @media (max-width:640px){
            .profile-head{
                flex-direction:column;         /* Back first, title below */
                align-items:flex-start;        /* Back is left-aligned */
            }
            .profile-title{
                position:static;               /* stop absolute centering */
                width:100%; text-align:center;
                margin-top:10px;               /* drop the title a bit */
                pointer-events:auto;
            }
            .back-btn{
                padding:6px 12px; border-radius:999px; font-size:14px;
            }
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
                <h1>Profile</h1>
                <div class="admin-info dropdown">
                    <i class="fas fa-user-circle"></i> Admin <i class="fas fa-caret-down"></i>
                    <ul class="dropdown-menu">
                        <li><a href="profile.php" class="active">Profile</a></li>
                        <li><a href="change-password.php">Change Password</a></li>
                        <li><a href="#" id="logoutBtn">Logout</a></li>
                    </ul>
                </div>

            </header>

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


            <section class="content">
                <div class="profile-container">
                    <!-- Back button at top-left -->
                    <div class="profile-head">
                        <a href="#" id="backBtn" class="btn-outline back-btn"><i class="fas fa-arrow-left"></i> Back</a>
                        <h2 class="profile-title">Admin Profile</h2>
                    </div>




                    <img src="images/admin.webp" alt="Admin Photo">
                    <h2>John Doe</h2>
                    <p class="role">Super Admin</p>

                    <div class="profile-info">
                        <p><strong>Email:</strong> <span id="emailText">admin@example.com</span></p>
                        <p><strong>Phone:</strong> <span id="phoneText">+1 234 567 890</span></p>
                        <p><strong>Joined:</strong> <span id="joinedText">January 2025</span></p>
                        <p><strong>Status:</strong> <span id="statusText">Active</span></p>
                    </div>

                    <!-- Edit button -->
                    <a href="#" class="edit-btn" id="editBtn"><i class="fas fa-edit"></i> Edit Profile</a>

                    <!-- Save / Cancel for editing -->
                    <div id="editActions" class="edit-actions" style="display:none;">
                        <button id="saveBtn" class="edit-btn"><i class="fas fa-save"></i> Save</button>
                        <button id="cancelBtn" class="cancel-btn"><i class="fas fa-times"></i> Cancel</button>
                    </div>

                </div>
            </section>

            <footer style="text-align:center; padding:15px; margin-top:auto; background:#fff; box-shadow:0 -2px 5px rgba(0,0,0,0.1);">
                &copy; 2025 Senesa Child Center. All rights reserved.
            </footer>
        </main>

        <script>
            // sidebar & dropdown & logout (shared)
            const adminInfo    = document.querySelector('.admin-info');
            const dropdownMenu = adminInfo?.querySelector('.dropdown-menu');
            const logoutBtn    = document.getElementById('logoutBtn');
            const logoutModal  = document.getElementById('logoutModal');
            const closeLogout  = logoutModal?.querySelector('.close');
            const confirmLogout= document.getElementById('confirmLogout');
            const cancelLogout = document.getElementById('cancelLogout');
            const toggleBtn    = document.querySelector('.sidebar-toggle');
            const sidebar      = document.querySelector('.sidebar');

            // dropdown
            adminInfo?.addEventListener('click', (e)=>{ e.stopPropagation(); dropdownMenu?.classList.toggle('show'); });
            document.addEventListener('click', ()=> dropdownMenu?.classList.remove('show'));

            // open modal + 🔒 lock body
            logoutBtn?.addEventListener('click', (e)=>{
            e.preventDefault();
            if (!logoutModal) return;
            logoutModal.style.display = 'flex';
            dropdownMenu?.classList.remove('show');
            document.body.classList.add('modal-open');   // lock scroll
            });

            // unified close + 🔓 unlock body
            function closeLogoutModal(){
            if (!logoutModal) return;
            logoutModal.style.display = 'none';
            document.body.classList.remove('modal-open'); // unlock scroll
            }

            closeLogout?.addEventListener('click', closeLogoutModal);
            cancelLogout?.addEventListener('click', closeLogoutModal);
            window.addEventListener('click', (e)=>{ if(e.target === logoutModal) closeLogoutModal(); });
            // optional: ESC key
            document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape' && logoutModal?.style.display === 'flex') closeLogoutModal(); });

            // confirm -> unlock then redirect
            confirmLogout?.addEventListener('click', ()=>{
            document.body.classList.remove('modal-open');
            window.location.href = 'admin-login.html';
            });

            // sidebar toggle
            toggleBtn?.addEventListener('click', ()=> sidebar?.classList.toggle('active'));

        </script>

        <script>
            const editBtn = document.getElementById('editBtn');
            const saveBtn = document.getElementById('saveBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const editActions = document.getElementById('editActions');

            const emailText = document.getElementById('emailText');
            const phoneText = document.getElementById('phoneText');
            const joinedText = document.getElementById('joinedText');
            const statusText = document.getElementById('statusText');

            let originalData = {};

            editBtn.addEventListener('click', function(e){
                e.preventDefault();
                originalData = {
                    email: emailText.textContent,
                    phone: phoneText.textContent,
                    joined: joinedText.textContent,
                    status: statusText.textContent
                };

                emailText.innerHTML = `<input type="email" id="emailInput" value="${originalData.email}">`;
                phoneText.innerHTML = `<input type="text" id="phoneInput" value="${originalData.phone}">`;
                joinedText.innerHTML = `<input type="text" id="joinedInput" value="${originalData.joined}">`;
                statusText.innerHTML = `<input type="text" id="statusInput" value="${originalData.status}">`;

                editBtn.style.display = 'none';
                editActions.style.display = 'flex';
            });

            saveBtn.addEventListener('click', function(){
                emailText.textContent = document.getElementById('emailInput').value;
                phoneText.textContent = document.getElementById('phoneInput').value;
                joinedText.textContent = document.getElementById('joinedInput').value;
                statusText.textContent = document.getElementById('statusInput').value;

                editBtn.style.display = 'inline-block';
                editActions.style.display = 'none';

                // Send updated data to backend here if needed
                console.log('Profile saved!');
            });

            cancelBtn.addEventListener('click', function(){
                emailText.textContent = originalData.email;
                phoneText.textContent = originalData.phone;
                joinedText.textContent = originalData.joined;
                statusText.textContent = originalData.status;

                editBtn.style.display = 'inline-block';
                editActions.style.display = 'none';
            });

        </script>

        <!-- Back button --> 
        <script>
  (function(){
    const backBtn = document.getElementById('backBtn');
    if(!backBtn) return;

    backBtn.addEventListener('click', (e) => {
      e.preventDefault();

      // If there's history, go back
      if (history.length > 1) {
        history.back();
        return;
      }

      // Otherwise always fallback to dashboard
      window.location.href = 'dashboard.html';
    });
  })();
</script>


</script>







    </body>
</html>
